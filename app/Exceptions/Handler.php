<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;



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
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
//     public function render($request, Throwable $exception)
// {
//     if ($request->expectsJson()) {
//         if ($exception instanceof ValidationException) {
//             return response()->json([
//                 'error' => 'Validation Error',
//                 'message' => $exception->validator->errors(),
//             ], 422);
//         }

//         if ($exception instanceof HttpException) {
//             return response()->json([
//                 'error' => 'HTTP Error',
//                 'message' => $exception->getMessage(),
//             ], $exception->getStatusCode());
//         }

//         return response()->json([
//             'error' => 'Server Error',
//             'message' => $exception->getMessage(),
//         ], 500);
//     }

//     return parent::render($request, $exception);
// }

}
