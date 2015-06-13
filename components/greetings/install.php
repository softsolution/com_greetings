<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
function info_component_greetings() {
    $_component['title'] = '������������';                                          //�������� 
    $_component['description'] = '��������� ������������ ��� InstantCMS';           //��������
    $_component['link'] = 'greetings';                                               //������ (�������������)
    $_component['author'] = 'soft-solution.ru';                                     //�����
    $_component['internal'] = '0';                                                  //���������� (������ ��� �������)? 1-��, 0-���
    $_component['version'] = '1.0';                                                 //������� ������

    return $_component;
}

function install_component_greetings() {

    $inCore = cmsCore::getInstance();                                //���������� ����
    $inDB = cmsDatabase::getInstance();                              //���������� ���� ������
    $inConf = cmsConfig::getInstance();

    include($_SERVER['DOCUMENT_ROOT'] . '/includes/dbimport.inc.php');

    dbRunSQL($_SERVER['DOCUMENT_ROOT'] . '/components/greetings/install.sql', $inConf->db_prefix);

    return true;
}

function upgrade_component_greetings() {
    
    return true;

}

?>