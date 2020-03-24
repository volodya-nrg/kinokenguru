define(['jquery', 'underscore', 'backbone', 'backbone.marionette',
		
		'models/cat',
		'models/country',
		'models/product',
		'models/quality_video',
		'models/quality_dubbing',
		'models/human',
		'models/info',
		'models/info_cat',
		'models/user',
		'models/comment',
		'models/idea',
		'models/page',
		
		'views/cat',
		'views/cats_list',
		'views/country',
		'views/countries_list',
		'views/product',
		'views/products_list',
		'views/quality_video',
		'views/quality_videos_list',
		'views/quality_dubbing',
		'views/quality_dubbings_list',
		'views/human',
		'views/humans_list',
		'views/info',
		'views/infos_list',
		'views/info_cat',
		'views/info_cats_list',
		'views/user',
		'views/users_list',
		'views/comment',
		'views/comments_list',
		'views/idea',
		'views/ideas_list',
		'views/page',
		'views/pages_list',
		
		'views/etc'
		],
	function($,		_,			Bb,			Mn,
		
		CatModel,
		CountryModel,
		ProductModel,
		QualityVideoModel,
		QualityDubbingModel,
		HumanModel,
		InfoModel,
		InfoCatModel,
		UserModel,
		CommentModel,
		IdeaModel,
		PageModel,
		
		CatView,
		CatsListView,
		CountryView,
		CountriesListView,
		ProductView,
		ProductsListView,
		QualityVideoView,
		QualityVideosListView,
		QualityDubbingView,
		QualityDubbingsListView,
		HumanView,
		HumansListView,
		InfoView,
		InfosListView,
		InfoCatView,
		InfoCatsListView,
		UserView,
		UsersListView,
		CommentView,
		CommentsListView,
		IdeaView,
		IdeasListView,
		PageView,
		PagesListView,
		
		EtcView
												){
		var Router = Mn.AppRouter.extend({
			appRoutes: {
				'admin':					'index',
				
				'admin/products':			'productsList',
				'admin/products/create':	'productUpdate',
				'admin/products/:id/edit':	'productUpdate',
								
				'admin/cats':				'catsList',
				'admin/cats/create':		'catUpdate',
				'admin/cats/:id/edit':		'catUpdate',
				
				'admin/countries':			'countriesList',
				'admin/countries/create':	'countryUpdate',
				'admin/countries/:id/edit':	'countryUpdate',
				
				'admin/quality-videos':			'qualityVideosList',
				'admin/quality-videos/create':	'qualityVideoUpdate',
				'admin/quality-videos/:id/edit':'qualityVideoUpdate',
				
				'admin/quality-dubbings':			'qualityDubbingsList',
				'admin/quality-dubbings/create':	'qualityDubbingUpdate',
				'admin/quality-dubbings/:id/edit':	'qualityDubbingUpdate',
				
				'admin/humans':				'humansList',
				'admin/humans/create':		'humanUpdate',
				'admin/humans/:id/edit':	'humanUpdate',
				
				'admin/infos':				'infosList',
				'admin/infos/create':		'infoUpdate',
				'admin/infos/:id/edit':		'infoUpdate',
				
				'admin/info-cats':				'infoCatsList',
				'admin/info-cats/create':		'infoCatUpdate',
				'admin/info-cats/:id/edit':		'infoCatUpdate',
				
				'admin/users':				'usersList',
				'admin/users/:id/edit':		'userUpdate',
				
				'admin/comments':			'commentsList',
				'admin/comments/:id/edit':	'commentUpdate',
				
				'admin/ideas':				'ideasList',
				'admin/ideas/:id/edit':		'ideaUpdate',
				
				'admin/pages':				'pagesList',
				'admin/pages/:id/edit':		'pageUpdate',
				
				'admin/etc':				'etc'
			},
			controller: {
				index: function()
				{
					// console.log('index');
				},
				productsList: function()
				{
					if(window.app.view.productsList === undefined){
						window.app.view.productsList = new ProductsListView();
						window.app.view.productsList.render(); // покажем костяк, куда вставить прелоадер
						window.app.view.productsList.fetchRender(); // загрузили, обновили
					
					} else {
						window.app.view.productsList.render();
					}	
				},
				productUpdate: function(id)
				{
					if(window.app.view.product === undefined){
						window.app.view.product = new ProductView();
					}
					
					// всегда ставим сюда чистую модель, из-за возможности создания новой
					window.app.view.product.model = new ProductModel();
					window.app.view.product.myFiles = [];
					
					// на тот случай если обновили страницу на месте добавления/редактирования
					if(window.app.view.productsList === undefined){
						window.app.view.productsList = new ProductsListView();
						window.app.view.productsList.collection.fetch({
							success: function(){
								/********/
								if(id !== null){
									var m = window.app.view.productsList.collection.get(parseInt(id));
									window.app.view.product.model.attributes = m.attributes;
								}
								window.app.view.product.render();
								/********/
							},
							error: function(){}
						});
					
					} else {
						/********/
						if(id !== null){
							var m = window.app.view.productsList.collection.get(parseInt(id));
							window.app.view.product.model.attributes = m.attributes;
						}
						window.app.view.product.render();
						/********/
					}
				},

				humansList: function()
				{	
					if(window.app.view.humansList === undefined){
						window.app.view.humansList = new HumansListView();
					}
					
					window.app.view.humansList.render();
				},
				humanUpdate: function(id)
				{
					if(window.app.view.human === undefined){
						window.app.view.human = new HumanView();
					}
					
					// всегда ставим сюда чистую модель, из-за возможности создания новой
					window.app.view.human.model = new HumanModel();
					window.app.view.human.myFiles = [];
					
					if(id !== null){
						var m = window.app.col.humans.get(parseInt(id));
						window.app.view.human.model.attributes = m.attributes;
					}
					
					window.app.view.human.render();
				},
				
				infosList: function()
				{	
					if(window.app.view.infosList === undefined){
						window.app.view.infosList = new InfosListView();
						window.app.view.infosList.render(); // покажем костяк, чтоб в него поместить прелоадер
						window.app.view.infosList.fetchRender(); // загрузили, обновили
					
					} else {
						window.app.view.infosList.render();
					}
				},
				infoUpdate: function(id)
				{
					if(window.app.view.info === undefined){
						window.app.view.info = new InfoView();	
					}
					
					// всегда ставим сюда чистую модель, из-за возможности создания новой
					window.app.view.info.model = new InfoModel();
					
					// на тот случай если обновили страницу на месте добавления/редактирования
					if(window.app.view.infosList === undefined){
						window.app.view.infosList = new InfosListView();
						window.app.view.infosList.collection.fetch({
							success: function(){
								/********/
								if(id !== null){
									var m = window.app.view.infosList.collection.get(parseInt(id));
									window.app.view.info.model.attributes = m.attributes;
								}
								window.app.view.info.render();
								/********/
							},
							error: function(){}
						});
					
					} else {
						/********/
						if(id !== null){
							var m = window.app.view.infosList.collection.get(parseInt(id));
							window.app.view.info.model.attributes = m.attributes;
						}
						window.app.view.info.render();
						/********/
					}
				},
				
				infoCatsList: function()
				{	
					if(window.app.view.infoCatsList === undefined){
						window.app.view.infoCatsList = new InfoCatsListView();
					}
					
					window.app.view.infoCatsList.render();
				},
				infoCatUpdate: function(id)
				{
					if(window.app.view.infoCat === undefined){
						window.app.view.infoCat = new InfoCatView();	
					}
					
					window.app.view.infoCat.model = new InfoCatModel();
					
					if(id !== null){
						var m = window.app.col.infoCats.get(parseInt(id));
						window.app.view.infoCat.model.attributes = m.attributes;
					}
					
					window.app.view.infoCat.render();
				},

				catsList: function()
				{
					if(window.app.view.catsList === undefined){
						window.app.view.catsList = new CatsListView();
					}
					
					window.app.view.catsList.render();
				},
				catUpdate: function(id)
				{
					if(window.app.view.cat === undefined){
						window.app.view.cat = new CatView();	
					}
					
					// всегда ставим сюда чистую модель, из-за возможности создания новой
					window.app.view.cat.model = new CatModel();
					
					if(id !== null){
						var m = window.app.col.productCats.get(parseInt(id));
						window.app.view.cat.model.attributes = m.attributes;
					}
					
					window.app.view.cat.render();
				},

				countriesList: function()
				{
					if(window.app.view.countriesList === undefined){
						window.app.view.countriesList = new CountriesListView();
					}
					
					window.app.view.countriesList.render();
				},
				countryUpdate: function(id)
				{
					if(window.app.view.country === undefined){
						window.app.view.country = new CountryView();
					}
					
					// всегда ставим сюда чистую модель, из-за возможности создания новой
					window.app.view.country.model = new CountryModel();
					
					if(id !== null){
						var m = window.app.col.countries.get(parseInt(id));
						window.app.view.country.model.attributes = m.attributes;
					}
					
					window.app.view.country.render();
				},

				qualityVideosList: function()
				{
					if(window.app.view.qualityVideosList === undefined){
						window.app.view.qualityVideosList = new QualityVideosListView();
					}
					
					window.app.view.qualityVideosList.render();
				},
				qualityVideoUpdate: function(id)
				{
					if(window.app.view.qualityVideo === undefined){
						window.app.view.qualityVideo = new QualityVideoView();
					}
					
					// всегда ставим сюда чистую модель, из-за возможности создания новой
					window.app.view.qualityVideo.model = new QualityVideoModel();
					
					if(id !== null){
						var m = window.app.col.qualityVideos.get(parseInt(id));
						window.app.view.qualityVideo.model.attributes = m.attributes;
					}
					
					window.app.view.qualityVideo.render();
				},
				
				qualityDubbingsList: function()
				{
					if(window.app.view.qualityDubbingsList === undefined){
						window.app.view.qualityDubbingsList = new QualityDubbingsListView();
					}
					
					window.app.view.qualityDubbingsList.render();
				},
				qualityDubbingUpdate: function(id)
				{
					if(window.app.view.qualityDubbing === undefined){
						window.app.view.qualityDubbing = new QualityDubbingView();	
					}
					
					// всегда ставим сюда чистую модель, из-за возможности создания новой
					window.app.view.qualityDubbing.model = new QualityDubbingModel();
					
					if(id !== null){
						var m = window.app.col.qualityDubbings.get(parseInt(id));
						window.app.view.qualityDubbing.model.attributes = m.attributes;
					}
					
					window.app.view.qualityDubbing.render();
				},

				usersList: function()
				{
					if(window.app.view.usersList === undefined){
						window.app.view.usersList = new UsersListView();
						window.app.view.usersList.render(); // покажем костяк, куда вставить прелоадер
						window.app.view.usersList.fetchRender(); // загрузили, обновили
					
					} else {
						window.app.view.usersList.render();
					}
				},
				userUpdate: function(id)
				{
					if(window.app.view.user === undefined){
						window.app.view.user = new UserView();
					}
					
					// всегда ставим сюда чистую модель
					window.app.view.user.model = new UserModel();
					
					// на тот случай если обновили страницу на месте добавления/редактирования
					if(window.app.view.usersList === undefined){
						window.app.view.usersList = new UsersListView();
						window.app.view.usersList.collection.fetch({
							success: function(){
								/********/
								var m = window.app.view.usersList.collection.get(parseInt(id));
								window.app.view.user.model.attributes = m.attributes;
								window.app.view.user.render();
								/********/
							},
							error: function(){}
						});
					
					} else {
						/********/
						var m = window.app.view.usersList.collection.get(parseInt(id));
						window.app.view.user.model.attributes = m.attributes;
						window.app.view.user.render();
						/********/
					}
				},

				commentsList: function()
				{
					if(window.app.view.commentsList === undefined){
						window.app.view.commentsList = new CommentsListView();
						window.app.view.commentsList.render(); // покажем костяк, куда вставить прелоадер
						window.app.view.commentsList.fetchRender(); // загрузили, обновили
					
					} else {
						window.app.view.commentsList.render();
					}
				},
				commentUpdate: function(id)
				{
					if(window.app.view.comment === undefined){
						window.app.view.comment = new CommentView();
					}
					
					// всегда ставим сюда чистую модель
					window.app.view.comment.model = new CommentModel();
					
					// на тот случай если обновили страницу на месте добавления/редактирования
					if(window.app.view.commentsList === undefined){
						window.app.view.commentsList = new CommentsListView();
						window.app.view.commentsList.collection.fetch({
							success: function(){
								/********/
								var m = window.app.view.commentsList.collection.get(parseInt(id));
								window.app.view.comment.model.attributes = m.attributes;
								window.app.view.comment.render();
								/********/
							},
							error: function(){}
						});
					
					} else {
						/********/
						var m = window.app.view.commentsList.collection.get(parseInt(id));
						window.app.view.comment.model.attributes = m.attributes;
						window.app.view.comment.render();
						/********/
					}
				},
				
				ideasList: function()
				{
					if(window.app.view.ideasList === undefined){
						window.app.view.ideasList = new IdeasListView();
						window.app.view.ideasList.render(); // покажем костяк, куда вставить прелоадер
						window.app.view.ideasList.fetchRender(); // загрузили, обновили
					
					} else {
						window.app.view.ideasList.render();
					}
				},
				ideaUpdate: function(id)
				{
					if(window.app.view.idea === undefined){
						window.app.view.idea = new IdeaView();
					}
					
					// всегда ставим сюда чистую модель
					window.app.view.idea.model = new IdeaModel();
					
					// на тот случай если обновили страницу на месте добавления/редактирования
					if(window.app.view.ideasList === undefined){
						window.app.view.ideasList = new IdeasListView();
						window.app.view.ideasList.collection.fetch({
							success: function(){
								/********/
								var m = window.app.view.ideasList.collection.get(parseInt(id));
								window.app.view.idea.model.attributes = m.attributes;
								window.app.view.idea.render();
								/********/
							},
							error: function(){}
						});
					
					} else {
						/********/
						var m = window.app.view.ideasList.collection.get(parseInt(id));
						window.app.view.idea.model.attributes = m.attributes;
						window.app.view.idea.render();
						/********/
					}
				},
				
				pagesList: function()
				{
					if(window.app.view.pagesList === undefined){
						window.app.view.pagesList = new PagesListView();
						window.app.view.pagesList.render(); // покажем костяк, куда вставить прелоадер
						window.app.view.pagesList.fetchRender(); // загрузили, обновили
					
					} else {
						window.app.view.pagesList.render();
					}
				},
				pageUpdate: function(id)
				{
					if(window.app.view.page === undefined){
						window.app.view.page = new PageView();
					}
					
					// всегда ставим сюда чистую модель
					window.app.view.page.model = new PageModel();
					
					// на тот случай если обновили страницу на месте добавления/редактирования
					if(window.app.view.pagesList === undefined){
						window.app.view.pagesList = new PagesListView();
						window.app.view.pagesList.collection.fetch({
							success: function(){
								/********/
								var m = window.app.view.pagesList.collection.get(parseInt(id));
								window.app.view.page.model.attributes = m.attributes;
								window.app.view.page.render();
								/********/
							},
							error: function(){}
						});
					
					} else {
						/********/
						var m = window.app.view.pagesList.collection.get(parseInt(id));
						window.app.view.page.model.attributes = m.attributes;
						window.app.view.page.render();
						/********/
					}
				},
				
				etc: function()
				{
					if(window.app.view.etc === undefined){
						window.app.view.etc = new EtcView();
					}
					
					window.app.view.etc.render();
				}
			},
			initialize: function()
			{
			},
			onRoute: function(name, path, args)
			{
				// console.log(path);
				var aPath = path.split('/');
				
				$('nav ul li a').removeClass('active');
				$('nav ul li a[href="/'+aPath[0]+'/'+aPath[1]+'"]').addClass('active');
				
//				window.CKEDITOR.basePath = '/vendor/ckeditor/';
//				window.CKEDITOR.config.contentsCss = '/vendor/ckeditor/contents.css';
//				window.CKEDITOR.plugins.basePath = '/vendor/ckeditor/plugins/';
//				window.CKEDITOR.plugins.tabletools.path = '/vendor/ckeditor/plugins/tabletools/';
			}
		});

		return Router;
	}
);