<?php
/**
 * Interfaz que sera implementada por los multiples Request.
 */

namespace Fw\Core\Request;

interface RequestInterface
{
    /**
     * Se valida y ejecuta la solicitud.
     *
     **/
    public function send();
}
