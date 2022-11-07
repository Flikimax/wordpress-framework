<?php
/**
 * Interfaz que sera implementada por los multiples Request.
 */

namespace Fw\Core\Request;

interface RequestInterface
{
    /**
     * Valida y ejecuta la solicitud.
     *
     **/
    public function send();
}
