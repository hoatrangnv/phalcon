{{ include "layout/header" }}
    <form name="form_edit" id="form_edit" method="POST" action="{{s_action}}">    
    <table class="table" id="form_action">       
        <tr>
            <td class="head" colspan="2">
                <a class="btn btn-default btn-sm expand-all" href="javascript://">Mở rộng tất cả</a>
                <a class="btn btn-default btn-sm collapse-all" href="javascript://">Thu gọn tất cả</a>
            </td>
       </tr>    
	    {{ for item in fieldset }}
	    <tr>
	        <td class="subtitle" colspan="2">
                <div class="box-horizontal">
                    <div class="left"><a class="collapse" data-index="config_{{ item.id }}" href="javascript://" name="{{ item.name }}">{{ item.name }}</a></div>
                    <div class="right">
                        <a class="collapse" data-index="config_{{ item.id }}" href="javascript://"><i class="collapse-icon icon icon icon-expand-alt" data-index="config_{{ item.id }}"></i></a>
                    </div>
                </div>
	        </td>
	    </tr>
	    <tbody class="collapse-content" data-index="config_{{ item.id }}">  
	        {{ for subitem in item.fields}}
	        <tr>
	            <td width="35%" class="{{ subitem.valign }}">
	                {{ subitem.label }}
	            </td> 
	            <td>{{ subitem.field }}</td>
	        </tr>
	        {{ endfor }}                      
	    </tbody>    
	    {{ endfor }}           
    </table>
    </form>
{{ include "layout/footer" }}
