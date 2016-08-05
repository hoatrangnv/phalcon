/*
Author: TranQuangMinh  
Email: Thachphathieng@gmail.com
 */
(function($){
    $.fn.prettyImg = function(options) {   
        var settings = $.extend({
                'align': 'middle',
                'mode':'fit',
                'ratio': 1.3
            }, options);  
        return this.each(function(){ 
            var ratio = $(this).attr('data-ratio');
            if( !ratio ){
                ratio = settings['ratio'];
            }
            var width = $(this).width();
            $(this).height( width/ratio  ).addClass('fixed-frame align-'+settings['align']);
            _fixImg(settings['mode'],$(this));
        });
        // private method.
        function _fixImg(mode,$this){
            switch(mode){
                case 'fill':
                    $this.find('img').width('100%').height('auto');
                    $this.css('overflow','hidden');
                    if( $this.find('img').height() < $this.height() ){
                       $this.find('img').height('100%').width("auto").css({"max-width":"none",'max-height':'100%'});
                    }
                    $this.find('img').css({
                        'margin-left': ( ( $this.width() -  $this.find('img').width() )/2 )+'px',
                        'margin-top': ( ( $this.height() -  $this.find('img').height() )/2 )+'px',
                    });
                break;
                case 'fit':
                    $this.find('img').width('100%').height('auto');
                    if( $this.find('img').height() >= $this.outerHeight() ){
                       $this.find('img').height('100%').width("auto");
                    }
                break;
                case 'center':
                    $this.find('img').css({
                        'max-width':'100%',
                    });
                    if( $this.find('img').height() >= $this.outerHeight() ){
                       $this.find('img').width("auto").css('max-height','100%');
                    }
                break;
            }
        }
    }
}(jQuery));