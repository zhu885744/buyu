<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('public/header.php'); ?>
  <div class="col-mb-12 col-12 error-page-404" id="main" role="main">
    <div class="error-page">
      <h2 class="post-title">404 - <?php _e('页面没找到'); ?></h2>
      <p><?php _e('你想查看的页面已被隐藏或删除了, 要不要搜索看看？ '); ?></p>
      <form id="search" method="post" action="<?php $this->options->siteUrl();?>" role="search">
        <label for="s" class="sr-only"><?php _e('搜索关键字');?></label>
        <input type="text" id="s" name="s" class="text" placeholder="<?php _e('输入关键字搜索');?>"/>
      </form>
    </div>
  </div>
<?php $this->need('public/footer.php'); ?>