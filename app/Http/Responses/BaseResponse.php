<?php

namespace App\Http\Responses;

use Exception;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class BaseResponse
{
    /**
     * @throws Exception
     */
    public static function view($data, $msg = null, $customMsg = null): void
    {
        if ($data instanceof Exception) {
            $exMessage = $data->getMessage();
            $message = config('app.debug') ? $exMessage : "Failed to {$msg} .";
            self::notify('Error!', $message, 'danger');
            throw new Exception($message);
        }

        $resourceName = is_string($data) ? $data : 'Item';
        $successMessage = $customMsg ?? ('Successfully ' . $msg . '.');

        self::notify('Success', $successMessage, 'success');
    }

    public static function json($data, $msg = null, $customMsg = null): JsonResponse
    {
        $status = 'success';
        $successMessage = $customMsg ?? ('Successfully ' . $msg . '.');
        $errorMessage = 'Failed to ' . $msg . '.';
        $responseData = $data;

        if ($data instanceof \Exception) {
            $status = 'error';

            $response = [
                'status' => $status,
                'message' => $errorMessage,
                'errors' => config('app.debug') ? $data->getMessage() : null,
            ];

            // Check status code
            $statusCode = match (true) {
                $data instanceof HttpException => $data->getStatusCode(),
                $data instanceof AuthorizationException => 403, // Forbidden
                $data instanceof AccessDeniedHttpException => 403, // Forbidden
                $data instanceof BadRequestHttpException => 400, // Unauthorized
                $data instanceof UnauthorizedHttpException => 401, // Unauthorized
                $data instanceof AuthenticationException => 401, // Unauthorized
                $data instanceof ModelNotFoundException => 404, // Not Found
                $data instanceof NotFoundHttpException => 404, // Not Found
                $data instanceof MethodNotAllowedHttpException => 405, // Method Not Allowed
                $data instanceof ValidationException => 422, // Unprocessable Entity
                $data instanceof HttpResponseException => 422, // Unprocessable Entity
                $data instanceof RuntimeException => 404,
                default => 500, // Internal Server Error
            };

            return response()->json($response, $statusCode);
        }

        if (isset($data['data'])) {
            $responseData = $data['data'];
        }

        $response = [
            'status' => $status,
            'message' => $successMessage,
            'data' => $responseData,
        ];

        return response()->json($response);
    }

    public static function request($validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation Error',
            'errors' => $validator->errors(),
        ], 422));
    }

    /**
     * Generate a Filament Notification
     *
     * @param string $title
     * @param string $message
     * @param string $type (success, danger, warning, info)
     */
    public static function notify(string $title, string $message, string $type = 'success')
    {
        Notification::make()
            ->title($title)
            ->body($message)
            ->{$type}()
            ->send();
    }

    /**
     * Success Notification
     *
     * @param string $message
     */
    public static function success(string $message)
    {
        self::notify('Success', $message);
    }

    /**
     * Delete Notification
     *
     * @param string $resource
     */
    public static function delete(string $resource)
    {
        self::notify('Success', 'Successfully deleted ' . $resource . '.');
    }

    /**
     * Info Notification
     *
     * @param string $title
     * @param string $message
     */
    public static function info(string $title, string $message)
    {
        self::notify($title, $message, 'info');
    }

    /**
     * Warning Notification
     *
     * @param string $title
     * @param string $message
     */
    public static function warn(string $title, string $message)
    {
        self::notify($title, $message, 'warning');
    }

    /**
     * Error Notification
     *
     * @param string $message
     */
    public static function error(string $message)
    {
        self::notify('Failed', $message, 'danger');
    }

    /**
     * Function for handling "Nothing has been changed" scenario.
     */
    public static function noChanges($data): void
    {
        $message = "Nothing has been changed for " . (is_string($data) ? $data : 'Item');
        self::notify('No Changes', $message, 'info');
    }
}
