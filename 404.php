<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('public/header.php'); ?>
<main id="main">
  <div class="container">
    <div class="row">  
      <div class="col-mb-12 col-12 error-page-404">
        <div class="buyu-cards">
          <article class="buyu-card">
            <div class="error-page">
              <h2 class="post-title">404 - <?php _e('页面没找到'); ?></h2>
              <p><?php _e('你想查看的页面已被隐藏或删除了, 要不要搜索看看？ '); ?></p>
              <p>
                <form id="search" method="post" action="<?php $this->options->siteUrl();?>" role="search">
                  <label for="s" class="sr-only"><?php _e('搜索关键字');?></label>
                  <input type="text" id="s" name="s" class="text" placeholder="<?php _e('输入关键字搜索');?>" autocomplete="off"/>
                </form>
              </p>
              <p><a class="post-bth" href="<?php $this->options->siteUrl();?>"><?php _e('返回首页'); ?></a></p>
            </div>
          </article>
        </div>
      </div>
    </div>
  </div>
</main>  
<?php $this->need('public/footer.php'); ?>