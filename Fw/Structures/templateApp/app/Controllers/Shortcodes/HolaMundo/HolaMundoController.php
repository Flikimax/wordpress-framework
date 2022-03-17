<?php
/**
 * Shortcode
 * 
 */

# namespace

class HolaMundoController
{
    public function index()
    { 
        return view('layout', [
            'content' => view('index'),
        ]);
    }

}
