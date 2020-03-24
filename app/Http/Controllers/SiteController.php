<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 121);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon;

use App\Models\Cats;
use App\Models\Products;
use App\Models\Humans;
use App\Models\Infos;
use App\Models\InfoCats;
use App\Models\Comments;
use App\Models\Profiles;
use App\Models\Ideas;
use App\Models\Pages;

use App\MyClasses\Mediafire;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkMyCookie')
            ->only('index',
                'cats',
                'infos',
                'product',
                'search',
                'yourIdeas');
        // except
    }

    public function index()
    {
        $cache_time = config('my_constants.cache_time');

        // почистим видео (now plaing) каторые уже не актуальные (старше 3 часов)
        DB::table('now_playing')->where('ts', '<', date("Y-m-d H:i:s", time() - 3600 * 3))->delete();

        if (cache()->has('catsWhoHasTotal')) {
            $aCats = cache('catsWhoHasTotal');

        } else {
            $aCats = Cats::getAllWhoHasTotal();
            cache(['catsWhoHasTotal' => $aCats], Carbon::now()->addMinutes($cache_time));
        }

        if (cache()->has('productsLast')) {
            $aLastProducts = cache('productsLast');

        } else {
            $aLastProducts = Products::getLast(5);
            cache(['productsLast' => $aLastProducts], Carbon::now()->addMinutes($cache_time));
        }

        // информация о данной странице
        if (cache()->has('infoPageIndex')) {
            $Page = cache('infoPageIndex');

        } else {
            $Page = Pages::find(1);
            cache(['infoPageIndex' => $Page], Carbon::now()->addMinutes($cache_time));
        }

        return view('index', [
            'aCats' => $aCats,
            'aLastProducts' => $aLastProducts,
            'aNowWatchProducts' => Products::getNowWatch(),
            'show_metrik' => 1,

            'title' => $Page->title,
            'meta_keywords' => $Page->meta_keywords,
            'meta_desc' => $Page->meta_desc,
            'description' => $Page->description
        ]);
    }

    public function cats(Request $request, $slug = "")
    {
        $cache_time = config('my_constants.cache_time');
        $cat = null;

        if (!empty($slug)) {
            $cat = Cats::where('slug', $slug)->firstOrFail();

            if (cache()->has('productsAllThumbFromCat_' . $cat->id)) {
                $aProducts = cache('productsAllThumbFromCat_' . $cat->id);

            } else {
                $aProducts = Products::getFromCat($cat->id);
                cache(['productsAllThumbFromCat_' . $cat->id => $aProducts], Carbon::now()->addMinutes($cache_time));
            }

            $title = $cat->title;
            $meta_keywords = "";
            $meta_desc = str_limit(strip_tags($cat->description), 255);
            $description = $cat->description;

        } else {
            if (cache()->has('productsAllThumb')) {
                $aProducts = cache('productsAllThumb');

            } else {
                $aProducts = Products::getMyAll();
                cache(['productsAllThumb' => $aProducts], Carbon::now()->addMinutes($cache_time));
            }

            // информация о данной странице
            if (cache()->has('infoPageCatIndex')) {
                $Page = cache('infoPageCatIndex');

            } else {
                $Page = Pages::find(2);
                cache(['infoPageCatIndex' => $Page], Carbon::now()->addMinutes($cache_time));
            }

            $title = $Page->title;
            $meta_keywords = $Page->meta_keywords;
            $meta_desc = $Page->meta_desc;
            $description = $Page->description;
        }

        if (cache()->has('catsWhoHasTotal')) {
            $aCats = cache('catsWhoHasTotal');

        } else {
            $aCats = Cats::getAllWhoHasTotal();
            cache(['catsWhoHasTotal' => $aCats], Carbon::now()->addMinutes($cache_time));
        }

        return view('cats', [
            'cat' => $cat,
            'aProducts' => $aProducts,
            'show_metrik' => 1,

            'title' => $title,
            'meta_keywords' => $meta_keywords,
            'meta_desc' => $meta_desc,
            'description' => $description
        ]);
    }

    public function infos($mixed = "")
    {
        $cache_time = config('my_constants.cache_time');

        if (cache()->has('infosTree')) {
            $infosTree = cache('infosTree');

        } else {
            InfoCats::getAsTreeHtml(InfoCats::getAsTree(), $infosTree);
            cache(['infosTree' => $infosTree], Carbon::now()->addMinutes($cache_time));
        }

        if (cache()->has('infoItem_' . $mixed)) {
            $data = cache('infoItem_' . $mixed);

        } else {
            $data = Infos::findViaSlug($mixed);
            cache(['infoItem_' . $mixed => $data], Carbon::now()->addMinutes($cache_time));
        }

        // если это не статья
        if (is_null($data['post'])) {
            // если это самая главная страница
            if (is_null($data['folder'])) {
                // информация о данной странице
                if (cache()->has('infoPageInfosIndex')) {
                    $Page = cache('infoPageInfosIndex');

                } else {
                    $Page = Pages::find(4);
                    cache(['infoPageInfosIndex' => $Page], Carbon::now()->addMinutes($cache_time));
                }

                $title = $Page->title;
                $meta_keywords = $Page->meta_keywords;
                $meta_desc = $Page->meta_desc;
                $description = $Page->description;

            } else {
                $title = $data['folder']->name;
                $meta_keywords = "";
                $meta_desc = str_limit(strip_tags($data['folder']->description), 255);
                $description = $data['folder']->description;
            }

        } else {
            $title = $data['post']->title;
            $meta_keywords = "";
            $meta_desc = str_limit(strip_tags($data['post']->description), 255);
            $description = "";
        }

        return view('infos', [
            'tree' => $infosTree,
            'data' => $data,

            'title' => $title,
            'meta_keywords' => $meta_keywords,
            'meta_desc' => $meta_desc,
            'description' => $description
        ]);
    }

    public function admin(Request $request)
    {
        if (session()->has('admin')) {
            return redirect('/admin/products');
        }

        if ($request->isMethod('post')) {
            $aErrors = [];
            $login = $request->login;
            $pass = $request->pass;

            if (empty($login)) {
                $aErrors[] = "укажите логин";
            }
            if (empty($pass)) {
                $aErrors[] = "укажите пароль";
            }

            if (sizeof($aErrors) === 0) {
                if (
                    $login === config('my_constants.admin_login') &&
                    $pass === config('my_constants.admin_pass')
                ) {
                    session(['admin' => 1]);

                    return redirect('/admin/products');

                } else {
                    $request->session()->flash('errors', ["не верная пара логин/пароль"]);
                }

            } else {
                $request->session()->flash('errors', $aErrors);
            }

            return back()->withInput();
        }

        return view('admin_login');
    }

    public function product($product_slug)
    {
        $cache_time = config('my_constants.cache_time');
        $product = Products::where('slug', $product_slug)->active()->firstOrFail();

        // надо зафиксировать просмотр
        $ses_id = session()->getId();
        $count = DB::table('see')->where([
            ['ses_id', '=', $ses_id],
            ['el_id', '=', $product->id]
        ])->count();
        if ($count === 0) {
            DB::table('see')->insert(
                ['ses_id' => $ses_id, 'el_id' => $product->id, 'opt' => 'product']
            );
        }

        // возьмем актуальные данные
        if (cache()->has('productFull_' . $product->id)) {
            $product = cache('productFull_' . $product->id);

        } else {
            $product = Products::getMyOne($product->id, true, true, true);
            cache(['productFull_' . $product->id => $product], Carbon::now()->addMinutes($cache_time));
        }

        // зафиксируем просмотр данного фильма, предворительно удалим старую метку
        DB::table('now_playing')->where([
            ['product_id', '=', $product->id],
            ['ses_id', '=', $ses_id]
        ])->delete();

        DB::table('now_playing')->insert([
            'product_id' => $product->id,
            'ses_id' => $ses_id
        ]);
        //\

        $title = $product->name;
        $meta_keywords = "";
        $meta_desc = str_limit(strip_tags($product->description), 255);
        $description = "";

        return view('product', [
            'product' => $product,
            'comments' => Comments::getMyAllForEl($product->id, 'product'),
            'stat' => Products::getStatistic($product->id),
            'show_metrik' => 1,

            'title' => $title,
            'meta_keywords' => $meta_keywords,
            'meta_desc' => $meta_desc,
            'description' => $description
        ]);
    }

    public function search(Request $request)
    {
        $cache_time = config('my_constants.cache_time');
        $is_serched = false;
        $q = e(strip_tags($request->q));

        $aProducts = [];

        if (!empty($q)) {
            $aProducts = Products::search($q);
            $is_serched = true;
        }

        // информация о данной странице
        if (cache()->has('infoPageSearch')) {
            $Page = cache('infoPageSearch');

        } else {
            $Page = Pages::find(3);
            cache(['infoPageSearch' => $Page], Carbon::now()->addMinutes($cache_time));
        }

        return view('search', [
            'aProducts' => $aProducts,
            'is_serched' => $is_serched,

            'title' => $Page->title,
            'meta_keywords' => $Page->meta_keywords,
            'meta_desc' => $Page->meta_desc,
            'description' => $Page->description
        ]);
    }

    public function yourIdeas()
    {
        $cache_time = config('my_constants.cache_time');

        // информация о данной странице
        if (cache()->has('infoPageIdeas')) {
            $Page = cache('infoPageIdeas');

        } else {
            $Page = Pages::find(7);
            cache(['infoPageIdeas' => $Page], Carbon::now()->addMinutes($cache_time));
        }

        return view('your_ideas', [
            'ideas' => Ideas::orderBy('created_at', 'desc')->get(),

            'title' => $Page->title,
            'meta_keywords' => $Page->meta_keywords,
            'meta_desc' => $Page->meta_desc,
            'description' => $Page->description
        ]);
    }

    public function download($id)
    {
        $id = (!empty($id) && is_numeric($id) && ($id > 0) && ($id < PHP_INT_MAX)) ? abs(intval($id)) : 0;

        if ($id === 0) {
            http_response_code(404);
            exit;
        }

        $Product = Products::findOrFail($id);
        $Mediafire = new Mediafire();

        try {
            $aRes = $Mediafire->getSession();

        } catch (\Exception $e) {
            http_response_code(404);
            exit;
        }

        if (!empty($aRes["session_token"]) && !empty($aRes["secret_key"]) && !empty($aRes["time"])) {
            try {
                $aLinks = $Mediafire->getLinks($Product->link, $aRes["session_token"],
                    $aRes["secret_key"],
                    $aRes["time"]);
            } catch (\Exception $e) {
                http_response_code(404);
                exit;
            }

        } else {
            http_response_code(404);
            exit;
        }

        if (empty($aLinks['direct_download'])) {
            http_response_code(404);
            exit;
        }

        $file = $aLinks['direct_download'];
        $header = get_headers($file, 1);

        if (ob_get_level() === 0) {
            ob_start();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: video/mp4');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $header['Content-Length']);

        readfile($file);

        exit;
    }

    public function updateSee()
    {
        $rows = DB::table('see')->where('ts', '<', date("Y-m-d H:i:s", time() - 86400))->get();

        if ($rows->isEmpty()) {
            return;
        }

        // создадим порядок для удобства
        $aProducts = [];
        foreach ($rows as $val) {
            if (isset($aProducts[$val->el_id])) {
                $aProducts[$val->el_id]++;

            } else {
                $aProducts[$val->el_id] = 1;
            }
        }
        // обновим данные в таблице
        foreach ($aProducts as $id => $val) {
            DB::table('products')->where('id', $id)->increment('see', $val);
        }

        DB::table('see')->where('ts', '<', date("Y-m-d H:i:s", time() - 86400))->delete();
    }

    public function updateInQueue()
    {
        $Product = Products::where('in_queue', 1)->orderBy('created_at', 'asc')
            ->first();

        if (is_null($Product) === false) {
            $Product->in_queue = 0;
            $Product->save();

            // обновим sitemap
            app('App\Http\Controllers\AdminController')->updateSitemap();
        }
    }

    public function myFavorites()
    {
        $cache_time = config('my_constants.cache_time');
        $aProducts = [];

        if (!is_null(\Cookie::get("my_favorites"))) {
            $aIds = explode("|", \Cookie::get("my_favorites"));

            foreach ($aIds as $id) {
                if (is_numeric($id) && ($id > 0)) {
                    $tmp = Products::getMyOne($id);

                    if (is_null($tmp) === false) {
                        $aProducts[] = $tmp;
                    }
                }
            }
        }

        return view('my_favorites', [
            'aProducts' => $aProducts,

            'title' => "Мои избранные фильмы",
            'meta_keywords' => "",
            'meta_desc' => "",
            'description' => ""
        ]);
    }

    public function ajaxToggleLike(Request $request)
    {
        if ($request->ajax() === false) {
            abort(404);
        }

        $is_up = !empty($request->is_up) ? 1 : 0;
        $opt = $request->opt;
        $el_id = $request->el_id;
        $user_id = session()->has('user_id') ? session('user_id') : 0;
        $ses_id = session()->getId();
        $aSearch = [['el_id', '=', $el_id], ['opt', '=', $opt]];
        $aInsert = ['el_id' => $el_id, 'opt' => $opt, 'is_up' => $is_up];

        if ($user_id) {
            $aSearch[] = ['user_id', '=', $user_id];
            $aInsert['user_id'] = $user_id;

        } else {
            $aSearch[] = ['ses_id', '=', $ses_id];
            $aInsert['ses_id'] = $ses_id;
        }

        $row = DB::table('likes')->where($aSearch)->first();

        // если пользователь еще не лайкал, то создадим запись
        if (is_null($row)) {
            DB::table('likes')->insert($aInsert);

        } else {
            // убрать в любом случае
            DB::table('likes')->where($aSearch)->delete();

            // если данные различны
            if ($row->is_up !== $is_up) {
                DB::table('likes')->insert($aInsert);
            }
        }

        return response()->json();
    }

    public function ajaxSearch(Request $request)
    {
        if ($request->ajax() === false) {
            abort(404);
        }

        $result = true;
        $q = e(strip_tags($request->q));

        if (!empty($q) && mb_strlen($q) > 2) {
            $msg = Products::select('slug', 'name')->where('name', 'LIKE', '%' . $q . '%')
                ->active()
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }

        return response()->json(['result' => $result, 'msg' => $msg]);
    }

    public function ajaxAddComment(Request $request)
    {
        if ($request->ajax() === false) {
            abort(404);
        }

        $result = false;
        $msg = "";
        $aErrors = [];

        $el_id = $request->el_id;
        $opt = $request->opt;
        $text = e(strip_tags($request->text));
        $aText = explode("\r\n", $text);
        $aText = array_diff($aText, ['']);
        $text = implode("\r\n", $aText);

        if (!empty(session('user_id'))) {
            $user_id = session('user_id');
            $email = Profiles::find($user_id)->email;
            $name = "";

        } else {
            $user_id = 0;
            $email = $request->email;
            $name = e(strip_tags($request->name));

            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $aErrors[] = 'укажите корректный е-мэйл';
            }
            if (mb_strlen($name) > 50) {
                $name = str_limit($name, 50);
            }
        }

        if (empty($text)) {
            $aErrors[] = 'впишите комментарий';
        }
        if (empty($el_id)) {
            $aErrors[] = 'не известен el_id';
        }
        if (empty($opt) || !in_array($opt, ['product'])) {
            $aErrors[] = 'не извенстна опция';
        }

        if (sizeof($aErrors) === 0) {
            $comment = new Comments();
            $comment->text = $text;
            $comment->user_id = $user_id;
            $comment->ses_id = session()->getId();
            $comment->name = $name;
            $comment->email = $email;
            $comment->ip = $request->ip();
            $comment->el_id = $el_id;
            $comment->opt = $opt;
            $comment->save();

            $result = true;

            $view = view('modules.comment_item', ['item' => Comments::getMyOne($comment->id)]);
            $msg = $view->render();

        } else {
            $msg = implode("\n", $aErrors);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(['result' => $msg]);
    }

    public function ajaxRemoveComment(Request $request)
    {
        if ($request->ajax() === false) {
            abort(404);
        }

        $result = false;
        $msg = "";
        $aErrors = [];

        $el_id = abs(intval($request->el_id));
        $opt = $request->opt;

        if (empty($el_id)) {
            $aErrors[] = 'не известен el_id';
        }
        if (empty($opt) || !in_array($opt, ['product'])) {
            $aErrors[] = 'не извенстна опция';
        }

        if (sizeof($aErrors) === 0) {
            $comment = Comments::getMyOne($el_id);

            if (is_null($comment) === false) {
                if (
                    (session()->has('user_id') && $comment->user_id === session('user_id'))
                    ||
                    ($comment->ses_id === session()->getId())
                ) {

                    $comment->delete();
                    $result = true;

                } else {
                    $msg = 'комментарий не принадлежит Вам';
                }

            } else {
                $msg = 'комментария с таким id нет';
            }

        } else {
            $msg = implode("\n", $aErrors);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(['result' => $msg]);
    }

    public function ajaxAddIdea(Request $request)
    {
        if ($request->ajax() === false) {
            abort(404);
        }

        $result = false;
        $msg = "";
        $aErrors = [];

        $text = e(strip_tags($request->text));
        $aText = explode("\r\n", $text);
        $aText = array_diff($aText, ['']);
        $text = implode("\r\n", $aText);

        if (empty($text)) {
            $aErrors[] = 'впишите рекомендацию';
        }

        if (sizeof($aErrors) === 0) {
            $idea = new Ideas();
            $idea->text = $text;
            $idea->ses_id = session()->getId();
            $idea->ip = $request->ip();
            $idea->save();

            $result = true;

            $view = view('modules.idea_item', ['item' => Ideas::find($idea->id)]);
            $msg = $view->render();

        } else {
            $msg = implode("\n", $aErrors);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(['result' => $msg]);
    }

    public function ajaxRemoveIdea(Request $request)
    {
        if ($request->ajax() === false) {
            abort(404);
        }

        $result = false;
        $msg = "";
        $aErrors = [];

        $id = abs(intval($request->id));

        if (empty($id)) {
            $aErrors[] = 'не известен id';
        }

        if (sizeof($aErrors) === 0) {
            $idea = Ideas::find($id);

            if (is_null($idea) === false) {
                if ($idea->ses_id === session()->getId()) {

                    $idea->delete();
                    $result = true;

                } else {
                    $msg = 'рекомендация не принадлежит Вам';
                }

            } else {
                $msg = 'рекомендации с таким id нет';
            }

        } else {
            $msg = implode("\n", $aErrors);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json(['result' => $msg]);
    }

    public function ajaxSetAccessForNowPlaying(Request $request)
    {
        if ($request->ajax() === false) {
            abort(404);
        }

        $id = $request->product_id;

        if (!empty($id) && is_numeric($id) && $id > 0) {
            // интервал в секундах, свидетельствующий о том что фильм действительно смотрят
            $interval = config('my_constants.time_interval_for_now_playing');

            DB::table('now_playing')->where([
                ['product_id', '=', $id],
                ['ses_id', '=', session()->getId()],
                ['ts', '<', date("Y-m-d H:i:s", time() - $interval)]
            ])
                ->update(['has_interval' => 1]);
        }

        return response()->json();
    }

    public function ajaxAddSubscriber(Request $request)
    {
        if ($request->ajax() === false) {
            abort(404);
        }

        $result = false;
        $msg = "";
        $aErrors = [];

        $email = $request->email;

        if (empty($email)) {
            $aErrors[] = 'укажите е-мэйл';

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $aErrors[] = 'укажите корректный е-мэйл';
        }

        if (sizeof($aErrors) === 0) {
            $count = DB::table('subscribers')->where('email', $email)->count();

            if ($count === 0) {
                DB::table('subscribers')->insert(['email' => $email]);
                $result = true;

            } else {
                $msg = "данный е-мэйл уже присутствует в базе";
            }

        } else {
            $msg = implode("\n", $aErrors);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        return response()->json();
    }

    public function ajaxToggleInMyFavorites(Request $request, $id)
    {
        if ($request->ajax() === false) {
            abort(404);
        }

        $result = false;
        $msg = "";

        //1. надо проверить: есть ли такой в базе id
        if (Products::where('id', $id)->count()) {
            $array = [];
            $value = $request->cookie('my_favorites');

            // если кука есть
            if (is_null($value) === false) {
                $array = explode("|", $value);

                // елси id есть, то уберем ее, значит пользователь решил удалить из избранного
                if (in_array($id, $array)) {
                    $tmp = [];
                    foreach ($array as $val) {
                        if ($val != $id) {
                            $tmp[] = $val;
                        }
                    }
                    $array = $tmp;

                } else {
                    $array[] = $id;
                }

            } else {
                $array[] = $id;
            }

            $result = true;

        } else {
            $msg = "такого id в базе нет";
        }

        if (!$result) {
            return response()->json(['error' => $msg], 400);
        }

        if (sizeof($array)) {
            return response()->json([
                'total' => sizeof($array)
            ])->cookie('my_favorites', implode("|", $array), 60 * 24 * 365);

        } else {
            return response()->json([
                'total' => 0
            ])->cookie('my_favorites', "", -1);
        }
    }
}