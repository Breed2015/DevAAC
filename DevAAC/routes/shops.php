<?php
/**
 * DevAAC
 *
 * Automatic Account Creator by developers.pl for TFS 1.0
 *
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package    DevAAC - Shop
 * @author     Bruno Novais <cardososp@gmail.com>
 * @copyright  2015 Bruno Novais
 * @license    http://opensource.org/licenses/MIT MIT
 * @version    development
 * @link       https://github.com/novasdream/DevAAC
 */

use DevAAC\Models\Shop;
use DevAAC\Models\Account;
use DevAAC\Models\Player;
use DevAAC\Models\ShopHistory;

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/houses",
 *  @SWG\Api(
 *    path="/houses",
 *    description="Operations on houses",
 *    @SWG\Operation(
 *      summary="Get all houses",
 *      notes="",
 *      method="GET",
 *      type="House",
 *      nickname="getShopOffers"
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/shops', function() use($DevAAC) {
    $houses = Shop::all();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($houses->toJson(JSON_PRETTY_PRINT));
});





/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/shops",
 *  @SWG\Api(
 *    path="/shops/{id}/buy",
 *    description="Operations on shops",
 *    @SWG\Operation(
 *      summary="Buy Shop Offer by ID",
 *      method="POST",
 *      type="Shop",
 *      nickname="buyOfferByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of offer to bid on",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=400, message="Bad request"),
 *      @SWG\ResponseMessage(code=401, message="Not logged in"),
 *      @SWG\ResponseMessage(code=403, message="Player not on authenticated account"),
 *      @SWG\ResponseMessage(code=422, message="Not enough coins in player's accounts")
 *    )
 *  )
 * )
 */
$DevAAC->post(ROUTES_API_PREFIX.'/shops/:id/buy', function($id) use($DevAAC) {
   // if(!SHOP_COIN)
    //    throw new InputErrorException('Shop are disabled.', 400);

  //  if( ! $DevAAC->auth_account )
  //      throw new InputErrorException('You are not logged in.', 401);

    $request = $DevAAC->request;
    $shop_offer = Shop::findOrFail($id);

//    if($house->bid_end !== 0 && new DateTime() > $house->bid_end)
//        throw new InputErrorException('Auction has ended.', 410);

    $player = Player::findOrFail($request->getAPIParam('player_id'));

    if($player->account->id != $DevAAC->auth_account->id && !$DevAAC->auth_account->isGod())
        throw new InputErrorException('Voce nao tem permissao para comprar com esse personagem.', 403);
        //throw new InputErrorException('You do not have permission to buy with this player.', 403);
    $account = $player->account;
    $coins_need = ($account->shop_coins - $shop_offer->price);

    if( $coins_need < 0)
        throw new InputErrorException('Voce nao tem coins suficientes, faltam '. $coins_need.' coins ' , 422);
    //throw new InputErrorException('You dont have coins enough price['. $shop_offer->price .'] balance['.$player->balance.']' , 422);
    $shopHistory = DevAAC\Models\ShopHistory::create(
        array(
            'player_id' =>  $player->id,
            'shop_id' => $shop_offer->id,
            'price' => $shop_offer->price,
            'buy_date' => new \DateTime()
        )
    );
    $shopHistory->save();
    $account->shop_coins = $coins_need;
    $account->save();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($shop_offer->toJson(JSON_PRETTY_PRINT));
});
