<html lang = "en">
   <head>
   <title>Dashboard</title>
   	<meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="/assets/css/leads/dashboard.css" rel="stylesheet">
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <link href="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.0/angular-material.min.css" rel="stylesheet" >
 		<link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
   </head>
   
   <body ng-app="DashboardApplication" ng-controller="DashboardCtrl as ctrl" ng-cloak> 
      <div id="header-dashboard" class="theme-background-color"
         layout = "row" ng-cloak>
         <md-sidenav md-component-id = "left" class = "md-sidenav-left">
            <!--  menu items -->
            <md-toolbar class="md-hue-2">
                <span flex></span>
                <div layout="column" class="md-toolbar-tools-bottom inset">
                  <i class="material-icons md-36">face</i> Menu
                </div>
              </md-toolbar>
              <md-list>
              <md-item ng-repeat="item in menu">
                <a ng-click="redirect(item.link)">
                  <md-item-content md-ink-ripple layout="row" layout-align="start center">
                    <div class="inset">
                      <i class="material-icons">{{ item.icon }}</i>
                    </div>
                    <div class="">{{item.title}}</div>
                  </md-item-content>
                </a>
              </md-item>
              <md-divider></md-divider>
              
            </md-list>            
            <!--  end of items -->
         </md-sidenav>
         
         <md-content class="theme-background-color theme-color">           
            <md-button ng-click = "openLeftMenu()">
            	<i class="material-icons md-36">menu</i></md-button>
            	<span style="font-size:22px;font-weight:bold;">[[$View->page_title]]</span>
         </md-content>
         <div style="position:absolute;right:10px;top:10px;color:#fff;">Sites <site-selector></site-selector></div>
         
      </div>
      
      <!--  body -->
      <div class="dashboard-body">
	   	<div layout="row" layout-xs="column" style="padding: 0 40px; width:100%;margin-top:2em;">
	      	<div id="page_heading">[[*$page_heading*]]</div>      
	        <div class="message-error messages" ng-if="Data.Messages.error"><i class="material-icons error">error</i><span class="message">{{Data.Messages.error}}</span><i class="material-icons close-x pull-right" ng-click="clearMessage('error')">close</i></div>
	        <div class="message-warning messages" ng-if="Data.Messages.warning"><i class="material-icons warning">warning</i><span class="message">{{Data.Messages.warning}}</span><i class="material-icons close-x pull-right" ng-click="clearMessage('warning')">close</i></div>
	        <div class="message-success messages" ng-if="Data.Messages.success"><i class="material-icons success">done</i><span class="message">{{Data.Messages.success}}</span><i class="material-icons close-x pull-right" ng-click="clearMessage('success')">close</i></div>
		</div>
	   	<div layout="row" layout-xs="column" style="padding: 20px 40px; width:100%;">
   			[[include file="./$partial"]]
		</div>
      </div>

    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-animate.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-aria.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angular_material/1.0.0/angular-material.min.js"></script>
	<script src="//cdn.jsdelivr.net/angular-material-icons/0.4.0/angular-material-icons.min.js"></script>

      <script src = "/assets/js/charts.min.js"></script>
      <script src = "/assets/js/angular-chart.min.js"></script>

      <script src = "/assets/js/leads//dashboard.[[$dashboard]].app.js"></script>
      <script src = "/assets/js/leads/dashboard.[[$dashboard]].controller.js"></script>
      <script src = "/assets/js/leads//dashboard.[[$dashboard]].directives.js"></script>
      <script src = "/assets/js/leads//dashboard.directives.js"></script>
      <script src = "/assets/js/leads//dashboard.helper.js"></script>
      
   </body>
</html>
