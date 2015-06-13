<?php
/* ****************************************************************************************** */
/* created by soft-solution.ru                                                                */
/* backend.php of component greetings for InstantCMS 1.10.2                                   */
/* ****************************************************************************************** */
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    
    $opt = cmsCore::request('opt', 'str', 'list_items');
    $component_id  = cmsCore::request('id', 'int', 0);
    
    cmsCore::loadModel('greetings');
    $model = new cms_model_greetings();

    $cfg = $inCore->loadComponentConfig('greetings');

    cpAddPathway('Поздравления', '?view=components&do=config&id='.$component_id);
    echo '<h3>Поздравления</h3>';
    
//=================================================================================================//
//=================================================================================================//

    $toolmenu[] = array('icon'=>'liststuff.gif', 'title'=>'Все поздравления', 'link'=>'?view=components&do=config&id='.$component_id.'&opt=list_items');
    $toolmenu[] = array('icon'=>'newstuff.gif', 'title'=>'Новое поздравление', 'link'=>'?view=components&do=config&id='.$component_id.'&opt=add_item');
  
    if ($opt == 'list_items' || $opt == 'show_item' || $opt == 'hide_item') {
        $toolmenu[] = array('icon'=>'edit.gif', 'title'=>'Редактировать выбранные', 'link'=>"javascript:checkSel('?view=components&do=config&id=".$component_id."&opt=edit_item&multiple=1');");
        $toolmenu[] = array('icon'=>'show.gif', 'title'=>'Публиковать выбранные', 'link'=>"javascript:checkSel('?view=components&do=config&id=".$component_id."&opt=show_item&multiple=1');");
        $toolmenu[] = array('icon'=>'hide.gif', 'title'=>'Скрыть выбранные', 'link'=>"javascript:checkSel('?view=components&do=config&id=".$component_id."&opt=hide_item&multiple=1');");
        $toolmenu[] = array('icon'=>'delete.gif', 'title'=>'Удалить выбранные', 'link'=>"javascript:checkSel('?view=components&do=config&id=".$component_id."&opt=delete_item&multiple=1');");
    }

    $toolmenu[] = array('icon'=>'config.gif', 'title'=>'Настройки', 'link'=>'?view=components&do=config&id='.$component_id.'&opt=config');

    cpToolMenu($toolmenu);

//=================================================================================================//
//=================================================================================================//

    if($opt=='saveconfig'){

        if(!cmsCore::validateForm()) { cmsCore::error404(); }

        $cfg = array();
        $cfg['perpage']          = cmsCore::request('perpage', 'int');
        $cfg['amount']           = cmsCore::request('amount', 'int');
        $cfg['guest_enabled']    = cmsCore::request('guest_enabled', 'int');
        $cfg['guest_publish']    = cmsCore::request('guest_publish', 'int');
        $cfg['moderation']       = cmsCore::request('moderation', 'int');
        $cfg['img_collection']   = cmsCore::request('img_collection', 'int');
        $cfg['img_width']        = cmsCore::request('img_width', 'int');
        $cfg['show_userlink']    = cmsCore::request('show_userlink', 'int');
        $cfg['user_image']       = cmsCore::request('user_image', 'int');
        $cfg['guest_image']      = cmsCore::request('guest_image', 'int');
        $cfg['thumbsqr']         = cmsCore::request('thumbsqr', 'int');

        $inCore->saveComponentConfig('greetings', $cfg);
        cmsCore::addSessionMessage('Настройки сохранены!', 'success');
        cmsCore::redirectBack();
        
    }
    
