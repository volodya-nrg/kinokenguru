"use strict";
// node r.js -o require_build_public_js.js

$(function(){
	if($('.afisha').length){
		window.afisha_width = $('.afisha').outerWidth();
		var $items = $('.afisha').find('.afisha-item');
		var total_items = $items.length;
		var width_item = $items.first().outerWidth();
		var left_coof_orig = (window.afisha_width - width_item) / (total_items-1) - 15;
		var left_coof_dublicat = left_coof_orig;
		var afisha_height = $('.afisha').height();
		
		// выравним все
		$items.each(function(key, value){
			$(this).css({
				top: key * 15 + 'px',
				left: key * left_coof_dublicat + 'px',
				height: 'calc('+afisha_height+'px - '+key+' * 30px - 2px)',
				transform: 'perspective(1000px) rotateY(-'+(key * 10)+'deg)',
				'z-index': 5 - key
			}).attr('index', key);
			
			if(!key){
				$(this).addClass('active');
			}
			
			left_coof_dublicat += 4;
		});

		$('.afisha-item-cover').on('click', function(event){
			var $parent_list = $(this).closest('.afisha');
			var $parent = $(this).closest('.afisha-item');
			var $items = $parent_list.find('.afisha-item');
			var cur_index = parseInt($parent.attr('index'));
			var cur_top = parseInt($parent.css('top'));

			// если кликают на самый первый элемент, то выйдем
			if(!cur_top){
				return;	
			}

			// подхватим эл-мы после выбранного
			var resiver = [];
			var tmp = [];
			var is_found = false;
			$items.each(function(key, value){
				if(parseInt($(this).attr('index')) === cur_index){
				   is_found = true; 
				}

				is_found? resiver.push($(this)): tmp.push($(this));
			});

			for(var i=0; i<tmp.length; i++){
			   resiver.push(tmp[i]);
			}
			
			left_coof_dublicat = left_coof_orig;
			$.each(resiver, function(key, value){
				var $obj = value;
				var cursor = !key? 'default': 'pointer';
				
				$obj.css({
					height: 'calc('+afisha_height+'px - '+key+' * 30px - 2px)',
					transform: 'perspective(1000px) rotateY(-'+(key * 10)+'deg)',
					'z-index': 5 - key
				});
				
				if(!key){
					$obj.addClass('active');
				
				} else if($obj.hasClass('active')) {
					$obj.removeClass('active');
				}
				
				$obj.animate({
					top: (key * 15) + 'px',
					left: key * left_coof_dublicat + 'px'
				}, 'slow', 'easeOutCubic');
				
				left_coof_dublicat += 4;
			});
		});
	}
	if($('.spoiler').length){
		$('.spoiler').find('.spoiler-panel').on('click', function(){
			var $parent = $(this).closest('.spoiler');
			var $wraper = $parent.find('.spoiler-wraper');
			var $content = $parent.find('.spoiler-content');

			// тут открываем
			if($wraper.height() < $content.height()){
				// 10 - padding-top
				$wraper.animate({
					height: $content.height() + 10 + 'px'
				}, 'normal');	

				$(this).find('i.fa').removeClass('fa-chevron-down').addClass('fa-chevron-up');
			} else {
				$wraper.animate({
					height: $parent.data('height') + 'px'
				}, 'fast');

				$(this).find('i.fa').removeClass('fa-chevron-up').addClass('fa-chevron-down');
			}
		});

		$('.spoiler').each(function(){
			var $wraper = $(this).find('.spoiler-wraper');
			var $content = $(this).find('.spoiler-content');
			var $panel = $(this).find('.spoiler-panel');
			var height_def = $(this).data('height');
			
			$wraper.height(height_def);

			if($content.height() < $wraper.height()){
				$panel.hide();
			}
			
			if($content.height() < height_def){
				$wraper.height($content.height());
			}
		});
	}
	if($('.tabs').length){
		setTabsEvent();
	}
	if($('.form-top-search').length){
		$('.form-top-search').find('input[name=q]').on('keydown keyup', function(e){
			var $parent = $(this).closest('.form-top-search');
			var $popup = $parent.find('.form-top-search-pop-up');
			var $preloader = $parent.find('.fa-spin');
			var val = $.trim($(this).val());

			if(val.length < 3){
				if($popup.css('display') === 'block'){
					$popup.hide();
				}
				return;	
			}
			if(e.type === "keydown"){
				window.change_search = val;
				return;
			}
			if(val !== window.change_search){
				$popup.hide();
				$preloader.show();
				$(this).attr('readonly', true);
				var that = $(this);

				$.post('/search/ajax-search', {q: val}, function(response){
					$preloader.hide();
					that.attr('readonly', false);

					if(response.result){
						$popup.empty().show();

						if(response.msg.length === 0){
							$popup.append('<center class="text-muted">пусто</center>');

						} else {
							for (var i=0, size = response.msg.length; i<size; i++) {
								var product = response.msg[i];
								$popup.append('<li class="text-eclipse"><a class="a-gray-sm" href="/'+product.slug+'" title="'+product.name+'">'+product.name+'</a></li>');	
							}
						}

						$(document).off('click');
						$(document).on('click', function(e){
							 var in_zone = $(e.target).closest('.form-top-search').length;

							 if (!in_zone) {
								 $popup.hide();
								 $(document).off('click');
							 }
						});
					}	
				}, 'json');
			}
		});
	}
	if($('.cover-thumb').length){
		$('.cover-thumb').on('mouseover', function(){
			var main = $('#cover-main').attr('original');
			
			$('#cover-main').attr('src', '/images/md_'+$(this).attr('original'))
							.attr('original', $(this).attr('original'));
			$(this).attr('src', '/images/sm_'+main)
				  .attr('original', main);
		});
	}
	if($('.ul-main-tree').length){
		$('.ul-main-tree-caret').on('click', function(){
			if($(this).hasClass('fa-caret-down')){
				$(this).removeClass('fa-caret-down').addClass('fa-caret-up');
				$(this).parent().find('> ul').first().show();
				
			} else {
				$(this).removeClass('fa-caret-up').addClass('fa-caret-down');
				$(this).parent().find('> ul').first().hide();
			}
		});
		$('.ul-main-tree-open').each(function(){
			$(this).parent()
				  .find('.fa-caret-down')
				  .first()
				  .removeClass('fa-caret-down')
				  .addClass('fa-caret-up');
		});
	}
	if($('.slick').length){
		$('#slick-humans').slick({
			infinite: false,
			slidesToShow: 9,
			slidesToScroll: 1,
			responsive: [
//				{
//					breakpoint: 768,
//					settings: {
//						arrows: false,
//						centerMode: true,
//						centerPadding: '40px',
//						slidesToShow: 3
//					}
//				},
//				{
//					breakpoint: 480,
//					settings: {
//						arrows: false,
//						centerMode: true,
//						centerPadding: '40px',
//						slidesToShow: 1
//					}
//				}
			]
		});
		$('#slick-frames').slick({
			infinite: false,
			slidesToShow: 5,
			slidesToScroll: 1,
			swipe: false
		});
	}
	if($('.modal').length){
		$('[data-toggle=modal]').on('click', function(event){
			showModal($(this).data('target'), this);
		});
	}
	
	// события на верхнее меню и его подменю
	$('.top-menu-pop-up-parent').mouseover(function(){
		$(this).find('.top-menu-pop-up').show();
	});
	$('.top-menu-pop-up-parent').mouseleave(function(){
		if($(this).find('#form-login').length){
			var $target = $(this).find('.top-menu-pop-up');
			$target.css('display', 'block');
			
			$(document).off('click');
			$(document).on('click', function(e){
				 var in_zone = $(e.target).closest('.top-menu-pop-up-parent').length;

				 if (!in_zone) {
					 $target.hide();
					 $(document).off('click');
				 }
			});
			
		} else {
			$(this).find('.top-menu-pop-up').hide();
		}
	});
	
	// если это страница карточки товара
	if(window.isProductPage){
		if(checkFlash() === false){
			showModal('#modalDownloadFlash');
		}
	}
});

