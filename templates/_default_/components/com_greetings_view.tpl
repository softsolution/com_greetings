<div class="float_bar">
    <table cellpadding="2" cellspacing="0">
        <tr>
            <td><img src="/components/board/images/add.gif" border="0"/></td>
            <td><a href="/greetings/add.html">{$LANG.ADD_GREETINGS}</a></td>
        </tr>
    </table>
</div>

<h1 class="con_heading">{$LANG.GREETINGS}</h1>
<div class=clear></div>

<div id="greetings_body">

{if $items}
    <table class="list_greetings"><tbody>
    {foreach key=tid item=item from=$items name=foo}
        <tr id="mt_item" {if $smarty.foreach.foo.index % 2}class="bg_light"{else}class="bg_dark"{/if}>
            <td id="greeting_image">
                <a href="/greetings/read{$item.id}.html">
                    <img src="/upload/greetings/small/{$item.file}" border="0" alt="{$item.title|escape:'html'}"/>
                </a>
            </td>
            <td id="greeting_text">
                <h3><a class="userlink" href="/greetings/read{$item.id}.html">{$item.title}</a></h3>
                <div class="greeting_mess">{$item.description}</div>
                <div class="greeting_date">{$item.pubdate}</div>
                {if $is_admin || ($item.user_id==$user_id && $user_id)}
                <div class="greetings_remote">
                    <span class="editlinks">
                    <a title="{$LANG.EDIT}" href="/greetings/edit{$item.id}.html">{$LANG.EDIT}</a>
                    | <a title="{$LANG.DELETE_GREETINGS}?" href="javascript:void(0)" onclick="greetings.deleteItem({$item.id}, '{csrf_token}');return false;">{$LANG.DELETE}</a>
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
</div>