<?php if ($this->is('index')) : ?>
  <div class="col-mb-12 col-8" id="main" role="main">
    <?php while ($this->next()): ?>
        <article class="post">
            <h2 class="post-title" itemprop="name headline">
                <a itemprop="url" href="<?php $this->permalink() ?>">
                  <?php $this->title() ?>
                </a>
            </h2>
            <ul class="post-meta">
                <li><time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time></li>
                <?php if ($this->options->JCommentStatus !== "off"): // 判断全局评论是否关闭 ?>
                  <li><?php $this->commentsNum('无评论', '1 条评论', '%d 条评论'); ?></li>
                <?php endif; ?>
                <li><?php get_post_view($this) ?>次阅读</li>
            </ul>
            <p class="card-text"><?php $this->excerpt(150, '...'); ?></p>
        </article>
    <?php endwhile; ?>

    <?php $this->pageNav('«', '»', 1, '···', array(
        'wrapTag' => 'div',
        'wrapClass' => 'page-navigator',
        'itemTag' => 'li',
        'textTag' => 'span',
        'currentClass' => 'current',
        'prevClass' => 'prev',
        'nextClass' => 'next',
    )); ?>
  </div>
<?php endif; ?>

<?php if ($this->is('page') || $this->is('post')) : ?>
  <link href="<?php $this->options->themeUrl('assets/css/buyu.Lightbox.css'); ?>" rel="stylesheet">
  <link href="<?php $this->options->themeUrl('assets/css/APlayer.min.css'); ?>" rel="stylesheet" >
  <script src="<?php $this->options->themeUrl('assets/js/APlayer.min.js'); ?>"></script>
  <script src="<?php $this->options->themeUrl('assets/js/DPlayer.min.js'); ?>"></script>
  <div class="col-mb-12 col-8" id="main" role="main">
    <article class="post">
        <h1 class="post-title" itemprop="name headline">
            <?php $this->title() ?>
        </h1>
        <ul class="post-meta">
            <li><time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time></li>
            <?php if ($this->options->JCommentStatus !== "off"): // 判断全局评论是否关闭 ?>
              <li><?php $this->commentsNum('无评论', '1 条评论', '%d 条评论'); ?></li>
            <?php endif; ?>
            <li><?php get_post_view($this) ?>次阅读</li>
        </ul>
        <div class="post-content" itemprop="articleBody">
          <?php echo processContent($this->content, $this->title); ?>
        </div>
        <?php if ($this->is('post')) : ?>
          <p itemprop="keywords" class="tags"><?php _e('标签: '); ?><?php $this->tags(', ', true, 'none'); ?></p>
        <?php endif; ?>
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
<?php endif; ?>