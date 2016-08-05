<form name="form_edit" id="form_edit" method="POST" action="{{s_action}}"> 
{{ if action == 'add' }}
    <table class="table">
        <tr>
            <td colspan="100%">
                {{ if gid > 0 }}
                    Nhóm liên kết: <b>{{links_group.name}}</b>
                    <input type="hidden" name="group_id" value="{{ links_group.id }}" />
                {{ else }}
                    <select class="stylize" name="group_id" data-allow-create="true">
                        <option value="0">Chọn nhóm liên kết...</option> 
                        {{ for item in links_group }}
                        <option value="{{item.id}}">{{item.name}}</option>
                        {{ endfor }}
                        <option value="+">+ Tạo nhóm mới</option>                
                    </select>
                {{ endif }}
            </td>
        </tr> 
        <tr>
            <td colspan="100%" class="subtitle">Liên kết</td>
        </tr>        
        <tr>
            <td width="35%"><input class="input" type="text" name="name[]" value="{{data.name}}" placeholder="Tên" /></td>
            <td><input class="input" name="url[]" value="{{data.url}}" placeholder="URL" /></td>
            <td width="5%"><a href="javascript://" class="add-row" title="Thêm"><i class="icon icon-plus"></i></a></td>
            <td width="5%"><a href="javascript://" class="remove-row hide" title="Xóa"><i class="icon icon-minus"></i></a></td>
        </tr>  
         <tr>
            <td colspan="100%" class="foot">{{s_button}}</td>
        </tr>           
    </table>
{{ else }}
    <table class="table">   
        <tr>
            <td width="10%">Tên:</td>
            <td width="90%"><input class="input" name="name" value="{{frmdata.name}}" /></td>
        </tr>
        <tr>
            <td>Mô tả</td>
            <td><input class="input" type="text" name="title" value="{{frmdata.title}}" /></td>
        </tr>
        <tr>
            <td>URL</td>
            <td><input class="input" type="text" name="url" value="{{frmdata.url}}" /></td>
        </tr> 
        <tr>
            <td>&nbsp;</td>
            <td>
                <input type="checkbox" name="target" id="link_target" value="_blank" data-default="{{frmdata.target}}" /> <label class="inline" for="link_target">Open in new window</label>
                &nbsp;&nbsp;
                <input type="checkbox" name="rel" id="link_rel" value="nofollow" data-default="{{frmdata.rel}}"> <label class="inline" for="link_rel">Nofollow</label>
            </td>
        </tr>                                                    
         <tr>
            <td colspan="2" class="foot">{{s_button}}</td>
        </tr>           
    </table>
{{ endif }}
</form>