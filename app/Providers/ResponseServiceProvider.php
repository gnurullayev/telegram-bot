<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro('customJson', function (mixed $data, int $status = 200, array $headers = [], int $options = 0) {
            return Response::json([
                'status' => 'success',
                'data' => $data,
            ], $status, $headers, $options);
        });

        Response::macro('customJsonError', function (string $message, int $status = 400, array $headers = [], int $options = 0) {
            return Response::json([
                'status' => 'error',
                'message' => $message,
                'data' => [],
            ], $status, $headers, $options);
        });

        Response::macro('customHtml', function ($content) {
            return Response::make("<html><body>$content</body></html>", 200)
                ->header('Content-Type', 'text/html');
        });
    }
}
