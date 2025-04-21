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

  <div class="item">
    &copy; <?php echo date('Y'); ?>&nbsp;
    <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>&nbsp;由&nbsp;
    <a href="https://typecho.org" target="_blank" rel="noopener noreferrer">Typecho</a>&nbsp;强力驱动
  </div>
</footer>

<button id="backToTop">返回顶部</button>
<script src="<?php $this->options->themeUrl('assets/js/buyu.style.js'); ?>"></script>
<?php if ($this->options->JCustomScript()): ?>
  <script type="text/javascript">
    <?php echo $this->options->JCustomScript(); ?>
  </script>
<?php endif; ?>

<?php $this->footer(); ?>

<?php if ($this->options->CustomContent): ?>
  <?php echo $this->options->CustomContent(); ?>
<?php endif; ?>
</body>
</html>