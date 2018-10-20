
app.controller('DashboardCtrl', function($scope, $http, $mdDialog, $mdSidenav) {

    $scope.Data = {
    		User : {},
    		alert : '',
    		Template: {},
    		ContentTags: [],
    };
    
    $http({
        method: "post",
        url   : Helper.BaseUrl + '/dashboard/getDashboardInitAjax'
    }).then(
        function (response) {
            $scope.Data.User = response.data.data.User;
            $scope.Data.Account = response.data.data.Account;
            $scope.Data.Template = response.data.data.Template;
            $scope.Data.ContentTags = response.data.data.ContentTags;
        }
    );

	$scope.openLeftMenu = function() {
		$mdSidenav('left').toggle();
    };
    
    $scope.openMenu = function($mdOpenMenu, ev) {
        originatorEv = ev;
        $mdOpenMenu(ev);
    };

    $scope.redirect = function (slug) {
		window.location.href = Helper.BaseUrl + '/' + slug;
    };
    
/*
    $scope.redirectToSite = function (Site) {
    	if (Site.guid)
    		window.location.href = Helper.BaseUrl + '/dashboard/site/' + Site.guid;
    }

    $scope.siteAction = function(action, id) {
    	if (action == 'download') {
    		window.location.href = Helper.BaseUrl + '/dashboard/downloadsite';
    	}
    	else if (action == 'managesite') {
    		window.location.href = Helper.BaseUrl + '/dashboard/site/' + id;    		
    	}
    	else if (action == 'disable_selected') {
    		//window.location.href = Helper.BaseUrl + '/dashboard/site/' + id;    		
    	}
    }
    
    
    // sample charts
    $scope.labels = ["Link 1", "Link 2", "Link 3"];
    $scope.data = [300, 500, 100];
    
    $scope.labels2 = ['2006', '2007', '2008', '2009', '2010', '2011', '2012'];
    $scope.series2 = ['Series A', 'Series B'];

    $scope.data2 = [
      [65, 59, 80, 81, 56, 55, 40],
      [28, 48, 40, 19, 86, 27, 90]
    ];
    
    $scope.menu = [{
        link: 'dashboard',
        title: 'Dashboard',
        icon: 'dashboard',
        submenu: [
          {
            link: '',
            title: 'd1',
            icon: 'd1'
          }
        ]
      },
      {
          link: 'dashboard',
          title: 'Settings',
          icon: 'settings',
          submenu: [
            {
              link: '',
              title: 'd1',
              icon: 'd1'
            }
          ]
    },
     {
       link: '/logout',
       title: 'Logout',
       icon: 'power_settings_new'
     }];
   */ 
});
