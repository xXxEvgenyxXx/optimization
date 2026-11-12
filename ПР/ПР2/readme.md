# ПР2. MVC

index.php:

```
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

```

.htaccess:

```
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f

RewriteCond %{REQUEST_FILENAME} !-d 
    
RewriteRule ^ index.php [QSA,L]
```

config/db.php:

```
<?php 

return [
    'host' => 'http://localhost/',
    'dbname' => 'jewelry-store',
    'user' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

?>
```

/app/Controllers/MaterialController.php:

```
<?php 

namespace App\Controllers;

use App\Core\Controller;

use App\Models\Material;

class MaterialController extends Controller {

    private Material $model;

    public function __construct(){
        $this -> model = new Material();
    }

    public function index():void{
        
        $this -> view('materials/index',['materials' => $this -> model -> all()]);
    }

    public function show(array $id):void{
        $material = $this -> model -> find($id[0]);

        if(!$material){
            http_response_code(404);
            exit('Материал не найден, 404 отжимания');
        };

        $material = $material[0];

        $this -> view('materials/show',['material' => $material]);
    }
};


```

/app/Controllers/ProductController.php:

```
<?php 

namespace App\Controllers;

use App\Core\Controller;

use App\Models\Product;

class ProductController extends Controller {

    private Product $model;

    public function __construct(){
        $this -> model = new Product();
    }

    public function index():void{
        
        $this -> view('products/index',['products' => $this -> model -> all()]);
    }

    public function show(array $id):void{
        $product = $this -> model -> find($id[0]);

        if(!$product){
            http_response_code(404);
            exit('Изделие не найдено, 404 отжимания');
        };

        $product = $product[0];

        $this -> view('products/show',['product' => $product]);
    }
};


```

/app/Core/Controller.php:

```
<?php 

namespace App\Core;

use App\Core;

abstract class Controller{
    protected function view(string $template, array $data = []):void{
        View::render($template,$data);
    }
}

?>
```

/app/Core/Database.php:

```
<?php

namespace App\Core;

use PDO;

class Database {
    private static ?PDO $pdo = null;

    public static function pdo():PDO {
        if(self::$pdo === null){
            $config = require __DIR__.'/../../config/db.php';

            $dsn = "mysql::host={$config['host']}; dbname={$config['dbname']}; charset={$config['charset']}";

            self::$pdo = new PDO(
                $dsn,
                $config['user'],
                $config['password'],
            );
        }

        return self::$pdo;
    }
}

?>
```

/app/Core/Router.php:
```
<?php 

namespace App\Core;
use App\Controllers\MaterialController;
use App\Controllers\ProductController;

class Router{

    public array $routes = [];

    public function get(string $path, callable|array $handler ):void {
        $this -> routes['GET'][$path][] = $handler;
    }

    public function post(string $path, callable|array $handler ):void {
        $this -> routes['POST'][$path][] = $handler;
    }

    public function dispatch(string $method, string $uri): void { //Переписал dispatch чтоб поддерживал сразу несколько выводов
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $route => $handlers) {
            $pattern = '#^' . preg_replace('#\{(\w+)}#', '(\d+)', $route) . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                foreach ($handlers as $handler) {
                    if (is_array($handler)) {
                        [$class, $action] = $handler;
                        (new $class())->$action($matches);
                    } else {
                        $handler($matches);
                    }
                }
                return;
            }
        }

        http_response_code(404);
        echo "100 отжиманий";
    }
}
```

/app/Core/View.php:

```
<?php 

namespace App\Core;

class View {
    
    public static function render(string $template, array $data = []):void{
        extract($data);

        $viewFile = __DIR__.'/../Views/'.$template.'.php';

        if(!file_exists($viewFile)){
            http_response_code(500);
            echo "Файл не найден";
        }

        ob_start();

        require $viewFile;

        $content = ob_get_clean();

        require __DIR__.'/../Views/layout.php';
    }
}

?>
```

/app/Models/Material.php:
```
<?php

namespace App\Models;

use App\Core\Database;

class Material {
    public function all(): array {
        return Database::pdo()
        -> query("SELECT * FROM material")
        -> fetchAll();
    }

    public function find(int $id): ?array {
        $stm = Database::pdo() -> prepare ("SELECT * FROM material WHERE id=?");

        $stm -> execute([$id]);

        return $stm -> fetchAll() ?:null;
    }
}
?>
```
/app/Models/Product.php:

``
<?php

namespace App\Models;

use App\Core\Database;

class Product {
    public function all(): array {
        return Database::pdo()
        -> query("SELECT * FROM product")
        -> fetchAll();
    }

    public function find(int $id): ?array {
        $stm = Database::pdo() -> prepare ("SELECT * FROM product WHERE id=?");

        $stm -> execute([$id]);

        return $stm -> fetchAll() ?:null;
    }
}
?>
```

/app/Views/materials/index.php:

```
<h1>Все доступные материалы</h1>

<?php foreach ($materials as $material): ?>

    <div>
        <h3>
            <?= htmlspecialchars($material['name']) ?>
        </h3>
        <p>
            <?= htmlspecialchars($material['name']) ?>
            - <a href="/materials/<?= $material['id'] ?>">подробнее о материале</a>
        </p>
    </div>

<?php endforeach; ?>
```

/app/Views/materials/show.php:

```
<h1>Данные о материале</h1>

<p>Имя: <?=htmlspecialchars("{$material['name']}")?></p>

<p>Цена за грамм: <?=htmlspecialchars("{$material["price_per_gram"]}")?> рублей</p>
```

/app/Views/layout.php:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <main>
        <?= $content?>
    </main>
</body>
</html>
```