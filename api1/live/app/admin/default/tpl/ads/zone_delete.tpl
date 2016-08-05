{{ include "layout/header" }}  
 <form name="form_delete" id="form_delete" method="POST" action="{{s_action}}">   
 <table class="table">  
   <tr>
        <td class="text-center text-bold text-danger">
            Lưu ý: Khi bạn xóa vị trí này thì các banner hoặc logo thuộc vị trí này cũng sẽ bị xóa theo. 
            <br />Và không thể khôi phục lại được. Hãy chắc chắn là bạn muốn xóa
        </td>
   </tr> 
</table>
</form>
{{ include "layout/footer" }}