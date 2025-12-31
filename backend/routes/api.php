<?php

// Permite requisições de qualquer origem (teste)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With, Cache-Control");

// Para OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

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


});