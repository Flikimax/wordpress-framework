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

