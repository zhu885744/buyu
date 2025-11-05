// 开源不易，请请尊重作者版权，保留本信息
function showConsoleInfo() {
    const version = 'v1.3.1';
    const copyright = 'buyu 主题';
    console.log('\n' + ' %c ' + copyright + ' ' + version + ' %c https://zhuxu.asia/  ' + '\n', 'color: #fadfa3; background: #030307; padding:5px 0;', 'background: #fadfa3; padding:5px 0;');
    console.log('开源不易，请尊重作者版权，保留基本的版权信息。');
}
showConsoleInfo();

// 工具函数：简化DOM操作
const $ = (selector, parent = document) => parent.querySelector(selector);
const $$ = (selector, parent = document) => [...parent.querySelectorAll(selector)];

document.addEventListener('DOMContentLoaded', () => {
    // 初始化所有功能模块（按执行优先级排序）
    initNavigationMenu();
    initLazyLoad();
    initTaskList();
    initBackToTopButton();
    initCodeCopyButtons();
    initArticleSharing();
    initArticleLiking();
    initDetailsPanels();
    initRewardModal();
    initCollapsePanels();
    initTabs();
    initThemeToggle();
});

// 初始化浅色深色模式切换功能
function initThemeToggle() {
    // 常量定义
    const THEME_KEY = 'preferred-theme';
    const TRANSITION_DURATION = 600; // 图标过渡时间(ms)
    const TRANSITION_CLASS = 'theme-transition';
    const SYSTEM_DARK_QUERY = '(prefers-color-scheme: dark)';

    // DOM元素获取（使用现有工具函数）
    const htmlElement = document.documentElement;
    const themeToggle = $('#theme-toggle');
    const lightIcon = $('.light-icon', themeToggle);
    const darkIcon = $('.dark-icon', themeToggle);

    // 元素验证
    if (!themeToggle || !lightIcon || !darkIcon) return;

    // 主题切换核心函数
    const updateThemeUI = (theme, showNotification = false) => {
        const isDark = theme === 'dark';
        
        // 更新根元素主题属性
        htmlElement.setAttribute('data-bs-theme', theme);
        
        // 设置图标过渡样式
        const transitionStyle = `opacity ${TRANSITION_DURATION}ms ease, transform ${TRANSITION_DURATION}ms ease`;
        lightIcon.style.transition = transitionStyle;
        darkIcon.style.transition = transitionStyle;
        
        // 统一更新图标状态（减少分支判断）
        lightIcon.style.opacity = isDark ? '0' : '1';
        lightIcon.style.transform = isDark ? 'translateY(10px)' : 'translateY(0)';
        darkIcon.style.opacity = isDark ? '1' : '0';
        darkIcon.style.transform = isDark ? 'translateY(0)' : 'translateY(10px)';

        // 显示通知
        if (showNotification) {
            Qmsg.info(`已切换至${isDark ? '深色' : '浅色'}模式`);
        }
        
        // 触发全局主题变化事件
        document.dispatchEvent(new CustomEvent('themechanged', { 
            detail: { theme } 
        }));
    };

    // 保存主题偏好
    const saveTheme = (theme) => {
        localStorage.setItem(THEME_KEY, theme);
    };

    // 切换主题（用户主动操作）
    const toggleTheme = () => {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        htmlElement.classList.add(TRANSITION_CLASS);
        updateThemeUI(newTheme, true);
        saveTheme(newTheme);
        
        setTimeout(() => {
            htmlElement.classList.remove(TRANSITION_CLASS);
        }, TRANSITION_DURATION / 2);
    };

    // 初始化主题（读取缓存或系统偏好）
    const initTheme = () => {
        const savedTheme = localStorage.getItem(THEME_KEY);
        if (savedTheme) {
            updateThemeUI(savedTheme);
        } else {
            const prefersDark = window.matchMedia(SYSTEM_DARK_QUERY).matches;
            updateThemeUI(prefersDark ? 'dark' : 'light');
        }
    };

    // 处理系统主题变化
    const handleSystemThemeChange = (e) => {
        if (!localStorage.getItem(THEME_KEY)) {
            const newTheme = e.matches ? 'dark' : 'light';
            updateThemeUI(newTheme, true);
        }
    };

    // 处理主题变化时的元素样式更新
    const handleThemeChanged = (e) => {
        const isDark = e.detail.theme === 'dark';
        
        // 图片样式处理（使用工具函数$$）
        $$('img').forEach(img => {
            img.style.transition = 'filter 0.3s ease';
            img.style.filter = isDark ? 'brightness(0.9)' : 'brightness(1)';
        });

        // 代码块样式处理
        $$('pre').forEach(pre => {
            pre.style.transition = 'background-color 0.3s ease';
        });

        // 打赏模态框处理（使用工具函数$）
        const rewardModal = $('#reward-modal');
        if (rewardModal) {
            rewardModal.style.transition = 'background-color 0.3s ease';
        }
    };

    // 绑定事件
    themeToggle.addEventListener('click', toggleTheme);
    themeToggle.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleTheme();
        }
    });

    // 系统主题监听
    window.matchMedia(SYSTEM_DARK_QUERY).addEventListener('change', handleSystemThemeChange);
    
    // 主题变化事件监听
    document.addEventListener('themechanged', handleThemeChanged);

    // 初始化执行
    initTheme();
}