//=================================================================================================//
//=================================================================================================//

    if ($opt=='config') { 
        cpAddPathway('Поздравления', '?view=components&do=config&id='.$component_id.'&opt=list');
        cpAddPathway('Настройки', '?view=components&do=config&id='.$component_id.'&opt=config');

        $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.form.js"></script>';
        $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';
        $GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/tabs/tabs.css" rel="stylesheet" type="text/css" />';
    ?>

<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $component_id;?>" method="post" name="optform" target="_self" id="optform">
    <input type="hidden" name="csrf_token" value="<?php echo cmsUser::getCsrfToken(); ?>" />
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
                            <strong>Модерация поздравлений от зарегистрированных пользователей:</strong><br />
                        </td>
                        <td valign="top">
                            <label><input name="moderation" type="radio" value="1"  <?php if (@$cfg['moderation']) { echo 'checked="checked"'; } ?> /> Да</label>
                            <label><input name="moderation" type="radio" value="0"  <?php if (@!$cfg['moderation']) { echo 'checked="checked"'; } ?> /> Нет</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Предлагать пользователям выбирать изображение из коллекции сайта:</strong><br />
                            <span class="hinttext">
                                Файлы коллекции сохранить в папку  /upload/greetings/collection/ на сервере.<br />
                                default.jpg - изображение по умолчанию<br />Разрешенные типы файлов: jpg, jpeg, png, gif, bmp
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
        <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='?view=components&do=config&id=<?php echo $component_id; ?>';"/>
    </p>
</form>

<script type="text/javascript">$('#config_tabs > ul#tabs').tabs();</script>

<?php }

//=================================================================================================//
//=================================================================================================//

   if ($opt == 'show_item') {
       if (!isset($_REQUEST['item'])) {
           if (isset($_REQUEST['item_id'])) {
               dbShow('cms_greetings', (int) $_REQUEST['item_id']);
           }
           echo '1'; exit;
       } else {
           dbShowList('cms_greetings', $_REQUEST['item']);
           cmsCore::addSessionMessage('Поздравления опубликованы', 'success');
           cmsCore::redirectBack();
       }
   }

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'hide_item') {
        if (!isset($_REQUEST['item'])) {
            if (isset($_REQUEST['item_id'])) {  dbHide('cms_greetings', $_REQUEST['item_id']); }
            echo '1'; exit;
        } else {
            dbHideList('cms_greetings', $_REQUEST['item']);
            cmsCore::addSessionMessage('Поздравления сняты с публикации', 'success');
            cmsCore::redirectBack();
        }
    }

