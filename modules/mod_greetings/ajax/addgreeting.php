<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

	define('PATH', $_SERVER['DOCUMENT_ROOT']);
	include(PATH.'/core/ajax/ajax_core.php');

    //PROTECT FROM DIRECT RUN
    if (isset($_REQUEST['sid'])){
        if (md5(session_id()) != $_REQUEST['sid']){ die(); }
    } else { 
        die();
    }

    $user_id   = $inUser->id;
    $is_admin  = $inCore->userIsAdmin($inUser->id);
    
    //Загружаем конфигурацию компонента
    $cfg_com = $inCore->loadComponentConfig('greetings');
    
    if(!$cfg_com['guest_enabled'] && !$user_id){
        echo '<input name="formexist" id="formexist" type="hidden" value="guest_enabled"><span id="guest_enabled" class="guest_enabled">Поздравления могут добавлять только зарегистрированные пользователи</span>'; 
        die();
    }
    
    //если установлено ограничение на количество поздравлений в сутки,
    //считаем сколько объявлений пользователь добавил сегодня
    if ($cfg_com['amount']!=0 && !$is_admin){
        $user_ip = $inUser->ip;
        $amount_today = $inDB->rows_count('cms_greetings', "DATE(pubdate) BETWEEN DATE(NOW()) AND DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AND ip = '$user_ip'");

        if($cfg_com['amount']<=$amount_today){
            echo '<input name="formexist" id="formexist" type="hidden" value="limit_today"><span id="limit" class="limit_today">Исчерпан лимит добавления поздравлений на сегодня. Попробуйте позже.</a>';
            die();
        }
    }


    $error = '';
    $captha_code           = $inCore->request('code', 'str', '');
    $published             = ($inUser->is_admin || $cfg['guest_publish']) ? 1 : 0;
    $is_submit             = $inCore->inRequest('description');

    $item['title']         = $inCore->request('title', 'str', '');
    $item['description']   = $inCore->request('description', 'str', '');
    $item['file']          = $inCore->request('imageurl', 'str', '');
    
    if ($captha_code=='' && !$inUser->id){ $error .= 'Вы не указали код с картинки!';}
    if ($is_submit && !$inUser->id && !$inCore->checkCaptchaCode($inCore->request('code', 'str')) && $captha_code!='') { $error .= 'Неправильно указан код с картинки!'; }

    if(!$item['description']) {$error .= 'Поздравление не должно быть пустым<br/>';}

    if($error){
        // Отдаем в шаблон
        ob_start();
        $smarty = $inCore->initSmarty('modules', 'mod_greetings_ajaxform.tpl');
        $smarty->assign('error', $error);
        $smarty->assign('item', $item);
        $smarty->assign('user_id', $inUser->id);
        $smarty->assign('cfg_com', $cfg_com);
        $smarty->assign('sid', md5(session_id()));
        $smarty->display('mod_greetings_ajaxform.tpl');			
        $html = ob_get_clean();

        echo $html;
    
        
    } else {
        
        $inCore->loadModel('greetings');
        $model = new cms_model_greetings();

        if(!$item['file']) { $item['file']='/upload/greetings/collection/default.jpg'; } 
        $item['user_id']   = $user_id;
        $item['ip']        = $inUser->ip;
        $published         = ($inUser->is_admin || $cfg_com['guest_publish']) ? 1 : 0;
        $item['published'] = $published;
                
        $model->addGreeting($item);

        $html = '<input name="status" type="hidden" value="sendok" id="status"><span id="addsuccess" class="mod_greetings_success">Поздравление успешно добавлено</span>';

        echo $html;

    }
?>
