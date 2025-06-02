<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('public/header.php'); ?>

<div class="col-mb-12 col-8" id="main" role="main">
    <h3 class="archive-title"><?php $this->archiveTitle([
            'category' => _t('分类「 %s 」下的文章'),
            'search'   => _t('搜索到包含关键字「 %s 」的文章'),
            'tag'      => _t('标签「 %s 」下的文章'),
            'author'   => _t('「 %s 」发布的文章')
        ], '', ''); ?></h3>
    <?php if ($this->have()): ?>
        <?php while ($this->next()): ?>
            <article class="post">
                <h2 class="post-title" itemprop="name headline">
                    <a itemprop="url" href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
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
    <?php else: ?>
        <article class="post">
            <h2 class="post-title"><?php _e('没有找到内容'); ?></h2>
        </article>
    <?php endif; ?>

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

<?php $this->need('public/footer.php'); ?>
