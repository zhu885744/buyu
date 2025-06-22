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
    
    <div class="pagination-container">
      <?php $this->pageLink('上一页'); ?>
      <?php $this->pageLink('下一页','next'); ?>
    </div>
  </div>
<?php endif; ?>

<?php if ($this->is('page') || $this->is('post')) : ?>
  <link href="<?php echo get_theme_url('assets/css/buyu.APlayer.css'); ?>" rel="stylesheet" >
  <script src="<?php echo get_theme_url('assets/js/buyu.APlayer.js'); ?>"></script>
  <script src="<?php echo get_theme_url('assets/js/buyu.DPlayer.js'); ?>"></script>
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
            <button class="post-bth" id="like" data-cid="<?php $this->cid(); ?>" data-like-url="<?php echo Helper::options()->index; ?>?action=like" data-get-like-url="<?php echo Helper::options()->index; ?>?action=get_like">
              <i class="fa fa-thumbs-up mr-1"></i>&nbsp;点赞 <span id="like-count">0</span>
            </button>
          <?php endif; ?>
          <button class="post-bth" id="share" data-title="<?php echo htmlspecialchars($this->title); ?>" data-url="<?php echo htmlspecialchars($this->permalink); ?>">
           <i class="fa fa-share-alt mr-1"></i>&nbsp;分享
          </button>
        </div>
    </article>
    <?php $this->need('public/comments.php'); ?>
  </div>
<?php endif; ?>