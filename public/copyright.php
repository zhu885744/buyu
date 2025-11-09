<div class="border rounded-sm mt-lg p-md space-y-md">
  <!-- 版权归属信息 -->
  <div class="flex items-center gap-sm">
    <i class="fa fa-copyright" style="color: var(--color-blue-500);" aria-hidden="true"></i>
    <span class="text-neutral-600 ml-1">版权属于：</span>
    <span><?php $this->author(); ?></span>
  </div>

  <!-- 文章标签信息 -->
  <div class="flex items-center gap-sm flex-wrap">
    <i class="fa fa-tag" style="color: var(--color-purple-500);" aria-hidden="true"></i>
    <span class="text-neutral-600 ml-1">文章标签：</span>
    <div class="flex flex-wrap gap-sm">
      <?php if ($this->tags): ?>
        <?php foreach ($this->tags as $tag): ?>
          <a href="<?php echo $tag['permalink']; ?>" class="text-link hover:text-link-hover transition-color" rel="tag">
            <?php echo $tag['name']; ?>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        无标签
      <?php endif; ?>
    </div>
  </div>

  <!-- 文章链接信息 -->
  <div class="flex items-center gap-sm flex-wrap">
    <i class="fa fa-link" style="color: var(--color-green-500);" aria-hidden="true"></i>
    <span class="text-neutral-600 ml-1">本文链接：</span>
    <a class="text-link hover:text-link-hover transition-color flex-1 break-all" href="<?php $this->permalink() ?>" target="_blank" rel="noopener noreferrer nofollow">
      <?php $this->permalink() ?>
    </a>
  </div>

  <!-- 许可协议信息 -->
  <div class="flex items-center gap-sm flex-wrap">
    <i class="fa fa-creative-commons" style="color: var(--color-yellow-500);" aria-hidden="true"></i>
    <span class="text-neutral-600 ml-1">文章采用：</span>
    <a class="text-link hover:text-link-hover transition-color" href="//creativecommons.org/licenses/by-nc-sa/4.0/deed.zh" target="_blank" rel="noopener noreferrer nofollow" title="知识共享 署名-非商业性使用-相同方式共享 4.0 国际许可协议">
      CC BY-NC-SA 4.0
    </a>
    许可协议授权
  </div>
</div>