<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Profiles;

class Comments extends Model
{
    public static function getMyOne($el_id)
    {
        $item = self::find($el_id);

        if (!empty($item->user_id)) {
            $item->name = Profiles::find($item->user_id)->name;
        }

        return $item;
    }

    public static function getMyAll()
    {
        $Items = self::orderBy('created_at', 'desc')->get();

        foreach ($Items as $val) {
            if (!empty($val->user_id)) {
                $val->name = Profiles::find($val->user_id)->name;
            }
        }

        return $Items;
    }

    public static function getMyAllForEl($el_id, $opt)
    {
        $aIds = self::where([
            ['el_id', '=', $el_id],
            ['opt', '=', $opt]
        ])
            ->orderBy('created_at', 'ASC')
            ->pluck('id')
            ->toArray();
        $output = [];
        foreach ($aIds as $val) {
            $output[] = self::getMyOne($val);
        }

        return $output;
    }
}
