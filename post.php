<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('public/header.php'); ?>
<link href="<?php $this->options->themeUrl('css/buyu.Lightbox.css'); ?>" rel="stylesheet">
<div class="col-mb-12 col-8" id="main" role="main">
    <article class="post">
        <h1 class="post-title" itemprop="name headline" style="color: #3354AA;">
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
        <p itemprop="keywords" class="tags"><?php _e('标签: '); ?><?php $this->tags(', ', true, 'none'); ?></p>
    </article>
    
    <?php $this->need('public/comments.php'); ?>

    <ul class="post-near">
        <li>上一篇: <?php $this->thePrev('%s', '没有了'); ?></li>
        <li>下一篇: <?php $this->theNext('%s', '没有了'); ?></li>
    </ul>
</div>
<script src="<?php $this->options->themeUrl('js/buyu.Lightbox.js'); ?>"></script>
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function() {
    // 图片灯箱初始化
    Fancybox.bind('[data-fancybox]', {
      // 可以在这里添加自定义选项
    });
  });
</script>
<?php $this->need('public/footer.php'); ?>
