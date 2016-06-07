<?php
  $title="web 后台管理";
  $arr=array(
       'tinyint','int','bigint','decimal','char','varchar','text','mediumtext','longtext',
       'datetime','date','','','','',''
  );
?>
<?php include(CONST_VIEW.'common/header_admin.php'); ?>
<style type="text/css">
.length,.place,.default{
  width: 50px;
}
.sort{
  width: 40px;
}
</style>
<ol class="breadcrumb">
  <li><a href="javascript:;">系统后台</a></li>
  <li><a href="javascript:;">simpleSql</a></li>
  <li class="active">快速建库</li>
</ol>
<div class="container" style="padding-bottom:20px;">
  <button type="button" id="btnAdd" class="btn btn-primary">Add</button>
  <button type="button" id="btnCreate" class="btn btn-primary">生成</button>
</div>
<div class="container-fluid table-responsive">
  <form class="form-horizontal" id="frm">
  <table class="table table-hover table-bordered table-striped " id="sql-tab">
    <tr>
      <th>名称</th>
      <th>类型</th>
      <th>长度</th>
      <th>小数位</th>
      <th>！Null</th>
      <th>默认</th>
      <th>注释</th>
      <th>排序</th>
    </tr>
  </table>
  </form>
    <pre class="pre-scrollable">
      预定义了主键和自增长的id和create_at 和 update_at 时间量.三个字段；
    </pre>
    <pre class="pre-scrollable" id="sql-pre">
      
    </pre>
</div>
<script type="text/template" id="tpl-sql1">
  <tr class="sub">
      <td>
        <input type="text" class="form-control name" name="table[n][name]" maxlengtd="20" />
      </td>
      <td>
        <select class="form-control type" onchange="" name="table[n][type]">
          <?php
            foreach ($arr as $val) :
          ?>
          <option value="<?php echo $val;?>"><?php echo $val;?></option>
          <?php endforeach; ?>
        </select>
      </td>
      <td>
        <input type="text" class="form-control length text-center" maxlength="4" value="1" name="table[n][length]" />
      </td>
      <td>
        <input type="text" class="form-control place text-center" maxlength="2" value="2" name="table[n][place]" />
      </td>
      <td>
        <input type="checkbox" class="form-control null" value="1" checked="checked" name="table[n][null]" />
      </td>
      <td>
        <input type="text" class="form-control default text-center" maxlength="10" value="0" name="table[n][default]" />
      </td>
      <td>
      <input type="text" class="form-control comment" maxlength="200" value=""  name="table[n][comment]" />
      </td>
      <td>
      <input type="text" class="form-control sort text-center" maxlength="2" value="1" onchange="sortTd(this)" />
      </td>
    </tr>
</script>
<script type="text/javascript">
  def.num=0;
  function html_load () {
    $("#btnAdd").on("click",function(){
        var html=$("#tpl-sql1").html();
        html=html.replace(/table\[n\]/g,"table["+def.num+"]");
        def.num++;
        $("#sql-tab").append(html);
    }).trigger('click');
    $("#btnCreate").on("click",function(){
        var url=def.self;
        data=$("#frm").serialize();
        console.log(data);
        ajax_post(url,data,function(j){
          $("#sql-pre").html(j.data);
        })
    })
  }
  function sortTd(o){
      var obj=$(o);
      var arr=[];
      $("#sql-tab .sub").each(function(){
          arr.push([$(this),$(this).find(".sort").val()]);
      })
      arr.sort(function(a,b){
        return a[1]-b[1];
      });
      $("#sql-tab .sub").remove();
      for(var i in arr){
          $("#sql-tab").append(arr[i][0]);
      }
  }
</script>
<?php include(CONST_VIEW.'common/footer_admin.php'); ?>