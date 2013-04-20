'use strict';

/* Controllers */

function appFrameCtrl($scope, growlService, $timeout) {
  $scope.growlState = false;
  $scope.growlMessage = '...';
  $scope.$on( 'growlService.growlStateUpdate', function( event, newState ) {
    $scope.growlState = newState;
  });
  $scope.$on( 'growlService.growlMessage', function( event, newMessage ) {
    $scope.growlMessage = newMessage;
    $scope.growlState = true;
  });
}
appFrameCtrl.$inject = ['$scope', 'growlService', '$timeout'];

function DebateCtrl($scope, debateService, $route, $http, $routeParams, $location) {
  $scope.$route = $route;
  $scope.$location = $location;
  $scope.$routeParams = $routeParams;

  // Initialization
  $scope.editing = false;
  $scope.memory = {}; // An object to hold unedited Debate objects while editing
  $scope.edit = {};

  // Handle Routes
  $scope.debate = debateService.load($scope.$routeParams.debateId);
  console.log($scope.debate);
  $scope.editFormPath = 'form/debate/1';

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
    console.log('togglin\'');
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
    var newDebate = debateService.save($scope.edit);
    $scope.debate = $scope.edit;
    $scope.editing = false;
  };
}
DebateCtrl.$inject = ['$scope', 'debateService', '$route', '$http', '$routeParams', '$location'];

function contentionGroupCtrl($scope, restService) {
  // Initialization
  var $parent = $scope.$parent;
  console.log($parent.debate);

  console.log($scope);
  $scope.apiSubmitPath = 'api/debate/'+$scope.debate.id+'/contention';
  $scope.editing = false;
  $scope.memory = {}; // An object to hold unedited Contention objects while editing
  $scope.edit = {};

  $scope.getPath = function(did, cid, aff) {
    var path;
    if (!(angular.isNumber(did))) {
      return false;
    }
    if (!(angular.isNumber(cid))) {
      cid = 'new';
    }
    path = 'form/contention/'+did+'/'+cid;
    if (cid == 'new') {
      path += '/'+aff
    }
    return path;
  };
  var affToInt = function(affString) {
    if (affString === 'aff') {
      return 1;
    }
    else if (affString === 'neg') {
      return 0;
    }
  };

  // Toggle New Contention Form
  $scope.toggleEditForm = function() {
    var ct;
    $scope.editing = !$scope.editing;
    if ($scope.editing == true) {
      if ($scope.contentionType == 'aff') {
        ct = 1;
      }
      else {
        ct = 0;
      }
      console.log($scope.edit);
      $scope.edit.aff = ct;
      console.log($scope.contentionGroup);
      console.log($scope.contentionType);
      console.log($scope.edit.aff);

      // We are now editing
      $scope.edit.name = '';
    }
  };

  $scope.formContentionSubmit = function() {
    restService.saveContention($scope.edit, $scope.apiSubmitPath)
      .then(function(response) {
        if (response.status === 200) {
          // Successfully created.
          $scope.contentionGroup.unshift(response.data);
          $scope.toggleEditForm();
        }
      });
  };
}
contentionGroupCtrl.$inject = ['$scope', 'restService'];

function contentionCtrl($scope, restService) {
  // Initialization
  $scope.editing = false;
  $scope.memory = {};
  $scope.edit = {};

  // Toggle Contention New Form
  $scope.toggleEditForm = function() {
    console.log("Wooba");
    $scope.editing = !$scope.editing;
    if ($scope.editing == true) {
      $scope.memory = angular.copy($scope.contention);
      $scope.edit = angular.copy($scope.contention);
    }
    else {
      $scope.contention = angular.copy($scope.memory);
    }
  };

  $scope.formContentionSubmit = function() {
    restService.saveContention($scope.edit, $scope.apiSubmitPath)
      .then(function(response) {
        if (response.status === 200) {
          // Successfully updated.
          $scope.contention.id = response.data.id;
          $scope.contention.name = response.data.name;
          $scope.editing = false;
        }
      });
  };
}
contentionCtrl.$inject = ['$scope', 'restService'];

function pointCtrl($scope, restService) {

}
pointCtrl.$inject = ['$scope', 'restService', '$route', '$http', '$routeParams', '$location'];
