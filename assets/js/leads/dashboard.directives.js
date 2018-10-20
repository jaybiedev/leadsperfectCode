app
.directive('userProfile', function() {
	  return {
	        templateUrl: Helper.BaseTemplateUrl + '/editProfileView.html',
	        controller : function ($scope, $http) {
	        	$http({
	                method: "get",
	                url   : Helper.BaseUrl + '/microservices/user'
	            }).then(
	                function (response) {
	                    $scope.Data.User = response.data.data.User;	  
	                }
	            );
	        	
	        	$scope.save = function() {
	        		if (!validate()) {
	        			return false;
	        		}
	        		
	        		debugger;
	        		if (typeof $scope.initMessages == 'function') {
	        			$scope.initMessages();
	        		}
	        		
	        		
	        		$http({
		                method: "post",
		                url  : Helper.BaseUrl + '/microservices/user',
		                data : $scope.Data.User 
		            }).then(
		                function (response) {
		                    $scope.Data.User = response.data.data.User;	  
		                    
		                    if ($scope.Data.Messages) {
		                    	$scope.Data.Messages['success'] = 'Successfully updated user profile';
		                    }
		                }
		            );
	        	}
	        	
	        },
	        link: function (scope, element) {
	        	// can add single or multiple flag.
	        	// default to single file upload
	        	validate = function() {
	        		if (scope.Data.User['password'] && scope.Data.User['password'] != scope.Data.User['password_confirm']) {
	        			if (scope.Data.Messages) {
	                    	scope.Data.Messages['error'] = 'Password does not match.';
	                    }
	        			return false;
	        		}
	        		
	        		if (scope.Data.User['password'] && scope.Data.User['password'].length < 8) {
	        			if (scope.Data.Messages) {
	                    	scope.Data.Messages['error'] = 'Password too short. Please use minimum of 8 characters.';
	                    }
	        			return false;
	        		}
	        		
	        		if (!scope.Data.User['email']) {
	        			if (scope.Data.Messages) {
	                    	scope.Data.Messages['error'] = 'Email or username can not be blank.';
	                    }
	        			return false;	        			
	        		}
	        		
	        		return true;
	        	}
	        }
	    };
})
.directive('fileUpload', function () {
    return {
        templateUrl: Helper.BaseTemplateUrl + '/uploadFileView.html',
        controller : function ($scope, $http) {
            
        	$scope.uploadFile = function(){
                var formData = new FormData();

                formData.append('file', document.getElementById('uploadFileInput').files[0]);
                // Add code to submit the formData  
                
                // formData.get('file')
                return $http({
                    method: "post",
                    url   : Helper.BaseUrl + '/dashboard/site/upload?format=json',
                    date  : formData
                }).then(
                    function (response) {
                        $scope.Data.Sites = response.data;
                    }
                );
            };                	
        },
        link: function (scope, element) {
        	// can add single or multiple flag.
        	// default to single file upload

            scope.fileName = 'Choose a file...';

            element.bind('change', function () {
                scope.$apply(function () {
                    scope.fileName = document.getElementById('uploadFileInput').files[0].name;
                });
            });
        }
    };
})

.directive('iframeDirective', ['$sce', function($sce) {
  return {
    restrict: 'E',
    template: '<iframe src="{{ trustedUrl }}" frameborder="0" allowfullscreen></iframe>',
    link: function postLink(scope, element, attrs) {
    	var url = null;
    	if (attrs['url'])
    		url = attrs['url'];
    	if (!url && attrs['siteSlug']) 
    		url = Helper.BaseUrl + '/' + attrs['siteSlug'];
    	
      scope.trustedUrl = $sce.trustAsResourceUrl(url);
    }
  }
}])

;
