{{ include "layout/header" }}
	<form name="form_listing" id="form_listing" method="POST" action="">
	<table class="table table-hover" id="listing">
		<tr>
			<td colspan="100%" class="head">
            <div class="box-horizontal">
                <div class="left btn-group">{{u_action_list}}</div>
                <div class="right form-horizontal">           
                    <input class="input input-sm" style="width:180px;" type="text" name="keyword" value="{{data_keyword}}" />
                    <select name="fcid" class="stylize stylize-sm" style="width:180px;">
                        <option value="0">- Chuyên mục -</option>
                        {{ for item in allcat }}
                        <option value="{{item.id}}"{{item.selected}}>{{item.indent}}{{item.title}}</option>
                        {{ endfor }}
                    </select>
                    <button class="btn btn-primary btn-sm" type="submit" name="do">Tìm</button>
                </div>
            </div>            
			</td>
		</tr>			
        {{ if news|length }}            
		<tr>
			<td width="1%" class="subtitle text-center"><input type="checkbox" name="checkall" /></td>
            <td width="74%" class="subtitle">Tiêu đề</td>               
            <td width="10%" class="subtitle">Ngày đăng</td>             
			<td width="5%" class="subtitle">Xem</td>
			<td width="5%" class="subtitle">Thứ tự <input type="submit" value="" name="save" style="background: url(/static/sites/flatlight/img/save_icon.gif);width: 16px;border: none;height: 14px;background-repeat: no-repeat;"></td>
			<td width="5%" class="subtitle">Vị trí <input type="submit" value="" name="save" style="background: url(/static/sites/flatlight/img/save_icon.gif);width: 16px;border: none;height: 14px;background-repeat: no-repeat;"></td>				
			<td width="10%" class="subtitle">&nbsp;</td>
		</tr>
		{{ for item in news }}
		<tr>
			<td class="text-center">
				<input type="checkbox" name="ids[]" value="{{item.id}}" />
				<input type="hidden" name="idz[]" value="{{item.id}}" />
			</td>
			<td>
                {{item.featured}} {{item.status}} {{item.title}}
                <div class="overview text-sm text-muted">
                    Chuyên mục: {{ item.catlist }}      
                </div>
            </td>					
            <td>{{item.created_at|date}}</td>
            <td class="text-center">{{item.hits}}</td>
	    <td><input type="text" name="sort[]" id="sort" style="width: 30px; text-align: center;" value="{{item.sortorder}}"></td>
	    <td><input type="text" name="position[]" id="position" style="width: 30px; text-align: center;" value="{{item.position}}"></td>
	        <td class="text-right">
                <div class="btn-task-group">{{item.u_view}} {{item.u_edit}} {{item.u_delete}}</div>       
                
            </td>
		</tr>
		{{ endfor }}					
		<tr>
			<td colspan="100%" class="foot">
                <div class="box-horizontal">
                    <div class="left text-muted text-sm">{{current_page}}</div>
                    <div class="right">{{pagination}}</div>
                </div>
			</td>
		</tr>
		{{else}}                                          																		
		<tr>
			<td class="text-center text-muted">Tin tức không tồn tại</td>
		</tr>
		{{endif}}
	</table>
	</form>
{{ include "layout/footer" }}