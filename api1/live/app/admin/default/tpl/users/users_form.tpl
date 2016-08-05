		<form name="form_edit" id="form_edit" method="POST" action="{{s_action}}">
		<table class="table has-tab">
           <tr>
                <td class="head tab tab-light" data-toggle="tab" colspan="2">
                    <ul class="box-horizontal">   
                        <li><a data-index="content1" href="javascript://">Tài khoản đăng nhập</a></li>                    
                        <li><a data-index="content2" href="javascript://">Thông tin cá nhân</a></li>
                    </ul>                                                       
                </td>
           </tr>               
           <tbody class="tab-content" data-index="content1">
            <tr>
                <td width="25%">{{txt_fullname}}:</td>
                <td>
                    <input class="input" type="text" name="fullname" value="{{data_fullname}}">                            
                </td>
           </tr>         
            <tr>
                <td>{{txt_email}}:</td>
                <td>
                    <input class="input" type="text" name="email" value="{{data_email}}">                            
                </td>
           </tr>               
            <tr>
                <td>{{txt_password}}:</td>
                <td>
                    <input class="input" type="password" name="password">                          
                </td>
           </tr>     
            <tr>
                <td>{{txt_group}}:</td>
                <td>
                    <select class="stylize" name="users_group_id" id="users_group_id" data-default="{{data_users_group_id}}">
                        {{ for item in usersgroup }}
                        <option value="{{item.id}}">{{item.name}}</option>
                        {{ endfor }}
                    </select>
                </td>
           </tr>  
            <tr>
                <td class="text-top">{{txt_role}}:</td>
                <td>
                    <ul id="selectrole" class="select-multiple">   
                        <li class="root btn-group">
                            <a href="javascript://" class="btn btn-default btn-xs" id="selectall">Chọn tất cả</a>
                            <a href="javascript://" class="btn btn-default btn-xs" id="unselectall">Tùy chọn</a>
                        </li>                    
                        <div class="clearfix"></div>                 
                        {{ for item in usersrole }}
                        <li><label for="users_role_id_{{item.id}}"><input type="checkbox" name="users_role_id[]" id="users_role_id_{{item.id}}" value="{{item.id}}" /><span>{{item.name}}</span> <a class="tooltip icon icon-question-sign" data-html="{{item.description}}"></a></label></li>
                        {{ endfor }}                            
                    </ul>
                </td>
            </tr>          
            <tr>
                <td>{{txt_active}}:</td>
                <td>
                    <input type="radio" name="active" id="active1" value="1" data-default="{{data_active}}" /> {{txt_enabled}}
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                    <input type="radio" name="active" id="active0" value="0" data-default="{{data_active}}" /> {{txt_disabled}}
                </td>
           </tr>                
           </tbody>
           <tbody class="tab-content" data-index="content2">    
            <tr>
                <td width="16%">{{txt_birthday}}:</td>
                <td>
                    <input class="input" type="text" name="birthday" id="birthday" value="{{data_birthday}}" data-format="DD/MM/YYYY" data-template="DD  MM  YYYY" />
                </td>
            </tr>  
            <tr>
                <td>{{txt_gender}}:</td>
                <td>
                    <select class="stylize" style="width:140px;" name="gender" id="gender" data-default="{{data_gender}}">
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                    </select>
                </td>
            </tr>                                    
            <tr>
                <td class="text-top">{{txt_address}}:</td>
                <td>
                    <div class="form-horizontal">
                        <input class="input" style="width:20%;" type="text" name="address_num" value="{{data_address_num}}" placeholder="{{txt_address_num}}" />
                        <input class="input" style="width:79%;" type="text" name="address_street" value="{{data_address_street}}" placeholder="{{txt_address_street}}, {{txt_ward}}, {{txt_district}}" /> 
                    </div>
                    <div class="box-horizontal" style="margin-top:5px;">
                        <select class="stylize" style="width:201px;" name="country" id="country">
                            <option value="0">- {{txt_country}} -</option>                            
                        </select>                        
                        <select class="stylize" style="width:200px;" name="province" id="province">
                            <option value="0">- {{txt_province}} -</option>                            
                        </select>                                                                
                    </div>
                </td>
           </tr>                                
            <tr>
                <td>{{txt_phone}}:</td>
                <td>
                    <input class="input" type="text" name="phone" value="{{data_phone}}">                            
                </td>
           </tr>   
            <tr>
                <td class="text-top">{{txt_note}}:</td>
                <td>
                    <textarea class="input" name="note">{{data_note}}</textarea>                            
                </td>
           </tr>              
           </tbody>
           <tr>           
                <td colspan="2" class="foot clearfix">
                    <div class="left text-danger" style="margin-top:5px;"><input type="checkbox" name="is_admin" id="is_admin" value="1" data-default="{{data_is_admin}}" /> <label class="inline" for="is_admin">{{ txt_is_admin }}</label></div>
                    <div class="right">{{s_button}}</div>
                </td>
           </tr>
		</table>
	</form>	    
	<script type="text/javascript">
        (function(){    
            var $selectrole = $('#selectrole'),
                $country    = $('#country'),
                $province   = $('#province');
            
            $('#birthday').combodate({
                firstItem:'name'
            });              
            
            $selectrole.on('click', '#selectall, #unselectall', function(e){
                e.preventDefault();
                var status = (this.id == 'selectall') ? true : false;
                $selectrole.find('input[name^="users_role_id"]').prop('checked', status).uniform('update');
            })            
            
            var role_list = new Array(0, {{data_users_role_id}});
            if (role_list.length){
                $selectrole.find('input[name^="users_role_id"]').each(function(i){
                    if ($.in_array(this.value, role_list)){
                         $(this).prop('checked', true).uniform('update');
                    }
                });                             
            }    

            load_locations($country, 0, 'c', '{{data_country}}', '{{txt_country}}');
            load_locations($province, '{{data_country}}', 'p', '{{data_province}}', '{{txt_province}}');
            
            $country.on('change', function(){
                load_locations($province, this.value, 'p', 0, '{{txt_province}}');
            });
            
            function load_locations(select, pid, type, value, text) {
                $.getJSON('{{u_ajax_location}}', {
                    pid:pid, 
                    type:type
                }, function(result){
                    var options = '<option value="0">- '+ text +' -</option>';
                    for(var i = 0; i < result.length; i++) {
                        options += '<option value="'+result[i].id+'">'+result[i].title+'</option>';
                    }
                    select.html(options).selectbox('refresh').selectbox('value', value);   
                });
            }
                       
        })();      

	</script>