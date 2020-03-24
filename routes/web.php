<?php

use Illuminate\Support\Facades\DB;

use App\Models\Cats;
use App\Models\InfoCats;
use App\Models\Humans;

/* на значения {id, secret, product_slug} - прописаны в файле RouteServiceProvider */

Route::get('/', 'SiteController@index');
Route::get('index', function () {
    return redirect('/');
});
Route::get('cats/{slug?}', 'SiteController@cats')->where('slug', '[a-z][-a-z]*'); // жанры
Route::get('infos/{mixed?}', 'SiteController@infos')->where('mixed', '[a-z0-9][-a-z0-9/]*');
//Route::get('humans/{name?}', 'SiteController@humans')->where('name', '[a-z0-9][-a-z0-9]*');
Route::get('search', 'SiteController@search');
Route::get('download/{id}', 'SiteController@download'); // шаблон назначен
Route::get('{product_slug}', 'SiteController@product'); // шаблон назначен
Route::get('update-see-jkhsdfuwyeruxcvb', 'SiteController@updateSee');
Route::get('update-in-queue-lkjkjzxcbnbnb', 'SiteController@updateInQueue');
Route::get('my-favorites', 'SiteController@myFavorites');

Route::get('your-ideas', 'SiteController@yourIdeas');
Route::post('ajax-add-idea', 'SiteController@ajaxAddIdea');
Route::post('ajax-remove-idea', 'SiteController@ajaxRemoveIdea');

Route::post('search/ajax-search', 'SiteController@ajaxSearch');
Route::post('ajax-add-comment', 'SiteController@ajaxAddComment');
Route::post('ajax-remove-comment', 'SiteController@ajaxRemoveComment');
Route::post('ajax-toggle-like', 'SiteController@ajaxToggleLike');
Route::post('ajax-set-access-for-now-playing', 'SiteController@ajaxSetAccessForNowPlaying');
Route::post('ajax-add-subscriber', 'SiteController@ajaxAddSubscriber');
Route::post('ajax-toggle-in-my-favorites/{id}', 'SiteController@ajaxToggleInMyFavorites');

Route::match(['get', 'post'], 'admin', 'SiteController@admin');

// секция Логин
Route::group(['middleware' => 'redirectToProfileIfHasUserId', 'prefix' => 'login'], function () {
    Route::post('check-auth', 'LoginController@checkAuth');
    Route::match(['get', 'post'], 'recover-pass', 'LoginController@recoverPass');
    Route::match(['get', 'post'], 'set-new-pass/{secret}', 'LoginController@setNewPass');
});

// секция регистрации, сессии пользователя недолжно быть
Route::group(['middleware' => 'redirectToProfileIfHasUserId', 'prefix' => 'reg'], function () {
    Route::match(['get', 'post'], '/', 'RegController@index');
    Route::get('index', function () {
        return redirect('/reg');
    });
    Route::get('confirm-email/{secret}', 'RegController@confirmEmail');
});

// секция пользователя, проверяем на сессию "user_id"
Route::group(['middleware' => 'allowOnlyProfile', 'prefix' => 'profile'], function () {
    Route::get('/', 'ProfileController@index');
    Route::get('index', function () {
        return redirect('/profile');
    });
    Route::match(['get', 'post'], 'settings', 'ProfileController@settings');
    Route::get('exit', 'ProfileController@logout');

    // favorits
    // money
});

// секция админа, проверяем на сессию "admin"
Route::group(['middleware' => 'allowOnlyAdmin', 'prefix' => 'admin'], function () {
    // если это обычный GET запрос, а не аякс
    if (Request::ajax() === false) {

        // тут пропишем только те урлы, каторые нужна только для backbon-а
        $aParam1 = [
            'products',
            'cats',
            'countries',
            'quality-videos',
            'quality-dubbings',
            'humans',
            'infos',
            'info-cats',
            'users',
            'comments',
            'ideas',
            'pages',
            'etc'
        ];
        Route::get('{param_1?}/{param_2?}/{param_3?}', function ($param_1 = "", $param_2 = "", $param_3 = "") {

            // фундамент, подгрузим нужные переменные сразу
            return view('admin', [
                'countries' => json_encode(DB::table('countries')->orderBy('name', 'asc')->get()),
                'productCats' => json_encode(Cats::all()),
                'infoCats' => json_encode(InfoCats::getMyAll(1)),
                'qualityVideos' => json_encode(DB::table('quality_videos')->get()),
                'qualityDubbings' => json_encode(DB::table('quality_dubbings')->get()),
                'humans' => json_encode(Humans::getMyAll())
            ]);

        })->where([
            // тут нужно точное название в переменных, для точной проверки
            'param_1' => '(' . implode("|", $aParam1) . ')',
            'param_2' => '(create|[1-9][0-9]*)',
            'param_3' => 'edit'
        ]);

        // подгрузка картинок в редактор
        Route::post('upload-image', 'AdminController@uploadImage');
        // выход
        Route::get('exit', 'AdminController@logout');

    } else {
        $a = 'AdminController@';

        Route::get('get-json-products-list', $a . 'getJsonProductsList');
        Route::match(['post', 'put'], 'products/{id?}', $a . 'updateProduct');
        Route::delete('products/{id}', $a . 'deleteProduct');

        Route::match(['post', 'put'], 'humans/{id?}', $a . 'updateHuman');
        Route::delete('humans/{id}', $a . 'deleteHuman');

        Route::get('get-json-infos-list', $a . 'getJsonInfosList');
        Route::match(['post', 'put'], 'infos/{id?}', $a . 'updateInfo');
        Route::delete('infos/{id}', $a . 'deleteInfo');

        Route::match(['post', 'put'], 'info-cats/{id?}', $a . 'updateInfoCat');
        Route::delete('info-cats/{id}', $a . 'deleteInfoCat');

        Route::match(['post', 'put'], 'cats/{id?}', $a . 'updateCat');
        Route::delete('cats/{id}', $a . 'deleteCat');

        Route::match(['post', 'put'], 'countries/{id?}', $a . 'updateCountry');
        Route::delete('countries/{id}', $a . 'deleteCountry');

        Route::match(['post', 'put'], 'quality-videos/{id?}', $a . 'updateQualityVideo');
        Route::delete('quality-videos/{id}', $a . 'deleteQualityVideo');

        Route::match(['post', 'put'], 'quality-dubbings/{id?}', $a . 'updateQualityDubbing');
        Route::delete('quality-dubbings/{id}', $a . 'deleteQualityDubbing');

        Route::get('get-json-users-list', $a . 'getJsonUsersList');
        Route::put('users/{id}', $a . 'updateUser');

        Route::get('get-json-comments-list', $a . 'getJsonCommentsList');
        Route::put('comments/{id}', $a . 'updateComment');
        Route::delete('comments/{id}', $a . 'deleteComment');

        Route::get('get-json-ideas-list', $a . 'getJsonIdeasList');
        Route::put('ideas/{id}', $a . 'updateIdea');
        Route::delete('ideas/{id}', $a . 'deleteIdea');

        Route::get('get-json-pages-list', $a . 'getJsonPagesList');
        Route::put('pages/{id}', $a . 'updatePage');

        Route::post('update-sitemap', $a . 'updateSitemap');
        Route::post('send-news', $a . 'sendNews');
        Route::post('clear-cache', $a . 'clearCache');
    }
});