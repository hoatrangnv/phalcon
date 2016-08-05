$(document).ready(function(){ 

	var modalOnAfter = function(){
		$modalContent = $('div.modal-content');
		$modalContent.find('.table').addClass('noborder noshadow');
		$modalContent.find('input[placeholder], textarea[placeholder]').placeholder();
		$modalContent.find('input.date').datepicker({ lang: 'vi', date_format: globalvars.date_format });
		$modalContent.find('[data-toggle="tab"]').tab();
		//$modalContent.find('input:file').uniform();
		$modalContent.find('input:checkbox, input:radio').each(function(){
			var $this = $(this);
			$this.filter('[value="'+ $this.attr('data-default') +'"]').attr('checked', true);
		}).uniform();
		
		$modalContent.find('select.stylize').each(function(){
			var $this = $(this),
				allow_create = $this.attr('data-allow-create');

			if ($this.attr('data-default')){
				$this.val($this.attr('data-default'));		
			}

			if (allow_create){
				var $new_field = $('<input class="input" type="text" name="new_'+$this.attr('name')+'" value="" />');
				$this.change(function(){	
					if ($(this).val() == '+'){
						$new_field.insertAfter($('.selectbox'));
					} else {
						$new_field.remove();
					}
				});			
			}
		}).selectbox();		
		
		$modalContent.find('input.slugify').each(function(){
			var $this = $(this).prop('autocomplete', 'off'),
				name = $this.attr('name'), 	
				$result = $('[name='+name+'_alias]').prop('autocomplete', 'off');

			$this.on('keyup', function(){
	        	$result.val(this.value.slugify());
	    	});
		});				
	};	
	
	var $window 			= $(window),
		$document 			= $(this),
		$layout_container 	= $('#layout_container'),
		$layout_sidebar 	= $('#layout_sidebar'),
		$layout_content 	= $('#layout_content'),
		$content_header 	= $('#content_header'),
		$content_body 		= $('#content_body'),
		$listing			= $('#listing');
		
	$document.on('click.flexify.modalbox', '.modal, a[rel^=action]', function(e){
		e.preventDefault();
		if (this.rel.match(/action/)) {
			var $form_listing = $('#form_listing');
			var $items = $form_listing.find('input:checkbox[name="ids[]"]'); 
			switch(this.rel){
				case 'action_change':
					var items_checked = [];				
					$items.each2(function(){
						if(this.checked === true){
							items_checked.push(this.value);
						}
					});  
					if(items_checked.length > 0){
						$(this).modalbox({rel:'form', url:this.href, params:{ids:items_checked}, request_method:'POST', width:500, onafter: modalOnAfter});
						return false;
					}		
				break;
				default:
					var checked_counter = 0;				
					$items.each2(function(){
						if(this.checked === true){
							checked_counter++;
						}
					});    
					if(checked_counter){
						return $form_listing.attr('action', this.href).submit(); 
					}
				break;
			}
			$(this).modalbox({rel:'alert', html: 'Vui lòng chọn ít nhất 1 '+ globalvars.module_name});
		} else {		
			$(this).modalbox({ onafter: modalOnAfter });		
		}
	}); 
	  	
	$content_header.css('width', $layout_content.width() +'px').on('click', 'button[type="submit"]', function(){
		var form_target = $(this).attr('data-form-target');
		$('#'+form_target).append($('<input type="hidden" name="'+this.name+'" />')).submit();
	});
	
	$window.on('resize', function(){
		$content_header.css('width', $layout_content.width() +'px');
	});

	$content_body.notify({ effect:'none', msg: globalvars.notify_message, css: globalvars.notify_css}); 	

	//$('input:file').uniform();	
	$('input:checkbox, input:radio').each(function(){
		var $this = $(this);
		$this.filter('[value="'+ $this.attr('data-default') +'"]').attr('checked', true);
	}).uniform();
	
	$('select.stylize').each(function(){
		var $this = $(this),
			allow_create = $this.attr('data-allow-create');		
			
		if ($this.attr('data-default')){
			$this.val($this.attr('data-default'));		
		}
		
		if (allow_create){
			var $new_field = $('<input class="input" type="text" name="new_'+$this.attr('name')+'" value="" />');
			$this.change(function(){	
				if ($(this).val() == '+'){
					$new_field.insertAfter($('.selectbox'));
				} else {
					$new_field.remove();
				}
			});			
		}
	}).selectbox();
	
	$('input.colors').colorpicker({ letterCase: 'uppercase', swatchPosition:'right' });	
	$('input[placeholder], textarea[placeholder]').placeholder();
	$('input.date').datepicker({lang: 'vi', date_format: globalvars.date_format});
	$('[data-toggle="tab"]').tab();	
	
	$editor = $('textarea.wysiwyg');
	if ($editor.length){
		$editor.liveEditor();	  
	}
	
	$('input.slugify').each(function(){
		var $this = $(this).prop('autocomplete', 'off'),
			name = $this.attr('name'), 	
			$result = $('[name='+name+'_alias]').prop('autocomplete', 'off');

		$this.on('keyup', function(){
        	$result.val(this.value.slugify());
    	});
	});

	$('.shorter').each(function(){
		$(this).shorter({maxlen: 70, pos: 'right', suffix:'...'});	
	});

	$document
		.on('click', 'input[name=checkall]', function(){ 
			var status = this.checked; 
			$listing.find('input[name^=ids]').prop('checked', status).uniform('update');
		})
		.on('click', '.add-row', function(){
			var $this = $(this),    
				$rows = $this.parents('tr'),
				$newrows = $rows.clone();
			
			$newrows.find('input').attr('value', '');
			$newrows.find('.remove-row').show(0, function(){
				var $minus = $(this);
				$minus.click(function(){
					$minus.parents('tr').remove();   
				})
			})
			$rows.after($newrows);
		})
		.on('click', '.browse', function(e){
			e.preventDefault();
			var $this = $(this),
				folder = $this.attr('data-folder') || '',
				type = $this.attr('data-type') || 'images', 
				target = $this.attr('data-target') || '#images',
				limit = $this.attr('data-limit') || 1,
				previewClass = $this.attr('data-preview-class') || '',
				inputName = $this.attr('data-input-name') || '',
				storageUrl = globalvars.base_url + '/files.files/?s='+globalvars.drive_account+'&type=' 
				+ type + '&target='+encodeURIComponent(target)+'&limit='+limit;

			if (folder != ''){
				storageUrl += '&folder='+folder;
			}
			
			if (previewClass != ''){
				storageUrl += '&preview_class='+previewClass;
			}
			
			if (inputName != ''){
				storageUrl += '&input_name='+inputName;
			}
			$this.modalbox({url: storageUrl, rel: 'imagepicker', title:'Thư viện ảnh', width:762, height:512, top:0})
		})
		.on('click', '#images span.remove', function(){
			var $this = $(this);
			var removeParent = $this.attr('data-remove-parent') || 'no';
			if (removeParent == 'yes'){
				$this.parents('li').remove();
			} else {
				$this.parent().empty();
			}
		})
		.on('click', '#select_multiple input[type="checkbox"]', function(){
			$(this).parents('li')[(this.checked === true) ? 'addClass' : 'removeClass']('active');
		})
		.on('keypress', function(e){
			if (e.which == "13" && (e.target.tagName != 'TEXTAREA') && (e.target.tagName != 'INPUT')){
				e.preventDefault();			
			}
		});
		
	
    var $sortable = $('.sortable');
    $sortable.sortable({ 
        handle: '.sortable-handle',
        placeholder: 'sortable-highlight', 
        opacity: 0.8,
        containment: '#listing',
        helper: function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;           
        },		    
        start: function (event, ui) {
        	ui.placeholder.height(ui.item.height());
			ui.placeholder.html('<td colspan="'+parseInt(ui.helper.children().length)+'"></td>');
        },	
        stop: function(event, ui){
            return $.post($sortable.attr('data-url'), $sortable.sortable('serialize'));
        }
    });

    $('.nestable').each(function(){
    	var $nestable = $(this);
    	$nestable
    		.nestable({ maxDepth: globalvars.nesteable_level, grouped: globalvars.nesteable_grouped })
			.on('change', function() {  
				return $.post($nestable.attr('data-url'), {orders:$nestable.nestable('serialize')});
			})      
			.on('mouseenter mouseleave', 'div.nestable-content', function(event){
				var $task = $(this).find('.btn-task-group').stop(true,true);
				if(event.type == 'mouseenter'){
					$task.css('visibility', 'visible');  
				} else {
					$task.css('visibility', 'hidden');  
				}
				return false;
			});   
    });
	
	$content_body.css({'margin-top':($content_header.height())+'px'});
	$layout_container.css('min-height', $window.height()+'px');
	
	var $copyright 		= $('#copyright'),
		document_height	= $document.height();
		container_height= $layout_container.height(),
		sidebar_height 	= $layout_sidebar.height(),
		content_height 	= $layout_content.height();
	
	if (content_height < (document_height-$layout_content.offset().top)){
		var content_height = document_height - $layout_content.offset().top;
		$layout_sidebar.height(content_height);
		$copyright.show();
	} else {
		$layout_sidebar.height(content_height);
	}

	$window.on('scroll', function(){
		$copyright[($(this).scrollTop() + window.innerHeight >= document_height) ? 'fadeIn' : 'fadeOut'](600);
	});	
});  
