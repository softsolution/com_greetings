<h1 class="con_heading">{$pagetitle}</h1>
<div class=clear></div>

<form action="" method="POST" name="greetingsform" enctype="multipart/form-data">
    <input name="title_fake" type="text" id="title_fake" maxlength="250"  value=""/>
    <table id="add_table_greeting">
        <tr>
            <td>
                <span class="name_field_greeting">{$LANG.TITLE_GREETINGS}</span>
            </td>
        </tr>
        <tr>
            <td>
                <input name="title" class="text-input fullfield" type="text" size="52" id="title" value="{$item.title}" />
            </td>
        </tr>
        <tr>
            <td>
                <span class="name_field_greeting">{$LANG.DESC_GREETINGS}</span>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="description"  class="text-input fullfield" id="description" rows="10" cols="40">{$item.description}</textarea>
            </td>
        </tr>
    </table>

    <div id="select_image" style="display:{if $do==edit}block{else}none{/if};"><img id="choose_img" src="{$item.file}" border="0" width="{$cfg.img_width}"></div>
    <input id="file" name="file" type="hidden" value="{$item.file}">

    {* Коллекция картирок *}
    {if $cfg.img_collection}
        {* Выбор картинки из коллекции сайта *}
        <div id="greetings_image">
            <a id="collection_link" style="display:block;" href="javascript:void(0)" onclick="greetings.showCollection();return false;">{$LANG.CHOOSE_FROM_COLLECTION}</a>
            {add_js file='components/greetings/js/jquery.ui.widget.min.js'}
            {add_js file='components/greetings/js/jquery.ui.mouse.min.js'}
            {add_js file='components/greetings/js/jquery.ui.core.min.js'}
            {add_js file='components/greetings/js/jquery.ui.selectable.min.js'}
            {literal}
                <style>
                #selectable li {
                {/literal}
                width:{$cfg.img_width}px;
                height:{$cfg.img_width}px;
                {literal}
                }
                </style>
                <script type="text/javascript">
                $(function() {
                    $("#selectable").selectable();
                    $("#selectable").selectable({
                        selected: function(event, ui) {
                            var file = $('#selectable .ui-selected').attr('rel');
                            $('#file').val(file);
                            $('#choose_img').attr('src', file);
                            greetings.hideCollection();
                            $('#select_image').show();
                        }
                    });
                });
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
            <input name="imgfile" type="file" id="picture" size="33" /><br />
            <span id="file_tipes">{$LANG.TYPES_FILES}</span>
        </div>
        <div class=clear></div>
    {/if}

    {if !$user_id}
        <table>
            <tr>
                <td valign="top" class="">
                    <div><strong>{$LANG.SECUR_SPAM}: </strong></div>
                    <div><small>{$LANG.SECUR_SPAM_TEXT}</small></div>
                </td>
            <tr>
            </tr>
                <td valign="top" class="">{captcha}</td>
            </tr>
        </table>
        <div class=clear></div>
    {/if}
    
    <div class="controlmodule">
        <input type="submit" name="submit" style="margin-top:10px;font-size:18px" value="{if $do=='edit'}{$LANG.SAVE}{else}{$LANG.ADD_GREETINGS}{/if}"/>
    </div>
</form>
{literal}
<script type="text/javascript">
$(document).ready(function() {
    $('#title').focus();
});
</script>
<style>
#title_fake{display:none;}
</style> 
{/literal}