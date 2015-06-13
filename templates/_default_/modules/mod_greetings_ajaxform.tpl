{if $error}<div class="mod_greetings_errors">{$error}</div>{/if}
<form action="/modules/mod_greetings/ajax/addgreeting.php" method="POST" name="greetingsform" id="greetingsform" enctype="multipart/form-data">
    <input name="formexist" id="formexist" type="hidden" value="exist">
    <input name="sid" type="hidden" value="{$sid}">
    <table id="mod_greetings">
        <tr><td><span class="">Ваше имя</span></td></tr>
        <tr><td>
                <input type="text" maxlength="100" name="title" class="mod_greeting_title" value="{$item.title}" />
            </td></tr>
        <tr><td><span class="">Текст поздравления</span></td></tr>
        <tr><td><textarea name="description"  class="mod_greeting_textarea" id="mod_description">{$item.description}</textarea>
            </td></tr>
    </table>
    <input type="hidden" value="" name="imageurl" id="imageurl">
    {if !$user_id}
        <p style="margin-bottom:10px">
        {php}echo cmsPage::getCaptcha();{/php}
        </p>
    {/if}
</form>    

    {* Картинка пользователя *}
    {if $cfg_com.user_image}
        <div id="user_image">
            <div>Загрузить изображение</div>
            
            <input name="attach_img" type="file" id="attach_img" /><br />
            <span id="mod_file_tipes">Картинка jpg, jpeg, png, gif, bmp</span>
        </div>
        <div class=clear></div>
    {/if}


    <div class="mod_greetings_control">
        <input type="button" onclick="sendGreetings()" name="gosend" value="Добавить" id="gosend" />
        <input type="button" name="cancel" onclick="hideGreetingForm()" value="Отмена"/>
    </div>
    
