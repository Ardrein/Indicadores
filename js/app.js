//declaracion del modulo indicatorsApp
var indicatorsApp = angular.module('indicatorsApp', ['ngRoute']);

//directiva
indicatorsApp.directive('lista', function($timeout){
  return {
    link: function ($scope, $element, $attrs) {
     if ($scope.$last){
       var elements = document.getElementsByClassName("variable");
       var maxHeight = 0;


       $timeout(function(){
        for (var i = 0; i < elements.length; i++) {
         var elementHeight = elements[i].offsetHeight;

         if (elements[i].offsetHeight > maxHeight) {
           maxHeight = elementHeight;
         }
       }  

       for (var i = 0; i < elements.length; i++) {
        elements[i].style.height = maxHeight+'px';
        elements[i].style['min-height'] = 'auto';
      }

    });
     }
   }
 }
});

//filtro
indicatorsApp.filter('formato', ['$filter', function ($filter) {
  return function (input, decimals, type) {
    if(type == 'Percentage'){
      return $filter('number')(input * 100, decimals) + '%';
    }else{
      return $filter('number')(input, decimals);
    }
    
  };
}]);

//routing
indicatorsApp.config(function($routeProvider, $locationProvider){
  $routeProvider
  .when('/indicadores',{
    templateUrl:'indicadores.html'
  }) 
  .when('/',{
    templateUrl:'home.php'
  })    
  .otherwise({
    redirectTo: '/'
  });

});

//Controlador 

indicatorsApp.controller('VariablesController',['$scope','$http','$location',function($scope,$http,$location){
  var viewModel = this;
  viewModel.variables={};
  viewModel.indicadores={};


  $scope.initLists = function(variables, indicadores){
    viewModel.variables = variables;
    viewModel.indicadores = indicadores;
  }

  $scope.asignarVariables = function(){
    $http({
      method:'POST',
      url: 'indicadores.php',

      data: {'indicadores': viewModel.indicadores, 'variables':viewModel.variables}
    }).then(function successCallback(response){
      if(angular.fromJson(response).status == '200'){
       viewModel.indicadores = angular.fromJson(response).data;
       //console.log(response.data);
       $location.path('indicadores');
     }
   });

  };



}]);




