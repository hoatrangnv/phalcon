{{ include "layout/header" }}
<form>
    <table class="table">      
        {{ if categorieslist|notempty }}     
        <tr>
            <td style="padding:0;border:0;">
                <div class="nestable" data-url="{{u_reorder}}">{{ categorieslist }}</div>
            </td>
        </tr>  
        {{else}}
        <tr>
             <td class="text-center text-muted">Chuyên mục không tồn tại</td>
        </tr>
        {{endif}}                   
    </table>
</form>
{{ include "layout/footer" }}    

