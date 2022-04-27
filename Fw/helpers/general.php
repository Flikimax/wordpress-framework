<?php
/**
 * Separa un string utilizando las letras en mayusculas.
 * Ejemplo: FrameworkWp
 * Resultado: Framework Wp
 *
 * @param string $string
 * @param string $separator
 * @return string
 **/
function spaceUpper(string $string, string $separator = ' ') : string
{
    $string = preg_replace( '/[A-Z]/', "{$separator}$0",  $string);
    $string = trim($string, $separator);
    
    return $string;
}

/**
 * Convierte un string a un slug.
 *
 * @param string $string
 * @param string $separator Separador de palabras.
 * @return string
 **/
function strToSlug(string $string, string $separator = '-') : string
{
    $string = remove_accents($string);
    $string = strtolower($string);
    $string = str_replace(' ', $separator, $string);

    return $string;
}

/**
 * Retorna la url base de un controlador.
 * Ejemplo: Controllers\Routes\AlohaMundoController => /aloha-mundo
 * 
 * @param string $routeController
 * @return string
 **/
function routeUrl(string $routeController) : string
{
    # Se remueve la palabra controller, se recorta el nombre de la clase y se remueven los acentos.
    $routeController = str_replace('Controller', '', $routeController);
    $routeController = basename($routeController, '\\');
    $routeController = remove_accents($routeController);
    
    $routeController = preg_replace( '/[A-Z]/', '-$0',  $routeController);
    $routeController = strtolower( ltrim($routeController, '-') );
    $routeController = str_replace( ' ', '-', $routeController );
    
    return '/' . trim($routeController, '/');
}
