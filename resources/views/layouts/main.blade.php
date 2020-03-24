<?
use App\Models\Cats;
?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" 	content="IE=edge">
    <meta name="viewport" 				content="width=device-width, initial-scale=1">
	<meta name="csrf-token"				content="{{ csrf_token() }}">
    <meta name="keywords" 				content="{{ $meta_keywords or "" }}">
    <meta name="description" 			content="{{ $meta_desc or "" }}">
    <meta name="yandex-verification"	content="aeeaf1371fa77047" />
	
	<link rel="icon" type="image/x-icon"   href="/img/favicon.png" />
    
	<link rel="stylesheet" type="text/css" href="/vendor/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="/vendor/slick-1.6.0/slick/slick.css"/>
	<link rel="stylesheet" type="text/css" href="/vendor/slick-1.6.0/slick/slick-theme.css"/>
    
	<link rel="stylesheet" type="text/css" href="/css/all.min.css" />
	
    <title>{{ $title or "" }}</title>
</head>
<body>
	<header>
    	<table cellpadding="0" cellspacing="0" width="100%">
    		<tr height="70">
				<td class="is-relative" rowspan="2" width="150">
					<a id="logo" href="/" 
					   alt="{{ config('my_constants.domain') }}"><img src="/img/logo-v2.png" /></a>
                </td>
    			<td width="20" rowspan="2"></td>
    			<td width="*">
                	<form class="form-top-search" action="/search">
						<i class="fa fa-cog fa-spin"></i>
						<div class="form-top-search-input-block">
							<input class="my-input" type="text" name="q" placeholder="поиск" />
							<ul class="form-top-search-pop-up"></ul>
						</div>
						<button class="my-btn" type="submit"><i class="fa fa-search"></i></button>
                    </form>
					<br />
					<small id="slogan">
						<span class="text-white">{{ config('my_constants.domain') }}</span> - только лучшее
					</small>
					
					@if( !is_null(Cookie::get("my_favorites")) )
						<a id="a-my-favorites" class="a-reverse-line" href="/my-favorites">
							<i class="fa fa-star"></i> 
							Мои избранные фильмы
							<span>- {{ sizeof(explode("|", Cookie::get("my_favorites"))) }}</span>
						</a>
					@else
						<a id="a-my-favorites" class="a-gray-md a-reverse-line" href="/my-favorites">
							<i class="fa fa-star"></i> 
							Мои избранные фильмы
							<span></span>
						</a>	
					@endif
					
                </td>
    		</tr>
            <tr>
            	<td align="right" valign="bottom">
					<nav>
						<ul>
							@if(str_replace(url('/'), "", url()->current()) !== "")
								<li class="top-menu-pop-up-parent">
									<a class="a-top-menu" href="/cats">Категории</a>
									<div class="top-menu-pop-up" align="left">
										@php
											if(cache()->has('catsWhoHasTotal')){
												$aCats = cache('catsWhoHasTotal');

											} else {
												$aCats = Cats::getAllWhoHasTotal();
												cache(['catsWhoHasTotal' => $aCats], 
													  Carbon::now()->addMinutes(config('my_constants.cache_time')));
											}
										@endphp

										@foreach($aCats as $val)
											<a class="a-gray-md" href="/cats/{{ $val->slug }}">{{ $val->name }}</a>

											@if($loop->last === false)
												<br />
											@endif
										@endforeach
									</div>	
								</li>
							@endif
							
							<li>
								<a class="a-top-menu" href="/your-ideas">Ваши предложения</a>	
							</li>
							
							@if(session()->has('user_id'))
								<li class="top-menu-pop-up-parent">
									<a class="a-top-menu">Профиль</a>
									<div class="top-menu-pop-up" align="right">
										<a class="a-gray-md" href="/profile/settings">настройки</a>
										<br />
										<a class="a-gray-md" href="/profile/exit">выход</a>
									</div>	
								</li>
							@else
								<li class="top-menu-pop-up-parent">
									<a class="a-top-menu">Вход</a>
									<div class="top-menu-pop-up">
										<form id="form-login" onsubmit="return false;">
											<input class="my-input" type="text" name="email" value="" placeholder="Е-мэйл" required maxlength="255" />
											<br />
											<input class="my-input" type="password" name="pass" value="" placeholder="Пароль" required />
											<br />
											<br />
											<input class="hide" type="reset" />
											<button class="my-btn btn-block" onclick="login(this)">Войти в личный кабинет</button>
											<br />
											<div>
												<span class="pull-left">
													<input type="checkbox" name="remember" />
													<small class="text-muted">Запомнить</small>
												</span>
												<span class="pull-right">
													<small><a class="a-gray-md" href="/login/recover-pass">Забыли пароль?</a></small>
												</span>
											</div>
											<br />
											<br />
											<hr class="hr-lighten" />
											<center>
												или
												<br />
												<a class="a-gray-md" href="/reg">Зарегистрироваться</a>	
											</center>	
										</form>
									</div>
								</li>
							@endif
                        </ul>
                    </nav>
                </td>
            </tr>
    	</table>
    </header>
	<main>
		@yield('content')
	</main>
	<footer>
		<div class="as-tbl-100">
			<div class="as-tbl-cell" align="left">
				<span class="text-muted">Тех. поддержка:</span> 
				<a class="a-gray-sm a-reverse-line" 
				   href="mailto:{{ config("my_constants.email_from") }}">{{ config("my_constants.email_from") }}</a>
			</div>
			<div class="as-tbl-cell" align="center">
				<a class="a-gray-sm" href="/infos/pravoobladatelyam">Правообладателям</a>
				<br />
				<a class="a-gray-sm" href="/infos">Статьи</a>
			</div>
			<div class="as-tbl-cell" align="right">
				<span class="text-muted">
					{{ config("my_constants.domain") }} 
					@ 
					{{ date("Y") != 2017? "2017-".date("Y"): date("Y") }}
				</span>
			</div>
		</div>
	</footer>
	<div id="preloader"><i class="fa fa-refresh fa-spin fa-fw text-danger"></i></div>
</body>

@if(config('app.debug'))
	<script data-main="/js/public/main" src="/vendor/require/require.js"></script>

@else
	<script src="/js/public/all.js"></script>
	
	@if(!empty($show_metrik))
		<!-- Yandex.Metrika counter --> <script type="text/javascript"> (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter42699084 = new Ya.Metrika({ id:42699084, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/42699084" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
		
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-51904411-4', 'auto');
			ga('send', 'pageview');
		</script>
	@endif
@endif

</html>