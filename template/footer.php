
      </div> <?php /*#content*/ ?>

      <div class="ajax-msg alert hide"></div>
    </div> <?php /*#wrap*/ ?>



    <div id="wrap_resource">
        <?php echo serialize_vars_as_js(array('DEV_MODE' => DEV_MODE,'IS_MOBILE' => $IS_MOBILE,'WEB_HOST' => WEB_HOST)); ?>


        <script src="//<?=toggle_min_script('ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.js')?>" type="text/javascript"></script>
        <script>window.jQuery || document.write('<script src="<?=toggle_min_script('js/vendor/jquery-1.8.2.js')?>" type="text/javascript"><\/script>')</script>

        <script src="<?=toggle_min_script(PARENT_WEB_PATH.'/js/vendor/bootstrap.hacked.js')?>" type="text/javascript"></script>

      <?php if (SENTRY_API_JS != '') { ?>
        <script src="//d3nslu0hdya83q.cloudfront.net/dist/1.0/raven.min.js"></script>
        <script>Raven.config(SENTRY_API).install();</script>
      <?php } ?>

      <? include("template/project.footer.php"); ?>

      <?php if (!empty($FOOTER_EXTRA)) { include("template/$FOOTER_EXTRA"); }?>
    </div> <?php /*#wrap_resource*/ ?>
  </body>
</html>
