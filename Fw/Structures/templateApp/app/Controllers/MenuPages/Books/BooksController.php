<?php
/**
 * Menu Page Books.
 */

# namespace;

# use # namespace\CategoriesController as Categories;

class BooksController
{
    # Opcionales.
    # https://developer.wordpress.org/reference/functions/add_menu_page
    public static $pageTitle  = 'Fw Libros';
    public static $menuTitle  = 'Fw Libros';
    public static $capability = 'install_plugins';
    public static $menuSlug   = 'fw-libros';
    public static $icon       = 'dashicons-book-alt';
    
    /**
     * Metodo por default.
     *
     **/
    public function index( array $params )
    {
        return view('menuPages/books/layout', [
            'content' => view('menuPages/books/index', [
                'categoryList' => $this->getCategoryList(),
                'baseUrl' => $params['baseUrl']
            ]),
        ]);
    }

    /**
     * Obtiene la lista de categorias.
     * Método no accesible por url (privado). 
     *
     * @return array
     **/
    private function getCategoryList() : array
    {
        return Categories::$list;
    }

    /**
     * Página de error 404.
     *
     **/
    public function error404()
    {
        return view('menuPages/books/layout', [
            'content' => view('menuPages/404')
        ]);
    }

}