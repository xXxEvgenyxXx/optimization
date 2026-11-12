<?php 

namespace App\Core;

use App\Core;

abstract class Controller{
    protected function view(string $template, array $data = []):void{
        View::render($template,$data);
    }
}

?>