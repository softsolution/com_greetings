<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

	define('PATH', $_SERVER['DOCUMENT_ROOT']);
	include(PATH.'/core/ajax/ajax_core.php');

    if (!isset($_REQUEST['module_id'])) { die(2); }
    if (!isset($_REQUEST['page'])) { die(4); }

    // Грузим шаблонизатор
    $smarty = $inCore->initSmarty();

    // Входные данные
    $page		= $inCore->request('page', 'int', 1);	
    $module_id	= $inCore->request('module_id', 'int', '');

    // Грузим конфиг модуля
    $cfg = $inCore->loadModuleConfig($module_id);

    if (!isset($cfg['greetingscount'])) { $cfg['greetingscount']= 5; }
    if (!isset($cfg['showimages'])) { $cfg['showimages']= 1; }
    if (!isset($cfg['imagewidth'])) { $cfg['imagewidth']= 90; }
    if (!isset($cfg['addgreetings'])) { $cfg['addgreetings']= 1; }
    if (!isset($cfg['showlink'])) { $cfg['showlink']= 0; }
    if (!isset($cfg['maxlen'])) { $cfg['maxlen']= 100; }
    if (!isset($cfg['is_pag'])) { $cfg['is_pag']= 0; }
    
    $is_greetings = false;

    $perpage = $cfg['greetingscount'];

	$sql = "SELECT g.*, u.nickname as author, u.login 
		FROM cms_greetings g 
		LEFT JOIN cms_users u ON u.id = g.user_id 
                WHERE g.published = 1 ORDER BY g.pubdate DESC LIMIT ".(($page-1)*$perpage).", $perpage";

    $result = $inDB->query($sql);
        
    $sql_total = "SELECT 1 FROM cms_greetings WHERE published = 1";
    $result_total = $inDB->query($sql_total) ;
    $total_page   = $inDB->num_rows($result_total);

    if ($total_page){

    $is_greetings = true;

        $greetings = array();	

        while($greeting = $inDB->fetch_assoc($result)){

            $greeting['pubdate']     = $inCore->dateFormat($greeting['pubdate'], true, false);

            $greeting['description'] = nl2br($greeting['description']);
            if (strlen($greeting['description'])>$cfg['maxlen']) {
                $greeting['description'] = substr($greeting['description'], 0, $cfg['maxlen']). '...'; 
                }

            $greeting['file'] = existImage($greeting['file']);
            $greetings[] = $greeting;
        }
    }
    
    // Отдаем в шаблон
    ob_start();
    $smarty = $inCore->initSmarty('modules', 'mod_greetings.tpl');			
    $smarty->assign('greetings', $greetings);
    $smarty->assign('is_ajax', true);
    $smarty->assign('is_greetings', $is_greetings);
    $smarty->assign('module_id', $module_id);
    $smarty->assign('pagebar_module', cmsPage::getPagebar($total_page, $page, $perpage, 'javascript:greetingPage(%page%, '.$module_id.')'));
    $smarty->assign('cfg', $cfg);
    $smarty->display('mod_greetings.tpl');			
    $html = ob_get_clean();
    echo $html;
    
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
?>
