<?php 

namespace Boot\Service;

use Database\Database;

class DatabaseService extends BootService
{

    public function boot(): void
    {
        if(!Database::connect()) {
            echo "<h1>Erro 500 (DB)</h1>";
            http_response_code(500);
            die;
        }
    }

}