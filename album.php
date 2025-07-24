<?php
/**
 * 相册
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('public/header.php');
?>

<div class="col-mb-12 col-12" id="main" role="main">
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

    <!-- 相册网格布局 -->
    <div class="grid-container">
      <?php
        // 获取自定义字段中的图片数据（JSON格式）
        $imageData = $this->fields->album_images;
        if (!empty($imageData)) {
          // 解析JSON为数组
          $images = json_decode($imageData, true);
          // 循环输出图片
          if (is_array($images)) {
            foreach ($images as $img) {
                echo '<div class="grid-item">';
                // 图片标签（data-src为图片URL，alt为描述）
                echo '<a href="' . $img['url'] . '" data-fancybox="album">';
                echo '<img data-src="' . $img['url'] . '" alt="' . $img['alt'] . '">';
                echo '</a>';
                // 可选：显示图片标题
                if (!empty($img['title'])) {
                    echo '<div class="img-caption">' . $img['title'] . '</div>';
                }
                echo '</div>';
            }
          } else {
            echo '<p>图片数据格式错误，请检查JSON格式</p>';
          }
        } else {
          echo '<p>暂无图片，请添加相册内容</p>';
        }
       ?>
    </div>
  </article>
  <?php $this->need('public/comments.php'); ?>
</div>

<?php $this->need('public/footer.php');?>