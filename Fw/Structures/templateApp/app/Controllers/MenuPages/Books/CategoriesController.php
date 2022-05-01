<?php
/**
 * Sub Menu Page, Categories.
 */

# namespace;

class CategoriesController
{
    public static $position = 1;

    /** @var array $list Lista de Categorias */
    public static $list = [
        'adventures'      => 'Adventures',
        'science-fiction' => 'Science Fiction',
        'fairy-tales'     => 'Fairy Tales',
        'gothic'          => 'Gothic',
        'crime'           => 'Crime',
        'romance'         => 'Romance',
        'dystopian'       => 'Dystopian',
        'fantastic'       => 'Fantastic',
    ];

    public function index( array $params )
    {
        # Si no llega cat
        if ( ! isset($_GET['cat']) ) {
            return view('menuPages/books/layout', [
                'content'  => view('menuPages/books/index', [
                    'categoryList' => $this->list(),
                    'baseUrl' => $params['baseUrl']
                ]),
            ]);
        }

        # Si llega una categoria por url y existe en la lista.
        if ( !empty($_GET['cat']) && array_key_exists($_GET['cat'], $this->list()) ) {
            return $this->show( $_GET['cat'] );
        } 

        # En caso de error.
        return $this->error404();
    }

    /**
     * Muestra una categoria especifica.
     *
     * @param string $category Slug de la categoria.
     **/
    public function show(string $category)
    {
        return view('menuPages/books/layout', [
            'content' => view('menuPages/books/show', [
                'category' => self::$list[$category]
            ]),
        ]);
    }

    /**
     * Retorna la lista de categorias.
     *
     * @return array
     **/
    public function list() : array
    {
        return self::$list;
    }

    /**
     * Página de error 404.
     * En este caso de hace privado el método para que no se pueda acceder desde otro lugar.
     *
     * @param Type $var Description
     **/
    private function error404()
    {
        return view('menuPages/books/layout', [
            'content' => view('menuPages/404')
        ]);
    }
    
}