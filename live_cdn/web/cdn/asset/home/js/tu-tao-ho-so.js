$(document).ready(function(){
 	main_tu_tao_ho_so();
 	not_dynamic();
});
function main_tu_tao_ho_so (){

	add_profile_block();
	//ajax_form();
	add_text_to();
	exprience_add_form();
	exprience_detail_form();
	exprience_edit_form();
	exprience_delete_form();

	consultor_add_form();
	consultor_detail_form();
	consultor_edit_form();
	consultor_delete_form();

	diploma_add_form();
	diploma_detail_form();
	diploma_edit_form();
	diploma_delete_form();
	$("textarea.autosize").autosize();
}
/**************/
function not_dynamic(){
	btn_ex();
}
function btn_ex(){
	$('.btn-toggle').on('click',function(){
		$(this).toggleClass('active');
		var href = $(this).attr('href');
		return false;
	});
	$('.expansion').on('click',function(){
		var href = $(this).attr('href');
		var text = $(this).text();
		if(text =="Rút gọn"){text ="Mở rộng"}else{text="Rút gọn"}
		if ( $(this).hasClass('change-text') ) {
			$(this).text(text)
		}
		$('.content-ex'+href).toggleClass('active');
	});
}

/*function ajax_form(){
	$('.ajax-form').on('click',function(){
		var pra = $(this).data('close');
		$(this).parents('#'+pra).slideUp('fast').next().find('.clone-btn').fadeIn('fast');
		// chạy hàm ajax form submit tại đây
		return false;
	})
}*/

function add_profile_block(){
	$('.clone-btn').on('click',function(){
		$this = $(this);
		var open = $this.data('open');
		$('html').find('#'+open).slideDown('fast');
		$(this).fadeOut('fast');
	})
}
/** Experience **/

/**
@author: VuTran
**/
function exprience_add_form(){
	$( "#form-expericence" ).submit(function(e){
	    e.preventDefault();
	    $('#load-ajax').html( '<center><span class="snake_loading"></span></center>' );
	    var url = $(this).attr( 'action' ),
	        data = $( "#form-expericence" ).serialize();
	    $.ajax({
	        type: 'POST',
	        url: url,
	        data: data,
	        dataType: "json",
	        success: function($response) {
	        	$( '.snake_loading' ).remove();
	        	$( '.alert' ).remove();
	        	$( '.mess-input' ).remove();

	        	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
        			window.location.replace($response.url);
	        	} else {
	        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
        				$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
		        	} else {
			        	if (typeof $response.status != 'undefined' && $response.status == 200) {
			        		$.ajax({
					            type: 'POST',
					            url: $response.list_experience,
					            success: function(response) {
					            	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
					            		window.location.replace($response.url);
					            	} else {
					            		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
					            			$( ".mess-list-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Hồ sơ người tìm việc không tồn tại</div>' );
					            		} else {
					            			$('#kinh-nghiem').html(response);
					            			main_tu_tao_ho_so();
					            		}
					            	}

					            }
				        	});
			        		$("#form-expericence")[0].reset();
			        		$( ".mess-experience-global" ).html( '<div class="alert alert-success"><div class="box-icon"></div>Thêm mới kinh nghiệm thành công</div>' );

			        	} else {
			        		if (typeof $response.status != 'undefined' && $response.status != 200) {
			        			$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.status + '<div>' );
			        		} else {
					        	if (typeof $response.start_date != 'undefined' || $response.end_date != 'undefined' || $response.position != 'undefined' || $response.company_name != 'undefined' || $response.description != 'undefined') {
					        		$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Thông tin không hợp lệ</div>' );
					        	}

					        	if (typeof $response.start_date != 'undefined') {
					        		$( ".start-date" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.start_date[0] + '</span>' );
					        	}

					        	if (typeof $response.end_date != 'undefined') {
					        		$( ".end-date" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.end_date[0] + '</span>' );
					        	}

					        	if (typeof $response.position != 'undefined') {
					        		$( ".position" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.position[0] + '</span>' );
					        	}

					        	if (typeof $response.company_name != 'undefined') {
					        		$( ".company-name" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.company_name[0] + '</span>' );
					        	}

					        	if (typeof $response.description != 'undefined') {
					        		$( ".description" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.description[0] + '</span>' );
					        	}
					        }
		        		}
	        		}
	        	}
	        }
	    });
	});
}

