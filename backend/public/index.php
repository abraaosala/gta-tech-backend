<?php

require dirname(__DIR__) . "/vendor/autoload.php";

use Illuminate\Http\Request;


try {


    $router = require dirname(__DIR__) . "/bootstrap/app.php";

    require dirname(__DIR__) . "/routes/api.php";

    $request = Request::capture();


    $response = $router->dispatch($request);

    $response->send(); //code...
} catch (Throwable $e) {
    response()->json([
        'success' => false,
        'details' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], 500);
}
