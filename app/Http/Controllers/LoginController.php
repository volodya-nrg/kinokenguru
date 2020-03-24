<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon;

use App\Models\Profiles;
use App\Models\Pages;

class LoginController extends Controller
{
    public function checkAuth(Request $request)
    {
        $result = false;
        $msg = "";
        $aErrors = [];
        $email = trim($request->email);
        $pass = trim($request->pass);
        $remember = $request->remember;

        if (empty($email)) {
            $aErrors[] = 'укажите е-мэйл';

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $aErrors[] = 'укажите корректный е-мэйл';
        }
        if (empty($pass)) {
            $aErrors[] = 'укажите пароль';
        }

        if (sizeof($aErrors) === 0) {
            if (Profiles::where('email', $email)->count() === 1) {
                $profile = Profiles::where('email', $email)->first();

                if ($profile->key_check_email === "") {
                    if (\Hash::check($pass, $profile->pass)) {

                        if (!empty($remember)) {
                            $time = strtotime('+1 month');
                            $secret = str_random(10);
                            $cookie = cookie('my_cookie', implode("|", [$profile->id, $secret, $time]), $time);

                            $profile->key_cookie = $secret;
                            $profile->save();
                        }

                        session(['user_id' => $profile->id]);
                        $result = true;

                    } else {
                        $msg = 'логин/пароль не правильны';
                    }

                } else {
                    $msg = 'е-мэйл ожидает подтверждения';
                }

            } else {
                $msg = 'такого пользователя у нас нет';
            }

        } else {
            $msg = implode("\n", $aErrors);
        }

        if (!$result) {
            return response()->json(['error' => $msg], 403);
        }

        if (isset($cookie)) {
            return response()->json()->cookie($cookie);

        } else {
            return response()->json();
        }
    }

    public function recoverPass(Request $request)
    {
        if ($request->isMethod('post')) {
            $result = false;
            $aErrors = [];
            $email = $request->email;

            if (empty($email)) {
                $aErrors[] = 'укажите е-мэйл';

            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $aErrors[] = 'укажите корректный е-мэйл';
            }

            if (sizeof($aErrors) === 0) {
                $profile = Profiles::where('email', $email)->first();

                if (!empty($profile)) {
                    if ($profile->key_check_email === "") {
                        $secret = str_random(32);

                        $profile->key_check_pass = $secret;
                        $profile->save();

                        $email_from = config('my_constants.email_from');
                        $email_signature = config('my_constants.email_signature');

                        \Mail::send('emails.recover_pass', ['secret' => $secret, 'email' => $email],
                            function ($message) use ($email, $email_from, $email_signature) {
                                if (config('app.debug') === false) {
                                    $message->from($email_from, $email_signature);
                                }
                                $message->to($email)
                                    ->subject($email_signature . ' - восстановление пароля');
                            });

                        $request->session()->flash('ok', '<p>На е-мэйл (' . $email . ') было выслано письмо. Следуйте инструкциям в письме.</p>');
                        $result = true;

                    } else {
                        $request->session()->flash('errors', ['е-мэйл ждет подтверждения']);
                    }

                } else {
                    $request->session()->flash('errors', ['пользователя, с таким е-мэйлом, нет']);
                }

            } else {
                $request->session()->flash('errors', $aErrors);
            }

            // делаем редирект, чтоб после форма не имела возможности посылаться еще раз
            if ($result === true) {
                return back();

            } else {
                return back()->withInput();
            }
        }

        $cache_time = config('my_constants.cache_time');

        // информация о данной странице
        if (cache()->has('infoPageRecoverPass')) {
            $Page = cache('infoPageRecoverPass');

        } else {
            $Page = Pages::find(5);
            cache(['infoPageRecoverPass' => $Page], Carbon::now()->addMinutes($cache_time));
        }

        return view('recover_pass', [
            'title' => $Page->title,
            'meta_keywords' => $Page->meta_keywords,
            'meta_desc' => $Page->meta_desc,
            'description' => $Page->description
        ]);
    }

    public function setNewPass(Request $request, $secret)
    {
        $profile = Profiles::where('key_check_pass', $secret)->firstOrFail();

        if ($request->isMethod('post')) {
            $result = false;
            $aErrors = [];
            $pass = $request->pass;
            $pass_c = $request->pass_c;

            if (empty($pass)) {
                $aErrors[] = 'укажите пароль';

            } elseif (mb_strlen($pass) < 5) {
                $aErrors[] = 'пароль слишком короткий, укажите мин. 5 символов';
            }
            if (empty($pass_c)) {
                $aErrors[] = 'укажите пароль(повтор)';
            }

            if (sizeof($aErrors) === 0) {
                if ($pass === $pass_c) {
                    $profile->pass = \Hash::make($pass);
                    $profile->save();

                    $request->session()->flash('ok', '<p>Пароль успешно изменен.</p>');
                    $result = true;

                } else {
                    $request->session()->flash('errors', ['пароли не совпадают']);
                }

            } else {
                $request->session()->flash('errors', $aErrors);
            }

            // делаем редирект, чтоб после форма не имела возможности посылаться еще раз
            if ($result === true) {
                return back();

            } else {
                return back()->withInput();
            }
        }

        return view('set_new_pass');
    }
}
