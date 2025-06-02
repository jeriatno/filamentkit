<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Prologue\Alerts\Facades\Alert;

trait HasLogger
{
    /**
     * Log error including line number and ID.
     */
    function errorMessage(\Exception $ex, $channel, $fn, $id = null)
    {
        if ($id !== null) {
            if (is_array($id)) {
                $idString = implode(', ', $id);
                $idDescription = count($id) > 1 ? 'IDs' : 'ID';
            } else {
                $idString = $id;
                $idDescription = 'ID';
            }
        } else {
            $idString = '';
            $idDescription = '';
        }

        $message = sprintf(
            '%s: %s (Line %s)%s%s',
            $fn,
            $ex->getMessage(),
            $ex->getLine(),
            $idDescription ? ', with ' . $idDescription . ': ' : '',
            $idString
        );

        // Check if all parameters are the same
        $paramsHash = md5(serialize([$message]));

        if (Cache::get($paramsHash) !== true) {
            Log::channel($channel)->error($message);

            // Set cache to true for the current parameter combination
            Cache::put($paramsHash, true, 60); // Cache for 60 seconds
        }
    }

    /**
     * Log info including line number and ID.
     */
    function infoMessage($channel, $fn, $key = null, $id = null, $msg = null)
    {
        if (is_array($id)) {
            $idString = implode(', ', $id);
            $idDescription = count($id) > 1 ? 'IDs' : 'ID';
        } else {
            $idString = $id;
            $idDescription = $msg ?? 'is not found!';
        }

        $message = sprintf(
            '%s: %s with %s %s',
            $fn,
            $key,
            $idString,
            $idDescription
        );

        // Check if all parameters are the same
        $paramsHash = md5(serialize([$message]));

        if (Cache::get($paramsHash) !== true) {
            Log::channel($channel)->info($message);

            // Set cache to true for the current parameter combination
            Cache::put($paramsHash, true, 60); // Cache for 60 seconds
        }
    }

    /**
     * Log warning including line number and ID.
     */
    function warningMessage($channel, $fn, $msg = null)
    {
        $message = sprintf(
            '%s: %s',
            $fn,
            $msg
        );

        // Check if all parameters are the same
        $paramsHash = md5(serialize([$message]));

        if (Cache::get($paramsHash) !== true) {
            // Log the message
            Log::channel($channel)->warning($message);

            // Set cache to true for the current parameter combination
            Cache::put($paramsHash, true, 60); // Cache for 60 seconds
        }
    }

    /**
     * Log show message only based on channel.
     */
    function showMessage($channel, $msg = null, $startTime = null, $totalData = 0, $errorData = null)
    {
        // Calculate execution time if start time is provided
        $totalExecutionMsg = '';
        if ($startTime) {
            $executionTime = microtime(true) - $startTime;
            $formattedExecutionTime = gmdate('H:i:s', $executionTime);
            $totalExecutionMsg = 'Execution time: ' . $formattedExecutionTime;
        }

        // Format total data processed message
        $dataCountMsg = $totalData ? ' of ' . $totalData . ' items.' : '';

        // Format error data message
        $errorDataMsg = $errorData ? 'Unprocessed : ' .$errorData : '';

        // Construct the message
        $message = trim(sprintf('%s %s %s %s', $msg, $totalExecutionMsg, $dataCountMsg, $errorDataMsg));

        // Log the message
        Log::channel($channel)->info($message);
    }
}
