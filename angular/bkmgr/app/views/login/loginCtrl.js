angular.module('bkmgr.controller', [])
.controller('loginCtrl', ['$scope', '$location', 'AuthService', function($scope, $location, AuthService){
    var m = $scope;

    m.login = {
        name: '',
        passwd: '',
        remberme: false
    };

    m.doLogin = function(){
        AuthService.login($scope.login).then(function(response){
            $location.path('/home');
        },function(){
            console.log('login failed');
        });
    };

}]);
