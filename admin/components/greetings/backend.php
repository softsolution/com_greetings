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
        
        $cfg['thumb1']           = cmsCore::request('thumb1', 'int');
        $cfg['thumb2']           = cmsCore::request('thumb2', 'int');
        $cfg['thumbsqr']         = cmsCore::request('thumbsqr', 'int');
        
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
                        <td >
                            <strong>Количество поздравлений: </strong><br/>
                            <span class="hinttext">
                                Количество поздравлений на странице
                            </span>
                        </td>
                        <td valign="top" width="100">
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
                                Файлы коллекции сохранить в папки:  /upload/greetings/small/ и /upload/greetings/medium/ на сервере. Название файлов коллекции должно начинаться с greeting<br />
                                default.jpg - изображение по умолчанию<br />Разрешенные типы файлов: jpg, png, gif
                            </span>
                        </td>
                        <td valign="top">
                            <label><input name="img_collection" type="radio" value="1"  <?php if (@$cfg['img_collection']) { echo 'checked="checked"'; } ?> /> Да</label>
                            <label><input name="img_collection" type="radio" value="0"  <?php if (@!$cfg['img_collection']) { echo 'checked="checked"'; } ?> /> Нет</label>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Ширина маленькой копии фото поздравления: </strong></td>
                        <td><input type="text" value="150" size="5" name="thumb1" value="<?php echo @$cfg['thumb1'];?>" /> px</td>
                    </tr>
                    <tr>
                        <td><strong>Ширина средней копии фото поздравления: </strong></td>
                        <td><input type="text" value="400" size="5" name="thumb2" value="<?php echo @$cfg['thumb2'];?>" /> px</td>
		    </tr>
                    <tr>
                        <td>
                            <strong>Квадратные изображения:</strong>
                        </td>
                        <td>
                            <label><input type="radio" value="1" name="thumbsqr" <?php if (@$cfg['thumbsqr']) { echo 'checked="checked"'; } ?>>Да</label>
                            <label><input type="radio" value="0" name="thumbsqr" <?php if (@!$cfg['thumbsqr']) { echo 'checked="checked"'; } ?>>Нет</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Разрешить загружать свои изображения: </strong><br/>
                            <span class="hinttext">Пользователи cмогут загружать свои изображения к поздравлениям</span>
                        </td>
                        <td valign="top">
                            <label><input name="user_image" type="radio" value="1"  <?php if (@$cfg['user_image']) { echo 'checked="checked"'; } ?> /> Да</label>
                            <label><input name="user_image" type="radio" value="0"  <?php if (@!$cfg['user_image']) { echo 'checked="checked"'; } ?> /> Нет</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Разрешить гостям загружать изображения: </strong><br/>
                            <span class="hinttext">Если этот и предыдущий пункт включены гости смогут загружать фото</span>
                        </td>
                        <td valign="top">
                            <label><input name="guest_image" type="radio" value="1"  <?php if (@$cfg['guest_image']) { echo 'checked="checked"'; } ?> /> Да</label>
                            <label><input name="guest_image" type="radio" value="0"  <?php if (@!$cfg['guest_image']) { echo 'checked="checked"'; } ?> /> Нет</label>
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

        $item = array();
        $item['title']         = cmsCore::request('title', 'str');
        $item['description']   = cmsCore::request('description', 'html');
        $item['file']          = cmsCore::request('file', 'str');
        $item['published']     = cmsCore::request('published', 'int');
        $item['user_id']       = $inUser->id;
        $item['ip']            = $inUser->ip;

        if (isset($_FILES["imgfile"]["name"]) && @$_FILES["imgfile"]["name"]!=''){

            $file         = $model->uploadPhoto();
            $item['file'] = $file['filename'];
            
        } else {
            if($item['file']=='') { $item['file']='default.jpg'; }
        }

        $greeting_id = $model->addGreeting($item);
        cmsUser::clearCsrfToken();
        cmsCore::redirect('?view=components&do=config&opt=list_items&id='.$component_id);

    }

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'update_item'){

        if(!cmsCore::validateForm()) { cmsCore::error404(); }
        
        if(cmsCore::inRequest('item_id')) {
            
            $item_id = cmsCore::request('item_id', 'int', 0);
            $greeting = $model->getGreeting($item_id);
            
            $item = array();
            $item['title']         = cmsCore::request('title', 'str');
            $item['description']   = cmsCore::request('description', 'html');
            $item['file']          = cmsCore::request('file', 'str');
            $item['published']     = cmsCore::request('published', 'int');
            
            if (isset($_FILES["imgfile"]["name"]) && @$_FILES["imgfile"]["name"]!=''){
                
                $file = $model->uploadPhoto();
                $item['file'] = $file['filename'];

            } else {
                if($item['file']=='') { $item['file']='default.jpg'; }
            }

            
            if ($item['file'] != $greeting['file']) {
                if (!preg_match('/^greeting([a-z0-9]+)\.(jpg|png|gif)$/', $greeting['file']) && $greeting['file']!='default.jpg') {
                    @unlink(PATH.'/upload/greetings/small/'.$greeting['file']);
                    @unlink(PATH.'/upload/greetings/medium/'.$greeting['file']);
                }
            }
            
            $model->updateGreeting($item, $item_id);
            
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

        $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/components/greetings/js/greetings.js"></script>'; 
        
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

            if($mod['file']=='') { $mod['file']='default.jpg'; }

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
                    <td><strong>Заголовок поздравления:</strong></td>
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
                            <img src="/upload/greetings/small/<?php echo @$mod['file']; ?>" id="choose_img" border="0" width="<?php echo $cfg['thumb1']; ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input id="file" name="file" type="hidden" value="<?php echo @$mod['file']; ?>">
                        <div id="user_image">
                            <div>Загрузить изображение</div>
                            <input name="imgfile" type="file" id="picture" size="33" /><br />
                            <span id="file_tipes">Картинка jpg, png, gif</span>
                        </div>
                        <div class=clear></div>
                    </td>
                    <td align="center">
                        <?php if ($cfg['img_collection']) { ?>или
                    </td>
                    <td align="center">
                        <a id="collection_link" style="display:block;" href="javascript:void(0)" onclick="greetings.showCollection();return false;">Выбрать из коллекции сайта</a>
                    </td>
                    <?php } ?>
                </tr>
            </table>
        <?php if ($cfg['img_collection']) { ?>
            <div id="greetings_image">
                    <style>
                        #feedback { font-size: 1.4em; }
                        #collist a.selected {
                            border:3px solid #FECA40 !important;
                        }
                        #collist a {
                            border:3px solid #fff;
                            cursor:pointer;
                            margin: 3px;
                            padding: 1px;
                            float: left;
                            text-align: center;
                            overflow: hidden;
                            width:<?php echo $cfg['thumb1']; ?>px;
                            height:<?php echo $cfg['thumb1']; ?>px;
                        }
                        #file_tipes{font-size:11px;color:#666;}
                    </style>
                    <script>
                    $(function() {
                        $('#collist a').live('click', function(e){
                            var selectimg = $(this).attr('rel');
                            $('#file').val(selectimg);
                            $('#choose_img').attr('src', '/upload/greetings/small/'+selectimg);
                            greetings.hideCollection();
                            $('#select_image').show();
                            $('#collist a').removeClass('selected');
                            $(this).addClass('selected');
                        });
                    });
                    </script>
                <div id="collection_block" style="display:none;">
                    <p>Нажмите на изображение понравившегося фото</p>
                    <?php echo $model->CollectionList($cfg['thumb1']); ?>
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