/**
@author: VuTran
**/
function exprience_detail_form(){
	$('.exprience-btn-edit').on('click',function(){
		$('#form-add-kn').slideDown('fast');
		$('#form-add-kn').next().find('.clone-btn').fadeOut('fast');
		var id = $(this).data('id'),
			type= $(this).parents('ul').attr('id'),
			url = $(this).attr('data-url');
		$.ajax({
	        type: 'POST',
	        url: url,
	        data:  {
            	id:id,
	        },
	        success: function($response) {
	        	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
        			window.location.replace($response.url);
	        	} else {
	        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
            			$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
            		} else {
						$('#start_date').val($response.start_date);
						$('#end_date').val($response.end_date);
						$('#position').val($response.position);
						$('#company_name').val($response.company_name);
						$('#description').val($response.description);
						$('.exprience-submit').html('<button type="button" class="btn btn-danger btn-big update-experience">Cập nhật </button><input name="id" class="exprience-id" type="hidden" value="' + $response.id +'">');
						$("#form-expericence").attr("action", $response.action);
						$(".exprience-reset").show();
						initializeBitCal();
	            		main_tu_tao_ho_so();
            		}
            	}
	        }
	    });
		return false;
	})
}
function button_reset(text,action){
	$( '.snake_loading' ).remove();
	$( '.alert' ).remove();
	$( '.mess-input' ).remove();
	$( '.exprience-id' ).remove();
	$('.exprience-submit button').text(text);
	$("#form-expericence").attr("action", action);
}
/**
@author: VuTran
**/
function exprience_edit_form(){
	$(".update-experience").on('click', function(e){
	    e.preventDefault();
	    $('#load-ajax').html('<center><span class="snake_loading"></span></center>');
	    var url = $('#form-expericence').attr('action'),
	        data = $("#form-expericence").serialize();
	    $.ajax({
	        type: 'POST',
	        url: url,
	        data: data,
	        success: function($response) {
	        	$( '.snake_loading' ).remove();
	        	$( '.alert' ).remove();
	        	$( '.mess-input' ).remove();
	        	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
	        			window.location.replace($response.url);
	        	} else {
	        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
            			$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
            		} else {
            			if (typeof $response.status != 'undefined' && $response.status == 200) {
            				$.ajax({
					            type: 'POST',
					            url: $response.list_experience,
					            success: function(response) {
					            	if (typeof response.exception != 'undefined' && response.exception == 400 && response.url != 'undefined') {
					            		window.location.replace($response.url);
					            	} else {
					            		if (typeof response.exception != 'undefined' && response.exception != 400) {
					            			$( ".mess-list-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Hồ sơ người tìm việc không tồn tại</div>' );
					            		} else {
					            			$('#kinh-nghiem').html(response);
					            		}
					            	}

					            	initializeBitCal();
					            	main_tu_tao_ho_so();
					            }
					     	});
					     	$( ".mess-experience-global" ).html( '<div class="alert alert-success"><div class="box-icon"></div>Cập nhật kinh nghiệm thành công!</div>' );
					     	$('#start_date').val($response.start_date);
							$('#end_date').val($response.end_date);
							$('#position').val($response.position);
							$('#company_name').val($response.company_name);
							$('#description').val($response.description);
				        } else {
				        	if (typeof $response.mess_start_date != 'undefined' || $response.mess_end_date != 'undefined' || $response.mess_position != 'undefined' || $response.mess_company_name != 'undefined' || $response.mess_description != 'undefined') {
					        		$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Thông tin không hợp lệ</div>' );
				        	}

				        	if (typeof $response.mess_start_date != 'undefined') {
				        		$( ".start-date" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.mess_start_date[0] + '</span>' );
				        	}

				        	if (typeof $response.mess_end_date != 'undefined') {
				        		$( ".end-date" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.mess_end_date[0] + '</span>' );
				        	}

				        	if (typeof $response.mess_position != 'undefined') {
				        		$( ".position" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.mess_position[0] + '</span>' );
				        	}

				        	if (typeof $response.mess_company_name != 'undefined') {
				        		$( ".company-name" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.mess_company_name[0] + '</span>' );
				        	}

				        	if (typeof $response.mess_description != 'undefined') {
				        		$( ".description" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.mess_description[0] + '</span>' );
				        	}
				        }
				    }
				}
	        }
	    });
	});
}

