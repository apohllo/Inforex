{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1 style="color: red">Wersja bardzo alfa</h1>

<h1>Automatyczne rozpoznawanie jednostek identyfikujących &mdash; osoby</h1>

<form method="post">
	<textarea name="content" style="width: 99%; height: 120px;">{$content}</textarea>
	<input type="submit" value="Wyślij" name="process">
</form>

<br/>
<div class="ui-widget ui-widget-content ui-corner-all">
	<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Wynik przetwarzania:</div>
	<div style="padding: 5px;">{$result}</div>
</div>
<br/>

</td>

{include file="inc_footer.tpl"}