<?php
$this->comments()->to($comments);
$isHidden = $this->hidden;
$allowComment = $this->allow('comment');
$commentStatus = $this->options->JCommentStatus;
$userHasLogin = $this->user->hasLogin();
// 获取最大评论字数
$maxCommentLength = Helper::options()->JTextLimit;
?>
<div id="comments">
    <?php if ($isHidden) : ?>
      <div class="post-cards">
        <div class="post-card">
          <span>当前文章受密码保护，无法评论</span>
        </div>
      </div> 
    <?php else : ?>
        <?php if ($allowComment && $commentStatus !== "off") : ?>
          <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.OwO.css?v=1.3.0'); ?>">
          <div class="post-cards">
            <div class="post-card">
              <span class="comment-title">发送评论（<?php $this->commentsNum(_t('暂无评论'), _t('仅有 1 条评论'), _t('已有 %d 条评论')); ?>）</span>
              <span>本站使用 Cookie 技术保留您的个人信息</span>
              <div id="<?php $this->respondId(); ?>">
                <?php $comments->cancelReply(); ?>
                <form method="post" action="<?php $this->commentUrl(); ?>" id="comment-form" role="form">
                    <div class="input-group">
                        <div class="form-group">
                            <label for="author" class="required"><?php _e('昵称'); ?></label>
                            <input class="form-control" type="text" name="author" id="author" placeholder="必填" value="<?php echo $userHasLogin ? $this->user->screenName() : $this->remember('author'); ?>" autocomplete="off" maxlength="16" required />
                        </div>
                        <div class="form-group">
                            <label for="mail1" <?php if ($this->options->commentsRequireMail) : ?> class="required" <?php endif; ?>><?php _e('邮箱'); ?></label>
                            <input class="form-control" type="email" name="mail" id="mail1" placeholder="必填" value="<?php $this->user->hasLogin() ? $this->user->mail() : $this->remember('mail') ?>" <?php if ($this->options->commentsRequireMail) : ?> autocomplete="off" required <?php endif; ?> />
                        </div>
                        <div class="form-group">
                            <label for="url" <?php if ($this->options->commentsRequireURL) : ?> class="required" <?php endif; ?>><?php _e('网址'); ?></label>
                            <input class="form-control" type="url" name="url" id="url" placeholder="<?php _e('https://'); ?>" value="<?php $this->remember('url'); ?>" <?php if ($this->options->commentsRequireURL) : ?> required <?php endif; ?> />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="textarea" class="required"><?php _e('内容'); ?></label>
                        <textarea class="form-control OwO-textarea" rows="8" name="text" id="textarea" placeholder="善语结善缘，恶语伤人心..." required><?php $this->remember('text'); ?></textarea>
                        <!-- 添加字数提示元素 -->
                        <div id="comment-word-count" style="font-size: 12px; color: #666;"></div>
                        <div class="OwO"></div>
                    </div>
                    <input type="hidden" name="remember" value="1">
                    <div class="d-grid comment-submit-button-container" style="margin-bottom: 3.5em;">
                        <button type="submit" id="comment-submit-button" class="comment-submit-button">发送评论</button>
                    </div>
                </form>
              </div>
            </div>
          </div>

          <div class="listComments">
            <?php if ($comments->have()) : ?>
              <ol class="comment-list">
                <?php $comments->listComments(); ?>
              </ol>
            <?php endif; ?>
          </div>

          <script type="text/javascript" src="<?php echo get_theme_url('assets/js/buyu.OwO.js?v=1.3.0'); ?>"></script>
          <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function () {
                const textarea = document.getElementById('textarea');
                const wordCountElement = document.getElementById('comment-word-count');
                const submitButton = document.getElementById('comment-submit-button');
                const maxLength = <?php echo $maxCommentLength ? $maxCommentLength : 'Infinity'; ?>;

                // 仅在 maxLength 不是 Infinity 时添加 input 事件监听器
                if (maxLength!== Infinity) {
                    textarea.addEventListener('input', function () {
                        console.log('Input event triggered');
                        const currentLength = Array.from(textarea.value).length;
                        if (currentLength > maxLength) {
                            wordCountElement.textContent = `当前字数：${currentLength}，您已超出 ${currentLength - maxLength} 个字，请缩短评论字数`;
                            wordCountElement.style.color = 'red';
                            submitButton.disabled = true; // 禁用发送评论按钮
                            submitButton.style.opacity = 0.5; // 降低按钮透明度
                        } else {
                            wordCountElement.textContent = `当前字数：${currentLength}，您还可以输入 ${maxLength - currentLength} 个字`;
                            wordCountElement.style.color = '#666';
                            submitButton.disabled = false; // 启用发送评论按钮
                            submitButton.style.opacity = 1; // 恢复按钮透明度
                        }
                    });
                } else {
                    // 如果没有字数限制，显示当前字数
                    textarea.addEventListener('input', function () {
                        const currentLength = Array.from(textarea.value).length;
                        wordCountElement.textContent = `当前字数：${currentLength}`;
                    });
                }

                new OwO({
                    logo: 'OωO',
                    container: document.getElementsByClassName('OwO')[0],
                    target: document.getElementsByClassName('OwO-textarea')[0],
                    api: '<?php echo get_theme_url('assets/json/OwO.json?v=1.3.0'); ?>'
                });
            });
        </script>
        <?php else : ?>
          <div class="post-cards">
            <div class="post-card">
              <span><?php echo $commentStatus === "off" ? '博主关闭了所有页面的评论' : '博主关闭了当前页面的评论'; ?></span>
            </div>
          </div>
        <?php endif; ?>
    <?php endif; ?>
  </div>
