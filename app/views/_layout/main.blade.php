<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="../../../public/favicon.ico">

	<title>@yield('title', "LaravelRUS")</title>

	<!-- Bootstrap core CSS -->
<!--	<link href="/bootstrap-todc/css/bootstrap.css" rel="stylesheet">-->
<!--	<link href="/bootstrap-todc/css/todc-bootstrap.css" rel="stylesheet">-->

	<link href="/bootstrap/css/bootstrap.css" rel="stylesheet">

	<link href="/app/css/style_core.css" rel="stylesheet">

	<!-- Just for debugging purposes. Don't actually copy this line! -->
	<!--[if lt IE 9]><script src="/bootstrap/js/ie8-responsive-file-warning.js"></script><![endif]-->

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->


	<!-- Bootstrap core JavaScript
================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="/vendor/jquery/jquery-1.11.1.min.js"></script>
	<script src="/bootstrap/js/bootstrap.min.js"></script>

<!--	<script src="/vendor/ace/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>-->
<!--	<script>-->
<!--		$(document).ready(function() {-->
<!--			var editor = ace.edit("ace-editor");-->
<!--			editor.setTheme("ace/theme/crimson_editor");-->
<!--			editor.getSession().setMode("ace/mode/php");-->
<!--		});-->
<!--	</script>-->

</head>

<body>

<div class="navbar coreos-nav navbar-static-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#laravel-navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div class="brand-area">
				<a class="navbar-brand" href="/">Laravel.ru</a>
			</div>
		</div>

		<div class="navbar-collapse collapse" id="laravel-navbar-collapse">
			<ul class="nav navbar-nav">
				<li><a href="<?= route("documentation") ?>">Документация</a></li>
				<?if(allowEditTerms()){?><li><a href="<?= route("terms") ?>">Термины</a></li><?}?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?if(Auth::check()){?>
					<li><a href="<?= route('user.blog', [Auth::user()->name]) ?>">Блог</a></li>
					<li><a href="<?= route('auth.logout') ?>">Выход</a></li>
				<?}else{?>
					<li><a href="<?= route('auth.login') ?>">Вход</a></li>
					<li><a href="<?= route('auth.registration') ?>">Регистрация</a></li>
				<?}?>
			</ul>
		</div><!--/.nav-collapse -->

	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			@include("_layout.partials.flash")
		</div>
	</div>
</div>

@yield("container")


<footer>
	<div class="container">
		<p>&copy; Русское Laravel сообщество <?= date('Y') ?></p>
	</div>
</footer>


</body>
</html>