<div id="dashboard-site" guid="[[$site_guid]]" style="width:100%">
	[[if ($sub_resource == 'charts' || $sub_resource == '')]]
		[[include file="./_site_chart.tpl"]]
	[[/if]]
	[[if ($sub_resource == 'settings')]]
		[[include file="./_site_settings.tpl"]]
	[[/if]]
	[[if ($sub_resource == 'template')]]
		[[include file="./_site_template.tpl"]]
	[[/if]]
	[[if ($sub_resource == 'profile')]]
		[[include file="./_profile.tpl"]]
	[[/if]]
	
</div>