document.addEventListener("DOMContentLoaded", function() {
    var e = document.querySelectorAll(".buyu_config__aside .item"),
        s = document.querySelector(".buyu_config > form"),
        n = document.querySelectorAll(".buyu_content");

    // 先尝试恢复 sessionStorage 数据
    var savedCurrent = sessionStorage.getItem("buyu_config_current");
    
    // 隐藏所有内容区域（包括表单）
    s.style.display = "none";
    n.forEach(function(content) {
        content.style.display = "none";
    });

    if (savedCurrent) {
        // 找到对应的菜单项并激活
        e.forEach(function(item) {
            var t = item.getAttribute("data-current");
            if (t === savedCurrent) {
                item.classList.add("active");
                // 显示对应内容区域
                n.forEach(function(content) {
                    if (content.classList.contains(savedCurrent)) {
                        content.style.display = "block";
                    }
                });
                // 显示表单（如果需要）
                s.style.display = "block";
                return; // 找到后退出循环
            }
        });
    } else if (e.length > 0) {
        // 若无数据且有菜单项，执行初始化（显示第一个选项）
        e[0].classList.add("active");
        var firstCurrent = e[0].getAttribute("data-current");
        // 显示对应内容区域
        n.forEach(function(content) {
            if (content.classList.contains(firstCurrent)) {
                content.style.display = "block";
            }
        });
        // 显示表单（如果需要）
        s.style.display = "block";
    }

    // 为每个菜单项添加点击事件（逻辑不变）
    e.forEach(function(o) {
        o.addEventListener("click", function() {
            // 移除所有激活状态
            e.forEach(function(item) {
                item.classList.remove("active");
            });
            // 激活当前项
            o.classList.add("active");
            // 保存到 sessionStorage
            var c = o.getAttribute("data-current");
            sessionStorage.setItem("buyu_config_current", c);
            // 显示对应内容
            s.style.display = "block"; // 确保表单显示
            n.forEach(function(content) {
                content.style.display = "none";
                if (content.classList.contains(c)) {
                    content.style.display = "block";
                }
            });
        });
    });
});