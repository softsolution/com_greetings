<?php
/* ****************************************************************************************** */
/* created by soft-solution.ru                                                                */
/* frontend.php of component greetings for InstantCMS 1.10.2                                  */
/* ****************************************************************************************** */
if (!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function greetings() {
    
    global $_LANG;
    
    $inCore = cmsCore::getInstance();
    $inConf     = cmsConfig::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

    $cfg = $inCore->loadComponentConfig('greetings');

    $inCore->loadModel('greetings');
    $model = new cms_model_greetings();
    
    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }
    
    define('HOST', 'http://' . $inCore->getHost());
    
    $inCore->loadLanguage('components/greetings');

    $user_id   = $inUser->id;
    $is_admin  = $inCore->userIsAdmin($inUser->id);
 
    $do        = cmsCore::request('do', 'str', 'view');
    $page      = cmsCore::request('page', 'int', 1);
    
    $perpage   = $cfg['perpage'] ? $cfg['perpage'] : 15;
    $cfg['amount'] = $cfg['amount'] ? $cfg['amount']  : 0;
    
    $pagetitle = $inCore->menuTitle();
    $pagetitle = $pagetitle ? $pagetitle : $_LANG['GREETINGS'];

    $inPage->setTitle($pagetitle);
    $inPage->addPathway($pagetitle, '/greetings');
    $inPage->addHeadJS('components/greetings/js/greetings.js');
    $inPage->addHeadCSS('templates/'.$inConf->template.'/css/greetings.css');

/* ==================================================================================================== */
/* ========================== ЛЕНТА ПОЗДРАВЛЕНИЙ ====================================================== */
/* ==================================================================================================== */

    if ($do == 'view') {
        
        $is_moder = $inUser->is_admin;
        $total = $model->getItemsCount($is_moder);

        if (!$orderby) { $orderby = 'pubdate'; }
        if (!$orderto) { $orderto = 'DESC'; }
        $inDB->orderBy($orderby, $orderto);
        $inDB->limitPage($page, $perpage);
        
        $items = $model->getItems($is_moder);
        if(!$items && $page > 1){ cmsCore::error404(); }

        $pagebar = cmsPage::getPagebar($total, $page, $perpage, '/greetings/page-%page%');

        $smarty = $inCore->initSmarty('components', 'com_greetings_view.tpl');
        $smarty->assign('is_admin', $is_admin);
        $smarty->assign('user_id', $user_id);
        $smarty->assign('cfg', $cfg);
        $smarty->assign('items', $items);
        $smarty->assign('pagebar', $pagebar);
        $smarty->display('com_greetings_view.tpl');
    }

/* ==================================================================================================== */
/* ========================== ДОБАВЛЯЕМ ПОЗДРАВЛЕНИЕ ================================================== */
/* ==================================================================================================== */
    
    if ($do == 'add') {

        if (!$inUser->id && !$cfg['guest_enabled']){ cmsUser::goToLogin(); }
        
        //если установлено ограничение по добавлению поздравлений
        //от одного пользователя 
        //считаем сколько объявлений пользователь добавил сегодня
        if ($cfg['amount']!=0 && !$is_admin){
            $user_ip = $inUser->ip;
            $amount_today = $inDB->rows_count('cms_greetings', "DATE(pubdate) BETWEEN DATE(NOW()) AND DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AND ip = '$user_ip'");
            
            if($cfg['amount']<=$amount_today){
                cmsCore::addSessionMessage($_LANG['NO_ADD_TODAY'], 'info');
                $inCore->redirect('/greetings');
            }
        }
        
        if ( !$inCore->inRequest('submit') ) {
            
            $inPage->setTitle($_LANG['ADD_GREETINGS']);
            $inPage->addPathway($_LANG['ADD_GREETINGS']);

            if (IS_BILLING && $inUser->id){ cmsBilling::checkBalance('greetings', 'add_greetings'); }
            $inPage->setTitle($_LANG['ADD_ADV']);

            $item = cmsUser::sessionGet('greetings');
            if ($item) { cmsUser::sessionDel('greetings'); }
            
            if($cfg['img_collection']){
                $collection_list = $model->CollectionList($cfg['thumb1']);
            }

            $smarty = $inCore->initSmarty('components', 'com_greetings_add.tpl');
            $smarty->assign('do', $do);
            $smarty->assign('pagetitle', $_LANG['ADD_GREETINGS']);
            $smarty->assign('user_id', $user_id);
            $smarty->assign('is_admin', $is_admin);
            $smarty->assign('item', $item);
            $smarty->assign('collection_list', $collection_list);
            $smarty->assign('cfg', $cfg);
            $smarty->display('com_greetings_add.tpl');

        }
        
        if ( $inCore->inRequest('submit') ) {

            $title_fake = cmsCore::request('title_fake', 'str', '');
            if ($title_fake) { cmsCore::error404(); }
            
            if(!cmsCore::validateForm()) { cmsCore::error404(); }

            $errors = false;

            $item = array();
            $item['title']         = cmsCore::request('title', 'str');
            $item['description']   = cmsCore::request('description', 'str');
            $item['file']          = cmsCore::request('file', 'str');

            if ($user_id){
                $item['published'] = $cfg['moderation'] && !$is_admin ? 0 : 1;
            } else {
                $item['published'] = $cfg['guest_publish'] ? 1 : 0;
            }
            
            if (!$inUser->id && !$inCore->checkCaptchaCode(cmsCore::request('code', 'str'))) {
                cmsCore::addSessionMessage($_LANG['ERR_CAPTCHA'], 'error'); $errors = true;
            }
        
            if (!$item['description']) {
                cmsCore::addSessionMessage($_LANG['DESC_NOT_EMPTY'], 'error'); $errors = true;
            }

            if ($errors){
                $item['description'] = htmlspecialchars(stripslashes($_REQUEST['description']));
                $item['title']       = stripslashes($item['title']);
                cmsUser::sessionPut('greetings', $item);
                $inCore->redirect('/greetings/add.html');
            }
            
            if (isset($_FILES["imgfile"]["name"]) && @$_FILES["imgfile"]["name"]!=''){
                
                $file         = $model->uploadPhoto();
                $item['file'] = $file['filename'];

            } else {
                if($item['file']=='')  { $item['file']='default.jpg'; }
            }
            
            $item['user_id'] = $user_id;
            $item['ip']      = $inUser->ip;
            
            $item_id = $model->addGreeting($item);
            
            if (IS_BILLING && $user_id) {
                cmsBilling::process('greetings', 'add_greetings');
            }
            
            cmsUser::clearCsrfToken();
            
            if ($item['published']) {
                cmsCore::addSessionMessage($_LANG['ADD_GREETINGS_SUCCESS'], 'success');
                $inCore->redirect('/greetings/read'.$item_id.'.html');
            }

            if (!$item['published']) {

                $link = '<a href="'.HOST.'/greetings/read'.$item_id.'.html">'.$item['title'].'</a>';
                
                if($inUser->id){
                    $user = '<a href="'.HOST.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a>';
                } else {
                    $user = $_LANG['GUEST'].', ip: '.$inUser->ip;
                }
                
                $message = str_replace('%user%', $user, $_LANG['MSG_GR_SUBMIT']);
                $message = str_replace('%link%', $link, $message);
                cmsUser::sendMessage(USER_UPDATER, 1, $message);
                
                $admin_email = $inDB->get_field('cms_users', 'id=1', 'email');
                if($admin_email){
                    $inCore->mailText($admin_email, $_LANG['MSG_NEW_NEEDMODER'].' - '.$inConf->sitename, $message);
                }

                cmsCore::addSessionMessage($_LANG['GR_IS_ADDED'].'<br>'.$_LANG['GR_PREMODER_TEXT'], 'success');
                $inCore->redirect('/greetings');
            }

        }
    }
    
/* ==================================================================================================== */
/* ========================== РЕДАКТИРУЕМ ПОЗДРАВЛЕНИЕ ================================================ */
/* ==================================================================================================== */
    
    if ($do == 'edit') {

        if (!$inUser->id) { cmsUser::goToLogin(); }
        
        $id   = cmsCore::request('id', 'int', 0);
        $greeting = $model->getGreeting($id);

        if (!$greeting) { cmsCore::error404(); }
        
        $is_admin  = $inUser->is_admin;
	$is_author = ($inUser->id == $greeting['user_id']);
        
        if (!$is_admin && !$is_author) {
            cmsCore::addSessionMessage($_LANG['YOU_HAVENT_ACCESS'], 'error');
            $inCore->redirect('/greetings');
        }
        
        if ( !$inCore->inRequest('submit') ) {
            
            $inPage->setTitle($_LANG['EDIT_GREETINGS']);
            $inPage->addPathway($_LANG['EDIT_GREETINGS']);

            $item = cmsUser::sessionGet('greetings');
            if ($item) { cmsUser::sessionDel('greetings'); }
            
            $item = $item ? $item : $greeting;
            
            if($cfg['img_collection']){
                $collection_list = $model->CollectionList($cfg['thumb1']);
            }

            $smarty = $inCore->initSmarty('components', 'com_greetings_add.tpl');
            $smarty->assign('do', $do);
            $smarty->assign('pagetitle', $_LANG['EDIT_GREETINGS']);
            $smarty->assign('user_id', $user_id);
            $smarty->assign('is_admin', $is_admin);
            $smarty->assign('item', $item);
            $smarty->assign('collection_list', $collection_list);
            $smarty->assign('cfg', $cfg);
            $smarty->display('com_greetings_add.tpl');

        }
        
        if ( $inCore->inRequest('submit') ) {

            $title_fake = cmsCore::request('title_fake', 'str', '');
            if ($title_fake) { cmsCore::error404(); }
            
            if(!cmsCore::validateForm()) { cmsCore::error404(); }

            $errors = false;

            $item = array();
            $item['title']         = cmsCore::request('title', 'str');
            $item['description']   = cmsCore::request('description', 'str');
            $item['file']          = cmsCore::request('file', 'str');

            if (!$item['description']) {
                cmsCore::addSessionMessage($_LANG['DESC_NOT_EMPTY'], 'error'); $errors = true;
            }

            if ($errors){
                $item['description'] = htmlspecialchars(stripslashes($_REQUEST['description']));
                $item['title']   = stripslashes($item['title']);
                cmsUser::sessionPut('greetings', $item);
                $inCore->redirect('/greetings/edit'.$item['id'].'.html');
            }
            
            if (isset($_FILES["imgfile"]["name"]) && @$_FILES["imgfile"]["name"]!=''){
                
                $file = $model->uploadPhoto();
                $item['file'] = $file['filename'];

            } else {
                if($item['file']=='') { $item['file']='default.jpg'; }
            }
            
            if (!$is_admin && $cfg['moderation']){
                $item['published'] = 0;
            } else {
                $item['published'] = $greeting['published'];
            }
            
            $model->updateGreeting($item, $id);
            cmsUser::sessionClearAll();
            cmsUser::clearCsrfToken();

            if ($item['file'] != $greeting['file']) {
                if (!preg_match('/^greeting([a-z0-9]+)\.(jpg|png|gif)$/', $greeting['file']) && $greeting['file']!='default.jpg') {
                    @unlink(PATH.'/upload/greetings/small/'.$greeting['file']);
                    @unlink(PATH.'/upload/greetings/medium/'.$greeting['file']);
                }
            }
            
            if (!$item['published'] && !$is_admin) {

                $link = '<a href="'.HOST.'/greetings/read'.$id.'.html">'.$item['title'].'</a>';
                $user = '<a href="'.HOST.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a>';

                $message = str_replace('%user%', $user, $_LANG['MSG_GR_EDITED']);
                $message = str_replace('%link%', $link, $message);
                cmsUser::sendMessage(USER_UPDATER, 1, $message);

                cmsCore::addSessionMessage($_LANG['GR_EDIT_PREMODER_TEXT'], 'info');
                
                $admin_email = $inDB->get_field('cms_users', 'id=1', 'email');
                if($admin_email){
                    $inCore->mailText($admin_email, $_LANG['MSG_NEW_NEEDMODER'].' - '.$inConf->sitename, $message);
                }
            }
            
            cmsCore::addSessionMessage($_LANG['EDIT_GREETINGS_SUCCESS'], 'success');
            $inCore->redirect('/greetings/read'.$id.'.html');

        }
   
    }

/* ==================================================================================================== */
/* ========================== УДАЛЯЕМ ПОЗДРАВЛЕНИЕ ==================================================== */
/* ==================================================================================================== */
    
    if ($do == 'delete') {

	if(!cmsCore::validateForm()) { cmsCore::halt(); }
	if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { cmsCore::halt(); }
        
        if (!$inUser->id) { cmsCore::halt(); }
        
        $id   = cmsCore::request('id', 'int', 0);
        $item = $inDB->get_fields('cms_greetings', "id='$id'", "*");
        
        if (!$item){ cmsCore::halt(); }
        
        $is_admin  = $inUser->is_admin;
	$is_author = ($inUser->id == $item['user_id']);
        
        if (!$is_admin && !$is_author) { cmsCore::halt(); }
            
        $model->deleteGreeting($id);
        
        if ($inUser->id != $item['user_id'] && $item['user_id']>0){
            cmsUser::sendMessage(USER_UPDATER, $item['user_id'], $_LANG['YOUR_GREETING'].' <b>&laquo;'.mb_substr($item['description'], 0, 15).'...&raquo;</b> '.$_LANG['WAS_DELETED']);
            if($item['user_id'] && $item['user_id']>1){
            
            }
            $user_email = $inDB->get_field('cms_users', 'id='.$item['user_id'], 'email');
            if($user_email){
                $inCore->mailText($user_email, $_LANG['MSG_NEW_NEEDMODER'].' - '.$inConf->sitename, $message);
            }
        }

        cmsCore::addSessionMessage($_LANG['GREETING_DELETE'], 'success');
        cmsUser::clearCsrfToken();
        cmsCore::jsonOutput(array('error' => false, 'redirect'  => '/greetings'));
    }
    
/* ==================================================================================================== */
/* ========================== ПУБЛИКАЦИЯ ПОЗДРАВЛЕНИЯ ================================================= */
/* ==================================================================================================== */
   
    if ($do == 'publish'){
        
        $id        = cmsCore::request('id', 'int', 0);
	$item     = $model->getGreeting($id);
        if (!$item){ cmsCore::error404(); }

	// если уже опубликовано, 404
	if ($item['published']) { cmsCore::error404(); }

	// публиковать могут админы и модераторы доски
	if(!$inUser->is_admin) { cmsCore::error404(); }

	// публикуем
        $inDB->setFlag('cms_greetings', $id, 'published', 1);

 	if($item['user_id']){
            $link = '<a href="/greetings/read'.$item['id'].'.html">'.$item['title'].'</a>';
            $message = str_replace('%link%', $link, $_LANG['MSG_ADV_ACCEPTED']);
            cmsUser::sendMessage(USER_UPDATER, $item['user_id'], $message);
	}

	cmsCore::addSessionMessage($_LANG['GR_IS_ACCEPTED'], 'success');
        $inCore->redirect('/greetings/read'.$item['id'].'.html');

    }
    
/* ==================================================================================================== */
/* ========================== ПРОСМОТР ПОЗДРАВЛЕНИЯ =================================================== */
/* ==================================================================================================== */
    if($do=='read'){

	$id        = cmsCore::request('id', 'int', 0);
	$item      = $model->getGreeting($id);
	if (!$item){ cmsCore::error404(); }

	if (!$item['published'] && !$is_admin && !$item['user_id']!=$user_id) { cmsCore::error404(); }

	// для неопубликованного показываем инфо: на модерации
	if (!$item['published']) {
            cmsCore::addSessionMessage($_LANG['GR_IS_MODER'], 'info');
	}

	$item['description'] = nl2br($item['description']);

	$inPage->addPathway($item['title']);
	$inPage->setTitle($item['title']);
	$inPage->setDescription($item['title']);
        
        $is_admin  = $inUser->is_admin;
	$is_author = ($user_id == $item['user_id']);
        
        if ($is_admin || $is_author) { $item['moderator'] = 1;}

	$smarty = $inCore->initSmarty('components', 'com_greetings_item.tpl');
	$smarty->assign('item', $item);
	$smarty->assign('cfg', $cfg);
	$smarty->assign('user_id', $user_id);
	$smarty->assign('is_admin', $is_admin);
	$smarty->display('com_greetings_item.tpl');

    }
    
}

?>