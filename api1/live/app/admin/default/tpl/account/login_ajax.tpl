<form name="form_edit" id="form_edit" method="POST" action="{{s_action}}">
<input type="hidden" name="furl" value="{{furl}}">
<table class="table table-lg">   
	<tr>
		<td>
            <div class="alert alert-warning space-bottom">Bạn đã thoát khỏi hệ thống! Vui lòng đăng nhập lại</div>
            <input class="input input-lg space-bottom" type="text" name="email" id="email" placeholder="{{ t('global:email') }}" />
            <input class="input input-lg" type="password" name="password" id="password" placeholder="{{ t('global:password') }}" />        
        </td>  
   </tr>
	<tr>
		<td class="foot">
            <div class="box-horizontal">
                <div class="left" style="margin-top:16px;"><input type="checkbox" name="remember" value="1" /> <span class="text-muted">{{ t('global:remember') }}</span></div>
                <div class="right"><button class="btn btn-primary btn-lg" type="submit" name="save">{{ t('global:button_login') }}</button></div>
            </div>
		</td>
   </tr>
</table>
</form> 