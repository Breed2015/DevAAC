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
 * @package    DevAAC - Creatures
 * @author     Bruno Novais <cardososp@gmail.com>
 * @copyright  2015 Bruno Novais
 * @license    http://opensource.org/licenses/MIT MIT
 * @version    development
 * @link       https://github.com/novasdream/DevAAC
 */


use DevAAC\Models\Wiki_Creature;
use DevAAC\Models\Wiki_Creature_Loot;
use DevAAC\Models\Wiki_Creature_Attr;
use Illuminate\Database\Capsule\Manager as Capsule;


/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/monsters",
 *  @SWG\Api(
 *    path="/monsters/{name}",
 *    description="Operations on monsters",
 *    @SWG\Operation(
 *      summary="Get monster based on name",
 *      method="GET",
 *      type="Monster",
 *      nickname="getMonterByName",
 *      @SWG\Parameter( name="name",
 *                      description="Name of Monster that needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer/string"),
 *      @SWG\ResponseMessage(code=404, message="Monster not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/monsters/:name', function($name) use($DevAAC) {
        $req = $DevAAC->request;
    try {
        $monster = Wiki_Creature::where('name', $name)->firstOrFail();
    } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        throw new InputErrorException('Monster not found.', 404);
    }

    if($req->get('loot'))
    {
        $monster->loot;
    }

    if($req->get('attr'))
    {
        $monster->attributes;
        $monster->immunities;

    }

    if($req->get('voice'))
    {
        $monster->voices;
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($monster->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/monsters",
 *  @SWG\Api(
 *    path="/monsters",
 *    description="Operations on Monsters",
 *    @SWG\Operation(
 *      summary="Get monsters based on name",
 *      method="GET",
 *      type="Monster",
 *      nickname="getMonsters",
 *      @SWG\Parameter( name="q",
 *                      description="ID or name of Monster that needs to be fetched",
 *                      paramType="query",
 *                      required=true,
 *                      type="integer/string"),
 *      @SWG\ResponseMessage(code=404, message="Monster not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/monsters', function() use($DevAAC) {
    $req = $DevAAC->request;
    $monsters = Capsule::table('wiki_creature');

    // support ?q=partialname
    if($req->get('q'))
    {
        $monsters->where('name', 'LIKE', '%'.$req->get('q').'%');

    }

    if(intval($req->get('offset')))
        $monsters->skip($req->get('offset'));

    $limit = intval($req->get('limit'));
    if($limit && ($limit <= 10 ) )
        $monsters->take($limit);
    else
        $monsters->take(10);

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode($monsters->get(), JSON_PRETTY_PRINT));
});