// 初始化tabs标签页
function initTabs() {
    $$('.shortcode-tabs').forEach(tabs => {
        const tabsId = tabs.id;
        const navItems = $$(`.tabs-item[data-tabs-id="${tabsId}"]`, tabs);
        const panels = $$(`.tabs-panel[data-tabs-id="${tabsId}"]`, tabs);
        
        navItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                navItems.forEach(nav => nav.classList.remove('tabs-item-active'));
                panels.forEach(panel => panel.classList.remove('tabs-panel-active'));
                item.classList.add('tabs-item-active');
                panels[index].classList.add('tabs-panel-active');
            });
            
            item.setAttribute('tabindex', '0');
            item.addEventListener('keydown', (e) => {
                if (['Enter', ' '].includes(e.key)) {
                    e.preventDefault();
                    item.click();
                }
            });
        });
    });
}

// 初始化折叠面板
function initCollapsePanels() {
    $$(".collapse-header").forEach(header => {
        const panel = header.nextElementSibling;
        const collapseContainer = header.closest(".shortcode-collapse");
        if (!panel || !collapseContainer) return;
        
        // 初始化状态
        const isOpen = collapseContainer.classList.contains("collapse-open");
        header.setAttribute("aria-expanded", isOpen);
        if (!isOpen) panel.classList.add('hidden');
        
        // 状态切换处理函数
        const toggleState = () => {
            const isOpenNow = collapseContainer.classList.toggle("collapse-open");
            panel.classList.toggle("hidden", !isOpenNow);
            header.setAttribute("aria-expanded", isOpenNow);
        };
        
        // 点击和键盘事件
        header.addEventListener("click", toggleState);
        header.addEventListener("keydown", (e) => {
            if (['Enter', ' '].includes(e.key)) {
                e.preventDefault();
                toggleState();
            }
        });
    });
}

// 初始化details折叠面板（互斥展开）
function initDetailsPanels() {
    const detailsElements = $$('details');
    if (!detailsElements.length) return;
    
    detailsElements.forEach(detail => {
        detail.addEventListener('toggle', function () {
            if (this.open) {
                detailsElements.forEach(other => {
                    if (other !== this && other.open) other.open = false;
                });
            }
        });
    });
}

