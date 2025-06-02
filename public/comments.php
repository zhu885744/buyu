<?php
$this->comments()->to($comments);
$isHidden = $this->hidden;
$allowComment = $this->allow('comment');
$commentStatus = $this->options->JCommentStatus;
$userHasLogin = $this->user->hasLogin();
?>

<div id="comments">
    <?php if ($isHidden) : ?>
        <span>当前文章受密码保护，无法评论</span>
    <?php else : ?>
        <?php if ($allowComment && $commentStatus !== "off") : ?>
            <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/buyu.OwO.css'); ?>">
            <h2>发表评论（<?php $this->commentsNum(_t('暂无评论'), _t('仅有 1 条评论'), _t('已有 %d 条评论')); ?>）</h2>
            <h4>本站使用 Cookie 技术保留您的个人信息以便您下次快速评论</h4>
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
                            <input class="form-control" type="email" name="mail" id="mail1" placeholder="必填" value="<?php echo $userHasLogin ? $this->user->mail() : $this->remember('mail'); ?>" <?php if ($this->options->commentsRequireMail) : ?> autocomplete="off" required <?php endif; ?> />
                        </div>
                        <div class="form-group">
                            <label for="url" <?php if ($this->options->commentsRequireURL) : ?> class="required" <?php endif; ?>><?php _e('网址'); ?></label>
                            <input class="form-control" type="url" name="url" id="url" placeholder="<?php _e('https://'); ?>" value="<?php $this->remember('url'); ?>" <?php if ($this->options->commentsRequireURL) : ?> required <?php endif; ?> />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="textarea" class="required"><?php _e('内容'); ?></label>
                        <textarea class="form-control OwO-textarea" rows="8" name="text" id="textarea" placeholder="善语结善缘，恶语伤人心..." required><?php $this->remember('text'); ?></textarea>
                        <div class="OwO"></div>
                    </div>
                    <!-- 隐藏并默认勾选「记住我」 -->
                    <input type="hidden" name="remember" value="1">
                    <div class="d-grid comment-submit-button-container" style="margin-bottom: 3.5em;">
                        <button type="submit" id="comment-submit-button" class="comment-submit-button">发送评论</button>
                    </div>
                </form>
            </div>
            
            <div class="listComments">
                <?php if ($comments->have()) : ?>
                    <ol class="comment-list">
                        <?php $comments->listComments(); ?>
                    </ol>
                <?php endif; ?>
            </div>
            
            <script src="<?php $this->options->themeUrl('assets/js/buyu.OwO.js'); ?>"></script>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function () {
                    new OwO({
                        logo: 'OωO',
                        container: document.getElementsByClassName('OwO')[0],
                        target: document.getElementsByClassName('OwO-textarea')[0],
                        api: '<?php $this->options->themeUrl('assets/json/OwO.json'); ?>',
                        position: 'down',
                        width: '100%',
                        maxHeight: '250px'
                    });
                    
                    // 为头像添加淡入动画效果
                    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
                    lazyImages.forEach(img => {
                        img.addEventListener('load', function() {
                            this.classList.add('loaded');
                        });
                    });
                });
            </script>
        <?php else : ?>
            <span><?php echo $commentStatus === "off" ? '博主关闭了所有页面的评论' : '博主关闭了当前页面的评论'; ?></span>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
function threadedComments($comments, $options)
{
    $commentClass = '';
    if ($comments->authorId) {
        $commentClass .= $comments->authorId == $comments->ownerId ? ' comment-by-author' : ' comment-by-user';
    }

    $commentLevelClass = $comments->levels > 0 ? ' comment-child' : ' comment-parent';
?>

<li id="li-<?php $comments->theId(); ?>" class="comment-body<?php echo $commentLevelClass . $commentClass; ?>">
    <div id="<?php $comments->theId(); ?>">
        <div class="comment-author">
            <?php 
            // 添加 loading="lazy" 属性实现头像懒加载
            $comments->gravatar('30', '', '', array('loading' => 'lazy', 'class' => 'lazy-avatar')); 
            ?>
            <cite class="fn">
                <?php $comments->author(); ?>
                <?php if ($comments->authorId == $comments->ownerId) : ?>
                    <span class="comment-badge">博主</span>
                <?php endif; ?>
            </cite>
        </div>
        <div class="comment-meta">
            <cite class="fn"><?php $comments->date('Y-m-d H:i'); ?></cite>
            <span class="comment-reply"><?php $comments->reply(); ?></span>
        </div>
        <div class="comment-content">
            <?php $comments->content(); ?>
        </div>
        <?php if ('waiting' == $comments->status) : ?><span class="badge">待审核</span><?php endif; ?>
        <span class="badge" style="color: #3354AA;"><?php echo convertip($comments->ip); ?></span>
    </div>
    <?php if ($comments->children) : ?>
        <div class="comment-children">
            <?php $comments->threadedComments($options); ?>
        </div>
    <?php endif; ?>
</li>
<?php } ?>    