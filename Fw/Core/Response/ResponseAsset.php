<?php
/**
 * Respuesta de tipo asset.
 * 
 */

namespace Fw\Core\Response;

class ResponseAsset extends Response
{
    /**
     * Response Asset.
     * Retorna la url del asset.
     *
     * @param string $pluginPath
     * @return string|null
     **/
    public function response(string $pluginPath) : ?string
    {
        if ( $assetPath = assetUrl($pluginPath, $this->getData()) ) {
            return $assetPath;
        }

        return null;
    }
}
