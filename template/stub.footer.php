<?php // leave file path as plain text (rather than php var) for better gen_js.py parsing
// keep the sequence of html attribute so that parser can get correct js files
if (DEV_MODE) { ?>
<script type="text/javascript" src="<?=WEB_PATH?>/js/vendor/ajax.js"></script>
<script type="text/javascript" src="<?=WEB_PATH?>/js/vendor/dpd.js"></script>
<script type="text/javascript" src="<?=WEB_PATH?>/js/vendor/Class.js"></script>
<script type="text/javascript" src="<?=WEB_PATH?>/js/vendor/jquery.tmpl.js"></script>
<script type="text/javascript" src="<?=WEB_PATH?>/js/vendor/jquery-ui-1.9.2.custom.js"></script>
<?php } else { ?>
<script src="<?=WEB_PATH?>/js/___STUB___.lib.min.js" type="text/javascript"></script>
<?php } ?>
<?php // leave file path as plain text (rather than php var) for better gen_js.py parsing
if (DEV_MODE) { ?>
<script src="<?=toggle_min_script(WEB_PATH.'/js/std.js')?>" type="text/javascript"></script>
<script src="<?=toggle_min_script(WEB_PATH.'/js/common.jquery.js')?>" type="text/javascript"></script>
<script src="<?=toggle_min_script(WEB_PATH.'/js/common.project.js')?>" type="text/javascript"></script>
<script src="<?=toggle_min_script(WEB_PATH.'/js/CrudManager.js')?>" type="text/javascript"></script>
<script src="<?=toggle_min_script(WEB_PATH.'/js/___STUB___.js')?>" type="text/javascript"></script>
<?php } else { ?>
<script src="<?=WEB_PATH?>/js/___STUB___.pack.min.js" type="text/javascript"></script>
<?php } ?>
<?php
echo serialize_vars_as_js(array(
    'TRANS'=> array(
    ),
));
?>