/**
@author: VuTran
**/
function exprience_delete_form(){
	$('.exprience-btn-delete').on('click',function(){
		$this = $(this);
		var id = $(this).data('id'),
			type= $(this).parents('ul').attr('id'),
			url = $(this).attr('data-url');
		$('body').append('<div class="overlay"></div>');
		$('body').append('\
			<div class="alert-popup alert alert-danger">\
				Bạn thực sự muốn xóa mục này ?\
				<div class="action-bottom">\
					<span class="no-delete btn btn-primary white w150">Không xóa</span>\
					<span class="ok-delete btn btn-danger white w150">Đồng ý</span>\
				</div>\
			</div>\
		');
		$('body').on('click','.ok-delete',function(){
			$.ajax({
	            type: 'POST',
	            url: url,
	            data: {
	            	id:id
	            },
	            success: function($response) {
	            	$this.parents('li').css('background','#F5CACA').animate({
						'opacity':'0'
					}).fadeOut('fast');
					$('.overlay').fadeOut('fast',function(){$(this).remove();});
					$('.alert-popup').fadeOut('fast',function(){$(this).remove();});
	            	$( '.snake_loading' ).remove();
	        		$( '.alert' ).remove();
	        		$( '.mess-input' ).remove();
	        		$("#form-expericence")[0].reset();

	        		if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
	        			window.location.replace($response.url);
		        	} else {
		        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
	            			$( ".mess-list-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
	            		} else {
		            		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
		            			$( ".mess-list-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Hồ sơ người tìm việc không tồn tại</div>' );
		            		} else {
		            			if (typeof $response.status != 'undefined' && $response.status == 200) {
			            			$.ajax({
							            type: 'POST',
							            url: $response.list_experience,
							            success: function(response) {
							            	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
							            		window.location.replace($response.url);
							            	} else {
							            		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
							            			$( ".mess-list-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Hồ sơ người tìm việc không tồn tại</div>' );
							            		} else {
							            			$('#kinh-nghiem').html(response);
							            			initializeBitCal();
	            									main_tu_tao_ho_so();
							            		}
							            	}

							            }
							     	});
			            		}
		            		}
	            		}
	            	}
	            }
        	});

		});
		$('body').on('click','.no-delete',function(){
			$('.overlay').fadeOut('fast',function(){$(this).remove();});
			$('.alert-popup').fadeOut('fast',function(){$(this).remove();});
		});
		$('body').on('click','.overlay',function(){
			$('.overlay').fadeOut('fast',function(){$(this).remove();});
			$('.alert-popup').fadeOut('fast',function(){$(this).remove();});
		});
		return false;
	})
}

/** Consultor **/

