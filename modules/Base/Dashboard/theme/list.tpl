<table id="Base_Dashboard" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="4" class="header">{$header}</td>
	</tr>
	<tr>

	{assign var=x value=0}
	{foreach key=key item=button from=$buttons}
	{assign var=x value=$x+1}

		<td>


<!-- SHADIW BEGIN -->
	<div class="layer" style="padding: 9px; width: 220px;">
		<div class="content_shadow">
<!-- -->

			{$__link.buttons.$key.link.open}
			<div class="button">
				{if isset($button.icon)}
				<img src="{$button.icon}" border="0" width="32" height="32" align="middle">&nbsp;&nbsp;
				{/if}
				{$__link.buttons.$key.link.text}
			</div>
			{$__link.buttons.$key.link.close}

<!-- SHADOW END -->
 		</div>
		<div class="shadow-top">
			<div class="left"></div>
			<div class="center"></div>
			<div class="right"></div>
		</div>
		<div class="shadow-middle">
			<div class="left"></div>
			<div class="right"></div>
		</div>
		<div class="shadow-bottom">
			<div class="left"></div>
			<div class="center"></div>
			<div class="right"></div>
		</div>
	</div>
<!-- -->

		</td>
			<!-- $key holds name of the module -->
	{if ($x%4)==0}
	</tr>
	<tr>
	{/if}
	{/foreach}
	</tr>
</table>
