<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait HasHistory
{
    /*
    |--------------------------------------------------------------------------
    | EMAIL HISTORIES
    |--------------------------------------------------------------------------
    */

    /**
     * Displays email histories based on the provided ID.
     *
     * @param int|string $id The ID of the email histories to retrieve.
     * @return mixed The email histories retrieved from the emailSpoolService.
     */
    public function showEmailHistories($id)
    {
        return $this->emailSpoolService->getHistories($this->model::app, $id);
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT HISTORIES
    |--------------------------------------------------------------------------
    */

    /**
     * Get the reject action list.
     *
     * @return array
     */
    public function getRejectActions(): array
    {
        return property_exists($this->model, 'rejectAction') ? $this->model->rejectAction : ['Rejected'];
    }

    /**
     * Displays reject histories based on the provided ID.
     *
     * @param int|string $id The ID of the reject histories to retrieve.
     * @return mixed The reject histories retrieved from the model.
     */
    public function showRejectHistories($id)
    {
        return $this->model->showHistoryByAction($id, $this->getRejectActions());
    }

    /*
    |--------------------------------------------------------------------------
    | ACTION HISTORIES
    |--------------------------------------------------------------------------
    */

    /**
     * Displays action histories grouped by date based on the model and the provided ID.
     *
     * @param int|string $id The ID for which action histories will be retrieved.
     * @param bool  $isArray  Whether to return the result as an array. Defaults to false.
     * @return array|Collection Grouped and sorted action histories by date.
     */
    public function showActionHistories($id, $isArray = false)
    {
        $activityLogs = $this->model->showHistories($id);

        $logs = $activityLogs->groupBy(function ($item) {
            return $item->created_at->format('d M Y'); // Group logs by the creation date.
        })->map(function ($group) {
            return $group->sortByDesc('id'); // Sort each group by descending ID.
        });

        if($isArray) {
            return $logs->toArray();
        }

        return $logs;
    }

    /**
     * Retrieves workflow actions for a specific ID based on action histories.
     *
     * @param int|string $id The ID for which workflow actions will be retrieved.
     * @return array An array of workflow actions derived from the action histories.
     */
    public function showWorkflowActions($id, $isReverse = true): array
    {
        $workflowActions = [];
        foreach ($this->showActionHistories($id) as $date => $log) {
            foreach ($log as $key => $item) {
                $workflow = $this->model->getWorkflowAction($item->action, $isReverse);
                $workflowActions[$key] = $workflow; // Map workflow actions with their corresponding keys.
            }
        }

        return $workflowActions;
    }

}