/**
@author: VuTran
**/
function consultor_add_form(){
	$( "#form-consultor" ).submit(function(e){
		alert('demo');
	    e.preventDefault();
	    $('#consultor-load-ajax').html( '<center><span class="snake_loading"></span></center>' );
	    var url = $(this).attr( 'action' ),
	        data = $( "#form-consultor" ).serialize();
	    $.ajax({
	        type: 'POST',
	        url: url,
	        data: data,
	        dataType: "json",
	        success: function($response) {
	        	$( '.snake_loading' ).remove();
	        	$( '.alert' ).remove();
	        	$( '.mess-input' ).remove();

	        	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
        			window.location.replace($response.url);
	        	} else {
	        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
        				$( ".mess-consultor-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
		        	} else {
			        	if (typeof $response.status != 'undefined' && $response.status == 200) {
			        		$.ajax({
					            type: 'POST',
					            url: $response.list_consultor,
					            success: function(response) {
					            	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
					            		window.location.replace($response.url);
					            	} else {
					            		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
					            			$( ".mess-list-consultor-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Hồ sơ người tìm việc không tồn tại</div>' );
					            		} else {
					            			$('#nguoi-tham-khao').html(response);
					            			main_tu_tao_ho_so();
					            		}
					            	}

					            }
				        	});
			        		$("#form-consultor")[0].reset();
			        		$( ".mess-consultor-global" ).html( '<div class="alert alert-success"><div class="box-icon"></div>Thêm mới người tham khảo thành công</div>' );

			        	} else {
			        		if (typeof $response.status != 'undefined' && $response.status != 200) {
			        			$( ".mess-consultor-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.status + '<div>' );
			        		} else {
					        	if (typeof $response.name != 'undefined' || $response.email != 'undefined' || $response.phone != 'undefined' || $response.company_name != 'undefined' || $response.position != 'undefined') {
					        		$( ".mess-consultor-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Thông tin không hợp lệ</div>' );
					        	}

					        	if (typeof $response.name != 'undefined') {
					        		$( ".consultor-name" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.name[0] + '</span>' );
					        	}

					        	if (typeof $response.email != 'undefined') {
					        		$( ".consultor-email" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.email[0] + '</span>' );
					        	}

					        	if (typeof $response.phone != 'undefined') {
					        		$( ".consultor-phone" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.phone[0] + '</span>' );
					        	}

					        	if (typeof $response.company_name != 'undefined') {
					        		$( ".consultor-company-name" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.company_name[0] + '</span>' );
					        	}

					        	if (typeof $response.position != 'undefined') {
					        		$( ".consultor-position" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.position[0] + '</span>' );
					        	}
					        }
		        		}
	        		}
	        	}
	        }
	    });
	});
}

