<script class='crud_head_row' type='text/x-jquery-tmpl'>
    <ul class='unstyled'></ul>
</script>
<script class='crud_head_column' type='text/x-jquery-tmpl'>
    <li class='${k}'>${kDisp}</li>
</script>

<script class='crud_body' type='text/x-jquery-tmpl'>
    <ul class='unstyled'></ul>
</script>
<script class='crud_body_row' type='text/x-jquery-tmpl'>
    <li></li>
</script>
<script class='crud_body_column' type='text/x-jquery-tmpl'>
    <div name='${k}' class='${k}'>${v}</div>
</script>

<script id='crud_control' type='text/x-jquery-tmpl'>
    <div class='crud-control'>
        <a class='crud-control-edit' href="javascript:void(0)" ><i class="icon-edit"></i></a>
        <a class='crud-control-remove' href="javascript:void(0)" ><i class="icon-remove"></i></a>
    </div>
</script>

<script class='crud_add' type='text/x-jquery-tmpl'>
    <ul class='unstyled'></ul>
</script>
<script class='crud_add_column' type='text/x-jquery-tmpl'>
    <li class='${k}'>
        <span class='k'>${kDisp}</span><input type='text' name='${k}' class='v' value=''/>
    </li>
</script>

<script class='crud_edit' type='text/x-jquery-tmpl'>
    <ul class='unstyled'></ul>
</script>
<script class='crud_edit_column' type='text/x-jquery-tmpl'>
    <li class='${k}'>
        <span class='k'>${kDisp}</span><input type='text' name='${k}' class='v' value='${v}'/>
    </li>
</script>