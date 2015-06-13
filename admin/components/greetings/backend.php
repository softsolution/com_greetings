<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

cpAddPathway('Поздравления', '?view=components&do=config&id='.$_REQUEST['id']);
echo '<h3>Поздравления</h3>';
if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list_items'; }

$toolmenu = array();
if ($opt != 'config') {
    $toolmenu[0]['icon'] = 'newstuff.gif';
    $toolmenu[0]['title'] = 'Новое поздравление';
    $toolmenu[0]['link'] = '?view=components&do=config&id=' . (int) $_REQUEST['id'] . '&opt=add_item';

    $toolmenu[2]['icon'] = 'liststuff.gif';
    $toolmenu[2]['title'] = 'Все поздравления';
    $toolmenu[2]['link'] = '?view=components&do=config&id=' . (int) $_REQUEST['id'] . '&opt=list_items';


    if ($opt == 'list_items' || $opt == 'show_item' || $opt == 'hide_item') {
        $toolmenu[11]['icon'] = 'edit.gif';
        $toolmenu[11]['title'] = 'Редактировать выбранные';
        $toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=" . (int) $_REQUEST['id'] . "&opt=edit_item&multiple=1');";

        $toolmenu[12]['icon'] = 'show.gif';
        $toolmenu[12]['title'] = 'Публиковать выбранные';
        $toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=" . (int) $_REQUEST['id'] . "&opt=show_item&multiple=1');";

        $toolmenu[13]['icon'] = 'hide.gif';
        $toolmenu[13]['title'] = 'Скрыть выбранные';
        $toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=" . (int) $_REQUEST['id'] . "&opt=hide_item&multiple=1');";

        $toolmenu[14]['icon'] = 'delete.gif';
        $toolmenu[14]['title'] = 'Удалить выбранные';
        $toolmenu[14]['link'] = "javascript:checkSel('?view=components&do=config&id=" . (int) $_REQUEST['id'] . "&opt=delete_item&multiple=1');";
    }
    $toolmenu[15]['icon'] = 'config.gif';
    $toolmenu[15]['title'] = 'Настройки';
    $toolmenu[15]['link'] = '?view=components&do=config&id=' . (int) $_REQUEST['id'] . '&opt=config';
}
if ($opt == 'config') {
    $toolmenu[16]['icon'] = 'save.gif';
    $toolmenu[16]['title'] = 'Сохранить';
    $toolmenu[16]['link'] = 'javascript:document.optform.submit();';
}

if ($opt != 'list_items') {
    $toolmenu[17]['icon'] = 'cancel.gif';
    $toolmenu[17]['title'] = 'Отмена';
    $toolmenu[17]['link'] = '?view=components&do=config&id=' . (int) $_REQUEST['id'];
}

cpToolMenu($toolmenu);

//LOAD CURRENT CONFIG
$cfg = $inCore->loadComponentConfig('greetings');
$inCore->loadModel('greetings');
$model = new cms_model_greetings();
$inUser = cmsUser::getInstance();

//CONFIG DEFAULTS
if (!isset($cfg['perpage'])) { $cfg['perpage'] = 15; }
if (!isset($cfg['amount'])) { $cfg['amount'] = 5;}
if (!isset($cfg['guest_enabled'])) { $cfg['guest_enabled'] = 1; }
if (!isset($cfg['guest_publish'])) { $cfg['guest_publish'] = 0; }
if (!isset($cfg['img_collection'])) { $cfg['img_collection'] = 1; }
if (!isset($cfg['img_width'])) { $cfg['img_width'] = 150; }
if (!isset($cfg['show_userlink'])) { $cfg['show_userlink'] = 0; }
if (!isset($cfg['user_image'])) { $cfg['user_image'] = 0; }
if (!isset($cfg['guest_image'])) { $cfg['guest_image'] = 0; }
if (!isset($cfg['thumbsqr'])) { $cfg['thumbsqr'] = 1;}


//SAVE CONFIG
if($opt=='saveconfig'){
    $cfg = array();
    $cfg['perpage']          = $inCore->request('perpage', 'int');
    $cfg['amount']           = $inCore->request('amount', 'int');
    $cfg['guest_enabled']    = $inCore->request('guest_enabled', 'int');
    $cfg['guest_publish']    = $inCore->request('guest_publish', 'int');
    $cfg['img_collection']   = $inCore->request('img_collection', 'int');
    $cfg['img_width']        = $inCore->request('img_width', 'int');
    $cfg['show_userlink']    = $inCore->request('show_userlink', 'int');
    $cfg['user_image']       = $inCore->request('user_image', 'int');
    $cfg['guest_image']      = $inCore->request('guest_image', 'int');
    $cfg['thumbsqr']         = $inCore->request('thumbsqr', 'int');
    
    $inCore->saveComponentConfig('greetings', $cfg);
    $msg = 'Настройки сохранены!';
    $opt = 'config';
}

