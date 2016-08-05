<form name="form_edit" id="form_edit" method="POST" action="{{s_action}}"> 
<table class="table">
    <tr>
        <td width="25%">Nhóm thuộc tính:</td>
        <td width="75%">
            <select class="stylize" name="attributes_group_id" data-allow-create="true">
                <option value="0">Chọn nhóm...</option> 
                {{ for item in attributes_group }}
                <option value="{{item.id}}">{{item.name}}</option>
                {{ endfor }}
                <option value="+">+ Tạo nhóm mới</option>                
            </select>
        </td>
    </tr> 
</table>    
<table class="table">
    <tr>
        <td class="subtitle" width="38%">{{txt_name}}</td>
        <td class="subtitle" width="38%">Giá trị mặc định</td>
        <td class="subtitle" width="14%" colspan="2"></td>
    </tr>        
    <tr>
        <td><input class="input" type="text" name="name[]" size="30" value="{{data_name}}" /></td>
        <td><input class="input" name="default[]" value="{{data_default}}" /></td>
        <td><a href="javascript://" class="add-row" title="Thêm"><i class="icon icon-plus"></i></a></td>
        <td><a href="javascript://" class="remove-row hide" title="Xóa"><i class="icon icon-minus"></i></a></td>
    </tr>  
     <tr>
        <td colspan="4" class="foot">{{s_button}}</td>
    </tr> 			
</table>
</form>