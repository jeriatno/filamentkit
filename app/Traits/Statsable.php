<?php

namespace App\Traits;

use App\Http\Responses\ResponseBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait Statsable
{
    /**
     * Display statistics for the dashboard.
     *
     * This method fetches statistical data, such as total users, active orders,
     * or revenue, from the repository. The data is typically formatted as JSON
     * for use in the dashboard interface.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getStats(Request $request): JsonResponse
    {
        $stats = $this->repository->stats($request);

        return ResponseBase::json($stats, 'retrieved');
    }
}
