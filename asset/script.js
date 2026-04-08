$(function () {

    // ===== 从 DOM 中获取图片数据 =====
    var images = [];
    $('.waterfall-item').each(function () {
        var $item = $(this);
        var index = parseInt($item.data('index'));
        var thumbnail = $item.find('img').attr('src');
        var full = $item.data('full') || thumbnail;
        
        images[index] = {
            thumbnail: thumbnail,
            full: full
        };
    });

    var totalImages = images.length;
    var currentIndex = 0;
    var autoplayTimer = null;
    var isPlaying = false;

    // ===== 灯箱功能 =====
    function openLightbox(index) {
        currentIndex = index;
        showImage(index);
        $('#lightbox').addClass('active');
        $('body').css('overflow', 'hidden');
    }

    function closeLightbox() {
        $('#lightbox').removeClass('active');
        $('body').css('overflow', '');
        stopAutoplay();
    }

    function showImage(index) {
        var $img = $('#lightboxImg');
        var $loading = $('#lightboxLoading');

        $loading.show();
        $img.css('opacity', 0);

        var bigSrc = images[index].full;
        var tempImg = new Image();
        tempImg.onload = function () {
            $img.attr('src', bigSrc);
            $loading.hide();
            $img.animate({ opacity: 1 }, 200);
        };
        tempImg.onerror = function () {
            $img.attr('src', images[index].thumbnail);
            $loading.hide();
            $img.animate({ opacity: 1 }, 200);
        };
        tempImg.src = bigSrc;

        updatePageInfo();
    }

    function updatePageInfo() {
        $('#pageInfo').text('图片 ' + (currentIndex + 1) + ' / 共 ' + totalImages + ' 张');
    }

    function prevImage() {
        currentIndex = (currentIndex - 1 + totalImages) % totalImages;
        showImage(currentIndex);
    }

    function nextImage() {
        currentIndex = (currentIndex + 1) % totalImages;
        showImage(currentIndex);
    }

    function toggleAutoplay() {
        if (isPlaying) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    }

    function startAutoplay() {
        isPlaying = true;
        $('#btnAutoplay').addClass('playing');
        $('#playIcon').hide();
        $('#pauseIcon').show();
        autoplayTimer = setInterval(function () {
            nextImage();
        }, 3000);
    }

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

    function toggleFullscreen() {
        var el = document.documentElement;
        if (!document.fullscreenElement && !document.webkitFullscreenElement) {
            if (el.requestFullscreen) {
                el.requestFullscreen();
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }
    }

    // ===== 返回顶部按钮 =====
    var $backToTop = $('#backToTop');
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 400) {
            $backToTop.addClass('visible');
        } else {
            $backToTop.removeClass('visible');
        }
    });
    $backToTop.on('click', function () {
        $('html, body').animate({ scrollTop: 0 }, 400);
    });

    // ===== 事件绑定 =====

    // 点击图片打开灯箱
    $(document).on('click', '.waterfall-item', function () {
        var index = parseInt($(this).data('index'));
        openLightbox(index);
    });

    // 关闭按钮
    $('#btnClose').on('click', function (e) {
        e.stopPropagation();
        closeLightbox();
    });

    // 点击深色背景关闭灯箱（点击卡片区域不关闭）
    $('#lightbox').on('click', function (e) {
        if (e.target === this) {
            closeLightbox();
        }
    });

    // 工具栏控制按钮
    $('#btnPrev').on('click', function (e) { e.stopPropagation(); prevImage(); });
    $('#btnNext').on('click', function (e) { e.stopPropagation(); nextImage(); });
    $('#btnAutoplay').on('click', function (e) { e.stopPropagation(); toggleAutoplay(); });
    $('#btnFullscreen').on('click', function (e) { e.stopPropagation(); toggleFullscreen(); });

    // 图片内部悬浮箭头
    $('#hoverLeft').on('click', function (e) { e.stopPropagation(); prevImage(); });
    $('#hoverRight').on('click', function (e) { e.stopPropagation(); nextImage(); });

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
            case ' ':
                e.preventDefault();
                toggleAutoplay();
                break;
            case 'f':
                toggleFullscreen();
                break;
        }
    });

    // ===== 布局切换（移动端单栏/双栏切换） =====
    var isSingleCol = false;
    $('#layoutToggle').on('click', function () {
        isSingleCol = !isSingleCol;
        var $waterfall = $('#waterfall');
        if (isSingleCol) {
            $waterfall.addClass('single-col');
            $(this).find('.toggle-label').text('双栏模式');
            $(this).find('.toggle-icon').html(
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
                '<rect x="3" y="3" width="7" height="18" rx="1"/><rect x="14" y="3" width="7" height="18" rx="1"/></svg>'
            );
        } else {
            $waterfall.removeClass('single-col');
            $(this).find('.toggle-label').text('单栏模式');
            $(this).find('.toggle-icon').html(
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
                '<rect x="5" y="3" width="14" height="18" rx="1"/></svg>'
            );
        }
    });

});
