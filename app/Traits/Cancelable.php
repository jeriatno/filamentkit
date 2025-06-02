<?php

namespace App\Traits;

use App\Http\Responses\ResponseBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait Cancelable
{
    /**
     * Bulk cancel operations.
     *
     * This method processes a batch cancellation request. It takes input from
     * the request (e.g., IDs of items to cancel) and delegates the cancellation
     * logic to the repository. The response is returned as a JSON object indicating
     * success or failure of the operation.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function bulkCancel(Request $request): JsonResponse
    {
        $data = $this->repository->cancel($request);

        return ResponseBase::json($data, 'cancelled', true);
    }
}
