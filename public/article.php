<?php if ($this->is('index')) : ?> 
<!-- 首页文章列表 -->
<main id="pjax-container">
  <div class="container">
    <div class="row">
      <div class="col-mb-12 col-12">
        <div class="post-cards">
          <div class="post-card">
            <form id="search" method="post" action="<?php $this->options->siteUrl(); ?>" role="search">
              <input type="text" id="s" name="s" class="text" placeholder="<?php _e('输入关键字搜索'); ?>" />
            </form>
          </div>
        </div> 
        <div class="post-cards">
          <?php while ($this->next()): ?>
            <article class="post-card">
              <h2 class="post-title" itemprop="name headline">
                <a itemprop="url" href="<?php $this->permalink() ?>">
                  <?php $this->title() ?>
                </a>
              </h2>
              <ul class="post-meta">
                <li><i class="fa fa-calendar mr-1"></i>&nbsp;<time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php echo time_ago($this->date); ?></time></li>
                <?php if ($this->options->JCommentStatus !== "off"): ?>
                  <li><i class="fa fa-commenting-o mr-1"></i>&nbsp;<?php $this->commentsNum('无评论', '1 条评论', '%d 条评论'); ?></li>
                <?php endif; ?>
                <li><i class="fa fa-eye mr-1"></i>&nbsp;<?php get_post_view($this) ?>次阅读</li>
              </ul>
              <p class="card-text"><?php $this->excerpt(150, '...'); ?></p>
            </article>
          <?php endwhile; ?>
        </div>
    
        <!-- 分页控件 -->
        <div class="pagination-container">
          <?php $this->pageLink('上一页'); ?>
          <span class="page-info">
            第 <?php echo $this->getCurrentPage(); ?> 页 / 共 <?php echo $this->getTotalPage(); ?> 页
          </span>
          <?php $this->pageLink('下一页', 'next'); ?>
        </div>
      </div>
    </div>
  </div>
</main>
<?php endif; ?>

<?php if ($this->is('page') || $this->is('post')) : ?>
<main id="pjax-container">
  <div class="container">
    <div class="row">
      <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.APlayer.css?v=1.3.1'); ?>">
      <script type="text/javascript" src="<?php echo get_theme_url('assets/js/buyu.APlayer.js?v=1.3.1'); ?>"></script>
      <script type="text/javascript" src="<?php echo get_theme_url('assets/js/buyu.DPlayer.js?v=1.3.1'); ?>"></script>
      <div class="col-mb-12 col-12">
        <div class="post-cards">
          <article class="post-card">
            <h2 class="post-title" itemprop="name headline">
              <?php $this->title() ?>
            </h2>
            <ul class="post-meta">
              <li><i class="fa fa-calendar mr-1"></i>&nbsp;<time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php echo time_ago($this->date); ?></time></li>
              <?php if ($this->options->JCommentStatus !== "off"): ?>
                <li><i class="fa fa-commenting-o mr-1"></i>&nbsp;<?php $this->commentsNum('无评论', '1 条评论', '%d 条评论'); ?></li>
              <?php endif; ?>
              <li><i class="fa fa-eye mr-1"></i>&nbsp;<?php get_post_view($this) ?>次阅读</li>
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
            <?php if ($this->options->copyright!== "off") :?>
              <?php $this->need('public/copyright.php'); ?>
            <?php endif; ?>
            <div class="post-button" style="text-align: center;">
              <?php if ($this->options->like!== "off") :?>
                <button class="post-bth" id="like" data-cid="<?php $this->cid(); ?>" data-like-url="<?php echo Helper::options()->index; ?>?action=like" data-get-like-url="<?php echo Helper::options()->index; ?>?action=get_like">
                  <i class="fa fa-thumbs-up mr-1"></i>&nbsp;点赞 <span id="like-count">0</span>
                </button>
              <?php endif; ?>
              <?php if ($this->options->tip!== "off") :?>
                <button class="post-bth" onclick="showReward()">
                  <i class="fa fa-heart"></i>&nbsp;打赏
                </button>
                <?php $this->need('public/Modal.php'); ?>
              <?php endif; ?>
              <button class="post-bth" id="share" data-title="<?php echo htmlspecialchars($this->title); ?>" data-url="<?php echo htmlspecialchars($this->permalink); ?>">
               <i class="fa fa-share-alt mr-1"></i>&nbsp;分享
              </button>
            </div>
          </article>
        </div>
        <?php $this->need('public/comments.php'); ?>
      </div>
    </div>
  </div>
</main>
<?php endif; ?>