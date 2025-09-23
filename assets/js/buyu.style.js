/** 开源不易，请尊重作者版权，保留本信息 **/
function showConsoleInfo() {
    const version = 'v1.3.1';
    const copyright = 'buyu 主题';
    console.log('\n' + ' %c ' + copyright + ' ' + version + ' %c https://zhuxu.asia/  ' + '\n', 'color: #fadfa3; background: #030307; padding:5px 0;', 'background: #fadfa3; padding:5px 0;');
    console.log('开源不易，请尊重作者版权，保留基本的版权信息。');
}
// 调用函数
showConsoleInfo();

document.addEventListener('DOMContentLoaded', function () {
    // 初始化图片懒加载
    initLazyLoad();
    // 初始化任务列表
    initTaskList();
    // 初始化返回顶部按钮
    initBackToTopButton();
    // 初始化代码复制按钮
    initCodeCopyButtons();
    // 初始化文章分享功能
    initArticleSharing();
    // 初始化文章点赞功能
    initArticleLiking();
    // 初始化折叠面板
    initDetailsPanels();
    // 初始化导航菜单
    initNavigationMenu();
});

// 导航菜单
function initNavigationMenu() {
  // 获取元素并缓存
  const menuToggle = document.querySelector('.menu-toggle');
  const mainMenu = document.querySelector('.main-menu');
  const navOverlay = document.querySelector('.nav-overlay');
  const dropdownTriggers = document.querySelectorAll('.has-dropdown > a');
  const dropdownMenus = document.querySelectorAll('.dropdown');
  // 存储当前状态
  let isMenuOpen = false;
  // 监听全局点击事件
  document.addEventListener('click', function(e) {
    // 仅在移动端生效（桌面端无需此逻辑）
    if (window.innerWidth > 768) return;

    // 判断点击目标是否为「下拉相关元素」
    const isClickOnDropdownTrigger = e.target.closest('.has-dropdown > a'); // 点击下拉触发按钮
    const isClickOnDropdownMenu = e.target.closest('.dropdown'); // 点击下拉菜单内容
    const isClickOnMenuToggle = e.target.closest('.menu-toggle'); // 点击汉堡菜单按钮

    // 若点击的是「空白区域」（非下拉相关、非汉堡按钮），且有展开的下拉菜单，则关闭
    if (!isClickOnDropdownTrigger && !isClickOnDropdownMenu && !isClickOnMenuToggle) {
      closeAllDropdowns();
    }
  });

  // 切换菜单显示/隐藏
  function toggleMenu(forceClose = false) {
    if (!mainMenu || !navOverlay) return;
    const shouldOpen = forceClose ? false : !isMenuOpen;
    mainMenu.classList.toggle('active', shouldOpen);
    navOverlay.classList.toggle('active', shouldOpen);
    document.body.classList.toggle('overflow-hidden', shouldOpen);
    isMenuOpen = shouldOpen;
    if (menuToggle) {
      menuToggle.classList.toggle('active', shouldOpen);
    }
  }

  // 关闭所有下拉菜单（新增 maxHeight 重置以确保动画流畅）
  function closeAllDropdowns() {
    document.querySelectorAll('.has-dropdown.active').forEach(item => {
      item.classList.remove('active');
      const dropdown = item.querySelector('.dropdown');
      if (dropdown) dropdown.style.maxHeight = '0'; // 重置高度，配合过渡动画
    });
  }

  // 点击汉堡菜单按钮
  if (menuToggle) {
    menuToggle.addEventListener('click', () => toggleMenu());
  }

  // 点击遮罩层关闭菜单和所有下拉
  if (navOverlay) {
    navOverlay.addEventListener('click', () => {
      closeAllDropdowns();
      toggleMenu(true);
    });
  }

  // 处理下拉菜单触发
  dropdownTriggers.forEach(trigger => {
    trigger.addEventListener('click', function(e) {
      const parentItem = this.parentElement;
      const dropdown = this.nextElementSibling;
      // 移动端处理
      if (window.innerWidth <= 768) {
        e.preventDefault();
        const isActive = parentItem.classList.contains('active');
        // 关闭其他所有下拉菜单
        if (!isActive) {
          closeAllDropdowns();
        }
        // 切换当前下拉菜单状态
        parentItem.classList.toggle('active', !isActive);
        // 应用平滑高度过渡
        if (dropdown) {
          dropdown.style.maxHeight = isActive ? '0' : `${dropdown.scrollHeight}px`;
        }
      }
    });
  });

  // 点击菜单项后关闭菜单（针对移动端）
  mainMenu?.querySelectorAll('a:not(.has-dropdown > a)').forEach(link => {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768 && isMenuOpen) {
        closeAllDropdowns();
        toggleMenu(true);
      }
    });
  });

  // 窗口大小改变时重置菜单状态
  function handleResize() {
    const isMobile = window.innerWidth <= 768;
    // 桌面端状态重置
    if (!isMobile) {
      if (isMenuOpen) {
        toggleMenu(true);
      }
      closeAllDropdowns();
      // 恢复下拉菜单默认行为
      dropdownMenus.forEach(menu => {
        menu.style.maxHeight = '';
      });
    } 
    // 移动端状态初始化
    else {
      dropdownMenus.forEach(menu => {
        const parent = menu.closest('.has-dropdown');
        menu.style.maxHeight = parent?.classList.contains('active') 
          ? `${menu.scrollHeight}px` 
          : '0';
      });
    }
  }

  // 初始化时执行一次
  handleResize();

  // 监听窗口大小变化（防抖优化）
  let resizeTimeout;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(handleResize, 150);
  });

  // 支持键盘Esc关闭菜单
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isMenuOpen) {
      closeAllDropdowns();
      toggleMenu(true);
    }
    // Esc键可单独关闭下拉菜单（即使主菜单未打开）
    else if (e.key === 'Escape' && window.innerWidth <= 768) {
      closeAllDropdowns();
    }
  });
}

