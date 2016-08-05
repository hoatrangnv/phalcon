
<div class="files">
    <div class="files-header">
        <form id="form_upload" name="form_upload" method="POST" enctype="multipart/form-data">
            <div class="btn-uploader">        
                <input type="file" name="qqfile" id="uploadfile" multiple />
                <span style="-moz-user-select: none;" class="browse-btn">Tải lên từ máy tính...</span>                                    
            </div>
        </form>
    </div>
    <div class="files-container grid" id="filelist">

    </div>  
    <div class="files-footer clearfix">
        <div class="right">
            <div class="btn-group">
                <button class="btn btn-primary" type="button" name="save" id="button_insert">OK</button>
                <button class="btn btn-default" type="button" name="cancel" id="button_cancel">Cancel</button>
            </div>
        </div>
    </div>      
</div>	

<script type="text/javascript" src="/static/assets/lazyload/lazyload.js"></script> 

<script type="text/javascript">

    var queryString    = "{{ query_string }}",
        listUrl         = "/vendor/cloudify/list.php?" + queryString,
        uploadUrl       = "/vendor/cloudify/upload.php?" + queryString,
        deleteUrl       = "/vendor/cloudify/delete.php?" + queryString,
        containerId     = '#filelist',
        selectLimit     = {{ select_limit }},
        destination     = decodeURIComponent("{{ destination }}"),
        previewClass    = "{{ preview_class }}",
        inputName       = "{{ input_name }}";

    (function(){

        var $destination    = $(destination + ' ul'),
            $listing        = $(containerId),
            $nextpage       = $('<a class="btn btn-default btn-sm btn-block next-page" style="width:98%" href="javascript://">Tiếp</a>');    

        $('#uploadfile').on('change', function(){
            var $this = $(this),
                $form = $('#form_upload').attr('action', uploadUrl),
                $iframe = $('<iframe id="uploadstatus" name="uploadstatus" style="position:absolute;top:-9999px;left:-9999px;" src="about:blank" />'),
                $message = $('<div class="alert" style="margin-top:10px;position:absolute;z-index:5;top:45px;width:98%;" />').insertAfter($this.parent()).hide();

            $form.before($iframe);
            $form.attr('target','uploadstatus');
            $iframe.on('load', function(){  
                var result = $iframe.contents().find('body').html();
                setTimeout(function(){ $iframe.remove(); }, 2000);
                var result = $.parseJSON(result);
                if(result.success == false){
                    $message.addClass('alert-danger').html(result.message.replace("\n", "<br />")).slideDown(200);
                } else {
                    $('ul', $listing).prepend('<li><a class="delete" href="javascript://" rel="'+result.url+'" name="'+result.filename+'" title="Xóa ảnh">&times;</a><a href="javascript://" rel="'+result.url+'" name="'+result.filename+'"><img src="'+result.url+'" style="border-color:yellow;" /></a></li>');                        
                    $message.addClass('alert-success').html(result.message).slideDown(200);
                }
                setTimeout(function(){ $message.slideUp(function(){ $(this).empty(); }) }, 8000);
                return false;
            });
            $form.submit();                  
        });

        var data = loadImageList(listUrl);

        if (data != ''){
            $nextpage.attr('rel', 2);
            displayImageList(containerId, data).append($nextpage);                    
        }

        $listing
            .on('click', 'a.next-page', function(e){
                e.preventDefault();
                var pagenum = parseInt(this.rel);
                $(this).remove();
                var data = loadImageList(listUrl, pagenum);
                if (data != ''){
                    $nextpage.attr('rel', pagenum+1);
                    displayImageList(containerId, data).append($nextpage);
                }
            })            
            .on('click', 'a:not(.delete, .next-page)', function(e){
                e.preventDefault();
                if (selectLimit == 1){
                    $listing.find('img.active').removeClass('active');
                }
                $(this).find('img').addClass('active');
            })   
            .on('dblclick', 'a:not(.delete, .next-page)', function(e){
                e.preventDefault();
                if (selectLimit > 1){
                    insertImage(this.rel, this.name, inputName, true);
                } else {
                    insertImage(this.rel, this.name, inputName, false);
                }
                closeModal();
            })                   
            .on('click', 'a.delete', function(e){
                e.preventDefault();
                var $this = $(this),
                    fileUrl = this.rel,
                    filename = this.name;

                if (confirm("Bạn có chắc muốn xóa tập tin này?")) {
                    $.post(deleteUrl, {file: filename}, function(result){
                        if (result == 'success'){
                            $this.parent().remove();
                            var $img = $destination.find('img');
                            if ($img.length){
                                $img.each(function(){
                                    if (fileUrl == $(this).attr('src')){
                                        if (selectLimit > 1){
                                            $(this).parents('li').remove();
                                        } else {
                                            $(this).parent().empty();
                                        }
                                    }
                                })
                            }
                        }
                    });
                }
            });

        $('#button_insert').on('click', function(){
            var $img = $listing.find('img.active');
            if (selectLimit > 1){
                $img.each(function(){
                    var $parent = $(this).parent();
                    insertImage($parent.attr('rel'), $parent.attr('name'), inputName, true);
                });  
            } else {
                var $parent = $img.parent();
                insertImage($parent.attr('rel'), $parent.attr('name'), inputName, false);
            }
            closeModal();  
        });

        $('#button_cancel').on('click', function(){
            closeModal();   
        }); 

        function generateImage(url, filename, inputName, removeParent){
            return '<li><div class="'+previewClass+'"><span class="remove" data-remove-parent="'+removeParent+'">&times;</span><img src="'+url+'" /><input type="hidden" name="'+inputName+'" value="'+filename+'" /></div></li>';            
        }

        function insertImage(url, filename, inputName, multiple){
            if (multiple == true){
                var $hasFirstImg = $destination.find('li:first img').length;
                if ($hasFirstImg == 0){
                    $destination.html(generateImage(url, filename, inputName+'[]', 'no'));                                
                } else {
                    $destination.append(generateImage(url, filename, inputName+'[]', 'yes'));                 
                }
            } else {
                $destination.html(generateImage(url, filename, inputName, 'no'));            
            }
        }

    })();

    function displayImageList(container, data){
        $container = $(container);
        $container.append(data);                 
        $('img.lazy').lazyload({
            container: $container,
            threshold : 20
        });
        return $container;
    }

    function loadImageList(url, pagenum){

        if (typeof(pagenum) == 'number'){
            url += '&page='+pagenum;
        }

        var result = $.ajax({type: "GET", url: url, dataType:'json', async: false}).responseText;

        if(result == 'error'){
            window.location = location.protocol+'//'+location.hostname + globalvars.base_url +'account.login/?furl=' + encodeURIComponent(location.pathname);
            return false;                
        }

        result = $.parseJSON(result);
        var shtml = '';
        if (result.length){
            shtml = '<ul class="clearfix">';
            for(i = 0; i < result.length; i++){
                shtml += '<li><a class="delete" href="javascript://" rel="'+result[i].url+'" name="'+result[i].filename+'" title="Xóa ảnh">&times;</a><a href="javascript://" rel="'+result[i].url+'" name="'+result[i].filename+'"><img class="lazy" data-original="'+result[i].url+'" /></a></li>';    
            }
            shtml += '</ul>';
            return shtml;
        }
        return shtml;
    } 

    function closeModal(){
        return jQuery.modalbox('close');        
    }       
</script>
