<?php
  $title="web 后台管理";
?>
<?php include(CONST_VIEW.'common/header_admin.php'); ?>
<style type="text/css">
.pre-scrollable{
  min-height: 500px;
}
</style>
<ol class="breadcrumb">
  <li><a href="javascript:;">系统后台</a></li>
  <li><a href="javascript:;">simpleSql</a></li>
  <li class="active">快速建库</li>
</ol>
<form class="form-horizontal" id="formSubmit">
  <div class="form-group">
    <label for="tab_name" class="col-sm-2 control-label">数据表名称</label>
    <div class="col-sm-6">
      <input type="text" class="form-control" id="tab_name" name="tab_name" value="<?php echo $tab_name; ?>" placeholder="管理员名称">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-1 col-sm-1">
      <button type="button" id="btnSubmit" class="btn btn-default">提 交</button>
    </div>
  </div>
  <input type="hidden" name="a" value="<?php echo CONST_ACTION ?>" />
  <input type="hidden" name="m" value="<?php echo CONST_METHOD ?>" />
</form>
<script type="text/javascript">
  function html_load () {
    $("#formSubmit").on("keydown",function(e){
      var key=e.which;
      if(key==13){
        $("#btnSubmit").trigger("click");
      }
    })
    $("#btnSubmit").on("click",function(){
        form_submit();
    })
    $("#sql-pre1,#sql-pre2,#sql-pre3,#sql-pre4,#sql-pre5").on("click",function(){
        /*
        $(this).zclip({
            path: "public/js/ZeroClipboard.swf",
            copy: function(){
              return $(this).html();
            },afterCopy:function(){}
        });
        */
    });
  }
  function form_submit(){
      $("#formSubmit").submit();
  }
</script>

<?php if(isset($showType) && $showType=='tpl' ):?>
  <div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">基本信息</a></li>
    <li role="presentation"><a href="#action" aria-controls="action" role="tab" data-toggle="tab">Action-tpl</a></li>
    <li role="presentation"><a href="#add" aria-controls="add" role="tab" data-toggle="tab">add-tpl</a></li>
    <li role="presentation"><a href="#edit" aria-controls="edit" role="tab" data-toggle="tab">edit-tpl</a></li>
    <li role="presentation"><a href="#list" aria-controls="list" role="tab" data-toggle="tab">list-tpl</a></li>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="home">
        <pre class="pre-scrollable" id="sql-pre1"><?php echo htmlspecialchars($table_ddl);?>
        </pre>
    </div>
    <div role="tabpanel" class="tab-pane" id="action">
        <pre class="pre-scrollable" id="sql-pre2"><?php echo htmlspecialchars($action_tpl);?>
        </pre>
    </div>
    <div role="tabpanel" class="tab-pane" id="add">
        <pre class="pre-scrollable" id="sql-pre3"><?php echo htmlspecialchars($add_tpl);?>
        </pre>
    </div>
    <div role="tabpanel" class="tab-pane" id="edit">
        <pre class="pre-scrollable" id="sql-pre4"><?php echo htmlspecialchars($edit_tpl);?>
        </pre>
    </div>
    <div role="tabpanel" class="tab-pane" id="list">
        <pre class="pre-scrollable" id="sql-pre5"><?php echo htmlspecialchars($list_tpl);?>
        </pre>
    </div>
  </div>
</div>
<?php endif;?>

<?php include(CONST_VIEW.'common/footer_admin.php'); ?>