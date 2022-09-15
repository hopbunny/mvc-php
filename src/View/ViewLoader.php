<?php 

namespace View;

class ViewLoader 
{

    public static function render(string $viewName, array $args = []): string 
    {
        $viewPath = __DIR__.'/../../view/'.$viewName.'.php';
        if(!file_exists($viewPath)) {
            return "<p>View '{$viewName}' não encontrada!</p>";
        }   

        // extract()
        // [nome => 'João', idade => 18];
        // $nome = 'João';
        // $idade = 18;

        ob_start();

        extract($args);
        include $viewPath;
        
        return ob_get_clean();
    }

}