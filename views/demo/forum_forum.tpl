

{$helper->stats(false, smarty_template)}

<div class="forumhead forum-{$forum.id}">
  <h3 class="{$forum.title}">Messages</h3>

<ul class="forummessages forum-{$forum.id}">
{foreach from=$messages item=message}

  {if empty($message.class)}
  {* note @iteration is odd by is much faster than using {cycle} *}
  <li id="msg-{$message.id}" class="msgwrap {if $message@iteration is odd by 2}odd{else}even{/if}">
  {else}
  <li id="msg-{$message.id}" class="msgwrap {if $message@iteration is odd by 2}odd{else}even{/if} {$message.class}">
  {/if}

Subject: {$message.subject}<br />
id: {$message.author.id}<br />
User: <a href="{$message.author.link}">{$message.author.name}</a><br />

<img src="{$message.author.image.url}" width="{$message.author.image.width}" height="{$message.author.image.height}" alt="{$message.author.image.alt|default:$message.author.name}" />

  </li>
{/foreach}
</ul>

{$helper->stats('%3$d files using %2$.3fMB in %1$.1fms', smarty_template)}
