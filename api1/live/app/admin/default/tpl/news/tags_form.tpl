<form name="form_edit" id="form_edit" method="POST" action="{{s_action}}">   
<table class="table">
    <tr>
        <td>
            <label>Nhập mỗi tag trên một dòng</label>
            <textarea class="input" name="title" rows="8" />{{data.title}}</textarea>
        </td>
    </tr>  
     <tr>
        <td colspan="4" class="foot">{{s_button}}</td>
    </tr> 			
</table>
</form>