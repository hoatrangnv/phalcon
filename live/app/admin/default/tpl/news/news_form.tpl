{{ include "layout/header" }}

<form name="form_edit" id="form_edit" method="POST" action="{{s_action}}">
    <table class="table">  
        <tr>
            <td width="75%" style="padding:0;border-right:1px solid #eee;" class="box-top">
                <table class="table">        
                    <tbody> 
                        <tr>
                            <td>
                                <label>Tiêu đề</label>
                                <input class="input slugify" type="text" name="title" value="{{ data.title }}" />   
                                <label style="margin-top:10px;">Mô tả ngắn</label>
                                <textarea class="wysiwyg" name="intro" data-height="100" data-folder="articles" >{{data.intro}}</textarea>                                                      
                                <label style="margin-top:10px;">Nội dung</label>
                                <textarea class="wysiwyg" data-height="300" data-folder="articles" name="content" id="content">{{data.content}}</textarea>
                                <label style="margin-top:10px;">Tags</label>
                                <input class="input" type="text" name="tags_id" id="tags_id" value="{{data.taglist}}" />                                     
                            </td>
                       </tr>                                   
                    </tbody> 
                    <tbody>
                        <tr>
                            <td class="subtitle">SEO</td>
                        </tr>
                        <tr>
                            <td class="field-group">
                                <label>URL Friendly</label>
                                <input class="input" type="text" name="title_alias" id="title_alias" value="{{data.title_alias}}" />
                                <label style="margin-top:10px;">Meta Title</label>
                                <input class="input" type="text" name="meta_title" value="{{data.meta_title}}" />                                  
                                <label style="margin-top:10px;">Meta Description</label>
                                <textarea class="input" name="meta_description" style="height:65px;">{{data.meta_description}}</textarea>                                                              
                            </td>  
                        </tr>             
                    </tbody> 
                    <tbody>
                        <tr>
                            <td class="subtitle">Mở rộng</td>
                        </tr>
                        <tr>
                            <td class="field-group">
                                <label style="margin-top:10px;">Vị trí địa lý</label>
                                <input class="input" type="text" name="location" value="{{data.location}}" />

                                <label style="margin-top:10px;">Liên kết vị trí địa lý</label>
                                <input class="input" type="text" name="linkl" value="{{data.linkl}}" />
                                </br> 
                                Ex:</br> 
                                /mien-bac-41</br>
                                /mien-nam-40</br>
                                /mien-trung-39</br>
                                /mien-bac-38</br>                                  
                                <label style="margin-top:10px;">Năm</label>
                                <input class="input" type="text" name="year" value="{{data.year}}" /> 
                                <label style="margin-top:10px;">Liên kết năm</label>
                                <input class="input" type="text" name="linky" value="{{data.linky}}" />
                                <label style="margin-top:10px;">Tiến độ</label>
                                <input class="input" type="text" name="processes" value="{{data.processes}}" />
                                <label style="margin-top:10px;">Liên kết tiến độ</label>
                                <input class="input" type="text" name="linkp" value="{{data.linkp}}" />                                                            
                            </td>  
                        </tr>             
                    </tbody>           
                </table>               
            </td>
            <td width="25%" class="box-top" style="padding:0;">
                <table class="table">
                    <tr>
                        <td>
                            <label>Chuyên mục chính</label>
                            <select class="stylize" name="primary_cid" id="primary_cid" style="width:220px" data-default="{{ data.primary_cid }}">          
                                <option value="0">Chọn chuyên mục...</option>
                                {{ for item in categories }}
                                <option value="{{item.id}}">{{item.indent}}{{item.title}}</option>
                                {{ endfor }}          
                            </select>  
                        </td>                
                    </tr>                
                    <tr>
                        <td>
                            <label>Chuyên mục liên quan</label>
                            <ul id="select_multiple" class="select-multiple" style="width:240px">          
                                {{ for item in categories }}
                                <li>{{item.indent}}<label for="categories_id_{{item.id}}"><input type="checkbox" name="categories_id[]" id="categories_id_{{item.id}}" value="{{item.id}}" /><span {{item.css}}>{{item.title}}</span></label></li>
                                {{ endfor }}          
                            </ul>  
                        </td>                
                    </tr>
                    <tr>
                        <td class="subtitle">
                            <div class="box-horizontal">
                                <div class="left">Hình ảnh</div>
                                <div class="right">
                                    <a class="browse" style="line-height:0" href="javascript://"  data-folder="articles" data-target="#images" data-limit="100" data-input-name="image" data-type="all" data-preview-class="img120">Chọn ảnh...</a>
                                </div> 
                            </div>
                        </td>
                    </tr>                    
                    <tr>
                        <td>
                            <div id="images" class="images clearfix">
                                <ul class="clearfix">
                                    {{ for item in data.image }}
                                    <li><div class="img120">{{ item }}</div></li>
                                    {{ endfor }}    
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                         <td class="subtitle">Tùy chọn hiển thị</td>
                    </tr>        
                    <tr>
                        <td>
                            <input type="checkbox" name="show_comment" value="1" data-default="{{data.show_comment}}" /> Cho phép phản hồi
                            <br /><br />                        
                            <input type="checkbox" name="featured" value="1" data-default="{{data.featured}}" /> Tin nóng                            
                            <br /><br />
                            <input type="checkbox" name="status" value="0" data-default="{{data.status}}"> Ẩn tin này
                        </td>
                    </tr>                                         
                </table>
            </td>
        </tr>
    </table>                      
</form>

<script type="text/javascript">
    (function(){
        var $categories = $('#select_multiple input[type=checkbox]');
        var catlist = "{{ data.catlist }}".split(",");
        $categories.each(function(){
            if ($.in_array(this.value, catlist)){
                $(this).prop('checked', true).parents('li').addClass('active');
            }
        })    

        $('#tags_id').autocomplete({
            serviceUrl: '{{u_ajax_tags}}',
            minChars: 2,
            delimiter: ','                              
        });    
    })();
</script>

{{ include "layout/footer" }}	