<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Cats;
use App\Models\Products;
use App\Models\Humans;
use App\Models\Infos;
use App\Models\InfoCats;
use App\Models\Profiles;
use App\Models\Comments;
use App\Models\Ideas;
use App\Models\Pages;

class AdminController extends Controller
{
    public function getJsonProductsList()
    {
        return response()->json(Products::getMyAll(1, true, true, true));
    }

    public function deleteProduct($id)
    {
        Products::removeOne($id);
        return response()->json();
    }

    public function updateProduct(Request $request, $id = 0)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $id = !empty($id) ? abs(intval($id)) : 0;
        $name = !empty($request->name) ? $request->name : "";
        $year = !empty($request->year) ? abs(intval($request->year)) : 0;
        $link = !empty($request->link) ? $request->link : "";
        $duration = !empty($request->duration) ? abs(intval($request->duration)) : 0;
        $description = !empty($request->description) ? $request->description : "";
        $quality_video_id = isset($request->quality_video_id) ? abs(intval($request->quality_video_id)) : 0;
        $quality_dubbing_id = isset($request->quality_dubbing_id) ? abs(intval($request->quality_dubbing_id)) : 0;
        $budget = !empty($request->budget) ? abs(intval(str_replace(" ", "", $request->budget))) : 0;
        $rating_imdb = !empty($request->rating_imdb) ? abs(round($request->rating_imdb, 2)) : 0;
        $rating_kinopoisk = !empty($request->rating_kinopoisk) ? abs(round($request->rating_kinopoisk, 2)) : 0;
        $is_hide = !empty($request->is_hide) ? 1 : 0;
        $in_queue = !empty($request->in_queue) ? 1 : 0;
        $video_file = (!empty($request->video_file) && is_file(public_path() . "/" . $request->video_file)) ? public_path() . "/" . $request->video_file : "";
        $slogan = !empty($request->slogan) ? $request->slogan : "";
        $old = !empty($request->old) ? $request->old : "";
        $name_original = !empty($request->name_original) ? $request->name_original : "";

        //[name: '', size: '', data: '']
        $files = !empty($request->all()['files']) ? $request->all()['files'] : [];
        $images = !empty($request->images) ? $request->images : [];
        $frames = !empty($request->frames) ? $request->frames : [];
        $countries = !empty($request->countries) ? $request->countries : [];
        $trailers = !empty($request->trailers) ? $request->trailers : [];
        $producers = !empty($request->producers) ? $request->producers : [];
        $actors = !empty($request->actors) ? $request->actors : [];
        $cats = !empty($request->cats) ? $request->cats : [];

        if ($name === "") {
            $aErrs[] = 'name is empty';
        }
        if (sizeof($cats) === 0) {
            $aErrs[] = 'cats is empty';
        }
        if ($link === "") {
            $aErrs[] = 'link is empty';
        }
        if (!$id && empty($video_file)) {
            $aErrs[] = 'video_file is empty';
        }

