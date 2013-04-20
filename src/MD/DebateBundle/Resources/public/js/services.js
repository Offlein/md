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
      delay = typeof delay !== 'undefined' ? delay : self.growlDefaultDelay;
      $timeout.cancel(self.timeoutPromise);
      if (angular.isNumber(delay) && delay > 0) {
        self.timeoutPromise = $timeout(function() {
          $rootScope.$broadcast( 'growlService.growlMessage', newMessage );
        }, delay);
      }
    }
  };
}]);

debateAppModule.factory('debateService', ['$rootScope', '$resource', 'growlService', 'restService', function($rootScope, $resource, growlService, restService) {
  return {
    Debate: $resource('api/debate/:debateId', {debateId:'@id'}, {
      update: {method: 'PUT'}
    }),
    load: function(id) {
      var debate = this.Debate.get({debateId:id});
      return debate;
    },
    save: function(debateSave) {
      console.log(debateSave.id);
      if (!(angular.isDefined(debateSave.id))) {
        // No ID already set; create a new debate and don't update
        var newDebate = new this.Debate(debateSave);
        console.log(newDebate);
        newDebate.$save();
        console.log(newDebate);
      }
      else {
        // Updating, not creating new
        growlService.growlDelay("Taking a long time to load...");
        var newDebate = this.Debate.get({debateId:debateSave.id}, function() {
          // Success
          newDebate.name = debateSave.name;
          newDebate.description = debateSave.description;
          delete newDebate.contentions_sorted;
          delete newDebate.editable;
          growlService.growlDelay("Taking a long time to save...");
          newDebate.$update(function() {
            // Update succeeded
            growlService.growl("Debate saved OK!");
          }, function() {
            // Update Failed!
            growlService.growl("Debate changes failed to save!", 0);
          });

        }, function() {
          // Failure to load before saving
          growlService.growl("Unable to connect Debate to server!", 0);
        });
      }
      return newDebate;

      /*
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
        */
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