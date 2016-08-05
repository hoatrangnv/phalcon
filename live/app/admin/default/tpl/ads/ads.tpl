{{ include "layout/header" }}
	<form name="form_listing" id="form_listing" method="POST" action="">
	<table class="table table-hover" id="listing">
		<tr>
			<td colspan="6" class="head">
                <div class="box-horizontal">
				    <div class="left btn-group">{{u_action_list}}</div>
				</div>
			</td>
		</tr>
        {{ if ads|length }}         
		<tr>
          <td width="1%" class="subtitle order"><i class="icon icon-reorder"></i></td>        
		  <td width="2%" class="subtitle text-center"><input type="checkbox" name="checkall" /></td> 
		  <td width="7%" class="subtitle text-center">Hình</td>
          <td width="50%" class="subtitle">Tiêu đề</td>                
          <td width="20%" class="subtitle text-center">Vị trí</td> 
	  <td width="5%" class="subtitle">Sắp xếp <input type="submit" value="" name="save" style="background: url(/static/sites/flatlight/img/save_icon.gif);width: 16px;border: none;height: 14px;background-repeat: no-repeat;"></td> 	  
	  <td width="3%" class="subtitle">&nbsp;</td>
	</tr>
        <tbody class="sortable" data-url="{{u_reorder}}">
		{{ for item in ads}}
		<tr id="orders_{{item.id}}">
            <td class="order"><i class="icon icon-reorder sortable-handle"></i></td>                                  
            <td class="text-center"><input type="checkbox" name="ids[]" value="{{item.id}}" /></td>
            <td>{{item.image}}</td>
            <td>
                {{item.name}} <span class="btn-task-group">{{item.u_edit}}{{item.u_delete}}</span>
            </td>	      
            <td class="text-center">{{item.zone_name}}</td>  
            <td><input type="text" name="sort[]" id="sort" style="width: 30px; text-align: center;" value="{{item.sortorder}}">
		<input type="hidden" name="idz[]" value="{{item.id}}" />
	    </td>      
            <td class="text-center">{{item.status}}</td>
		</tr>
		{{ endfor }}
        </tbody>
        <tr>
            <td colspan="6" class="foot">
                <div class="box-horizontal">
                    <div class="left text-muted text-sm">{{current_page}}</div>
                    <div class="right">{{pagination}}</div>
                </div>
            </td>
        </tr>          
		{{else}}
		<tr>
			<td class="text-center"><div class="text-muted">Dữ liệu không tồn tại</div></td>
		</tr>
		{{endif}}			
	</table>
	</form>
{{ include "layout/footer" }}
