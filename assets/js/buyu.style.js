// 开源不易，请尊重作者版权，保留本信息
function showConsoleInfo() {
    const version = 'v1.3.1';
    const copyright = 'buyu 主题';
    console.log('\n' + ' %c ' + copyright + ' ' + version + ' %c https://zhuxu.asia/  ' + '\n', 'color: #fadfa3; background: #030307; padding:5px 0;', 'background: #fadfa3; padding:5px 0;');
    console.log('开源不易，请尊重作者版权，保留基本的版权信息。');
}
// 调用函数
showConsoleInfo();

document.addEventListener('DOMContentLoaded', function () {
    // 初始化所有功能模块
    initLazyLoad();
    initTaskList();
    initBackToTopButton();
    initCodeCopyButtons();
    initArticleSharing();
    initArticleLiking();
    initDetailsPanels();
    initNavigationMenu();
    initRewardModal(); // 初始化文章打赏
    initCollapsePanels(); // 初始化折叠面板
    initTabs(); // 初始化tabs标签页
});

// 初始化tabs标签页
function initTabs() {
    const tabsGroups = document.querySelectorAll('.shortcode-tabs');
    if (!tabsGroups.length) return;
    
    tabsGroups.forEach(tabs => {
        const navItems = tabs.querySelectorAll('.tabs-item');
        const panels = tabs.querySelectorAll('.tabs-panel');
        
        navItems.forEach(item => {
            // 点击切换标签
            item.addEventListener('click', () => {
                const index = parseInt(item.getAttribute('data-index'));
                
                // 更新导航状态
                navItems.forEach(nav => nav.classList.remove('tabs-item-active'));
                item.classList.add('tabs-item-active');
                
                // 更新内容面板
                panels.forEach(panel => panel.classList.remove('tabs-panel-active'));
                panels[index].classList.add('tabs-panel-active');
            });
            
            // 键盘支持
            item.setAttribute('tabindex', '0');
            item.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    item.click();
                }
            });
        });
    });
}

// 折叠面板交互逻辑
function initCollapsePanels() {
    const headers = document.querySelectorAll(".collapse-header");
    
    if (headers.length === 0) return;
    
    headers.forEach(header => {
        const panel = header.nextElementSibling;
        const collapseContainer = header.closest(".shortcode-collapse");
        
        if (!panel || !collapseContainer) return;
        
        // 初始化状态
        const isOpen = collapseContainer.classList.contains("collapse-open");
        header.setAttribute("aria-expanded", isOpen);
        
        // 状态切换处理函数
        const toggleState = () => {
            const isOpenNow = collapseContainer.classList.contains("collapse-open");
            // 切换容器状态类（触发CSS旋转动画）
            collapseContainer.classList.toggle("collapse-open");
            // 切换内容显示
            panel.classList.toggle("hidden");
            // 更新无障碍属性
            header.setAttribute("aria-expanded", !isOpenNow);
        };
        
        // 点击切换事件
        header.addEventListener("click", toggleState);
        
        // 键盘支持（直接调用切换函数，避免模拟click）
        header.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                toggleState();
            }
        });
    });
}

// 初始化折叠面板(details元素)
function initDetailsPanels() {
    const detailsElements = document.querySelectorAll('details');
    if (detailsElements.length === 0) return; // 无元素则直接返回
    
    detailsElements.forEach(detail => {
        detail.addEventListener('toggle', function () {
            if (this.open) {
                // 关闭其他已展开的details（使用for循环提升性能）
                for (let i = 0; i < detailsElements.length; i++) {
                    const otherDetail = detailsElements[i];
                    if (otherDetail !== this && otherDetail.open) {
                        otherDetail.open = false;
                    }
                }
            }
        });
    });
}

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

