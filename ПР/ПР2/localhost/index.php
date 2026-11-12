<?php 

$uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';

echo "<pre>";
echo "URL: {$_SERVER['REQUEST_URI']}\n";
echo "Путь: {$uri}\n";
echo "Метод: {$_SERVER['REQUEST_METHOD']}\n";
echo "</pre>";

spl_autoload_register(function (string $class){

    $prefix = 'App\\';
    $baseDir = __DIR__.'/app/';

    if(!str_starts_with($class, $prefix)){
        return;
    }
    $file = $baseDir.str_replace('\\','/',substr($class, strlen($prefix))).'.php';
    if(file_exists($file)){
        require $file;
    }
});

// Простой маршрутизатор

use App\Core\Router;
use App\Controllers\MaterialController;
use App\Controllers\ProductController;

$router = new Router();

$router -> get('/', [MaterialController::class,'index']);
$router -> get('/', [ProductController::class,'index']);
$router -> get('/products/{id}', [ProductController::class,'show']);
$router -> get('/materials/{id}', [MaterialController::class, 'show']);

//Запуск роутера
$router -> dispatch($_SERVER['REQUEST_METHOD'],$_SERVER['REQUEST_URI']);
?>

<div>
    <a href="/">Главная страница</a>
    <a href="/about">О магазине</h1>
</div>
