<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

    function info_module_mod_greetings(){

        //
        // Описание модуля
        //

        //Заголовок (на сайте)
        $_module['title']        = 'Последние поздравления';

        //Название (в админке)
        $_module['name']         = 'Последние поздравления';

        //описание
        $_module['description']  = 'Модуль Последние поздравления для компонента Поздравления';
        
        //ссылка (идентификатор)
        $_module['link']         = 'mod_greetings';
        
        //позиция
        $_module['position']     = 'sidebar';

        //автор
        $_module['author']       = 'soft-solution.ru';

        //текущая версия
        $_module['version']      = '1.0';

        //
        // Настройки по-умолчанию
        //
        $_module['config'] = array();
	$_module['config']['greetingscount'] = 5;
        $_module['config']['showimages'] = 1;
        $_module['config']['imagewidth'] = 90;
        $_module['config']['addgreetings'] = 1;
        $_module['config']['showlink'] = 0;
        $_module['config']['maxlen'] = 100;
        $_module['config']['is_pag'] = 0;

        return $_module;

    }

// ========================================================================== //

    function install_module_mod_greetings(){

        return true;

    }

// ========================================================================== //

    function upgrade_module_mod_greetings(){

        return true;
        
    }

// ========================================================================== //

?>