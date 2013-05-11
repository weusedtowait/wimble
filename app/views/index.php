<!DOCTYPE html>
<html ng-app='Wimble' id='Wimble'>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' lang='es'/>
	<?php
		$css = array(
			//'bootstrap.css',
			//'bootstrap-responsive.css',
			//'angular-ui.min.css',
			//'select2/select2.css',
		);
		$less = array(
			//'styles.less'
		);
		$js = array(
			'libs/jquery.min.js',
			//'libs/jquery-ui.min.js',
			//'libs/less.min.js',
			'libs/angular.min.js',
			//'libs/angular-ui.min.js',
			//'libs/select2.min.js',
			//'libs/bootstrap.min.js',
			'controllers/app.js',
		);
		//echo Asset::css($css);
		//echo Asset::css(array('bootstrap-responsive.css'), array('media' => 'screen'));
		//echo Asset::css($less, array('rel' => 'stylesheet/less'));
		echo Asset::js($js);
	?>
	<? /*
	<!--[if lte IE 7]>
	<script src="assets/js/libs/browser_warning/warning.js"></script>
	<script>
		window.onload = function(){e("assets/js/libs/browser_warning/")}
	</script>
	<![endif]-->

	<!--[if lte IE 8]>
	<script>
		document.createElement('crud-buttons');
	</script>
	<![endif]-->
 	*/ ?>
</head>
<body ng-controller="AppCtrl">
	<div class="container" ng-view></div>
</body>
</html>