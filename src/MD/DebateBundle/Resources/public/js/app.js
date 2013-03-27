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

debateAppModule.factory('appFrameService', function($rootScope, $timeout) {
  return {
    state: false,
    message: 'Working...',
    timeoutPromise: '',
    growlDefaultTime: 1800,
    growlDefaultDelay: 3000,
    toggleGrowlState: function() {
      this.state = !this.state;
      this.updateGrowlState(this.state);
    },
    updateGrowlState: function(newState) {
      $rootScope.$broadcast( 'appFrameService.growlStateUpdate', newState );
    },
    growl: function(newMessage, timeout) {
      self = this;
      timeout = typeof timeout !== 'undefined' ? timeout : self.growlDefaultTime;
      $timeout.cancel(self.timeoutPromise);
      $rootScope.$broadcast( 'appFrameService.growlMessage', newMessage );
      if (angular.isNumber(timeout) && timeout > 0) {
        self.timeoutPromise = $timeout(function() {
          self.updateGrowlState(false);
        }, timeout);
      }
    },
    growlDelay: function(newMessage, delay) {
      self = this;
      delay = typeof delay !== 'undefined' ? delayt : self.growlDefaultDelay;
      $timeout.cancel(self.timeoutPromise);
      if (angular.isNumber(delay) && delay > 0) {
        self.timeoutPromise = $timeout(function() {
          $rootScope.$broadcast( 'appFrameService.growlMessage', newMessage );
        }, delay);
      }
    }
  };
});
debateAppModule.$inject = ['$rootScope', '$timeout'];


/*
// Declare app level module which depends on filters, and services
angular.module('debateApp', ['debateApp.filters', 'debateApp.services', 'debateApp.directives']).
  config(['$interpolateProvider', function ($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
  }]).
  config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/view1', {templateUrl: 'partials/partial1.html', controller: MyCtrl1});
    $routeProvider.when('/view2', {templateUrl: 'partials/partial2.html', controller: MyCtrl2});
    $routeProvider.otherwise({redirectTo: '/view1'});
  }]);
*/