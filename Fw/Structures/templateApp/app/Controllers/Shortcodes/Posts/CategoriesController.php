<?php
/**
 * Shortcode para mostrar categorías.
 */

# namespace;

class CategoriesController 
{
    /**
     * Se obtienen y se muestran las categorías.
     * Metodo por default.
     * Ejemplo de uso del Shortcode: [carpeta_plugin Posts=Categories]
     * 
     * @param array $attrs Atributos del Shortcode.
     * 
     **/
    public function index($attrs)
    {
        $categories = get_categories();

        return view('shortcodes/posts/categories', [
            'categories' => $categories
        ]);
    }

}