/* ==================================================================================================== */
/* ======================== Добавляем и редактируем поздравление ====================================== */
/* ==================================================================================================== */

    if ($opt == 'submit_item'){
        
        if(!cmsCore::validateForm()) { cmsCore::error404(); }
        $inCore->includeGraphics();

        $item = array();
        $item['title']         = cmsCore::request('title', 'str');
        $item['description']   = cmsCore::request('description', 'html');
        $item['file']          = cmsCore::request('file', 'str');
        $item['published']     = cmsCore::request('published', 'int');
        $item['user_id']       = $inUser->id;
        $item['ip']            = $inUser->ip;

        if (isset($_FILES["imgfile"]["name"]) && @$_FILES["imgfile"]["name"]!=''){

            $tmp_name = $_FILES["imgfile"]["tmp_name"];
            $file = $_FILES["imgfile"]["name"];
            $path_parts = pathinfo($file);
            $ext = $path_parts['extension'];
            if(mb_strstr($ext, 'php')) { die(); }
            $file = md5($file.time()).'.'.$ext;
            $item['file'] = '/upload/greetings/' . $file;

            if (@move_uploaded_file($tmp_name, PATH."/upload/greetings/".$file)){
                if (isset($cfg['img_width'])) { $img_width = $cfg['img_width']; } else { $img_width = 150; }
                @img_resize(PATH."/upload/greetings/$file", PATH."/upload/greetings/".$file, $img_width, $img_width, $cfg['thumbsqr']);
                @chmod(PATH."/upload/greetings/".$file, 0644);
            }
            
        } else {
            if($item['file']=='') { $item['file']='/upload/greetings/collection/default.jpg'; }
        }

        $greeting_id = $model->addGreeting($item);
        cmsUser::clearCsrfToken();
        cmsCore::redirect('?view=components&do=config&opt=list_items&id='.$component_id);

    }

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'update_item'){

        if(!cmsCore::validateForm()) { cmsCore::error404(); }
        $inCore->includeGraphics();
        
        if(cmsCore::inRequest('item_id')) {
            
            $item_id = cmsCore::request('item_id', 'int', 0);
            $greeting = $model->getGreeting($item_id);
            
            $item = array();
            $item['title']         = cmsCore::request('title', 'str');
            $item['description']   = cmsCore::request('description', 'html');
            $item['file']          = cmsCore::request('file', 'str');
            $item['published']     = cmsCore::request('published', 'int');
            
            if (isset($_FILES["imgfile"]["name"]) && @$_FILES["imgfile"]["name"]!=''){

                $tmp_name = $_FILES["imgfile"]["tmp_name"];
                $file = $_FILES["imgfile"]["name"];
                $path_parts = pathinfo($file);
                $ext = $path_parts['extension'];
                if(mb_strstr($ext, 'php')) { die(); }
                $file = md5($file.time()).'.'.$ext;
                $item['file'] = '/upload/greetings/' . $file;

                if (@move_uploaded_file($tmp_name, PATH."/upload/greetings/".$file)){
                    if (isset($cfg['img_width'])) { $img_width = $cfg['img_width']; } else { $img_width = 150; }
                    @img_resize(PATH."/upload/greetings/$file", PATH."/upload/greetings/".$file, $img_width, $img_width, $cfg['thumbsqr']);
                    @chmod(PATH."/upload/greetings/".$file, 0644);
                }

            } else {
                if($item['file']=='') { $item['file']='/upload/greetings/collection/default.jpg'; }
            }


            if ($item['file'] != $greeting['file']) {
                if (preg_match('/^(\/upload\/greetings\/)?([\da-z]+)\.(jpg|jpeg|bmp|png|gif)$/', $greeting['file'])) {
                    @unlink(PATH . $greeting['file']);
                }
            }
            
            $greeting_id = $model->updateGreeting($item, $item_id);
            
        }
    
        if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
            cmsCore::redirect('?view=components&do=config&id='.$component_id.'&opt=list_items');
        } else {
            cmsCore::redirect('?view=components&do=config&id='.$component_id.'&opt=edit_item');
        }
    }

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'delete_item') {
        if (!isset($_REQUEST['item'])) {
            if (isset($_REQUEST['item_id'])) {
                $model->deleteGreeting((int)$_REQUEST['item_id']);
                cmsCore::addSessionMessage('Поздравление удалено', 'success');
            }
            
        } else {
            $model->deleteGreetings($_REQUEST['item']);
            cmsCore::addSessionMessage('Поздравления удалены', 'success');
        }
        cmsCore::redirect('?view=components&do=config&id='.$component_id.'&opt=list_items');
    }

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'add_item' || $opt == 'edit_item') {
        if ($opt == 'add_item') {
            echo '<h3>Добавить поздравление</h3>';
            cpAddPathway('Добавить поздравление', '?view=components&do=config&id='.$component_id.'&opt=add_item');
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
            $result = $inDB->query($sql);
            if ($inDB->num_rows($result)) {
                $mod = $inDB->fetch_assoc($result);
            }

            if($mod['file']=='') { $mod['file']='/upload/greetings/collection/default.jpg'; }

            echo '<h3>Редактировать поздравление</h3>';
            cpAddPathway('Поздравления', '?view=components&do=config&id='.$component_id . '&opt=list_items');
        } ?>

        <form action="index.php?view=components&amp;do=config&amp;id=<?php echo $component_id;?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
            <input type="hidden" name="csrf_token" value="<?php echo cmsUser::getCsrfToken(); ?>" />
            <table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
                <tr>
                    <td><strong>Публиковать поздравление?</strong></td>
                    <td>
                        <label><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />Да</label>
                        <label><input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />Нет</label>
                    </td>
                </tr>
                <tr>
                    <td><strong>Ваше имя:</strong></td>
                    <td>
                        <input name="title" type="text" size="52" id="title" value="<?php echo @$mod['title']; ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Текст поздравления:</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <textarea name="description"  id="description" rows="10" style="border:solid 1px gray;width:588px"><?php echo @$mod['description']; ?></textarea>
                    </td>
                </tr>
            </table>
            <table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
                <tr>
                    <td valign="top" colspan="3"><strong>Изображение</strong></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div id="select_image">
                            <img src="<?php echo @$mod['file']; ?>" id="choose_img" border="0" width="<?php echo $cfg['img_width']; ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input id="file" name="file" type="hidden" value="<?php echo @$mod['file']; ?>">
                        <div id="user_image">
                            <div>Загрузить изображение</div>

                            <input name="imgfile" type="file" id="picture" size="33" /><br />
                            <span id="file_tipes">Картинка jpg, jpeg, png, gif, bmp</span>
                        </div>
                        <div class=clear></div>
                    </td>
                    <td align="center">
                        <?php if ($cfg['img_collection']) { ?>или
                    </td>
                    <td align="center">
                        <a id="collection_link" style="display:block;" href="javascript:showCollection()">Выбрать из коллекции сайта</a>
                    </td>
                    <?php } ?>
                </tr>
            </table>
        <?php if ($cfg['img_collection']) { ?>
            <div id="greetings_image">
                
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
                    <p>Нажмите на изображение понравившегося фото</p>
                    <?php echo $model->CollectionList($cfg['img_width']); ?>
                </div>
            </div>
            <div class=clear></div>
        <?php } ?>

    <p><label><input name="add_mod" type="submit" id="add_mod" <?php if ($opt == 'add_item') { echo 'value="Добавить поздравление"'; } else { echo 'value="Сохранить изменения"'; } ?> /></label>
       <label><input name="back2" type="button" id="back2" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $component_id; ?>';"/></label>
              <input name="opt" type="hidden" id="do" <?php if ($opt == 'add_item') { echo 'value="submit_item"'; } else { echo 'value="update_item"'; } ?> />
              <?php if ($opt == 'edit_item') { echo '<input name="item_id" type="hidden" value="' . $mod['id'] . '" />'; } ?>
            </p>
    </form>

<?php }

