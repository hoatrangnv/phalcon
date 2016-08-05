{{ include "layout/header" }}
    
    <form name="form_listing" id="form_listing" method="POST" action="{{s_action}}"> 
    <table class="table table-hover" id="listing" data-url="{{u_reorder}}">                 
    {{ if has_attributes_group }}                  
	    {{ for item in attributes_group }}      
        <tbody class="attribute-group" id="orders_{{item.id}}" data-url="{{item.u_reorder}}">   
            <tr>
                <td width="1%" class="subtitle order"><i class="icon icon-reorder sortable-handle sortable-handle-group"></i></td>
                <td width="96%" class="subtitle"><a href="javascript://" class="editable" style="color:inherit" data-url="{{u_editable}}" data-type="input" data-field="name" data-params="id:{{item.id}},type:group">{{item.name}}</a></td>
                <td width="3%" class="subtitle"><span class="btn-task-group visible">{{item.u_delete}}</span></td>
            </tr>     
            {{ for subitem in item.attributes }}       
            <tr id="orders_{{subitem.id}}" class="attribute-item">
                <td class="order"><i class="icon icon-reorder sortable-handle"></i></td>
                <td style="padding-left:30px;">
                    <a href="javascript://" class="editable" data-url="{{u_editable}}" data-type="input" data-field="name" data-params="id:{{subitem.id}}">{{subitem.name}}</a>
                </td>
                <td><span class="btn-task-group visible">{{subitem.u_delete}}</span></td>
            </tr>     
            {{ endfor }}
        </tbody>
	    {{ endfor }}   	                               
    {{else}}
	    <tr>
	        <td class="text-center text-muted">Thuộc tính không tồn tại</td>
	    </tr>            	        
    {{endif}}
    </table>
    </form>
    <script type="text/javascript">
    (function(){
        $('.attribute-group').sortable({ 
            handle: '.sortable-handle',
            placeholder: 'sortable-highlight', 
            items: '.attribute-item',
            connectWith: '.attribute-group',
            opacity: 0.7,
            containment: 'document',
            helper: function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;           
            },      
            start: function (event, ui) {
                ui.placeholder.height(ui.item.height());
                ui.placeholder.html('<td colspan="'+parseInt(ui.helper.children().length)+'"></td>');
            },        
            stop: function(event, ui){
                var $parent = ui.item.parent();
                return $.post($parent.attr('data-url'), $parent.sortable('serialize'));
            }
        });
        
        $('#listing').sortable({ 
            handle: '.sortable-handle-group',
            placeholder: 'sortable-highlight', 
            forcePlaceholderSize: true,          
            items: '.attribute-group',
            opacity: 0.7,          
            stop: function(event, ui){
                return $.post($(this).attr('data-url'), $(this).sortable('serialize'));
            }
        });    
    })();
    </script>
{{ include "layout/footer" }}    