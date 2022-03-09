<?php
/**
 * Configuraciones de la aplicación.
 * 
 */

namespace Fw\Config;

use Fw\Config\Singleton;

class Apps extends Singleton
{
    /** @var string|null $currentPluginSlug Slug de la App actual. */
    private static ?string $currentPluginSlug = null;

    /** @var object|null $instance Instancia de la App actual. */
    private static ?object $instance = null;

    /**
     * Se obtiene la configuración de una App especificada.
     *
     * @param string $pluginSlug Slug de la App a buscar.
     * @return object|null
     */
    public static function getApp (string $pluginSlug) : ?object
    {   
        # Se valida que exista la App.
        if ( !isset( self::$instances[static::class]->{$pluginSlug} ) ) {
            return null;
        }
        
        return self::$instances[static::class]->{$pluginSlug};
    }

    /**
     * Obtiene una configuración de una App.
     *
     * @param string $pluginSlug Slug de la App.
     * @param string $configName Nombre de la configuración a buscar.
     * @return mixed
     */
    public static function getConfig (string $pluginSlug, string $configName) : mixed
    {
        if ( !isset(self::$instances[static::class]->{$pluginSlug}->$configName) ) {
            return null;
        }

        return self::$instances[static::class]->{$pluginSlug}->$configName;
    }


    /**
     * Recibe y setea las configuraciones de una App.
     *
     * @param string $pluginSlug Slug del plugin.
     * @param mixed $configs Configuraciones de la App.
     * @return void
     */
    public static function setApp (string $pluginSlug, mixed $configs = []) : void
    {   
        self::$currentPluginSlug = $pluginSlug;
        self::$instance = (object) [];

        foreach ($configs as $name => $config) {
            self::setConfig(
                $name,
                $config
            );
        }
        
        self::$instances[static::class]->{$pluginSlug} = self::$instance;
        self::$currentPluginSlug = null;
        self::$instance = null;
    }

    /**
     * Setea una configuración en una App especifica.
     *
     * @param string $pluginSlug Slug del plugin.
     * @param string $property Propiedad que se quiere obtener.
     * @return mixed Valor.
     */
    public static function setConfig (string $name, mixed $config, string $pluginSlug = '')
    {
        # Se valida que no exista la propiedad.
        if ( property_exists( self::$instance, $name ) ) {
            return null;
        }

        # Se valida si se usara la instancia actual o el slug especificado.
        if ( self::$instance && empty($pluginSlug) ) {
            self::$instance->{$name} = $config;
        } else {
            self::$instances[static::class]->{$pluginSlug}->$configName = $config;
        }
    }

}