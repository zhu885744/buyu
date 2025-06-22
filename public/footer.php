<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

        </div>
    </div>
</div>

<footer id="footer" role="contentinfo">
  <?php if ($this->options->JFooter_Left()): ?>
    <div class="item">
      <?php echo $this->options->JFooter_Left(); ?>
    </div>   
  <?php endif; ?>  
  <?php if ($this->options->ICPbeian): ?>
    <div class="item">
      <a href="http://beian.miit.gov.cn" class="icpnum" target="_blank" rel="noopener noreferrer">
        <?php echo htmlspecialchars($this->options->ICPbeian(), ENT_QUOTES, 'UTF-8'); ?>
      </a>
    </div>    
  <?php endif; ?>
  <?php if ($this->options->gonganbeian): ?>
    <div class="item">
      <a href="https://beian.mps.gov.cn/#/query/webSearch" class="icpnum" target="_blank" rel="noopener noreferrer">
        <?php echo htmlspecialchars($this->options->gonganbeian(), ENT_QUOTES, 'UTF-8'); ?>
      </a>
    </div>
  <?php endif; ?> 
  <!-- 下方版权建议保留，谢谢 -->
  <div class="item">
    Copyright&nbsp;&copy; <?php echo date('Y'); ?>&nbsp;
    <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>&nbsp;版权所有
  </div>
  <div class="item">
    Powered by&nbsp;<a href="https://typecho.org/" target="_blank" rel="noopener noreferrer">typecho</a>&nbsp;|&nbsp;Theme is&nbsp;<a href="https://github.com/zhu885744/buyu" target="_blank" rel="noopener noreferrer">buyu</a>
  </div>
</footer>
<i class="fa fa-arrow-up" id="backToTop"></i>
<?php
$cdnUrl = $this->options->JAssetsURL;
$getThemeUrl = function($path) use ($cdnUrl) {
    if (!empty($cdnUrl)) {
        return rtrim($cdnUrl, '/') . '/' . ltrim($path, '/');
    }
    return Typecho_Common::url($path, $this->options->themeUrl);
};
?>
<script src="<?php echo $getThemeUrl('assets/js/buyu.style.js'); ?>"></script>
<script src="<?php echo $getThemeUrl('assets/js/buyu.fancybox.js'); ?>"></script>
<script type="text/javascript">
  //自定义js
  <?php echo $this->options->JCustomScript(); ?>
</script>
<?php $this->footer(); ?>
<?php if ($this->options->CustomContent): ?>
  <?php echo $this->options->CustomContent(); ?>
<?php endif; ?>
</body>
</html>