
// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {

    $routeProvider.when('/items', {
        templateUrl: PageUrl('item/items')
    });

    $routeProvider.when('/items/:id', {
        templateUrl: PageUrl('item/items'),
        controller: 'ItemSearchController',
        resolve: {
            info: function(Server) {
                return Server.info().$promise;
            },
            item: function(Item, $route) {
                return Item.get({id: $route.current.params.id}).$promise;
            }
        }
    });

     $routeProvider.when('/items/:id/info', {
        templateUrl: PageUrl('item/item'),
        controller: 'ItemController',
        resolve: {
            info: function(Server) {
                return Server.info().$promise;
            },
            item: function(Item, $route) {
                return Item.getItemLoot({id: $route.current.params.id}).$promise;
            }
        }
    });


}]);
// Module ItemController
DevAAC.controller('ItemController', ['$scope', 'Item', 'Player', 'Server', 'info','Account','$route','item',
    function($scope, Item, Player, Server, info, Account,$route,item) {
    $scope.item = item;

}]);

// Module ItemSearchController
DevAAC.controller('ItemSearchController', ['$scope', 'Item', 'Player', 'Server', 'info','Account','$route',
function($scope, Item, Player, Server, info, Account,$route) {
$scope.items = Item.getWithName({q:$route.current.params.id});


}]);



// Module Controller(s)
DevAAC.controller('ItemsController', ['$scope', 'Item', 'Player', 'Server', 'info','Account',
    function($scope, Item, Player, Server, info, Account) {

}]);


DevAAC.factory('Item', ['$resource',
    function($resource){
        return $resource(ApiUrl('items/:id'), {}, {
            get:    { cache: false },
            getWithName: { params: {q: 'sword'}, isArray: true, cache: false },
            getItemLoot: { params: {loot:1 }, isArray: false, cache: false },
            query: { isArray: true, cache: true }
        });
    }
]);


function generate(type, text) {
    var n = noty({
        text        : text,
        type        : type,
        dismissQueue: true,
        layout      : 'topLeft',
        closeWith   : ['click'],
        theme       : 'relax',
        maxVisible  : 4,
        timeout: 5000,
        animation   : {
            open  : 'animated bounceInLeft',
            close : 'animated bounceOutLeft',
            easing: 'swing',
            speed : 500
        }
    });
}

DevAAC.filter('capitalize', function() {
    return function(input, all) {
      return (!!input) ? input.replace(/([^\W_]+[^\s-]*) */g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();}) : '';
    }
  });