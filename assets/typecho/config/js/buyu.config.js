document.addEventListener("DOMContentLoaded", function() {
    var e = document.querySelectorAll(".buyu_config__aside .item"),
        s = document.querySelector(".buyu_config > form"),
        n = document.querySelectorAll(".buyu_content");
    
    // 初始化显示第一个配置项
    e[0].classList.add("active");
    s.style.display = "block";
    
    // 为每个菜单项添加点击事件
    e.forEach(function(o) {
        o.addEventListener("click", function() {
            // 移除所有菜单项的激活状态
            e.forEach(function(e) {
                e.classList.remove("active")
            });
            // 为当前点击的菜单项添加激活状态
            o.classList.add("active");
            
            // 保存当前选择到sessionStorage
            var c = o.getAttribute("data-current");
            sessionStorage.setItem("buyu_config_current", c);
            
            // 显示对应内容区域
            n.forEach(function(e) {
                e.style.display = "none";
                if (e.classList.contains(c)) {
                    e.style.display = "block";
                }
            });
        });
    });
    
    // 如果sessionStorage中有保存的选择，则恢复
    if (sessionStorage.getItem("buyu_config_current")) {
        var o = sessionStorage.getItem("buyu_config_current");
        e.forEach(function(e) {
            var t = e.getAttribute("data-current");
            if (t === o) {
                e.classList.add("active");
            }
        });
        n.forEach(function(e) {
            if (e.classList.contains(o)) {
                e.style.display = "block";
            }
        });
    }
});