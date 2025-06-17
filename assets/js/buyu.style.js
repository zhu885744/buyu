document.addEventListener('DOMContentLoaded', function() {
    // 任务列表
    document.querySelectorAll('.post-content ul, .post-content ol').forEach(function(list) {
      list.querySelectorAll('li').forEach(function(li) {
        var html = li.innerHTML.trim();
        // 匹配任务列表
        var match = html.match(/^\[([ xX])\]\s+(.*)$/);
        if (match) {
            var isDone = match[1].toLowerCase() === 'x';
            var icon = isDone
              ? '<i class="fa fa-check-square" aria-hidden="true" style="color:#4caf50"></i>'
              : '<i class="fa fa-square-o" aria-hidden="true"></i>';
            var text = isDone
              ? '<span class="task-done">' + match[2] + '</span>'
              : match[2];
            li.innerHTML = icon + ' ' + text;
            li.classList.add('task-list-item');
        }
      });
    });
    
    var backToTopButton = document.getElementById('backToTop');
    var scrollThreshold = 200; // 当滚动超过这个阈值时显示按钮

    // 确保按钮存在
    if (!backToTopButton) return;

    // 初始隐藏按钮
    backToTopButton.classList.remove('visible');

    // 监听滚动事件 - 使用 addEventListener 而不是 onscroll
    window.addEventListener('scroll', function() {
        if (window.scrollY > scrollThreshold) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    });

    // 处理点击事件
    backToTopButton.addEventListener('click', function(e) {
        e.preventDefault(); // 防止默认行为
        
        // 平滑滚动到顶部
        if (window.scrollTo && typeof window.scrollTo === 'function') {
            // 检查浏览器是否支持平滑滚动行为
            if ('scrollBehavior' in document.documentElement.style) {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            } else {
                // 兼容不支持平滑滚动的浏览器
                smoothScrollPolyfill();
            }
        } else {
            // 非常旧的浏览器回退
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    });

    // 平滑滚动的兼容性实现
    function smoothScrollPolyfill() {
        var start = window.scrollY;
        var duration = 500; // 滚动持续时间（毫秒）
        var startTime = null;

        function animation(currentTime) {
            if (!startTime) startTime = currentTime;
            var timeElapsed = currentTime - startTime;
            var progress = Math.min(timeElapsed / duration, 1);
            var easeProgress = easeInOutCubic(progress);
            window.scrollTo(0, start - (start * easeProgress));
            
            if (timeElapsed < duration) {
                requestAnimationFrame(animation);
            }
        }

        // 缓动函数
        function easeInOutCubic(t) {
            return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
        }

        requestAnimationFrame(animation);
    }

    // 确保没有其他 onscroll 事件覆盖我们的滚动处理
    window.onscroll = null;    

    //页面加载时调用一次，使图片显示
    var img = document.querySelectorAll("img[data-src]")
    for(var i = 0; i < img.length; i++) {
        img[i].style.opacity = "0"
    }
    Limg()

    function Limg() {
        var viewHeight = document.documentElement.clientHeight // 可视区域的高度
        var t = document.documentElement.scrollTop || document.body.scrollTop;
        var limg = document.querySelectorAll("img[data-src]")
        // Array.prototype.forEach.call()是一种快速的方法访问forEach，并将空数组的this换成想要遍历的list
        Array.prototype.forEach.call(limg, function(item, index) {
            var rect
            if(item.getAttribute("data-src") === "")
                return
            //getBoundingClientRect用于获取某个元素相对于视窗的位置集合。集合中有top, right, bottom, left等属性。
            rect = item.getBoundingClientRect()
            // 图片一进入可视区，动态加载
            if(rect.bottom >= 0 && rect.top < viewHeight) {
                (function() {
                    var img = new Image()
                    img.src = item.getAttribute("data-src")
                    item.src = img.src
                    //给图片添加过渡效果，让图片显示
                    var j = 0
                    setInterval(function() {
                        j += 0.2
                        if(j <= 1) {
                            item.style.opacity = j
                            return
                        }
                    }, 100)
                    item.removeAttribute('data-src')
                })()
            }
        })
    }

    // 代码框增加复制按钮
    const codeblocks = document.getElementsByTagName("pre");

    // 添加复制按钮到每个代码块
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

    // 显示复制按钮
    function showCopyButton(copyButton) {
        copyButton.style.visibility = "visible";
    }

    // 隐藏复制按钮
    function hideCopyButton(copyButton) {
        copyButton.style.visibility = "hidden";
    }

    // 复制代码到剪贴板
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

    // 遍历每个代码块
    for (let i = 0; i < codeblocks.length; i++) {
        const codeBlock = codeblocks[i];
        const copyButton = addCopyButton(codeBlock);

        // 鼠标移入显示按钮
        codeBlock.addEventListener('mouseover', () => {
            showCopyButton(copyButton);
        });

        // 鼠标移出隐藏按钮
        codeBlock.addEventListener('mouseout', () => {
            hideCopyButton(copyButton);
        });

        // 点击复制代码
        copyButton.addEventListener('click', (event) => {
            copyCodeToClipboard(codeBlock, copyButton);
        });
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

    // 文章分享功能
    // 分享按钮
    const shareBtn = document.getElementById('share');
    if (shareBtn) {
        const shareTitle = shareBtn.getAttribute('data-title') || document.title;
        const shareUrl = shareBtn.getAttribute('data-url') || window.location.href;
        
        shareBtn.addEventListener('click', async function() {
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

    // 文章点赞功能
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
                if (data.count !== undefined) likeCount.textContent = data.count; 
            });
        
        // 点赞按钮点击事件
        likeBtn.addEventListener('click', function() {
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

    //折叠面板
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
});