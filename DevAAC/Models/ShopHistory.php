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
 * @version    master
 * @link       https://github.com/DevelopersPL/DevAAC
 */

namespace DevAAC\Models;

use DevAAC\Helpers\DateTime;

// https://github.com/illuminate/database/blob/master/Eloquent/Model.php
// https://github.com/otland/forgottenserver/blob/master/schema.sql

/**
 * @SWG\Model(required="['id','player_id','shop_id','price']")
 */
class ShopHistory extends \Illuminate\Database\Eloquent\Model {
    /**
     * @SWG\Property(name="id", type="integer")
     * @SWG\Property(name="player_id", type="integer")
     * @SWG\Property(name="shop_id", type="integer")
     * @SWG\Property(name="price", type="integer")
     * @SWG\Property(name="discount", type="integer")
     * @SWG\Property(name="buy_date", type="DateTime::ISO8601")
     * @SWG\Property(name="delivery_date", type="DateTime::ISO8601")
     * @SWG\Property(name="code", type="string")
     * @SWG\Property(name="delivery_status", type="integer")
     */

    protected $table = 'shop_history';
    public $timestamps = false;
    protected $guarded = array('id,code');
    protected $hidden = array('end_date');


    public function player()
    {
        return $this->belongsTo('DevAAC\Models\Player');
    }


    public function getBuyDateAttribute()
    {
        $date = new DateTime();
        $date->setTimestamp($this->attributes['buy_date']);
        return $date;
    }

    public function setBuyDateAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['buy_date'] = $d->getTimestamp();
        elseif((int) $d != (string) $d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['buy_date'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['buy_date'] = $d;
    }

    public function getDeliveryDateAttribute()
    {
        $date = new DateTime();
        $date->setTimestamp($this->attributes['delivery_date']);
        return $date;
    }

    public function setDeliveryDateAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['delivery_date'] = $d->getTimestamp();
        elseif((int) $d != (string) $d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['delivery_date'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['delivery_date'] = $d;
    }


}