if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

if ($opt=='config') { 
    cpAddPathway('Поздравления', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list');
    cpAddPathway('Настройки', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=config');
    
    $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.form.js"></script>';
    $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';
    $GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/tabs/tabs.css" rel="stylesheet" type="text/css" />';
?>

<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
<div id="config_tabs" style="margin-top:12px;">
    <ul id="tabs">
        <li><a href="#basic"><span>Общие</span></a></li>
    </ul>
    <div id="basic">
        <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
               <tr>
                    <td width="">
                        <strong>Количество поздравлений: </strong><br/>
                        <span class="hinttext">
                            Количество поздравлений на странице
                        </span>
                    </td>
                    <td valign="top">
                        <input name="perpage" type="text" id="perpage" value="<?php echo @$cfg['perpage'];?>" style="width:50px"/> 
                    </td>
                </tr>
                <tr>
                    <td width="">
                        <strong>Количество поздравлений от одного пользователя в сутки: </strong><br/>
                        <span class="hinttext">
                            Оставьте поле пустым для неограниченного количества
                        </span>
                    </td>
                    <td valign="top">
                        <input name="amount" type="text" id="amount" value="<?php if (@$cfg['amount']>0) echo @$cfg['amount'];?>" style="width:50px"/> 
                    </td>
                </tr>
                <tr>
                    <td width="">
                        <strong>Показывать ссылку на пользователя: </strong><br/>
                        <span class="hinttext">
                            Подпись пользователя будет одновременно ссылкой на его профиль
                        </span>
                    </td>
                    <td valign="top">
                        <label><input name="show_userlink" type="radio" value="1"  <?php if (@$cfg['show_userlink']) { echo 'checked="checked"'; } ?> /> Да</label>
                        <label><input name="show_userlink" type="radio" value="0"  <?php if (@!$cfg['show_userlink']) { echo 'checked="checked"'; } ?> /> Нет</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Принимать поздравления от незарегистрированных пользователей:</strong><br />
                    </td>
                    <td valign="top">
                        <label><input name="guest_enabled" type="radio" value="1"  <?php if (@$cfg['guest_enabled']) { echo 'checked="checked"'; } ?> /> Да</label>
                        <label><input name="guest_enabled" type="radio" value="0"  <?php if (@!$cfg['guest_enabled']) { echo 'checked="checked"'; } ?> /> Нет</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Публиковать поздравления неавторизованных пользователей без модерации:</strong><br />
                    </td>
                    <td valign="top">
                        <label><input name="guest_publish" type="radio" value="1"  <?php if (@$cfg['guest_publish']) { echo 'checked="checked"'; } ?> /> Да</label>
                        <label><input name="guest_publish" type="radio" value="0"  <?php if (@!$cfg['guest_publish']) { echo 'checked="checked"'; } ?> /> Нет</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Предлагать пользователям выбирать изображение из коллекции сайта:</strong><br />
                        <span class="hinttext">
                            Файлы коллекции сохранить в папку  /upload/greetings/collection/ на сервере.<br />
                            default.jpg - изображение по умолчанию<br />Разрешенные типы файлов jpg, png, gif (расширение в нижнем регистре)
                        </span>
                    </td>
                    <td valign="top">
                        <label><input name="img_collection" type="radio" value="1"  <?php if (@$cfg['img_collection']) { echo 'checked="checked"'; } ?> /> Да</label>
                        <label><input name="img_collection" type="radio" value="0"  <?php if (@!$cfg['img_collection']) { echo 'checked="checked"'; } ?> /> Нет</label>
                    </td>
                </tr>
                <tr>
                    <td width="">
                        <strong>Ширина изображения в поздравлении: </strong><br/>
                        <span class="hinttext">В пикселях</span>
                    </td>
                    <td valign="top">
                        <input name="img_width" type="text" id="img_width" value="<?php if (@$cfg['img_width']>0) echo @$cfg['img_width'];?>" style="width:50px"/> px
                    </td>
                </tr>
                <tr>
                    <td width="">
                        <strong>Разрешить загружать свои изображения: </strong><br/>
                        <span class="hinttext">Пользователи cмогут загружать свои изображения к поздравлениям</span>
                    </td>
                    <td valign="top">
                        <label><input name="user_image" type="radio" value="1"  <?php if (@$cfg['user_image']) { echo 'checked="checked"'; } ?> /> Да</label>
                        <label><input name="user_image" type="radio" value="0"  <?php if (@!$cfg['user_image']) { echo 'checked="checked"'; } ?> /> Нет</label>
                    </td>
                </tr>
                <tr>
                    <td width="">
                        <strong>Разрешить гостям загружать изображения: </strong><br/>
                        <span class="hinttext">Если этот и предыдущий пункт включены гости смогут загружать фото</span>
                    </td>
                    <td valign="top">
                        <label><input name="guest_image" type="radio" value="1"  <?php if (@$cfg['guest_image']) { echo 'checked="checked"'; } ?> /> Да</label>
                        <label><input name="guest_image" type="radio" value="0"  <?php if (@!$cfg['guest_image']) { echo 'checked="checked"'; } ?> /> Нет</label>
                    </td>
                </tr>
                <tr>
                    <td><strong>Квадратные изображения:</strong></td>
                    <td valign="top">
                        <select name="thumbsqr" id="select" style="width:60px">
                            <label><option value="1" <?php if (@$cfg['thumbsqr']=='1') { echo 'selected="selected"'; } ?>>Да</option></label>
                            <label><option value="0" <?php if (@$cfg['thumbsqr']=='0') { echo 'selected="selected"'; } ?>>Нет</option></label>
                    </select>
                    </td>
                </tr>
                
        </table>
    </div>
</div>

<p>
    <input name="opt" type="hidden" value="saveconfig" />
    <input name="save" type="submit" id="save" value="Сохранить" />
    <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='?view=components&do=config&id=<?php echo (int)$_REQUEST['id']; ?>';"/>
</p>
</form>

<script type="text/javascript">$('#config_tabs > ul#tabs').tabs();</script>

<?php }

 if ($opt == 'show_item') {
    if (!isset($_REQUEST['item'])) {
        if (isset($_REQUEST['item_id'])) {
            dbShow('cms_greetings', (int) $_REQUEST['item_id']);
        }
        echo '1';
        exit;
    } else {
        dbShowList('cms_greetings', $_REQUEST['item']);
        $opt = 'list_items';
    }
}

if ($opt == 'hide_item') {
    if (!isset($_REQUEST['item'])) {
        if (isset($_REQUEST['item_id'])) {
            dbHide('cms_greetings', (int) $_REQUEST['item_id']);
        }
        echo '1';
        exit;
    } else {
        dbHideList('cms_greetings', $_REQUEST['item']);
        $opt = 'list_items';
    }
}

/* ==================================================================================================== */
/* ======================== Добавляем и редактируем поздравление ====================================== */
/* ==================================================================================================== */

//ДОБАВЛЯЕМ ПОЗДРАВЛЕНИЕ
if ($opt == 'submit_item'){
    
$item = array();
$item['title']         = $_REQUEST['title'];
$item['description']   = $_REQUEST['description'];
$item['file']          = $_REQUEST['file'];
$item['published']     = (int)$_REQUEST['published'];
$item['user_id']       = $inUser->id;
$item['ip']            = $inUser->ip;

if ($item['description']) {
    
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
        }
        
        if ($uploadimage) {
            $item['file'] = '/upload/greetings/' . $filename;
        }
        
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
    
}

header('location:?view=components&do=config&opt=list_items&id='.(int)$_REQUEST['id']);
}

//РЕДАКТИРУЕМ ПОЗДРАВЛЕНИЕ ПОЗДРАВЛЕНИЕ
if ($opt == 'update_item'){
    $item = array();
    $item['title']         = $_REQUEST['title'];
    $item['description']   = $_REQUEST['description'];
    $item['file']          = $_REQUEST['file'];
    $item['published']     = (int)$_REQUEST['published'];
    $id = (int)$_REQUEST['item_id'];

    $greeting = $model->getGreeting($id);
    
    if ($item['description']) {

        //изображение пользователя
        if ($_REQUEST['upload'] && isset($_FILES["picture"]["name"]) && @$_FILES["picture"]["name"] != '') {

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
            }
        }

        if ($uploadimage) {
            $item['file'] = '/upload/greetings/' . $filename;
        }

        if ($item['file'] == '') {
            $item['file'] = '/upload/greetings/collection/default.jpg';
        }

        if ($inCore->moveUploadedFile($source, $uploadfile, $errorCode)) {

            //CREATE THUMBNAIL
            if (isset($cfg['img_width'])) {
                $img_width = $cfg['img_width'];
            } else {
                $img_width = 150;
            }

            //resize image
            @img_resize($uploadfile, $uploadimage, $img_width, $img_width, $cfg['thumbsqr']);

            //DELETE ORIGINAL							
            @unlink($uploadfile);
        }

        if ($item['file'] != $greeting['file']) {
            //удаляем старое изображение, если оно было загружено пользователем
            if (preg_match('/^(\/upload\/greetings\/)?([\da-z]+)\.(jpg)$/', $greeting['file'])) {
                @unlink(PATH . $greeting['file']);
            }
        }

        //обновляем поздравление
        $greeting_id = $model->updateGreeting($item, $id);
    }

    if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
        header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items');
    } else {
        header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_item');
    }
}
//УДАЛЯЕМ ЗАПИСИ
if ($opt == 'delete_item') {
    if (!isset($_REQUEST['item'])) {
        if (isset($_REQUEST['item_id'])) {
            $model->deleteGreeting((int)$_REQUEST['item_id']);
        }
    } else {
        $model->deleteGreetings($_REQUEST['item']);
    }
    header('location:?view=components&do=config&id=' . (int) $_REQUEST['id'] . '&opt=list_items');
}

