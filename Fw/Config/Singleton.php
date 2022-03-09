<?php
/**
 * Singleton para el Framework.
 * 
 */

namespace Fw\Config;

class Singleton
{
    /** @var array $instances Instancias del Singleton. */
    protected static array $instances = [];

    # Constructor protegido para que solo pueda ser instanciado desde el método getInstance.
    protected function __construct() { }

    /**
     * Cración del Singleton.
     *
     * @return Singleton
     **/
    public static function getInstance() : Singleton
    {
        $class = static::class;
        if ( !isset( self::$instances[$class] ) ) {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }

    # La clonación y la deserialización no están permitidas para los singletons.
    protected function __clone() {}

    public function __wakeup()
    {
        throw new \Exception("No se puede deserializar un singleton.");
    }
}
