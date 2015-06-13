<?php

    header('Content-Type: text/html; charset=windows-1251');
    session_start();

    define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

    include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
    $inCore->loadClass('user');		//����

    $inUser = cmsUser::getInstance();
    $inDB  = cmsDatabase::getInstance();

    //LOAD CURRENT CONFIG
    $cfg = $inCore->loadComponentConfig('greetings');
    
//CONFIG DEFAULTS FROM COMPONENT
if (!isset($cfg['amount'])) { $cfg['amount'] = 5;}//���������� ��������� �� ������ ������������ � �����
if (!isset($cfg['guest_enabled'])) { $cfg['guest_enabled'] = 1; }//��������� �� ������������ �� �������������������� �������������
if (!isset($cfg['img_width'])) { $cfg['img_width'] = 150; }//������ ��������
if (!isset($cfg['user_image'])) { $cfg['user_image'] = 0; }//��������� ������������� ��������� ���� �����������
if (!isset($cfg['thumbsqr'])) { $cfg['thumbsqr'] = 1;}//���������� �����������
if (!isset($cfg['guest_image'])) { $cfg['guest_image'] = 0; }//����� �� ����� ��������� ��������

$inUser->update();

$user_id   = $inUser->id;
$is_admin  = $inCore->userIsAdmin($inUser->id);
// ������������ �� �����������
if(!$user_id && !$cfg['guest_image'] && !$cfg['guest_enabled'] && !$cfg['user_image']){
        echo "{";
        echo "error: '�������� ������ ������ ��� ������������������!',\n";
        echo "msg: ''\n";
        echo "}";
        die();
}

// ���� ������������ ������������ �� �����������
if($user_id && !$cfg['user_image'] && !$cfg['user_image']){
        echo "{";
        echo "error: '�������� ������ �� ��������!',\n";
        echo "msg: ''\n";
        echo "}";
        die();
}


    if(isset($_FILES['attach_img'])) {

        if ($cfg['user_image']){
            
            //������� ������� ���������� ������������ ������� �������
            if ($cfg['amount']!=0 && !$is_admin) {
                $user_ip = $inUser->ip;
                $amount_today = $inDB->rows_count('cms_greetings', "DATE(pubdate) BETWEEN DATE(NOW()) AND DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AND ip = '$user_ip'");

                if($cfg['amount']<=$amount_today){
                    
                    echo "{";
                    echo		"error: '������� ����� ���������� ������������ �� �������!',\n";
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

            // ������������ ����
            $filename       = md5($realfile.time()).'-orig.'.$ext;
            $uploadfile     = $uploaddir . $filename;
            
            // ������������ ����
            $filename_jpg   = md5($realfile.time()).'.jpg';
            $uploadphoto    = $uploaddir . $filename_jpg;
            
            // url �����
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
                                    echo	"error: '���� �� ��������! ��������� ��� ���, ������ � ����� �� ������ � ����� /upload/greetings.',\n";
                                    echo	"msg: ''\n";
                                    echo "}";
                            } 

                    } else { 
                                    echo "{";
                                    echo	"error: '�������� ��� �����! ���������� ����: jpg, jpeg, gif, png, bmp.',\n";
                                    echo	"msg: ''\n";
                                    echo "}";
                    } //filetype
            //}

    } //img is on
		else {
			echo "{";
			echo		"error: '�������� ������ ���������!',\n";
			echo		"msg: ''\n";
			echo "}";	
		}
	} else { 	
			echo "{";
			echo		"error: '���� �� ��������!',\n";
			echo		"msg: ''\n";
			echo "}";
	 }

	return;
?>