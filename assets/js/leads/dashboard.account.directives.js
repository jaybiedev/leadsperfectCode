app
.directive('siteList', function() {
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

.directive('accountContactForm', function() {
    return {
        restrict: 'E',
        templateUrl: Helper.BaseTemplateUrl + '/account_contact_form.html',
        controller: function($scope, $http) {
            $scope.Data = {Sites: []};
            return $http({
                method: "get",
                url   : Helper.BaseApiUrl + '/Account/contactform?format=json',
            }).then(
                function (response) {
                    $scope.Data.ContactForm = response.data;
                }
            );
        }
    }
})

;
