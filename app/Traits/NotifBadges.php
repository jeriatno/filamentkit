<?php

namespace App\Traits;

use App\Http\Responses\ResponseBase;
use Illuminate\Http\JsonResponse;

trait NotifBadges
{
    /**
     * @return JsonResponse
     */
    public function getNotifBadge(): \Illuminate\Http\JsonResponse
    {
        $data = $this->repository->notify();

        return ResponseBase::json($data);
    }
}
