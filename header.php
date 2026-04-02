<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8"> <!-- 字符编码 -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 响应式设计 -->
	<meta name="keywords" content="<?php echo my_option('seo_keyword'); ?>"> <!-- 关键字 -->
	<meta name="description" content="<?php echo my_option('seo_description'); ?>"> <!-- 描述 -->
	<!-- 浏览器标签页显示 -->
	<title><?php echo my_option('seo_title'); ?>-<?php echo my_option('seo_description'); ?></title>
	<link rel="icon" href="<?php echo my_option('opt_favicon'); ?>" type="image/png" />
	<?php wp_head(); ?>
</head>

<body>

<div class="container">
	<div class="main">
		<a class="title" href="<?php echo get_option('home'); ?>"><?php echo my_option('seo_title'); ?></a>
		<span class="desc"><?php echo my_option('seo_description'); ?></span>
		<?php
			$header = my_option('opt_top_declare');
		?>
		<span class="web_address"><a href="<?php echo $header['opt_header_home']; ?>" >回家的路</a></span>
		<div class="socia">
			<a href="<?php echo $header['opt_telegram']; ?>" target="_blank" >
				<img src="<?php echo get_template_directory_uri(); ?>/image/telegram.png"> Telegram
			</a>:查看往期美图
		</div>
	</div>
</div>


<!-- 时间线 -->
<div class="gradient-line">
	<div class="dline"></div>
    <div class="date_text">最近更新：<?php the_time('Y-m-d'); ?></div>
    <div class="dline"></div>
</div>
