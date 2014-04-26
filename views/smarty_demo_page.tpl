<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>{$title|default:'No title supplied'}</title>
<style>
body { font-family: "Verdana", sans-serif; font-size: 90%; }
table { border-collapse: collapse; margin: auto; border: 2px solid gray; }
td, th { border: 1px solid silver; padding: 2px 4px; }
th { background: gray; color: white; }
.titlerow { text-align: left; background: silver; color: black; font-weight: normal; font-size: 80%; }
pre { margin: 0; }
.bylines { text-align: center; }
.stats { text-align: center; font-size: 80%; }
</style>

</head>

<body>

<h1>Mr Anchovy's Smarty3 module for the Kohana PHP Framework</h1>

{$content|default:'No content supplied'}

<p class="bylines">
<span><a href="http://github.com/MrAnchovy/Kohana_Smarty3">Smarty3 module</a> version {$versions.module} &bull;
<a href="http://www.smarty.net">Smarty</a> version {$versions.smarty} &bull;
<a href="http://kohanaframework.org/">Kohana</a> version {$versions.kohana}
</span>
</p>

<p class="bylines"><span><small>
PHP {$versions.php} &bull;
{if $versions.server}{$versions.server} &bull; {/if}
{$helper->stats('%3$d files using %2$.1fMB in %1$.0fms')}
</small></span></p>



</body>
</html>
