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

