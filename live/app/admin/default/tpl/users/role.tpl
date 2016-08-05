{{ include "layout/header" }}
    <form name="form_listing" id="form_listing" method="POST" action="">
        <table class="table table-hover" id="listing" data-rowsclone="0">     
            <tbody class="sortable" data-url="{{u_reorder}}">
            {{ for item in usersrole }}
            <tr id="orders_{{item.id}}">
                <td width="1%" class="order"><i class="icon icon-reorder sortable-handle"></i></td>   
                <td>
                    {{ item.name }} <span class="btn-task-group">{{ item.u_edit }} {{item.u_delete}}</span>
                    <div class="text-muted">{{item.description}}</div>
                </td>   
            </tr>
            {{ endfor }}
            </tbody>             
        </table>
    </form>
{{ include "layout/footer" }}