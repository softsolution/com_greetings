<?php
/* ****************************************************************************************** */
/* created by soft-solution.ru                                                                */
/* install.php of module greetings for InstantCMS 1.10.2                                      */
/* ****************************************************************************************** */

    function info_module_mod_greetings(){

        $_module['title']        = 'Поздравления';
        $_module['name']         = 'Поздравления';
        $_module['description']  = 'Модуль Поздравления выводит последние поздравления для компонента Поздравления';
        $_module['link']         = 'mod_greetings';
        $_module['position']     = 'sidebar';
        $_module['author']       = '<a href="http://soft-solution.ru">soft-solution.ru</a>';
        $_module['version']      = '1.1';

        $_module['config'] = array('greetingscount' => 5, 'showimages' => 1, 'imagewidth' => 90,  'showadd' => 1, 'showlink' => 0,  'maxlen' => 100, 'is_pag' => 0);
            
        return $_module;

    }

    function install_module_mod_greetings(){

        return true;

    }

    function upgrade_module_mod_greetings(){

        return true;
        
    }

?>