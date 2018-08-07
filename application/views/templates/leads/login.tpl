<html lang="en" >
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Angular Material style sheet -->
  <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/angular_material/1.1.8/angular-material.min.css">
  <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
  form {
  	margin: 0 auto !important;
	width: 380px;
    margin: 4em auto;
    padding: 2em !important;
    background: #fafafa;
    border: 1px solid #ebebeb;
    box-shadow: rgba(0,0,0,0.14902) 0px 1px 1px 0px, rgba(0,0,0,0.09804) 0px 1px 2px 0px;
  }
  form label {
  	padding-left: 40px !important;
  }
  form input {
  	margin-left: 40px;
  }
  #title-header {
    text-align: center;
    margin-top: 4em;
  }
  #title-header h1{
  	color: #636363;
  	font-weight: 300;
  	font-size: 2em;
  }
  </style>
</head>
<body ng-app="LoginApp" ng-cloak>
<div ng-controller="LoginCtrl" layout="column" layout-padding ng-cloak>
  <br/>
  <div id="title-header">
  	<h1>Login</h1>
  </div>
  <form action="[[$Helper->getUrl()->getLoginUrl()]]" method="post" id="loginFrm">
  <md-content class="md-no-momentum">
		<md-input-container class="md-icon-float md-block">
		  <!-- Use floating label instead of placeholder -->
		  <label>Username / Email</label>
		  <i class="material-icons">person_outline</i>
		  <input ng-model="Data.username" name="username" type="text" ng-required="true">
		</md-input-container>
		
		<md-input-container md-no-float class="md-block">
		  <label>Password</label>
		  <i class="material-icons">lock_open</i>
		  <input ng-model="Data.password" type="password" name="password" ng-required="true">
		</md-input-container>
		
        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
      		<md-button class="md-primary md-hue-1" ng-disabled="true">Create</md-button>
      			<div flex></div>
      		<md-button class="md-raised md-primary theme-background-color" ng-click="login()">Login</md-button>
    	</section>
  </md-content>
	</form>
</div>
  
  <!-- Angular Material requires Angular.js Libraries -->
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-animate.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-aria.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-messages.min.js"></script>

  <!-- Angular Material Library -->
  <script src="//ajax.googleapis.com/ajax/libs/angular_material/1.1.8/angular-material.min.js"></script>
  
  <!-- Your application bootstrap  -->
  <script type="text/javascript">    
    angular.module('LoginApp', ['ngMaterial', 'ngMessages'])
  		.controller('LoginCtrl', function($scope, $location, $http) {
  		
		    $scope.Data = {
		      username: '',
		      password: '',
		      destination: '',
		      is_json: true
		    };
		    
		    var urlParams = new URLSearchParams(window.location.search);
			if (urlParams.has('destination'))
				$scope.Data.destination = urlParams.get('destination');
				
			$scope.login = function() {
				if ($scope.Data.username == '' || $scope.Data.password == '')
					return;
			
					document.getElementById('loginFrm').submit();
			}
			
			$scope.login_xhttp = function() {
				if ($scope.Data.username == '' || $scope.Data.password == '')
					return;
					
				$http({
			        method : "POST",
			        url : "/login",
			        params: $scope.Data
			    }).then(function mySuccess(response) {
					// window.location.href = '/dashboard';
			    }, function myError(response) {
			        alert('Unable to login ' + response.statusText);
			    });
			}
	  });
  </script>
  
</body>
</html>