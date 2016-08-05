<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Hệ Thống Quản Trị</title>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <meta name="robots" content="noindex, nofollow" />
    <meta name="robots" content="noarchive" />
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->
    <link rel="icon" type="image/x-icon" href="{{ theme_url('img/favicon.ico') }}" />
    <link rel="shortcut icon" href="{{ theme_url('img/favicon.ico') }}" />
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400&subset=vietnamese" />  
    <link rel="stylesheet" type="text/css" href="{{ assets_url('webfonts/fontawesome/fontawesome.css') }}" /> 
    {{ if environment == 'dev' }}
    <link rel="stylesheet" type="text/css" href="/static/min.php?t=css&f=admin/{{theme_name}}/css/admin.css&sources[admin/{{theme_name}}/css]=admin.less" />        
    <link rel="stylesheet" type="text/css" href="/static/min.php?t=css&f=assets/jquery/admin.css&sources[assets/jquery/css]=uniform.css" />        
    <link rel="stylesheet" type="text/css" href="/static/min.php?t=css&f=assets/flexify/admin.css&sources[assets/flexify/css]=notify.css,tooltip.css,collapse.css,tab.css,floating.css,autocomplete.css,colorpicker.css,datepicker.css,modalbox.css,nestable.css,selectbox.css,scrollbar.css,placeholder.css" />
    {{ else }}
    <link rel="stylesheet" type="text/css" href="{{ theme_url('css/admin.css') }}" />      
    <link rel="stylesheet" type="text/css" href="{{ assets_url('jquery/admin.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ assets_url('flexify/admin.css') }}" />    
    {{ endif }}
    <!--[if lt IE 9]>
    <script src="{{ assets_url('iefix/respond.js') }}"></script>
    <script src="{{ assets_url('iefix/selectivizr.js') }}"></script>
    <![endif]-->
    <script type="text/javascript">
        var globalvars = {
                base_url        : "{{ base_url() }}",
                assets_url      : "{{ assets_url() }}",
                theme_url       : "{{ theme_url() }}",
                drive_account   : "{{ app_config('drive_account') }}",
                notify_message  : "{{ flash_message }}",
                notify_css      : "{{ flash_css }}",
                module_name     : "{{ lower_module_name }}",
                date_format     : "{{ sys_config('date_format') }}",
                decimal         : {{ sys_config('decimal') }},
                decimal_point   : "{{ sys_config('decimal_point') }}",
                thousands_sep   : "{{ sys_config('thousands_sep') }}",
                {{ if module == 'config.links' }}
                    nesteable_level : {{ sys_config('links_level') }}+1,
                    nesteable_grouped: false
                {{ else }}
                    nesteable_level : {{ sys_config('categories_level') }},
                    nesteable_grouped: false                
                {{ endif }}
        };          

    </script>
    <script type="text/javascript" src="{{ assets_url('jquery/jquery.js') }}"></script>         
    <script type="text/javascript" src="{{ assets_url('jquery/jquery.ui.js') }}"></script>         
    {{ if environment == 'dev' }}
    <script type="text/javascript" src="/static/min.php?t=js&f=assets/jquery/admin.js&sources[assets/jquery/js]=cookie.js,each2.js,in_array.js,combodate.js,uniform.js"></script>         
    <script type="text/javascript" src="/static/min.php?t=js&f=assets/flexify/admin.js&sources[assets/flexify/js]=notify.js,tooltip.js,moneyinput.js,floating.js,hoverdelay.js,shorter.js,collapse.js,dropdown.js,tab.js,autocomplete.js,colorpicker.js,datepicker.js,modalbox.js,nestable.js,selectbox.js,scrollbar.js,placeholder.js,editable.js,slugify.js"></script>         
    {{ else }}
    <script type="text/javascript" src="{{ assets_url('jquery/admin.js') }}"></script>
    <script type="text/javascript" src="{{ assets_url('flexify/admin.js') }}"></script>   
    {{ endif }}
    <script type="text/javascript" src="{{ assets_url('liveeditor/scripts/innovaeditor.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ assets_url('liveeditor/scripts/style/istoolbar.css') }}" /> 
    <script type="text/javascript" src="{{ assets_url('liveeditor/scripts/istoolbar.js') }}"></script>
    <script type="text/javascript" src="{{ assets_url('liveeditor/scripts/language/en-US/editor_lang.js') }}"></script>
    <script type="text/javascript" src="{{ assets_url('liveeditor/scripts' ~ editor_dir ~ 'editor.js') }}"></script>
    <script type="text/javascript" src="{{ theme_url('js/admin.js') }}"></script>        
</head>
<body>

<!--[if lt IE 9]>
<div class="alert alert-warning text-center">Bạn đang sử dụng một trình duyệt <strong>quá cũ</strong>. Hãy <a href="http://browsehappy.com/">nâng cấp lên trình duyệt mới</a> để duyệt web nhanh hơn.</div>
<![endif]-->

<div class="header" id="layout_header">
    <div class="header-inside">      
        <div class="logo" style="background-image: url('{{ theme_url('img/logo_dark.png') }}'); background-position: center center; background-repeat: no-repeat;"><a href="{{ base_url() }}" title="Admin">ADMIN</a></div>
        <div class="menu">
            <div class="left">
                <a href="/" title="Xem website" target="_blank"><i class="icon icon-external-link-sign"></i> Xem website</a>
            </div>
            <div class="right">
                <a href="{{ url('account.account') }}" title="Thông tin cá nhân" class="modal" rel="form" modal-width="500">{{l_hello}}</a>
                <a href="{{ url('account.logout') }}"><i class="icon icon-signout"></i> Thoát</a>       
                {{ if is_admin == 1 }}     
                <a href="{{ url('config.config') }}" class="active"><i class="icon icon-gear"></i> Cài đặt</a>            
                {{ endif }}
            </div>
        </div>                                          
    </div>                
</div>       
         
<div class="container" id="layout_container">
    
    <div class="sidebar" id="layout_sidebar">   
        <ul>
        {{ for item in sidebar_menu }}
            <!--<li class="head">{{ item.name }}</li>-->
            {{ for menu in item.menus }}
            <li><a class="{{menu.css}}" href="{{menu.url}}">{{menu.icon}} {{menu.name}}</a></li>
            {{ endfor }}
        {{ endfor }}
        </ul>        
    </div><!--end .sidebar-->
    
    <div class="content" id="layout_content"> 
        
        <div class="content-header" id="content_header">
            {{if has_relate_menu}}
                <ul class="header-menu">
                {{for menu in relate_menu}}
                    <li><a class="{{menu.css}}" href="{{menu.url}}"{{relate_menu.attributes}}>{{menu.name}}</a></li>
                {{endfor}}
                </ul>   
            {{endif}}
            
            <div class="header-title box-horizontal">
                <div class="title">{{module_name}}{{module_action_title}}</div>
                <div class="action">{{u_add}}{{s_button}}</div>
            </div>
        </div> 

        <div class="content-body" id="content_body">
        
            
        