// 导航菜单（动态创建遮罩层，优化移动端体验）
function initNavigationMenu() {
    const menuToggle = $('.menu-toggle');
    const mainMenu = $('.main-menu');
    let navOverlay = null; // 动态遮罩层
    let isMenuOpen = false;

    // 创建遮罩层
    const createOverlay = () => {
        if (navOverlay) return;
        navOverlay = document.createElement('div');
        navOverlay.className = 'nav-overlay';
        navOverlay.id = 'navOverlay';
        navOverlay.setAttribute('aria-hidden', 'true');
        document.body.appendChild(navOverlay);
        navOverlay.addEventListener('click', () => toggleMenu(true));
    };

    // 移除遮罩层
    const removeOverlay = () => {
        if (navOverlay) {
            navOverlay.removeEventListener('click', () => toggleMenu(true));
            navOverlay.remove();
            navOverlay = null;
        }
    };

    // 切换菜单状态
    const toggleMenu = (forceClose = false) => {
        if (!mainMenu) return;
        const shouldOpen = forceClose ? false : !isMenuOpen;
        
        mainMenu.classList.toggle('active', shouldOpen);
        document.body.classList.toggle('overflow-hidden', shouldOpen);
        
        // 控制遮罩层
        if (shouldOpen) {
            createOverlay();
            setTimeout(() => navOverlay?.classList.add('active'), 10); // 延迟触发动画
        } else {
            navOverlay?.classList.remove('active');
            setTimeout(removeOverlay, 300); // 等待动画结束
        }
        
        isMenuOpen = shouldOpen;
        menuToggle?.classList.toggle('active', shouldOpen);
    };

    // 关闭所有下拉菜单
    const closeAllDropdowns = () => {
        $$('.has-dropdown.active').forEach(item => {
            item.classList.remove('active');
            const dropdown = $('.dropdown', item);
            if (dropdown) dropdown.style.maxHeight = '0';
        });
    };

    // 汉堡菜单点击事件
    menuToggle?.addEventListener('click', () => toggleMenu());

    // 处理下拉菜单
    $$('.has-dropdown > a').forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            if (window.innerWidth > 768) return; // 桌面端不处理
            
            e.preventDefault();
            const parentItem = this.parentElement;
            const dropdown = this.nextElementSibling;
            const isActive = parentItem.classList.contains('active');
            
            if (!isActive) closeAllDropdowns(); // 关闭其他下拉
            parentItem.classList.toggle('active', !isActive);
            
            // 平滑高度过渡
            if (dropdown) {
                dropdown.style.maxHeight = isActive ? '0' : `${dropdown.scrollHeight}px`;
            }
        });
    });

    // 移动端点击菜单项后关闭菜单
    $$('a:not(.has-dropdown > a)', mainMenu).forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768 && isMenuOpen) {
                closeAllDropdowns();
                toggleMenu(true);
            }
        });
    });

    // 窗口大小变化处理（防抖）
    const handleResize = () => {
        const isMobile = window.innerWidth <= 768;
        if (!isMobile && isMenuOpen) {
            toggleMenu(true); // 桌面端关闭菜单
        }
        closeAllDropdowns();
        
        // 重置下拉菜单样式
        $$('.dropdown').forEach(menu => {
            menu.style.maxHeight = isMobile ? '0' : '';
        });
    };

    handleResize(); // 初始化执行
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(handleResize, 150);
    });

    // ESC键关闭
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAllDropdowns();
            if (isMenuOpen) toggleMenu(true);
        }
    });
}

// 初始化图片懒加载
function initLazyLoad() {
    const lazyImages = $$('img[data-src]');
    if (!lazyImages.length) return;

    // 初始隐藏图片
    lazyImages.forEach(img => {
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.5s ease'; // 统一过渡效果
    });

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.getAttribute('data-src');
                if (!src) return;

                // 预加载图片
                const newImg = new Image();
                newImg.src = src;
                newImg.onload = () => {
                    img.src = src;
                    img.removeAttribute('data-src');
                    // 淡入显示（使用CSS过渡替代JS定时器）
                    setTimeout(() => img.style.opacity = '1', 50);

                    // 重新绑定灯箱
                    const lightboxLink = img.closest('a[data-fancybox]');
                    if (lightboxLink && window.Fancybox) {
                        Fancybox.destroy();
                        Fancybox.bind("[data-fancybox]", {
                            Carousel: {
                                Zoomable: {
                                    Panzoom: { protected: true }
                                }
                            }
                        });
                    }
                };
                observer.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => observer.observe(img));
}

// 初始化任务列表
function initTaskList() {
    $$('.post-content ul, .post-content ol').forEach(list => {
        $$('li', list).forEach(li => {
            const html = li.innerHTML.trim();
            const match = html.match(/^\[([ xX])\]\s+(.*)$/);
            if (match) {
                const isDone = match[1].toLowerCase() === 'x';
                const icon = isDone 
                    ? '<i class="fa fa-check-square" aria-hidden="true" style="color:#4caf50"></i>'
                    : '<i class="fa fa-square-o" aria-hidden="true"></i>';
                const text = isDone 
                    ? `<span class="task-done">${match[2]}</span>`
                    : match[2];
                li.innerHTML = `${icon} ${text}`;
                li.classList.add('task-list-item');
            }
        });
    });
}

