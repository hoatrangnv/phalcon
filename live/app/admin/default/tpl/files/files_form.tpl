
<form name="form_edit" id="form_edit" method="POST" action="{{s_action}}" enctype="multipart/form-data">
<table class="table">
   <tr>
        <td class="head tab tab-light" data-toggle="tab">
            <ul class="box-horizontal">   
                <li><a data-index="content1" href="javascript://">Tải lên từ máy tính</a></li>                    
                <li><a data-index="content2" href="javascript://">Sao chép từ liên kết(URL)</a></li>
            </ul>                                                       
        </td>
   </tr>               
   <tbody class="tab-content" data-index="content1">
        <tr>
             <td><input type="file" name="filename[]" multiple="multiple" /></td>
        </tr>                        
    </tbody>
   <tbody class="tab-content" data-index="content2">                     
        <tr>
             <td><input type="text" class="input" name="url" id="url" placeholder="http://"/></td>
        </tr>
    </tbody>    
	<tr>
		<td class="foot text-right">{{s_button}}</td>
   </tr>
</table>
</form>	    