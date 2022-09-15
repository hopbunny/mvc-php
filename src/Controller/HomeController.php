<?php 

namespace Controller;

use View\ViewLoader;

class HomeController extends BaseController
{

    public function get() {
        return ViewLoader::render('home', ['pageTitle' => 'MVC - Início']);
    }
    
}