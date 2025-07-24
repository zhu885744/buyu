<div class="buyu_detail__copyright">
  <div class="content">
    <div class="item">
      <i class="fa fa-copyright fa-1x" style="color: #249FF8;"></i>
      <span>版权属于：</span>
      <span class="text"><?php $this->author(); ?></span>
    </div>
    <div class="item">
      <i class="fa fa-link fa-1x" style="color: #39B54A;"></i>
      <span>本文链接：</span>
      <span class="text">
        <a class="link" href="<?php $this->permalink() ?>" target="_blank" rel="noopener noreferrer nofollow"><?php $this->permalink() ?></a>
      </span>
    </div>
    <div class="item">
      <i class="fa fa-tags fa-1x" style="color:#3e1cff;"></i>
      <span>文章标签：</span>
      <span class="text">
        <?php if ($this->tags): ?>
          <?php foreach ($this->tags as $tag): ?>
            <a href="<?php echo $tag['permalink']; ?>" class="link">
              <?php echo $tag['name']; ?>
            </a>
          <?php endforeach; ?>
          <?php else: ?>
            无标签
          <?php endif; ?>
      </span>
    </div>
    <div class="item">
      <i class="fa fa-creative-commons fa-1x" style="color: #F3B243;"></i>
      <span>文章采用：</span>
      <span class="text">
        <a class="link" href="//creativecommons.org/licenses/by-nc-sa/4.0/deed.zh" target="_blank" rel="noopener noreferrer nofollow">CC BY-NC-SA 4.0</a>&nbsp;许可协议授权
      </span>
    </div>
  </div>
</div>