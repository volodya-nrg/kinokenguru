<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profiles;

class ProfileController extends Controller
{
    public function index()
    {
        return redirect('profile/settings');
    }

    public function logout(Request $request)
    {
        session()->forget('user_id');

        if ($request->hasCookie('my_cookie')) {
            return redirect('/')->withCookie(\Cookie::forget('my_cookie'));
        }

        return redirect('/');
    }

    public function settings(Request $request)
    {
        $allowMime = ["image/jpeg", "image/png", "image/gif"];
        $profile = Profiles::findOrFail(session('user_id'));

        if ($request->isMethod('post')) {
            $result = false;
            $msg = "";
            $aErrors = [];
            $dir_images = config('my_constants.dir_images');

            // надо проверить на изменение пароля
            $old_pass = $request->old_pass;
            $new_pass = $request->new_pass;
            $new_pass_confirm = $request->new_pass_confirm;

            if (!empty($old_pass) || !empty($new_pass) || !empty($new_pass_confirm)) {
                if (empty($old_pass)) {
                    $aErrors[] = 'укажите старый пароль';
                }
                if (empty($new_pass)) {
                    $aErrors[] = 'укажите новый пароль';

                } elseif (mb_strlen($new_pass) < 5) {
                    $aErrors[] = 'пароль слишком короткий, укажите мин. 5 символов';
                }
                if (empty($new_pass_confirm)) {
                    $aErrors[] = 'укажите новый пароль (повтор)';
                }

                if (sizeof($aErrors) === 0) {
                    if ($new_pass === $new_pass_confirm) {
                        if (\Hash::check($old_pass, $profile->pass)) {
                            $profile->pass = \Hash::make($new_pass);
                            $profile->name = e(strip_tags($request->name));
                            $profile->save();

                            $result = true;

                        } else {
                            $request->session()->flash('errors', ['старый пароль не правильный']);
                        }

                    } else {
                        $request->session()->flash('errors', ['новые пароли не совпадают']);
                    }

                } else {
                    $request->session()->flash('errors', $aErrors);
                }
            } else {
                $profile->name = e(strip_tags($request->name));
                $profile->save();

                $result = true;
            }

            // делаем обновления с картинками при условии что ранее данные обновились
            if ($result) {
                // если нет определенного параметра и есть аватарка, то удалим аватарку
                if (empty($request->has_avatar)) {
                    $file = $dir_images . "/avatar_" . $profile->id . ".jpg";
                    $file_sm = $dir_images . "/avatar_sm_" . $profile->id . ".jpg";

                    if (is_file($file)) {
                        unlink($file);
                    }
                    if (is_file($file_sm)) {
                        unlink($file_sm);
                    }
                }

                // если есть файл, действительный и это картинка
                if ($request->hasFile('avatar') &&
                    $request->file('avatar')->isValid() &&
                    in_array($request->avatar->getMimeType(), $allowMime)) {

                    $path_tmp = $request->avatar->path();

                    $img = \Image::make($path_tmp)->encode('jpg', 100);
                    $img->resize(null, 300, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img->save($path_tmp, 100);

                    \Image::make($path_tmp)->fit(300, 300)->save($dir_images . '/avatar_' . $profile->id . '.jpg');
                    \Image::make($path_tmp)->fit(100, 100)->save($dir_images . '/avatar_sm_' . $profile->id . '.jpg');
                }

                $request->session()->flash('ok', '<p>Данные сохранены.</p>');
            }

            return back();
        }

        return view('profile.settings', [
            'email' => $profile->email,
            'name' => $profile->name,
        ]);
    }
}
