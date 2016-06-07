<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php if (!empty($title))echo $title;?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<?php echo dom_help(array('public/css/bootstrap.min.css','public/css/style.css')); ?>
<script type="text/javascript">
	var def=<?php
		if(empty($tpl_args)):
			echo '[]';
		else:
			echo json_encode($tpl_args);
		endif;
		?>;
</script>
</head>
<body>
<header>
	<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="#" class="navbar-brand">Web Admin</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="<?php echo '#';url('DiskTop','index'); ?>">我的桌面</a></li>
            <li><a href="<?php echo '#';url('DiskTop','index'); ?>">个人信息</a></li>
            <li><a href="<?php echo '#';url('Index','logout'); ?>">安全退出</a></li>
            <li><a href="<?php echo '#';url('DiskTop','index'); ?>">帮助</a></li>
          </ul>
          <form class="navbar-form navbar-right">
            <input type="text" placeholder="Search..." class="form-control">
          </form>
        </div>
      </div>
    </nav>
</header><!--header-->

<div class="container-fluid">
	<div class="row">
		<aside class="col-sm-3 col-md-2 sidebar" id="aside-nav">
        <?php
          $nav=array(
            array(
              'id'=>-10,'name'=>'SimpleSql','action'=>'SimpleSql','method'=>'index','parent'=>0,
              'sub'=>array(
                '-12'=>array('id'=>-12,'name'=>'快速建库','action'=>'SimpleSql','method'=>'index','parent'=>-10),
                '-13'=>array('id'=>-12,'name'=>'快速生成','action'=>'SimpleSql','method'=>'create','parent'=>-10),
              )
            ),
          );
          if(!empty($nav)):
            foreach ($nav as $val):
        ?>
        <div class="list-group">
          <a class="list-group-item active" id="nav-<?php echo $val['action']; ?>" href="javascript:;"><?php echo $val['name']; ?> <span class="glyphicon glyphicon-chevron-up"></span></a>
          <?php foreach ($val['sub'] as $v): ?>
          <a class="list-group-item <?php if(CONST_ACTION==$v['action'] && CONST_METHOD==$v['method'])echo 'list-group-item-info'; ?>" href="<?php echo url($v['action'],$v['method']); ?>"><?php echo $v['name']; ?></a>
          <?php endforeach; ?>
      	</div>
        <?php endforeach; endif; ?>
		</aside><!-- aside -->

		<section class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
		