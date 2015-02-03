
// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/shop', {
        templateUrl: PageUrl('shop'),
        controller: 'ShopController',
        resolve: {
            info: function(Server) {
                return Server.info().$promise;
            }
        }
    });
}]);

// Module Controller(s)
DevAAC.controller('ShopController', ['$scope', 'Shop', 'Player', 'Server', 'info','Account',
    function($scope, Shop, Player, Server, info, Account) {
    // Quantidade de casas carregadas no sistema
    $scope.account = Account.factory.my();
    $scope.shops = Shop.query(function(){
        $scope.loaded = true;
    });
    $scope.bidOffer = {
        player: false,
        balance: 0,
        canBuy : false
    };
    $scope.statusmsg = {
            type: 'danger',
            msg: ''
        };

    $scope.players = Player.my({}, function(players) {
        try {
            $scope.bidOffer.player = _.find($scope.players, function(p) {
                return (p.level >= $scope.shop.level_need )
            }).name;
        } catch(e) {}
    });

    $scope.buyOffer = function(offer_id) {
        if(!$scope.bidOffer.player){
                generate('warning','<div class="activity-item"><i class="fa fa-thumbs-down text-success"></i><div class="activity">Selecione um personagem para comprar um item.<span>About few seconds ago</span></div></div>');
        }
        // Find player
        for (var i = 0; i < $scope.players.length; i++) {
            if ($scope.players[i].name == $scope.bidOffer.player) {
                var offer = {
                    id: offer_id,
                    player_id: $scope.players[i].id
                };
                var shop = Shop.get({id: offer.id});
                shop.$buy({id: offer.id, player_id: offer.player_id}, function(offer) {
                    generate('success','<div class="activity-item"><i class="fa fa-thumbs-up text-success"></i><div class="activity">Comprado  <a href="#/shop/"> '+ shop.name +' </a> com sucesso.<span>About few seconds ago</span></div></div>');
                    $scope.account.shop_coins -= shop.price;
                }, function(response) {
                    generate('error','<div class="activity-item"><i class="fa fa-thumbs-down text-success"></i><div class="activity"> '+ response.data.message +' </a><span>About few seconds ago</span></div></div>');
                });
            }
        }
    };

}]);


DevAAC.factory('Shop', ['$resource',
    function($resource){
        return $resource(ApiUrl('shops/:id'), {}, {
            get:    { cache: false },
            query: { isArray: true, cache: true },
            buy:    { url: ApiUrl('shops/:id/buy'), method: 'POST' }
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