// 初始化返回顶部按钮（动态生成）
function initBackToTopButton() {
    // 动态创建返回顶部按钮元素
    const backToTopButton = document.createElement('button');
    backToTopButton.id = 'backToTop';
    backToTopButton.className = 'back-to-top';
    backToTopButton.setAttribute('aria-label', '返回页面顶部');
    backToTopButton.setAttribute('title', '点击返回顶部');
    backToTopButton.setAttribute('tabindex', '0');
    backToTopButton.innerHTML = '<i class="fa fa-arrow-up" aria-hidden="true"></i>';
    
    // 添加到页面body中
    document.body.appendChild(backToTopButton);

    // 配置参数
    const scrollThreshold = 200; // 滚动超过200px显示按钮
    backToTopButton.classList.remove('visible'); // 初始隐藏

    // 监听滚动事件：控制按钮显示/隐藏
    window.addEventListener('scroll', () => {
        backToTopButton.classList.toggle('visible', window.scrollY > scrollThreshold);
    });

    // 点击事件：平滑滚动到顶部
    backToTopButton.addEventListener('click', (e) => {
        e.preventDefault();
        // 现代浏览器支持原生平滑滚动
        if ('scrollBehavior' in document.documentElement.style) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            // 降级方案：手动实现平滑滚动动画
            const start = window.scrollY;
            const duration = 500; // 动画时长500ms
            let startTime = null;

            // 动画函数
            const animation = (currentTime) => {
                startTime = startTime || currentTime;
                const timeElapsed = currentTime - startTime;
                const progress = Math.min(timeElapsed / duration, 1); // 进度0-1
                // 缓动函数：先慢后快再慢
                const easeInOutCubic = (t) => 
                    t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
                // 计算当前滚动位置
                window.scrollTo(0, start - (start * easeInOutCubic(progress)));
                // 未完成则继续动画
                if (timeElapsed < duration) {
                    requestAnimationFrame(animation);
                }
            };

            // 启动动画
            requestAnimationFrame(animation);
        }
    });

    // 键盘支持：按Enter或空格键触发点击
    backToTopButton.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            backToTopButton.click();
        }
    });
}

// 初始化代码块复制按钮
function initCodeCopyButtons() {
    $$('pre').forEach(codeBlock => {
        // 避免重复添加按钮
        if ($('.copy-btn', codeBlock)) return;

        // 设置代码块定位
        codeBlock.style.position = 'relative';
        
        // 创建复制按钮
        const copyBtn = document.createElement('button');
        copyBtn.className = 'copy-btn';
        copyBtn.textContent = '复制';
        copyBtn.style.cssText = `
            position: absolute; right: 8px; top: 8px;
            cursor: pointer; z-index: 9999;
            background: rgba(0,0,0,0.5); color: white;
            border: none; border-radius: 4px; padding: 4px 8px;
            font-size: 12px; transition: background 0.2s;
        `;
        codeBlock.appendChild(copyBtn);

        // 复制逻辑
        copyBtn.addEventListener('click', async () => {
            try {
                const codeElement = $('.code', codeBlock) || codeBlock.firstElementChild;
                const textToCopy = codeElement?.textContent || '';
                if (!textToCopy) throw new Error('无代码内容');

                await navigator.clipboard.writeText(textToCopy);
                copyBtn.textContent = '复制成功';
            } catch (err) {
                console.error('复制失败:', err);
                copyBtn.textContent = '复制失败';
            } finally {
                setTimeout(() => copyBtn.textContent = '复制', 1500);
            }
        });
    });
}

