{{ include "layout/header" }}	
	<form name="form_listing" id="form_listing" method="POST" action="">
	<table class="table table-hover" id="listing">
		<tr>
			<td colspan="8" class="head">
                <div class="box-horizontal">
				    <div class="left btn-group">{{u_action_list}}</div>
				</div>
			</td>
		</tr>
        {{ if has_zones}}         
		<tr>
          <td width="2%" class="subtitle text-center">ID</td>        
		  <td width="2%" class="subtitle text-center"><input type="checkbox" name="checkall" /></td> 
          <td width="40%" class="subtitle">Tên vị trí</td>                
          <td width="30%" class="subtitle">Tên ánh xạ</td>                
          <td width="10%" class="subtitle text-center">Kích thước</td>   
		  <td width="8%" class="subtitle text-center">&nbsp;</td>
		</tr>
        <tbody>
		{{ for item in zones}}
		<tr>      
            <td class="text-center">{{item.id}}</td>
            <td class="text-center"><input type="checkbox" name="ids[]" value="{{item.id}}" /></td>
            <td>
                {{item.name}} <span class="btn-task-group">{{item.u_edit}} {{item.u_delete}}</span>
            </td>	      
            <td>{{item.alias}}</td>                     
            <td class="text-center">{{item.width}} x {{item.height}}</td>      
            <td class="text-center">{{item.status}}</td>
		</tr>
		{{ endfor }}
        </tbody>         
		{{else}}
		<tr>
			<td class="text-center"><div class="text-muted">Không tồn tại vị trí</div></td>
		</tr>
		{{endif}}			
	</table>
	</form>
{{ include "layout/footer" }}
