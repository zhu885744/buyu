<?php
/**
 * 友链
 * 
 * @package custom 
 **/
if (!defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}
$this->need('public/header.php');
?>
<main id="main">
  <div class="container">
    <div class="row">
      <div class="col-mb-12 col-12">
        <div class="buyu-cards">
          <article class="buyu-card">
            <h2 class="post-title font-bold mb-md" itemprop="name headline">
              <?php $this->title() ?>
            </h2>
            <ul class="post-meta">
              <li><i class="fa fa-calendar mr-1"></i>&nbsp;<time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php echo time_ago($this->date ?? new \Typecho\Date(time())); ?></time></li>
              <?php if ($this->options->JCommentStatus !== "off"): ?>
                <li><i class="fa fa-commenting-o mr-1"></i>&nbsp;<?php $this->commentsNum('无评论', '1 条评论', '%d 条评论'); ?></li>
              <?php endif; ?>
              <li><i class="fa fa-eye mr-1"></i>&nbsp;<?php get_post_view($this) ?>次阅读</li>
            </ul>
            <div class="post-content" itemprop="articleBody">
              <?php echo processContent($this->content, $this->title); ?>
              <div class="friend-container">
                <?php if (isset($this->options->plugins['activated']['Links'])) : ?>
                <?php
                  if (class_exists('Links_Plugin')) {
                    Links_Plugin::output('
                      <a class="friend-card" href="{url}" title="{title}" target="_blank" rel="noopener">
                        <img class="friend-avatar" data-src="{image}" alt="{name}" width="{size}" height="{size}">
                        <div class="friend-info">
                          <p class="friend-name">{name}</p>
                          <p class="friend-description">
                            <span class="cjk-latin-custom-space">{description}</span>
                          </p>
                        </div>
                      </a>
                    ', 0);
                  }
                ?>
                <?php endif; ?>
              </div>
            </div>
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
<?php $this->need('public/footer.php'); ?>