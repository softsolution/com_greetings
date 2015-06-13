{* Файл стилей, при использовании не стандартного шаблона измените путь к этому файлу *}
{add_css file='templates/_default_/css/greetings.css'}

{if $do=='edit'}
<div class="wrap_greeting_button">
    <div id="greetingbutton">
        <a href="/greetings/delete{$item.id}.html" title="{$LANG.DELETE_GREETINGS}" class="greetingbut">{$LANG.DELETE_GREETINGS}</a>
    </div> 
</div>
{/if}
<h1 class="con_heading">{if $do=='add'}{$LANG.ADD_GREETINGS}{else}{$LANG.EDIT_GREETINGS}{/if}</h1>
<div class=clear></div>
{if $error}<p style="color:red">{$error}</p>{/if}
<form action="" method="POST" name="greetingsform" enctype="multipart/form-data">
    <table id="add_table_greeting">
        <tr><td><span class="name_field_greeting">{$LANG.TITLE_GREETINGS}</span></td></tr>
        <tr><td><input name="title" class="greeting_field fullfield" type="text" size="52" id="title" value="{$item.title}" />
                <div id="titlecheck">{if $validation.title}<p class="novalid">{$LANG.DONT_EMPTY}</p>{/if}</div>
            </td></tr>
        <tr><td><span class="name_field_greeting">{$LANG.DESC_GREETINGS}</span></td></tr>
        <tr><td><textarea name="description"  class="greeting_field fullfield" id="description" rows="10" cols="40">{$item.description}</textarea>
                <div id="descriptioncheck">{if $validation.description}<p class="novalid">{$LANG.DONT_EMPTY}</p>{/if}</div>
            </td></tr>
    </table>

    {* Картинка *}
    <div id="select_image" style="display:{if $do==edit}block{else}none{/if};"><img id="choose_img" src="{$item.file}" border="0" width="{$cfg.img_width}"></div>
    <input id="file" name="file" type="hidden" value="{$item.file}">
    {* /Картинка *}

    {* Коллекция картирок *}
    {if $cfg.img_collection}
        {* Выбор картинки из коллекции сайта *}
        <div id="greetings_image">
            <a id="collection_link" style="display:block;" href="javascript:showCollection()">{$LANG.CHOOSE_FROM_COLLECTION}</a>
            {add_js file='components/greetings/js/jquery.ui.widget.min.js'}
            {add_js file='components/greetings/js/jquery.ui.mouse.min.js'}
            {add_js file='components/greetings/js/jquery.ui.core.min.js'}
            {add_js file='components/greetings/js/jquery.ui.selectable.min.js'}
            {literal}
                <style>
                #feedback { font-size: 1.4em; }
                #selectable .ui-selecting { }
                #selectable li.ui-selected { border:3px solid #FECA40 !important;}
                #selectable .ui-state-default {border:3px solid #fff;cursor:pointer;}
                #selectable { list-style-type: none; margin: 0; padding: 0; }
                #selectable li { margin: 3px; padding: 1px; float: left;
                {/literal}
                width:{$cfg.img_width}px;
                height:{$cfg.img_width}px;
                {literal}
                text-align: center; overflow: hidden;}
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
            {/literal}
            <div id="collection_block" style="display:none;">
                {$collection_list}
            </div>
        </div>
        <div class=clear></div>
    {/if}

    {* Картинка пользователя *}
    {if $cfg.user_image && ($user_id || (!$user_id && $cfg.guest_image))}
        <div id="user_image">
            <div>{$LANG.LOAD_IMAGE}</div>
            <input type="hidden" value="1" name="upload">
            <input name="picture" type="file" id="picture" size="33" /><br />
            <span id="file_tipes">{$LANG.TYPES_FILES}</span>
        </div>
        <div class=clear></div>
    {/if}

    {if !$user_id}
        <p style="margin-bottom:10px">
        {php}echo cmsPage::getCaptcha();{/php}
    </p>
    {/if}
    
    <div class="controlmodule">
        <input type="button" onclick="sendGreetings()" name="gosend" value="{if $do=='edit'}{$LANG.SAVE}{else}{$LANG.ADD}{/if}"/>
        <input type="button" name="cancel" onclick="window.history.go(-1)" value="{$LANG.CANCEL}"/>
    </div>
    
</form>