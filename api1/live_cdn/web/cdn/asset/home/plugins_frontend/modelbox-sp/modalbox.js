/*!========================================================================
 * Flexify Modalbox 1.8.1
 * Author: Pham Dinh Long | longfanos@gmail.com | (@longfanos}
 * ========================================================================*/
;(function ($, window, document, undefined) { "use strict";
  
	var	idx = 0, $overlay, $modal, $container, $content;
		
    var Modalbox = function(element) {
		this.$el 	  = $(element);
		this.settings = {};
		this.init();
	};
	
    Modalbox.defaults = { 
		auto: false,
		skin: null,
		url: null,
		rel: null,
		width: null,
		height: null,
		maxwidth: 700,
		maxheight: 500,			
		opacity : 0.4,
		overlay : true, 	
		draggable: true,
		header: true,
		footer: false,
		title: '',
		html: '',			
		top: false, //Modal top position
		left: false, //Modal left position,
		multiple: true,
		onbefore: function(){ }, //Callback function before modal opened
		onafter: function(){ } //Callback function after modal opened
	};	
		
    Modalbox.prototype = {
		scaleImage: function(){
			var self 	= this,
				$images = $content.find('img');
				
			if ($images.length){
				$images.css('display', 'none').one('load.flexify.modalbox', function(){
					$images.css('display', 'block').imagescale({width:self.settings.maxwidth, height:self.settings.maxheight});
					self.position();
				});
			}
		},
		
		/*dragHandle: function(){
			var $header = $container.find('.modal-header');

			if ($header.is(':visible')) {
				$header.css('cursor', 'move');
				$modal.draggable({ 
					handle: '.modal-header', 
					containment: 'document', 
					stop: function (ev, ui) {
						$(this).css({top:ui.position.top, left:ui.position.left});
					}
				});							
			}
		},*/
		
		init: function(){
			var self = this;
			
			if (this.settings.skin != ''){
				$modal.addClass(this.settings.skin);
			}
			
			$(window).on('resize.flexify.modalbox', function(){
				self.position();
			});
		},
		
		makeSettings: function(options){
			options 			= options || {},
			options.skin 		= options.skin || this.$el.attr('modal-skin'),		
			options.url 		= options.url || this.$el.attr('href'),
			options.title 		= options.title || this.$el.attr('title'),
			options.rel 		= options.rel || this.$el.attr('rel'),
			options.width 		= options.width || this.$el.attr('modal-width'),
			options.height 		= options.height || this.$el.attr('modal-height'),
			options.html 		= options.html || this.$el.attr('modal-html'),
			options.maxwidth 	= options.maxwidth || this.$el.attr('modal-maxwidth'),
			options.maxheight	= options.maxwidth || this.$el.attr('modal-maxheight');			

			this.settings = $.extend({}, Modalbox.defaults, options);		
		},
		
		open: function() {
			var self = this, settings = self.settings, url = settings.url, html;

			if (settings.overlay == true && $overlay.not(':visible')){
				self.overlay('show');				
			}				
			
			$modal.addClass('modalbox-active').show();
			
			if (settings.rel == 'form'){
				settings.top = '35px';
			}
			
			if (settings.rel != 'confirm' && settings.rel != 'alert'){				
				if (settings.title != '') {
					$container.prepend('<div class="modal-header"><div class="modal-title">'+settings.title+'</div></div>');		
				}
				$container.find('.modal-close').show();
				$content.html('<div class="modal-loading"></div>');
				self.position();				
			}			
				
			switch(settings.rel){
				case 'html':
					$content.html(settings.html);
					self.scaleImage();
				break;	
				
				case 'image':
					$content.html('<img src="'+url+'" />');	
					self.scaleImage();					
				break;
				
				case 'imagepicker':
					html = $.ajax({url: url, type:'GET', data: settings.params, async:false}).responseText;		
					$content.html(html);
				break;
				
				case 'iframe':
					html = '<iframe id="modal_iframe" src="'+url+'" width="100%" height="100%" frameBorder="0"></iframe>';			
					$content.html(html).css('overflow-y', 'hidden');
					$("#modal_iframe").one('load.flexify.modalbox', function(){
						var $this = $(this),
							title = $this.contents().find('title').text();
						if (typeof(title) != 'undefined' && title != ''){
							$container.prepend('<div class="modal-header"><div class="modal-title">'+title+'</div></div>');
							self.position();
							$modal.draggable({ 
								handle: '.modal-header', 
								containment: 'document', 
								stop: function (ev, ui) {
									$(this).css({top:ui.position.top, left:ui.position.left});
								}
							});								
						}
					})
				break;	
				
				case 'ajax':
					html = $.ajax({url: url, type:'GET', data: settings.params, async:false}).responseText;			
					
					settings.onbefore();
					
					$content.html(html);
					self.scaleImage();
					
					settings.onafter();
					
					self.formcontrol();
				break;

				case 'form':
					html = settings.html;
										
					if (html == ''){
						var params = settings.params,
							method = (typeof(settings.request_method) != 'undefined') ? settings.request_method : 'GET';				
						
						html = $.ajax({url: url, type:method, data:params, async:false}).responseText;
					}	
					
					settings.onbefore();
					$content.html(html).tabindex();
					settings.onafter();
					
					self.formcontrol();
				break;	
				
				case 'confirm':
					$container.find('.modal-close').hide();
					$content.html('\
						<form><table class="table">\
							<tr><td class="text-center" style="padding:30px;">'+settings.html+'</td></tr>\
							<tr><td class="foot text-center">\
								<button class="btn btn-danger" name="yes">Yes</button>\
								<button class="btn btn-primary" name="no" data-dismiss="modal">No</button>\
							</td></tr>\
						</table></form>\
					').on('click.flexify.modalbox', 'button[name="yes"]', function(){
						$.ajax({type:'GET', url: url}).done(function(data){
							return self.formresult(data, url); 			 	                             
						});
						return false;
					});				
				break;	
				
				case 'alert':
					$container.find('.modal-close').hide();
					$content.html('\
						<form><table class="table">\
							<tr><td class="text-center" style="padding:30px;">'+settings.html+'</td></tr>\
							<tr><td class="foot text-center">\
								<button class="btn btn-primary" name="yes">OK</button>\
							</td></tr>\
						</table></form>\
					').on('click.flexify.modalbox', 'button[name="yes"]', function(){
						self.close();
						return false;
					});	
				break;					
			}
			
			self.position();
			
			/*if (settings.draggable) {
				self.dragHandle();
			}	*/
			
			$modal.on('click.flexify.modalbox', '[data-dismiss="modal"]', $.proxy(this.close, this));
			
			$(document).on('keydown.flexify.modalbox', $.proxy(this.keydown, this));	
		},
		
        close: function(){

        	/*if($modal.is(':ui-draggable')){
				$modal.draggable('destroy');        		
        	}
			*/
			if (idx > 1){
				$overlay.remove();
				$modal.remove();
				idx--;
				Modalbox.getObject();
			} else {
		
				this.overlay('hide');
				
				$modal.removeClass('modalbox-active').removeAttr('style').hide(0, function(){
					$container.find('.modal-header').remove();     
					$container.find('.modal-error').remove();     
					$content.removeAttr('style').html('<div class="modal-loading"></div>');
				});			
			}		

			$(document).off('keydown.flexify.modalbox');			
        },
		
		overlay: function(type){
			if (this.settings.overlay !== false || this.settings.opacity !== null){
				if (type == 'show'){
					$overlay.css({'opacity':+this.settings.opacity, visibility:'visible', 'z-index':(99999+idx)}).fadeIn(50);
					$overlay.off('click');
				}else{
					$overlay.fadeOut(50, function(){
						$(this).removeAttr('style');
					})            
				}
			}
			return false	
		},
		
		position: function() {
			var self = this, settings = self.settings, $window = $(window);
			
			//Set max content size
			if (settings.width){
				$content.width(settings.width);			
			} else {
				if ($content.width() > settings.maxwidth){
					$content.width(settings.maxwidth);
				}
			}
			if (settings.height){
				$content.height(settings.height);
			} else {
				if ($content.height() > settings.maxheight){
					$content.height(settings.maxheight);
				}
			}	
			
			var top = 0, left = 0, offset = $modal.offset(), scrollTop = $window.scrollTop(), scrollLeft = $window.scrollLeft();
				
			$modal.css({top: -9e4, left: -9e4});
			offset.top -= scrollTop;
			offset.left -= scrollLeft;
			$modal.css({position: 'fixed', 'z-index': (100000+idx)});

			if (settings.left !== false) {
				left += settings.left;
			} else {
				left += Math.round(Math.max($window.width() - $modal.width(), 0) / 2);
			}
			if (settings.top !== false) {
				top += settings.top
			} else {
				top += Math.round(Math.max($window.height() - $modal.height(), 0) / 2);
			}
			$modal.css({top: top, left: left, visibility:'visible'});
		},
		
		formresult: function(rs, url){

			var self = this, res = $.parseJSON(rs);

			if (res.msg != ''){
				res.msg = $('<p>'+res.msg+'</p>').text();
			}
			
			if (res.reloadurl != ''){
				res.reloadurl = (res.reloadurl == 'this') ? url : res.reloadurl.replace(/&amp;/g, "&");
			}

			switch(res.type){
				 case 'error':  
					if ($('.modalbox-error').hasClass('active')){
						return false;
					}

					var $msg = $('<div class="modalbox-error">'+ res.msg +'<span class="close">&times;</span></div>');						
					
					$msg.addClass('active').prependTo($container).stop(true,true).slideDown(100, function(){
						$msg.on('click.flexify.modalbox', 'span.close', function(){
							$msg.removeClass('active').stop().slideUp(100);
							return false;
						});  
					}).on('click.flexify.modalbox', function(){
						$msg.removeClass('active').stop().slideUp(100);
						return false;						
					}).delay(10000).slideUp(100, function(){$msg.removeClass('active')});                                                
				 break;
				 case 'success':
					if (res.reloadtarget == 'window'){
						self.close();
						if (res.reloadurl != ''){
							location.href = res.reloadurl;
						}						
					} else if (res.reloadtarget == 'parent'){
						self.close();
						var html = $.ajax({url: res.reloadurl, type:'GET', async:false}).responseText;
						self.settings.onbefore();
						$content.html(html).tabindex();
						self.settings.onafter();
					} else {
						if ($('.modalbox-success').hasClass('active')){
							return false;
						}

						if (res.msg != ''){
							var $msg = $('<div class="modalbox-success">'+ res.msg +'<span class="close"></span></div>');						
							$msg.addClass('active').prependTo($container).stop(true,true).slideDown(100, function(){
								$msg.on('click.flexify.modalbox', 'span.close', function(){
									$msg.removeClass('active').stop().slideUp(100);
									return false;
								});  
							}).on('click.flexify.modalbox', function(){
								$msg.removeClass('active').stop().slideUp(100);
								return false;							
							}).delay(5000).slideUp(100, function(){ $msg.removeClass('active'); }); 						
						}

						if (res.reloadurl != ''){

							var html = $.ajax({url: res.reloadurl, type:'GET', async:false}).responseText;
							
							self.settings.onbefore();							
							if (res.reloadtarget == 'this'){
								$content.html(html).tabindex();
							} else {							
								$content.find(res.reloadtarget).html(html).tabindex();
							}							
							self.settings.onafter();							
							self.formcontrol();						
							self.position();
						}						
					}					
				 break;
				 case 'notice': 
					self.close();
					$('div.container').notify({ msg: res.msg, css: 'notify-notice' });             
				 break;        
			}
			return false;  
		},
		
		formcontrol: function()		{
			var self = this,
				$form = $content.find('form'),
				form_enctype = $form.attr('enctype') || null,
				form_action_url = $form.attr('action') || self.settings.url,
				url = form_action_url;
			
			if (form_enctype == 'multipart/form-data'){
				form_action_url += (url.indexOf('?') == -1) ? '/?ajax=1' : '&ajax=1';
			}

			$form.attr('action', form_action_url).prepend($('<input type="hidden" name="do" value="save"/>'));	
			
			$form.on('click.flexify.modalbox', 'button[type="submit"]', function(){
				
				if ($('.modalbox-error, .modalbox-success').hasClass('active')){
					return false;
				} 

				var $button = $(this).attr('disabled', true);

				if (form_enctype == 'multipart/form-data'){ 
					var $iframe = $('<iframe id="uploadstatus" name="uploadstatus" style="position:absolute;top:-9999px;left:-9999px;" src="about:blank" />');
					$(document.body).append($iframe);
					$form.attr('target','uploadstatus');
					$iframe.on('load', function(){  
						var result = $iframe.contents().find('body').html();
						setTimeout(function(){ $iframe.remove(); }, 100);
						$button.attr('disabled', false);
						return self.formresult(result, url); 				 	                             
					});
					$form.submit();	
				} else {
					$.ajax({type:'POST', url:form_action_url, data:$form.serialize()}).done(function(result){
						$button.attr('disabled', false);
						return self.formresult(result, url); 			 	                             
					});
				}	
				return false;
			}).on('click.flexify.modalbox', 'button[name="cancel"]', function(){
				$('.modalbox-error, .modalbox-success').remove();
				self.close();
				return false;
			});			
		},
		
		keydown: function(e) {
			switch(e.keyCode) {
				case 27: //ESC
					this.close();
				break;
				case 13: //ENTER
					if(e.target.tagName != 'TEXTAREA' && e.target.tagName != 'INPUT'){
						e.preventDefault();
					}
				break;
			}
		}
    };
	
	Modalbox.setup = function() {
		idx++;
		$(document.body).append('\
			<div id="modalbox_overlay_'+idx+'" class="modalbox-overlay"></div>\
			<div id="modalbox_'+idx+'" class="modalbox" style="z-index:'+(100+idx)+'">\
				<div class="modal-container">\
					<a class="modal-close" href="javascript://" data-dismiss="modal">&times;</a>\
					<div class="modal-content"></div>\
				</div>\
			</div>\
		');	
		Modalbox.getObject();
	}
	
	Modalbox.getObject = function() {
		$overlay 	= $('#modalbox_overlay_'+idx);
		$modal 		= $('#modalbox_'+idx);
		$container 	= $modal.find('div.modal-container');
		$content 	= $modal.find('div.modal-content');								
	}
	
	//Append new modalbox template when DOM loaded
	$(Modalbox.setup);
	
    $.fn['modalbox'] = $['modalbox'] = function(options) {
		var $this = this;

		
		if (typeof options == 'object' && $modal.hasClass('modalbox-active')){
			Modalbox.setup();
		}

		if (!$this[0]){
			$this = $('<a/>'); //Create tmp obj if undefined selector when using $.modalbox();
		}
				
		$this.each(function(){
			var data = $(this).data('flexify.modalbox');
            if (!data){
                $(this).data('flexify.modalbox', (data = new Modalbox(this)));
            }
			data.makeSettings(options);
			data[(typeof options == 'string' ? options : 'open')]();
        });
		
		return $this;
    };
	
})(jQuery, window, document);