// 初始化代码块复制按钮
function initCodeCopyButtons() {
    const codeblocks = document.getElementsByTagName("pre");

    function addCopyButton(codeBlock) {
        // 确保代码块相对定位，使按钮可以放在内部
        codeBlock.style.position = "relative";
        
        // 创建复制按钮
        const copy = document.createElement("div");
        copy.style.cssText = `
            position: absolute;
            right: 8px;
            top: 8px;
            cursor: pointer;
            z-index: 9999;
            background: none;
            border: none;
            padding: 0;
            margin: 0;
            box-shadow: none;
            font: inherit;
            color: inherit;
        `;
        copy.textContent = "复制";
        codeBlock.appendChild(copy);
        return copy;
    }

    async function copyCodeToClipboard(codeBlock, copyButton) {
        try {
            // 获取代码块内的文本内容
            const codeElement = codeBlock.querySelector('code') || codeBlock.childNodes[0];
            const textToCopy = codeElement.textContent;
            
            await navigator.clipboard.writeText(textToCopy);
            copyButton.textContent = "复制成功";
            // 1秒后恢复文本
            setTimeout(() => {
                copyButton.textContent = "复制";
            }, 1000);
        } catch (err) {
            console.error('复制失败: ', err);
            copyButton.textContent = "复制失败";
            setTimeout(() => {
                copyButton.textContent = "复制";
            }, 1000);
        }
    }

    // 为所有代码块添加按钮
    for (let i = 0; i < codeblocks.length; i++) {
        const codeBlock = codeblocks[i];
        const copyButton = addCopyButton(codeBlock);
        copyButton.addEventListener('click', () => {
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

// 初始化打赏模态框功能
function initRewardModal() {
    // 获取元素
    const rewardModal = document.getElementById('reward-modal');
    const rewardContent = document.getElementById('reward-content');
    const closeReward = document.getElementById('close-reward');
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    const body = document.body;

    // 显示模态框
    function showReward() {
        // 保存当前滚动位置
        const scrollTop = body.scrollTop || document.documentElement.scrollTop;
        
        // 显示并触发动画
        rewardModal.style.display = 'flex';
        setTimeout(() => {
            rewardModal.style.opacity = '1';
            rewardContent.style.transform = 'scale(1)';
            rewardContent.style.opacity = '1';
        }, 10);
        
        // 禁止页面滚动
        body.style.overflow = 'hidden';
        body.style.position = 'fixed';
        body.style.width = '100%';
        body.style.top = `-${scrollTop}px`;
        body.setAttribute('data-scrolltop', scrollTop);
    }

    // 关闭模态框
    function hideReward() {
        // 触发关闭动画
        rewardModal.style.opacity = '0';
        rewardContent.style.transform = 'scale(0.95)';
        rewardContent.style.opacity = '0';
        
        // 恢复页面滚动和位置
        setTimeout(() => {
            rewardModal.style.display = 'none';
            const scrollTop = body.getAttribute('data-scrolltop');
            body.style.overflow = '';
            body.style.position = '';
            body.style.width = '';
            body.style.top = '';
            window.scrollTo(0, scrollTop);
        }, 300);
    }

    // 标签页切换逻辑
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // 移除所有active类
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.setAttribute('aria-selected', 'false');
            });
            tabContents.forEach(content => content.classList.remove('active'));
            
            // 添加当前active类
            button.classList.add('active');
            button.setAttribute('aria-selected', 'true');
            const tabId = button.getAttribute('data-tab');
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
        
        // 键盘支持
        button.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                button.click();
            }
        });
    });

    // 事件绑定
    if (closeReward) {
        closeReward.addEventListener('click', hideReward);
    }
    
    // 点击遮罩层关闭
    if (rewardModal) {
        rewardModal.addEventListener('click', (e) => {
            if (e.target === rewardModal) {
                hideReward();
            }
        });
    }
    
    // ESC键关闭
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && rewardModal && rewardModal.style.display === 'flex') {
            hideReward();
        }
    });

    // 暴露全局方法供外部调用
    window.showReward = showReward;
}