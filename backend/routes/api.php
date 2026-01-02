<?php

use app\Http\controllers\admin\ProductController;
use app\Http\controllers\admin\SaleController;
use app\Http\controllers\admin\CategoryController;
use app\Http\controllers\AuthController;
use app\Http\controllers\ProfileController;
use app\Http\controllers\admin\UserController;
use app\Http\controllers\admin\LandingController;
use app\models\Category;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\Http\controllers\PublicController;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Profiler\Profile;

$router->get('/', function () {
    response()->success('API Funcionando');
});
$router->group(['prefix' => 'api'], function () use ($router) {


    // Rotas Públicas para Landing Page
    $router->get('/public/settings', [PublicController::class, 'getSettings']);
    $router->get('/public/services', [PublicController::class, 'getServices']);
    $router->get('/public/reviews', [PublicController::class, 'getReviews']);
    $router->post('/public/contact', [PublicController::class, 'storeContact']);

    $router->get('/debug-env', function () {
        return response()->json([
            'DB_DRIVER'   => env('DB_DRIVER'),
            'DB_HOST'     => env('DB_HOST'),
            'DB_PORT'     => env('DB_PORT'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'DB_USERNAME' => env('DB_USERNAME'),
            // 'DB_PASSWORD' => '********', // Omitido por segurança
        ]);
    });

    $router->resource('sales', SaleController::class);


    $router->group([
        'middleware' => 'auth'
    ], function ($router) {
        $router->get('/me', [ProfileController::class, 'index']);

        $router->group([], function ($router) {
            $router->resource('products', ProductController::class);
            $router->resource('users', UserController::class);
            $router->resource('categories', CategoryController::class);

            // Gestão da Landing Page
            $router->get('/admin/landing/settings', [LandingController::class, 'getSettings']);
            $router->post('/admin/landing/settings', [LandingController::class, 'updateSetting']);
            $router->get('/admin/landing/contacts', [LandingController::class, 'getContacts']);
            $router->delete('/admin/landing/contacts/{id}', [LandingController::class, 'deleteContact']);
            $router->resource('landing/services', \app\Http\controllers\admin\ProductController::class); // Reutilizando para serviços se desejar, ou criaremos um específico depois
        });
    });


    $router->get('/db-check', function () {
        try {
            $dbName = DB::connection()->getDatabaseName();
            $driver = DB::connection()->getConfig('driver');
            return response()->json([
                'success' => true,
                'message' => 'Database connection successful',
                'database' => $dbName,
                'driver' => $driver
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ], 500);
        }
    });
});