<?php
/**
 * 递归渲染嵌套评论列表
 *
 * 该函数用于递归渲染评论列表，支持嵌套评论显示。会根据评论作者身份和评论层级添加不同的 CSS 类，
 * 并显示评论的作者信息、发布时间、回复按钮、评论内容、审核状态和 IP 地址。
 *
 * @param object $comments 当前评论对象，包含评论的各种信息
 * @param array $options 评论显示的相关选项
 */
function threadedComments($comments, $options)
{
    // 初始化评论的 CSS 类名
    $commentClass = '';
    // 判断评论作者是否有用户 ID
    if ($comments->authorId) {
        // 如果评论作者是文章所有者，添加 'comment-by-author' 类，否则添加 'comment-by-user' 类
        $commentClass .= $comments->authorId == $comments->ownerId? ' comment-by-author' : ' comment-by-user';
    }
    // 根据评论层级判断是子级评论还是父级评论，添加相应的 CSS 类
    $commentLevelClass = $comments->_levels > 0? ' comment-child' : ' comment-parent';  // 评论层数大于0为子级，否则是父级
?>

<!-- 评论项容器，使用评论 ID 作为唯一标识，并添加评论层级和作者相关的 CSS 类 -->
<li id="li-<?php $comments->theId(); ?>" class="comment-body<?php echo $commentLevelClass . $commentClass; ?>">
    <!-- 单个评论的主要内容容器，使用评论 ID 作为唯一标识 -->
    <div id="<?php $comments->theId(); ?>">
        <!-- 评论作者信息区域 -->
        <div class="comment-author">
            <!-- 显示评论作者的头像，尺寸为 40px，使用懒加载 -->
            <?php echo getGravatar($comments->mail, 40, '', '', true, ['class' => 'avatar', 'loading' => 'lazy']); ?>
            <!-- 评论作者姓名区域 -->
            <cite class="fn">
                <!-- 显示评论作者姓名 -->
                <?php $comments->author(); ?>
                <!-- 如果评论作者是文章所有者，显示 '博主' 徽章 -->
                <?php dengji($comments->mail);?>
            </cite>
        </div>
        <!-- 评论元信息区域，包含评论发布时间和回复按钮 -->
        <div class="comment-meta">
            <!-- 显示评论发布时间，格式为 'Y-m-d H:i' -->
            <cite class="fn"><?php $comments->date('Y-m-d H:i'); ?></cite>
            <!-- 显示回复按钮 -->
            <span class="comment-reply"><?php $comments->reply(); ?></span>
        </div>
        <!-- 评论内容区域 -->
        <div class="comment-content">
            <!-- 显示评论内容 -->
            <?php $comments->content(); ?>
        </div>
        <!-- 如果评论状态为待审核，显示 '待审核' 徽章 -->
        <?php if ('waiting' == $comments->status) {?><span class="badge" style="color: #3354AA;">待审核</span><?php } ?>
        <!-- 显示评论者的 IP 地址，通过 convertip 函数处理 -->
        <span class="badge" style="color: #3354AA;"><?php echo convertip($comments->ip); ?></span>
    </div>
    <!-- 如果当前评论有子评论，递归渲染子评论列表 -->
    <?php if ($comments->children) : ?>
        <div class="comment-children">
            <?php $comments->threadedComments($options); ?>
        </div>
    <?php endif; ?>
</li>
<?php } ?>