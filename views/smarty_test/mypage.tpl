
{extends file='smarty_test/myproject.tpl'}

{block name=title}My Page Title{/block}
{block name=head}
  <link href="/css/mypage.css" rel="stylesheet" type="text/css"/>
  <script src="/js/mypage.js"></script>
{/block}
{block name=body}My HTML Page Body goes here{/block}
