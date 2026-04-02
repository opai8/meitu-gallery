<!-- Footer -->
<footer class="footer">

	<div class="line"></div>
	<div class="footer-line"><?php echo my_option('footer_ending');?> </div>
	<div class="footer-line">邮箱：<?php echo my_option('footer_email'); ?></div>
	<div class="footer-line"><?php echo my_option('footer_copyright'); ?></div>

</footer>

<?php wp_footer(); ?>


<!-- 回到顶部 -->
<div id="BackTop" class="back-top">
    <a href="#top" title="返回顶部" aria-label="返回页面顶部">
        <img src="<?php echo get_template_directory_uri(); ?>/image/top.png" loading="lazy" alt="返回顶部图标">
    </a>
</div>

<script type="text/javascript">
	idBackTop=document.getElementById('BackTop');
	idBackTop.onclick=function (){
			document.documentElement.scrollTop=0;
			sb();
			}
	window.onscroll=sb;
	function sb(){
			if(document.documentElement.scrollTop==0){
					idBackTop.style.display="none";
					}else{
							idBackTop.style.display="block";
							}
			}
</script>

<!-- 统计代码 -->
<?php
if( !empty(my_option( 'site_statistical' ) ) ) {
	echo my_option('site_statistical');
}
?>

</body>
</html>