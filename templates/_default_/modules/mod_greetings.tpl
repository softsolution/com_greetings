<link href="/templates/_default_/css/greetings.css" rel="stylesheet" type="text/css" />

{if $cfg.addgreetings==1}<script type="text/javascript" src="/includes/jquery/jquery.form.js" ></script>
{if $cfg_com.user_image==1}<script language="JavaScript" type="text/javascript" src="/includes/jquery/upload/ajaxfileupload.js"></script>{/if}
{/if}

{if $is_greetings}
{if $cfg.is_pag==1 || $cfg.addgreetings==1}<script type="text/javascript" src="/modules/mod_greetings/js/greetings.js" ></script>{/if}

{if !$is_ajax}<div id="module_ajax_{$module_id}" >{/if}
<div id="module_id" data-module-id="{$module_id}"></div>
{* Форма добавления поздравления *}
{if $cfg.addgreetings==1}
<div id="mod_greetings_add">
    <a class="mod_greetings_addlink" href="javascript:getForm({$module_id})">Добавить поздравление</a>
    <div id="greeting_preloader" style="display:none;"></div>
    <div id="add_greetingform" style="display:none;"></div>
</div>
{/if}

<table width="100%" cellspacing="1" cellpadding="2" border="0"><tbody>
{foreach key=aid item=greeting from=$greetings}
<tr>
    {if $cfg.showimages!=0}
        <td width="{$cfg.imagewidth}" valign=top>
            <div class="mod_greeting_image">
                <img src="{$greeting.file}" border="0" width="{$cfg.imagewidth}" />
            </div>
        </td>
    {/if}
<td valign=top>
    <div class="mod_greeting_entry" style="margin-left:10px;">
        <div class="mod_greeting_date">{$greeting.pubdate}</div>
        <div class="mod_greeting_mess">{$greeting.description}</div>
        <div class="mod_greeting_author">
            {if $cfg.showlink && $greeting.login}
                <a class="mod_userlink" href="/users/{$greeting.login}">{$greeting.title}</a>
            {else}
                {$greeting.title}
            {/if}
        </div>
    </div>
</td> 
{/foreach}
</tbody></table>

{if $cfg.is_pag && $pagebar_module}
    <div class="mod_greetings_pagebar">{$pagebar_module}</div>
{/if}

<a class="mod_greetings_all" href="/greetings">Все поздравления</a>

{if !$is_ajax}</div>{/if}
{else}
    <p>Нет поздравлений</p>
{/if}