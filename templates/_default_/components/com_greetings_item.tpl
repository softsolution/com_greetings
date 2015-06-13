{* ================================================================================ *}
{* ==================== Просмотр поздравления ===================================== *}
{* ================================================================================ *}
<h1 class="con_heading" style="text-align: center;">{$item.title}</h1>

<table width="100%" cellspacing="10" cellpadding="10" class="gr_item_full">
    <tr>
        {if $item.file}
            <td valign="top" align="center">
                <img src="/upload/greetings/medium/{$item.file}" border="0" alt="{$item.title|escape:'html'}"/>
            </td>
        {/if}
    </tr>
    <tr>
        <td valign="top"  align="center">
            <div class="gr_text_full">
            	{$item.description}
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