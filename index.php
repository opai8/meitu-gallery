
<?php get_header(); ?>

<div class="waterfall">

	<?php 
		// 获取顶部广告数组
		// $ad_arr = my_option('ad_top_group');
		// $ads_html = hui_insert_ads($ad_arr);
		// echo $ads_html;
		
		// 输出50图
		echo hui_get_thumbnail(false,true);
	?>

</div>


<?php get_footer(); ?>