// Cookie 操作工具
const Cookie = {
    set: (name, value, days) => {
        const d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${d.toUTCString()};path=/;SameSite=Strict`;
    },
    get: (name) => {
        const arr = document.cookie.split(';');
        for (let c of arr) {
            c = c.trim();
            if (c.startsWith(`${name}=`)) return c.slice(name.length + 1);
        }
        return '';
    }
};

// 初始化文章分享功能
function initArticleSharing() {
    const shareBtn = $('#share');
    if (!shareBtn) return;

    const shareTitle = shareBtn.dataset.title || document.title;
    const shareUrl = shareBtn.dataset.url || window.location.href;
    const shareText = `${shareTitle}\n${shareUrl}`;

    shareBtn.addEventListener('click', async () => {
        try {
            if (navigator.clipboard) {
                await navigator.clipboard.writeText(shareText);
                Qmsg.success('已复制分享链接！');
                return;
            }
            throw new Error('Clipboard API 不可用');
        } catch (error) {
            console.error('复制失败:', error);
            // 降级方案：创建临时元素选中复制
            const temp = document.createElement('textarea');
            temp.value = shareText;
            temp.style.cssText = 'position:fixed;opacity:0';
            document.body.appendChild(temp);
            temp.select();
            
            try {
                document.execCommand('copy');
                Qmsg.success('已复制分享链接！');
            } catch (err) {
                Qmsg.info(`请手动复制：${shareText}`);
            } finally {
                document.body.removeChild(temp);
            }
        }
    });
}

// 初始化文章点赞功能
function initArticleLiking() {
    const likeBtn = $('#like');
    const likeCount = $('#like-count');
    if (!likeBtn || !likeCount) return;

    const { cid, likeUrl, getLikeUrl } = likeBtn.dataset;
    if (!cid || !likeUrl || !getLikeUrl) {
        console.error('缺少点赞必要参数');
        return;
    }

    // 获取初始点赞数
    fetch(`${getLikeUrl}&cid=${cid}`)
        .then(res => res.ok ? res.json() : { count: 0 })
        .then(data => {
            if (data.count !== undefined) likeCount.textContent = data.count;
        })
        .catch(err => console.error('获取点赞数失败:', err));

    // 点赞事件
    likeBtn.addEventListener('click', () => {
        if (Cookie.get(`liked_${cid}`)) {
            Qmsg.warning('您已经点过赞啦！');
            return;
        }

        fetch(`${likeUrl}&cid=${cid}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    likeCount.textContent = data.count;
                    Cookie.set(`liked_${cid}`, '1', 365);
                    Qmsg.success('点赞成功，感谢支持！');
                } else {
                    Qmsg.warning(data.msg || '点赞失败');
                }
            })
            .catch(err => {
                console.error('点赞请求失败:', err);
                Qmsg.error('网络错误，点赞失败');
            });
    });
}

// 初始化打赏模态框
function initRewardModal() {
    const rewardModal = $('#reward-modal');
    const rewardContent = $('#reward-content');
    const closeReward = $('#close-reward');
    if (!rewardModal || !rewardContent) return;

    // 标签页切换
    $$('.tab-btn').forEach(button => {
        button.addEventListener('click', () => {
            $$('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.setAttribute('aria-selected', 'false');
            });
            $$('.tab-content').forEach(content => content.classList.remove('active'));
            
            button.classList.add('active');
            button.setAttribute('aria-selected', 'true');
            $(`#${button.dataset.tab}-tab`).classList.add('active');
        });

        button.addEventListener('keydown', (e) => {
            if (['Enter', ' '].includes(e.key)) {
                e.preventDefault();
                button.click();
            }
        });
    });

    // 显示模态框
    const showReward = () => {
        const scrollTop = window.scrollY;
        rewardModal.style.display = 'flex';
        rewardModal.style.opacity = '0'; // 重置透明度
        rewardContent.style.transform = 'scale(0.95)';
        rewardContent.style.opacity = '0';

        // 触发动画
        setTimeout(() => {
            rewardModal.style.opacity = '1';
            rewardContent.style.transform = 'scale(1)';
            rewardContent.style.opacity = '1';
        }, 10);

        // 锁定滚动
        document.body.style.cssText = `
            overflow: hidden; position: fixed;
            width: 100%; top: -${scrollTop}px;
        `;
        document.body.dataset.scrolltop = scrollTop;
    };

    // 隐藏模态框
    const hideReward = () => {
        rewardModal.style.opacity = '0';
        rewardContent.style.transform = 'scale(0.95)';
        rewardContent.style.opacity = '0';

        setTimeout(() => {
            rewardModal.style.display = 'none';
            const scrollTop = document.body.dataset.scrolltop || 0;
            document.body.style = ''; // 重置样式
            window.scrollTo(0, scrollTop);
        }, 300);
    };

    // 事件绑定
    closeReward?.addEventListener('click', hideReward);
    rewardModal.addEventListener('click', (e) => {
        if (e.target === rewardModal) hideReward();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && rewardModal.style.display === 'flex') {
            hideReward();
        }
    });

    // 暴露全局方法
    window.showReward = showReward;
}

