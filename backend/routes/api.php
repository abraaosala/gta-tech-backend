<?php

use app\Http\controllers\admin\ProductController;
use app\Http\controllers\admin\SaleController;
use app\Http\controllers\admin\CategoryController;
use app\Http\controllers\AuthController;
use app\http\controllers\ProfileController;
use app\Http\controllers\admin\UserController;
use app\models\Category;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Profiler\Profile;

$router->get('/', function () {
    response()->success('API Funcionando');
});
$router->group(['prefix' => 'api'], function () use ($router) {


    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/refresh', [AuthController::class, 'refresh']);

    $router->resource('sales', SaleController::class);


    $router->group([
        'middleware' => 'auth'
    ], function ($router) {
        $router->get('/me', [ProfileController::class, 'index']);

        $router->group([], function ($router) {
            $router->resource('products', ProductController::class);
            $router->resource('users', UserController::class);
            $router->resource('categories', CategoryController::class);
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
