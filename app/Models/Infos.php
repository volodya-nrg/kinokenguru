<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\InfoCats;

class Infos extends Model
{
    public static function getMyOne($id)
    {
        return self::find($id);
    }

    public static function getMyAll()
    {
        return self::all();
    }

    public static function findViaSlug($slugs, $all = 0)
    {
        $aDirs = collect();
        $aFiles = collect();
        $folder = null;
        $post = null;
        $aBreadCrumbs = [['infos' => 'Информация']];

        // если ни чего не задано, то вернем главные каталоги и главные статьи без родителей
        if ($slugs === "") {
            if ($all === 1) {
                $aDirs = InfoCats::where('parent_id', 0)->orderBy('pos', 'ASC')->get();
                $aFiles = self::where('cat_id', 0)->orderBy('created_at', 'ASC')->get();

            } else {
                $aDirs = InfoCats::where('parent_id', 0)->active()->orderBy('pos', 'ASC')->get();
                $aFiles = self::where('cat_id', 0)->active()->orderBy('created_at', 'ASC')->get();
            }

        } else {
            $aTmpDirs = explode("/", $slugs);
            $total = sizeof($aTmpDirs);

            foreach ($aTmpDirs as $key => $val) {
                // будем просматривать каждый уровень в подпапках
                if ($all === 1) {
                    $el = InfoCats::where('slug', $val)->first();

                } else {
                    $el = InfoCats::where('slug', $val)->active()->first();
                }

                // если обращение на последний элемент, иначе проверяем подпапки
                if ($key === $total - 1) {
                    // тут надо понять: к папке обращаются или к статье
                    // если к папке, то подхватим подпапки и файлы лежащие в ней
                    if (is_null($el) === false) {
                        if ($all === 1) {
                            $aDirs = InfoCats::where('parent_id', $el->id)->orderBy('pos', 'ASC')->get();
                            $aFiles = self::where('cat_id', $el->id)->orderBy('created_at', 'ASC')->get();

                        } else {
                            $aDirs = InfoCats::where('parent_id', $el->id)->active()
                                ->orderBy('pos', 'ASC')->get();
                            $aFiles = self::where('cat_id', $el->id)->active()
                                ->orderBy('created_at', 'ASC')->get();
                        }

                        $folder = $el;
                        $aBreadCrumbs[] = [$el->slug => $el->name];

                        // если все же идет обращение к статье, то поищем ее
                    } else {
                        if ($all === 1) {
                            $tmp = self::where('slug', $val)->pluck('id')->toArray();

                        } else {
                            $tmp = self::where('slug', $val)->active()->pluck('id')->toArray();
                        }

                        if (sizeof($tmp) === 0) {
                            abort(404);
                        }

                        $post = self::getMyOne($tmp[0]);
                        $aBreadCrumbs[] = [$post->slug => $post->title];
                    }

                } elseif (is_null($el)) {
                    abort(404);

                } else {
                    $aBreadCrumbs[] = [$el->slug => $el->name];
                }
            }
        }

        return [
            'dirs' => $aDirs,
            'files' => $aFiles,
            'folder' => $folder,
            'post' => $post,
            'breadcrumbs' => $aBreadCrumbs
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_hide', 0);
    }
}
