<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception, Request $request) {
            // API/AJAX requests should keep receiving JSON responses.
            if ($request->expectsJson()) {
                return null;
            }

            // Let Laravel keep its normal redirect behaviour for validation,
            // authentication and explicitly generated HTTP responses.
            if ($exception instanceof ValidationException
                || $exception instanceof AuthenticationException
                || $exception instanceof HttpResponseException) {
                return null;
            }

            $status = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            $message = match ($status) {
                400 => 'Yêu cầu không hợp lệ. Vui lòng kiểm tra lại thông tin và thử lại.',
                401 => 'Phiên đăng nhập đã hết. Vui lòng đăng nhập lại để tiếp tục.',
                403 => 'Bạn không có quyền thực hiện thao tác này.',
                404 => 'Trang bạn đang tìm không tồn tại hoặc đã được di chuyển.',
                413 => 'Dữ liệu tải lên quá lớn. Nếu đang tải ảnh, hãy nén ảnh xuống rồi thử lại.',
                419 => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang rồi thử lại.',
                429 => 'Bạn thao tác quá nhanh. Vui lòng chờ một chút rồi thử lại.',
                503 => 'Website đang tạm thời bảo trì. Vui lòng quay lại sau ít phút.',
                default => 'Hệ thống đang gặp sự cố tạm thời. Dữ liệu của bạn chưa được lưu.',
            };

            return response()->view('errors.generic', [
                'status' => $status,
                'message' => $message,
                'isAdminRequest' => $request->is('admin/*') || $request->is('admin'),
            ], $status);
        });
    })->create();
