'use strict';

/* Controllers */

function appFrameCtrl($scope, appFrameService, $timeout) {
  $scope.growlState = false;
  $scope.growlMessage = '...';
  $scope.$on( 'appFrameService.growlStateUpdate', function( event, newState ) {
    $scope.growlState = newState;
  });
  $scope.$on( 'appFrameService.growlMessage', function( event, newMessage ) {
    $scope.growlMessage = newMessage;
    $scope.growlState = true;
  });
}
appFrameCtrl.$inject = ['$scope', 'appFrameService', '$timeout'];

function DebateCtrl($scope, appFrameService, $route, $http, $routeParams, $location) {
  $scope.$route = $route;
  $scope.$location = $location;
  $scope.$routeParams = $routeParams;

  // Initialization
  $scope.apiPathSubmit = 'api/debate';
  $scope.editing = false;
  $scope.memory = {}; // An object to hold unedited Debate objects while editing

  // Handle Routes
  $http.get('api/debate/'+$scope.$routeParams.debateId).success(function(data) {
      $scope.debate = data;
      $scope.editFormPath = 'form/debate/1';
  });

  $scope.getPath = function(did) {
    var path;
    if (!(angular.isNumber(did))) {
      return false;
    }
    path = 'form/debate/'+did;
    return path;
  }

  // Filter contentions by Affirmative Value
  $scope.filterIsAff = function(items, isAff) {
    var result = {};
    angular.forEach(items, function(value, key) {
      if (value.aff === isAff) {
        result[key] = value;
      }
    });
    return result;
  };

  // Filter to create formMethod
  $scope.formMethod = function(id) {
    var result = ''
    if (angular.isNumber(id)) {
      result = 'PUT';
    }
    return result;
  };

  // Toggle Debate Edit Form
  $scope.toggleEditForm = function() {
    $scope.editing = !$scope.editing;
    if ($scope.editing == true) {
      // We are now editing
      $scope.memory = angular.copy($scope.debate);
      $scope.edit = angular.copy($scope.debate);
    }
    else {
      // We are stopping editing
      $scope.edit = angular.copy($scope.memory);
    }
    console.log($scope.memory.name);
  };

  // Handle Form Submissions
  $scope.formDebateSubmit = function() {
    var formMethod = 'POST';
    var formAction = $scope.apiPathSubmit;
    var newDebate = {};

    if (angular.isNumber(this.edit.id)) {
      formMethod = 'PUT';
      formAction += '/'+this.edit.id;
      newDebate.id  = this.edit.id;
    }

    newDebate.name         = this.edit.name;
    newDebate.description  = this.edit.description;

    appFrameService.growlDelay('...Still saving debate...');

    $http({method: formMethod, url: formAction, data: newDebate}).
      success(function(data, status) {
        appFrameService.growl('...I saved your debate.');
        $scope.memory = angular.copy(data);
        $scope.debate = angular.copy(data);
        $scope.toggleEditForm();
        $scope.saving = false;
      }).
      error(function(data, status) {
        //alert("Saving Debate failed!");
        appFrameService.growl('...Your debate failed to save! Please try again.', 10000);
      });

  };
}
DebateCtrl.$inject = ['$scope', 'appFrameService', '$route', '$http', '$routeParams', '$location'];

function contentionGroupCtrl($scope, appFrameService, $http) {
  // Initialization
  $scope.apiPathSubmit = 'api/debate/'+$scope.debate.id+'/contention';
  $scope.editing = false;
  $scope.memory = {}; // An object to hold unedited Contention objects while editing
  $scope.edit = {};

  $scope.getPath = function(cid, aff) {
    var path;
    if (!(angular.isNumber(cid))) {
      return false;
    }
    path = 'form/contention/'+cid+'/new/'+aff;
    return path;
  }
  $scope.affToInt = function(affString) {
    if (affString === 'aff') {
      return 1;
    }
    else if (affString === 'neg') {
      return 0;
    }
  }

  // Toggle Contention New Form
  $scope.toggleEditForm = function() {
    $scope.editing = !$scope.editing;
    if ($scope.editing == true) {
      // We are now editing
      $scope.edit.name = '';
      $scope.edit.aff = $scope.affToInt($scope.contentionType);
      console.log($scope.affToInt($scope.contentionType));
    }
    else {
      // We are stopping editing
      $scope.edit.name = angular.copy($scope.memory);
    }
    console.log($scope.memory);
  };

  // Handle Form Submissions
  $scope.formContentionSubmit = function() {
    var formMethod = 'POST';
    var formAction = $scope.apiPathSubmit;
    var newContention = {};

    console.log(angular.isNumber(this.edit.id));

    if (angular.isNumber(this.edit.id)) {
      formMethod = 'PUT';
      formAction += '/'+this.edit.id;
      newContention.id = this.edit.id;
    }
    newContention.name  = this.edit.name;
    newContention.aff   = this.edit.aff;
    console.log($scope.debate);

    appFrameService.growlDelay('...Still saving contention...');

    $http({method: formMethod, url: formAction, data: newContention}).
      success(function(data, status) {
        appFrameService.growl('...I saved your contention.');
        $scope.memory = angular.copy(data);
        var contentionPush = data;
        delete contentionPush.debate;
        $scope.contentionGroup.push(contentionPush);
        $scope.toggleEditForm();
        $scope.saving = false;
      }).
      error(function(data, status) {
        //alert("Saving Debate failed!");
        appFrameService.growl('...Your contention failed to save! Please try again.', 10000);
      });

  };
}
contentionGroupCtrl.$inject = ['$scope', 'appFrameService', '$http'];

function contentionCtrl($scope, appFrameService) {

}
contentionCtrl.$inject = ['$scope', 'appFrameService'];

