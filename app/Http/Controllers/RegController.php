<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon;

use App\Models\Profiles;
use App\Models\Pages;

class RegController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $result = false;
            $email = trim($request->email);
            $pass = trim($request->pass);
            $pass_c = trim($request->pass_c);
            $name = trim($request->name);
            $agree = $request->agree;
            $aErrors = [];

            if (empty($email)) {
                $aErrors[] = 'укажите е-мэйл';
            }
            if (empty($pass)) {
                $aErrors[] = 'укажите пароль';
            }
            if (empty($pass_c)) {
                $aErrors[] = 'укажите пароль (повтор)';
            }
            if (empty($agree)) {
                $aErrors[] = 'подтвердите согласие с правилами';
            }

            if (sizeof($aErrors) === 0) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $aErrors[] = 'укажите корректный е-мэйл';
                }
                if (mb_strlen($pass) < 5) {
                    $aErrors[] = 'пароль слишком короткий, укажите мин. 5 символов';
                }
                if ($pass !== $pass_c) {
                    $aErrors[] = 'пароли не совпадают';
                }

                if (sizeof($aErrors) === 0) {
                    if (Profiles::where([['email', '=', $email], ['key_check_email', '!=', '']])->count() === 0) {
                        if (Profiles::where('email', $email)->count() === 0) {
                            $secret = str_random(32);

                            $profile = new Profiles();
                            $profile->email = $email;
                            $profile->pass = \Hash::make($pass);
                            $profile->name = e(strip_tags($name));
                            $profile->key_check_email = $secret;
                            $profile->save();

                            $email_from = config('my_constants.email_from');
                            $email_signature = config('my_constants.email_signature');

                            \Mail::send('emails.confirm_email',
                                [
                                    'secret' => $secret,
                                    'name' => $profile->name,
                                    'email' => $email
                                ],
                                function ($message) use ($email, $email_from, $email_signature) {
                                    if (config('app.debug') === false) {
                                        $message->from($email_from, $email_signature);
                                    }

                                    $message->to($email)
                                        ->subject('Подтвердите ваш е-мэйл на ' . $email_signature);
                                });

                            $request->session()->flash('ok', '<p>Профиль создан. На Вашу почту (' . $email . ') был выслан секретный ключ, в виде ссылки, пройдите по ней, чтобы подтвердить эл. адрес.</p>');
                            $result = true;

                        } else {
                            $request->session()->flash('errors', ['войдите под своей учетной записью']);
                        }

                    } else {
                        $request->session()->flash('errors', ['е-мэйл ждет подтверждения']);
                    }

                } else {
                    $request->session()->flash('errors', $aErrors);
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
        if (cache()->has('infoPageReg')) {
            $Page = cache('infoPageReg');

        } else {
            $Page = Pages::find(6);
            cache(['infoPageReg' => $Page], Carbon::now()->addMinutes($cache_time));
        }

        return view('reg', [
            'title' => $Page->title,
            'meta_keywords' => $Page->meta_keywords,
            'meta_desc' => $Page->meta_desc,
            'description' => $Page->description
        ]);
    }

    public function confirmEmail($secret)
    {
        $profile = Profiles::where('key_check_email', $secret)->firstOrFail();
        $profile->key_check_email = "";
        $profile->save();

        return view('confirmed_email', [
            'email' => $profile->email
        ]);
    }
}
