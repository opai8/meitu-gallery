<?php
/**
 * 404 页面模板
 *
 * 当请求的页面未找到时，将显示此模板。
 *
 * @package Your_Theme
 */

// 确保不被直接访问
if ( ! defined( 'ABSPATH' ) ) {
	exit; // 退出执行
}

get_header(); // 加载头部

?>


<div class="error-404-content">
    <?php 
    // 使用主题目录下的图片，这是WordPress的标准做法
    // 请确保您的主题目录下有 /assets/images/404.png 这个文件
    $image_url = get_template_directory_uri() . '/image/404_1.png';
    ?>
    <img src="<?php echo esc_url( $image_url ); ?>" alt="404 Not Found" class="error-404-image"/>
    
    <div class="error-404-buttons">
        <?php 
        // 使用 home_url() 函数动态获取首页链接，而不是硬编码 "/"
        $home_url = esc_url( home_url( '/' ) );
        ?>
        <a href="<?php echo $home_url; ?>" class="btn-home">回到首页</a>
        
        <?php 
        // 联系站长的链接，可以链接到联系页面或直接是邮件地址
        // 假设您有一个“联系我们”页面
        $contact_url = esc_url( home_url( '/contact/' ) ); 
        // 或者使用 mailto:
        // $contact_url = 'mailto:your-email@example.com';
        ?>
        <a href="<?php echo $contact_url; ?>" target="_blank" class="btn-contact">联系站长</a>
        <div style="clear: both;"></div>
    </div>
    
    <!-- (可选但推荐) 添加一个搜索框，帮助用户找到他们想要的内容 -->
    <div class="error-404-search">
        <p>或者，尝试搜索您想找的内容：</p>
        <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <input type="search" class="search-field" placeholder="输入关键词..." value="<?php echo get_search_query(); ?>" name="s" title="搜索：" />
            <button type="submit" class="search-submit">搜索</button>
        </form>
    </div>

</div>

<?php get_footer(); ?>
</body>
</html>