
<h2>A demonstration of some Smarty features</h2>

<table>

<tr>
<th>Controller</th>
<th>View</th>
<th>Output</th>
</tr>

<tr><th colspan="3" class="titlerow">
Variables
</th></tr>
<tr>
<td><pre>{capture assign="text"}{literal}
$view->var = 'A variable';
{/literal}{/capture}{$text|htmlspecialchars}</pre></td>

<td><pre>{ldelim}$var{rdelim}</pre></td>
<td>{$var}</td>
</tr>

<tr><th colspan="3" class="titlerow">
The 'default' modifier
</th></tr>
<tr>
<td><pre>
</pre></td>
<td><pre>{ldelim}$notset|default:'Not set!'{rdelim}</pre></td>
<td>{$notset|default:'Not set!'}</td>
</tr>

<tr><th colspan="3" class="titlerow">
The 'foreach' construct: note the new $item@key syntax in Smarty3
</th></tr>
<tr>
<td><pre>{capture assign="text"}{literal}
$view->things = array(
  'First'  => 'One',
  'Second' => 'Two',
  'Third'  => 'Three',
);
{/literal}{/capture}{$text|htmlspecialchars}</pre></td>

<td><pre>{capture assign="text"}{literal}
<ul>
{foreach from=$things item=thevalue}
  <li>{$thevalue@key}: {$thevalue}</li>
{/foreach}
</ul>
{/literal}{/capture}{$text|htmlspecialchars}</pre></td>

<td>
<ul>
{foreach from=$things item=thevalue}
  <li>{$thevalue@key}: {$thevalue}</li>
{/foreach}
</ul>
</td>

</tr>

<tr><th colspan="3" class="titlerow">
Comments
</th></tr>
<tr>
<td></td>

<td><pre>{literal}
{* This is a comment *}
{/literal}</pre></td>

<td>
{* This is a comment *}
</td>

</tr>

<tr><th colspan="3" class="titlerow">
Included templates
</th></tr>
<tr>

<td></td>

<td><pre>{literal}
{include 'smarty_demo_include.tpl'}
{/literal}</pre></td>

<td>
{include 'smarty_demo_include.tpl'}
</td>

</tr>

<tr><th colspan="3" class="titlerow">
The helper object (available in all views in Smarty3 for Kohana)
</th></tr>
<tr>

<td></td>

<td><pre>{capture assign="text"}{literal}
{$helper->stats('%3$d files using %2$.1fMB in %1$.0fms')}
{/literal}{/capture}{$text|htmlspecialchars}</pre></td>

<td>
{$helper->stats('%3$d files using %2$.1fMB in %1$.0fms')}
</td>

</tr>

<tr><th colspan="3" class="titlerow">
An unmodified Smarty object is available for advanced/legacy use if you wish
</th></tr>
<tr>
<td><pre>{capture assign="text"}{literal}
$view->smarty()->assign('direct', 'Smarty');
{/literal}{/capture}{$text|htmlspecialchars}</pre></td>

<td><pre>{literal}
{$direct}
{/literal}</pre></td>

<td>
{$direct}
</td>


<tr><th colspan="3" class="titlerow">
By default (changeable in the config file), this module overrides Smarty's default PASSTHRU setting for PHP tags
</th></tr>
<tr>

<td></td>

<td><pre>{capture assign="text"}{literal}
<?php die; ?>
{/literal}{/capture}{$text|htmlspecialchars}</pre></td>

<td>
<?php die; ?>
</td>

</tr>

</table>

