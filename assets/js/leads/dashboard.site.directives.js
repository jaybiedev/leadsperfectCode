app
.directive('samplesamplesample', ['$sce', function($sce) {
  return {
    restrict: 'E',
    template: '<iframe src="{{ trustedUrl }}" frameborder="0" allowfullscreen></iframe>',
    link: function(scope) {
      scope.trustedUrl = $sce.trustAsResourceUrl("//www.gcichurches.org/yukonok/church");
    }
  }
}])
;
