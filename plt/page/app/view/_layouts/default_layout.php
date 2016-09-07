<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  	<link rel="stylesheet" href="css/style.css" type="text/css" />
  	<link rel="stylesheet" type="text/css" href="css/uploadify.css">

  	<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
  	<link rel="stylesheet" type="text/css" href="css/jquery-ui-timepicker-addon.css">
  	<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/ >

	
	<script src="js/jquery-1.7.1.min.js" type="text/javascript"></script>
	<script src="js/jquery.uploadify.min.js" type="text/javascript"></script>

	<script src="js/jquery-ui.js" type="text/javascript"></script>
	<script src="js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
	<script src="js/jquery-ui-timepicker-zh-CN.js" type="text/javascript"></script>


	
  	<title><?php $this->_block('title'); $this->_endblock(); ?></title>
</head>

<body>
<div class="container">
		<div class="header">
			<a href="<?php echo url('account/signup');?>">Account_SignUp</a>
		</div>
		<div class="new_form">
			<?php $this->_block('contents'); $this->_endblock(); ?>
		</div>
		
		<div class="large_form">
			<?php $this->_block('lcontents'); $this->_endblock(); ?>
		</div>
	</div>

</body>

<script type="text/javascript">
		<?php $this->_block('script'); $this->_endblock(); ?>
</script>
</html>
