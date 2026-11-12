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