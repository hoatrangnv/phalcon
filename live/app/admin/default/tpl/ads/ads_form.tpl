{{ include "layout/header" }}
    <form name="form_edit" id="form_edit" method="POST" action="{{s_action}}">
    <table class="table">
	    <tr>
		    <td width="20%">Vị trí:</td>
		    <td width="80%">
                <select class="stylize" name="ads_zone_id" id="ads_zone_id" data-default="{{data_ads_zone_id}}">
                    {{ for item in zones}}
                    <option value="{{item.id}}">{{item.name}}</option>
                    {{ endfor }}
                </select>            
		    </td>
	    </tr>	                            
	    <tr>
		    <td>Tiêu đề:</td>
		    <td>
                <input class="input" type="text" name="name" id="name" size="40" value="{{data_name}}" maxlength="600">
            </td>
	    </tr>			
	    <tr>
		    <td>Hình ảnh:</td>
		    <td>
                <div id="images" class="images">
                    <ul class="clearfix"><li><div class="img200">{{data_image}}</div></li></ul>
                </div>
                <a class="browse" href="javascript://" data-folder="misc" data-target="#images" data-type="all" data-preview-class="img200">Chọn ảnh...</a>
            </td>
	    </tr>
	    <tr>
		    <td>Liên kết:</td>
		    <td>
			    <input class="input" type="text" name="url" id="url" size="55" value="{{data_url}}">
		    </td>
	    </tr>
        <tr>
            <td>Thời hạn hiển thị:</td>
            <td>
                <div class="box-horizontal">
                    <div class="left">     
                        <select class="stylize" name="expiry" id="expiry" data-default="{{data_expiry}}">
                            <option value="0">Mãi mãi</option>
                            <option value="1">Tùy chọn</option>
                        </select>               
                    </div>
                    <div class="form-horizontal" id="timerow">
                        <label>Từ</label><input class="input date" style="width:120px;" type="text" name="start_time" id="start_time" value="{{data_start_time}}" />
                        <label>Đến</label><input class="input date" style="width:120px;" type="text" name="expiry_time" id="expiry_time" value="{{data_expiry_time}}" />
                    </div>
                </div>
            </td>
        </tr>      
        <tr>
            <td>Trang hiển thị:</td>
            <td>
                <select class="stylize" name="page" id="page" data-default="{{data_page}}">
                    {{ for item in pages}}
                    <option value="{{item.id}}">{{item.name}}</option>
                    {{ endfor }}
                </select>
            </td>
        </tr>     
        <tr id="optionrow">
            <td class="text-top">Chuyên mục:</td>
            <td>
                <ul id="selectcat" class="select-multiple">     
                    <li class="root btn-group">
                        <a href="javascript://" class="btn btn-default btn-xs" id="selectall">Chọn tất cả</a>
                        <a href="javascript://" class="btn btn-default btn-xs" id="unselectall">Tùy chọn</a>
                    </li>   
                    <div class="clearfix"></div>                       
                    {{ for item in categories}}
                    <li>{{item.indent}}<label for="categories_id_{{item.id}}"><input type="checkbox" name="categories_id[]" id="categories_id_{{item.id}}" value="{{item.id}}" /><span {{item.css}}>{{item.title}}</span></label></li>
                    {{ endfor }}          
                </ul>
            </td>
        </tr>        		                   				
	    <tr>
	      <td>Trạng thái</td>
	      <td>
		    <input type="radio" name="active" id="active_1" value="1" data-default="{{data_active}}" /> Hiển thị &nbsp; 
		    <input type="radio" name="active" id="active_0" value="0" data-default="{{data_active}}" /> Ẩn
	     </td>
	    </tr>
    </table>
    </form>

    <script type="text/javascript">
    (function(){
        var $page = $('#page'),
            $expiry = $('#expiry'),
            $timerow = $('#timerow'),
            $optionrow = $('#optionrow'),
            $selectcat = $('#selectcat');
                              
        change_expiry();
        change_page();
                
        $expiry.change(function(){
            change_expiry();
        });   
            
        $page.change(function(){
            change_page();
        });   

        function change_expiry(){
            ($expiry.val() == 1) ? $timerow.show() : $timerow.hide();
        }
            
        function change_page(){
            ($page.val() == 1) ? $optionrow.show() : $optionrow.hide();
        }
                      
        $selectcat.on('click', '#selectall, #unselectall', function(e){
            e.preventDefault();
            var status = (this.id == 'selectall') ? true : false;
            $selectcat.find('input[name^="categories_id"]').attr('checked', status).uniform('update');
        })
                                
        var cat_list = new Array(0,'{{data_categories_id}}');
        if (cat_list.length){
            $selectcat.find('input[name^="categories_id"]').each(function(i){
                if ($.in_array(this.value, cat_list)){
                    $(this).attr('checked', 'checked').uniform.update();
                }
            });                             
        }     
    })();
    </script>

{{ include "layout/footer" }}