<?php
/**
 * Clase de excepción personalizada.
 */

namespace Fw\Init\Exceptions; 
 
class General extends \Exception
{
    /**
     * Contructor.
     *
     * Inicializa el mensaje de error.
     * Inicializa la excepción.
     *
     * @param string $message Mensaje de error.
     * @param int $code Código de error.
     * @param Exception|null $previous Objeto de excepción.
     * @return void
     **/
    public function __construct(string $message, int $code = 0, Exception $previous = null)
    {
        $messageHtml = '<strong>Details.</strong><br>';
        $messageHtml .= "{$message}<br>";

        parent::__construct($messageHtml, $code, $previous);
    }

    /**
     * Retorna el mensaje de error.
     *
     * @return string
     **/
    public function getError() : string
    {
        $message = '';
        if (WP_DEBUG) {
            $message .= $this->getMessage();
        }
        return $message . $this->getStatusCode();
    }

    /**
     * Genera una excepción personalizada.
     * Dependiendo el tipo del error, se genera un mensaje personalizado o uno por defecto.
     *
     * @return string
     **/
    public function getStatusCode() : string
    {
        $errorCode = $this->getCode();
        $message = 'Error code: ';
        switch ( $errorCode ) {
            case 403:
                $message .= "{$errorCode} Forbidden.";
                break;
            case 404:
                $message .= "{$errorCode} Not Found.";
                break;
            default:
                $message = "{$errorCode} Unknown error.\n";
                break;
        }

        return $message;
    }
    
}