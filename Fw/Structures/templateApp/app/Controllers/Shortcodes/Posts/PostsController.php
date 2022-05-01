<?php
/**
 * Shortcode para mostrar posts.
 */

# namespace;

class PostsController
{
    /**
     * Constructor.
     *
     * @param array $attrs Atributos del Shortcode.
     * @return Exception|void
     * @throws method_exists($this, $type)
     **/
    public function __construct(array $attrs)
    {
        # Validación del tipo.
        $type = ( array_key_exists('type', $attrs) && !empty($attrs['type']) ) ? $attrs['type'] : $type = 'posts';
        # Validación del limite.
        $limit = ( array_key_exists('limit', $attrs) && !empty($attrs['limit']) ) ? $attrs['limit'] : $limit = 5;

        # Validación que la funcion relacionada al tipo, exista.
        if ( !method_exists($this, $type) ) {
            throw new \Exception('The type does not exist.');
        }

        $this->type = $type;
        $this->limit = $limit;
    }
    
    /**
     * Metodo por default.
     *
     * @param array $attrs Atributos del Shortcode.
     **/
    public function index($attrs) 
    {
        unset( $attrs['type'] );

        return view('shortcodes/posts/posts', [
            'posts' => $this->{$this->type}($attrs)
        ]);
    }

    /**
     * Se obtienen los posts de acuerdo a los parámetros.
     *
     * @param array $attrs Atributos del Shortcode.
     * @return array
     * 
     * Ejemplo de uso del Shortcode: [carpeta_plugin Posts=Posts type="posts" limit=10]
     * Para más información de los parámetros de la función, ver: https://developer.wordpress.org/reference/functions/get_posts
     **/
    private function posts(array $attrs) : array
    {
        return get_posts([
            'numberposts' => $this->limit     
        ]); 
    }

    /**
     * Se obtienen las pages de acuerdo a los parámetros.
     *
     * @param array $attrs Atributos del Shortcode.
     * @return array
     * 
     * Ejemplo de uso del Shortcode: [carpeta_plugin Posts=Posts type="pages" limit=10]
     * Para más información de los parámetros de la función, ver: https://developer.wordpress.org/reference/functions/get_pages/
     **/
    private  function pages(array $atts) : array
    {
        return get_pages(array (
            'number' => $this->limit
        ));
    }

}
