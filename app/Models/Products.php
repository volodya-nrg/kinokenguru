<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\Humans;
use App\Models\Cats;

class Products extends Model
{
    public static function getMyOne($id, $addCountries = false, $addHumans = false, $addFrames = false)
    {
        $Item = DB::table('products AS P')
            ->select(DB::raw('P.*, '
                . 'V.name AS quality_video_name, '
                . 'V.description AS quality_video_description, '
                . 'D.name AS quality_dubbing_name, '
                . 'D.description AS quality_dubbing_description'))
            ->leftJoin('quality_videos AS V', 'P.quality_video_id', '=', 'V.id')
            ->leftJoin('quality_dubbings AS D', 'P.quality_dubbing_id', '=', 'D.id')
            ->where('P.id', $id)
            ->first();

        if (is_null($Item)) {
            return null;
        }

        $Item->images = DB::table('images')->where(['el_id' => $id, 'opt' => 'product'])
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->toArray();
        $Item->trailers = DB::table('product_trailers')->where('product_id', $id)
            ->orderBy('ts', 'desc')
            ->pluck('link')
            ->toArray();

        // тут нужно C.id, чтоб в админке можно было выбрать нужный параметр из селекта
        $Item->cats = DB::table('product_cats AS P')
            ->select('C.id', 'C.slug', 'C.name')
            ->leftJoin('cats AS C', 'P.cat_id', '=', 'C.id')
            ->where('P.product_id', $id)
            ->get();

        if ($addCountries) {
            // тут нужно C.id, чтоб в админке можно было выбрать нужный параметр из селекта
            $Item->countries = DB::table('product_countries AS P')
                ->select('C.id', 'C.slug', 'C.name')
                ->leftJoin('countries AS C', 'P.country_id', '=', 'C.id')
                ->where('P.product_id', $id)
                ->get();
        }
        if ($addHumans) {
            $Item->producers = DB::table('product_humans AS P')
                ->select('H.id', 'H.fio_ru', 'H.fio_original', 'P.is_producer')
                ->leftJoin('humans AS H', 'P.human_id', '=', 'H.id')
                ->where([
                    'P.product_id' => $id,
                    'P.is_producer' => 1
                ])
                ->get();
            // заполним картинками
            if ($Item->producers->isEmpty() === false) {
                foreach ($Item->producers as $producer) {
                    $producer->images = DB::table('images')
                        ->where(['el_id' => $producer->id, 'opt' => 'human'])
                        ->pluck('name')
                        ->toArray();
                }
            }


            $Item->actors = DB::table('product_humans AS P')
                ->select('H.id', 'H.fio_ru', 'H.fio_original', 'P.is_producer')
                ->leftJoin('humans AS H', 'P.human_id', '=', 'H.id')
                ->where([
                    'P.product_id' => $id,
                    'P.is_producer' => 0
                ])
                ->get();
            // заполним картинками
            if ($Item->actors->isEmpty() === false) {
                foreach ($Item->actors as $actor) {
                    $actor->images = DB::table('images')
                        ->where(['el_id' => $actor->id, 'opt' => 'human'])
                        ->pluck('name')
                        ->toArray();
                }
            }
        }
        if ($addFrames) {
            $Item->frames = DB::table('images')->where(['el_id' => $id, 'opt' => 'frame'])
                ->orderBy('name', 'desc')
                ->pluck('name')
                ->toArray();
        }

        return $Item;
    }

    public static function getMyAll($all = 0, $addCountries = false, $addHumans = false, $addFrames = false)
    {
        $output = [];

        if ($all === 1) {
            $items = self::select('id')->orderBy('created_at', 'DESC')->get();

        } else {
            $items = self::select('id')->active()->orderBy('created_at', 'DESC')->get();
        }

        foreach ($items as $item) {
            $output[] = self::getMyOne($item->id, $addCountries, $addHumans, $addFrames);
        }

        return $output;
    }

    public static function removeImages($id, $opt, $aCovers = [])
    {
        $path = config('my_constants.dir_images');
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
            if (is_file($path . "/hr_" . $val)) unlink($path . "/hr_" . $val);
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
        self::removeImages($id, 'product'); // старые фото
        self::removeImages($id, 'frame'); // старые кадры из фильма
        DB::table('product_countries')->where('product_id', $id)->delete(); // страны
        DB::table('product_trailers')->where('product_id', $id)->delete(); // трейлеры
        DB::table('product_humans')->where('product_id', $id)->delete(); // актеров
        DB::table('product_cats')->where('product_id', $id)->delete(); // категории
    }

    public static function getLast($num = 10)
    {
        $aIds = self::active()
            ->take($num)
            ->orderBy('created_at', 'DESC')
            ->pluck('id')
            ->toArray();

        $reciver = [];
        foreach ($aIds as $id) {
            $reciver[] = self::getMyOne($id);
        }

        return $reciver;
    }

    public static function getNowWatch($limit = 12)
    {
        $aIds = DB::table('now_playing')->distinct()
            ->where('has_interval', 1)
            ->take($limit)
            ->pluck('product_id')
            ->toArray();

        $reciver = [];
        foreach ($aIds as $val) {
            $reciver[] = self::getMyOne($val);
        }

        return $reciver;
    }

    public static function getFromCat($cat_id, $all = 0)
    {
        $aIds = DB::table('product_cats')->where('cat_id', $cat_id)
            ->pluck('product_id')
            ->toArray();

        // показать только открытые
        if ($all === 1) {
            $aIds = self::whereIn('id', $aIds)->orderBy('created_at', 'desc')
                ->pluck('id')
                ->toArray();
        } else {
            $aIds = self::whereIn('id', $aIds)->active()
                ->orderBy('created_at', 'desc')
                ->pluck('id')
                ->toArray();
        }

        $reciver = [];
        foreach ($aIds as $id) {
            $reciver[] = self::getMyOne($id);
        }

        return $reciver;
    }

    public static function search($q)
    {
        $result = [];
        $data = self::where('name', 'LIKE', '%' . $q . '%')
            ->active()
            ->orderBy('created_at', 'desc')
            ->pluck('id')
            ->toArray();

        if (sizeof($data)) {
            foreach ($data as $val) {
                $result[] = self::getMyOne($val);
            }
        }

        return $result;
    }

    public static function getStatistic($id)
    {
        $see_new = DB::table('see')->where('el_id', $id)
            ->where('opt', 'product')
            ->count();
        $total_comments = DB::table('comments')->where([
            ['el_id', '=', $id],
            ['opt', '=', 'product']
        ])->count();

        $total_comments_new = DB::table('comments')->where([
            ['el_id', '=', $id],
            ['opt', '=', 'product'],
            ['created_at', '>', date('Y:m:d H:i:s', time() - 86400)]
        ])->count();

        $likes = DB::table('likes')->where('el_id', $id)
            ->where('opt', 'product')
            ->get();

        return [
            'see_new' => $see_new,
            'total_comments' => $total_comments,
            'total_comments_new' => $total_comments_new,
            'likes' => $likes
        ];
    }

    public function scopeActive($query)
    {
        return $query->where([
            ['is_hide', '=', 0],
            ['in_queue', '=', 0],
        ]);
    }
}
