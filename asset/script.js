/**
 * 美图库主题脚本 - 单文章图集版
 * 
 * 功能：
 * 1. 灯箱预览（支持广告图片）
 * 2. 上/下一页导航
 * 3. 自动播放
 * 4. 广告点击跳转
 */

jQuery(function ($) {

    // ========================================
    // 1. 初始化变量
    // ========================================
    
    // 获取所有瀑布流项目（包括广告）
    var $waterfallItems = $('.waterfall-item');
    var totalItems = $waterfallItems.length;  // 总数（包括广告）
    var currentIndex = 0;
    var autoplayTimer = null;
    var isPlaying = false;
    
    // 广告标记
    var AD_ITEM_CLASS = 'waterfall-ad';
    var AD_DATA_ATTR = 'data-is-ad';

    // ========================================
    // 2. 灯箱功能
    // ========================================

    /**
     * 打开灯箱
     * @param {number} index - 当前点击图片的索引
     */
    function openLightbox(index) {
        currentIndex = index;
        showImage(index);
        $('#lightbox').addClass('active');
        $('body').css('overflow', 'hidden');
    }

    /**
     * 关闭灯箱
     */
    function closeLightbox() {
        $('#lightbox').removeClass('active');
        $('body').css('overflow', '');
        stopAutoplay();
    }

    /**
     * 显示指定索引的图片
     * @param {number} index - 图片索引
     */
    function showImage(index) {
        var $img = $('#lightboxImg');
        var $loading = $('#lightboxLoading');
        var $item = $waterfallItems.eq(index);

        // 显示加载中
        $loading.show();
        $img.css('opacity', 0);

        // 获取大图 URL
        var fullUrl = $item.data('full');

        // 预加载大图
        var tempImg = new Image();
        tempImg.onload = function () {
            $img.attr('src', fullUrl);
            $loading.hide();
            $img.animate({ opacity: 1 }, 200);
        };
        tempImg.onerror = function () {
            // 加载失败时使用缩略图
            $img.attr('src', $item.find('img').attr('src'));
            $loading.hide();
            $img.animate({ opacity: 1 }, 200);
        };
        tempImg.src = fullUrl;

        // 更新页码信息
        updatePageInfo(index);
    }

    /**
     * 更新页码信息
     * @param {number} index - 当前索引
     */
    function updatePageInfo(index) {
        // 计算实际图片序号（排除广告）
        var actualIndex = 0;
        var totalImages = 0;
        
        $waterfallItems.each(function(i) {
            var isAd = $(this).hasClass('waterfall-ad');
            if (!isAd) {
                totalImages++;
                if (i <= index) {
                    actualIndex++;
                }
            }
        });

        // 显示页码（从 1 开始）
        $('#pageInfo').text('Image ' + actualIndex + ' / ' + totalImages);
    }

    /**
     * 上一张图片
     */
    function prevImage() {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems;
        showImage(currentIndex);
    }

    /**
     * 下一张图片
     */
    function nextImage() {
        currentIndex = (currentIndex + 1) % totalItems;
        showImage(currentIndex);
    }

    /**
     * 切换自动播放
     */
    function toggleAutoplay() {
        if (isPlaying) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    }

    /**
     * 开始自动播放
     */
    function startAutoplay() {
        isPlaying = true;
        $('#btnAutoplay').addClass('playing');
        $('#playIcon').hide();
        $('#pauseIcon').show();
        autoplayTimer = setInterval(function () {
            nextImage();
        }, 3000);
    }

    /**
     * 停止自动播放
     */
    function stopAutoplay() {
        isPlaying = false;
        $('#btnAutoplay').removeClass('playing');
        $('#playIcon').show();
        $('#pauseIcon').hide();
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    }

    // ========================================
    // 3. 事件绑定
    // ========================================

    // 点击瀑布流项目打开灯箱
    $waterfallItems.on('click', function (e) {
        var $item = $(this);
        var isAd = $item.hasClass('waterfall-ad');
        
        // 如果是广告，直接跳转链接（不打开灯箱）
        if (isAd) {
            var adUrl = $item.data('ad-url');
            if (adUrl) {
                window.open(adUrl, '_blank', 'noopener,noreferrer');
            }
            e.preventDefault();
            return;
        }
        
        // 普通图片，打开灯箱
        var index = $waterfallItems.index($item);
        openLightbox(index);
    });

    // ========================================
    // 4. 布局切换（移动端单栏/双栏切换）
    // ========================================
    
    var isSingleCol = false;
    var $waterfall = $('#waterfall');
    var $layoutToggle = $('#layoutToggle');
    
    $layoutToggle.on('click', function () {
        isSingleCol = !isSingleCol;
        
        if (isSingleCol) {
            // 切换到单栏模式
            $waterfall.addClass('single-col');
            $(this).find('.toggle-label').text('双栏模式');
            $(this).find('.toggle-icon').html(
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
                '<rect x="3" y="3" width="7" height="18" rx="1"/><rect x="14" y="3" width="7" height="18" rx="1"/></svg>'
            );
        } else {
            // 切换到双栏模式
            $waterfall.removeClass('single-col');
            $(this).find('.toggle-label').text('单栏模式');
            $(this).find('.toggle-icon').html(
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
                '<rect x="5" y="3" width="14" height="18" rx="1"/></svg>'
            );
        }
    });

    // ========================================
    // 5. 灯箱事件绑定
    // ========================================

    // 关闭灯箱
    $('#lightbox, #btnClose').on('click', function (e) {
        if (e.target === this || e.target.id === 'btnClose' || $(e.target).closest('#btnClose').length) {
            closeLightbox();
        }
    });

    // 上/下一页按钮
    $('#btnPrev, #hoverLeft').on('click', function (e) {
        e.stopPropagation();
        prevImage();
    });

    $('#btnNext, #hoverRight').on('click', function (e) {
        e.stopPropagation();
        nextImage();
    });

    // 自动播放按钮
    $('#btnAutoplay').on('click', function (e) {
        e.stopPropagation();
        toggleAutoplay();
    });

    // 键盘导航
    $(document).on('keydown', function (e) {
        if (!$('#lightbox').hasClass('active')) return;

        switch (e.key) {
            case 'ArrowLeft':
                prevImage();
                break;
            case 'ArrowRight':
                nextImage();
                break;
            case 'Escape':
                closeLightbox();
                break;
        }
    });

    // ========================================
    // 6. 回到顶部按钮
    // ========================================

    var $backToTop = $('#backToTop');

    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 300) {
            $backToTop.addClass('visible');
        } else {
            $backToTop.removeClass('visible');
        }
    });

    $backToTop.on('click', function () {
        $('html, body').animate({ scrollTop: 0 }, 400);
    });

});
