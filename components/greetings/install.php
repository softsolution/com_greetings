<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
function info_component_greetings() {
    $_component['title'] = 'Поздравления';                                          //название 
    $_component['description'] = 'Компонент Поздравления для InstantCMS';           //описание
    $_component['link'] = 'greetings';                                               //ссылка (идентификатор)
    $_component['author'] = 'soft-solution.ru';                                     //автор
    $_component['internal'] = '0';                                                  //внутренний (только для админки)? 1-Да, 0-Нет
    $_component['version'] = '1.0';                                                 //текущая версия

    return $_component;
}

function install_component_greetings() {

    $inCore = cmsCore::getInstance();                                //подключаем ядро
    $inDB = cmsDatabase::getInstance();                              //подключаем базу данных
    $inConf = cmsConfig::getInstance();

    include($_SERVER['DOCUMENT_ROOT'] . '/includes/dbimport.inc.php');

    dbRunSQL($_SERVER['DOCUMENT_ROOT'] . '/components/greetings/install.sql', $inConf->db_prefix);

    return true;
}

function upgrade_component_greetings() {
    
    return true;

}

?>