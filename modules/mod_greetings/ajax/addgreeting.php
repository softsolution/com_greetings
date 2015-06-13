<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }
    header('Content-Type: text/html; charset=UTF-8'); 
    session_start();

    //PROTECT FROM DIRECT RUN
    if (isset($_REQUEST['sid'])){
        if (md5(session_id()) != $_REQUEST['sid']){ die(); }
    } else { 
        die();
    }

    define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

    // Грузим ядро и классы
    include(PATH.'/core/cms.php');
    
    // Грузим конфиг
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
    
    $item['title']         = Utf8Win($item['title']);
    $item['description']   = Utf8Win($item['description']);

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

        echo cp1251_to_utf8($html);
    
        
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

        echo cp1251_to_utf8($html);

    }

function cp1251_to_utf8($html) {
$html = iconv('cp1251', 'utf-8', $html);
return 	$html;}   

function utf8_to_cp1251($utf8) {

    $windows1251 = "";
    $chars = preg_split("//",$utf8);

    for ($i=1; $i<count($chars)-1; $i++) {
        $prefix = ord($chars[$i]);
        $suffix = ord($chars[$i+1]);

        if ($prefix==215) {
            $windows1251 .= chr($suffix+80);
            $i++;
        } elseif ($prefix==214) {
            $windows1251 .= chr($suffix+16);
            $i++;
        } else {
            $windows1251 .= $chars[$i];
        }
    }

    return $windows1251;
}

function Utf8Win($str,$type="w")  {
    static $conv='';

    if (!is_array($conv))  {
        $conv = array();

        for($x=128;$x<=143;$x++)  {
            $conv['u'][]=chr(209).chr($x);
            $conv['w'][]=chr($x+112);

        }

        for($x=144;$x<=191;$x++)  {
            $conv['u'][]=chr(208).chr($x);
            $conv['w'][]=chr($x+48);
        }

        $conv['u'][]=chr(208).chr(129);
        $conv['w'][]=chr(168);
        $conv['u'][]=chr(209).chr(145);
        $conv['w'][]=chr(184);
        $conv['u'][]=chr(208).chr(135);
        $conv['w'][]=chr(175);
        $conv['u'][]=chr(209).chr(151);
        $conv['w'][]=chr(191);
        $conv['u'][]=chr(208).chr(134);
        $conv['w'][]=chr(178);
        $conv['u'][]=chr(209).chr(150);
        $conv['w'][]=chr(179);
        $conv['u'][]=chr(210).chr(144);
        $conv['w'][]=chr(165);
        $conv['u'][]=chr(210).chr(145);
        $conv['w'][]=chr(180);
        $conv['u'][]=chr(208).chr(132);
        $conv['w'][]=chr(170);
        $conv['u'][]=chr(209).chr(148);
        $conv['w'][]=chr(186);
        $conv['u'][]=chr(226).chr(132).chr(150);
        $conv['w'][]=chr(185);
    }

    if ($type == 'w') {
        return str_replace($conv['u'],$conv['w'],$str);
    } elseif ($type == 'u') {
        return str_replace($conv['w'], $conv['u'],$str);
    } else {
        return $str;
    }
}
?>
