<?php

use Fw\Core\Response\ResponseAsset;
use Fw\Paths;

if (!function_exists('asset')) {
    /**
     * Retorna una respuesta de tipo View.
     *
     * @param string $asset
     * @param string|null $file
     * @return ResponseAsset|string|null
     */
    function asset(string $asset, string $file = null): ResponseAsset|string|null
    {
        if ( !$file ) {
            return new ResponseAsset('asset', $asset);
        }

        if ( $pluginPath = Paths::findPluginPath($file) ) {
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
     * @param string $asset
     * @return string|null
     */
    function assetPath(string $pluginPath, string  $asset) : ?string
    {
        if ( file_exists($assetPath = Paths::buildPath($pluginPath, 'assets', $asset)) ) {
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
     * @param string $asset
     * @return string|null
     */
    function assetUrl(string $pluginPath, string $asset) : ?string
    {
        if ( file_exists($assetPath = Paths::buildPath($pluginPath, 'assets', $asset)) ) {
            return Paths::findPluginUrl($assetPath) . "/assets/$asset";
        } else if (WP_DEBUG) {
            echo "<strong>Details:</strong> $asset asset does not exist. <br>";
        }

        return null;
    }
}
