</section><!--section-->
	</div><!--it's row-->
</div><!--it's container-->
<footer>
<!-- modal confirm -->
<div class="modal fade bs-example-modal-lg" id="bt_confirm_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">系统提示</h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
      </div>
    </div>
  </div>
</div>

<!-- modal alert -->
<div class="modal fade bs-example-modal-sm" id="bt_alert_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">系统提示</h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
      </div>
    </div><!--modal-content-->
  </div>
</div>

</footer><!--/.footer-->
<div class="hide">
	<?php echo dom_help(array('public/js/jquery-1.11.2.min.js','public/js/bootstrap.min.js','public/js/common.js')); ?>
  <?php echo dom_help(array('public/js/jquery.zclip.min.js')); ?>
</div>
</body>
</html>