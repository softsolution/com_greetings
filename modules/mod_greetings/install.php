<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

    function info_module_mod_greetings(){

        //
        // �������� ������
        //

        //��������� (�� �����)
        $_module['title']        = '��������� ������������';

        //�������� (� �������)
        $_module['name']         = '��������� ������������';

        //��������
        $_module['description']  = '������ ��������� ������������ ��� ���������� ������������';
        
        //������ (�������������)
        $_module['link']         = 'mod_greetings';
        
        //�������
        $_module['position']     = 'sidebar';

        //�����
        $_module['author']       = 'soft-solution.ru';

        //������� ������
        $_module['version']      = '1.0';

        //
        // ��������� ��-���������
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