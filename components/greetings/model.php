<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_greetings{

    function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        return true;

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

        if (!$this->inDB->num_rows($result)) {
            return false;
        }
        
        $greeting = array();
        $greeting = $this->inDB->fetch_assoc($result);
        
        return $greeting;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

public function updateGreeting($item, $id) {

        $sql = "UPDATE cms_greetings 
                SET title = '{$item['title']}', 
                description = '{$item['description']}', 
                file = '{$item['file']}' 
                WHERE id = '{$id}' 
                LIMIT 1";

        $this->inDB->query($sql);

        return true;
    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */
    
    public function deleteGreeting($id){
        
        $greeting = $this->getGreeting($id);
        
        //delete image
        if (preg_match('/^(\/upload\/greetings\/)?([\da-z]+)\.(jpg)$/', $greeting['file'])) {
            @unlink(PATH.$greeting['file']);
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
    if ($handle = opendir($_SERVER['DOCUMENT_ROOT'] . '/upload/greetings/collection')) {
        $n = 0;
        $html = '';
        $html = '<ol id="selectable">';
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && (strstr($file, '.png') || strstr($file, '.jpg') || strstr($file, '.gif'))) {

                $dir = '/upload/greetings/collection/';
                $html .='<li class="ui-state-default" rel="'.$dir.$file.'"><img width="'.$img_width.'" src="'.$dir.$file.'" border="0" /></li>';
                $n++;
            }
        }
        $html .='</ol>';
        closedir($handle);
    }

    if (!$n) {
        $html = '<p>Папка "/upload/greetings/collection" пуста!</p>';
    }

    $html .='<div align="right" style="clear:both">[<a href="javascript:selectDefault(\'\')">По умолчанию</a>] [<a href="javascript:hideCollection()">Закрыть</a>]</div>';

    return $html;
}

/* ==================================================================================================== */
/* ==================================================================================================== */

}