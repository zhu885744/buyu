<?php
/**
 * 归档页
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('public/header.php');
?>

<div class="col-mb-12 col-8" id="main" role="main">
    <article class="post">
        <h1 class="post-title" itemprop="name headline">
            <?php $this->title() ?>
        </h1>
        <ul class="post-meta">
            <li><time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time></li>
            <li><?php $this->commentsNum('无评论', '1 条评论', '%d 条评论'); ?></li>
            <li><?php get_post_view($this) ?>次阅读</li>
        </ul>
        <div itemprop="articleBody">
            <div class="post-content" id="post-<?php $this->cid(); ?>">
                <?php Typecho_Widget::widget('Widget_Stat')->to($stat); ?>
                <div class="friend-container"style="margin-top: 15px; margin-bottom: 15px;">
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

                <div class="clear"></div>
                <div class="entry">
                    <?php
                    $this->widget('Widget_Contents_Post_Recent', 'pageSize=10000')->to($archives);
                    $year = 0;
                    $i = 0;
                    $output = '<div class="post-cc">';
                    $first = true;
                    while ($archives->next()):
                        $year_tmp = date('Y', $archives->created);
                        if ($year != $year_tmp && $year > 0) {
                            $output .= '</div></details>'; // 结束上一个年份的details和div
                        }
                        if ($year != $year_tmp) {
                            $year = $year_tmp;
                            if ($first) {
                                $output .= '<details open style="margin-top: 2px; margin-bottom: 2px;"><summary>' . $year . ' 年</summary><br><div class="year-content">'; // 第一个年份添加open属性和下边距
                                $first = false;
                            } else {
                                $output .= '<details style="margin-bottom: 2px;"><summary>' . $year . ' 年</summary><br><div class="year-content">'; // 开始新的年份details和div并添加下边距
                            }
                        }
                        $month_tmp = date('m', $archives->created);
                        $day_tmp = date('d', $archives->created);
                        $output .= '<div class="month-day-title">' . $month_tmp . '月' . $day_tmp . '日： <a href="' . $archives->permalink . '">' . $archives->title . '</a></div>';
                    endwhile;
                    if ($year > 0) {
                        $output .= '</div></details>'; // 结束最后一个年份的details和div（如果有的话）
                    }
                    $output .= '</div>'; // 结束post-cc的div

                    echo $output;
                    ?>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </article>
    <?php $this->need('public/comments.php'); ?>
</div>

<?php $this->need('public/footer.php'); ?>    