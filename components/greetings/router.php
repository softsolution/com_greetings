<?php
/* ****************************************************************************************** */
/* created by soft-solution.ru                                                                */
/* router.php of component greetings for InstantCMS 1.10.2                                    */
/* ****************************************************************************************** */
function routes_greetings() {

    $routes[] = array(
        '_uri' => '/^greetings\/add.html$/i',
        'do' => 'add'
    );

    $routes[] = array(
        '_uri' => '/^greetings\/edit([0-9]+).html$/i',
        'do' => 'edit',
        1 => 'id'
    );
    
    $routes[] = array(
        '_uri' => '/^greetings\/delete([0-9]+).html$/i',
        'do' => 'delete',
        1 => 'id'
    );
    
    $routes[] = array(
        '_uri' => '/^greetings\/publish([0-9]+).html$/i',
        'do' => 'publish',
        1 => 'id'
    );
    
    $routes[] = array(
        '_uri' => '/^greetings\/read([0-9]+).html$/i',
        'do' => 'read',
        1 => 'id'
    );
    
    $routes[] = array(
        '_uri' => '/^greetings\/page-([0-9]+)$/i',
        'do' => 'view',
        1 => 'page'
    );
    
    $routes[] = array(
        '_uri' => '/^greetings\/$/i',
        'do' => 'view'
    );

    return $routes;
}

?>