// 初始化图片懒加载
function initLazyLoad() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    lazyImages.forEach(img => img.style.opacity = '0');
    
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.getAttribute('data-src');
                if (src) {
                    const newImg = new Image();
                    newImg.src = src;
                    newImg.onload = () => {
                        img.src = src;
                        let opacity = 0;
                        const fadeInInterval = setInterval(() => {
                            opacity += 0.2;
                            if (opacity <= 1) {
                                img.style.opacity = opacity;
                            } else {
                                clearInterval(fadeInInterval);
                            }
                        }, 100);
                        img.removeAttribute('data-src');
                        
                        // 找到包含此图片的灯箱链接
                        const lightboxLink = img.closest('a[data-fancybox]');
                        
                        // 如果找到灯箱链接，重新绑定Fancybox
                        if (lightboxLink) {
                            // 先销毁之前的Fancybox实例
                            Fancybox.destroy();
                            
                            // 重新绑定Fancybox
                            Fancybox.bind('[data-fancybox]', {
                              // 自定义Fancybox配置
                            });  
                        }
                    };
                }
                observer.unobserve(img);
            }
        });
    });

    lazyImages.forEach(image => {
        observer.observe(image);
    });
}

// 初始化任务列表
function initTaskList() {
    const lists = document.querySelectorAll('.post-content ul, .post-content ol');
    lists.forEach(list => {
        const listItems = list.querySelectorAll('li');
        listItems.forEach(li => {
            const html = li.innerHTML.trim();
            const match = html.match(/^\[([ xX])\]\s+(.*)$/);
            if (match) {
                const isDone = match[1].toLowerCase() === 'x';
                const icon = isDone
                   ? '<i class="fa fa-check-square" aria-hidden="true" style="color:#4caf50"></i>'
                    : '<i class="fa fa-square-o" aria-hidden="true"></i>';
                const text = isDone
                   ? '<span class="task-done">' + match[2] + '</span>'
                    : match[2];
                li.innerHTML = icon + ' ' + text;
                li.classList.add('task-list-item');
            }
        });
    });
}

// 初始化返回顶部按钮
function initBackToTopButton() {
    const backToTopButton = document.getElementById('backToTop');
    const scrollThreshold = 200;

    if (!backToTopButton) return;

    backToTopButton.classList.remove('visible');

    window.addEventListener('scroll', () => {
        if (window.scrollY > scrollThreshold) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    });

    backToTopButton.addEventListener('click', function (e) {
        e.preventDefault();
        if (window.scrollTo && typeof window.scrollTo === 'function') {
            if ('scrollBehavior' in document.documentElement.style) {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            } else {
                smoothScrollPolyfill();
            }
        } else {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    });

    function smoothScrollPolyfill() {
        const start = window.scrollY;
        const duration = 500;
        let startTime = null;

        function animation(currentTime) {
            if (!startTime) startTime = currentTime;
            const timeElapsed = currentTime - startTime;
            const progress = Math.min(timeElapsed / duration, 1);
            const easeProgress = easeInOutCubic(progress);
            window.scrollTo(0, start - (start * easeProgress));

            if (timeElapsed < duration) {
                requestAnimationFrame(animation);
            }
        }

        function easeInOutCubic(t) {
            return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
        }

        requestAnimationFrame(animation);
    }
}

// 初始化代码复制按钮
function initCodeCopyButtons() {
    const codeblocks = document.getElementsByTagName("pre");

    function addCopyButton(codeBlock) {
        codeBlock.style.position = "relative";
        const copy = document.createElement("div");
        copy.style.cssText = `
            position: absolute;
            right: 4px;
            top: 4px;
            background-color: white;
            padding: 2px 8px;
            margin: 8px;
            border-radius: 4px;
            cursor: pointer;
            z-index: 9999;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05), 0 2px 4px rgba(0,0,0,0.05);
        `;
        copy.textContent = "复制";
        copy.style.visibility = "hidden";
        codeBlock.appendChild(copy);
        return copy;
    }

    function showCopyButton(copyButton) {
        copyButton.style.visibility = "visible";
    }

    function hideCopyButton(copyButton) {
        copyButton.style.visibility = "hidden";
    }

    async function copyCodeToClipboard(codeBlock, copyButton) {
        try {
            const textToCopy = codeBlock.childNodes[0].textContent;
            await navigator.clipboard.writeText(textToCopy);
            copyButton.textContent = "复制成功";
            setTimeout(() => {
                copyButton.textContent = "复制";
            }, 1000);
        } catch (err) {
            console.error('复制失败: ', err);
        }
    }

    for (let i = 0; i < codeblocks.length; i++) {
        const codeBlock = codeblocks[i];
        const copyButton = addCopyButton(codeBlock);

        codeBlock.addEventListener('mouseover', () => {
            showCopyButton(copyButton);
        });

        codeBlock.addEventListener('mouseout', () => {
            hideCopyButton(copyButton);
        });

        copyButton.addEventListener('click', (event) => {
            copyCodeToClipboard(codeBlock, copyButton);
        });
    }
}

// Cookie 操作函数
function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + d.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/;SameSite=Strict";
}

