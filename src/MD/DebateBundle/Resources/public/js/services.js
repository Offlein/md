'use strict';

/* Services */

debateAppModule.factory('growlService', ['$rootScope', '$timeout', function($rootScope, $timeout) {
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
      $rootScope.$broadcast( 'growlService.growlStateUpdate', newState );
    },
    growl: function(newMessage, timeout) {
      self = this;
      timeout = typeof timeout !== 'undefined' ? timeout : self.growlDefaultTime;
      $timeout.cancel(self.timeoutPromise);
      $rootScope.$broadcast( 'growlService.growlMessage', newMessage );
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
          $rootScope.$broadcast( 'growlService.growlMessage', newMessage );
        }, delay);
      }
    }
  };
}]);

debateAppModule.factory('debateService', ['$rootScope', 'growlService', 'restService', function($rootScope, growlService, restService) {
  return {
    load: function(did) {
      var Debate = $resource('/api/debate/:debateId', {debateId:'@did'});
      var debate = Debate.get({debateId:did}, function() {
        
      });
      /*return restService.loadDebate(did)
        .then(function(response) {
          if (response.status === 200) {
            // Successfully updated.
            return response.data;
          }
        });*/
    },
    save: function(debate) {
      var formMethod = 'POST';
      var apiSubmitPath = 'api/debate';
      var newDebate = {};

      if (angular.isDefined(debate.id)) {
        console.log("puttin'");
        formMethod = 'PUT';
        apiSubmitPath += '/'+debate.id;
      }
      else {
        console.log("not puttin'");
      }

      growlService.growlDelay('...Still saving debate...');
      return restService[formMethod](debate, apiSubmitPath)
        .then(function(response) {
          if (response.status === 200) {
            // Successfully updated.
            growlService.growl('...I saved your debate.');
            newDebate = response.data;
            console.log(newDebate);
            return newDebate;
          }
          else {
            growlService.growl('...Your debate failed to save! Error: '+reason, 10000);
          }
        });
    }
  };
}]);

debateAppModule.factory('restService', ['$rootScope', '$http', 'growlService', function($rootScope, $http, growlService) {
  return {
    POST: function(item, path) {
      var promise = $http({method: 'POST', url: path, data: item});
      promise.then(function(result) {
        return result;
      }, function(reason) {
        return reason;
      });
      return promise;
    },
    PUT: function(item, path) {
      var promise = $http({method: 'POST', url: path, data: item});
      promise.then(function(result) {
        return result;
      }, function(reason) {
        return reason;
      });
      return promise;
    },
    // Load Debate
    loadDebate: function(did) {
      var debate = {};
      var path = 'api/debate/'+did;
      var promise = $http.get(path);
      promise.then(function(result) {
        return result;
      }, function(reason) {
        return false;
      });
      return promise;
    },
    // Contention Form Submissions
    saveContention: function(edit, formAction) {
      var formMethod = 'POST';
      var newContention = {};

      console.log(edit);

      if (angular.isDefined(edit.id)) {
        formMethod = 'PUT';
        formAction += '/'+edit.id;
        newContention.id = edit.id;
      }
      newContention.name  = edit.name;
      newContention.aff   = edit.aff;

      growlService.growlDelay('...Saving contention is taking a weirdly long time...');

      var promise = $http({method: formMethod, url: formAction, data: newContention});
      promise.then(function(result) {
        growlService.growl('...I saved your contention.');
        return result;
      }, function(reason) {
        //alert("Saving Debate failed!");
        growlService.growl('...Your contention failed to save! Error: '+reason, 10000);
        return false;
      });
      return promise;
    },
    // Contention Form Submissions
    saveDebate: function(edit, formAction) {
      var formMethod = 'POST';
      var newDebate = {};

      if (angular.isDefined(edit.id)) {
        formMethod = 'PUT';
        formAction += '/'+edit.id;
        newDebate.id  = edit.id;
      }

      newDebate.name         = edit.name;
      newDebate.description  = edit.description;

      growlService.growlDelay('...Still saving debate...');

      var promise = $http({method: formMethod, url: formAction, data: newDebate});
      promise.then(function(result) {
          growlService.growl('...I saved your debate.');
          return result;
        }, function(reason) {
          growlService.growl('...Your debate failed to save! Error: '+reason, 10000);
        });
      return promise;
    }
  }
}]);