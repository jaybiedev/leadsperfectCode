<div id="dashboard-account" guid="[[$account_guid]]" style="width:100%">
	[[if ($sub_resource == 'charts' || $sub_resource == '')]]
		[[include file="./_account_chart.tpl"]]
	[[/if]]
	[[if ($sub_resource == 'settings')]]
		[[include file="./_account_settings.tpl"]]
	[[/if]]
	[[if ($sub_resource == 'sites')]]
		[[include file="./_account_sites.tpl"]]
	[[/if]]
	[[if ($sub_resource == 'template')]]
		[[include file="./_account_template.tpl"]]
	[[/if]]
	[[if ($sub_resource == 'profile')]]
		[[include file="./_profile.tpl"]]
	[[/if]]
	
</div>