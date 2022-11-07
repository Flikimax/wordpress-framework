<?php
/**
 * Respuesta de tipo asset.
 * 
 */

namespace Fw\Core\Response;

use Fw\Core\Response\Response;
use Fw\Core\Response\ResponseInterface;

class ResponseAsset extends Response implements ResponseInterface
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