/* ==================================================================================================== */
/* ======================== Список поздравлений ======================================================= */
/* ==================================================================================================== */
        
    if ($opt == 'list_items'){

        cpAddPathway('Поздравления', '?view=components&do=config&id='.$component_id.'&opt=list_items');

        $fields[] = array('title'=>'id', 'field'=>'id', 'width'=>'30');
        $fields[] = array('title'=>'Подпись', 'field'=>'title', 'width'=>'', 'link'=>'?view=components&do=config&id='.$component_id.'&opt=edit_item&item_id=%id%', 'filter' => 15,  'maxlen' => 80);
        $fields[] = array('title'=>'Текст поздравления', 'field'=>'description', 'width'=>'', );
        $fields[] = array('title'=>'ip', 'field'=>'ip', 'width'=>'100');
        $fields[] = array('title'=>'Показ', 'field'=>'published', 'width'=>'50', 'do'=>'opt', 'do_suffix'=>'_item');

        $actions[] = array('title'=>'Редактировать', 'icon'=>'edit.gif', 'link'=>'?view=components&do=config&id='.$component_id.'&opt=edit_item&item_id=%id%');
        $actions[] = array('title'=>'Удалить', 'icon'=>'delete.gif', 'confirm'=>'Удалить поздравление?', 'link'=>'?view=components&do=config&id='.$component_id.'&opt=delete_item&item_id=%id%');

        cpListTable('cms_greetings', $fields, $actions, '', 'pubdate DESC');		
    }

?>