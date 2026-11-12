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

