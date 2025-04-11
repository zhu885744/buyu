<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('public/header.php'); ?>
<link href="<?php $this->options->themeUrl('assets/css/buyu.Lightbox.css'); ?>" rel="stylesheet">
<div class="col-mb-12 col-8" id="main" role="main">
    <article class="post">
        <h1 class="post-title" itemprop="name headline">
            <?php $this->title() ?>
        </h1>
        <ul class="post-meta">
            <li><time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time></li>
            <li><?php $this->commentsNum('无评论', '1 条评论', '%d 条评论'); ?></li>
            <li><?php get_post_view($this) ?>次阅读</li>
        </ul>
        <div class="post-content" itemprop="articleBody">
          <?php
            $pattern = '/\<img.*?src\=\"(.*?)\"[^>]*>/i';
            $replacement = '<a href="$1" class="index-img" data-fancybox /><img data-src="$1" alt="'.$this->title.'" title="点击查看大图"></a>';
            $content = preg_replace($pattern, $replacement, $this->content);
            echo $content;
          ?>
        </div>
    </article>
    <?php $this->need('public/comments.php'); ?>
</div>
<script src="<?php $this->options->themeUrl('assets/js/buyu.Lightbox.js'); ?>"></script>
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function() {
    // 图片灯箱初始化
    Fancybox.bind('[data-fancybox]', {
      // 可以在这里添加自定义选项
    });
  });
</script>
<?php $this->need('public/footer.php'); ?>