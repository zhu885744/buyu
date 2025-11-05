<?php if ($this->is('index')) : ?> 
<!-- 首页文章列表 -->
<main id="main" class="transition-all">
  <div class="container">
    <div class="row">
      <div class="col-mb-12 col-12">
        <div class="buyu-cards gap-md">
          <div class="buyu-card p-md border rounded shadow-sm">
            <form id="search" method="post" action="<?php $this->options->siteUrl(); ?>" role="search" class="transition-all">
              <input type="text" id="s" name="s" class="text p-sm border rounded" placeholder="<?php _e('输入关键字搜索'); ?>" />
            </form>
          </div>
        </div>
        <div class="buyu-cards gap-md mt-md">
          <?php
          $sticky = $this->options->stickyPosts; // 置顶文章ID，多个用|分隔
          if($sticky){
              $sticky_cids = explode('|',$sticky);
              $sticky_html = '<span style="color: rgb(255 0 0);">[置顶]</span> ';
              $db = Typecho_Db::get();
              $select1 = $this->select()->where('type = ?', 'post');
              $select2 = $this->select()->where('type = ? && status = ? && created < ?', 'post','publish',time());
              
              $this->row = [];
              $this->stack = [];
              $this->length = 0;
              
              $order = '';
              foreach($sticky_cids as $i => $cid) {
                  if($i == 0) {
                      $select1->where('cid = ?', $cid);
                  } else {
                      $select1->orWhere('cid = ?', $cid);
                  }
                  $order .= " when $cid then $i";
                  $select2->where('table.contents.cid != ?', $cid);
              }
              
              if ($order) {
                  $select1->order('', "(case cid$order end)");
              }
              
              if ($this->_currentPage == 1) {
                  foreach($db->fetchAll($select1) as $sticky_post){
                      $sticky_post['sticky'] = $sticky_html;
                      $this->push($sticky_post);
                  }
              }
              
              $uid = $this->user->uid;
              if($uid) {
                  $select2->orWhere('authorId = ? && status = ?', $uid, 'private');
              }
              
              $sticky_posts = $db->fetchAll($select2->order('table.contents.created', Typecho_Db::SORT_DESC)->page($this->_currentPage, $this->parameter->pageSize));
              foreach($sticky_posts as $sticky_post) {
                  $this->push($sticky_post);
              }
              
              $this->setTotal($this->getTotal() - count($sticky_cids));
          }
          ?>
          
          <?php while ($this->next()): ?>
            <article class="buyu-card p-md border rounded shadow-sm hover:shadow-hover transition-shadow">
              <h2 class="post-title font-bold mb-md" itemprop="name headline">
                <a itemprop="url" href="<?php $this->permalink() ?>" class="text-link transition-color hover:text-link-hover">
                  <?php if(isset($this->sticky)) echo $this->sticky; $this->title()?>
                </a>
              </h2>
              <ul class="post-meta mb-md text-muted">
                <li><i class="fa fa-calendar mr-1"></i>&nbsp;<time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php echo time_ago($this->date ?? new \Typecho\Date(time())); ?></time></li>
                <?php if ($this->options->JCommentStatus !== "off"): ?>
                  <li><i class="fa fa-commenting-o mr-1"></i>&nbsp;<?php $this->commentsNum('无评论', '1 条评论', '%d 条评论'); ?></li>
                <?php endif; ?>
                <li><i class="fa fa-eye mr-1"></i>&nbsp;<?php get_post_view($this) ?>次阅读</li>
              </ul>
              <p class="card-text"><?php echo customExcerpt($this->content);?></p>
            </article>
          <?php endwhile; ?>
        </div>
        <!-- 分页控件 -->
        <div class="pagination-container flex justify-center items-center gap-md mt-lg">
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
<main id="main" class="transition-all">
  <div class="container">
    <div class="row">
      <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.APlayer.css?v=1.3.1'); ?>">
      <script type="text/javascript" src="<?php echo get_theme_url('assets/js/buyu.APlayer.js?v=1.3.1'); ?>"></script>
      <script type="text/javascript" src="<?php echo get_theme_url('assets/js/buyu.DPlayer.js?v=1.3.1'); ?>"></script>
      <div class="col-mb-12 col-12">
        <div class="buyu-cards gap-md mt-md">
          <article class="buyu-card p-lg border rounded shadow-sm">
            <h2 class="post-title font-bold mb-md" itemprop="name headline">
              <?php $this->title() ?>
            </h2>
            <ul class="post-meta gap-sm mb-lg text-muted">
              <li><i class="fa fa-calendar mr-1"></i>&nbsp;<time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php echo time_ago($this->date ?? new \Typecho\Date(time())); ?></time></li>
              <?php if ($this->options->JCommentStatus !== "off"): ?>
                <li><i class="fa fa-commenting-o mr-1"></i>&nbsp;<?php $this->commentsNum('无评论', '1 条评论', '%d 条评论'); ?></li>
              <?php endif; ?>
              <li><i class="fa fa-eye mr-1"></i>&nbsp;<?php get_post_view($this) ?>次阅读</li>
            </ul>
            <div class="post-content" itemprop="articleBody">
              <!--判断文章是否被密码保护-->
              <?php if($this->hidden): ?>
              <!--如果被密码保护，则输出密码输入表单-->
              <form class="post-pwp p-md border rounded bg-neutral-50" action="<?php echo Typecho_Widget::widget('Widget_Security')->getTokenUrl($this->permalink); ?>" method="post">
                <label class="block mb-sm text-muted">文章已被加密，请输入密码后查看</label>
                <div class="post-form">
                  <div class="post-input flex gap-sm">
                    <input type="password" class="text flex-1 p-sm border rounded" name="protectPassword" placeholder="请输入密码" aria-label="请输入密码">
                    <input type="hidden" name="protectCID" value="<?php $this->cid(); ?>" />
                    <div class="input-group-append">
                      <button class="btn btn-primary p-sm bg-primary text-white rounded transition hover:bg-primary-hover" type="submit">提交</button>
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
            <div class="post-button flex justify-center gap-sm mt-lg">
              <?php if ($this->options->like!== "off") :?>
                <button class="post-bth p-sm border rounded transition hover:bg-neutral-100" id="like" data-cid="<?php $this->cid(); ?>" data-like-url="<?php echo Helper::options()->index; ?>?action=like" data-get-like-url="<?php echo Helper::options()->index; ?>?action=get_like">
                  <i class="fa fa-thumbs-up mr-1"></i>&nbsp;点赞 <span id="like-count">0</span>
                </button>
              <?php endif; ?>
              <?php if ($this->options->tip!== "off") :?>
                <button class="post-bth p-sm border rounded transition hover:bg-neutral-100" onclick="showReward()">
                  <i class="fa fa-heart"></i>&nbsp;打赏
                </button>
                <?php $this->need('public/Modal.php'); ?>
              <?php endif; ?>
              <button class="post-bth p-sm border rounded transition hover:bg-neutral-100" id="share" data-title="<?php echo htmlspecialchars($this->title); ?>" data-url="<?php echo htmlspecialchars($this->permalink); ?>">
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