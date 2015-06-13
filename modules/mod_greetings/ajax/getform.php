<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }
	header('Content-Type: text/html; charset=windows-1251'); 
	session_start();

    if (!isset($_REQUEST['module_id'])) { die(2); }

    define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

    // ������ ���� � ������
    include(PATH.'/core/cms.php');
    
    // ������ ������
    include(PATH.'/includes/config.inc.php');
    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());
    
    $inCore->loadClass('config'); 
    $inCore->loadClass('db'); 
    $inCore->loadClass('user');
    $inCore->loadClass('page');
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    $inUser->update();
    
    $is_admin  = $inCore->userIsAdmin($inUser->id);
    $user_id   = $inUser->id;
    
    // ������ ������������
    $smarty = $inCore->initSmarty();

    // ������� ������
    $module_id	= $inCore->request('module_id', 'int', '');
    
    //��������� ������������ ����������
    $cfg_com = $inCore->loadComponentConfig('greetings');
    
    if(!$cfg_com['guest_enabled'] && !$user_id){
        echo '<input name="formexist" id="formexist" type="hidden" value="guest_enabled"><span id="guest_enabled" class="mod_greetings_errors">������������ ����� ��������� ������ ������������������ ������������</span>'; 
        return;
        }
    //���� ����������� ����������� �� ���������� ������������ � �����,
    //������� ������� ���������� ������������ ������� �������
        if ($cfg_com['amount']!=0 && !$is_admin){
            $user_ip = $inUser->ip;
            $amount_today = $inDB->rows_count('cms_greetings', "DATE(pubdate) BETWEEN DATE(NOW()) AND DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AND ip = '$user_ip'");
            
            if($cfg_com['amount']<=$amount_today){
                echo '<input name="formexist" id="formexist" type="hidden" value="limit_today"><span id="limit" class="mod_greetings_errors">�������� ����� ���������� ������������ �� �������. ���������� �����.</span>';
                return;
            }
        }

     
    // ������ � ������
    ob_start();
    $smarty = $inCore->initSmarty('modules', 'mod_greetings_ajaxform.tpl');
    $smarty->assign('user_id', $user_id);
    $smarty->assign('cfg_com', $cfg_com);
    $smarty->assign('sid', md5(session_id()));
    $smarty->display('mod_greetings_ajaxform.tpl');			
    $html = ob_get_clean();
    echo $html;
    
?>