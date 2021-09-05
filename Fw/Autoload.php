<?php

namespace Fw;

/**
 * Generador del autoload psr-4 y files
 */
class Autoload  
{
    /**
     * Si la estructura del autoload no existe o no esta completa, la crea
     *
     * @param Array $args argumentos necesarios y requeridos para construir el autoload
     * @return Void
     **/
    public static function buildAutoload(array $args) : void
    {
        # Si esta en modo Dev (Desarrollo), se borra la carpeta para actualizar constantemente
        if ( array_key_exists('mode', $args) && method_exists(static::class, "{$args['mode']}Mode") ) { 
            self::{$args['mode'] . "Mode"}($args);
        }

        # Validacion de la estructura del autoload
        if ( !self::validateStructure($args['composerPath']) ) {
            if ($args = self::validateData($args)) {
                # Creacion de folders y files para el autoload
                self::createStructure($args);
            }
        }
    }

    /**
     * Acciones para el modo desarrollo
     *
     * @param Array $args argumentos necesarios y requeridos para construir el autoload
     * @return Void
     **/
    public static function devMode(array $args) : void
    {
        self::removeAutoload($args['composerPath']);
    }

    /**
     * Valida los argumentos requeridos 
     *
     * @param Array $args argumentos necesarios y requeridos para construir el autoload
     * @return Mixes
     **/
    public static function validateData(array $args)
    {
        if ( !array_key_exists('composerPath', $args) ) {
            echo "Se requiere el key `composerPath`.";
            return false;
        }
        if ( !array_key_exists('uniqueName', $args) ) {
            $args['uniqueName'] = self::createUniqueName(basename(dirname($args['composerPath'])));
        }
        if ( !array_key_exists('autoload', $args) ) {
            $args['autoload'] = $args['autoload']['psr-4'] = $args['autoload']['files'] = array();
        } else {
            if ( array_key_exists('files', $args['autoload']) ) {
                $files = array();
                foreach ($args['autoload']['files'] as $key => $file) {
                    $files[ str_shuffle( hash('tiger192,3', $file . time()) ) ] = $file;
                }
                $args['autoload']['files'] = $files;
            }
        }

        return $args;
    }

    /**
     * Valida si en la ruta pasada, existe toda la estructura requerida para el autoload
     *
     * @param String $path Ruta del directorio donde se verificara la estructura ($path/*)
     * @return Bool
     **/
    public static function validateStructure(string $path) : bool
    {
        # Si alguno de los folders o files principales no existe, retorna false
        if ( !file_exists("$path") || !file_exists("$path/composer") || !file_exists("$path/autoload.php") ) {
            return false;
        }

        $structurePath = "$path/composer";
        $structureComposer = [
            "$structurePath/ClassLoader.php",
            "$structurePath/autoload_classmap.php",
            "$structurePath/autoload_namespaces.php",
            "$structurePath/autoload_psr4.php",
            "$structurePath/autoload_real.php",
            "$structurePath/autoload_static.php",
        ];

        $files = glob("$path/composer/*.php");
        # Validacion de la estructura interna del autoload
        if ( $files != $structureComposer ) {
            return false;
        }
        
        # Si la estructura esta completa
        return true;
    }

    /**
     * Crea el autoload
     *
     * @param Array $args argumentos necesarios y requeridos para construir el autoload
     * @return Void
     **/
    public static function createStructure(array $args) : void
    {
        $templateAutoloadPath = Paths::buildPath(WPFW_PATH, 'Fw', 'structures', 'templateAutoload');
        $composerCopyPath = Paths::buildPath($args['composerPath'], 'composer');

        # FOLDERS
        if ( !file_exists($args['composerPath']) ) {
            Paths::createFolder($args['composerPath'], 0755);
        }
        if ( !file_exists($composerCopyPath) ) {
            Paths::createFolder($composerCopyPath, 0755);
        }

        # COMPOSER FILES
        foreach (glob(Paths::buildPath($templateAutoloadPath, 'composer', '*.php')) as $file) {
            if ( !file_exists($copy = Paths::buildPath($composerCopyPath, basename($file))) ) {
                Paths::copyFile($file, $copy);
                $data = file_get_contents($copy);

                self::{'create' . ucfirst( basename($copy, '.php') )}($args, $data);

                $content = self::{'create' . ucfirst( basename($copy, '.php') )}($args, $data);
                if ($content) {
                    
                    file_put_contents($copy, $content);
                    
                }

            }
        }

        # MAIN FILES
        foreach (glob(Paths::buildPath($templateAutoloadPath, '*.php')) as $file) {
            if ( !file_exists($copy = Paths::buildPath(dirname($composerCopyPath), basename($file))) ) {
                Paths::copyFile($file, $copy);
                $data = file_get_contents($copy);

                $content = self::{'create' . ucfirst( basename($copy, '.php') )}($args, $data);
                if ($content) {
                    file_put_contents($copy, $content);
                }
            }
        }
    }

    /**
     * Borra el autoload
     *
     * @param String $path Ruta del folder principal de composer
     * @return Bool|String
     **/
    public static function removeAutoload(string $path) 
    {
        if ( !file_exists($path) ) {
            return true;
        }

        try {
            $it = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
            $it = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
            foreach($it as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }
            rmdir($path);
        } catch (Exception $e) {
            echo 'Error al eliminar el autoload: ',  $e->getMessage(), "\n";
            return false;
        }

        return true;
    }

    /**
     * Crea y retorna el namespace usando basename del path pasado
     *
     * @param String $name Ruta|Nombre para retornar para usar como namespace
     * @return String
     **/
    public static function createNamepace(string $name) : string 
    {
        $namespace = basename($name, '.php');
        $namespace = ucwords($namespace, '-');
        $namespace = str_replace('-', '', $namespace);

        return $namespace;
    }

