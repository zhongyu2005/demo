<?php $title="web 管理员登陆"; ?>
<?php include(CONST_VIEW.'common/header.php'); ?>
<style type="text/css">
body {
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #eee;
}

.form-signin {
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}
.form-signin .form-signin-heading,
.form-signin .checkbox {
  margin-bottom: 10px;
}
.form-signin .checkbox {
  font-weight: normal;
}
.form-signin .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
	 -moz-box-sizing: border-box;
		  box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="text"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
.alert{
  margin-top:8px;
}
</style>
<div class="container">
  <form class="form-signin" role="form">
    <h2 class="form-signin-heading">Web 后台管理</h2>
    <input type="text" class="form-control" placeholder="Account Sign" id="username" name="username" required autofocus>
    <input type="password" class="form-control" placeholder="Password" id="userpass" name="userpass" required>
    <div class="checkbox">
      <label>
        <input type="checkbox" value="1"> Remember me
      </label>
    </div>
    <?php echo form_token(); ?>
    <input type="hidden" id="url" name="url" value="<?php echo $turl; ?>" />
    <button class="btn btn-lg btn-primary btn-block" type="submit">管理员登陆</button>
  </form>

</div> <!-- /container -->
<script type="text/javascript">
  function html_load () {
    $("form").eq(0).on("submit",function(){
      var uname=$("#username");
      var upass=$('#userpass');
      if(uname.val().length<4){
        uname.focus().select();
        var html=show_tips('账号不正确');
        $(this).append(html);
        return false;
      }
      $(this).find(".alert").remove();
      if(upass.val().length<4){
        upass.focus().select();
        var html=show_tips('密码不正确');
        $(this).append(html);
        return false;
      }
      $(this).find(".alert").remove();
      if(def.lock==true){
        return false;
      }
      def.lock=true;
      var url=def.self;
      var data={submit:'submit',username:uname.val(),userpass:upass.val(),'__hash__':$("#__hash__").val()};
      ajax_post(url,data,function(j){
        def.lock=false;
        if(j.error>0){
          var html=show_tips(j.msg);
          $("#__hash__").remove();
          $("form").eq(0).append(html).append(j.data.tk);
          return false;
        }
        location=$('#url').val();
      })
      return false;
    })
  }
</script>
<?php include(CONST_VIEW.'common/footer.php'); ?>