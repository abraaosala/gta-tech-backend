<?php  
use app\Http\Middleware\AuthMiddleware;
 
return [
    'auth'=> AuthMiddleware::class,
];