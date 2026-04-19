<!-- ========== 渐变分割线 ========== -->
<div class="gradient-divider"></div>

<!-- ========== 全屏渐变横线 ========== -->
<div class="fullwidth-gradient-line"></div>

<!-- ========== 页脚 ========== -->
<footer class="site-footer">
    <p class="footer-copyright">
        Copyright &copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?> All Rights Reserved.
    </p>
    <p class="footer-note">本站所有图片均来源于网络，仅供欣赏，如有侵权请联系删除。</p>
    
    <?php wp_footer(); ?>
</footer>

<!-- ========== 返回顶部按钮 ========== -->
<button class="back-to-top" id="backToTop" title="回到顶部">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<!-- ========== 图片灯箱 ========== -->
<div class="lightbox-overlay" id="lightbox">
    <div class="lightbox-card">
        <!-- 工具栏：图片上方的控制按钮 -->
        <div class="lightbox-toolbar">
            <button id="btnPrev" title="上一页">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <button id="btnAutoplay" title="自动播放">
                <svg id="playIcon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                <svg id="pauseIcon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="display:none;"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
            </button>
            <button id="btnNext" title="下一页">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 6 15 12 9 18"/></svg>
            </button>
            <span class="separator"></span>
            <button id="btnFullscreen" title="全屏">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/></svg>
            </button>
            <button class="btn-close" id="btnClose" title="关闭">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <!-- 三边白色边框（左、右、下） -->
        <div class="lightbox-body">
            <!-- 图片显示区域 -->
            <div class="lightbox-image-area">
                <div class="lightbox-loading" id="lightboxLoading"></div>
                <img id="lightboxImg" src="" alt="">

                <!-- 左侧悬浮箭头（图片内部） -->
                <div class="lightbox-hover-arrow left" id="hoverLeft">
                    <div class="arrow-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    </div>
                </div>
                <!-- 右侧悬浮箭头（图片内部） -->
                <div class="lightbox-hover-arrow right" id="hoverRight">
                    <div class="arrow-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 6 15 12 9 18"/></svg>
                    </div>
                </div>
            </div>

            <!-- 页脚：页码信息（白色边框内部） -->
            <div class="lightbox-footer">
                <span class="lightbox-page-info" id="pageInfo"></span>
            </div>
        </div>
    </div>
</div>

</body>
</html>
