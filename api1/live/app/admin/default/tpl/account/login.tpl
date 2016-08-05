<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <meta name="robots" content="noindex, nofollow" />
    <meta name="robots" content="noarchive" />
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->
    <link rel="icon" type="image/x-icon" href="{{ theme_url('img/favicon.ico') }}" />
    <link rel="shortcut icon" href="{{ theme_url('img/favicon.ico') }}" />
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300&subset=vietnamese" />  
    <link rel="stylesheet" type="text/css" href="{{ assets_url('webfonts/fontawesome/fontawesome.css') }}" />   
    <link rel="stylesheet" type="text/css" href="{{ theme_url('css/admin.css') }}" />    
    <link rel="stylesheet" type="text/css" href="{{ assets_url('jquery/admin.css') }}" />    
    <link rel="stylesheet" type="text/css" href="{{ assets_url('flexify/admin.css') }}" />    
    <!--[if lt IE 9]>
    <script src="{{ assets_url('iefix/respond.js') }}"></script>
    <script src="{{ assets_url('iefix/selectivizr.js') }}"></script>
    <![endif]-->
    <script type="text/javascript">
        var isInIFrame = (window.location != window.parent.location) ? true : false;
        var globalvars = {
            notify_message  : "{{ flash_message }}",
            notify_css      : "{{ flash_css }}"
        };        
    </script>   
    <script type="text/javascript" src="{{ assets_url('jquery/jquery.js') }}"></script>         
    <script type="text/javascript" src="{{ assets_url('jquery/jquery.ui.js') }}"></script>          
    <script type="text/javascript" src="{{ assets_url('jquery/admin.js') }}"></script>         
    <script type="text/javascript" src="{{ assets_url('flexify/admin.js') }}"></script>         

    <script type="text/javascript">
    $(function() { 

        if(isInIFrame){   
            $('body').css({background:'none', overflowY: 'hidden'});    
        }

        var $loginform = $('#login_form');
        
        $('#login_error').notify({effect:'none', msg: globalvars.notify_message, css: globalvars.notify_css, timeout:5000});     
        $('input[placeholder], textarea[placeholder]').placeholder();
        $("input:checkbox").uniform();  
        $loginform.css({position: 'absolute'});

        set_position();
        
        $(window).on('resize', set_position);
        
        function set_position(){
            $loginform.css({
                left: ($(window).width() - $loginform.outerWidth())/2,
                top: ($(window).height() - $loginform.outerHeight())/2
            });        
        }
    });
    </script>                     
</head>
<body>     
<!--[if lt IE 8]>
<div class="alert alert-warning text-center">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</div>
<![endif]-->

<div id="login_form" style="width: 400px;margin: 0 auto;font-family:'Roboto',sans-serif;">
	<form name="form_edit" id="form_edit" method="POST" action="{{s_action}}">
	    <input type="hidden" name="furl" value="{{furl}}">
	    <table class="table table-lg">
		    <tr>
			    <td class="head">
                    <div style="background: transparent url('{{ theme_url('img/biglogo_dark.png') }}') 0 0 no-repeat; width:310px;height:96px;margin: 0 auto;"></div>
                </td>
	       </tr>
		    <tr>
			    <td>
                    <div id="login_error"></div>
                    <input class="input input-lg space-bottom" type="text" name="email" id="email" placeholder="{{ t('global:email') }}" />
                    <input class="input input-lg" type="password" name="password" id="password" placeholder="{{ t('global:password') }}" />
                </td>  
	       </tr>
		    <tr>
			    <td>
                    <div class="box-horizontal">
                        <div class="left" style="margin-top:16px;"><input type="checkbox" name="remember" value="1" /> <span class="text-muted">{{ t('global:remember') }}</span></div>    
                        <div class="right"><button class="btn btn-primary btn-lg" type="submit" name="save">{{ t('global:button_login') }}</button></div>    
                    </div>
                </td>         
	       </tr>
	    </table>
	</form> 
</div>
</body>
</html>  

