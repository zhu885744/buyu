<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<footer id="footer" class="footer mt-md" role="contentinfo" aria-label="网站底部信息">
  <div class="footer__content">
    <!-- 自定义底部内容 -->
    <?php if ($this->options->JFooter_Left && $this->options->JFooter_Left()): ?>
      <div class="footer__item" aria-label="自定义底部内容">
        <?php echo $this->options->JFooter_Left(); ?>
      </div>
    <?php endif; ?>
    <!-- 主题设置自定义icp备案号 -->
    <?php if ($this->options->ICPbeian): ?>
      <div class="footer__item">
        <a href="http://beian.miit.gov.cn" class="icpnum" target="_blank" rel="noopener noreferrer">
          <?php echo htmlspecialchars($this->options->ICPbeian(), ENT_QUOTES, 'UTF-8'); ?>
        </a>
      </div>    
    <?php endif; ?>
    <!-- 主题设置自定义公安联网备案号 -->
    <?php if ($this->options->gonganbeian): ?>
      <div class="footer__item">
        <a href="https://beian.mps.gov.cn/#/query/webSearch" class="icpnum" target="_blank" rel="noopener noreferrer">
          <?php echo htmlspecialchars($this->options->gonganbeian(), ENT_QUOTES, 'UTF-8'); ?>
        </a>
      </div>
    <?php endif; ?> 
    <!-- 网站版权信息 -->
    <div class="footer__item" aria-label="网站版权">
      <span>Copyright&nbsp;&copy; <?php echo date('Y'); ?></span>
      <a href="<?php $this->options->siteUrl(); ?>" class="footer__site-link"title="<?php $this->options->title(); ?> 首页">
        <?php $this->options->title(); ?>
      </a>
      <span>版权所有</span>
    </div>
    <div class="footer__item" aria-label="技术支持">
      <span>Powered by </span>
      <a href="https://typecho.org/" target="_blank" rel="noopener noreferrer"class="footer__tech-link"title="Typecho 官方网站">
        Typecho
      </a>
      <span> | Theme by </span>
      <a href="https://github.com/zhu885744/buyu" target="_blank" rel="noopener noreferrer"class="footer__tech-link" title="buyu 主题 GitHub 仓库">
        buyu
      </a>
    </div>
  </div>
</footer>

<script type="text/javascript" src="<?php echo get_theme_url('assets/js/buyu.style.js?v=1.3.2'); ?>"defer></script>
<script type="text/javascript" src="<?php echo get_theme_url('assets/js/buyu.fancybox.js?v=1.3.2'); ?>"defer></script>
<script type="text/javascript" defer>
  <?php echo $this->options->JCustomScript(); ?>
</script>
<?php $this->footer(); ?>
<?php echo $this->options->CustomContent(); ?>
</body>
</html>