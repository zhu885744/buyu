<?php
/**
 * 归档页
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
            <div itemprop="articleBody">
              <div class="post-content" id="post-<?php $this->cid(); ?>">
                <h3><?php _e('网站统计'); ?></h3>
                <?php Typecho_Widget::widget('Widget_Stat')->to($stat); ?>
                <div class="friend-container" style="margin-top: 15px; margin-bottom: 15px;">
                  <div class="friend-card">
                    <div class="friend-info">
                      <p class="friend-name">文章总数</p>
                      <p class="friend-description">
                        <span class="cjk-latin-custom-space"><?php $stat->publishedPostsNum() ?>篇</span>
                      </p>
                    </div>
                  </div>
                  <div class="friend-card">
                    <div class="friend-info">
                      <p class="friend-name">分类总数</p>
                      <p class="friend-description">
                        <span class="cjk-latin-custom-space"><?php $stat->categoriesNum() ?>个</span>
                      </p>
                    </div>
                  </div>
                  <div class="friend-card">
                    <div class="friend-info">
                      <p class="friend-name">评论总数</p>
                      <p class="friend-description">
                        <span class="cjk-latin-custom-space"><?php $stat->publishedCommentsNum() ?>条</span>
                      </p>
                    </div>
                  </div>
                </div>
                
                <h3><?php _e('网站归档'); ?></h3>
                <div class="clear"></div>
                <div class="entry">
                  <?php
                  // 确保每个年份独立
                  $this->widget('Widget_Contents_Post_Recent', 'pageSize=10000')->to($archives);
                  $year = 0;
                  $output = '';
                  $firstYear = true;
                  
                  while ($archives->next()):
                    $currentYear = date('Y', $archives->created);
                    
                    // 当年份变化且不是第一个年份时，闭合上一个折叠面板
                    if ($year != $currentYear && $year > 0) {
                      $output .= '</div></div></div>'; // 闭合内容容器+折叠内容+面板容器
                    }
                    
                    // 年份变化时，创建新的折叠面板
                    if ($year != $currentYear) {
                      $year = $currentYear;
                      $isOpen = $firstYear ? 'true' : 'false';
                      
                      // 新折叠面板的完整结构
                      $output .= '<div class="shortcode-collapse collapse-info ' . ($isOpen === 'true' ? 'collapse-open' : '') . '">';
                      $output .= '<div class="collapse-header" role="button" tabindex="0" aria-expanded="' . $isOpen . '">';
                      $output .= '<span class="collapse-title">' . $year . ' 年</span>';
                      $output .= '<span class="collapse-icon"><i class="fa fa-chevron-down"></i></span>';
                      $output .= '</div>'; // 闭合标题栏
                      $output .= '<div class="collapse-content ' . ($isOpen === 'true' ? '' : 'hidden') . '">';
                      $output .= '<div class="archive-year-content">'; // 年份内容容器
                      
                      $firstYear = false;
                    }
                    
                    // 输出单篇文章条目
                    $month = date('m', $archives->created);
                    $day = date('d', $archives->created);
                    $output .= '<div class="archive-item">';
                    $output .= '<span class="archive-date">' . $month . '月' . $day . '日：</span>';
                    $output .= '<a href="' . $archives->permalink . '" class="archive-title" title="' . $archives->title . '">';
                    $output .= $archives->title;
                    $output .= '</a>';
                    $output .= '</div>';
                  endwhile;
                  
                  // 闭合最后一个年份的折叠面板
                  if ($year > 0) {
                    $output .= '</div></div></div>';
                  }
                  
                  echo $output;
                  ?>
                  <div class="clear"></div>
                </div>
              </div>
            </div>
          </article>
        </div>
      </div>
    </div>
  </div>
</main>
<?php $this->need('public/footer.php'); ?>