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
            <p class="card-text"><?php $this->excerpt(200, '...'); ?></p>
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
  <link href="<?php $this->options->themeUrl('assets/css/buyu.APlayer.css'); ?>" rel="stylesheet" >
  <script src="<?php $this->options->themeUrl('assets/js/buyu.APlayer.js'); ?>"></script>
  <script src="<?php $this->options->themeUrl('assets/js/buyu.DPlayer.js'); ?>"></script>
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
          <!--判断文章是否被密码保护-->
          <?php if($this->hidden): ?>
          <!--如果被密码保护，则输出密码输入表单-->
          <form class="post-pwp" action="<?php echo Typecho_Widget::widget('Widget_Security')->getTokenUrl($this->permalink); ?>" method="post">
            <label>文章已被加密，请输入密码后查看</label>
            <div class="post-form">
              <div class="post-input">
                <input type="password" class="text" name="protectPassword" class="form-control" placeholder="请输入密码" aria-label="请输入密码">
                <input type="hidden" name="protectCID" value="<?php $this->cid(); ?>" />
                <div class="input-group-append">
                  <button class="btn btn-primary" type="submit">提交</button>
                </div>
              </div>
            </div>
          </form>
          <?php else: ?>
          <!--如果未设置密码，则直接输出文章内容-->
          <?php echo processContent($this->content, $this->title); ?>
          <?php endif;?>
        </div>
        <?php if ($this->is('post')) : ?>
          <p itemprop="keywords" class="tags"><?php _e('标签: '); ?><?php $this->tags(', ', true, 'none'); ?></p>
        <?php endif; ?>
        <div class="post-button" style="text-align: center;">
          <?php if ($this->options->like!== "off") :?>
            <button type="button" id="like" class="post-bth">
              <i class="fa fa-thumbs-up mr-1"></i> 点赞 <span id="like-count">1</span>
            </button>
          <?php endif;?>
          <?php if ($this->options->Reward): ?>
            <button type="button" id="Reward" class="post-bth">
              <i class="fa fa-heart mr-1"></i> 打赏
            </button>
            <!----<?php echo $this->options->Reward(); ?>---->
          <?php endif;?>
          <button type="button" id="share" class="post-bth">
            <i class="fa fa-share-alt mr-1"></i> 分享
          </button>
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
<?php endif; ?>