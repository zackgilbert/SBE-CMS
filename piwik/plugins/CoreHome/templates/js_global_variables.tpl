<script type="text/javascript">
	var piwik = {literal}{}{/literal};
	piwik.token_auth = "{$token_auth}";
	piwik.piwik_url = "{$piwikUrl}";
	{if isset($idSite)}piwik.idSite = "{$idSite}";{/if}
	{if isset($siteName)}piwik.siteName = "{$siteName}";{/if}
	{if isset($siteMainUrl)}piwik.siteMainUrl = "{$siteMainUrl}";{/if}
	{if isset($period)}piwik.period = "{$period}";{/if}
	{if isset($date)}piwik.currentDateString = "{$date}";{/if}
	{if isset($minDateYear)}piwik.minDateYear = {$minDateYear};{/if}
	{if isset($minDateMonth)}piwik.minDateMonth = parseInt("{$minDateMonth}", 10);{/if}
	{if isset($minDateDay)}piwik.minDateDay = parseInt("{$minDateDay}", 10);{/if}
</script>
