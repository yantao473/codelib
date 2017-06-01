angular.module('bkmgr')
.factory('AuthService', ['$http', function($http) {
    var authService = {};

    authService.login = function(loginobj) {
        return $http.post('/dologin', loginobj).then(function(response) {
            var info = {
                user: loginobj.name
            };

            if(loginobj.remberme){
                sessionStorage.removeItem('userinfo');
                localStorage.setItem('userinfo', JSON.stringify(info));
            }else{
                localStorage.removeItem('userinfo');
                sessionStorage.setItem('userinfo', JSON.stringify(info));
            }


            return loginobj.name;
        });
    };

    authService.isAuthenticated = function(){
        return !!(localStorage.getItem('userinfo') || sessionStorage.getItem('userinfo'));
    };

    authService.logout = function(){

    };

    return authService;
}]);
