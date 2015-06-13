<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
if (!defined('VALID_CMS')) {
    die('ACCESS DENIED');
}

function greetings() {
    global $_LANG;
    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

    $cfg = $inCore->loadComponentConfig('greetings');

    // Проверяем включен ли компонент
    if (!$cfg['component_enabled']) {
        cmsCore::error404();
    }
    
    $inCore->loadModel('greetings');
    $model = new cms_model_greetings();
    
    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }
    
    $inCore->loadLanguage('components/greetings');

    $user_id   = $inUser->id;

    $is_admin  = $inCore->userIsAdmin($inUser->id);
 
    $id        = $inCore->request('id', 'int', 0);
    $do        = $inCore->request('do', 'str', 'view');
    $target    = $inCore->request('target', 'str', 'all');
    $page      = $inCore->request('page', 'int', 1);
    $perpage   = $cfg['perpage'] ? $cfg['perpage'] : 15;
    $cfg['amount'] = $cfg['amount'] ? $cfg['amount']  : 0;
    $cfg['img_width'] = $cfg['img_width'] ? $cfg['img_width']  : 150;

/* ==================================================================================================== */
/* ========================== ЛЕНТА ПОЗДРАВЛЕНИЙ ====================================================== */
/* ==================================================================================================== */

    if ($do == 'view') {

        $sql = "SELECT g.*,
                    u.nickname as author, u.login
		FROM cms_greetings g
		LEFT JOIN cms_users u ON u.id = g.user_id
                WHERE g.published = 1";
        
        if($target=='my' && $user_id) {
            $sql .=" AND g.user_id = $user_id";
        }
        
        $sql .= " ORDER BY g.pubdate DESC 
                LIMIT ".($page - 1)*$perpage.", " .$perpage;

        $result = $inDB->query($sql);

        //для корректной пагинации считаем количество отдельно
        $sql2 = "SELECT 1 FROM cms_greetings g";
        if ($target=='my') { $where .= " AND g.user_id = $user_id"; } 
        if (!$is_admin)    { $where .= " AND g.published = 1"; }
        if ($where) $sql2 .= ' WHERE g.id > 0 '.$where;

        $result_total = $inDB->query($sql2);
        $records = $inDB->num_rows($result_total);


        if ($inDB->num_rows($result)) {
            $greetings = array();
            while ($greeting = $inDB->fetch_assoc($result)) {
                $greeting['pubdate']     = $inCore->dateFormat($greeting['pubdate'], true, false);//дата
                $greeting['author']      = cmsUser::getProfileLink($greeting['login'], $greeting['author']);
                $greeting['description'] = nl2br($greeting['description']);
                $greeting['file'] = existImage($greeting['file']);
                $greetings[] = $greeting;
            }
            $is_greeting = true;
        } else {
            $is_greeting = false;
        }

        if($target=='my') {
            $filter_target = $target.'/';
        } else {
            $filter_target = '';
        }
        
        $pagebar = cmsPage::getPagebar($records, $page, $perpage, '/greetings/'.$filter_target.'page-%page%');

        $smarty = $inCore->initSmarty('components', 'com_greetings_view.tpl');
        
        $smarty->assign('is_admin', $is_admin);
        $smarty->assign('user_id', $user_id);
        $smarty->assign('target', $target);
        $smarty->assign('cfg', $cfg);
        $smarty->assign('greetings', $greetings);
        $smarty->assign('pagebar', $pagebar);
        $smarty->assign('is_greeting', $is_greeting);
        $smarty->display('com_greetings_view.tpl');
    }

/* ==================================================================================================== */
/* ========================== ПРОСМОТР ПОЗДРАВЛЕНИЯ =================================================== */
/* ==================================================================================================== */
//ПОКА НЕ ИСПОЛЬЗУЕТСЯ
//    if ($do == 'read') {
//    }
    
