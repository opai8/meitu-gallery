<?php
/**
 * 美图库主题头部模板 - 单文章图集版
 *
 * @package 美图库
 * @version 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body>
    <!-- ========== 页面头部 ========== -->
    <header class="site-header">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
            <?php echo get_seo_title(); ?>
        </a>

        <div class="nav-center">
            <span><?php echo get_seo_description(); ?></span>
        </div>

        <div class="nav-right">
			<?php 
			// 获取社交媒体选项数组
			$socia = my_option('opt_declare'); 
			
			// 核心判断：只有当 $socia 数组不为空时，才显示整个社交链接区域
			if (!empty($socia)) : 
			?>

				<?php // 判断 Telegram 链接是否存在且不为空，然后显示 ?>
				<?php if (!empty($socia['opt_telegram'])) : ?>
					<a href="<?php echo esc_url($socia['opt_telegram']); ?>" class="social-link" title="Telegram" target="_blank" rel="noopener noreferrer">
						<svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
							<path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z" />
						</svg>
						<span>Telegram</span>
					</a>
				<?php endif; ?>

				<?php // 判断 Github 链接是否存在且不为空，然后显示 ?>
				<?php if (!empty($socia['opt_github'])) : ?> 
					<a href="<?php echo esc_url($socia['opt_github']); ?>" class="social-link" title="Github" target="_blank" rel="noopener noreferrer">
						<svg class="social-icon" t="1777254304655" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="5121" id="mx_n_1777254304655" fill="currentColor"><path d="M512 42.666667A464.64 464.64 0 0 0 42.666667 502.186667 460.373333 460.373333 0 0 0 363.52 938.666667c23.466667 4.266667 32-9.813333 32-22.186667v-78.08c-130.56 27.733333-158.293333-61.44-158.293333-61.44a122.026667 122.026667 0 0 0-52.053334-67.413333c-42.666667-28.16 3.413333-27.733333 3.413334-27.733334a98.56 98.56 0 0 1 71.68 47.36 101.12 101.12 0 0 0 136.533333 37.973334 99.413333 99.413333 0 0 1 29.866667-61.44c-104.106667-11.52-213.333333-50.773333-213.333334-226.986667a177.066667 177.066667 0 0 1 47.36-124.16 161.28 161.28 0 0 1 4.693334-121.173333s39.68-12.373333 128 46.933333a455.68 455.68 0 0 1 234.666666 0c89.6-59.306667 128-46.933333 128-46.933333a161.28 161.28 0 0 1 4.693334 121.173333A177.066667 177.066667 0 0 1 810.666667 477.866667c0 176.64-110.08 215.466667-213.333334 226.986666a106.666667 106.666667 0 0 1 32 85.333334v125.866666c0 14.933333 8.533333 26.88 32 22.186667A460.8 460.8 0 0 0 981.333333 502.186667 464.64 464.64 0 0 0 512 42.666667" p-id="5122"></path></svg>
						<span>Github</span>
					</a>
				<?php endif; ?>

				<?php
					// 判断 Email 链接是否存在且不为空，然后显示
					$email = !empty($socia['opt_email']) ? $socia['opt_email'] : '';
				?>
				<?php if (!empty($email)) : ?>
					<a href="<?php echo esc_url('mailto:' . $email); ?>" class="social-link" title="Email">
						<svg class="social-icon" t="1777254729558" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="6559" fill="currentColor"><path d="M741.12 305.737143H276.114286L511.817143 528.457143z" fill="#ce93d8" p-id="6560"></path><path d="M524.8 566.857143a18.651429 18.651429 0 0 1-25.417143 0.182857l-62.72-59.245714L256 668.525714v49.737143h512v-49.737143l-181.577143-161.645714L524.8 566.857143zM256 337.005714v282.514286l153.965714-136.96zM768 619.52V330.788571l-155.245714 150.491429z" fill="#ce93d8" p-id="6561"></path><path d="M512 9.142857C234.24 9.142857 9.142857 234.24 9.142857 512S234.24 1014.857143 512 1014.857143 1014.857143 789.76 1014.857143 512 789.76 9.142857 512 9.142857z m292.571429 727.405714c0 10.057143-8.228571 18.285714-18.285715 18.285715H237.714286c-10.057143 0-18.285714-8.228571-18.285715-18.285715V287.451429c0-10.057143 8.228571-18.285714 18.285715-18.285715h548.571428c10.057143 0 18.285714 8.228571 18.285715 18.285715v449.097142z" fill="#ce93d8" p-id="6562"></path></svg>
						<span>Email</span>
					</a>
				<?php endif; ?>
				
			<?php endif; // 结束对 $socia 的判断 ?>

			<?php // 这个链接不属于社交图标，根据您的需求决定是否也放在条件判断内。这里假设它总是显示。?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">返回主页区域</a>

		</div>
    </header>

    <!-- ========== 通知栏 ========== -->
    <?php if ( get_bloginfo( 'description' ) ) : ?>
    <div class="notice-bar">
        <span>最近更新</span>
        <?php 
        // 获取最新一篇已发布文章的发布日期
        $latest_posts = get_posts( array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );
        
        if ( ! empty( $latest_posts ) ) {
            $latest_post = $latest_posts[0];
            $post_date = get_the_date( 'Y-m-d', $latest_post );
            $post_time = get_the_time( 'H:i:s', $latest_post );
            echo esc_html( $post_date . ' ' . $post_time );
        } else {
            echo esc_html( '暂无已发布文章' );
        }
    ?>
    </div>
    <?php endif; ?>

    <!-- ========== 广告区域 ========== -->
    <?php
/**
 * 广告区域渲染逻辑
 * 
 * 数据来源：WordPress 自定义选项 opt_ad
 * 布局规则：
 * - 0 个广告：不显示广告区域和分割线
 * - 1 个广告：单条横幅全屏
 * - 2 个广告：两条横幅全屏
 * - 3 个广告：第一条横幅全屏 + 后两条双列并排
 * 静态测试：WP_DEBUG 模式下自动加载测试数据
 */

