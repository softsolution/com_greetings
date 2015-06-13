<?php
/* ****************************************************************************************** */
/* created by soft-solution.ru                                                                */
/* module.php of component greetings for InstantCMS 1.10.2                                    */
/* ****************************************************************************************** */

function mod_greetings($module_id){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		
	$cfg     = $inCore->loadModuleConfig($module_id);
        $cfg_com = $inCore->loadComponentConfig('greetings');

	if (!isset($cfg['greetingscount'])) { $cfg['greetingscount']= 5; }
        if (!isset($cfg['showimages'])) { $cfg['showimages']= 1; }
        if (!isset($cfg['imagewidth'])) { $cfg['imagewidth']= 90; }
        if (!isset($cfg['addgreetings'])) { $cfg['addgreetings']= 1; }
        if (!isset($cfg['guest_canadd'])) { $cfg['guest_canadd']= 0; }
        if (!isset($cfg['showlink'])) { $cfg['showlink']= 0; }
        if (!isset($cfg['maxlen'])) { $cfg['maxlen']= 100; }
        if (!isset($cfg['is_pag'])) { $cfg['is_pag']= 0; }
        
        $is_greetings = false;
		
	// опции постраничной разбивки
	$page    = 1;
	$perpage = $cfg['greetingscount'];
		
	$sql = "SELECT g.*, u.nickname as author, u.login 
		FROM cms_greetings g 
		LEFT JOIN cms_users u ON u.id = g.user_id 
                WHERE g.published = 1 ORDER BY g.pubdate DESC LIMIT ".$cfg['greetingscount'];

        $result = $inDB->query($sql);
        
        // Считаем общее количество поздравлений если опция пагинация включена
	if ($cfg['is_pag']) {
			
            $sql_total = "SELECT 1 FROM cms_greetings WHERE published = 1";
            $result_total = $inDB->query($sql_total) ;
            $total_page = $inDB->num_rows($result_total);
            
	}
	
	if ($inDB->num_rows($result)){

	$is_greetings = true;

            $greetings = array();					
            while($greeting = $inDB->fetch_assoc($result)){
                
                $greeting['pubdate']     = $inCore->dateFormat($greeting['pubdate'], true, false);
                
                $greeting['description'] = nl2br($greeting['description']);
                if (mb_strlen($greeting['description'])>$cfg['maxlen']) {
                    $greeting['description'] = mb_substr($greeting['description'], 0, $cfg['maxlen']). '...'; 
                 }
                
                $greeting['file'] = existImage($greeting['file']);
                $greetings[] = $greeting;
            }
        }
		
	$smarty = $inCore->initSmarty('modules', 'mod_greetings.tpl');			
	$smarty->assign('greetings', $greetings);
        
	if ($cfg['is_pag']) {
            $smarty->assign('pagebar_module', cmsPage::getPagebar($total_page, $page, $perpage, 'javascript:greetingPage(%page%, '.$module_id.')'));
	}
	
	$smarty->assign('is_greetings', $is_greetings);
	$smarty->assign('module_id', $module_id);
	$smarty->assign('cfg', $cfg);
        $smarty->assign('cfg_com', $cfg_com);
	$smarty->display('mod_greetings.tpl');			
			
	return true;
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

?>