'use strict';

/* Filters */

angular.module('debateApp.filters', []).
  filter('affVal', function() {
    return function(input) {
      console.log(input);
      return input ? '\u2713' : '\u2718';
    };
  });

