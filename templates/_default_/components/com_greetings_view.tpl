{* Файл стилей, при использовании не стандартного шаблона измените путь к этому файлу *}
{add_css file='templates/_default_/css/greetings.css'}

<div class="wrap_greeting_button">
    <div id="greetingbutton">
        <a href="/greetings/add.html" title="{$LANG.ADD_GREETINGS}" class="greetingbut">{$LANG.ADD_GREETINGS}</a>
    </div> 
</div>

<h1 class="con_heading">{$LANG.GREETINGS}</h1>
<div class=clear></div>

{if $user_id}
<div class="tabBar">
    <div class="tabs">
        <a title="{$LANG.ALL_GREETINGS}" class="{if $target=='all'}selected{/if}" href="/greetings">{$LANG.ALL_GREETINGS}</a>
        <a title="{$LANG.MY_GREETINGS}" class="{if $target=='my'}selected{/if}" href="/greetings/my">{$LANG.MY_GREETINGS}</a>
    </div>
</div>
<div class=clear></div>
{/if}

<div id="greetings_body">

{if $is_greeting}
    <table class="list_greetings"><tbody>
    {foreach key=tid item=greeting from=$greetings name=foo}
        <tr id="mt_item" {if $smarty.foreach.foo.index % 2}class="bg_light"{else}class="bg_dark"{/if}>
            <td id="greeting_image">
                <a name="greeting{$greeting.id}"></a>
                <img src="{$greeting.file}" width="{$cfg.img_width}">
            </td>
            <td id="greeting_text">
                <div class="greeting_date">{$greeting.pubdate}</div>
                <div class="greeting_mess">{$greeting.description}</div>
                
                <div class="greeting_author">
                {if $cfg.show_userlink && $greeting.login}
                    <a class="userlink" href="/users/{$greeting.login}">{$greeting.title}</a>
                {else}
                    {$greeting.title}
                {/if}
                </div>

                {if $is_admin || ($greeting.user_id==$user_id && $user_id)}
                <div class="greetings_remote">
                    <span class="editlinks">
                    <a title="{$LANG.EDIT}" href="/greetings/edit{$greeting.id}.html">{$LANG.EDIT}</a>
                    | <a title="{$LANG.DELETE}" title="Удалить резюме" onclick="jsmsg('{$LANG.DELETE_GREETINGS}?', '/greetings/delete{$greeting.id}.html')" href="#">{$LANG.DELETE}</a>
                    </span>
                </div>
                {/if}
            </td>
        </tr>
    {/foreach}
    </tbody></table>
    {$pagebar}
{else}
    <p>{$LANG.NO_GREETINGS}</p>
{/if}

{literal}
<script type="text/JavaScript">
function jsmsg(msg, link){
	if(confirm(msg)){
		window.location.href = link;	
	}
}
</script>
{/literal}

</div>