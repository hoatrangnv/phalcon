{{ include "layout/header" }}
    <form name="form_edit" id="form_edit" method="POST" action="{{s_action}}"> 
    <table class="table">  
        <tr>
            <td width="25%">Vai trò:</td>
            <td width="75%"><input class="input" type="text" name="name" value="{{data_name}}" /></td>
        </tr>
        <tr>
            <td class="text-top">Mô tả:</td>
            <td><textarea class="input" name="description" cols="76" rows="2">{{data_description}}</textarea></td>
        </tr>
     </table>
     
     <table class="table table-hover">               
		<tr>
            <td class="head">Phân quyền sử dụng</td>
            <td class="head text-center">Tất cả</td>
            <td class="head text-center">Xem</td>
            <td class="head text-center">Tạo</td>
            <td class="head text-center">Sửa</td>
			<td class="head text-center">Xóa</td>
	   </tr>
       <tr>
            <td class="subtitle" width="50%">&nbsp;</td>
            <td class="subtitle text-center" width="10%"><input type="checkbox" name="all" value="1" /></td>
            <td class="subtitle text-center" width="10%"><input type="checkbox" name="all_read" value="1" /></td>
            <td class="subtitle text-center" width="10%"><input type="checkbox" name="all_create" value="1" /></td>
            <td class="subtitle text-center" width="10%"><input type="checkbox" name="all_change" value="1" /></td>
            <td class="subtitle text-center" width="10%"><input type="checkbox" name="all_remove" value="1" /></td>
       </tr>
       {{ for item in modules }}
		<tr>
            <td class="text-bold">{{item.label}}</td>
            <td class="text-center"><input type="checkbox" name="modules[{{item.name}}]" value="1"{{item.checked_all}} /></td>
            <td class="text-center"><input type="checkbox" name="permission[{{item.name}}.read]" value="{{item.permission_read}}"{{item.checked_read}} /></td>
            <td class="text-center"><input type="checkbox" name="permission[{{item.name}}.create]" value="{{item.permission_create}}"{{item.checked_create}} /></td>
            <td class="text-center"><input type="checkbox" name="permission[{{item.name}}.change]" value="{{item.permission_change}}"{{item.checked_change}} /></td>
            <td class="text-center"><input type="checkbox" name="permission[{{item.name}}.remove]" value="{{item.permission_remove}}"{{item.checked_remove}} /></td>
		</tr>   
        {{ endfor }}
    </table>    
    </form>
    <script type="text/javascript">
    (function(){    
        $('input:checkbox[name^=all]').on('click', function(){
            var $parent = $(this).parents('tr'),
                status = this.checked;          
                  
            switch(this.name){
                case 'all':
                    $('input:checkbox[name^="all_"], input:checkbox[name^="modules"], input:checkbox[name^="permission"]').attr('checked', status).uniform('update');
                break;   
                case 'all_read':
                case 'all_create':
                case 'all_change':
                case 'all_remove':
                    var action = this.name.split('_')[1];
                    $('input:checkbox[name$=".'+action+']"]').attr('checked', status).uniform('update');
                    
                    if (status === false){
                        $('input:checkbox[name="all"], input:checkbox[name^="modules"]').attr('checked', false).uniform('update');
                    } else {
                        if($parent.find('input[name^="all_"]:checked').length == 4){
                            $('input:checkbox[name="all"], input:checkbox[name^="modules"]').attr('checked', true).uniform('update');
                        }                        
                    }
                break;                  
                default:
                    return false;  
            }
        });
          
        $('input:checkbox[name^="modules"]').on('click', function(){
            var status = this.checked;
            $(this).parents('tr').find('input[name^="permission"]').attr('checked', status).uniform('update'); 
        });
          
        $('input:checkbox[name^="permission"]').on('click', function(){
            var $parent = $(this).parents('tr'),
                status  = this.checked;
           
            if(this.name.match(/.create|.change|.remove/gi)){
                if (status === true){
                    $parent.find('input[name$=".read]"]').attr('checked', true).uniform('update');    
                }
            } else if(this.name.match(/.read/gi)) {
                if (status === false && ($parent.find('input[name^="permission"]:not([name$=".read]"]):checked').length)){
                    alert('Bạn không thể bỏ quyền (Xem) khi các quyền (Tạo), (Sửa), (Xóa) đang được chọn');
                    $(this).attr('checked', true).uniform('update');
                } 
            }

            $parent.find('input[name^="modules"]').attr('checked', ($parent.find('input[name^="permission"]:checked').length == 4)).uniform('update');                
        });
    })();  
    </script>
{{ include "layout/footer" }}    