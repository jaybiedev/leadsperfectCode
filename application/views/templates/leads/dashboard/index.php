<html lang = "en">
   <head>
   	<meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="/css/dashboard.css" rel="stylesheet">
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <link href="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.0/angular-material.min.css" rel="stylesheet" >
 
      <style>
        md-card md-card-title+md-card-content {
            padding-top: 16px;
        }
        
        md-card .md-menu {
            padding-top:0;
        }
      </style>
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
              
              <!-- 
              <md-subheader>Management</md-subheader>
              <md-item>
                <a>
                  <md-item-content md-ink-ripple layout="row" layout-align="start center">
                    <div class="inset">
                      <ng-md-icon icon="group"></ng-md-icon>
                    </div>
                    <div class="inset">Group
                    </div>
                  </md-item-content>
                </a>
              </md-item>
               -->
            </md-list>            
            <!--  end of items -->
         </md-sidenav>
         
         <md-content class="theme-color">           
            <md-button ng-click = "openLeftMenu()">
            	<i class="material-icons md-36">menu</i></md-button>
            	<span style="font-size:22px;font-weight:bold;">Leads Perfect</span>
         </md-content>
         
      </div>
      
      <!--  body -->
      <div>
	   	<div layout="row" layout-xs="column">
	   		<div layout="column" flex-50>
	  			<div flex><?php include_once("_template.php");?></div>
  				<div flex>
  					<!--<div file-upload></div> -->            	
           			<site-list></site-list>   
  				</div>
	  		</div>  			
			<div flex-10><?php include_once("_stats.php");?></div>
		</div>
      </div>

    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-animate.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-aria.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angular_material/1.0.0/angular-material.min.js"></script>
	<script src="//cdn.jsdelivr.net/angular-material-icons/0.4.0/angular-material-icons.min.js"></script>

      <script src = "/assets/js/charts.min.js"></script>
      <script src = "/assets/js/angular-chart.min.js"></script>

      <script src = "/assets/js/leads/dashboard.app.js"></script>
      <script src = "/assets/js/leads/dashboard.controller.js"></script>
      <script src = "/assets/js/leads/dashboard.directives.js"></script>
      <script src = "/assets/js/leads/dashboard.helper.js"></script>
      
   </body>
</html>