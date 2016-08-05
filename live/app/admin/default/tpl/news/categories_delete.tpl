 {{ include "layout/header" }}
 
     <form name="form_delete" id="form_delete" method="POST" action="{{s_action}}">   
     <input type="hidden" name="subcat_ids" id="subcat_ids" value="{{subcat_ids}}" />
     <table class="table" id="form_action">  
       <tr>
            <td width="30%">Tên chuyên mục:</td>
            <td colspan="3" width="70%"><b>{{category_title}}</b></td>
       </tr> 
       <tr style="display: {{news_display}}">
            <td>Số <span class="text-lower">{{txt_module}}</span> trên chuyên mục:</td>
            <td colspan="3"><b>{{news_counter}}</b></td>
       </tr>
       
       <tr style="display: {{subcat_display}}">
            <td>Số chuyên mục con:</td>
            <td colspan="3"><b>{{subcat_counter}}</b></td>
       </tr> 
       <tr style="display: {{subnews_display}}">
            <td>Số <span class="text-lower">{{txt_module}}</span> trên chuyên mục con:</td>
            <td colspan="3"><b>{{subnews_counter}}</b></td>
       </tr>
       
      <tbody style="display: {{news_display}}">                                       
       <tr>
            <td colspan="4" class="head">Tùy chọn xóa <span class="text-lower">{{txt_module}}</span></td>
       </tr>                                        
       <tr>
            <td style="width:25%">
               <input type="radio" name="del_action" value="del" checked="checked"> Xóa <span class="text-lower">{{txt_module}}</span>
            </td>
            <td class="text-center" style="width:4%">
               <span class="text-sm text-danger">hoặc</span>
            </td>
            <td style="width:36%">
               <input type="radio" name="del_action" value="move"> Chuyển <span class="text-lower">{{txt_module}}</span> đến
            </td>
            <td style="width:29%">
                <select class="stylize nofloat" name="dest_id" style="width:100%;">
                    {{for item in allcat}}
                    <option value="{{item.id}}"{{item.selected}}>{{item.indent}}{{item.title}}</option>
                    {{ endfor }}
                </select> 
            </td>
       </tr>    
       </tbody>  
       
      <tbody style="display: {{subcat_display}}">  
       <tr>
            <td colspan="4" class="head">Tùy chọn xóa chuyên mục con</td>
       </tr>                                             
       <tr>
            <td>
               <input type="radio" name="subcat_del_action" value="del" checked="checked"> Xóa chuyên mục con
            </td>
            <td class="text-center">  
               <span class="text-sm text-danger">hoặc</span>
            </td>
            <td>        
        	    <input type="radio" name="subcat_del_action" value="move"> Chuyển chuyên mục con đến
    	    </td>
            <td>
			    <select class="stylize nofloat" name="subcat_dest_id" style="width:100%;">
				    {{ for item in parentcat }}
				    <option value="{{ item.id }}">{{ item.indent }}{{ item.title }}</option>
				    {{ endfor }}
			    </select> 
            </td>
       </tr>
       <tr style="display: {{subnews_display}}">
            <td>
               <input type="radio" name="sub_del_action" value="del" checked="checked"> Xóa <span class="text-lower">{{txt_module}}</span> của chuyên mục con
            </td>
            <td class="text-center">  
               <span class="text-sm text-danger">hoặc</span>
            </td>
            <td>  
               <input type="radio" name="sub_del_action" value="move"> Chuyển <span class="text-lower">{{txt_module}}</span> của chuyên mục con đến
            </td>
            <td>  
               <select class="stylize nofloat" name="sub_dest_id" style="width:100%;">
                    {{ for item in allcat }}
                    <option value="{{ item.id }}"{{ item.selected }}>{{ item.indent }}{{ item.title }}</option>
                    {{ endfor }}
                </select> 
            </td>
       </tr>    
       </tbody>
    </table>
    </form>
    
{{ include "layout/footer" }}