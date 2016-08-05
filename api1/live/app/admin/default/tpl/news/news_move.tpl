
    <form name="form_move" id="form_move" method="POST" action="{{s_action}}">
	<input type="hidden" name="str_ids" value="{{data_str_ids}}">
	<table class="table">
		<tr>
			<td width="40%">Tổng số tin:</td>
			<td><b>{{data_counter}}</b></td>
	   </tr>
		<tr>
			<td colspan="2">
                <label>Chuyên mục cần chuyển đến</label>
                <ul id="select_multiple" class="select-multiple">          
                    {{ for item in categories }}
                    <li>{{item.indent}}<label for="categories_id_{{item.id}}"><input type="checkbox" name="categories_id[]" id="categories_id_{{item.id}}" value="{{item.id}}" /><span {{item.css}}>{{item.title}}</span></label></li>
                    {{ endfor }}          
                </ul>   
		   </td>
	   </tr>
		<tr>
            <td colspan="2" class="foot text-right">{{s_button}}</td>
	   </tr>
     </table>
	 </form>



