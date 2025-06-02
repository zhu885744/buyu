document.addEventListener('DOMContentLoaded', function() {
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
});

// 确保没有其他 onscroll 事件覆盖我们的滚动处理
window.onscroll = null;    

//页面加载时调用一次，使图片显示
window.onload = function() {
	var img = document.querySelectorAll("img[data-src]")
	for(var i = 0; i < img.length; i++) {
		img[i].style.opacity = "0"
	}
	Limg()
}

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

//代码框增加复制按钮
var codeblocks = document.getElementsByTagName("pre")
//循环每个pre代码块，并添加 复制代码
for (var i = 0; i < codeblocks.length; i++) {
    //显示 复制代码 按钮
    currentCode = codeblocks[i]
    currentCode.style = "position: relative;"
    var copy = document.createElement("div")
    copy.style = "position: absolute;right: 4px;\
    top: 4px;background-color: white;padding: 2px 8px;\
    margin: 8px;border-radius: 4px;cursor: pointer;\
    z-index: 9999;\
    box-shadow: 0 2px 4px rgba(0,0,0,0.05), 0 2px 4px rgba(0,0,0,0.05);"
    copy.innerHTML = "复制"
    currentCode.appendChild(copy)
    //让所有 "复制"按钮 全部隐藏
    copy.style.visibility = "hidden"
}

for (var i = 0; i < codeblocks.length; i++) {
    !function (i) {
        //鼠标移到代码块，就显示按钮
        codeblocks[i].onmouseover = function () {
            codeblocks[i].childNodes[1].style.visibility = "visible"
        }

        //执行 复制代码 功能
        function copyArticle(event) {
            const range = document.createRange();

            //范围是 code，不包括刚才创建的div
            range.selectNode(codeblocks[i].childNodes[0]);

            const selection = window.getSelection();
            if (selection.rangeCount > 0) selection.removeAllRanges();
            selection.addRange(range);
            document.execCommand('copy');

            codeblocks[i].childNodes[1].innerHTML = "复制成功"
            setTimeout(function () {
                codeblocks[i].childNodes[1].innerHTML = "复制"
            }, 1000);
            //清除选择区
            if (selection.rangeCount > 0) selection.removeAllRanges(); 0
        }
        codeblocks[i].childNodes[1].addEventListener('click', copyArticle, false);

    }(i);

    !function (i) {
        //鼠标从代码块移开 则不显示复制代码按钮
        codeblocks[i].onmouseout = function () {
            codeblocks[i].childNodes[1].style.visibility = "hidden"
        }
    }(i);
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