<?php
// Permite requisições de qualquer origem (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With, Cache-Control");

// Para OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}


require dirname(__DIR__) . "/vendor/autoload.php";

use Illuminate\Http\Request;
use app\classes\Logger;


try {


    $router = require dirname(__DIR__) . "/bootstrap/app.php";

    require dirname(__DIR__) . "/routes/api.php";

    $request = Request::capture();


    $response = $router->dispatch($request);

    $response->send(); //code...
} catch (Throwable $e) {
    Logger::error($e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

    response()->json([
        'success' => false,
        'details' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], 500);
}
