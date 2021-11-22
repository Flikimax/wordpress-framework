<?php
if (!function_exists('asset')) {
    /**
     * Retorna una respuesta de tipo View.
     * 
     * @param string $view Nombre de la vista.
     * @param string $args Parametros para la vista.
     * @return Response
     */
    function asset(string $asset, string $file = null)
    {
        if ( !$file ) {
            return new Fw\Init\Response\ResponseAsset('asset', $asset);
        }

        if ( $pluginPath = \Fw\Paths::findPluginPath($file) ) {
            if ( $assetPath = assetUrl($pluginPath, $asset) ) {
                return $assetPath;
            }
        }

        return null;
    }
}

if (!function_exists('assetPath')) {
    /**
     * Valida si un asset existe y retorna su path.
     * 
     * @param string $pluginPath Ruta base.
     * @param string $view Nombre de la vista.
     * @return string|null
     */
    function assetPath(string $pluginPath, string  $asset) : ?string
    {
        if ( file_exists($assetPath = \Fw\Paths::buildPath($pluginPath, 'assets', $asset)) ) {
            return $assetPath;
        } else if (WP_DEBUG) {
            echo "<strong>Details:</strong> $asset asset does not exist. <br>";
        }

        return null;
    }
}

if (!function_exists('assetUrl')) {
    /**
     * Valida si un asset existe y retorna su url.
     * 
     * @param string $pluginPath Ruta base.
     * @param string $view Nombre de la vista.
     * @return string|null
     */
    function assetUrl(string $pluginPath, string  $asset) : ?string
    {
        if ( file_exists($assetPath = \Fw\Paths::buildPath($pluginPath, 'assets', $asset)) ) {
            return \Fw\Paths::findPluginUrl($assetPath) . "/assets/$asset";
        } else if (WP_DEBUG) {
            echo "<strong>Details:</strong> $asset asset does not exist. <br>";
        }

        return null;
    }
}
