<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
function routes_greetings() {

    //просмотр поздравления (на будующее)
//    $routes[] = array(
//        '_uri' => '/^greetings\/read([0-9]+).html$/i',
//        'do' => 'read',
//        1 => 'id'
//    );

    //добавление поздравления
    $routes[] = array(
        '_uri' => '/^greetings\/add.html$/i',
        'do' => 'add'
    );

    //редактирование поздравления
    $routes[] = array(
        '_uri' => '/^greetings\/edit([0-9]+).html$/i',
        'do' => 'edit',
        1 => 'id'
    );
    
    //удаление поздравления
    $routes[] = array(
        '_uri' => '/^greetings\/delete([0-9]+).html$/i',
        'do' => 'delete',
        1 => 'id'
    );
    
    //поздравления пользователя (пагинация)
    $routes[] = array(
        '_uri' => '/^greetings\/my\/page-([0-9]+)$/i',
        'do' => 'view',
        'target' => 'my',
        1 => 'page'
    );
    
    //поздравления пользователя
    $routes[] = array(
        '_uri' => '/^greetings\/my$/i',
        'do' => 'view',
        'target' => 'my'
    );
    
    //все поздравления (пагинация)
    $routes[] = array(
        '_uri' => '/^greetings\/page-([0-9]+)$/i',
        'do' => 'view',
        'target' => 'all',
        1 => 'page'
    );
    
    //все поздравления
    $routes[] = array(
        '_uri' => '/^greetings\/$/i',
        'do' => 'view',
        'target' => 'all'
    );

    return $routes;
}

?>