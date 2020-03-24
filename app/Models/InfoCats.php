<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InfoCats extends Model
{
    public $timestamps = false;

    public static function getAsSelect($all = 0)
    {
        makeAOneDimensionalArray(self::getAsTree($all), $output);

        return $output;
    }

    public static function getAsTree($all = 0)
    {
        $tree = [];
        $sub = [0 => &$tree];

        if ($all === 1) {
            $items = self::orderBy('parent_id', 'asc')->orderBy('pos', 'asc')->get()->toArray();

        } else {
            $items = self::orderBy('parent_id', 'asc')->active()->orderBy('pos', 'asc')->get()->toArray();
        }

        // строим дерево
        foreach ($items as $item) {
            $branch = &$sub[$item['parent_id']];
            $branch[$item['id']] = $item;
            $sub[$item['id']] = &$branch[$item['id']]['childs'];
        }

        return $tree;
    }

    public static function getMyAll($all = 0)
    {
        $items = self::getAsSelect($all);

        return $items;
    }

    public static function getMyOne($id)
    {
        $array = self::getAsSelect();
        $output = [];

        foreach ($array as $val) {
            if ($val['id'] === $id) {
                $output = $val;

                break;
            }
        }

        return $output;
    }

    public static function getAsTreeHtml($array, &$reciver = "", $deep = 0, $classParent = "ul-main-tree", $path = "infos")
    {
        if ($deep === 0) {
            $reciver .= '<ul class="' . $classParent . '">';

        } else {
            if (substr(\Request::path(), 0, strlen($path)) === $path) {
                $open = 'class="' . $classParent . '-open"';

            } else {
                $open = "";
            }

            $reciver .= '<ul ' . $open . '>';
        }

        foreach ($array as $val) {
            $reciver .= '<li><a href="/' . $path . "/" . $val['slug'] . '">' . $val['name'] . '</a>';

            if (sizeof($val['childs'])) {

                $reciver .= '<i class="' . $classParent . '-caret fa fa-caret-down fa-fw activity"></i>';
                self::getAsTreeHtml($val['childs'], $reciver, $deep + 1, $classParent, $path . "/" . $val['slug']);

            } else {
                $reciver .= '</li>';
            }
        }

        $reciver .= '</ul>';
    }

    public static function myDelete($id)
    {
        $id = (int)$id;
        $array = self::getAsSelect(1);
        $aChildIds = [];

        foreach ($array as $val) {
            if ($id === $val['id'] && !empty($val['child_ids']) && sizeof($val['child_ids'])) {
                $aChildIds = $val['child_ids'];
                break;
            }
        }

        $aChildIds[] = $id;

        self::whereIn('id', $aChildIds)->delete();

        return $aChildIds; // вернем id-шки, которые нужно будет удалить из коллекции
    }

    public function scopeActive($query)
    {
        return $query->where('is_hide', 0);
    }
}
