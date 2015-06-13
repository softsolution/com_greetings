<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

	define('PATH', $_SERVER['DOCUMENT_ROOT']);
	include(PATH.'/core/ajax/ajax_core.php');

    if (!isset($_REQUEST['module_id'])) { die(2); }

    $is_admin  = $inCore->userIsAdmin($inUser->id);
    $user_id   = $inUser->id;
    
    // Грузим шаблонизатор
    $smarty = $inCore->initSmarty();

    // Входные данные
    $module_id	= $inCore->request('module_id', 'int', '');
    
    //Загружаем конфигурацию компонента
    $cfg_com = $inCore->loadComponentConfig('greetings');
    
    if(!$cfg_com['guest_enabled'] && !$user_id){
        echo '<input name="formexist" id="formexist" type="hidden" value="guest_enabled"><span id="guest_enabled" class="mod_greetings_errors">Поздравления могут добавлять только зарегистрированные пользователи</span>'; 
        return;
        }
    //если установлено ограничение на количество поздравлений в сутки,
    //считаем сколько объявлений пользователь добавил сегодня
        if ($cfg_com['amount']!=0 && !$is_admin){
            $user_ip = $inUser->ip;
            $amount_today = $inDB->rows_count('cms_greetings', "DATE(pubdate) BETWEEN DATE(NOW()) AND DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AND ip = '$user_ip'");
            
            if($cfg_com['amount']<=$amount_today){
                echo '<input name="formexist" id="formexist" type="hidden" value="limit_today"><span id="limit" class="mod_greetings_errors">Исчерпан лимит добавления поздравлений на сегодня. Попробуйте позже.</span>';
                return;
            }
        }

     
    // Отдаем в шаблон
    ob_start();
    $smarty = $inCore->initSmarty('modules', 'mod_greetings_ajaxform.tpl');
    $smarty->assign('user_id', $user_id);
    $smarty->assign('cfg_com', $cfg_com);
    $smarty->assign('sid', md5(session_id()));
    $smarty->display('mod_greetings_ajaxform.tpl');			
    $html = ob_get_clean();
    echo $html;
    
?>