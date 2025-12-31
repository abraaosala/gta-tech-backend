<?php
use Illuminate\Database\Capsule\Manager as Capsule;

require dirname(__DIR__).'/vendor/autoload.php';

$capsule = new Capsule;

// Configuração da base de dados
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',   // ou IP do servidor
    'database'  => env('DB_NAME', 'gta_pos'),
    'username'  => env('DB_USER','root'),
    'password'  => env('DB_PASSWORD', ''),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Torna o Capsule global (opcional, para usar static methods)
$capsule->setAsGlobal();

// Inicializa o Eloquent
$capsule->bootEloquent();

return $capsule;