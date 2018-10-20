
app.controller('DashboardCtrl', function($scope, $http, $mdDialog, $mdSidenav) {

    $scope.Data = {
    		User : {},
    		alert : '',
    		Template: {},
    		ContentTags: [],
    		guid : '',
    		Charts : getChartData(),
    		Messages : {error:null, warning:null, success:null}
    };
    
    $scope.Data.guid = angular.element(document.getElementById("dashboard-account")).attr('guid');

    $http({
        method: "post",
        url   : Helper.BaseUrl + '/microservices/account'
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

    $scope.redirect = function (link) {
    	var url = Helper.BaseUrl + '/dashboard/account/' + $scope.Data.Account.guid + '/' + link;
    	if (link == 'logout')
    		url = Helper.BaseUrl + '/logout';
    	
		window.location.href = url;
    };
    
    $scope.redirectToSite = function (Site) {
    	if (Site.guid)
    		window.location.href = Helper.BaseUrl + '/dashboard/site/' + Site.guid;
    }

    $scope.siteAction = function(action, id) {
    	if (action == 'download') {
    		window.location.href = Helper.BaseUrl + '/dashboard/account/' + $scope.Data.Account.guid + '/downloadsites';
    	}
    	else if (action == 'managesite') {
    		window.location.href = Helper.BaseUrl + '/dashboard/site/' + id;    		
    	}
    	else if (action == 'disable_selected') {
    		//window.location.href = Helper.BaseUrl + '/dashboard/site/' + id;    		
    	}
    }

    
    $scope.menu = [
        {
            link: 'settings',
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
          link: 'sites',
          title: 'Manage Sites',
          icon: 'view_list',
          submenu: [
            {
              link: '',
              title: 'd1',
              icon: 'd1'
            }
          ]
    },
      {
          link: 'template',
          title: 'Template Preview',
          icon: 'format_shapes',
          submenu: [
            {
              link: '',
              title: 'd1',
              icon: 'd1'
            }
          ]
    },
    {
        link: 'charts',
        title: 'Charts',
        icon: 'show_chart',
        submenu: [
          {
            link: '',
            title: 'd1',
            icon: 'd1'
          }
        ]
      },
    {
        link: 'profile',
        title: 'Profile',
        icon: 'person',
        submenu: [
          {
            link: '',
            title: 'd1',
            icon: 'd1'
          }
        ]
    },
       {
         link: 'logout',
         title: 'Logout',
         icon: 'power_settings_new'
       }];
    
    

    function getChartData() {
    	var charts = {
			Views: {
				options: {
    			    scales: {
      			      yAxes: [
      			        {
      			          id: 'y-axis-1',
      			          type: 'linear',
      			          display: true,
      			          position: 'left'
      			        },
      			        {
      			          id: 'y-axis-2',
      			          type: 'linear',
      			          display: true,
      			          position: 'right'
      			        }
      			      ]
      			    }
      			  },
				labels: ["January", "February", "March", "April", "May", "June", "July"],
				series: ['Series A', 'Series B'],
				data: [
				    [65, 59, 80, 81, 56, 55, 40],
				    [28, 48, 40, 19, 86, 27, 90]
				  ],
				datasetOverride : [{ yAxisID: 'y-axis-1' }, { yAxisID: 'y-axis-2' }],
				onClick : function (points, evt) {
				    console.log(points, evt);
				  }
			},
			Actions : {
				labels:['2006', '2007', '2008', '2009', '2010', '2011', '2012'],
				series:['Series A', 'Series B'],
				data: [
				      [65, 59, 80, 81, 56, 55, 40],
				      [28, 48, 40, 19, 86, 27, 90]
				    ]    				
			}
		}
    	return charts;
    }
    
});