/**
@author: VuTran
**/
function consultor_detail_form(){
	$('.consultor-btn-edit').on('click',function(){
		$('#form-consultor').slideDown('fast');
		$('#form-consultor').next().find('.clone-btn').fadeOut('fast');
		var id = $(this).data('id'),
			type= $(this).parents('ul').attr('id'),
			url = $(this).attr('data-url');
		$.ajax({
	        type: 'POST',
	        url: url,
	        data:  {
            	id:id,
	        },
	        success: function($response) {
	        	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
        			window.location.replace($response.url);
	        	} else {
	        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
            			$( ".mess-consultor-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
            		} else {
						$('#consultor-name').val($response.name);
						$('#consultor-position').val($response.position);
						$('#consultor-company-name').val($response.company_name);
						$('#consultor-email').val($response.email);
						$('#consultor-phone').val($response.phone);
						$('.consultor-submit').html('<button type="button" class="btn btn-danger btn-big update-consultor">Cập nhật </button><input name="id" class="consultor-id" type="hidden" value="' + $response.id +'">');
						$("#form-consultor").attr("action", $response.action);
						$(".consultor-reset").show();
						initializeBitCal();
	            		main_tu_tao_ho_so();
            		}
            	}
	        }
	    });
		return false;
	})
}
function consultor_button_reset(text,action){
	$( '.snake_loading' ).remove();
	$( '.alert' ).remove();
	$( '.mess-input' ).remove();
	$( '.consultor-id' ).remove();
	$('.consultor-submit button').text(text);
	$("#form-consultor").attr("action", action);
}
/**
@author: VuTran
**/
function consultor_edit_form(){
	$(".update-consultor").on('click', function(e){
	    e.preventDefault();
	    $('#consultor-load-ajax').html('<center><span class="snake_loading"></span></center>');
	    var url = $('#form-consultor').attr('action'),
	        data = $("#form-consultor").serialize();
	    $.ajax({
	        type: 'POST',
	        url: url,
	        data: data,
	        success: function($response) {
	        	$( '.snake_loading' ).remove();
	        	$( '.alert' ).remove();
	        	$( '.mess-input' ).remove();
	        	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
	        			window.location.replace($response.url);
	        	} else {
	        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
            			$( ".mess-consultor-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
            		} else {
            			if (typeof $response.status != 'undefined' && $response.status == 200) {
            				$.ajax({
					            type: 'POST',
					            url: $response.list_consultor,
					            success: function(response) {
					            	if (typeof response.exception != 'undefined' && response.exception == 400 && response.url != 'undefined') {
					            		window.location.replace($response.url);
					            	} else {
					            		if (typeof response.exception != 'undefined' && response.exception != 400) {
					            			$( ".mess-list-consultor-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Hồ sơ người tìm việc không tồn tại</div>' );
					            		} else {
					            			$('#nguoi-tham-khao').html(response);
					            		}
					            	}

					            	initializeBitCal();
					            	main_tu_tao_ho_so();
					            }
					     	});
					     	$( ".mess-consultor-global" ).html( '<div class="alert alert-success"><div class="box-icon"></div>Cập nhật kinh nghiệm thành công!</div>' );
					     	$('#consultor-name').val($response.name);
							$('#consultor-position').val($response.position);
							$('#consultor-company-name').val($response.company_name);
							$('#consultor-email').val($response.email);
							$('#consultor-phone').val($response.phone);
				        } else {
				        	if (typeof $response.name != 'undefined' || $response.email != 'undefined' || $response.phone != 'undefined' || $response.company_name != 'undefined' || $response.position != 'undefined') {
				        		$( ".mess-consultor-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Thông tin không hợp lệ</div>' );
				        	}

				        	if (typeof $response.name != 'undefined') {
				        		$( ".consultor-name" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.name[0] + '</span>' );
				        	}

				        	if (typeof $response.email != 'undefined') {
				        		$( ".consultor-email" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.email[0] + '</span>' );
				        	}

				        	if (typeof $response.phone != 'undefined') {
				        		$( ".consultor-phone" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.phone[0] + '</span>' );
				        	}

				        	if (typeof $response.company_name != 'undefined') {
				        		$( ".consultor-company-name" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.company_name[0] + '</span>' );
				        	}

				        	if (typeof $response.position != 'undefined') {
				        		$( ".consultor-position" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.position[0] + '</span>' );
				        	}
				        }
				    }
				}
	        }
	    });
	});
}

/**
@author: VuTran
**/
function consultor_delete_form(){
	$('.consultor-btn-delete').on('click',function(){
		$this = $(this);
		var id = $(this).data('id'),
			type= $(this).parents('ul').attr('id'),
			url = $(this).attr('data-url');
		$('body').append('<div class="overlay"></div>');
		$('body').append('\
			<div class="alert-popup alert alert-danger">\
				Bạn thực sự muốn xóa mục này ?\
				<div class="action-bottom">\
					<span class="no-delete btn btn-primary white w150">Không xóa</span>\
					<span class="ok-delete btn btn-danger white w150">Đồng ý</span>\
				</div>\
			</div>\
		');
		$('body').on('click','.ok-delete',function(){
			$.ajax({
	            type: 'POST',
	            url: url,
	            data: {
	            	id:id
	            },
	            success: function($response) {
	            	$this.parents('li').css('background','#F5CACA').animate({
						'opacity':'0'
					}).fadeOut('fast');
					$( '.overlay' ).fadeOut('fast',function(){$(this).remove();});
					$( '.alert-popup' ).fadeOut('fast',function(){$(this).remove();});
	            	$( '.snake_loading' ).remove();
	        		$( '.alert' ).remove();
	        		$( '.mess-input' ).remove();
	        		$("#form-consultor")[0].reset();

	        		if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
	        			window.location.replace($response.url);
		        	} else {
		        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
	            			$( ".mess-list-consultor-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
	            		} else {
	            			if (typeof $response.status != 'undefined' && $response.status == 200) {
		            			$.ajax({
						            type: 'POST',
						            url: $response.list_consultor,
						            success: function(response) {
						            	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
						            		window.location.replace($response.url);
						            	} else {
						            		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
						            			$( ".mess-list-consultor-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Hồ sơ người tìm việc không tồn tại</div>' );
						            		} else {
						            			$('#nguoi-tham-khao').html(response);
						            			initializeBitCal();
    											main_tu_tao_ho_so();
						            		}
						            	}
						            }
						     	});
		            		}
	            		}
	            	}
	            }
        	});
		});
		$('body').on('click','.no-delete',function(){
			$('.overlay').fadeOut('fast',function(){$(this).remove();});
			$('.alert-popup').fadeOut('fast',function(){$(this).remove();});
		});
		$('body').on('click','.overlay',function(){
			$('.overlay').fadeOut('fast',function(){$(this).remove();});
			$('.alert-popup').fadeOut('fast',function(){$(this).remove();});
		});
		return false;
	})
}

