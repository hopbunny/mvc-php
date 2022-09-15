<?php

// estamos dizendo onde o php pode encontrar a classe Boot
// (este diretório não inclui a pasta src)
use Boot\Boot;

include_once __DIR__.'/config.php';

// estamos registrando a função que é responsável por carregar as classes
// (o php por padrão não sabe onde procurar suas classes e por isso é necessário)
spl_autoload_register(function(string $classPath) {
    $classPath = str_replace('\\', '/', $classPath);
    $classPath = __DIR__.'/src/'.$classPath.'.php';

    // para "encontrar" uma classe é utilizada a função include/require que
    // faz a classe existir no contexto deste arquivo - lembrando, tudo é um
    // diretório
    if(file_exists($classPath)) {
        include $classPath;
    }
});

// capturamos a url que foi acessada
$uri = parse_url($_SERVER['REQUEST_URI']);
$domainPath = parse_url(APP_HOST)['path'] ?? null;
if(!empty($domainPath)) {
    $uri = str_replace($domainPath, '', $uri);
}

// instanciamos/criamos a classe Boot que é responsável por carregar o nosso
// sistema e responder a requisição
$boot = new Boot($uri['path']);

echo $boot->executeController();
