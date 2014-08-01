<?php
    session_start();
    $path_fix_before_inc = '../';
    require $path_fix_before_inc.'core/main.inc.php';
    //require 'core/authentication.php'; // header/session should be handled as early as possible
    require 'template/header.php';
?>

<div id="___STUB___">
</div>

<div id="wrap_template">
<?php
    //require 'template/crud.php';
    //require 'template/___STUB___.crud.php';
?>
</div>
<?php
    add_extra_footer('___STUB___.footer.php');
    require 'template/footer.php';
?>
