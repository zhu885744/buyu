<div class="buyu_detail__copyright border rounded mt-lg">
  <div class="content space-y-md">
    <!-- 版权归属信息 -->
    <div class="item flex items-center gap-sm">
      <i class="fa fa-copyright inline inline text-base leading-none" style="color: var(--color-blue-500);" aria-hidden="true"></i>
      <span class="copyright-label text-neutral-600 ml-1 text-base">版权属于：</span>
      <span class="copyright-value text-base"><?php $this->author(); ?></span>
    </div>

    <!-- 文章标签信息 -->
    <div class="item flex items-center gap-sm flex-wrap">
      <i class="fa fa-tag text-base leading-none" style="color: var(--color-purple-500);" aria-hidden="true"></i>
      <span class="copyright-label text-neutral-600 ml-1 text-base">文章标签：</span>
      <span class="copyright-value flex flex-wrap gap-sm text-base">
        <?php if ($this->tags): ?>
          <?php foreach ($this->tags as $tag): ?>
            <a href="<?php echo $tag['permalink']; ?>" class="text-link hover:text-link-hover transition-color" rel="tag">
              <?php echo $tag['name']; ?>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          无标签
        <?php endif; ?>
      </span>
    </div>

    <!-- 文章链接信息 -->
    <div class="item flex items-center gap-sm flex-wrap">
      <i class="fa fa-link text-base leading-none" style="color: var(--color-green-500);" aria-hidden="true"></i>
      <span class="copyright-label text-neutral-600 ml-1 text-base">本文链接：</span>
      <span class="copyright-value flex-1 break-all text-base">
        <a class="text-link hover:text-link-hover transition-color" href="<?php $this->permalink() ?>" target="_blank" rel="noopener noreferrer nofollow">
          <?php $this->permalink() ?>
        </a>
      </span>
    </div>

    <!-- 许可协议信息 -->
    <div class="item flex items-center gap-sm flex-wrap">
      <i class="fa fa-creative-commons text-base leading-none" style="color: var(--color-yellow-500);" aria-hidden="true"></i>
      <span class="copyright-label text-neutral-600 ml-1 text-base">文章采用：</span>
      <span class="copyright-value text-base">
        <a class="text-link hover:text-link-hover transition-color" href="//creativecommons.org/licenses/by-nc-sa/4.0/deed.zh" target="_blank" rel="noopener noreferrer nofollow" title="知识共享 署名-非商业性使用-相同方式共享 4.0 国际许可协议">
          CC BY-NC-SA 4.0
        </a>
        &nbsp;许可协议授权
      </span>
    </div>
  </div>
</div>