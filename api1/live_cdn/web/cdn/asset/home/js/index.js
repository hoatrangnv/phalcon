    $(document).ready(function(){
        $('.list-post .intro').cut_str();
        $('.banner-content').nivoSlider({
            effect: 'fade',
            directionNav: false,
            controlNav: false,
            pauseTime:4000,
            animSpeed: 600
        });
    })
    
    $(window).load(function(){
            $('.list-post li').balanced_col();
    })
    
    $(window).load(function(){
        $('.fix-ratio').each(function(){
            var ratio = $(this).data('ratio');
            var width = $(this).width();
            $(this).height( width/ratio  );
            $(this).find('img').css('max-width','100%').height('auto');
                if( $(this).find('img').height() > $(this).height() ){
                   $(this).find('img').css('height','100%').width("auto");
                }
        });
    });
    
    $(window).resize(function(){
        $('.fix-ratio').each(function(){
            var ratio = $(this).data('ratio');
            var width = $(this).width();
            $(this).height( width/ratio  );
            $(this).find('img').css('max-width','100%').height('auto');
            if( $(this).find('img').height() > $(this).height() ){
               $(this).find('img').css('height','100%').width("auto");
            }
        });
    });
