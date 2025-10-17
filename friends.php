<?php
/**
 * 友链
 * 
 * @package custom 
 * 
 **/
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('public/header.php');
?>
<main id="pjax-container">
  <div class="container">
    <div class="row">
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
              <?php echo processContent($this->content, $this->title); ?>
              <div class="friend-container">
                <?php if (isset($this->options->plugins['activated']['Links'])) : ?>
                  <?php
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
                  ?>
                <?php endif; ?>
              </div>
            </div>
          </article>
        </div>
        <?php $this->need('public/comments.php'); ?>
      </div><!-- end #main-->
    </div>
  </div>
</main>
<?php $this->need('public/footer.php'); ?>