<?php
if (!function_exists('view')) {
    /**
     * Retorna una respuesta de tipo View.
     * 
     * @param string $view Nombre de la vista.
     * @param string $args Parametros para la vista.
     * @return Response
     */
    function view($view, $args = null)
    {
        return new Fw\Init\Response\ResponseView('view', $view, $args);
    }
}

if (!function_exists('viewPath')) {
    /**
     * Valida si una vista existe y retorna su path.
     * 
     * @param string $pluginPath Ruta base.
     * @param string $view Nombre de la vista.
     * @return string|null
     */
    function viewPath(string $pluginPath, string  $view) : ?string
    {
        if ( file_exists( $path = Fw\Paths::BuildPath($pluginPath, 'views', "$view.php")) ) {
            return Fw\Paths::BuildPath($pluginPath, 'views', "$view.php");
        } else if (WP_DEBUG) {
            echo "<strong>Details:</strong> $view view does not exist. <br>";
        }

        return null;
    }
}