function getCookie(name) {
    const arr = document.cookie.split(';');
    for (let i = 0; i < arr.length; i++) {
        const c = arr[i].trim();
        if (c.indexOf(name + "=") === 0) return c.substring(name.length + 1, c.length);
    }
    return "";
}

// 初始化文章分享功能
function initArticleSharing() {
    const shareBtn = document.getElementById('share');
    if (shareBtn) {
        const shareTitle = shareBtn.getAttribute('data-title') || document.title;
        const shareUrl = shareBtn.getAttribute('data-url') || window.location.href;

        shareBtn.addEventListener('click', async function () {
            const shareText = `${shareTitle}\n${shareUrl}`;

            try {
                if (navigator.clipboard) {
                    await navigator.clipboard.writeText(shareText);
                    Qmsg.success('已复制分享链接！');
                    return;
                }
                throw new Error('Clipboard API 不可用');
            } catch (error) {
                console.error('复制失败:', error);
                const tempElement = document.createElement('div');
                tempElement.textContent = shareText;
                tempElement.style.position = 'fixed';
                tempElement.style.opacity = '0';
                document.body.appendChild(tempElement);

                try {
                    const selection = window.getSelection();
                    const range = document.createRange();
                    range.selectNodeContents(tempElement);

                    if (!selection) throw new Error('Selection API 不可用');

                    selection.removeAllRanges();
                    selection.addRange(range);

                    if (navigator.clipboard && typeof ClipboardItem !== 'undefined') {
                        const clipboardItem = new ClipboardItem({ 'text/plain': new Blob([shareText], { type: 'text/plain' }) });
                        await navigator.clipboard.write([clipboardItem]);
                        Qmsg.success('已复制分享链接！');
                        return;
                    }
                    throw new Error('高级复制方法不可用');
                } catch (err) {
                    console.error('现代回退方案失败:', err);
                    Qmsg.info('请手动复制：' + shareText);
                } finally {
                    document.body.removeChild(tempElement);
                    window.getSelection()?.removeAllRanges();
                }
            }
        });
    }
}

// 初始化文章点赞功能
function initArticleLiking() {
    const likeBtn = document.getElementById('like');
    const likeCount = document.getElementById('like-count');

    if (likeBtn && likeCount) {
        const cid = likeBtn.getAttribute('data-cid');
        const likeUrl = likeBtn.getAttribute('data-like-url');
        const getLikeUrl = likeBtn.getAttribute('data-get-like-url');

        if (!cid || !likeUrl || !getLikeUrl) {
            console.error('缺少必要的点赞数据属性');
            return;
        }

        // 获取初始点赞数
        fetch(`${getLikeUrl}&cid=${cid}`)
           .then(res => res.json())
           .then(data => {
                if (data.count!== undefined) likeCount.textContent = data.count;
            });

        // 点赞按钮点击事件
        likeBtn.addEventListener('click', function () {
            if (getCookie(`liked_${cid}`)) {
                Qmsg.warning('您已经点过赞啦！');
                return;
            }

            fetch(`${likeUrl}&cid=${cid}`)
               .then(res => res.json())
               .then(data => {
                    if (data.success) {
                        likeCount.textContent = data.count;
                        setCookie(`liked_${cid}`, '1', 365);
                        Qmsg.success('点赞成功，感谢支持！');
                    } else {
                        Qmsg.warning(data.msg || '点赞失败');
                    }
                });
        });
    }
}

// 初始化折叠面板
function initDetailsPanels() {
    const detailsElements = document.querySelectorAll('details');
    detailsElements.forEach(detail => {
        detail.addEventListener('toggle', function () {
            if (this.open) {
                detailsElements.forEach(otherDetail => {
                    if (otherDetail!== this) {
                        otherDetail.open = false;
                    }
                });
            }
        });
    });
}