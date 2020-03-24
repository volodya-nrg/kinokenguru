<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Humans extends Model
{
    public $timestamps = false;

    public static function getMyOne($id)
    {
        $Item = self::find($id);
        $Item->images = DB::table('images')->where(['el_id' => $id, 'opt' => 'human'])
            ->pluck('name');

        return $Item;
    }

    public static function getMyAll()
    {
        $output = [];
        $items = self::select('id')->orderBy('id')->get();

        foreach ($items as $item) {
            $output[] = self::getMyOne($item->id);
        }

        return $output;
    }

    public static function removeCovers($id, $aCovers = [])
    {
        $path = config('my_constants.dir_images');
        $opt = 'human';
        $aOldImages = DB::table('images')->where(['el_id' => $id, 'opt' => $opt])
            ->pluck('name')
            ->toArray();

        // если изображений вообще нет, то выйдем
        if (sizeof($aOldImages) === 0) {
            return;
        }

        $aTargets = array_diff($aOldImages, $aCovers);

        // если нечего удалять, то выйдем
        if (sizeof($aTargets) === 0) {
            return;
        }

        foreach ($aTargets as $val) {
            if (is_file($path . "/" . $val)) unlink($path . "/" . $val);
            if (is_file($path . "/md_" . $val)) unlink($path . "/md_" . $val);
            if (is_file($path . "/sm_" . $val)) unlink($path . "/sm_" . $val);
        }

        DB::table('images')->where(['el_id' => $id, 'opt' => $opt])->delete();

        if (sizeof($aCovers)) {
            $aTmp = [];
            foreach ($aCovers as $val) {
                $aTmp[] = [
                    'name' => $val,
                    'el_id' => $id,
                    'opt' => $opt
                ];
            }

            DB::table('images')->insert($aTmp);
        }
    }

    public static function removeOne($id)
    {
        self::find($id)->delete();
        self::removeCovers($id); // старые фото
    }
}