/** Diploma **/

/**
@author: VuTran
**/
function diploma_add_form(){
	$( "#form-diploma" ).submit(function(e){
	    e.preventDefault();
	    $('#load-ajax').html( '<center><span class="snake_loading"></span></center>' );
	    var url = $(this).attr( 'action' ),
	        data = $( "#form-expericence" ).serialize();
	    $.ajax({
	        type: 'POST',
	        url: url,
	        data: data,
	        dataType: "json",
	        success: function($response) {
	        	$( '.snake_loading' ).remove();
	        	$( '.alert' ).remove();
	        	$( '.mess-input' ).remove();

	        	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
        			window.location.replace($response.url);
	        	} else {
	        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
        				$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
		        	} else {
			        	if (typeof $response.status != 'undefined' && $response.status == 200) {
			        		$.ajax({
					            type: 'POST',
					            url: $response.list_experience,
					            success: function(response) {
					            	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
					            		window.location.replace($response.url);
					            	} else {
					            		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
					            			$( ".mess-list-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Hồ sơ người tìm việc không tồn tại</div>' );
					            		} else {
					            			$('#kinh-nghiem').html(response);
					            			main_tu_tao_ho_so();
					            		}
					            	}

					            }
				        	});
			        		$("#form-expericence")[0].reset();
			        		$( ".mess-experience-global" ).html( '<div class="alert alert-success"><div class="box-icon"></div>Thêm mới kinh nghiệm thành công</div>' );

			        	} else {
			        		if (typeof $response.status != 'undefined' && $response.status != 200) {
			        			$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.status + '<div>' );
			        		} else {
					        	if (typeof $response.start_date != 'undefined' || $response.end_date != 'undefined' || $response.position != 'undefined' || $response.company_name != 'undefined' || $response.description != 'undefined') {
					        		$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Thông tin không hợp lệ</div>' );
					        	}

					        	if (typeof $response.start_date != 'undefined') {
					        		$( ".start-date" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.start_date[0] + '</span>' );
					        	}

					        	if (typeof $response.end_date != 'undefined') {
					        		$( ".end-date" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.end_date[0] + '</span>' );
					        	}

					        	if (typeof $response.position != 'undefined') {
					        		$( ".position" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.position[0] + '</span>' );
					        	}

					        	if (typeof $response.company_name != 'undefined') {
					        		$( ".company-name" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.company_name[0] + '</span>' );
					        	}

					        	if (typeof $response.description != 'undefined') {
					        		$( ".description" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.description[0] + '</span>' );
					        	}
					        }
		        		}
	        		}
	        	}
	        }
	    });
	});
}

