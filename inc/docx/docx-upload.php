<?php
/**
 * 文档转换器--文档上传
 * @author Jim King <hongyexs@gmail.com>
 * @license MIT License
 * @version 2025.09.20
 */


/**
 * 在编辑器底部生成一个 '文档上传'按钮
 * 上传文档,提取文档中的语义信息生成简洁的 HTML
 * 将多图片打包放进文档,在一次性上传进去
 */
function mammoth_add_post_meta_box() {
    $post_types = get_post_types();

    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'editor')) {
            add_meta_box(
                'mammoth_add_post',
                'DOCX文档转换器',
                'mammoth_render_editor_box',
                $post_type
            );
        }
    }
}

// 修改CSS加载路径为主题路径
function mammoth_admin_style($hook) {
    if (in_array($hook, ['post.php', 'post-new.php'])) {
        wp_enqueue_style(
            'mammoth-style',
            get_template_directory_uri() . '/inc/docx/mammoth.css',
            [],
            '1.3.0'
        );
    }
}

// 修改JS加载路径并添加依赖关系
function mammoth_load_javascript() {
    $js_dir = get_template_directory_uri() . '/inc/docx/';
    
    // 加载主脚本
    wp_enqueue_script(
        'mammoth-editor',
        $js_dir . 'mammoth-editor.js?v=1.21.0',
        ['jquery', 'wp-util'],
        '1.21.0',
        true
    );
    
    // 加载标签页脚本
    wp_enqueue_script(
        'mammoth-tabs',
        $js_dir . 'tabs.js?v=1.21.0',
        ['jquery'],
        '1.21.0',
        true
    );
}

// 修改元框渲染函数
function mammoth_render_editor_box( $post ) {
    ?>
    <div id="mammoth-docx-uploader" class="status-empty">
        <div>
            <label>
                Select docx file:
                <input type="file" id="mammoth-docx-upload" />
            </label>
        </div>

        <div id="mammoth-docx-loading">
            Loading...
        </div>

        <div id="mammoth-docx-inserting">
            Inserting...
        </div>

        <p class="mammoth-docx-error">
            Error while attempting to convert file:
            <span id="mammoth-docx-error-message"></span>
        </p>

        <div class="mammoth-docx-preview">
            <input type="hidden"
                id="mammoth-docx-upload-image-nonce"
                value="<?php echo wp_create_nonce( "media-form" ); ?>"
                />
            <input type="hidden"
                id="mammoth-docx-upload-image-href"
                value="<?php echo get_site_url( null, "wp-admin/async-upload.php", "admin" ); ?>"
                />
            <input type="hidden"
                id="mammoth-docx-admin-ajax-href"
                value="<?php echo get_site_url( null, "wp-admin/admin-ajax.php", "admin" ); ?>"
                />

            <p><input type="button" id="mammoth-docx-insert" value="Insert into editor" /></p>
            <div class="mammoth-tabs">
                <div class="tab">
                    <h4>Visual</h4>
                    <iframe
                        id="mammoth-docx-visual-preview"
                        src="about:blank"
                        data-stylesheets="<?php echo mammoth_editor_stylesheets_list(); ?>">
                    </iframe>
                </div>
                <div class="tab">
                    <h4>Raw HTML</h4>
                    <pre id="mammoth-docx-raw-preview">
                    </pre>
                </div>
                <div class="tab">
                    <h4>Messages</h4>
                    <div id="mammoth-docx-messages">
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php
}

// 添加样式表获取函数
function mammoth_editor_stylesheets_list() {
    $styles = get_editor_stylesheets();
    return !empty($styles) ? implode(',', $styles) : '';
}

// 添加必要的AJAX处理函数
add_action('wp_ajax_mammoth_docx_convert', 'handle_docx_conversion');
function handle_docx_conversion() {
    if (!wp_verify_nonce($_REQUEST['_nonce'], 'media-form')) {
        wp_send_json_error('无效请求', 403);
    }

    try {
        // 实际开发中应包含DOCX解析逻辑
        // 示例返回模拟内容
        wp_send_json_success([
            'html' => '<p>模拟的DOCX转换内容</p>',
            'messages' => ['转换成功']
        ]);
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage(), 500);
    }
}

// 替换所有钩子为标准主题集成方式
add_action('add_meta_boxes', 'mammoth_add_post_meta_box');
// add_action('admin_footer', 'mammoth_load_javascript');
add_action('admin_head', 'mammoth_load_javascript');
add_action('admin_enqueue_scripts', 'mammoth_admin_style', 10, 1);