        if (sizeof($aErrs) === 0) {
            // определимся с будущим id в суффиксе
            if ($id) {
                $future_id = $id;

            } else {
                $future_id = 0;
                foreach (DB::select('SHOW TABLE STATUS') as $obj) {
                    if ($obj->Name == 'products' && !empty($obj->Auto_increment) && is_numeric($obj->Auto_increment)) {
                        $future_id = $obj->Auto_increment;
                        break;
                    }
                }
                if ($future_id === 0) {
                    $future_id = Products::all()->count() + 1;
                }
            }

            // создадим slug
            $slug = str_slug($name, '-') . "_" . $future_id;

            // обработаем трейлеры
            $trailers_2 = [];
            if (is_array($trailers) && sizeof($trailers)) {
                $tmp = [];
                foreach ($trailers as $val) {
                    $tmp[] = trim($val);
                }
                $trailers_2 = array_unique($tmp); // чтоб не было дубликатов
            }
            $trailers = $trailers_2;

            // проверим еще на уникальности
            // если это у нас новый элемент
            if (empty($id)) {
                $is_uniq_slug = Products::where('slug', $slug)->count();
                $is_uniq_link = Products::where('link', $link)->count();
                $not_uniq_trailer = "";

                foreach ($trailers as $trailer) {
                    if (DB::table('product_trailers')->where('link', $trailer)->count()) {
                        $not_uniq_trailer = $trailer;
                        break;
                    }
                }

            } else {
                $is_uniq_slug = Products::where('id', '<>', $id)->where('slug', $slug)->count();
                $is_uniq_link = Products::where('id', '<>', $id)->where('link', $link)->count();
                $not_uniq_trailer = "";

                foreach ($trailers as $trailer) {
                    if (DB::table('product_trailers')->where('product_id', '<>', $id)->where('link', $trailer)->count()) {
                        $not_uniq_trailer = $trailer;
                        break;
                    }
                }
            }

            if ($is_uniq_slug) {
                $aErrs[] = 'slug already exists';
            }
            if ($is_uniq_link) {
                $aErrs[] = 'link already exists';
            }
            if (!empty($not_uniq_trailer)) {
                $aErrs[] = 'trailer (' . $not_uniq_trailer . ') already exists';
            }

            if (sizeof($aErrs) === 0) {
                $dir_images = config('my_constants.dir_images');

                $Product = $id ? Products::find($id) : new Products;
                $Product->slug = $slug;
                $Product->name = $name;
                $Product->name_original = $name_original;
                $Product->year = $year;
                $Product->link = $link;
                $Product->duration = $duration;
                $Product->description = $description;
                $Product->slogan = $slogan;
                $Product->old = $old;
                $Product->quality_video_id = $quality_video_id;
                $Product->quality_dubbing_id = $quality_dubbing_id;
                $Product->budget = $budget;
                $Product->rating_imdb = $rating_imdb;
                $Product->rating_kinopoisk = $rating_kinopoisk;
                $Product->is_hide = $is_hide;
                $Product->in_queue = $in_queue;
                $Product->save();

                if ($id) {
                    // почистим
                    Products::removeImages($id, 'product', $images); // старые фото
                    Products::removeImages($id, 'frame', $frames); // старые карды фильма
                    DB::table('product_countries')->where('product_id', $id)->delete(); // страны
                    DB::table('product_trailers')->where('product_id', $id)->delete(); // трейлеры
                    DB::table('product_humans')->where('product_id', $id)->delete(); // актеры
                    DB::table('product_cats')->where('product_id', $id)->delete(); // категории
                }

                $id = $Product->id; // именно тут (после удаления)

                // определится со странами
                if (is_array($countries) && sizeof($countries)) {
                    $aTmp = [];
                    foreach ($countries as $item) {
                        $aTmp[] = ['product_id' => $id, 'country_id' => $item];
                    }
                    DB::table('product_countries')->insert($aTmp);
                }
                // определится с трейлерами
                if (is_array($trailers) && sizeof($trailers)) {
                    $aTmp = [];
                    foreach ($trailers as $item) {
                        $aTmp[] = ['product_id' => $id, 'link' => $item];
                    }
                    DB::table('product_trailers')->insert($aTmp);
                }
                // определится с продюсерами
                if (is_array($producers) && sizeof($producers)) {
                    $aTmp = [];
                    foreach ($producers as $item) {
                        $aTmp[] = ['product_id' => $id, 'human_id' => $item, 'is_producer' => 1];
                    }
                    DB::table('product_humans')->insert($aTmp);
                }
                // определится с актерами
                if (is_array($actors) && sizeof($actors)) {
                    $aTmp = [];
                    foreach ($actors as $item) {
                        $aTmp[] = ['product_id' => $id, 'human_id' => $item, 'is_producer' => 0];
                    }
                    DB::table('product_humans')->insert($aTmp);
                }
                // определится с категориями
                if (is_array($cats) && sizeof($cats)) {
                    $aTmp = [];
                    foreach ($cats as $cat) {
                        $aTmp[] = ['product_id' => $id, 'cat_id' => $cat];
                    }
                    DB::table('product_cats')->insert($aTmp);
                }
                // если есть видео файл, то определимся по нему
                if (!empty($video_file)) {
                    exec(base_path() . '/ffmpeg -i "' . $video_file . '" 2>&1', $output);
                    $duration = "";

                    foreach ($output as $val) {
                        if (preg_match('/Duration: ([0-9:.]+)/', $val, $m)) {
                            $duration = $m[1];
                            break;
                        }
                    }

                    if (!empty($duration)) {
                        list($hour, $min, $sec) = explode(":", $duration);
                        $sec = (int)$hour * 3600 + (int)$min * 60 + (int)$sec;

                        // подстрахуемся
                        if ($sec > 10) {
                            // т.к. знаем время, то обовим его в гл. таблице
                            $Product->duration = $sec;
                            $Product->save();
                            $aTmp = [];

                            // создадим кадрые из фильма
                            // $step - шаг каждого перехода, $num - суфикс к файлу
                            for ($step = round($sec / 11), $i = $step, $num = 1; $num < 11; $i += $step, $num++) {
                                $time = new \DateTime('@' . $i);
                                $time_step = $time->format('H:i:s');
                                $imageName = createUniqIntName() . ".jpg";

                                exec(base_path() . '/ffmpeg -ss ' . $time_step . ' -i "' . $video_file . '" -f image2 -vframes 1 "' . $dir_images . '/' . $imageName . '"');

                                if (!is_file($dir_images . '/' . $imageName)) {
                                    continue;
                                }

                                $img = \Image::make($dir_images . '/' . $imageName);

                                // метим относительно ширины, из-за поп-апа
                                $img->resize(640, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                                $img->save($dir_images . '/' . $imageName, 90);
                                \Image::make($dir_images . '/' . $imageName)->fit(300, 169)
                                    ->save($dir_images . '/sm_' . $imageName, 80);
                                $aTmp[] = [
                                    'name' => $imageName,
                                    'el_id' => $id,
                                    'opt' => 'frame'
                                ];
                            }

                            if (sizeof($aTmp)) {
                                DB::table('images')->insert($aTmp);
                            }
                        }
                    }
                }

                // определится с новыми фотографиями
                if (is_array($files) && sizeof($files)) {
                    // надо отсортировать массив по ключу name
                    $aTmp = [];
                    foreach ($files as $file) {
                        $aTmp[] = $file['name'];
                    }
                    array_multisort($aTmp, SORT_NATURAL, $files);

                    // придумаем уникальное имя, один раз
                    $tmpName = createUniqIntName();

                    foreach ($files as $key => $file) {
                        $imageName = $tmpName . "_" . ($key + 1) . ".jpg";
                        $imageData = substr($file['data'], strlen('data:image/jpeg;base64,'));
                        $imageData = base64_decode($imageData);
                        $imageSize = (int)$file['size'];
                        $source = imagecreatefromstring($imageData); // Allowed memory size of - 268 435 456
                        $imageSave = imagejpeg($source, $dir_images . '/' . $imageName, 90);
                        imagedestroy($source);

                        if ($imageSave === false) {
                            continue;

                        } else {
                            // предохранимся от слишком больших разрешений фото
                            // exec('sips --resampleHeight 1000 "'.$dir_images.'/'.$imageName.'" --out "'.$dir_images.'/'.$imageName.'"');
                        }

                        $img = \Image::make($dir_images . '/' . $imageName);
                        $img->resize(null, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $img->save($dir_images . '/' . $imageName, 100);

                        // качество 100, т.к. отображаем md версию
                        \Image::make($dir_images . '/' . $imageName)->fit(407, 540)
                            ->save($dir_images . '/md_' . $imageName, 100);
                        \Image::make($dir_images . '/' . $imageName)->fit(272, 360)
                            ->save($dir_images . '/sm_' . $imageName, 80);
                        \Image::make($dir_images . '/' . $imageName)->fit(300, 169, null, 'top')
                            ->save($dir_images . '/hr_' . $imageName, 90);

                        DB::table('images')->insert([
                            'name' => $imageName,
                            'el_id' => $id,
                            'opt' => 'product'
                        ]);
                    }
                }

                $result = true;

            } else {
                $msg = implode("\n", $aErrs);
            }

        } else {
            $msg = implode("\n", $aErrs);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(Products::getMyOne($id, true, true, true));
    }

    // ---------------------------------------------------------------------------------------------
    public function deleteHuman($id)
    {
        Humans::removeOne($id);
        return response()->json();
    }

    public function updateHuman(Request $request, $id = 0)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $id = !empty($id) ? abs(intval($id)) : 0;
        $fio_ru = !empty($request->fio_ru) ? $request->fio_ru : "";
        $fio_original = !empty($request->fio_original) ? $request->fio_original : "";
        $about = !empty($request->about) ? $request->about : "";
        $birthday = !empty($request->birthday) ? $request->birthday : "";
        $country_id = !empty($request->country_id) ? $request->country_id : 0;
        $city_id = !empty($request->city_id) ? $request->city_id : 0;

        $files = !empty($request->all()['files']) ? $request->all()['files'] : [];
        $images = !empty($request->images) ? $request->images : [];

        if ($fio_ru === "") {
            $aErrs[] = 'fio_ru is empty';
        }

        $birthday = date('Y-m-d H:i:s');

        if (sizeof($aErrs) === 0) {
            $count = !$id ? Humans::where('fio_ru', $fio_ru)->count() : Humans::where('id', '<>', $id)->where('fio_ru', $fio_ru)->count();

            if ($count === 0) {
                $Humans = $id ? Humans::find($id) : new Humans;
                $Humans->fio_ru = $fio_ru;
                $Humans->fio_original = $fio_original;
                $Humans->about = $about;
                $Humans->birthday = $birthday;
                $Humans->country_id = $country_id;
                $Humans->save();

                if ($id) {
                    // почистим
                    Humans::removeCovers($id, $images); // старые фото
                }

                $id = $Humans->id; // именно тут (после удаления)

                // определится с новыми фотографиями
                if (is_array($files) && sizeof($files)) {
                    $path = config('my_constants.dir_images');

                    foreach ($files as $imageData) {
                        $imageName = createUniqIntName() . ".jpg";
                        $imageData = substr($imageData, strlen('data:image/jpeg;base64,'));
                        $imageData = base64_decode($imageData);
                        $source = imagecreatefromstring($imageData);
                        $imageSave = imagejpeg($source, $path . '/' . $imageName, 100);
                        imagedestroy($source);

                        if ($imageSave) {
                            $img = \Image::make($path . '/' . $imageName);
                            $img->resize(null, 800, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                            $img->save($path . '/' . $imageName, 100);

                            \Image::make($path . '/' . $imageName)->fit(317, 420)
                                ->save($path . '/md_' . $imageName, 90);
                            \Image::make($path . '/' . $imageName)->fit(136, 180)
                                ->save($path . '/sm_' . $imageName, 80);

                            DB::table('images')->insert([
                                'name' => $imageName,
                                'el_id' => $id,
                                'opt' => 'human'
                            ]);
                        }
                    }
                }

                $result = true;

            } else {
                $msg = 'fio_ru already exists';
            }

        } else {
            $msg = implode("\n", $aErrs);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(Humans::getMyOne($id));
    }

    // ---------------------------------------------------------------------------------------------
    public function getJsonInfosList()
    {
        return response()->json(Infos::getMyAll());
    }

    public function deleteInfo($id)
    {
        Infos::find($id)->delete();
        return response()->json();
    }

    public function updateInfo(Request $request, $id = 0)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $id = !empty($id) ? abs(intval($id)) : 0;
        $title = !empty($request->title) ? $request->title : "";
        $h1 = !empty($request->h1) ? $request->h1 : "";
        $description = !empty($request->description) ? $request->description : "";
        $is_hide = !empty($request->is_hide) ? 1 : 0;
        $meta_keywords = !empty($request->meta_keywords) ? $request->meta_keywords : "";
        $meta_desc = !empty($request->meta_desc) ? $request->meta_desc : "";
        $cat_id = !empty($request->cat_id) ? abs(intval($request->cat_id)) : 0;
        $slug = str_slug($title, '-');

        if ($title === "") {
            $aErrs[] = 'title is empty';
        }
        if ($title !== "" && $slug === "") {
            $aErrs[] = 'slug is empty';
        }
        if ($h1 === "") {
            $aErrs[] = 'h1 is empty';
        }

        if (sizeof($aErrs) === 0) {
            $count = !$id ? Infos::where('slug', $slug)->where('cat_id', $cat_id)->count() : Infos::where('id', '<>', $id)->where('slug', $slug)->where('cat_id', $cat_id)->count();

            if ($count === 0) {
                $meta_keywords = str_limit($meta_keywords, 252);
                $meta_desc = str_limit($meta_desc, 252);

                $Infos = $id ? Infos::find($id) : new Infos;
                $Infos->slug = $slug;
                $Infos->title = $title;
                $Infos->h1 = $h1;
                $Infos->description = $description;
                $Infos->is_hide = $is_hide;
                $Infos->meta_keywords = $meta_keywords;
                $Infos->meta_desc = $meta_desc;
                $Infos->cat_id = $cat_id;
                $Infos->save();

                $id = $Infos->id;
                $result = true;

            } else {
                $msg = 'slug with cat_id already exists';
            }

        } else {
            $msg = implode("\n", $aErrs);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json($Infos->toArray());
    }

    // ---------------------------------------------------------------------------------------------
    public function deleteInfoCat($id)
    {
        $aDel = InfoCats::myDelete($id);
        return response()->json($aDel);
    }

    public function updateInfoCat(Request $request, $id = 0)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $id = !empty($id) ? abs(intval($id)) : 0;
        $name = !empty($request->name) ? $request->name : "";
        $description = !empty($request->description) ? $request->description : "";
        $pos = !empty($request->pos) ? abs(intval($request->pos)) : 1;
        $parent_id = !empty($request->parent_id) ? abs(intval($request->parent_id)) : 0;
        $is_hide = !empty($request->is_hide) ? 1 : 0;

        $slug = str_slug($name, '-');

        if ($name === "") {
            $aErrs[] = 'name is empty';
        }
        if ($name !== "" && $slug === "") {
            $aErrs[] = 'slug is empty';
        }

        if (sizeof($aErrs) === 0) {
            $count = !$id ? InfoCats::where('slug', $slug)->where('parent_id', $parent_id)->count() : InfoCats::where('id', '<>', $id)->where('slug', $slug)->where('parent_id', $parent_id)->count();

            if ($count === 0) {
                $InfoCats = $id ? InfoCats::find($id) : new InfoCats;
                $InfoCats->slug = $slug;
                $InfoCats->name = $name;
                $InfoCats->description = $description;
                $InfoCats->pos = $pos;
                $InfoCats->is_hide = $is_hide;
                $InfoCats->parent_id = $parent_id;
                $InfoCats->save();

                $id = $InfoCats->id;
                $result = true;

            } else {
                $msg = 'slug with parent_id already exists';
            }

        } else {
            $msg = implode("\n", $aErrs);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(InfoCats::getMyOne($id));
    }

    // ---------------------------------------------------------------------------------------------
    public function deleteCat($id)
    {
        Cats::find($id)->delete();
        return response()->json();
    }

    public function updateCat(Request $request, $id = 0)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $id = !empty($id) ? abs(intval($id)) : 0;
        $slug = !empty($request->slug) ? $request->slug : "";
        $name = !empty($request->name) ? $request->name : "";
        $description = !empty($request->description) ? $request->description : "";
        $title = !empty($request->title) ? $request->title : "";

        if ($slug === "") {
            $aErrs[] = 'slug is empty';
        }
        if ($name === "") {
            $aErrs[] = 'name is empty';
        }

        if (sizeof($aErrs) === 0) {
            $count = !$id ? Cats::where('slug', $slug)->count() : Cats::where('id', '<>', $id)->where('slug', $slug)->count();

            if ($count === 0) {
                $Cats = !$id ? new Cats() : Cats::find($id);
                $Cats->slug = $slug;
                $Cats->name = $name;
                $Cats->description = $description;
                $Cats->title = $title;
                $Cats->save();

                $id = $Cats->id;
                $result = true;

            } else {
                $msg = 'is dublicate';
            }

        } else {
            $msg = implode("\n", $aErrs);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(['id' => $id]);
    }

    // ---------------------------------------------------------------------------------------------
    public function deleteCountry($id)
    {
        DB::table('countries')->where('id', $id)->delete();
        return response()->json();
    }

    public function updateCountry(Request $request, $id = 0)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $id = !empty($id) ? abs(intval($id)) : 0;
        $slug = !empty($request->slug) ? $request->slug : "";
        $name = !empty($request->name) ? $request->name : "";

        if ($slug === "") {
            $aErrs[] = 'slug is empty';
        }
        if ($name === "") {
            $aErrs[] = 'name is empty';
        }

        if (sizeof($aErrs) === 0) {
            $count = !$id ? DB::table('countries')->where('slug', $slug)->count() : DB::table('countries')->where('id', '<>', $id)->where('slug', $slug)->count();

            if ($count === 0) {
                $new_data = ['slug' => $slug, 'name' => $name];

                if ($id) {
                    DB::table('countries')->where('id', $id)->update($new_data);

                } else {
                    $id = DB::table('countries')->insertGetId($new_data);
                }

                $result = true;

            } else {
                $msg = 'is dublicate';
            }

        } else {
            $msg = implode("\n", $aErrs);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(['id' => $id]);
    }

    // ---------------------------------------------------------------------------------------------
    public function deleteQualityVideo($id)
    {
        DB::table('quality_videos')->where('id', $id)->delete();
        return response()->json();
    }

    public function updateQualityVideo(Request $request, $id = 0)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $id = !empty($id) ? abs(intval($id)) : 0;
        $name = !empty($request->name) ? $request->name : "";
        $description = !empty($request->description) ? $request->description : "";

        if ($name === "") {
            $aErrs[] = 'name is empty';
        }

        if (sizeof($aErrs) === 0) {
            $count = !$id ? DB::table('quality_videos')->where('name', $name)->count() : DB::table('quality_videos')->where('id', '<>', $id)->where('name', $name)->count();

            if ($count === 0) {
                $new_data = ['name' => $name, 'description' => $description];

                if ($id) {
                    DB::table('quality_videos')->where('id', $id)->update($new_data);

                } else {
                    $id = DB::table('quality_videos')->insertGetId($new_data);
                }

                $result = true;

            } else {
                $msg = 'is dublicate';
            }

        } else {
            $msg = implode("\n", $aErrs);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(['id' => $id]);
    }

    // ---------------------------------------------------------------------------------------------
    public function deleteQualityDubbing($id)
    {
        DB::table('quality_dubbings')->where('id', $id)->delete();
        return response()->json();
    }

    public function updateQualityDubbing(Request $request, $id = 0)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $id = !empty($id) ? abs(intval($id)) : 0;
        $name = !empty($request->name) ? $request->name : "";
        $description = !empty($request->description) ? $request->description : "";

        if ($name === "") {
            $aErrs[] = 'name is empty';
        }

        if (sizeof($aErrs) === 0) {
            $count = !$id ? DB::table('quality_dubbings')->where('name', $name)->count() : DB::table('quality_dubbings')->where('id', '<>', $id)->where('name', $name)->count();

            if ($count === 0) {
                $new_data = ['name' => $name, 'description' => $description];

                if ($id) {
                    DB::table('quality_dubbings')->where('id', $id)->update($new_data);

                } else {
                    $id = DB::table('quality_dubbings')->insertGetId($new_data);
                }

                $result = true;

            } else {
                $msg = 'is dublicate';
            }

        } else {
            $msg = implode("\n", $aErrs);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(['id' => $id]);
    }

    // ---------------------------------------------------------------------------------------------
    public function getJsonUsersList()
    {
        return response()->json(Profiles::getMyAll(1));
    }

    public function updateUser(Request $request, $id)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $name = !empty($request->name) ? e(strip_tags($request->name)) : "";

        $Profile = Profiles::find($id);
        $Profile->name = $name;
        $Profile->save();

        $result = true;

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(Profiles::getMyOne($id));
    }

    // ---------------------------------------------------------------------------------------------
    public function getJsonCommentsList()
    {
        return response()->json(Comments::getMyAll());
    }

    public function deleteComment($id)
    {
        Comments::find($id)->delete();
        return response()->json();
    }

    public function updateComment(Request $request, $id)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $text = !empty($request->text) ? $request->text : "";
        $name = !empty($request->name) ? $request->name : "";

        if ($text === "") {
            $aErrs[] = "text is empty";
        }

        if (sizeof($aErrs) === 0) {
            $Comment = Comments::find($id);
            $Comment->text = $text;
            $Comment->name = ((!empty($Comment->user_id) && !empty($name)) ? "" : $name);
            $Comment->save();

            $result = true;

        } else {
            $msg = implode("\n", $aErrs);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(Comments::getMyOne($id));
    }

    // ---------------------------------------------------------------------------------------------
    public function getJsonIdeasList()
    {
        return response()->json(Ideas::orderBy('created_at', 'desc')->get());
    }

    public function deleteIdea($id)
    {
        Ideas::find($id)->delete();
        return response()->json();
    }

    public function updateIdea(Request $request, $id)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $status = (!empty($request->status) && in_array($request->status, ['wait', 'ok', 'no'])) ? $request->status : "";
        $text = !empty($request->text) ? $request->text : "";
        $answer = !empty($request->answer) ? $request->answer : "";

        $aAnswer = explode("\r\n", $answer);
        $aAnswer = array_diff($aAnswer, ['']);
        $answer = implode("\r\n", $aAnswer);

        if ($text === "") {
            $aErrs[] = "text is empty";
        }
        if ($status === "") {
            $aErrs[] = "status is empty";
        }

        if (sizeof($aErrs) === 0) {
            $Idea = Ideas::find($id);

            if ($Idea !== null) {
                $Idea->status = $status;
                $Idea->text = $text;
                $Idea->answer = $answer;
                $Idea->save();

                $result = true;

            } else {
                $msg = "данного элемента уже нет";
            }

        } else {
            $msg = implode("\n", $aErrs);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(Ideas::find($id));
    }

    // ---------------------------------------------------------------------------------------------
    public function getJsonPagesList()
    {
        return response()->json(Pages::orderBy('id', 'asc')->get());
    }

    public function updatePage(Request $request, $id)
    {
        $result = false;
        $aErrs = [];
        $msg = "";

        $title = !empty($request->title) ? $request->title : "";
        $meta_keywords = !empty($request->meta_keywords) ? $request->meta_keywords : "";
        $meta_desc = !empty($request->meta_desc) ? $request->meta_desc : "";
        $description = !empty($request->description) ? $request->description : "";

        if (empty($title)) {
            $aErrs[] = "title is empty";
        }

        if (sizeof($aErrs) === 0) {
            $Page = Pages::find($id);
            $Page->title = $title;
            $Page->meta_keywords = $meta_keywords;
            $Page->meta_desc = $meta_desc;
            $Page->description = $description;
            $Page->save();
        } else {
            $msg = implode("\n", $aErrs);
        }

        $result = true;

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(Pages::find($id));
    }

    // ---------------------------------------------------------------------------------------------
    public function updateSitemap()
    {
        $links = [];
        $fomat = '<url><loc>' . config('app.url') . '%s</loc></url>';
        $br = "\n";

        foreach (Products::active()->get() as $val) {
            $links[] = sprintf($fomat, "/" . $val->slug);
        }

        $aXml = ['<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
            implode($br, $links),
            '</urlset>'];
        file_put_contents(public_path() . "/sitemap_products.xml", implode($br, $aXml), LOCK_EX);

        return response()->json();
    }

    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('upload')) {
            abort(404);
        }
        if (!$request->file('upload')->isValid()) {
            abort(404);
        }

        $file = $request->upload;
        $orig_name = $file->getClientOriginalName();
        $imageName = createUniqIntName() . "." . $file->extension();
        $dir_images = config('my_constants.dir_images');
        $output_file_path = "";
        $msg = "";

        if (!$file->getSize()) {
            $msg = 'файл ' . (!empty($orig_name) ? '-' . $orig_name . '-' : "") . ' пустой';
        } elseif (!in_array($file->getMimeType(), ["image/gif", "image/jpeg", "image/png"])) {
            $msg = 'файл ' . (!empty($orig_name) ? '-' . $orig_name . '-' : "") . ' не соответствует формату';
        } elseif (!$file->move($dir_images, $imageName)) {
            $msg = 'файл ' . (!empty($orig_name) ? '-' . $orig_name . '-' : "") . ' не переместился';
        } else {
            $file_path = $dir_images . "/" . $imageName;
            $img = \Image::make($file_path);
            $img->resize(NULL, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            //$img->insert(public_path().'/img/watermark.png', 'bottom-right', 10, 10);
            $img->save($file_path);
            $output_file_path = '/images/' . $imageName;
        }

        exit('<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction("' . $request->get('CKEditorFuncNum') . '","' . $output_file_path . '","' . $msg . '" );</script>');
    }

    public function sendNews()
    {
        $emails = DB::table('subscribers')->pluck('email')->toArray();

        if (sizeof($emails) === 0) {
            return response()->json();
        }

        $last_time_send_news = DB::table('vars')->where('name', 'last_time_send_news')
            ->pluck('value')
            ->first();
        $products = Products::where('created_at', '>', $last_time_send_news)->pluck('id')->toArray();

        if (sizeof($products) === 0) {
            return response()->json();
        }

        $aProducts = [];
        foreach ($products as $id) {
            $aProducts[] = Products::getMyOne($id);
        }

        $email_from = config('my_constants.email_from');
        $email_signature = config('my_constants.email_signature');

        \Mail::send('emails.news', ['aProducts' => $aProducts],
            function ($message) use ($emails, $email_from, $email_signature) {
                if (config('app.debug') === false) {
                    $message->from($email_from, $email_signature);
                }
                $message->to($emails)
                    ->subject($email_signature . ' - обновление');
            });

        return response()->json();
    }

    public function logout()
    {
        session()->forget('admin');

        return redirect('/');
    }

    public function clearCache()
    {
        cache()->flush();

        return response()->json();
    }
}