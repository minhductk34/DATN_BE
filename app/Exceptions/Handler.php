<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
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
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Custom 404 error response for API routes
        if ($exception instanceof NotFoundHttpException) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => null,
                    'warning' => 'Route không tồn tại.',
                ], 404);
            }
        }

        // Custom 405 error response for API routes
        if ($exception instanceof MethodNotAllowedHttpException) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'status' => '405',
                    'data' => null,
                    'warning' => 'Phương thức HTTP không được phép cho route này.',
                ], 405);
            }
        }

        // Custom validation error response
        if ($exception instanceof ValidationException) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'status' => '422',
                    'data' => $exception->errors(),
                    'warning' => 'Dữ liệu gửi lên không hợp lệ.',
                ], 422);
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Optionally log exceptions or perform other actions
        });
    }
}
