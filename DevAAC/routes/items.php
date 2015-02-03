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
 * @package    DevAAC - Items
 * @author     Bruno Novais <cardososp@gmail.com>
 * @copyright  2015 Bruno Novais
 * @license    http://opensource.org/licenses/MIT MIT
 * @version    development
 * @link       https://github.com/novasdream/DevAAC
 */

use DevAAC\Models\Wiki_Item;
use DevAAC\Models\Wiki_Item_Attr;


/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/item",
 *  @SWG\Api(
 *    path="/item/{id}/loot",
 *    description="Operations on item",
 *    @SWG\Operation(
 *      summary="Get Item based on ID or name",
 *      method="GET",
 *      type="Wiki_Item",
 *      nickname="getItembyID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of item that needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=404, message="Item not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/items', function() use($DevAAC) {

  $req = $DevAAC->request;
        $limit = 20;

      if($req->get('limit')){
        $limit = $req->get('limit');
      }
	if($req->get('q')){
		$items = Wiki_Item::where('name', 'LIKE', '%'.$req->get('q').'%') ->orderBy('name', 'asc')->take($limit);
		foreach ($items as $item ) {
			$item->get();
			$item->attributes;
		}
	}
    if( !isset($items))
        throw new InputErrorException('Items não encontrados!' , 404);
	$DevAAC->response->headers->set('Content-Type', 'application/json');
	$DevAAC->response->setBody($items->get()->each(function($item)
{
	$item->attributes ;
})->toJson(JSON_PRETTY_PRINT));


});


/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/item",
 *  @SWG\Api(
 *    path="/item/{id}",
 *    description="Operations on item",
 *    @SWG\Operation(
 *      summary="Get Item based on ID or name",
 *      method="GET",
 *      type="Wiki_Item",
 *      nickname="getItemListbyID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of item that needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=404, message="Item not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/items/:id', function($id) use($DevAAC) {
  $req = $DevAAC->request;


  try {
      $item = Wiki_Item::findOrFail($id);
  } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e)
  {
	try
	{
		$item = Wiki_Item::where('name', $id)->first();
	}
	catch(Illuminate\Database\Eloquent\ModelNotFoundException $e)
	{
		throw new InputErrorException('Item not found.', 404);
	}
  }
  $item->attributes;
  $loot = ($req->get('loot'))?true:false;
  if($loot)
    $item->loot;

  $DevAAC->response->headers->set('Content-Type', 'application/json');
  $DevAAC->response->setBody($item->toJson(JSON_PRETTY_PRINT));
});



/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/item",
 *  @SWG\Api(
 *    path="/item/{id}/loot",
 *    description="Operations on item",
 *    @SWG\Operation(
 *      summary="Get monsters will drop item based on ID or name",
 *      method="GET",
 *      type="Wiki_Item",
 *      nickname="getItembyID",
 *      @SWG\Parameter( name="id",
 *                      description="ID or name of Item ",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=404, message="Item not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/items/:id/loot', function($id) use($DevAAC) {
  $req = $DevAAC->request;
  try {
    $item = Wiki_Item::findOrFail($id);
    $item->getLoot;
  } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e)
  {
	try
	{
		$item = Wiki_Item::where('name', $id)->first();
		$item->getLoot;
	}
	catch(Illuminate\Database\Eloquent\ModelNotFoundException $e)
	{
		throw new InputErrorException('Item not found.', 404);
	}
  }
  $DevAAC->response->headers->set('Content-Type', 'application/json');
  $DevAAC->response->setBody($item->toJson(JSON_PRETTY_PRINT));
});

