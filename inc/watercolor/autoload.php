<?php
/**
 * WP-WaterMark
 * Description: 支持文字水印和图片水印，支持自定义
 * Version: 1.0.0
 * Requires at least: 5.6 +
 * Requires PHP: 7.0 +
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}


define('WM_DATA_DIR', get_stylesheet_directory() . '/inc/watercolor'); // 文件地址--物理地址 c://wp-content/...

define('WM_DATA_URI', get_stylesheet_directory_uri() . '/inc/watercolor'); //URL地址 http://...


// 核心文件
require_once WM_DATA_DIR . '/WP-Watermark.php';

