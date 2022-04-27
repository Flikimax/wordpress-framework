<?php
if ( !function_exists('view') ) {
    /**
     * Retorna una respuesta de tipo View.
     * 
     * @param string $view Nombre de la vista.
     * @param string $args Parametros para la vista.
     * @return Response
     */
    function view($view, $args = null)
    {
        $view = str_replace('.php', '', $view);
        return new Fw\Init\Response\ResponseView('view', $view, $args);
    }
}

if ( !function_exists('viewPath') ) {
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
            throw new \Fw\Init\Exceptions\General("Path view: ${view}", 404);
        }

        return null;
    }
}

if ( !function_exists('getPart') ) {
    /**
     * Retorna una parte del sitio dependiendo si es un tema de tipo bloque o estandar.
     * 
     * @param string $part
     * @return string|null
     */
    function getPart( string $part ) : ?string
    {
        $isThemeTypeBlock = Fw\Init\Init::isThemeTypeBlock();

        switch ($part) {
            case 'header':
                return $isThemeTypeBlock ? block_template_part('header') : get_header();
                break;
            case 'footer':
                return $isThemeTypeBlock ? block_template_part('footer') : get_footer();
                break;
        }

        return null;
    }
}