<div ng-cloak flex flex-gt-xs="70">
  <md-content>
    <md-tabs md-dynamic-height md-border-bottom>
      <md-tab label="Basic Site Information">
        <md-content class="md-padding">
			<form name="site-basic-info-frm">
	          	<md-input-container class="md-block">
	              <label>Name | $web.name </label>
	              <input ng-model="Data.Site.name">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Slug | $web.slug </label>
	              <input ng-model="Data.Site.slug">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Vanity URL | $web.vanity_url </label>
	              <input type="text" ng-model="Data.Site.vanity_url">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Phone | $web.phone </label>
	              <input type="tel" ng-model="Data.Site.phone">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Email | $web.email </label>
	              <input type="email" ng-model="Data.Site.email">
	            </md-input-container>
	          	<md-input-container class="md-block">
	          	  <label>Address 1 | $web.address1 </label>
	              <input ng-model="Data.Site.address1">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Address 2 | $web.address2 </label>
	              <input ng-model="Data.Site.address2">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>City | $web.city </label>
	              <input ng-model="Data.Site.city">
	            </md-input-container>
	          	<md-input-container class="md-block">
	          	<label>State/Province | $web.state </label>
	              <input ng-model="Data.Site.state">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Country | $web.country </label>
	              <input ng-model="Data.Site.country">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Zip | $web.zip </label>
	              <input ng-model="Data.Site.zip">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Logo | $web.log </label>
	              <input ng-model="Data.Site.logo">
	            </md-input-container>
	              <br />
	            <md-button type="submit" class="md-primary">Submit</md-button>
            </form>
	        </md-content>
	    </md-tab>
        <md-tab label="Customizations">
        <md-content class="md-padding">
			<form name="site-basic-info-frm">        
			<md-input-container class="md-block"  ng-repeat="(key, field) in Data.SiteData">
              <label>{{ field.content_tag_name }} | $site.{{ field.field }} {{key}}</label>
              <input ng-model="Data.SiteData[key].field_value">
            </md-input-container>
              <br />
            <md-button type="submit"  class="md-primary">Submit</md-button>
            </form>
        </md-content>
      </md-tab>
    </md-tabs>
  </md-content>
</div>