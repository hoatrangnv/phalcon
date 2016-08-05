    <form name="form_edit" id="form_edit" method="POST" action="{{s_action}}"> 
    <table class="table">  
        <tr>
            <td width="25%">Tên nhóm:</td>
            <td width="75%"><input class="input" type="text" name="name" value="{{data_name}}" /></td>
        </tr>
        <tr>
            <td class="text-top">Mô tả:</td>
            <td><textarea class="input" name="description" cols="76" rows="3">{{data_description}}</textarea></td>
        </tr>
        <tr>
            <td colspan="2" class="foot text-right">{{s_button}}</td>
        </tr>
    </table>
    </form>
