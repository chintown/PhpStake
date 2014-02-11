        </div>

        <div id="wrap_resource">
            <?php echo serialize_vars_as_js(array('DEV_MODE' => DEV_MODE,'IS_MOBILE' => $IS_MOBILE,'WEB_HOST' => WEB_HOST)); ?>
            <script src="<?=toggle_min_script(WEB_PATH.'/js/vendor/bootstrap.hacked.js')?>" type="text/javascript"></script>
            <?php if (SENTRY_API_JS != '') { ?>
              <script src="//d3nslu0hdya83q.cloudfront.net/dist/1.0/raven.min.js"></script>
              <script>Raven.config(SENTRY_API).install();</script>
            <?php } ?>
            <?php if (!empty($FOOTER_EXTRA)) { include("template/$FOOTER_EXTRA"); }?>
        </div> <?php /*#wrap_resource*/ ?>

</body>
</html>