//ВНЕШНЕЕ ПРЕДСТАВЛЕНИЕ
if ($opt == 'add_item' || $opt == 'edit_item') {
    if ($opt == 'add_item') {
        echo '<h3>Добавить поздравление</h3>';
        cpAddPathway('Добавить поздравление', '?view=components&do=config&id=' . (int) $_REQUEST['id'] . '&opt=add_item');
        $mod['published'] = 1;
    } else {
        if (isset($_REQUEST['multiple'])) {
            if (isset($_REQUEST['item'])) {
                $_SESSION['editlist'] = $_REQUEST['item'];
            } else {
                echo '<p class="error">Нет выбранных объектов!</p>';
                return;
            }
        }

        $ostatok = '';

        if (isset($_SESSION['editlist'])) {
            $id = array_shift($_SESSION['editlist']);
            if (sizeof($_SESSION['editlist']) == 0) {
                unset($_SESSION['editlist']);
            } else {
                $ostatok = '(На очереди: ' . sizeof($_SESSION['editlist']) . ')';
            }
        } else {
            $id = (int) $_REQUEST['item_id'];
        }

        $sql = "SELECT * FROM cms_greetings WHERE id = $id LIMIT 1";
        $result = dbQuery($sql);
        if (mysql_num_rows($result)) {
            $mod = mysql_fetch_assoc($result);
        }
        
        if($mod['file']=='') { $mod['file']='/upload/greetings/collection/default.jpg'; }

        echo '<h3>Редактировать поздравление</h3>';
        cpAddPathway('Поздравления', '?view=components&do=config&id=' . (int) $_REQUEST['id'] . '&opt=list_items');
    } ?>

<form action="index.php?view=components&amp;do=config&amp;id=<?php echo (int)$_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
<table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
<tr>
<td><strong>Публиковать поздравление?</strong></td>
<td><label><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />Да</label>
<label><input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />Нет</label></td>
</tr>
<tr><td><strong>Ваше имя:</strong></td>
<td><input name="title" type="text" size="52" id="title" value="<?php echo @$mod['title']; ?>" />
</td>
</tr>
<tr><td colspan="2"><strong>Текст поздравления:</strong></td>
<tr><td colspan="2"><textarea name="description"  id="description" rows="10" style="border:solid 1px gray;width:605px"><?php echo @$mod['description']; ?></textarea>
</td></tr>
<td valign="top"><strong>Изображение</strong></td>
<td><div id="select_image"><img src="<?php echo @$mod['file']; ?>" id="choose_img" border="0" width="<?php echo $cfg['img_width']; ?>"></div>
<input id="file" name="file" type="hidden" value="<?php echo @$mod['file']; ?>">
</table>
    <?php if ($cfg['img_collection']) { ?>
        <div id="greetings_image">
            <a id="collection_link" style="display:block;" href="javascript:showCollection()">Выбрать из коллекции сайта</a>
            <?php 
                $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/components/greetings/js/jquery.ui.widget.min.js"></script>'; 
                $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/components/greetings/js/jquery.ui.mouse.min.js"></script>'; 
                $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/components/greetings/js/jquery.ui.core.min.js"></script>'; 
                $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/components/greetings/js/jquery.ui.selectable.min.js"></script>'; 
            ?>

                <style>
                #feedback { font-size: 1.4em; }
                #selectable .ui-selecting { }
                #selectable li.ui-selected { border:3px solid #FECA40 !important;}
                #selectable .ui-state-default {border:3px solid #fff;cursor:pointer;}
                #selectable { list-style-type: none; margin: 0; padding: 0; }
                #selectable li { margin: 3px; padding: 1px; float: left;
                width:<?php echo $cfg['img_width']; ?>px;
                height:<?php echo $cfg['img_width']; ?>px;
                text-align: center; overflow: hidden;}
                #file_tipes{font-size:11px;color:#666;}
                </style>
                <script>
                $(function() {
                        $("#selectable").selectable();
                        $("#selectable").selectable({
                            selected: function(event, ui) {
                                var file = $('#selectable .ui-selected').attr('rel');
                                $('#file').val(file);
                                $('#choose_img').attr('src', file);
                                hideCollection();
                                $('#select_image').show();
                            }
                        });
                });
                function showCollection(){
                    $('#collection_block').slideUp('fast');
                    $('#collection_block').slideDown('fast');
                    $('#collection_link').hide();
                }
                function hideCollection(){
                    $('#collection_block').slideDown('fast');
                    $('#collection_block').slideUp('fast');
                    $('#collection_link').show();
                }
                function selectDefault(){
                    $('#file').val('/upload/greetings/collection/default.jpg');
                    $('#choose_img').attr('src', '/upload/greetings/collection/default.jpg');
                    hideCollection();
                    $('#select_image').show();
                }
                </script>
            <div id="collection_block" style="display:none;">
                <?php echo $model->CollectionList($cfg['img_width']); ?>
            </div>
        </div>
        <div class=clear></div>
    <?php } ?>
        

    <div id="user_image">
        <div>Загрузить изображение</div>
        <input type="hidden" value="1" name="upload">
        <input name="picture" type="file" id="picture" size="33" /><br />
        <span id="file_tipes">Картинка jpg, jpeg, png, gif, bmp</span>
    </div>
    <div class=clear></div>

