{* ================================================================================ *}
{* ==================== Просмотр поздравления ===================================== *}
{* ================================================================================ *}
<h1 class="con_heading">{$item.title}</h1>
<p class="gr_item_date">{$item.pubdate}</p>

<table cellspacing="10" cellpadding="10" class="gr_item_full">
    <tr>
        {if $item.file}
            <td valign="top">
                <img src="{$item.file}" border="0" alt="{$item.title|escape:'html'}"/>
            </td>
        {/if}
        <td valign="top">
            <div class="gr_text_full">
            	<p>{$item.description}</p>
            </div>
        </td>
    </tr>
</table>

{if $item.moderator}
    <div class="greetings_details">
        <span class="gr_item_edit"><a href="/greetings/edit{$item.id}.html">{$LANG.EDIT}</a></span>
        {if !$item.published && $is_admin}
            <span class="gr_item_publish"><a href="/greetings/publish{$item.id}.html">{$LANG.PUBLISH}</a></span>
        {/if}
        <span class="gr_item_delete"><a title="{$LANG.DELETE_GREETINGS}?" href="javascript:void(0)" onclick="greetings.deleteItem({$item.id}, '{csrf_token}');return false;">{$LANG.DELETE}</a></span>
    </div>
{/if}