<?php
/**
 * 一款基于 typecho 默认模版二次开发的 Typecho 主题
 *
 * @package buyu
 * @author 不语
 * @version 1.2.4
 * @link https://zhuxu.asia/
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('public/header.php');
?>

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
                <li><?php $this->commentsNum('无评论', '1 条评论', '%d 条评论'); ?></li>
                <li><?php get_post_view($this) ?>次阅读</li>
            </ul>
            <p class="card-text"><?php $this->excerpt(150, '...'); ?></p>
        </article>
    <?php endwhile; ?>

    <?php $this->pageNav('«', '»', 1, '···', array('wrapTag' => 'div', 'wrapClass' => 'page-navigator', 'itemTag' => 'li', 'textTag' => 'span', 'currentClass' => 'current', 'prevClass' => 'prev', 'nextClass' => 'next',)); ?>
</div>

<?php $this->need('public/footer.php'); ?>