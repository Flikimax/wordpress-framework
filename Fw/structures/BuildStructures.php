<?php
/**
 * Metodos para la construcción de estructuras.
 * 
 */

namespace Fw\Structures;

use Fw\Paths;

class BuildStructures
{
    public static function init(array $structures, array $args)
    {
        foreach ($structures as $structure) {
            if ( method_exists(static::class, $structure) ) {
                self::{$structure}($args);
            }
        }
    }

        
    /**
     * Generador de los forlders de app.
     *
     * @param array $args Argumentos requeridos para la creación de la estructura. 
     * @return void
     **/
    public static function app(array $args) : void
    {
        // App::buildApp([
        //     'templatePath' => Paths::buildPath($args['pluginPath'], 'app'),
        //     'mode' => $args['mode']
        // ]);
    }

        
    /**
     * Generador del autoload de la App.
     *
     * @param array $args Argumentos requeridos para la creación de la estructura. 
     * @return void
     **/
    public static function autoload(array $args) : void
    {
        Autoload::buildAutoload([
            'composerPath' => Paths::buildPath($args['pluginPath'], 'autoload'),
            'uniqueName' => $args['autoload']['uniqueName'],
            'autoload' => [
                'psr-4' => $args['autoload']['psr-4'],
                'files' => $args['autoload']['files']
            ],
            'mode' => $args['mode']
        ]);
    }



    /**
     * Crear una carpeta en una ruta especifica.
     *
     * @param string $path Ruta del directorio.
     * @param int $permissions Permisos para el directorio.
     * @return void
     **/
    public static function createFolder(string $path, int $permissions) : void
    {
        $old_umask = umask(0);
        mkdir($path, $permissions);
        umask($old_umask);
    }

    /**
     * Crear un file en una ruta especifica.
     *
     * @param string $path Ruta del directorio.
     * @param string $content contenido para el file.
     * @return Mixes
     **/
    public static function createFile(string $path, string $content)
    {
        # Se crea el archivo si no existe.
        $file = fopen($path, "w+b");
        if ( $file == false ) {
            echo "Error al crear el archivo: " . basename($path);
        } else {
            # Se escribe el contenido.
            fwrite($file, $content);
            # Fuerza a que se escriban los datos pendientes en el buffer.
            fflush($file);
        }
        # Cerrar el archivo
        fclose($file);
    }

    /**
     * Copia un file en una ruta especifica.
     *
     * @param string $file Ruta del file que se copiara.
     * @param string $copy Ruta para el nuevo file.
     * @return Mixes
     **/
    public static function copyFile(string $file, string $copy)
    {
        if ( !copy($file, $copy) ) {
            echo "Error al copiar: $file";
        }
    }
}
