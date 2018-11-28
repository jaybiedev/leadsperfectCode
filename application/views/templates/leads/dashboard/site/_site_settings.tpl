<div ng-cloak flex flex-gt-xs="70">
 <form method="POST" action="" enctype="multipart/form-data">
  <md-content>
    <md-tabs md-dynamic-height md-border-bottom>
      <md-tab label="Basic Site Information">
        <md-content class="md-padding">
			<form name="site-basic-info-frm">
	          	<md-input-container class="md-block">
	              <label>Name | $web.name </label>
	              <input name="name" ng-model="Data.Site.name">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Slug | $web.slug </label>
	              <input name="slug" ng-model="Data.Site.slug">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Vanity URL | $web.vanity_url </label>
	              <input name="vanity_url"  type="text" ng-model="Data.Site.vanity_url">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Phone | $web.phone </label>
	              <input name="phone"  type="tel" ng-model="Data.Site.phone">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Email | $web.email </label>
	              <input name="email" type="email" ng-model="Data.Site.email">
	            </md-input-container>
	          	<md-input-container class="md-block">
	          	  <label>Address 1 | $web.address1 </label>
	              <input name="address1"  ng-model="Data.Site.address1">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Address 2 | $web.address2 </label>
	              <input name="address2"  ng-model="Data.Site.address2">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>City | $web.city </label>
	              <input name="city"  ng-model="Data.Site.city">
	            </md-input-container>
	          	<md-input-container class="md-block">
	          	<label>State/Province | $web.state </label>
	              <input name="state"  ng-model="Data.Site.state">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Country | $web.country </label>
	              <input name="country"  ng-model="Data.Site.country">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Zip | $web.zip </label>
	              <input name="zip"  ng-model="Data.Site.zip">
	            </md-input-container>
	          	<md-input-container class="md-block">
	              <label>Logo | $web.log </label>
	              <input name="logo" type="file" ng-model="Data.Site.logo" style="padding-left: 60%;"
	              		value="Data.Site.logo"/>
	              <span>{{Data.Site.logo}}</span>
	              
	              <!--input ng-model="Data.Site.logo" -->
	            </md-input-container>
	              <br />
	            <md-button type="submit" class="md-primary">Submit</md-button>
            </form>
	        </md-content>
	    </md-tab>
        <md-tab label="Customizations">
        <md-content class="md-padding">
			<!-- form name="site-basic-info-frm" -->        
			<md-input-container class="md-block"  ng-repeat="(key, field) in Data.SiteData">
              <label>{{ field.content_tag_name }} | $web.{{ field.field }}</label>
              <input name="customization[{{key}}]" ng-model="Data.SiteData[key].field_value" ng-if="field.content_tag_system_name=='TEXT'">
              <textarea name="customization[{{key}}]" ng-model="Data.SiteData[key].field_value" style="height: 150px;"
              		ng-if="field.content_tag_system_name=='TEXTAREA'" rows="4">{{Data.SiteData[key].field_value}}</textarea>
              <input name="customization[{{key}}]" type="file" ng-model="Data.SiteData[key].field_value" style="padding-left: 60%;"
              		ng-if="field.content_tag_system_name=='IMAGE'" value="{{Data.SiteData[key].field_value}}"/>
              <span ng-if="field.content_tag_system_name=='IMAGE'">{{Data.SiteData[key].field_value}}</span>
            </md-input-container>
              <br />
            <md-button type="submit"  class="md-primary">Submit</md-button>
            <!-- /form -->
        </md-content>
      </md-tab>
    </md-tabs>
  </md-content>
  </form>
</div>