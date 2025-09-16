<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('public/header.php'); ?>

<div class="col-mb-12 col-12" id="main" role="main">
  <div class="post-cards">
    <div class="post-card">
      <span><?php $this->archiveTitle([
            'category' => _t('分类「 %s 」下的文章'),
            'search'   => _t('搜索到包含关键字「 %s 」的文章'),
            'tag'      => _t('标签「 %s 」下的文章'),
            'author'   => _t('「 %s 」发布的文章')
        ], '', ''); ?></span>
    </div>
  </div> 
  <?php if ($this->have()): ?>
    <?php while ($this->next()): ?>
      <div class="post-cards">
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
      </div>
    <?php endwhile; ?>
     
    <?php else: ?>
      <div class="post-cards">
        <div class="post-card">
          <span><?php _e('没有找到内容'); ?></span>
        </div>
      </div>
    <?php endif; ?>
        
    <div class="pagination-container">
      <?php $this->pageLink('上一页'); ?>
      <span class="page-info">
        第 <?php echo $this->getCurrentPage(); ?> 页 / 共 <?php echo $this->getTotalPage(); ?> 页
      </span>
      <?php $this->pageLink('下一页', 'next'); ?>
    </div>
</div>

<?php $this->need('public/footer.php'); ?>