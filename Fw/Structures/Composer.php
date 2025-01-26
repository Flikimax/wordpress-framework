<?php
/**
 * Generador del composer psr-4 y files.
 * 
 */

namespace Fw\Structures;

use Fw\Structures\BuildStructures;
use Fw\Paths;

class Composer  
{ 
    /**
     * @param array $args argumentos necesarios y requeridos para construir el composer.
     * @return void
     **/
    public static function buildComposer($args) : void
    {
        if ($args = self::validateData($args)) {
            # Creación de los files para el composer.
            self::createStructure($args);
        }
    }

    /**
     * Valida los argumentos requeridos.
     *
     * @param array $args argumentos necesarios y requeridos para construir el composer.json.
     * @return mixes
     **/
    public static function validateData(array $args)
    {
        if ( !array_key_exists('composerPath', $args) ) {
            echo "Se requiere el key `composerPath`.";
            return false;
        }
        if ( !array_key_exists('composer', $args) ) {
            $args['composer'] = $args['composer']['psr-4'] = $args['composer']['files'] = array();
        }

        return $args;
    }

    /**
     * Crea el composer.
     *
     * @param array $args argumentos necesarios y requeridos para construir el composer.
     * @return void
     **/
    public static function createStructure(array $args) : void
    {
        $templateAutoloadPath = Paths::buildPath(WPFW_PATH, 'Fw', 'Structures', 'templateAutoload', 'composer.json');
        $composerCopyPath = $args['composerPath'];
        $basePlugin = dirname($composerCopyPath);
        $copy = Paths::buildPath($composerCopyPath);

        if ( ! file_exists($copy) ) {
            BuildStructures::copyFile($templateAutoloadPath, $copy);

            $extension = pathinfo($copy, PATHINFO_EXTENSION);
            $method = 'create' . ucfirst( basename($copy, ".$extension") );
            $data = BuildStructures::$fileSystemDirect->get_contents($copy);

            $content = self::{$method}($args, $data);
            $content = self::createAutoloadFiles($args, $content);

            if ($content) {
                file_put_contents($copy, $content);
            }

            # Comando a ejecutar para instalar dependencias.
            $comando = "cd {$basePlugin} && composer install";

            # Ejecutar el comando
            shell_exec($comando);
        }
    }

    # =========== Creación de folder & files del Autoload ===========

    /**
     * Crea el file composer/composer.json
     **/
    private static function createComposer(array $args, string $data) : ?string
    {
        if ( !isset($args['composer']['psr-4']) || !$args['composer']['psr-4'] ) {
            return null;
        }

        foreach ($args['composer']['psr-4'] as $namespace => $path) {
            $namespace = str_replace('\\', '\\\\', $namespace);
            $path = trim($path, '/');
        }

        return str_replace('# %PSR-4%', $namespace, $data);
    }

    /**
     * Crea el file composer/composer.json
     **/
    private static function createAutoloadFiles(array $args, string $data) : ?string
    {
        if ( !isset($args['composer']['files']) || !$args['composer']['files'] ) {
            return str_replace('# %FILES%', '', $data);
        }

        ob_start();
        echo <<<EOT
        ,
                "files": [
        EOT;
        foreach ($args['composer']['files'] as $key => $filePath) {
            $comma = $key < count($args['composer']['files']) - 1 ? ',' : '';
            $filePath = str_replace('\\', '/', ltrim($filePath, "/"));
            if ($filePath[0] === "/") {
                $filePath = substr($filePath, 1);
            }

            echo <<<EOT

                        "$filePath"$comma
            EOT;
        }
        echo <<<EOT

                ]
        EOT;

        return str_replace('# %FILES%', ob_get_clean(), $data);
    }

}
