app.directive('siteList', function() {
    return {
        restrict: 'E',
        templateUrl: Helper.BaseTemplateUrl + '/site_list.html',
        controller: function($scope, $http) {
            $scope.Data = {Sites: []};
            return $http({
                method: "get",
                url   : Helper.BaseApiUrl + '/Site?format=json',
            }).then(
                function (response) {
                    $scope.Data.Sites = response.data;
                }
            );
        }
    }
})
.directive('templateIframe', ['$sce', function($sce) {
  return {
	    restrict: 'E',
	    template: '<iframe src="{{ trustedUrl }}" frameborder="0" allowfullscreen width="100%" height="100%"></iframe>',
	    link: function(scope) {
	      scope.trustedUrl = $sce.trustAsResourceUrl("/mesquitetx/church");
	    }
	  }
}])
.directive('fileUpload', function (
        ) {
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
        });

;
