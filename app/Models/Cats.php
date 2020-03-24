<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\Products;

class Cats extends Model
{
    public $timestamps = false;

    /*
     * ф-ия возвращает данные о категориях жанров
     *
     * return array objects
     */
    public static function getAllWhoHasTotal()
    {
        $res = DB::select(DB::raw('
			SELECT PC.cat_id, PC.product_id, C.slug, C.name
				FROM `product_cats` AS PC
				LEFT JOIN `products` AS P ON P.id = PC.product_id
				LEFT JOIN `cats` AS C ON C.id = PC.cat_id
				WHERE P.is_hide=0
		'));

        $aData = [];
        foreach ($res as $val) {
            $key = $val->cat_id;

            if (isset($aData[$key])) {
                $aData[$key]->total++;
                continue;

            } else {
                $aData[$key] = $val;
                $aData[$key]->total = 1;
            }
        }

        return $aData;
    }

    /*
     * ф-ия проверяет наличие категории
     */
    public static function isExists($slug)
    {
        return self::where('slug', $slug)->count();
    }

}