var afisha_width = 0;
var afisha_item_save_opacity = 0;
var change_search = "";
var ajax_doing = false;

function login(obj){
	var $obj = $(obj);
	var $form = $obj.closest('#form-login');
	var $reset = $form.find('input[type=reset]');
	var $preloader = $('#preloader');
	
	$obj.prop('disabled', true);
	$preloader.show();
	
	$.post('/login/check-auth', $form.serialize())
		.done(function() {
			$reset.click();
			window.location.href = '/';
		})
		.fail(function(response){
			alert(response.responseJSON.error);
		})
		.always(function() {
			$obj.prop('disabled', false);
			$preloader.hide();
		});
}
function addComment(obj){
	var $obj = $(obj);
	var $blockItems = $('#comment-items');
	var $form = $obj.closest('#form-comment');
	var $reset = $form.find('input[type=reset]');
	var $preloader = $('#preloader');
	var $empty = $('#comment-items-empty');
	
	$obj.prop('disabled', true);
	$preloader.show();
	
	$.post('/ajax-add-comment', $form.serialize())
		.done(function(response) {
			if($empty.length){
				$empty.hide();
			}
			
			$blockItems.append(response.result);
			$reset.click();
		})
		.fail(function(response){
			alert(response.responseJSON.error);
		})
		.always(function() {
			$obj.prop('disabled', false);
			$preloader.hide();
		});
}
function removeComment(obj, id, opt){
	// предохранимся от двойного нажатия
	if(window.ajax_doing === true){
		return;
	}
	
	var id = parseInt(id);
	var $obj = $(obj);
	var $parent = $obj.closest('.comment-item');
	var $preloader = $('#preloader');
	var $empty = $('#comment-items-empty');
	var request = {
		el_id: id,
		opt: opt
	};
	
	window.ajax_doing = true;
	$preloader.show();
	
	$.post('/ajax-remove-comment', request)
		.done(function(response) {
			$parent.fadeOut("fast", function(){
				$(this).remove();
				
				if($('.comment-item').length === 0){
					$empty.show();
				}
			});
		})
		.fail(function(response){
			alert(response.responseJSON.error);
		})
		.always(function() {
			window.ajax_doing = false;
			$preloader.hide();
		});
}
function toggleLike(obj, el_id, opt, is_up){
	var $obj = $(obj);
	var $preloader = $('#preloader');
	var $parent = $obj.closest('#like-wrapper');
	var $likeAmountUp = $parent.find('.like-amount-up');
	var $likeAmountDown = $parent.find('.like-amount-down');
	
	var request = {
		el_id: el_id,
		opt: opt,
		is_up: is_up
	};
	
	window.ajax_doing = true;
	$preloader.show();
	
	$.post('/ajax-toggle-like', request)
		.done(function(response) {
			// если уже имели мнение и нажали на другое
			if($parent.find('.fa-thumbs-up').hasClass('active') && is_up === 0){
				$parent.find('.fa-thumbs-up').removeClass('active');
				$obj.addClass('active');
				$likeAmountUp.text( parseInt($likeAmountUp.text())-1 );
				$likeAmountDown.text( parseInt($likeAmountDown.text())+1 );
				
			} else if ($parent.find('.fa-thumbs-down').hasClass('active') && is_up === 1) {
				$parent.find('.fa-thumbs-down').removeClass('active');
				$obj.addClass('active');
				$likeAmountUp.text( parseInt($likeAmountUp.text())+1 );
				$likeAmountDown.text( parseInt($likeAmountDown.text())-1 );
				
			} else {
				if($obj.hasClass('active')){
					$obj.removeClass('active');
					var num = -1;

				} else {
					$obj.addClass('active');
					var num = 1;
				}
				
				var $el = (is_up === 1)? $likeAmountUp: $likeAmountDown;
				$el.text(parseInt($el.text()) + num);
			}
		})
		.fail(function(response){
			// alert(response.responseJSON.error);
		})
		.always(function() {
			window.ajax_doing = false;
			$preloader.hide();
		});
}
function setTabsEvent(){
	$('.tabs').find('.tabs-panel-item').on('click', function(e){
		if($(this).hasClass('active')){
			return;
		}

		var $parent = $(this).closest('.tabs');
		var $panelItems = $parent.find('.tabs-panel-item');
		var $contentItems = $parent.find('.tabs-content-item');
		var index = $(this).index();

		// спрячем все и покажем нужный по индексу
		$contentItems.hide();
		$contentItems.eq(index).show();

		// уберем с панели активный и добавим на текущий элемент нужный класс
		$panelItems.removeClass('active');
		$(this).addClass('active');
	});
}
function addIdea(obj){
	var $obj = $(obj);
	var $blockItems = $('#list-ideas');
	var $form = $obj.closest('#form-idea');
	var $alert = $form.find('.alert');
	var $reset = $form.find('input[type=reset]');
	var $preloader = $('#preloader');
	var $empty = $('#idea-items-empty');
	
	$obj.prop('disabled', true);
	$preloader.show();
	$alert.empty().hide();
	
	$.post('/ajax-add-idea', $form.serialize())
		.done(function(response) {
			if($empty.length){
				$empty.hide();
			}
			
			$blockItems.prepend(response.result);
			$reset.click();
			$alert.html('<span class="text-green">добавлено</span>').show();
		})
		.fail(function(response){
			alert(response.responseJSON.error);
		})
		.always(function() {
			$obj.prop('disabled', false);
			$preloader.hide();
		});
}
function removeIdea(obj, id){
	// предохранимся от двойного нажатия
	if(window.ajax_doing === true){
		return;
	}
	
	var id = parseInt(id);
	var $obj = $(obj);
	var $parent = $obj.closest('.idea-item');
	var $preloader = $('#preloader');
	var $empty = $('#idea-items-empty');
	var request = {
		id: id
	};
	
	window.ajax_doing = true;
	$preloader.show();
	
	$.post('/ajax-remove-idea', request)
		.done(function(response) {
			$parent.fadeOut("fast", function(){
				$(this).remove();
				
				if($('.idea-item').length === 0){
					$empty.show();
				}
			});
		})
		.fail(function(response){
			alert(response.responseJSON.error);
		})
		.always(function() {
			window.ajax_doing = false;
			$preloader.hide();
		});
}
function addSubscriber(obj){
	var $obj = $(obj);
	var $form = $obj.closest('#form-subscribe');
	var $reset = $form.find('input[type=reset]');
	var $alert = $form.find('.alert');
	var $preloader = $('#preloader');
	
	$obj.prop('disabled', true);
	$preloader.show();
	$alert.empty().hide();
	
	$.post('/ajax-add-subscriber', $form.serialize())
		.done(function() {
			$alert.html('<span class="text-green">Вы успешно подписаны.</span>').show();
			$reset.click();
		})
		.fail(function(response){
			alert(response.responseJSON.error);
		})
		.always(function() {
			$obj.prop('disabled', false);
			$preloader.hide();
		});
}
function checkFlash() {
	var flashinstalled = false;
	
	if (navigator.plugins) {
		if (navigator.plugins["Shockwave Flash"]) {
			flashinstalled = true;
		
		} else if (navigator.plugins["Shockwave Flash 2.0"]) {
			flashinstalled = true;
		}
	
	} else if (navigator.mimeTypes) {
		var x = navigator.mimeTypes['application/x-shockwave-flash'];
		
		if (x && x.enabledPlugin) {
			flashinstalled = true;
		}
	
	} else {
		// на всякий случай возвращаем true в случае некоторых экзотических браузеров
		flashinstalled = true;
	}
	
	return flashinstalled;
}
function showModal(id, that){
	var html = [];
	var has_tabs = false;
	var has_slide_frames = false;
	var has_trailers = false;

	if(that !== undefined){
		var $that = $(that);
	}

	$('body').css('overflow', 'hidden');

	if(id === "#modalTrailers"){
		var sName = $that.data('name');
		var aTrailers = $that.data('trailers').split(",");
		var sPanels = "";
		var sContents = "";

		for(var i=0; i<aTrailers.length; i++){
			sPanels += '<div class="tabs-panel-item '+(!i? "active": "")+'">Трейлер №'+(i+1)+'</div>';
			sContents += [
				'<div class="tabs-content-item">',
					'<iframe width="580" height="326" src="https://www.youtube.com/embed/'+ aTrailers[i] +'" frameborder="0" allowfullscreen></iframe>',
				'</div>'
			].join("");
		}

		html = ['<h3>'+sName+'</h3><br />',
				'<div class="tabs">',
					'<div class="tabs-panel">',
						sPanels,
					'</div>',
					'<div class="tabs-content">',
						sContents,
					'</div>',
				'</div>'];

		$(id).find('.modal-wrapper').css('max-width', 600 + "px");	
		has_tabs = true;
		has_trailers = true;

	} else if(id === "#modalFrames"){
		html = ['<div id="slickSlideFrames">'];
		$('.slick-frame').each(function(){
			html.push('<div align="center"><img src="/images/'+ $(this).data('original') +'" /></div>');
		});
		html.push('</div>');

		has_slide_frames = true;
	}

	if(html.length){
		$(id).find('.modal-content').html(html.join(""));
	}

	// если имеем внутри табы, то установим обработчики на переключение табов
	if(has_tabs){
		setTabsEvent();
	}
	if(has_slide_frames){
		$('#slickSlideFrames').slick({
			infinite: false,
			slidesToShow: 1,
			slidesToScroll: 1
		});

		$('#slickSlideFrames').slick('slickGoTo', $that.data('key'));
	}

	$(id).fadeIn('fast').on('click', function(e){
		if($(e.target).hasClass('modal') === false){
			return;
		}

		$(this).off('click');
		$(this).fadeOut('fast', function(){
			$('body').css('overflow', 'auto');

			if(has_slide_frames){
				$('#slickSlideFrames').slick('unslick');
			}
			if(has_trailers){
				$(this).find('.modal-content').empty();
			}
		});
	});
}
function toggleMyFavorites(that, id){
	var $obj = $(that);
	var $preloader = $('#preloader');
	
	$preloader.show();
	window.ajax_doing = true;
	$.post('/ajax-toggle-in-my-favorites/'+id, {})
		.done(function(response) {
			if($obj.hasClass('text-orange')){
				$obj.removeClass('text-orange');
				
			} else {
				$obj.addClass('text-orange');
			}
			
			if(response.total === 0){
				$('#a-my-favorites').addClass('a-gray-md');
				$('#a-my-favorites > span').empty();
				
			} else {
				if($('#a-my-favorites').hasClass('a-gray-md')){
					$('#a-my-favorites').removeClass('a-gray-md');
				}
				
				$('#a-my-favorites > span').text("- "+response.total+"");
			}
		})
		.fail(function(response){
			alert(response.responseJSON.error);
		})
		.always(function() {
			$preloader.hide();
			window.ajax_doing = false;
		});
}