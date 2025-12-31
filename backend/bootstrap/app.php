<?php

require_once dirname(__DIR__)."/config/base.php";
require BASE . "/vendor/autoload.php";
require BASE . "/bootstrap/database.php";

require BASE . "/vendor/illuminate/support/helpers.php";
require BASE . "/app/helper/helper.php";

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Routing\RoutingServiceProvider;
use Illuminate\Support\Facades\Facade;
use app\repository\ConfigRepository;
use Illuminate\Hashing\HashManager;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Http\Request;

$dotenv = Dotenv\Dotenv::createImmutable(BASE);
$dotenv->load();


$container = new Container;

// 🔑 MUITO IMPORTANTE
Container::setInstance($container);
// Inicializa o Facade com o container
Facade::setFacadeApplication($container);
$events = new Dispatcher($container);


// Adiciona Capsule ao container
$container->instance('db', $capsule->getDatabaseManager());
$container->instance('db.connection', $capsule->getConnection());
// 🔑 Registra o Service Provider de rotas
(new RoutingServiceProvider($container))->register();


// hashting

$config = new ConfigRepository([
    'hashing' => [
        'driver' => 'bcrypt',
    ],
]);

$container->instance('config', $config);




// registra o HashManager
$provider = new HashServiceProvider($container);
$provider->register();

// adiciona alias 'hash' no container (o que o Facade espera)
$container->alias('hash', HashManager::class);

use Illuminate\Pipeline\Pipeline;

$container->instance(Pipeline::class, new Pipeline($container));

$middlewares = require BASE . '/app/Http/Kernel.php';

foreach ($middlewares as $alias => $class) {
    $container->bind($alias, $class);
}

$router = new Router($events, $container);

return $router;