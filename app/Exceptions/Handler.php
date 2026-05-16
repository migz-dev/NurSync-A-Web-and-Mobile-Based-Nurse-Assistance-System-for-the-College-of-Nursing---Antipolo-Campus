<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        // \Illuminate\Database\QueryException::class => 'warning',
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // You can send to Sentry/Bugsnag here if configured.
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * For JSON/AJAX requests, return a unified error body:
     *   { ok:false, error: string, details?: mixed }
     */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            [$status, $payload] = $this->buildJsonError($e);
            return response()->json($payload, $status);
        }

        return parent::render($request, $e);
    }

    /**
     * Convert exceptions into a consistent JSON structure.
     *
     * @return array{0:int,1:array<string,mixed>}
     */
    protected function buildJsonError(Throwable $e): array
    {
        $status  = 500;
        $payload = ['ok' => false, 'error' => 'Server error'];

        // 401 Unauthenticated
        if ($e instanceof AuthenticationException) {
            return [401, ['ok' => false, 'error' => 'Unauthenticated']];
        }

        // 403 Forbidden
        if ($e instanceof AuthorizationException) {
            return [403, ['ok' => false, 'error' => 'Forbidden']];
        }

        // 422 Validation
        if ($e instanceof ValidationException) {
            return [422, [
                'ok'     => false,
                'error'  => 'Validation failed',
                'details'=> $e->errors(),
            ]];
        }

        // 404 Not Found (route or model)
        if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
            return [404, ['ok' => false, 'error' => 'Not found']];
        }

        // 405 Method not allowed
        if ($e instanceof MethodNotAllowedHttpException) {
            return [405, ['ok' => false, 'error' => 'Method not allowed']];
        }

        // 429 Too Many Requests
        if ($e instanceof ThrottleRequestsException) {
            return [429, ['ok' => false, 'error' => 'Too many requests']];
        }

        // Generic Symfony HTTP exceptions (e.g., 400/401/403/404/500…)
        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            $msg    = trim((string) $e->getMessage());
            $payload['error'] = $msg !== '' ? $msg : ($status >= 500 ? 'Server error' : 'HTTP error');
            return [$status, $payload];
        }

        // Fallback 500; include extra details in debug
        if (config('app.debug')) {
            $payload['details'] = [
                'exception' => class_basename($e),
                'message'   => $e->getMessage(),
            ];
        }

        return [$status, $payload];
    }

    /**
     * Customize unauthenticated response for non-JSON requests if needed.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'error' => 'Unauthenticated'], 401);
        }

        // Redirect guests to the appropriate login page based on guard
        $guard = collect($exception->guards())->first();
        return match ($guard) {
            'admin'   => redirect()->guest(route('admin.login')),
            'faculty' => redirect()->guest(route('faculty.login')),
            default   => redirect()->guest(route('login')),
        };
    }
}