<p><label><input name="add_mod" type="submit" id="add_mod" <?php if ($opt == 'add_item') { echo 'value="Добавить поздравление"'; } else { echo 'value="Сохранить изменения"'; } ?> /></label>
   <label><input name="back2" type="button" id="back2" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>';"/></label>
          <input name="opt" type="hidden" id="do" <?php if ($opt == 'add_item') { echo 'value="submit_item"'; } else { echo 'value="update_item"'; } ?> />
          <?php if ($opt == 'edit_item') { echo '<input name="item_id" type="hidden" value="' . $mod['id'] . '" />'; } ?>
        </p>
</form>

<?php }

/* ==================================================================================================== */
/* ======================== Список поздравлений ======================================================= */
/* ==================================================================================================== */
        
if ($opt == 'list_items'){
cpAddPathway('Поздравления', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items');

//TABLE COLUMNS
$fields = array();

$fields[0]['title'] = 'id';		$fields[0]['field'] = 'id';		$fields[0]['width'] = '30';

$fields[1]['title'] = 'Подпись';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
$fields[1]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_item&item_id=%id%';
$fields[1]['filter'] = 15;
$fields[1]['maxlen'] = 80;

$fields[2]['title'] = 'Текст поздравления';
$fields[2]['field'] = 'description';
$fields[2]['width'] = '';

$fields[3]['title'] = 'ip';
$fields[3]['field'] = 'ip';
$fields[3]['width'] = '100';

$fields[4]['title'] = 'Показ';		$fields[4]['field'] = 'published';	$fields[4]['width'] = '50';
$fields[4]['do'] = 'opt';               $fields[4]['do_suffix'] = '_item';

//ACTIONS
$actions = array();
$actions[0]['title'] = 'Редактировать';
$actions[0]['icon']  = 'edit.gif';
$actions[0]['link']  = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

$actions[1]['title'] = 'Удалить';
$actions[1]['icon']  = 'delete.gif';
$actions[1]['confirm'] = 'Удалить поздравление?';
$actions[1]['link']  = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=delete_item&item_id=%id%';

//Print table
cpListTable('cms_greetings', $fields, $actions, '', 'pubdate DESC');		
}


?>