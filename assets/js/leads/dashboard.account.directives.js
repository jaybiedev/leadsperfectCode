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

;
