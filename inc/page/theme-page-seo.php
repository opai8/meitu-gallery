<?php

/* ========== 页面布局 page ========== */
/*
 * SEO theme-page-seo
 */

// ========================================
// robots.txt 配置
// ========================================
// 挂载到WordPress的robots_txt过滤器
add_filter('robots_txt', function ($output, $public) {
    if ('0' == $public) {
        return "User-agent: *\nDisallow: /\n";
    } else {
        if (!empty(my_option('seo_robots', getrobots(), 'seo_robots_fieldset'))) {
            $output = esc_attr(strip_tags(my_option('seo_robots', getrobots(), 'seo_robots_fieldset')));
        }
        return $output;
    }
}, 10, 2);