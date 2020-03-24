<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profiles extends Model
{
    public static function getMyOne($id)
    {
        $Item = self::find($id);
        $dir_images = config('my_constants.dir_images');

        if (is_file($dir_images . '/avatar_' . $Item->id . '.jpg')) {
            $Item->has_avatar = 1;

        } else {
            $Item->has_avatar = 0;
        }

        return $Item;
    }

    public static function getMyAll($all = 0)
    {
        $output = [];

        if ($all === 1) {
            $items = self::select('id')->orderBy('created_at', 'DESC')->get();

        } else {
            $items = self::select('id')->active()->orderBy('created_at', 'DESC')->get();
        }

        foreach ($items as $item) {
            $output[] = self::getMyOne($item->id);
        }

        return $output;
    }

    public function scopeActive($query)
    {
        return $query->where('key_check_email', '=', '');
    }
}