/**
@author: VuTran
**/
function diploma_detail_form(){
	$('.diploma-btn-edit').on('click',function(){
		$('#form-add-kn').slideDown('fast');
		$('#form-add-kn').next().find('.clone-btn').fadeOut('fast');
		var id = $(this).data('id'),
			type= $(this).parents('ul').attr('id'),
			url = $(this).attr('data-url');
		$.ajax({
	        type: 'POST',
	        url: url,
	        data:  {
            	id:id,
	        },
	        success: function($response) {
	        	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
        			window.location.replace($response.url);
	        	} else {
	        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
            			$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
            		} else {
						$('#start_date').val($response.start_date);
						$('#end_date').val($response.end_date);
						$('#position').val($response.position);
						$('#company_name').val($response.company_name);
						$('#description').val($response.description);
						$('.exprience-submit').html('<button type="button" class="btn btn-danger btn-big update-experience">Cập nhật </button><input name="id" class="exprience-id" type="hidden" value="' + $response.id +'">');
						$("#form-expericence").attr("action", $response.action);
						$(".exprience-reset").show();
						initializeBitCal();
	            		main_tu_tao_ho_so();
            		}
            	}
	        }
	    });
		return false;
	})
}
function diploma_button_reset(text,action){
	$( '.snake_loading' ).remove();
	$( '.alert' ).remove();
	$( '.mess-input' ).remove();
	$( '.exprience-id' ).remove();
	$('.exprience-submit button').text(text);
	$("#form-expericence").attr("action", action);
}
/**
@author: VuTran
**/
function diploma_edit_form(){
	$(".update-experience").on('click', function(e){
	    e.preventDefault();
	    $('#load-ajax').html('<center><span class="snake_loading"></span></center>');
	    var url = $('#form-expericence').attr('action'),
	        data = $("#form-expericence").serialize();
	    $.ajax({
	        type: 'POST',
	        url: url,
	        data: data,
	        success: function($response) {
	        	$( '.snake_loading' ).remove();
	        	$( '.alert' ).remove();
	        	$( '.mess-input' ).remove();
	        	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
	        			window.location.replace($response.url);
	        	} else {
	        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
            			$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
            		} else {
            			if (typeof $response.status != 'undefined' && $response.status == 200) {
            				$.ajax({
					            type: 'POST',
					            url: $response.list_experience,
					            success: function(response) {
					            	if (typeof response.exception != 'undefined' && response.exception == 400 && response.url != 'undefined') {
					            		window.location.replace($response.url);
					            	} else {
					            		if (typeof response.exception != 'undefined' && response.exception != 400) {
					            			$( ".mess-list-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Hồ sơ người tìm việc không tồn tại</div>' );
					            		} else {
					            			$('#kinh-nghiem').html(response);
					            		}
					            	}

					            	initializeBitCal();
					            	main_tu_tao_ho_so();
					            }
					     	});
					     	$( ".mess-experience-global" ).html( '<div class="alert alert-success"><div class="box-icon"></div>Cập nhật kinh nghiệm thành công!</div>' );
					     	$('#start_date').val($response.start_date);
							$('#end_date').val($response.end_date);
							$('#position').val($response.position);
							$('#company_name').val($response.company_name);
							$('#description').val($response.description);
				        } else {
				        	if (typeof $response.mess_start_date != 'undefined' || $response.mess_end_date != 'undefined' || $response.mess_position != 'undefined' || $response.mess_company_name != 'undefined' || $response.mess_description != 'undefined') {
					        		$( ".mess-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Thông tin không hợp lệ</div>' );
				        	}

				        	if (typeof $response.mess_start_date != 'undefined') {
				        		$( ".start-date" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.mess_start_date[0] + '</span>' );
				        	}

				        	if (typeof $response.mess_end_date != 'undefined') {
				        		$( ".end-date" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.mess_end_date[0] + '</span>' );
				        	}

				        	if (typeof $response.mess_position != 'undefined') {
				        		$( ".position" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.mess_position[0] + '</span>' );
				        	}

				        	if (typeof $response.mess_company_name != 'undefined') {
				        		$( ".company-name" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.mess_company_name[0] + '</span>' );
				        	}

				        	if (typeof $response.mess_description != 'undefined') {
				        		$( ".description" ).append( '<span class="mess-input mess-error" data-type="error">' + $response.mess_description[0] + '</span>' );
				        	}
				        }
				    }
				}
	        }
	    });
	});
}

