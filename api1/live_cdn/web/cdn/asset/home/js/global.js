 /*MEGA MENU */
(function(){
	// Button show cart mini //
	$('.cart-shop .M-btn').click(function(e){
		$('.mini-cart').stop(true,true).slideToggle('fast');
		return false;
	})
	$('*').not('.cart-shop ,.cart-shop *').click(function(){
		$('.mini-cart').stop(true,true).slideUp('slow');
	})
	// Button show cart mini //
	$('.hover-color').hover(function(){
		$('.hover-color').css({'background-color':''}).removeClass('active');
		$(this).css({'background-color':$(this).data('color-bg')}).addClass('active');
		$(this).parents('.menu-mega').css('border-right','5px solid '+$(this).data('color-bg'))
	},function(){
		//
		//$(this).parents('.menu-mega').css('border-right','0px')
	})
	
	$('.item-mega a').hover(function(){
		var id = $(this).attr('href');
		$('.menu-second .menu').removeClass('active');
		$('.img-menu .img-block').removeClass('active');
		$('.menu-second').find(id).addClass('active');
		$('.img-menu').find(id+'-img').addClass('active');
	},function(){

	});
	$('.hover-color.active').css({'background-color':$('.hover-color.active').data('color-bg')});
	$('.hover-color.active').parents('.menu-mega').css('border-right','5px solid '+$('.hover-color.active').data('color-bg'))
	/*CrEATE MAP*/
	var map;
      map = new GMaps({
        el: '#map',
        lat: 10.770329,
        lng: 106.675039
      });
      map.addMarker({
              lat: 10.770329,
              lng: 106.675039,
              title: 'CÔNG TY HOATUOIDEP',
             icon: "img/icon_map.png"
            });
      /* end Create map*/
	/*CrEATE MAP*/
	$('.list-address .postion-map').hover(function(){
		var lng = $(this).data('long');
		var lat = $(this).data('lat');
		map.panTo({lat:lat,lng:lng});
		map.addMarker({
              lat: lat,
              lng: lng,
              title: 'CÔNG TY HOATUOIDEP',
             icon: "img/icon_map.png"
            }); 

	} ,function(){})
	/*Router Map*/
	$('#geocoding_form').submit(function(e){
		e.preventDefault(); 
		GMaps.geocode({
		  address: $('#from').val(),
		  callback: function(results, status) {
		    if (status == 'OK') {
		      var latlng = results[0].geometry.location;
		      map.setCenter(latlng.lat(), latlng.lng());
		      map.removeMarkers();
		      map.addMarker({
	              lat: 10.770329,
	              lng: 106.675039,
	              title: 'CÔNG TY HOATUOIDEP',
	             icon: "img/icon_map.png"
            	});
		      map.addMarker({
		        lat: latlng.lat(),
		        lng: latlng.lng()
		      });
		      /*Router */
		      map.removePolylines();
		      map.travelRoute({
			  origin: [latlng.lat(), latlng.lng()],
			  destination: [10.770329, 106.675039],
			  travelMode: 'driving',
			  step: function(e) {
			  	 map.drawPolyline({
			        path: e.path,
			        strokeColor: '#27983F',
			        strokeOpacity: 0.6,
			        strokeWeight: 6
			      }); 
			  }
			  
				});
		    }
		  }
		});
	})
	$('.my-location').click(function(){
		$('.text-location .mark').fadeIn('fast')
		GMaps.geolocate({
		  success: function(position) {
		    map.setCenter(position.coords.latitude, position.coords.longitude);
		    map.removeMarkers();
		    map.addMarker({
              lat: 10.770329,
              lng: 106.675039,
              title: 'CÔNG TY HOATUOIDEP',
             icon: "img/icon_map.png"
            });
		    map.addMarker({
		        lat: position.coords.latitude,
		        lng: position.coords.longitude
		      });
		    $('#from').val(position.coords.latitude+','+position.coords.longitude)
		  },
		  error: function(error) {
		    alert('Lỗi vị trí: '+error.message);
		  },
		  not_supported: function() {
		    alert("Trình duyệt của bạn không hỗ trợ lấy vị trị, vui lòng đổi trình duyệt và  thử lại sau. ");
		  },
		  always: function() {
			$('.text-location .mark').fadeOut('slow')

		  }
		});
	})
	/*Router Map*/

})();

$(window).load(function(){
	$('.menu-mega ,.menu-second, .img-menu ').balanced_col();
	$('.fa-info-circle').hover(function(){
		var content = $(this).data('content');
		var pos = $(this).offset();
		var left = (pos.left)-250;
		var bottom = $(window).height() - pos.top;
		var tooltip = '\
			<div style="left:'+left+'px;bottom:'+bottom+'px;" class="content-tooltip">\
				'+content+'\
			</div>\
		';
		$('body').append(tooltip);
	},function(){
		$('.content-tooltip').fadeOut('fast',function(){$(this).remove();})
	})
})
/* MEGA MENU */
$(window).scroll(function(){
	scrollSticky();
})

$(function(){
	fixImageByRatio();
    /* Chỉnh sửa thanh tìm kiếm trên header  26/11/2014 */
    $(".style-select").select2({
        placeholder: "Chọn danh mục để tìm kiếm",
        allowClear: true
    });
    $('.select-category').hover(function(){
        $(this).find('.style-select').select2('open')
    },function(){})
    $(document).on('mousemove','#select2-drop-mask',function(event){
        var y = $( event.pageY );
        if ( event.pageY  > 70  ) {
            $('.select-category').find('.style-select').select2('close');
        }else {
            //$('.select-category').find('.style-select').select2('close');
        }
    });
    /* End: Chỉnh sửa thanh tìm kiếm trên header  26/11/2014 */
});



function fixImageByRatio(){
    $el = $('.block-category .box-item > a:first-child');
    if( $el.size() > 0 && $el.find('img').size() > 0 )
    {
        $(window).load(function(){
            $el.each(function(){
               var ratio = 0.752;
               var width = $(this).width();
               $(this).height( width/ratio  ).css({'position':'relative','background-color': '#ebebeb' });
               $(this).find('img').width('100%').height('auto').css({'position':'absolute','margin':'auto','top': '0','right': '0','left': '0','bottom': 'auto' });
                if( $(this).find('img').height() > $(this).height() ){
                    $(this).find('img').height('100%').width("auto").css('max-height','100%');
                }
                var t =( ( $(this).height() - $(this).find('.name-product').outerHeight() )  - $(this).find('img').height() )/2;
                if(t> 0){
                    $(this).find('img').css({
                        'top': t 
                    });
                }
             });
        }); /*End window LOAD*/


        $(window).resize(function(){
            $el.each(function(){
               var ratio = $(this).data('ratio');
               var width = $(this).width();
               $(this).height( width/ratio  );
               $(this).find('img').width('100%').height('auto');
               if( $(this).find('img').height() > $(this).height() ){
                    $(this).find('img').height('100%').width("auto");
                }
                var t =( ( $(this).height() - $(this).find('.name-product').outerHeight() )  - $(this).find('img').height() )/2;
                if(t> 0){
                    $(this).find('img').css({
                        'top': t 
                    });
                }
            });
        });/*End window RESIZE*/


    }/*End IF*/
}
/*************/