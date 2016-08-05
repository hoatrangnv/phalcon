{{ include "layout/header" }}
    <form name="form_edit" id="form_edit" method="POST" action="{{s_action}}">
		<table class="table" id="form_action">			
			<tr>
				<td width="15%">Tiêu đề:</td>
				<td width="85%">
					<input class="input slugify" type="text" name="title" value="{{data.title}}">
				</td>
		   </tr>
            <tr>
                <td>Tên ánh xạ:</td>
                <td>
                    <input class="input" type="text" name="title_alias" value="{{data.title_alias}}" /> 
                </td>
           </tr>                
            <tr>
                <td valign="top">Nội dung:</td>
				<td>           
                    <textarea class="wysiwyg" name="content" id="content" rows="20" cols="80">{{data.content}}</textarea>
                </td>
		   </tr>
            <tr>
                <td>Trạng thái:</td>
                <td>
                <input type="radio" name="status" value="1" data-default="{{data.status}}" />&nbsp;Hiển thị&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="status" value="0" data-default="{{data.status}}" />&nbsp;Ẩn
                </td>
            </tr>               
		</table>
	 </form>

{{ include "layout/footer" }}