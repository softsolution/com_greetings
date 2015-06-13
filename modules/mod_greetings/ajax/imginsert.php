<?php

/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

	define('PATH', $_SERVER['DOCUMENT_ROOT']);
	include(PATH.'/core/ajax/ajax_core.php');

    //LOAD CURRENT CONFIG
    $cfg = $inCore->loadComponentConfig('greetings');
    
//CONFIG DEFAULTS FROM COMPONENT
if (!isset($cfg['amount'])) { $cfg['amount'] = 5;}//количество сообщений от одного пользователя в сутки
if (!isset($cfg['guest_enabled'])) { $cfg['guest_enabled'] = 1; }//принимать ли поздравления от незарегистрированных пользователей
if (!isset($cfg['img_width'])) { $cfg['img_width'] = 150; }//ширина картинки
if (!isset($cfg['user_image'])) { $cfg['user_image'] = 0; }//разрешить пользователям загружать свои изображения
if (!isset($cfg['thumbsqr'])) { $cfg['thumbsqr'] = 1;}//квадратные изображения
if (!isset($cfg['guest_image'])) { $cfg['guest_image'] = 0; }//могут ли гости добавлять картинки

$user_id   = $inUser->id;
$is_admin  = $inCore->userIsAdmin($inUser->id);
// пользователь не авторизован
if(!$user_id && !$cfg['guest_image'] && !$cfg['guest_enabled'] && !$cfg['user_image']){
        echo "{";
        echo "error: 'Загрузка файлов только для зарегистрированных!',\n";
        echo "msg: ''\n";
        echo "}";
        die();
}

// если пользователь пользователь не авторизован
if($user_id && !$cfg['user_image'] && !$cfg['user_image']){
        echo "{";
        echo "error: 'Загрузка файлов не доступна!',\n";
        echo "msg: ''\n";
        echo "}";
        die();
}


    if(isset($_FILES['attach_img'])) {

        if ($cfg['user_image']){
            
            //считаем сколько объявлений пользователь добавил сегодня
            if ($cfg['amount']!=0 && !$is_admin) {
                $user_ip = $inUser->ip;
                $amount_today = $inDB->rows_count('cms_greetings', "DATE(pubdate) BETWEEN DATE(NOW()) AND DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AND ip = '$user_ip'");

                if($cfg['amount']<=$amount_today){
                    
                    echo "{";
                    echo		"error: 'Ичерпан лимит добавления поздравлений на сегодня!',\n";
                    echo		"msg: ''\n";
                    echo "}";	
                    die();
                }
            }

            $uploaddir  = PATH.'/upload/greetings/';
            $realfile   = $_FILES['attach_img']['name'];

            $path_parts = pathinfo($realfile);
            $ext        = strtolower($path_parts['extension']);

            if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png'){

            // оригинальный файл
            $filename       = md5($realfile.time()).'-orig.'.$ext;
            $uploadfile     = $uploaddir . $filename;
            
            // сконверченый файл
            $filename_jpg   = md5($realfile.time()).'.jpg';
            $uploadphoto    = $uploaddir . $filename_jpg;
            
            // url файла
            $fileurl = '/upload/greetings/'.$filename_jpg;

            if ($inCore->moveUploadedFile($_FILES['attach_img']['tmp_name'], $uploadfile, $_FILES['attach_img']['error'])) {

                $inCore->includeGraphics();
                $sql = "INSERT INTO cms_upload_images (post_id, session_id, fileurl, target) VALUES ('0', '".session_id()."', '{$fileurl}', '$place')";
                $inDB->query($sql);

            @img_resize($uploadfile, $uploadphoto, $cfg['img_width'], $cfg['img_width'], $cfg['thumbsqr']);

            if ($cfg['watermark']) { @img_add_watermark($uploadphoto); }

            @unlink($uploadfile);

                                    echo "{";
                                    echo	"error: '',\n";
                                    echo	"msg: '".$filename_jpg."'\n";
                                    echo "}";
                            } else { 
                                    echo "{";
                                    echo	"error: 'Файл не загружен! Проверьте его тип, размер и права на запись в папку /upload/greetings.',\n";
                                    echo	"msg: ''\n";
                                    echo "}";
                            } 

                    } else { 
                                    echo "{";
                                    echo	"error: 'Неверный тип файла! Допустимые типы: jpg, jpeg, gif, png, bmp.',\n";
                                    echo	"msg: ''\n";
                                    echo "}";
                    } //filetype
            //}

    } //img is on
		else {
			echo "{";
			echo		"error: 'Загрузка файлов запрещена!',\n";
			echo		"msg: ''\n";
			echo "}";	
		}
	} else { 	
			echo "{";
			echo		"error: 'Файл не загружен!',\n";
			echo		"msg: ''\n";
			echo "}";
	 }

	return;
?>