/* ==================================================================================================== */
/* ========================== ДОБАВЛЯЕМ ПОЗДРАВЛЕНИЕ ================================================== */
/* ==================================================================================================== */
    
    if ($do == 'add') {

        //если не авторизован, перебрасываем на ссылку для авторизации
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
        
        $inPage->setTitle($_LANG['ADD_GREETINGS']);
	$inPage->addPathway($_LANG['ADD_GREETINGS']);
	
	$inPage->backButton(false);
	$inPage->addHeadJS('components/greetings/js/greetings.js');

        $error = '';
        $captha_code           = $inCore->request('code', 'str', '');
        $published             = ($inUser->is_admin || $cfg['guest_publish']) ? 1 : 0;
        $is_submit             = $inCore->inRequest('description');
        
        $item['title']         = $inCore->request('title', 'str', '');
        $item['description']   = $inCore->request('description', 'str', '');
        $item['file']          = $inCore->request('file', 'str', '');

        if ($is_submit && !$inUser->id && !$inCore->checkCaptchaCode($inCore->request('code', 'str'))) { $error .= $_LANG['ERR_CAPTCHA']; }
        
        //изображение пользователя
        if ($inCore->inRequest('upload') && isset($_FILES["picture"]["name"]) && @$_FILES["picture"]["name"]!='' && $cfg['user_image'] && ($user_id || (!$user_id && $cfg['guest_image']))) {

                $inCore->includeGraphics();
                $uploaddir = PATH . '/upload/greetings/';

                $realfile = $_FILES['picture']['name'];
                $path_parts = pathinfo($realfile);
                $ext = strtolower($path_parts['extension']);

                $realfile = md5($realfile . '-' . time()) . '.' . $ext;

                if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png') {

                    $filename = md5($realfile . '-' . $userid . '-' . time()) . '.jpg';
                    $uploadfile = $uploaddir . $realfile;
                    $uploadimage = $uploaddir . $filename;

                    $source = $_FILES['picture']['tmp_name'];
                    $errorCode = $_FILES['picture']['error'];
                } else {
                    $error .= $_LANG['ERROR_TYPE_FILE']." jpg, jpeg, gif, bmp, png";
                }
            }
        
        if($uploadimage){
            $item['file'] = '/upload/greetings/'.$filename;
        }
        
        if (!$is_submit || $error) {
            
            if (IS_BILLING && $inUser->id){ cmsBilling::checkBalance('greetings', 'add_greetings'); }
            
            if(!$item){
                $item = cmsUser::sessionGet('greetings');
                if ($item) { cmsUser::sessionDel('greetings'); }

                $validation = cmsUser::sessionGet('valid_greetings');
                if ($validation) { cmsUser::sessionDel('valid_greetings'); }
            }
            
            //если значение $item пустое вытаскиваем данные из профиля
            if(!$item && $user_id!='') {
               $item['title'] = $inDB->get_field('cms_users', "id='{$user_id}'", 'nickname');
               $item['file']  = "/upload/greetings/collection/default.jpg";
            }

            //картинка из коллекции сайта
            if($cfg['img_collection']){
                $collection_list = $model->CollectionList($cfg['img_width']);
            }
            
            $smarty = $inCore->initSmarty('components', 'com_greetings_add.tpl');
            $smarty->assign('do', $do);
            $smarty->assign('user_id', $user_id);
            $smarty->assign('item', $item);
            $smarty->assign('validation', $validation);
            $smarty->assign('error', $error);
            $smarty->assign('collection_list', $collection_list);
            $smarty->assign('cfg', $cfg);
            $smarty->display('com_greetings_add.tpl');

        } else {
            
            //проверяем данные
            $errors = false;
	    $validation = array();

            if(!$item['description']) {$validation['description']=1; $errors = true;}
            
            if ($errors) {

                //экранируем символы
                $item['description']   = htmlspecialchars(stripslashes($_REQUEST['description']));
                $item['title']         = stripslashes($item['title']);

                cmsUser::sessionPut('greetings', $item);
                cmsUser::sessionPut('valid_greetings', $validation);
                $inCore->redirect('/greetings/add.html');
                
            }
            
            if (!$errors) {
                
                //дополнительная обработка полей
                $item['user_id'] = $user_id;
                $item['ip']      = $inUser->ip;
                $item['published']     = $published;
                
                if($item['file']=='') { $item['file']='/upload/greetings/collection/default.jpg'; }
                
                if ($inCore->moveUploadedFile($source, $uploadfile, $errorCode)) {
                //CREATE THUMBNAIL
                if (isset($cfg['img_width'])) { $img_width = $cfg['img_width']; } else { $img_width = 150; }

                //resize image
                @img_resize($uploadfile, $uploadimage, $img_width, $img_width, $cfg['thumbsqr']);

                //DELETE ORIGINAL							
                @unlink($uploadfile);
                }

                //добавляем поздравление
                $greeting_id = $model->addGreeting($item);
                
                if (IS_BILLING && $inUser->id){ cmsBilling::process('greetings', 'add_greetings'); }
                
                if($cfg['guest_publish']){
                    $msg = $_LANG['ADD_GREETINGS_SUCCESS'];
                } else {
                    $msg = $_LANG['ADD_GREETINGS_NOPUB'];
                }
                cmsCore::addSessionMessage($msg, 'success');
                $inCore->redirect('/greetings');
            }
        }
    }
    
