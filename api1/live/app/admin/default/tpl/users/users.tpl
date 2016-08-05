{{ include "layout/header" }}       
    <form name="form_listing" id="form_listing" method="POST" action="">
	<table class="table table-hover" id="listing">
		<tr>
			<td colspan="5" class="head">
                <div class="box-horizontal">
					<div class="left btn-group">{{u_action_list}}</div>
				</div>
			</td>
		</tr>				
		<tr>
			<td width="1%" class="subtitle text-center"><input type="checkbox" name="checkall" /></td>
            <td width="50%" class="subtitle">{{txt_fullname}}</td>
			<td width="14%" class="subtitle">{{txt_group}}</td>
            <td width="15%" class="subtitle text-right"> </td>
		</tr>
		{{ for item in users }}
		<tr>
			<td class="text-center">{{ item.checkbox }}</td>
            <td>
                {{ item.fullname }} <span class="btn-task-group">{{item.u_edit}} {{item.u_delete}}</span>
                <div>{{ item.email }}</div>
            </td>
			<td>{{item.group}}</td>
            <td class="text-right"> {{ item.is_admin }} {{ item.status }}</td>
		</tr>
		{{ endfor }}
        <tr>
            <td colspan="5" class="foot">
                <div class="box-horizontal">
                    <div class="left text-muted text-sm">{{current_page}}</div>
                    <div class="right">{{pagination}}</div>                        
                </div>
            </td>
        </tr>            					
	</table>
	</form>
{{ include "layout/footer" }}        