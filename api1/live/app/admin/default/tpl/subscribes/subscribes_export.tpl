
    <form name="form_export" id="form_export" method="POST" action="{{s_action}}">
	<table class="table">
        <tr>
            <td colspan="2">
                <div class="alert alert-info">
                    Xuất danh sách email nhận tin ra tập tin CSV để nhập vào các chương trình gửi email. Có thể lọc các email theo thời gian đăng ký                    
                </div>
            </td>
       </tr>       
        <tr>
            <td width="45%">Lọc email có ngày đăng ký lớn hơn:</td>
            <td>
                <input class="input date" style="width:40%;" type="text" name="from_date" id="from_date" value="" />
            </td>                                                                                             
        </tr>     
        <tr>
            <td width="45%">Lọc email có ngày đăng ký nhỏ hơn:</td>
            <td>
                <input class="input date" style="width:40%;" type="text" name="to_date" id="to_date" value="" />
            </td>
        </tr>           
        <tr>
            <td>Tên tập tin:</td>               
            <td><input class="input" type="text" name="filename" id="filename" value="" /></td>
        </tr>          
		<tr>
			<td colspan="2" class="foot text-right">{{s_button}}</td>
	   </tr>
     </table>
	 </form>

