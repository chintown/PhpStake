
      </div> <?php /*#content*/ ?>
      <?php
        if (isset($r_msg)) {
            $visibility = '';
            $alert_type = 'alert-' . $r_msg->type;
            $alert_msg = $r_msg->msg;
        } else {
            $alert_type = '';
            $visibility = 'hide';
            $alert_msg = '';
        }
      ?>

      <div class="ajax-msg alert <?=$alert_type.' '.$visibility?>"><?=$alert_msg?></div>
    </div> <?php /*#wrap*/ ?>



    <div id="wrap_resource">
        <?php echo serialize_vars_as_js(array('DEV_MODE' => DEV_MODE,'IS_MOBILE' => $IS_MOBILE,'WEB_HOST' => WEB_HOST)); ?>


        <script src="//<?=toggle_min_script('ajax.googleapis.com/ajax/libs/jquery/'.$CONF_JQUERY_VERSION.'/jquery.js')?>" type="text/javascript"></script>
        <script>window.jQuery || document.write('<script src="<?=toggle_min_script(PARENT_WEB_PATH.'/js/vendor/jquery-'.$CONF_JQUERY_VERSION.'.js')?>" type="text/javascript"><\/script>')</script>

        <script src="<?=toggle_min_script(PARENT_WEB_PATH.'/js/vendor/bootstrap.hacked.js')?>" type="text/javascript"></script>

      <?php if (is_defined_const_available('SENTRY_API_JS')) { ?>
        <script src="//d3nslu0hdya83q.cloudfront.net/dist/1.0/raven.min.js"></script>
        <script>Raven.config('<?=SENTRY_API_JS?>').install();</script>
      <?php } ?>

      <?php if (is_defined_const_available('GA_CODE')) { ?>
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
          <?=GA_CODE?>
        </script>
      <?php } ?>

      <? include("template/common.project.footer.php"); ?>

      <?php if (!empty($FOOTER_EXTRA)) { include("template/$FOOTER_EXTRA"); }?>


        <?php require "common/phpdebugbar.footer.php"; ?>
      <?php /*#wrap_resource*/ ?>

    </div>
  </body>
</html>

<?php
    require $project_path.'common/xhprof.footer.php';