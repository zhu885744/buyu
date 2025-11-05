document.addEventListener("DOMContentLoaded", function() {
    // 缓存DOM元素
    const tabItems = document.querySelectorAll(".buyu_config__aside .item");
    const formContainer = document.querySelector(".buyu_config > form");
    const contentAreas = document.querySelectorAll(".buyu_content");
    const STORAGE_KEY = "buyu_config_current"; // 统一存储键名

    // 初始化：隐藏所有内容
    const hideAllContents = () => {
        formContainer.style.display = "none";
        contentAreas.forEach(content => {
            content.style.display = "none";
        });
    };

    // 显示指定内容区域
    const showContent = (targetClass) => {
        // 显示表单容器
        formContainer.style.display = "block";
        // 显示目标内容区域
        contentAreas.forEach(content => {
            content.style.display = content.classList.contains(targetClass) ? "block" : "none";
        });
    };

    // 激活指定选项卡
    const activateTab = (tabElement) => {
        // 移除所有选项卡的激活状态
        tabItems.forEach(tab => tab.classList.remove("active"));
        // 激活当前选项卡
        tabElement.classList.add("active");
        // 获取目标内容类名
        const targetClass = tabElement.getAttribute("data-current");
        // 保存状态到sessionStorage
        sessionStorage.setItem(STORAGE_KEY, targetClass);
        // 显示对应内容
        showContent(targetClass);
    };

    // 初始化页面状态
    const initPage = () => {
        hideAllContents();
        
        // 尝试从sessionStorage恢复状态
        const savedTab = sessionStorage.getItem(STORAGE_KEY);
        
        if (savedTab) {
            // 查找保存的选项卡并激活
            for (const tab of tabItems) {
                if (tab.getAttribute("data-current") === savedTab) {
                    activateTab(tab);
                    return;
                }
            }
        }
        
        // 如果没有保存的状态或未找到，激活第一个选项卡
        if (tabItems.length > 0) {
            activateTab(tabItems[0]);
        }
    };

    // 绑定选项卡点击事件
    tabItems.forEach(tab => {
        tab.addEventListener("click", () => activateTab(tab));
        // 增加键盘导航支持（回车/空格激活）
        tab.setAttribute("tabindex", "0");
        tab.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                activateTab(tab);
            }
        });
    });

    // 初始化页面
    initPage();
});