    /**
     * Crea y retorna el nombre unico para el autoload
     *
     * @param String $path Ruta del folder principal
     * @return String
     **/
    public static function createuniqueName(string $name) : string
    {
        return (string) 'Fw' . self::createNamepace($name) . time();
    }
    
    # =========== CREACION DE FOLDERS & FILES DEL AUTOLOAD ===========

    /**
     * Crea el file composer/createAutoload_psr4.php
     *
     * @param Array $args argumentos necesarios y requeridos para construir el autoload
     * @param String $data Contenido de la plantilla
     * @return Null|String
     **/
    public static function createAutoload_classMap(array $args, string $data) : ?string
    {
        return  null;
    }

    public static function createAutoload_namespaces(array $args, string $data) : ?string
    {
        return null;
    }
    
    public static function createClassLoader(array $args, string $data) : ?string
    {
        return null;
    }

    public static function createAutoload_psr4(array $args, string $data) : ?string
    {
        if ( !isset($args['autoload']['psr-4']) || !$args['autoload']['psr-4'] ) {
            return null;
        }

        ob_start();
        foreach ($args['autoload']['psr-4'] as $namespace => $path) {
            $namespace = str_replace('\\', '\\\\', $namespace);
            $path = trim($path, '/');

            echo "'$namespace' => array(\$baseDir . '/$path'),\n    ";
        }

        return str_replace('# %PSR-4%', ob_get_clean(), $data);
    }

    public static function createAutoload_files(array $args, string $data) : ?string
    {
        if ( !isset($args['autoload']['files']) || !$args['autoload']['files'] ) {
            return null;
        }
        ob_start();
        foreach ($args['autoload']['files'] as $hash => $filePath) {
            echo "'$hash' => \$baseDir . '" . trim($filePath, '/') . "',\n    ";
        }

        return str_replace('# %FILES%', ob_get_clean(), $data);
    }

    public static function createAutoload_static(array $args, string $data) : ?string
    {
        if ( !isset($args['uniqueName']) || empty($args['uniqueName']) ) {
            return null;
        }

        # CLASE
        $newData = str_replace('ComposerStaticInit', "ComposerStaticInit{$args['uniqueName']}", $data);

        # AUTOCARGAR FILES
        ob_start();
        if ( isset($args['autoload']['files']) && is_array($args['autoload']['files']) ) {
            echo "public static \$files = array (\n";
            foreach ($args['autoload']['files'] as $hash => $file) {
                echo "        '$hash' => __DIR__ . '/../../' . '" . trim($file, '/') . "',\n";
            }
            echo "    );";
            $newData = str_replace('# %FILES%', ob_get_clean(), $newData);
        } 
 
        # LENGTHS PSR4
        ob_start();
        echo "public static \$prefixLengthsPsr4 = array (\n";
        foreach ($args['autoload']['psr-4'] as $namespace => $path) {
            $namespaceLen = strlen($namespace);
            $namespace = str_replace('\\', '\\\\', $namespace);

            echo <<<EOT
                    '{$namespace[0]}' => array (
                        '$namespace' => $namespaceLen,
                    ),

            EOT;
        }
        echo "    );";  
        $newData = str_replace('# %LENGTHSPSR4%', ob_get_clean(), $newData);

        # DIRS PSR4
        ob_start();
        echo "public static \$prefixDirsPsr4 = array (\n";
        foreach ($args['autoload']['psr-4'] as $namespace => $path) {
            $namespace = str_replace('\\', '\\\\', $namespace);
            $path = trim($path, '/');

            echo <<<EOT
                    '$namespace' => array (
                        0 => __DIR__ . '/../..' . '/$path',
                    ),

            EOT;
        }
        echo "    );";
        return str_replace('# %DIRSPSR4%', ob_get_clean(), $newData);
    }

    public static function createAutoload_real(array $args, string $data) : ?string
    {
        if ( !isset($args['uniqueName']) || empty($args['uniqueName']) ) {
            return null;
        }

        # CLASE PRINCIPAL
        $newData = str_replace('ComposerAutoloaderInit', "ComposerAutoloaderInit{$args['uniqueName']}", $data);
        # STATIC CLASS
        $newData = str_replace('ComposerStaticInit', "ComposerStaticInit{$args['uniqueName']}", $newData);

        # AUTOCARGAR FILES
        if ( isset($args['autoload']['files']) && is_array($args['autoload']['files']) ) {
            ob_start();
            # %FILES%
            echo <<<EOT
            if (\$useStaticLoader) {
                        \$includeFiles = Composer\Autoload\ComposerStaticInit{$args['uniqueName']}::\$files;
                    } else {
                        \$includeFiles = require __DIR__ . '/autoload_files.php';
                    }
                    foreach (\$includeFiles as \$fileIdentifier => \$file) {
                        composerRequire{$args['uniqueName']}(\$fileIdentifier, \$file);
                    }
            EOT;
            $newData = str_replace('# %FILES%', ob_get_clean(), $newData);
            
            # %FUNCTION_FILES%
            ob_start();
            echo <<<EOT
            function composerRequire{$args['uniqueName']}(\$fileIdentifier, \$file)
            {
                if (empty(\$GLOBALS['__composer_autoload_files'][\$fileIdentifier])) {
                    require \$file;
                    
                    \$GLOBALS['__composer_autoload_files'][\$fileIdentifier] = true;
                }
            }
            EOT;
            $newData = str_replace('# %FUNCTION_FILES%', ob_get_clean(), $newData);
        } 

        return $newData;
    }

    public static function createAutoload(array $args, string $data) : ?string
    {
        return str_replace('ComposerAutoloaderInit', "ComposerAutoloaderInit{$args['uniqueName']}", $data);
    }
}
