
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
    
    
});
