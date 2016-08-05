 
        <form name="form_edit" id="form_edit" method="POST" action="{{s_action}}">
        <table class="table">
            <tr>
                <td width="30%">Họ và tên:</td>
                <td width="70%">
                    <input class="input" type="text" name="fullname" id="fullname" value="{{data_fullname}}"/>
                </td>  
           </tr>
            <tr>
                <td>Email:</td>
                <td>
                    <input class="input" type="text" name="email" id="email" value="{{data_email}}" />
                </td>  
           </tr>              
            <tr>
                <td colspan="2" class="subtitle">Đổi mật khẩu</td>
           </tr>                      
            <tr>
                <td>Mật khẩu cũ:</td>
                <td>
                    <input class="input" type="password" name="oldpass" />
                </td>  
           </tr>
            <tr>
                <td>Mật khẩu mới:</td>
                <td>
                    <input class="input" type="password" name="newpass1" />
                </td>  
           </tr>
            <tr>
                <td>Xác nhận mật khẩu:</td>
                <td>
                    <input class="input" type="password" name="newpass2" />
                </td>  
           </tr>
            <tr>
                <td colspan="2" class="foot text-right">{{s_button}}</td>
           </tr>
        </table>    
        </form>        


