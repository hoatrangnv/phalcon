(function($){
    $.fn.cut_str = function(options) {   
        return this.each(function(){
            var length = 0;
            if( $(this).data('length') ){
              length = $(this).data('length');
            } else {
              length = 30;
            } 
             var settings = $.extend({
                    'length': length,
                    'suffix': '...'
                }, options);     
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

/* ------------------/.   ----------------*/
/* ------------------/ Mr. Tran Quang Minh ---------------------*/
/* -------------------------------------------------------------*/
$.fn.clearForm = function() {
      $('input').iCheck('update');
      $(".select-style").select2("val", "");
  return this.each(function() {
    var type = this.type, 
        tag = this.tagName.toLowerCase();
    if (tag == 'form')
      return $(':input',this).clearForm();
    if (type == 'text' || type == 'password' || tag == 'textarea')
      this.value = '';
    else if (type == 'checkbox' || type == 'radio'){
      this.checked = false;
    }
    else if (tag == 'select')
      this.selectedIndex = -1;
  });
};
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
 
