<?php
/* ****************************************************************************************** */
/* created by soft-solution.ru                                                                */
/* install.php of component greetings for InstantCMS 1.10.2                                   */
/* ****************************************************************************************** */
    function info_component_greetings() {
        $_component['title'] = 'Поздравления';
        $_component['description'] = 'Компонент Поздравления для InstantCMS';
        $_component['link'] = 'greetings';
        $_component['author'] = '<a href="http://soft-solution.ru">soft-solution.ru</a>';
        $_component['internal'] = '0';
        $_component['version'] = '1.1';

        return $_component;
    }

    function install_component_greetings() {

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

        cmsDatabase::getInstance()->importFromFile($_SERVER['DOCUMENT_ROOT'] . '/components/greetings/install.sql');
        
        if ($inCore->isComponentInstalled('billing')){
            cmsCore::loadClass('billing');
            if(!$inDB->rows_count('cms_billing_actions', "component='greetings' AND action='add_greetings'", 1)){
                cmsBilling::registerAction('greetings', array(
                    'name' => 'add_greetings',
                    'title' => 'Добавление поздравления')
                );
            }
        }
        
        return true;
    }


    function upgrade_component_greetings() {
        
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        
        if ($inCore->isComponentInstalled('billing')){
            cmsCore::loadClass('billing');
            if(!$inDB->rows_count('cms_billing_actions', "component='greetings' AND action='add_greetings'", 1)){
                cmsBilling::registerAction('greetings', array(
                    'name' => 'add_greetings',
                    'title' => 'Добавление поздравления')
                );
            }
        }

        return true;

    }

    function remove_component_greetings(){
	
        $inCore     = cmsCore::getInstance();
        $inDB       = cmsDatabase::getInstance();
        $inDB->query("DROP TABLE IF EXISTS cms_greetings");
		
    }

?>