<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" 	content="IE=edge">
    <meta name="viewport" 				content="width=device-width, initial-scale=1">
    <meta name="csrf-token"				content="{{ csrf_token() }}">
	
	<link rel="icon" type="image/x-icon"   href="/img/favicon.png" />
	
	<link type="text/css" rel="stylesheet" href="/vendor/font-awesome-4.7.0/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="/css/all.min.css" />
	
    <title>Администрирование</title>
</head>

<body id="admin">
	<header>
		<br />
		<br />
		<nav>
			<table cellspacing="0"  cellpadding="0" width="100%">
				<tr>
					<td align="left" valign="bottom">
						<ul class="pull-right">
							<li>
								<a class="a-top-menu" href="/" title="на главную">
									<i class="fa fa-home fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/products" title="фильмы">
									<i class="fa fa-film fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/humans" title="актеры">
									<i class="fa fa-address-card fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/infos" title="информация">
									<i class="fa fa-info-circle fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/info-cats" title="категории информации">
									<i class="fa fa-list-alt fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/cats" title="категории фильмов">
									<i class="fa fa-indent fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/countries" title="страны">
									<i class="fa fa-globe fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/users" title="пользователи">
									<i class="fa fa-users fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/comments" title="комментарии">
									<i class="fa fa-comments fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/ideas" title="рекомендации">
									<i class="fa fa-lightbulb-o fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/quality-videos" title="качество видео">
									<i class="fa fa-video-camera fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/quality-dubbings" title="озвучивание">
									<i class="fa fa-volume-up fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/pages" title="страницы">
									<i class="fa fa-files-o fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu a-change-route" href="/admin/etc" title="разное">
									<i class="fa fa-ellipsis-h fa-fw"></i>
								</a>
							</li>
							<li>
								<a class="a-top-menu" href="/admin/exit" title="выход">
									<i class="fa fa-sign-out fa-fw"></i>
								</a>
							</li>
						</ul>
					</td>
				</tr>
			</table>
		</nav>
	</header>
	<main id="app"></main>
	<div id="preloader"><i class="fa fa-refresh fa-spin fa-fw text-white"></i></div>
	<br />
</body>
<script type="text/javascript">
	// начальные, частоповторяющиеся данные
	var outer_countries = {!! $countries !!};
	var outer_productCats = {!! $productCats !!};
	var outer_infoCats = {!! $infoCats !!};
	var outer_qualityVideos = {!! $qualityVideos !!};
	var outer_qualityDubbings = {!! $qualityDubbings !!};
	var outer_humans = {!! $humans !!};
</script>

@if(config('app.debug'))
	<script data-main="/js/admin/main" src="/vendor/require/require.js"></script>

@else
	<script src="/vendor/ckeditor/ckeditor.js"></script>
	<script src="/js/admin/all.js"></script>
@endif

</html>