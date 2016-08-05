{{ include "layout/header" }}

    <form name="form_listing" id="form_listing" method="POST" action="">
        <table class="table table-hover" id="listing" data-rowsclone="2">
            <tr>
                <td colspan="3" class="head hide">
                    <div class="box-horizontal">
                        <div class="left btn-group">{{u_action_list}}</div>
                    </div>
                </td>
            </tr>        
            <tr>
                <td class="subtitle" width="3%">STT</td>   
                <td class="subtitle" width="50%">Email</td>   
                <td class="subtitle" width="47%">Ngày đăng ký</td>   
            </tr>        
            {{ for subscribe in subscribes }}
            <tr>
                <td class="text-center">{{subscribe.stt}}</td>   
                <td>
                    {{subscribe.email}} <span class="btn-task-group">{{subscribe.u_edit}} {{subscribe.u_delete}}</span>
                </td>   
                <td>{{subscribe.date}}</td>
            </tr>
            {{ endfor }}
            <tr>
                <td colspan="3" class="foot">
                    <div class="box-horizontal">
                        <div class="left text-muted text-sm">{{current_page}}</div>
                        <div class="right">{{pagination}}</div>                    
                    </div>
                </td>
            </tr>              
        </table>
    </form>

    {{ if exports|length }}
        <table class="table space-top">
            <tr>
                <td class="text-lg text-bold">Danh sách tập tin đã export</td>
            </tr>        
            {{ for item in exports }}
            <tr>
                <td style="border-bottom:0;">
                    {{export.file}}{{export.u_download}}{{export.u_delete}}
                    <em class="text-muted" style="margin-top:5px;">Tập tin này được export vào ngày {{export.date}}</em>
                </td>   
            </tr>
            {{ endfor }}
        </table>
    {{endif}}

{{ include "layout/footer" }}