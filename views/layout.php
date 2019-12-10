<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/skeleton/2.0.4/skeleton.min.css" />
<link rel="stylesheet" href="view/style.css" />
<!--
-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<title><?php echo $title; ?></title>
<style>
#w_query{display:none;}
table {
	/*
	outline: 1px solid blue;
	*/
	margin: 0 auto 0 auto;
}
textarea, textarea.myTextarea {
	width: 400px;
	heigh: 150px;"
}
</style>
</head>
<body>
<div class="container">
<?php echo $header_content; ?>
<?php echo $body_content; ?>
</div>
</body>
</html>