/**
@author: VuTran
**/
function diploma_delete_form(){
	$('.btn-delete').on('click',function(){
		$this = $(this);
		var id = $(this).data('id'),
			type= $(this).parents('ul').attr('id'),
			url = $(this).attr('data-url');
		$('body').append('<div class="overlay"></div>');
		$('body').append('\
			<div class="alert-popup alert alert-danger">\
				Bạn thực sự muốn xóa mục này ?\
				<div class="action-bottom">\
					<span class="no-delete btn btn-primary white w150">Không xóa</span>\
					<span class="ok-delete btn btn-danger white w150">Đồng ý</span>\
				</div>\
			</div>\
		');
		$('body').on('click','.ok-delete',function(){
			$.ajax({
	            type: 'POST',
	            url: url,
	            data: {
	            	id:id
	            },
	            success: function($response) {
	            	$this.parents('li').css('background','#F5CACA').animate({
						'opacity':'0'
					}).fadeOut('fast');
					$('.overlay').fadeOut('fast',function(){$(this).remove();});
					$('.alert-popup').fadeOut('fast',function(){$(this).remove();});
	            	$( '.snake_loading' ).remove();
	        		$( '.alert' ).remove();
	        		$( '.mess-input' ).remove();
	        		$("#form-expericence")[0].reset();

	        		if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
	        			window.location.replace($response.url);
		        	} else {
		        		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
	            			$( ".mess-list-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>' + $response.exception + '</div>' );
	            		} else {
		            		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
		            			$( ".mess-list-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Hồ sơ người tìm việc không tồn tại</div>' );
		            		} else {
		            			if (typeof $response.status != 'undefined' && $response.status == 200) {
			            			$.ajax({
							            type: 'POST',
							            url: $response.list_experience,
							            success: function(response) {
							            	if (typeof $response.exception != 'undefined' && $response.exception == 400 && $response.url != 'undefined') {
							            		window.location.replace($response.url);
							            	} else {
							            		if (typeof $response.exception != 'undefined' && $response.exception != 400) {
							            			$( ".mess-list-experience-global" ).html( '<div class="alert alert-danger"><div class="box-icon"></div>Hồ sơ người tìm việc không tồn tại</div>' );
							            		} else {
							            			$('#kinh-nghiem').html(response);
							            			initializeBitCal();
	            									main_tu_tao_ho_so();
							            		}
							            	}

							            }
							     	});
			            		}
		            		}
	            		}
	            	}
	            }
        	});

		});
		$('body').on('click','.no-delete',function(){
			$('.overlay').fadeOut('fast',function(){$(this).remove();});
			$('.alert-popup').fadeOut('fast',function(){$(this).remove();});
		});
		$('body').on('click','.overlay',function(){
			$('.overlay').fadeOut('fast',function(){$(this).remove();});
			$('.alert-popup').fadeOut('fast',function(){$(this).remove();});
		});
		return false;
	})
}