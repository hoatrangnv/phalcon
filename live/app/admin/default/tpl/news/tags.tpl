{{ include "layout/header" }}
    
<form name="form_listing" id="form_listing" method="POST" action=""> 
    <table class="table table-hover" id="listing">                 
    {{ if tags|length }}   
        <tr>
            <td width="40%" class="subtitle">Tên</td>               
            <td width="40%" class="subtitle">Tên ánh xạ</td>               
            <td width="7%" class="subtitle text-center">Clicks</td>                
            <td width="7%" class="subtitle text-center">Tin</td>                
            <td width="6%" class="subtitle">&nbsp;</td>
        </tr>                  
        {{ for item in tags }}       
        <tr>
            <td><a href="javascript://" class="editable" data-url="{{u_editable}}" data-type="input" data-field="title" data-params="id:{{item.id}}" data-saveby="button">{{item.title}}</a></td>
            <td>{{item.title_alias}}</span></td>
            <td class="text-center">{{item.hits|money}}</td>
            <td class="text-center">{{item.counter|money}}</td>
            <td><span class="btn-task-group visible">{{item.u_delete}}</span></td>
        </tr>     
        {{ endfor }}          
    {{else}}
	    <tr>
	        <td class="text-center text-muted">Tags không tồn tại</td>
	    </tr>            	        
    {{endif}}
    </table>
</form>

{{ include "layout/footer" }}    