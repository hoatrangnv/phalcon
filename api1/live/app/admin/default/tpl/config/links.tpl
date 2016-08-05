{{ include "layout/header" }}
    {{ if links_group|length }}     
    <div class="links" id="links" data-url="{{u_reorder}}">
        <ul>   
            {{ for item in links_group }}   
            <li class="link-group" id="orders_{{item.id}}">
                <div class="panel">
                    <div class="panel-heading box-horizontal">
                            <div class="left" style="margin-top:3px;">
                                <i class="icon icon-reorder sortable-handle sortable-handle-group" style="width:8px;padding: 5px 0;overflow:hidden;"></i>
                            </div>
                            <div class="left panel-title" style="margin-top:3px;margin-left:7px;">
                                <a href="javascript://" class="editable" style="color:inherit" data-url="{{u_editable}}" data-type="input" data-field="name" data-params="id:{{item.id}}" data-saveby="button">{{item.name}}</a>
                            </div>
                            <div class="right btn-task-group visible">{{item.u_add}} {{item.u_delete}}</div>
                            <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div class="nestable" data-url="{{item.u_reorder}}">{{item.links}}</div>
                    </div>
                    <div class="panel-footer text-italic text-sm text-muted">
                        Variable: {{ item.alias }}
                    </div>                    
                </div>
            </li>
            {{ endfor }}     
        </ul>
    </div>
    {{endif}}

<script type="text/javascript">
(function(){
    var $links = $('#links'), containerWidth = $links.width(), li = $links.find('>ul>li'), boxWidth, len = li.length;
    if (len <= 1){
        boxWidth = containerWidth;
    } else if (len == 2){
        boxWidth = (containerWidth - 24)/2;
    } else {
        boxWidth = (containerWidth - 36)/3;
    }

    li.width(boxWidth);

    
    $links.sortable({ 
        handle: '.sortable-handle-group',
        placeholder: 'sortable-highlight', 
        forcePlaceholderSize: true,          
        items: '.link-group',
        opacity: 0.7,          
        stop: function(event, ui){
            return $.post($(this).attr('data-url'), $(this).sortable('serialize'));
        }
    });    
})();
</script>

{{ include "layout/footer" }}    