/* ==================================================================================================== */
/* ========================== РЕДАКТИРУЕМ ПОЗДРАВЛЕНИЕ ================================================ */
/* ==================================================================================================== */
    
if ($do == 'edit') {

        //если не авторизован, перебрасываем на ссылку для авторизации
        if (!$inUser->id) {
            cmsUser::goToLogin();
        }

        $greeting = $model->getGreeting($id);

        if (!$greeting['id']) {
            cmsCore::error404();
        }

        //если хозяин или админ то разрешаем редактирование
        if ($is_admin || $inUser->id == $greeting['user_id']) {

            $error = '';
            $is_submit = $inCore->inRequest('description');
            
            $item['title']         = $inCore->request('title', 'str', '');
            $item['description']   = $inCore->request('description', 'str', '');
            $item['file']          = $inCore->request('file', 'str', '');
            
            //изображение пользователя
            if ($inCore->inRequest('upload') && isset($_FILES["picture"]["name"]) && @$_FILES["picture"]["name"]!='') {

                    $inCore->includeGraphics();
                    $uploaddir = PATH . '/upload/greetings/';

                    $realfile = $_FILES['picture']['name'];
                    $path_parts = pathinfo($realfile);
                    $ext = strtolower($path_parts['extension']);

                    $realfile = md5($realfile . '-' . time()) . '.' . $ext;

                    if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png') {

                        $filename = md5($realfile . '-' . $userid . '-' . time()) . '.jpg';
                        $uploadfile = $uploaddir . $realfile;
                        $uploadimage = $uploaddir . $filename;

                        $source = $_FILES['picture']['tmp_name'];
                        $errorCode = $_FILES['picture']['error'];
                    } else {
                        $error .= $_LANG['ERROR_TYPE_FILE']." jpg, jpeg, gif, bmp, png";
                    }
                }

            if($uploadimage){
                $item['file'] = '/upload/greetings/'.$filename;
            }
            
            if (!$is_submit || $error) {
                
                $inPage->setTitle($_LANG['EDIT_GREETINGS']);
                $inPage->addPathway($_LANG['EDIT_GREETINGS']);

                $inPage->backButton(false);
                $inPage->addHeadJS('components/greetings/js/greetings.js');
            
                if (!$item) {
                 
                    $item = cmsUser::sessionGet('greetings');
                    if ($item) { cmsUser::sessionDel('greetings'); }

                    $validation = cmsUser::sessionGet('valid_greetings');
                    if ($validation) { cmsUser::sessionDel('valid_greetings'); }
                }

                //если значение $item пустое получаем данные
                if ($item!='') { $item = $greeting; }

                //картинка из коллекции сайта
                if ($cfg['img_collection']) {
                    $collection_list = $model->CollectionList($cfg['img_width']);
                }

                $smarty = $inCore->initSmarty('components', 'com_greetings_add.tpl');
                $smarty->assign('do', $do);
                $smarty->assign('user_id', $user_id);
                $smarty->assign('item', $item);
                $smarty->assign('validation', $validation);
                $smarty->assign('error', $error);
                $smarty->assign('collection_list', $collection_list);
                $smarty->assign('cfg', $cfg);
                $smarty->display('com_greetings_add.tpl');
                
            } else {
                
                //проверяем данные
                $errors = false;
                $validation = array();

                if(!$item['description']) {$validation['description']=1; $errors = true;}

                if ($errors) {

                    //экранируем символы
                    $item['description']   = htmlspecialchars(stripslashes($_REQUEST['description']));
                    $item['title']         = stripslashes($item['title']);

                    cmsUser::sessionPut('greetings', $item);
                    cmsUser::sessionPut('valid_greetings', $validation);
                    $inCore->redirect('/greetings/edit'.$id.'.html');

                }
                
                if (!$errors) {
                    
                    if($item['file']=='') { $item['file']='/upload/greetings/collection/default.jpg'; }
                    
                    if ($inCore->moveUploadedFile($source, $uploadfile, $errorCode)) {
                    //CREATE THUMBNAIL
                    if (isset($cfg['img_width'])) { $img_width = $cfg['img_width']; } else { $img_width = 150; }

                    //resize image
                    @img_resize($uploadfile, $uploadimage, $img_width, $img_width, $cfg['thumbsqr']);

                    //DELETE ORIGINAL							
                    @unlink($uploadfile);
                    
                    }
                    
                    if($item['file']!=$greeting['file']){
                        //удаляем старое изображение, если оно было загружено пользователем
                        if (preg_match('/^(\/upload\/greetings\/)?([\da-z]+)\.(jpg)$/', $greeting['file'])) {
                                @unlink(PATH.$greeting['file']);
                        }
                    }

                    //обновляем поздравление
                    $greeting_id = $model->updateGreeting($item, $id);

                    cmsCore::addSessionMessage($_LANG['EDIT_GREETINGS_SUCCESS'], 'success');
                    $inCore->redirect('/greetings');
                }
            }

        } else {
            AccessDenied();
            return;
        }
    }

