<?php
/**
 * Interfaz que sera implementada por las multiples Response.
 */

namespace Fw\Init\Response;

interface ResponseInterface
{
    /**
     * Respuesta de las clases Response.
     *
     * @param string $pluginPath
     **/
    public function response(string $pluginPath);
}
