{{ include "layout/header" }}    
    <form name="form_listing" id="form_listing" method="POST" action="">
        <table class="table table-hover" id="listing" data-rowsclone="0">    
            <tbody class="sortable" data-url="{{u_reorder}}">
            {{ for group in usersgroup }}
            <tr id="orders_{{group.id}}">
                <td width="1%" class="order"><i class="icon icon-reorder sortable-handle"></i></td>   
                <td>
                    {{group.name}} <span class="btn-task-group">{{group.u_edit}} {{group.u_delete}}</span>
                    <div class="text-muted">{{group.description}}</div>
                </td>   
            </tr>
            {{ endfor }}
            </tbody>             
        </table>
    </form>
{{ include "layout/footer" }}