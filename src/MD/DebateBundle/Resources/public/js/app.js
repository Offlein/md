'use strict';


// Declare app level module which depends on filters, and services
var debateAppModule = angular.module('debateApp', ['ui', 'debateApp.filters'])
  .config(['$interpolateProvider', function ($interpolateProvider) {
    $interpolateProvider.startSymbol('{[{');
    $interpolateProvider.endSymbol('}]}');
  }])
  .config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/debate/:debateId', {templateUrl: 'template/debate', controller: DebateCtrl});
    $routeProvider.otherwise({redirectTo: '/debate/1'});
  }]);