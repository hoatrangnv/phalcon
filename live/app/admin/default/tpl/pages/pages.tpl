{{ include "layout/header" }}

    <form name="form_listing" id="form_listing" method="POST" action="">
	    <table class="table table-hover" id="listing">           
		    <tr>
			    <td colspan="100%" class="head">
                    <div class="box-horizontal">
                        <div class="left btn-group">{{u_action_list}}</div>
				    </div>
			    </td>
		    </tr>			

            {{ if pages|length }}        
		    <tr>
                <td width="1%" class="subtitle order"><i class="icon icon-reorder"></i></td>
                <td width="2%" class="subtitle text-center"><input type="checkbox" name="checkall" /></td>            
                <td width="40%" class="subtitle">Tên trang</td>
                <td width="40%" class="subtitle">Tên ánh xạ</td>
                <td width="8%" class="subtitle text-center">Clicks</td> 
                <td width="8%" class="subtitle"></td> 
		    </tr>
            <tbody class="sortable" data-url="{{u_reorder}}">
		    {{ for item in pages }}
		    <tr id="orders_{{item.id}}">
			    <td class="order"><i class="icon icon-reorder sortable-handle"></i></td>
                <td class="text-center"><input type="checkbox" name="ids[]" value="{{item.id}}" /></td>
                <td>{{item.title}} <span class="btn-task-group">{{item.u_edit}} {{item.u_delete}}</span></td>
                <td>{{item.title_alias}}</td>
                <td class="text-center">{{item.hits|money}}</td>
                <td class="text-center">{{item.status}}</td>
		    </tr>
		    {{ endfor }}	
            </tbody>
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
			    <td class="text-center"><div class="text-muted">Dữ liệu không tồn tại!</div></td>
		    </tr>
		    {{endif}}
	    </table>
    </form>

{{ include "layout/footer" }}