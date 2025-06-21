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
});

// 初始化图片懒加载
function initLazyLoad() {
    const images = document.querySelectorAll("img[data-src]");
    images.forEach(img => img.style.opacity = "0");

    function loadImages() {
        const viewHeight = document.documentElement.clientHeight;
        const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        const lazyImages = document.querySelectorAll("img[data-src]");

        lazyImages.forEach(img => {
            const rect = img.getBoundingClientRect();
            if (rect.bottom >= 0 && rect.top < viewHeight) {
                const src = img.getAttribute("data-src");
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
                    };
                }
            }
        });
    }

    // 页面加载时调用一次
    loadImages();
    // 监听滚动事件
    window.addEventListener('scroll', loadImages);
}

Fancybox.bind("[data-fancybox]", {
  // Your custom options
});

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