// 评论表情相关
"use strict";
function _classCallCheck(e, t) {
    if (!(e instanceof t))
        throw new TypeError("Cannot call a class as a function")
}
var _createClass = function() {
    function e(e, t) {
        for (var a = 0; a < t.length; a++) {
            var s = t[a];
            s.enumerable = s.enumerable || !1,
            s.configurable = !0,
            "value"in s && (s.writable = !0),
            Object.defineProperty(e, s.key, s)
        }
    }
    return function(t, a, s) {
        return a && e(t.prototype, a),
        s && e(t, s),
        t
    }
}();
!function() {
    var e = function() {
        function e(t) {
            var a = this;
            _classCallCheck(this, e);
            var s = {
                logo: "OwO",
                container: document.getElementsByClassName("OwO")[0],
                target: document.getElementsByTagName("textarea")[0],
                position: "down",
                width: "100%",
                maxHeight: "250px",
                api: "https://cdn.zhuxu.asia/OwO.json"
            };
            for (var n in s)
                s.hasOwnProperty(n) && !t.hasOwnProperty(n) && (t[n] = s[n]);
            this.container = t.container,
            this.target = t.target,
            "up" === t.position && this.container.classList.add("OwO-up");
            var i = new XMLHttpRequest;
            i.onreadystatechange = function() {
                4 === i.readyState && (i.status >= 200 && i.status < 300 || 304 === i.status ? (a.odata = JSON.parse(i.responseText),
                a.init(t)) : console.log("OwO data request was unsuccessful: " + i.status))
            }
            ,
            i.open("get", t.api, !0),
            i.send(null)
        }
        return _createClass(e, [{
            key: "init",
            value: function(e) {
                var t = this;
                this.area = e.target,
                this.packages = Object.keys(this.odata);
                for (var a = '\n            <div class="OwO-logo"><span>' + e.logo + '</span></div>\n            <div class="OwO-body" style="width: ' + e.width + '">', s = 0; s < this.packages.length; s++) {
                    a += '\n                <ul class="OwO-items OwO-items-' + this.odata[this.packages[s]].type + '" style="max-height: ' + (parseInt(e.maxHeight) - 53 + "px") + ';">';
                    for (var n = this.odata[this.packages[s]].container, i = 0; i < n.length; i++)
                        a += '\n                    <li class="OwO-item" title="' + n[i].text + '">' + n[i].icon + "</li>";
                    a += "\n                </ul>"
                }
                a += '\n                <div class="OwO-bar">\n                    <ul class="OwO-packages">';
                for (var o = 0; o < this.packages.length; o++)
                    a += "\n                        <li><span>" + this.packages[o] + "</span></li>";
                a += "\n                    </ul>\n                </div>\n            </div>\n            ",
                this.container.innerHTML = a,
                this.logo = this.container.getElementsByClassName("OwO-logo")[0],
                this.logo.addEventListener("click", function() {
                    t.toggle()
                }),
                this.container.getElementsByClassName("OwO-body")[0].addEventListener("click", function(e) {
                    var a = null;
                    if (e.target.classList.contains("OwO-item") ? a = e.target : e.target.parentNode.classList.contains("OwO-item") && (a = e.target.parentNode),
                    a) {
                        var s = t.area.selectionEnd
                          , n = t.area.value;
                        t.area.value = n.slice(0, s) + a.innerHTML + n.slice(s),
                        t.area.focus(),
                        t.toggle()
                    }
                }),
                this.packagesEle = this.container.getElementsByClassName("OwO-packages")[0];
                for (var c = function(e) {
                    !function(a) {
                        t.packagesEle.children[e].addEventListener("click", function() {
                            t.tab(a)
                        })
                    }(e)
                }, l = 0; l < this.packagesEle.children.length; l++)
                    c(l);
                this.tab(0)
            }
        }, {
            key: "toggle",
            value: function() {
                this.container.classList.contains("OwO-open") ? this.container.classList.remove("OwO-open") : this.container.classList.add("OwO-open")
            }
        }, {
            key: "tab",
            value: function(e) {
                var t = this.container.getElementsByClassName("OwO-items-show")[0];
                t && t.classList.remove("OwO-items-show"),
                this.container.getElementsByClassName("OwO-items")[e].classList.add("OwO-items-show");
                var a = this.container.getElementsByClassName("OwO-package-active")[0];
                a && a.classList.remove("OwO-package-active"),
                this.packagesEle.getElementsByTagName("li")[e].classList.add("OwO-package-active")
            }
        }]),
        e
    }();
    "undefined" != typeof module && "undefined" != typeof module.exports ? module.exports = e : window.OwO = e
}();