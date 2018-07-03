var KEYB_ENTER = 13;
var KEYB_VOID = 1; // alt+v
var KEYB_ARROW_DOWN = 40;
var KEYB_ARROW_UP = 38;
var KEYB_SUSPEND = 1; // alt+s
var KEYB_PLU = 1; // alt+p
var KEYB_FINISH = 1; // F10
var KEYB_ESCAPE = 27;


app.directive('posKeyMap', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            console.log(event.which);
            if(event.which === KEYB_ENTER) {
                scope.$apply(function (){
                    scope.$eval(attrs.posKeyMap);
                });

                event.preventDefault();
            }
        });
    };
});

app.directive('loadIndicator', function ($compile) {
    return {
        restrict: 'E',
        link: function (scope, element, attrs) {
            var html ='<div class="spin-loader " ng-show="' + attrs.show + '"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> ' + attrs.message +  '</div>';
            var e =$compile(html)(scope);
            element.replaceWith(e);
        }
    };
});


app.directive('connectionStatus', function ($compile) {
    return {
        restrict: 'E',
        link: function (scope, element, attrs) {
            var status = {message: 'Online', glyph:'glyphicon glyphicon-ok-circle', color:'text-green'};
            if (attrs.status == 'OFFLINE')
                var status = {message: 'Offline',  glyph: 'glyphicon glyphicon-off', color:'text-red'};

            var html ='<div class="lg ' + status.color + '"><span class="' + status.glyph + '"></span>&nbsp;' + status.message + '</div>';
            var e =$compile(html)(scope);
            element.replaceWith(e);
        }
    };
});
