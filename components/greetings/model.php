<?php
/* ****************************************************************************************** */
/* created by soft-solution.ru                                                                */
/* model.php of component greetings for InstantCMS 1.10.2                                     */
/* ****************************************************************************************** */
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_greetings{
    
    public $config    = array();

    public function __construct(){
        $this->inCore = cmsCore::getInstance();
        $this->inDB   = cmsDatabase::getInstance();
        $this->config = $this->inCore->loadComponentConfig('greetings');
    }

    public function getDefaultConfig() {
        
            $cfg = array(
                'perpage' => 15,
                'amount' => 5,
                'guest_enabled' => 1,
                'guest_publish' => 0,
                'moderation' => 0,
                'img_collection' => 1,
                'thumb1' => 150,
                'thumb2' => 400,
                'thumbsqr' => 0,
                'show_userlink' => 0,
                'user_image' => 0,
                'guest_image' => 0,
            );
            
        return $cfg;
    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */

    public function addGreeting($item) {

        $sql = "INSERT INTO cms_greetings (pubdate, user_id, category_id, title, description, file, ip, published) 
		VALUES (NOW(), '{$item['user_id']}', '', '{$item['title']}', '{$item['description']}', '{$item['file']}', '{$item['ip']}', '{$item['published']}')";

        $this->inDB->query($sql);
       
        $greeting_id = $this->inDB->get_last_id('cms_greetings');

        return $greeting_id ? $greeting_id : false;
    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getGreeting($id) {

        $sql = "SELECT * FROM cms_greetings WHERE id = $id LIMIT 1";
        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }
        
        $item = $this->inDB->fetch_assoc($result);
        $item['file']        = $this->existImage($item['file']);
        $item['pubdate']     = cmsCore::dateformat($item['pubdate'], true, false);
        
        return $item;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function updateGreeting($item, $id) {
        
        $this->inDB->update('cms_greetings', $item, $id);
        return true;
        
    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */
    
    public function deleteGreeting($id){
        
        $greeting = $this->getGreeting($id);
        
        //delete image
        if (!preg_match('/^greeting([\da-z]+)\.(jpg|png|gif)$/', $greeting['file']) && $greeting['file']!='default.jpg') {
            @unlink(PATH.'/upload/greetings/small/'.$greeting['file']);
            @unlink(PATH.'/upload/greetings/medium/'.$greeting['file']);
        }

        $this->inDB->query("DELETE FROM cms_greetings WHERE id='$id'");

        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteGreetings($id_list){
        foreach($id_list as $key=>$id){
            $this->deleteGreeting($id);
        }
        return true;
    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */

    public function CollectionList($img_width) {
        if ($handle = opendir($_SERVER['DOCUMENT_ROOT'] . '/upload/greetings/small/')) {
            $n = 0;
            $m = 0;
            $html = '<div id="collist">';
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..' && (mb_strstr($file, '.png') || mb_strstr($file, '.jpg') || mb_strstr($file, '.gif'))) {
                    
                    $dir = '/upload/greetings/small/';
                    if($m == 0){
                        $html .='<a href="javascript:void(0)" rel="default.jpg"><img width="'.$img_width.'" src="'.$dir.'default.jpg" border="0" href="javascript:void(0);" /></a>';
                        $m++;
                    }
                    
                    if (preg_match('/^greeting([\da-z]+)\.(jpg|png|gif)$/', $file)) {
                        $html .='<a href="javascript:void(0)" rel="'.$file.'"><img width="'.$img_width.'" src="'.$dir.$file.'" border="0" href="javascript:void(0);" /></a>';
                        $n++;
                    }
                }
            }
            $html .= '</div>';
            closedir($handle);
        }

        if (!$n) {
            $html = '<p>Нет файлов в коллекции!</p>';
        }
        

        $html .='<div align="right" style="clear:both">[<a href="javascript:void(0)" onclick="greetings.selectDefault();return false;">По умолчанию</a>] [<a href="javascript:void(0)" onclick="greetings.hideCollection();return false;">Закрыть</a>]</div>';

        return $html;
    }



/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getItems($show_all = false){

        $pub_where = ($show_all ? '1=1' : 'g.published = 1');

        $sql = "SELECT g.*, u.nickname as author, u.login FROM cms_greetings g LEFT JOIN cms_users u ON g.user_id = u.id WHERE {$pub_where} 
                {$this->inDB->where} 
                {$this->inDB->group_by}
                {$this->inDB->order_by}\n";

        if ($this->inDB->limit){
            $sql .= "LIMIT {$this->inDB->limit}";
        }

        $result = $this->inDB->query($sql);
        if(!$this->inDB->num_rows($result)){ return false; }
        
        $this->inDB->resetConditions();
        
        $items = array();
        
        while ($item = $this->inDB->fetch_assoc($result)) {
            $item['pubdate']     = cmsCore::dateformat($item['pubdate'], true, false);
            $item['author']      = cmsUser::getProfileLink($item['login'], $item['author']);
            $item['description'] = nl2br($item['description']);
            $item['file']        = $this->existImage($item['file']);
            $items[] = $item;
        }

        return $items;

    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getItemsCount($show_all = false){

        $pub_where = ($show_all ? '1=1' : 'g.published = 1');
        $sql = "SELECT 1 FROM cms_greetings g WHERE {$pub_where} {$this->inDB->where} {$this->inDB->group_by}";
        $result = $this->inDB->query($sql);

        return $this->inDB->num_rows($result);

    }
    
    public function existImage($imgfile) {
        
        if ($imgfile && @file_exists(PATH .'/upload/greetings/small/'. $imgfile)) {
            return $imgfile;
        } else {
            return 'default.jpg';
        }
    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */

    public function uploadPhoto($old_file = '') {

            // Загружаем класс загрузки фото
            cmsCore::loadClass('upload_photo');
            $inUploadPhoto = cmsUploadPhoto::getInstance();
            // Выставляем конфигурационные параметры
            $inUploadPhoto->input_name    = 'imgfile';
            $inUploadPhoto->upload_dir    = PATH.'/upload/greetings/';
            $inUploadPhoto->small_size_w  = $this->config['thumb1'];
            $inUploadPhoto->medium_size_w = $this->config['thumb2'];
            $inUploadPhoto->thumbsqr      = $this->config['thumbsqr'];
            $inUploadPhoto->is_watermark  = $this->config['watermark'];
            // Процесс загрузки фото
            $file = $inUploadPhoto->uploadPhoto($old_file);

            return $file;

    }

}