;(function ($) { "use strict";
	$.fn.tabindex = function() {
		return this.find('input, select, textarea, button, iframe').each(function(i){
			if (this.type != 'hidden') {
				this.tabIndex = 1+(i++);
			}
        });
    };
})(jQuery);

;(function ($) { "use strict";
	$.fn.imagescale = function(options) {
		var aspectRatio = 0;
		if (!options.height && !options.width) {
			return this;
		}

		if (options.height && options.width){
			aspectRatio = options.width / options.height;
		}

		return this.one("load", function(){
			this.removeAttribute("height");
			this.removeAttribute("width");
			this.style.height = this.style.width = "";
			var imgHeight = this.height, 
				imgWidth = this.width,
				imgAspectRatio = imgWidth / imgHeight,
				bxHeight = options.height,
				bxWidth = options.width,
				bxAspectRatio = aspectRatio;		
			if (!bxAspectRatio){
				if (bxHeight){
					bxAspectRatio = imgAspectRatio + 1;
				} else {
					bxAspectRatio = imgAspectRatio - 1;
				}
			}
			if ( (bxHeight && imgHeight > bxHeight) || (bxWidth && imgWidth > bxWidth) ){
				if ( imgAspectRatio > bxAspectRatio ) {
					bxHeight = ~~ ( imgHeight / imgWidth * bxWidth );
				} else {
					bxWidth = ~~ ( imgWidth / imgHeight * bxHeight );
				}
				this.height = bxHeight;
				this.width = bxWidth;
			}
		}).each(function() {
			if (this.complete){
				$(this).trigger("load");
			}
			this.src = this.src;
		});
    };
})(jQuery);