/* ==================================================================================================== */
/* ========================== УДАЛЯЕМ ПОЗДРАВЛЕНИЕ ==================================================== */
/* ==================================================================================================== */
    
    if ($do == 'delete') {

        //если не авторизован, перебрасываем на ссылку для авторизации
        if (!$inUser->id){ cmsUser::goToLogin(); }

        $greeting = $inDB->get_fields('cms_greetings', "id='$id'", "user_id, file, id");

        if(!$greeting['id']) { cmsCore::error404(); }

        //проверяем имеет ли пользователь право удалить
        if ($is_admin || $inUser->id == $greeting['user_id']) {
            
            $model->deleteGreeting($id);

            cmsCore::addSessionMessage($_LANG['GREETING_DELETE'], 'success');
            $inCore->redirect('/greetings');
            
        } else {
            AccessDenied();
            return;
        }
    }
}

function existImage($imageurl) {
    if (!$imageurl) {
        return '/upload/greetings/collection/default.jpg';
    }
    if ($imageurl && @file_exists(PATH . '' . $imageurl)) {
        return $imageurl;
    } else {
        return '/upload/greetings/collection/default.jpg';
    }
}

function AccessDenied() {
    global $_LANG;
    $inCore = cmsCore::getInstance();
    $smarty = $inCore->initSmarty('components', 'com_error.tpl');
    $smarty->assign('err_title', $_LANG['ACCESS_DENIED']);
    $smarty->assign('err_content', 'Недостаточно прав');
    $smarty->display('com_error.tpl');
    return;
}
?>