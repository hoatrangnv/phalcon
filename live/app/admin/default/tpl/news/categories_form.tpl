
		<form name="form_edit" id="form_edit" method="POST" action="{{s_action}}">                     

		<table class="table"> 	
            <tr>
                {{ if multiple == false }}                
                <td width="30%">{{t('title')}}:</td>
                <td width="70%">
                    <input class="input" type="text" name="title" id="title" value="{{data.title}}">    
                </td>
                {{ else }}
                <td width="30%" valign="top">{{t('title')}}:</td>
                <td width="70%">
                    <textarea class="input" name="title" rows="8" />{{data.title}}</textarea>
                    <div class="text-sm text-muted" style="margin-top:10px;">Mỗi tên chuyên mục trên một dòng</div>             
                </td>                
                {{ endif }}                
            </tr>         
		    {{ if has_categories == true }}       
            <tr>
                <td>{{t('parent')}}:</td>
                <td>
                    <select class="stylize" name="parent_id" id="parent_id" style="width:100%">
                        <option value="0">{{t('global:no')}}</option>
                        {{ for item in categories }}
                        <option value="{{item.id}}"{{item.selected}}>{{item.indent}}{{item.title}}</option>
                        {{ endfor }}
                    </select>    
                </td>
            </tr>       
            {{endif}}            

            <tr>
                <td>1=>Tin tức <br/>2=>Công trình<BR />3=>Tuyển dụng <br/></td>
                <td>
                    <input class="input" type="text" name="view" id="view" value="{{ data.view }}">                    
                </td>
            </tr>       
            <tr class="{{ if multiple == true }}hide{{ endif }}">
                <td class="text-top">Mô tả:</td>
                <td>
                    <textarea class="wysiwyg" name="description" id="description" rows="3">{{data.description}}</textarea>    
                </td>
            </tr>                  
	        <tr>
	            <td colspan="2" class="foot text-right">{{s_button}}</td>
	        </tr>               
    	</table>
    </form>