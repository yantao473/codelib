'use strict';

angular.module('bkmgr', [
    'ngRoute',
    'ui.bootstrap',
    'oc.lazyLoad'
])

.constant('modulesConfig', [{
    name: 'home',
    files: []
},{
    name: 'admin',
    files: []
},{
    name: 'login',
	files:[
		'views/login/login.css',
		'views/login/loginCtrl.js'
	]
}])

.config(['$ocLazyLoadProvider', 'modulesConfig', function($ocLazyLoadProvider, modulesConfig){
    $ocLazyLoadProvider.config({
        debug: true,
        events: true,
        modules: modulesConfig
    });
}])

.config(function($locationProvider, $routeProvider){
    $locationProvider.html5Mode(false);
    $locationProvider.hashPrefix('!');

    $routeProvider
    .when('/login', {
        templateUrl: 'views/login/login.html',
        resolve: {
            deps:["$ocLazyLoad", function($ocLazyLoad){
                return $ocLazyLoad.load('login');
            }]
        }
    })
    .when('/home',{
        controller: function(){},
        templateUrl: 'views/home/home.html'
    })
    .otherwise({
        redirectTo: '/home'
    });
})
.run(['$rootScope', '$location', 'AuthService', function($rootScope, $location, AuthService){
    $rootScope.$on('$routeChangeStart', function(event, next, current){
        if(!AuthService.isAuthenticated()){
            $location.path('/login');
        }
    });
}]);
