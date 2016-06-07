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