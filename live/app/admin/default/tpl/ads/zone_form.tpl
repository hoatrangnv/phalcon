
<form name="form_edit" id="form_edit" method="POST" action="{{s_action}}">
<table class="table">
    <tr>
        <td width="20%">{{txt_name}}:</td>
        <td width="80%">
            <input class="input" type="text" name="name" id="name" value="{{data_name}}" />
        </td>
    </tr>   
    <tr>
        <td>{{txt_alias}}:</td>
        <td>
            <input class="input" type="text" name="alias" id="alias" value="{{data_alias}}" style="width:93%;" />
            <a class="tooltip icon icon-question-sign" title="Tên ánh xạ được dùng thay thế cho ID, giúp nhận dạng các vị trí quảng cáo dễ hơn. Tên ánh xạ phải là duy nhất không được trùng lặp"></a>
        </td>
    </tr>           
    <tr>
        <td>Kích thước:</td>
        <td>
            <div class="box-horizontal">
                <div class="left">
                    {{txt_width}}&nbsp;&nbsp;<input class="input text-center" style="width:50px" type="text" name="width" id="width" value="{{data_width}}" /> px
                </div>
                <div class="left" style="margin-left:30px">
                    {{txt_height}}&nbsp;&nbsp;<input class="input text-center" style="width:50px" type="text" name="height" id="height" value="{{data_height}}" /> px
                </div>
            </div>
        </td>
    </tr>                               
	<tr>
	  <td>Trạng thái:</td>
	  <td>
		<input type="radio" name="active" id="active" value="1" data-default="{{data_active}}" /> Hiển thị &nbsp; 
		<input type="radio" name="active" id="active" value="0" data-default="{{data_active}}" /> Ẩn
	 </td>
	</tr>
	<tr>
		<td class="foot text-right" colspan="2">{{s_button}}</td>
	</tr>
</table>
</form>

