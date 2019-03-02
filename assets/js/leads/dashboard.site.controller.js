
app.controller('DashboardCtrl', function($scope, $http, $mdDialog, $mdSidenav, $location) {

    $scope.Data = {
    		User : {},
    		alert : '',
    		Template: {},
    		navigation: 'graphs',
    		guid: '',
    		Charts : getChartData(),
    		Messages : {error:null, warning:null, success:null}
    };
    
    $scope.Data.guid = angular.element(document.getElementById("dashboard-site")).attr('guid');

    $scope.tinymceBasicOptions = {
		relative_urls : false,
	    plugins: 'textcolor anchor code link hr table image',
	    menubar: "edit view insert format tools",
	    toolbar: "undo redo styleselect bold italic forecolor backcolor| image link  | code",
	    //images_upload_url: 'postAcceptor.php',
	    /* we override default upload handler to simulate successful upload*/
	    images_upload_handler: function (blobInfo, success, failure) {
	    	var xhr, formData;

	    	xhr = new XMLHttpRequest();
	    	xhr.withCredentials = false;
	    	xhr.open('POST', Helper.BaseUrl + '/microservices/image');

	    	xhr.onload = function() {
	    	  var json;

	    	  if (xhr.status < 200 || xhr.status >= 300) {
	    		failure('HTTP Error: ' + xhr.status + " " + xhr.statusText);
	    		return;
	    	  }

	    	  json = JSON.parse(xhr.responseText);

	    	  if (!json || typeof json.data.location != 'string') {
	    		failure('Invalid JSON: ' + xhr.responseText);
	    		return;
	    	  }

	    	  success(json.data.location);
	    	};

	    	formData = new FormData();
	    	formData.append('file', blobInfo.blob(), blobInfo.filename());
	    	formData.append('site', $scope.Data.guid);

	    	xhr.send(formData);	  
	    },
	    automatic_uploads: false
	};

    $http({
        method: "get",
        url   : Helper.BaseUrl + '/microservices/site?guid=' + $scope.Data.guid
    }).then(
        function (response) {
            $scope.Data.User = response.data.data.User;
            $scope.Data.Site = response.data.data.Site;
            $scope.Data.SiteData = response.data.data.SiteData;
            $scope.Data.Template = response.data.data.Template;
        }
    );

	$scope.openLeftMenu = function() {
		$mdSidenav('left').toggle();
    };
    
    $scope.openMenu = function($mdOpenMenu, ev) {
        originatorEv = ev;
        $mdOpenMenu(ev);
    };

    $scope.initMessages = function() {
    	$scope.Data.Messages = {error:null, warning:null, success:null};
    }
    
    $scope.clearMessage = function(index) {
    	$scope.Data.Messages[index] = '';
    }
    
    $scope.redirect = function (link) {
		var url = Helper.BaseUrl + '/dashboard/site/' + $scope.Data.guid + '/' + link;
    	if (link == 'logout') {
    		url = Helper.BaseUrl + '/logout';
    	}
    	
    	window.location.href = url;
    };
    
    $scope.redirectToSite = function (Site) {
    	if (Site.guid)
    		window.location.href = Helper.BaseUrl + '/dashboard/site/' + Site.guid;
    }

    $scope.siteAction = function(action, id) {
    	if (action == 'download') {
    		window.location.href = Helper.BaseUrl + '/dashboard/downloadsite';
    	}
    }
    
    $scope.deleteSiteInfo = function(ev) {
    	var field = angular.element(ev.currentTarget).attr('data-field');
    	var datasource = angular.element(ev.currentTarget).attr('data-source');
    	var confirm = $mdDialog.confirm()
        .title('Are you sure you want to delete ' +  field + '?')
        .textContent('Once this value is deleted, the default value will be used.')
        .targetEvent(ev)
        .ok('Yes')
        .cancel('No');

		  $mdDialog.show(confirm).then(function() {
			  $http.delete('/dashboard/site/' + $scope.Data.guid + '/settings',   
					  { params: {field: field}}
			    ).then(
			        function (response) {
			            if (response.data.success) {
			            	$scope.Data[datasource][field] =  null;				            		
			            }
			        }
			    );
		  }, function() {
			 // no 
		  });
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
