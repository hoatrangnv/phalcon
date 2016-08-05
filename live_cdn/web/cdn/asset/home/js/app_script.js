(function($){
    $.fn.cut_str = function(options) {     
     var settings = $.extend({
            'length': '30',
            'suffix': '...'
        }, options);     
        return this.each(function(){
            $this = jQuery(this);    
            var value = $.trim($this.text()) ;
            var arr_val = value.split(' ');
            var output = "";  
            var count = 0;
            var suffix = "";
            if(settings['length'] > arr_val.length) 
                {
                    count = arr_val.length;
                    suffix = "";
                }
            else 
                {
                    count = settings['length'];
                    suffix = settings['suffix'];
                }
                for (var i = 0; i < count ; i++) 
                    {
                        output += arr_val[i]+" ";
                    };

            $this.text(output+suffix);
                 
        });
    }
}(jQuery));
 
/* ------------------/. end Cut string function ----------------*/
/* ------------------/ Mr. Tran Quang Minh ---------------------*/
/* -------------------------------------------------------------*/
(function($){
    $.fn.balanced_col = function(options){
        var settings = $.extend({'plus':0},options);
        var max_height = 0;
        $(this).each(function(){ 
               if($(this).height() > max_height){
                    max_height = $(this).height();     
                  };      
           });
       $(this).height(max_height+settings['plus']);
    }
}(jQuery));
/**/
/* HOW TO USE  
    Has option :
    $('selector').balanced_col({
        'plus': ?px // Plus px in to selector after Run this function
    })
    None option :
    $('selector').balanced_col();

*/
//***/ 

/* ------------------/. end Set Height col function ----------------*/
/* ------------------/ Mr. Tran Quang Minh ---------------------*/
/* -------------------------------------------------------------*/
(function($){
    $.fn.counter_chart = function(options){
        var limit = $.extend({'limit':100},options);
        var selector = $.extend({'selector':".counting"},options);

        $this = $(this);
        $this.each(function(){
                var cnt = $this.parent().find('.counting');
                var txt = $(this).val(); 
                var len = txt.length;
                var limit = 120;
                // check if the current length is over the limit
                if(len > limit){
                   $(obj).val(txt.substr(0,limit));
                   $(cnt).html(len-1);
                 } 
                 else { 
                   $(cnt).html(len);
                 }
                 
                 // check if user has less than 20 chars left
                 if(limit-len <= 20) {
                  $(cnt).addClass("warning");
                 }
           });
    };
}(jQuery));

/*  HOW TO USE
    $(document).on('keyup','.c1 , .c2',function(){
        $(this).counter_chart({'limit':100,'selector':'span.counting'});
    } );
*/
/* ------------------/. end counter chart function ----------------*/
/* ------------------/ Mr. Tran Quang Minh ---------------------*/
/* -------------------------------------------------------------*/

/* ------------------/. Add div of table to responsive ----------------*/
/* ------------------/ Mr. Tran Quang Minh ---------------------*/
/* -------------------------------------------------------------*/
(function(){
    if( $(window).width() < 768 ){
            $('table').css({'max-width':'768px'}).wrap('<div class="wrap-table-res" style="overflow-x:scroll; overflow-y: hidden; width: 100%;"></div>');
    }
})()
/* ------------------/. Add div of table to responsive ----------------*/
/* ------------------/ Mr. Tran Quang Minh ---------------------*/
/* -------------------------------------------------------------*/
 

/* ------------------/. Fix top menu ----------------*/
/* ------------------/ Mr. Tran Quang Minh ---------------------*/
/* -------------------------------------------------------------*/

var $stickyHeight = 39; // chiều cao của banner quảng cáo
var $padding = 0; // khoảng cách top của bann     er khi dính
var $topOffset = 0; // khoảng cách từ top của banner khi bắt đầu dính (tức là khoảng cách tính từ trên xuống đến vị trí đặt banner )
var $footerHeight = 190; // Định vị điểm dừng của banner, tính từ chân lên
 
function scrollSticky(){
if(jQuery(window).height() >= $stickyHeight) {
     var aOffset = $(' .header-full ').offset();
if(jQuery(document).height() - $footerHeight - $padding < jQuery(window).scrollTop() + $stickyHeight) {
         var $top = jQuery(document).height() - $stickyHeight - $footerHeight - $padding - 185;
         jQuery(' .header-full ').attr('style', 'position:absolute; top:'+$top+'px;');
     }else if(jQuery(window).scrollTop() + $padding > $topOffset) {
        $('.menu-top,.key-s-suggest ,.promotion-hot .hidden-scroll').slideUp('fast');
            jQuery('.header-full ').children().addClass('neo');
            var height_h = $('.block-search-fix').height()+8;
            $('.mini-cart').css({'top': height_h})
        }else{
            $('.menu-top,.key-s-suggest ,.promotion-hot .hidden-scroll').slideDown('fast');
             //jQuery(' .header-full ').attr('style', 'position:relative;');
             jQuery(' .header-full ').children().removeClass('neo');
            $('.mini-cart').css({'top': 69})
     }
   }
 }

 

/* ------------------/. Fix top menu ----------------*/
/* ------------------/ Mr. Tran Quang Minh ---------------------*/
/* -------------------------------------------------------------*/
 
