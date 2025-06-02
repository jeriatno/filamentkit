<?php

    namespace App\Services;

    use App\Jobs\EmailSpoolNotificationJob;
    use App\Models\SettingEmailConfig;
    use App\Models\SettingEmailLog;
    use App\Traits\HasLogger;
    use App\User;
    use Carbon\Carbon;

    class EmailSpoolService
    {
        use HasLogger;

        protected $model;

        public function __construct()
        {
            $this->model = new SettingEmailLog();
        }

        public function recordDataLog($module, $data, $action = null, $recipients = [], $docType, $subject, $notes = null, $refId = null)
        {
            try {
                $docId = $data->id ?? $data['id'];
                $sender = auth()->user()->email ?? null;

                $logger = $this->model
                    ->where('module_name', $module)
                    ->where('doc_id', $docId)
                    ->where('action_name', $action)
                    ->first();

                if ($logger && $logger->status == GlobalEmailSpool::FAILED) {
                    $logger->to = !empty($recipients['to']) ? implode(', ', $recipients['to']) : null;
                    $logger->cc = !empty($recipients['cc']) ? implode(', ', $recipients['cc']) : null;
                    $logger->bcc = !empty($recipients['bcc']) ? implode(', ', $recipients['bcc']) : null;
                    $logger->subject = $subject ?? null;
                    $logger->recipients = json_encode($recipients);
                    $logger->status = SettingEmailLog::PENDING;
                    $logger->save();
                }
                else {
                    $logger = $this->model->create([
                        'app_name'      => 'SMIP',
                        'module_name'   => $module,
                        'action_name'   => $action,
                        'doc_id'        => $docId,
                        'doc_type'      => $docType,
                        'subject'       => $subject ?? null,
                        'sender'        => $sender ?? null,
                        'to'            => !empty($recipients['to']) ? implode(', ', $recipients['to']) : null,
                        'cc'            => !empty($recipients['cc']) ? implode(', ', $recipients['cc']) : null,
                        'bcc'           => !empty($recipients['bcc']) ? implode(', ', $recipients['bcc']) : null,
                        'action_time'   => Carbon::now(),
                        'send_time'     => Carbon::now(),
                        'recipients'    => json_encode($recipients),
                        'notes'         => $notes ?? null,
                        'doc_ref_id'    => $refId ?? null,
                        'status'        => SettingEmailLog::PENDING,
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now(),
                    ]);
                }

                return $logger;
            } catch (\Exception $ex) {
                $this->errorMessage($ex, 'email-spool', __METHOD__);

                return ['error' => $ex->getMessage()];
            }
        }

        public function sendNotification($module, $data, $action = null, $notes = null, $isPrincipal = false, $attach = [], $refId = null)
        {
            try {
                $docType = SettingEmailLog::$docType[$module];
                $emailConf = SettingEmailLog::$emailConf[$module] ?? null;
                $viewName = SettingEmailLog::views[$module];
                $docUrl = SettingEmailLog::url[$module];
                $docNumber = SettingEmailLog::$docNumber[$module] ?? null;

                // get email config
                $emailConfig = null;
                if (isset($emailConf)) {
                    if ($emailConf == SettingEmailConfig::class) {
                        $emailConfig = $emailConf::where('module_name', strtoupper($module))->where('action', $action)->first();
                    } else {
                        $emailConfig = $emailConf::where('action', $action)->first();
                    }
                }

                // get recipients
                $recipientObject = new GlobalEmailSpool::$emailLog[$module];
                $recipients = $recipientObject->setRecipients($data, $action, $emailConfig, $refId);
                if (isset($recipients['error'])) {
                    throw new \Exception($recipients['error']);
                }

                // get email subject
                $subject = $this->getSubject($module, $data, $emailConfig, $docNumber);

                // create email log
                $emailLog = $this->recordDataLog($module, $data, $action, $recipients, $docType, $subject, $notes, $refId);
                if (isset($emailLog['error'])) {
                    throw new \Exception($emailLog['error']);
                }

                // get doc url
                $document = $this->getDocuments($data, $docUrl, $docNumber);

                // get receivers
                $receiver = $this->getReceivers($isPrincipal, $data, $recipients);

                // get rejectors
                $rejector = $this->getRejectors($module, $data, $emailLog);

                // prepare to send
                if (config('mailing.email_spool.notify')) {
                    $job = (new EmailSpoolNotificationJob(
                        $data,
                        $emailLog,
                        $receiver,
                        $viewName,
                        $document,
                        $rejector,
                        $isPrincipal,
                        $attach
                    ));
                    dispatch($job)->onQueue('emailspool');
                }

            } catch (\Exception $ex) {
                $this->errorMessage($ex, 'email-spool', __METHOD__, $data->id);

                return ['error' => $ex->getMessage()];
            }
        }

        public function findById($id)
        {
            $logger = SettingEmailLog::find($id);

            $model = new SettingEmailLog::$docType[$logger->module_name];

            return $model->find($logger->doc_id);
        }

        public function getHistories($module, $id)
        {
            return GlobalEmailSpool::where('module_name', $module)
                ->where('doc_id', $id)
                ->latest()
                ->get();
        }

        public function getDirectReports($userId)
        {
            $user = User::with([
                'parentUser.orgChartMapping' => function ($query) {
                    $query->whereNotIn('email', function ($subQuery) {
                        $subQuery->select('email')->from('company_director');
                    });
                }
            ])
                ->find($userId);

            $ccEmails = collect([]);

            if ($user->parentUser) {
                $ccEmails = User::with([
                    'parentUser.orgChartMapping' => function ($query) {
                        $query->whereNotIn('email', function ($subQuery) {
                            $subQuery->select('email')->from('company_director');
                        });
                    }
                ])
                    ->find($user->parentUser->id)
                    ->parentUser
                    ->orgChartMapping;
            }

            $result = $ccEmails->merge([$user]);

            return $result;
        }

        public function getReceivers($isPrincipal, $data, $recipients)
        {
            // get partner
            if ($isPrincipal) {
                $partnerId = $data->partner_id ?? $data->bill_to_customer_id;
                $partner = Partner::where('id', $partnerId)->first();
                if ($partner) {
                    $receiver = $partner->name ?? 'Partner';
                }
                else {
                    $receiver = $data->courier_name ?? 'Partner';
                }
            }
            // get employee
            else {
                if (is_array($recipients['to']) && count($recipients['to']) > 1) {
                    $receiver = 'All';
                }
                else {
                    $receiver = User::where('email', $recipients['to'])->first()->name;
                }
            }

            return $receiver;
        }

        public function getDocuments($data, $docUrl, $docNumber)
        {
            // get doc url
            $docUrl = backpack_url($docUrl);
            if (isset($docNumber) && $docNumber != 'id')  {
                $docUrl = $docUrl . '?' . $docNumber . '=' . $data->$docNumber;
            } else {
                $docUrl = $docUrl . '/' . $data->id;
            }

            return [
                'url' => $docUrl,
                'number' => $data->$docNumber
            ];
        }

        public function getRejectors($module, $data, $emailLog): array
        {
            $rejectStatus = SettingEmailLog::$emailConf[$module];
            if (isset($rejectStatus) && in_array($data->status, $rejectStatus::GetRejectStatus)) {
                $rejector_name = $emailLog->actionBy->name ?? '-';
                $rejector_email = $emailLog->actionBy->email ?? '-';
                $notes = $emailLog->notes ?? '-';
            }

            return [
                'name' => $rejector_name ?? null,
                'email' => $rejector_email ?? null,
                'notes' => $notes ?? null
            ];
        }

        public function getSubject($module, $data, $emailConfig, $docNumber)
        {
            if (isset($emailConfig->subject)) {
                $subject = str_replace('[DocNumber]', $data->$docNumber, $emailConfig->subject);
            } else {
                $subject = 'You Have 1 Incoming '.plainText($module).' Notification' . ' ('.$data->$docNumber.')';
            }

            return $subject;
        }

        public function getEmailHistories($type, $id)
        {
            return $this->model->where('doc_type', $type)
                ->where('doc_id', $id)
                ->latest()
                ->get();
        }

        public function getEmailTimeline($type, $id, $refId = null)
        {
            $data = $this->model->where('doc_type', $type)
                ->where('doc_id', $id)
                ->select('action_time', 'send_time', 'received_time');

            if(isset($refId)) {
                $data = $data->where('ref_id', $refId);
            }

            $data = $data->first();

            return [
                'action_time'   => $data->action_time,
                'send_time'     => $data->send_time,
                'received_time' => $data->received_time,
            ];
        }
    }
