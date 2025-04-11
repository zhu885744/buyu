<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

        </div>
    </div>
</div>

<footer id="footer" role="contentinfo">
  <div class="item">
    <?php $this->options->JFooter_Left() ?>
  </div>   
  <?php if ($this->options->ICPbeian): ?>
    <div class="item">
      <a href="http://beian.miit.gov.cn" class="icpnum" target="_blank" rel="noreferrer"><?php echo $this->options->ICPbeian(); ?></a>
    </div>    
  <?php endif; ?>
  <?php if ($this->options->gonganbeian): ?>
    <div class="item">
      <a href="https://beian.mps.gov.cn/#/query/webSearch" class="icpnum" target="_blank" rel="noreferrer"><?php echo $this->options->gonganbeian(); ?></a>
    </div>
  <?php endif; ?> 
  <div class="item">
    &copy; <?php echo date('Y'); ?><a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>由<a href="https://typecho.org">Typecho</a>强力驱动
  </div>
</footer>
<button id="backToTop">返回顶部</button>
<script src="<?php $this->options->themeUrl('assets/js/buyu.style.js'); ?>"></script>
<script type="text/javascript">
  <?php $this->options->JCustomScript() ?>
</script>
<?php $this->footer(); ?>
<?php if ($this->options->CustomContent): $this->options->CustomContent(); ?>
<?php endif; ?>
</body>
</html>
