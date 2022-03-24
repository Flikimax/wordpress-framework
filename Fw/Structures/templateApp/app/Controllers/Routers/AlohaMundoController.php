<?php
/**
 * Router
 * 
 */

# namespace

class AlohaMundoController
{
    /** @var string $routeUrl URL que ejecutara este controlador. tusitio.com/wp-framework */
    // public static string $routeUrl = '/ruta-personalizada';
    
    /** @var bool $routeForce Forzar la URL (sobreescribe las URLs de WordPress). */
    public static bool $routeForce = true;
    
    /** @var bool $enableUri Habilita el uso de métodos por medio de url: tusitio.com/hola-mundo/metodo */
    public static bool $enableUri = true;

    # Constructor
    public function __construct ()
    { 
        # Algo de código.
    }

    /**
     * Método defaul: tusitio.com/wordpress-framework
     * 
     * @return Response
     */
    public function index()
    { 
        return view('layout', [
            'content' => view('routers/index', [
                'title' => 'Aloha Mundo',
                'message' => 'Aloha Mundo desde un controlador Router (método: index).',
            ]),
        ]);
    }

    /**
     * Método que se ejecutara al entrar a la url: tusitio.com/wordpress-framework/documentacion
     * 
     * @return Response
     */
    public function saludo()
    {
        return view('layout', [
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
        return view('layout', [
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
