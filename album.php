<?php
/**
 * 相册
 *
 * @package custom
 */
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
            <?php if ($this->options->copyright!== "off") :?>
              <?php $this->need('public/copyright.php'); ?>
            <?php endif; ?>
          </article>
        </div>
        <?php $this->need('public/comments.php'); ?>
      </div>
    </div>
  </div>
</main>
<?php $this->need('public/footer.php');?>