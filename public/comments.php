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
      <div class="buyu-cards">
        <div class="buyu-card">
          <span>当前文章受密码保护，无法评论</span>
        </div>
      </div> 
    <?php else : ?>
      <?php if ($allowComment && $commentStatus !== "off") : ?>
        <div class="buyu-cards">
          <div class="buyu-card">
            <!-- 评论标题区域 -->
            <div class="comment-section-header">
              <span class="comment-title">评论区（<?php $this->commentsNum(_t('暂无评论'), _t('仅有 1 条评论'), _t('已有 %d 条评论')); ?>）</span>
              <span class="comment-notice">本站使用 Cookie 技术保留您的个人信息</span>
            </div>

            <!-- 评论表单区域 -->
            <div id="<?php $this->respondId(); ?>" class="comment-form-container">
              <?php $comments->cancelReply(); ?>
              <form method="post" action="<?php $this->commentUrl(); ?>" id="comment-form" role="form">
                <!-- 表单内容保持不变 -->
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
                  <textarea class="form-control OwO-textarea" rows="6" name="text" id="textarea" placeholder="善语结善缘，恶语伤人心..." required><?php $this->remember('text'); ?></textarea>
                  <div id="comment-word-count"></div>
                  <div class="OwO"></div>
                </div>
                <input type="hidden" name="remember" value="1">
                <button type="submit" id="comment-submit-button" class="shortcode-button button-blue button-block mt-md">发送评论</button>
              </form>
            </div>

            <!-- 评论列表区域 -->
            <div class="listComments">
              <?php if ($comments->have()) : ?>
                <ol class="comment-list">
                  <?php $comments->listComments(); ?>
                </ol>
              <?php else : ?>
                <div class="no-comments">暂无评论，快来抢沙发吧~</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <script type="text/javascript">
          document.addEventListener("DOMContentLoaded", function () {
            const textarea = document.getElementById('textarea');
            const wordCountElement = document.getElementById('comment-word-count');
            const submitButton = document.getElementById('comment-submit-button');
            const maxLength = <?php echo $maxCommentLength ? $maxCommentLength : 'Infinity'; ?>;

            if (maxLength!== Infinity) {
              textarea.addEventListener('input', function () {
                const currentLength = Array.from(textarea.value).length;
                if (currentLength > maxLength) {
                  wordCountElement.textContent = `当前字数：${currentLength}，您已超出 ${currentLength - maxLength} 个字，请缩短评论字数`;
                  wordCountElement.style.color = 'red';
                  submitButton.disabled = true;
                  submitButton.style.opacity = 0.5;
                } else {
                  wordCountElement.textContent = `当前字数：${currentLength}，您还可以输入 ${maxLength - currentLength} 个字`;
                  submitButton.disabled = false;
                  submitButton.style.opacity = 1;
                }
              });
            } else {
              textarea.addEventListener('input', function () {
                const currentLength = Array.from(textarea.value).length;
                wordCountElement.textContent = `当前字数：${currentLength}`;
              });
            }

            new OwO({
              logo: 'OωO',
              container: document.getElementsByClassName('OwO')[0],
              target: document.getElementsByClassName('OwO-textarea')[0],
              api: '<?php echo get_theme_url('assets/json/OwO.json?v=1.3.1'); ?>'
            });
          });
        </script>
      <?php else : ?>
        <div class="buyu-cards">
          <div class="buyu-card">
            <span><?php echo $commentStatus === "off" ? '博主关闭了所有页面的评论' : '博主关闭了当前页面的评论'; ?></span>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
<?php
function threadedComments($comments, $options) {
    $commentClass = '';
    if ($comments->authorId) {
        $commentClass .= $comments->authorId == $comments->ownerId ? ' comment-by-author' : ' comment-by-user';
    }
    $commentLevelClass = $comments->_levels > 0 ? ' comment-child' : ' comment-parent';
?>
  <li id="li-<?php $comments->theId(); ?>" class="comment-body<?php echo $commentLevelClass . $commentClass; ?>">
    <div id="<?php $comments->theId(); ?>">
      <!-- 作者信息区域 -->
      <div class="comment-header">
        <?php echo getGravatar($comments->mail, 36, '', '', true, ['class' => 'avatar', 'loading' => 'lazy']); ?>
        <div class="comment-header-info">
          <div class="comment-author-name">
            <?php $comments->author(); ?>
            <?php dengji($comments->mail); // 博主标识 ?>
          </div>
          <div class="comment-meta">
            <time><?php $comments->date('Y-m-d H:i'); ?></time>
            <?php $comments->reply('回复'); ?>
          </div>
        </div>
      </div>

      <!-- 评论内容区域 -->
      <div class="comment-content">
        <?php 
          $commentAt = get_comment_at($comments->coid);
          ob_start();
          $comments->content(); 
          $content = ob_get_clean();
          if (!empty($commentAt)) {
            $content = preg_replace('/<p(.*?)>/', '<p$1>' . $commentAt, $content, 1);
          }
          echo $content;
        ?>
      </div>

      <!-- 状态信息区域 -->
      <div class="comment-status">
        <?php if ('waiting' == $comments->status) {?>
          <span class="shortcode-badge badge-info">待审核</span>
        <?php } ?>
        <span class="shortcode-badge badge-info"><?php echo convertip($comments->ip); ?></span>
        <span class="shortcode-badge badge-info"><?php _getAgentOS($comments->agent); ?> · <?php _getAgentBrowser($comments->agent); ?></span>
      </div>

      <!-- 子评论区域 -->
      <?php if ($comments->children) : ?>
        <div class="comment-children">
          <?php $comments->threadedComments($options); ?>
        </div>
      <?php endif; ?>
    </div>
  </li>
<?php } ?>