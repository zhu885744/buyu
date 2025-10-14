<div class="buyu_detail__copyright">
  <div class="content">
    <!-- 版权归属信息 -->
    <div class="item">
      <i class="fa fa-copyright fa-1x" style="color: #249FF8;" aria-hidden="true"></i>
      <span class="copyright-label">版权属于：</span>
      <span class="copyright-value"><?php $this->author(); ?></span>
    </div>

    <!-- 文章标签信息 -->
    <div class="item">
      <i class="fa fa-tags fa-1x" style="color: #3e1cff;" aria-hidden="true"></i>
      <span class="copyright-label">文章标签：</span>
      <span class="copyright-value">
        <?php if ($this->tags): ?>
          <?php foreach ($this->tags as $tag): ?>
            <a href="<?php echo $tag['permalink']; ?>" class="link" rel="tag">
              <?php echo $tag['name']; ?>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          无标签
        <?php endif; ?>
      </span>
    </div>

    <!-- 文章链接信息 -->
    <div class="item">
      <i class="fa fa-link fa-1x" style="color: #39B54A;" aria-hidden="true"></i>
      <span class="copyright-label">本文链接：</span>
      <span class="copyright-value">
        <a 
          class="link" 
          href="<?php $this->permalink() ?>" 
          target="_blank" 
          rel="noopener noreferrer nofollow"
        >
          <?php $this->permalink() ?>
        </a>
      </span>
    </div>

    <!-- 许可协议信息 -->
    <div class="item">
      <i class="fa fa-creative-commons fa-1x" style="color: #F3B243;" aria-hidden="true"></i>
      <span class="copyright-label">文章采用：</span>
      <span class="copyright-value">
        <a 
          class="link" 
          href="//creativecommons.org/licenses/by-nc-sa/4.0/deed.zh" 
          target="_blank" 
          rel="noopener noreferrer nofollow"
          title="知识共享 署名-非商业性使用-相同方式共享 4.0 国际许可协议"
        >
          CC BY-NC-SA 4.0
        </a>
        &nbsp;许可协议授权
      </span>
    </div>
  </div>
</div>