// 1. 获取广告配置数据（使用封装函数，已过滤启用的广告）
$enabled_ads = meitu_get_advertisements();

// 2. 开发环境：如果无数据，加载静态测试数据（便于调试）
// if ( empty( $enabled_ads ) && defined( 'WP_DEBUG' ) ) {
    // $enabled_ads = array(
        // array(
            // 'ad_id'       => 'ad1',
            // 'ad_img'      => 'https://a.hinimg.com/hm-files/2026/04/f6db47efb8d1e9f3c7f44f244c681fb7.jpg',
            // 'ad_url'      => 'https://example.com/ad1',
            // 'ad_switcher' => '1',
        // ),
        // array(
            // 'ad_id'       => 'ad2',
            // 'ad_img'      => 'https://a.hinimg.com/hm-files/2026/04/588fe9a506860a6fd02db1c17be7a89c.jpg',
            // 'ad_url'      => 'https://example.com/ad2',
            // 'ad_switcher' => '1',
        // ),
        // array(
            // 'ad_id'       => 'ad3',
            // 'ad_img'      => 'https://a.hinimg.com/hm-files/2026/04/588fe9a506860a6fd02db1c17be7a89c.jpg',
            // 'ad_url'      => 'https://example.com/ad3',
            // 'ad_switcher' => '1',
        // ),
    // );
// }

// 3. 获取广告总数
$ad_count = count( $enabled_ads );

// 4. 根据广告数量渲染不同布局
if ( $ad_count > 0 ) :
?>
    <div class="ad-container">
        <?php if ( 1 === $ad_count ) : ?>
        <!-- 情况 1：只有 1 个广告 → 单条横幅全屏 -->
        <div class="ad-banner">
            <a href="<?php echo esc_url( $enabled_ads[0]['ad_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="ad-link">
                <img src="<?php echo esc_url( $enabled_ads[0]['ad_img'] ); ?>" alt="广告" loading="lazy">
                <span class="ad-badge">AD</span>
            </a>
        </div>

        <?php elseif ( 2 === $ad_count ) : ?>
        <!-- 情况 2：有 2 个广告 → 两条横幅全屏 -->
        <div class="ad-banner">
            <a href="<?php echo esc_url( $enabled_ads[0]['ad_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="ad-link">
                <img src="<?php echo esc_url( $enabled_ads[0]['ad_img'] ); ?>" alt="广告" loading="lazy">
                <span class="ad-badge">AD</span>
            </a>
        </div>
        <div class="ad-banner">
            <a href="<?php echo esc_url( $enabled_ads[1]['ad_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="ad-link">
                <img src="<?php echo esc_url( $enabled_ads[1]['ad_img'] ); ?>" alt="广告" loading="lazy">
                <span class="ad-badge">AD</span>
            </a>
        </div>

        <?php elseif ( $ad_count >= 3 ) : ?>
        <!-- 情况 3：有 3 个或更多广告 → 第一条横幅 + 后两条双列（最多显示 3 个） -->

        <!-- 第一行：横幅广告（第 1 个） -->
        <div class="ad-banner">
            <a href="<?php echo esc_url( $enabled_ads[0]['ad_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="ad-link">
                <img src="<?php echo esc_url( $enabled_ads[0]['ad_img'] ); ?>" alt="广告" loading="lazy">
                <span class="ad-badge">AD</span>
            </a>
        </div>

        <!-- 第二行：双列广告（第 2、3 个） -->
        <div class="ad-grid">
            <div class="ad-item">
                <a href="<?php echo esc_url( $enabled_ads[1]['ad_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="ad-link">
                    <img src="<?php echo esc_url( $enabled_ads[1]['ad_img'] ); ?>" alt="广告" loading="lazy">
                    <span class="ad-badge">AD</span>
                </a>
            </div>
            <div class="ad-item">
                <a href="<?php echo esc_url( $enabled_ads[2]['ad_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="ad-link">
                    <img src="<?php echo esc_url( $enabled_ads[2]['ad_img'] ); ?>" alt="广告" loading="lazy">
                    <span class="ad-badge">AD</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- 广告与内容分割线（仅在有广告时显示） -->
    <div class="ad-content-divider"></div>

    <?php endif; ?>

    <!-- ========== 布局切换按钮（仅移动端显示） ========== -->
    <div class="layout-toggle-bar">
        <button class="layout-toggle-btn" id="layoutToggle">
            <span class="toggle-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="5" y="3" width="14" height="18" rx="1" />
                </svg>
            </span>
            <span class="toggle-label">单栏模式</span>
        </button>
    </div>