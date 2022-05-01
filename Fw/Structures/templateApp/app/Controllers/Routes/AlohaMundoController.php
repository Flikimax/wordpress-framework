<?php
/**
 * Router
 * 
 */

# namespace;

# URL default: tusitio.com/aloha-mundo

class AlohaMundoController
{
    /** @var string $routeUrl URL personalizada que ejecutara este controlador. tusitio.com/ruta-personalizada */
    // public static string $routeUrl = '/ruta-personalizada';
    
    /** @var bool $routeForce Forzar la URL (sobreescribe las URLs de WordPress). */
    public static bool $routeForce = true;
    
    /** @var bool $enableUri Habilita el uso de métodos por medio de url: tusitio.com/aloha-mundo/metodo */
    public static bool $enableUri = true;

    # Constructor
    public function __construct ()
    { 
        # Algo de código.
    }

    /**
     * Método defaul: tusitio.com/aloha-mundo
     * 
     * @return Response
     */
    public function index()
    { 
        return view('routers/layout', [
            'content' => view('routers/index', [
                'title' => 'Aloha Mundo',
                'message' => 'Aloha Mundo desde un controlador Router (método: index).',
                'routeUrl' => routeUrl( __CLASS__ )
            ]),
        ]);
    }

    /**
     * Método que se ejecutara al entrar a la url: tusitio.com/aloha-mundo/saludo
     * 
     * @return Response
     */
    public function saludo()
    {
        return view('routers/layout', [
            'content' => view('routers/saludo', [
                'title' => 'Aloha Mundo',
                'message' => 'Aloha Mundo desde un controlador Router (método: saludo).',
            ]),
        ]);
    }

    /**
     * Método que se ejecuta cuando no se encuentra una ruta.
     * 
     * @return Response
     */
    public function error404()
    {
        return view('routers/layout', [
            'content' => view('404', [
                'backText' => 'GO HOME',
                'backLink' => site_url(),
            ]),
        ]);
    }

    # Método privado - No accecible desde url.
    private function private()
    {
        # No accesible por URL.
    }

    # Método protegido - No accecible desde url.
    protected function protected()
    {
        # No accesible por URL.
    }

}
