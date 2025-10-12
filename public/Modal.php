<?php if ($this->options->tip!== "off") :?>
<!-- 模态框遮罩层 -->
<div id="reward-modal">
    <!-- 模态框内容区 -->
    <div id="reward-content">
        <!-- 模态框头部 -->
        <div class="reward-header">
            <h2>打赏作者</h2>
            <!-- 关闭按钮 -->
            <button id="close-reward">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <!-- 模态框主体内容 -->
        <div class="reward-container">
            <!-- 打赏方式标签页 -->
            <div class="payment-tabs">
                <!-- 标签切换栏 -->
                <div class="tab-buttons">
                    <button class="tab-btn active" data-tab="wechat">
                        <i class="fa fa-weixin fa-lg"></i>&nbsp;微信
                    </button>
                    <button class="tab-btn" data-tab="alipay">
                        <i class="fa fa-credit-card fa-lg"></i>&nbsp;支付宝
                    </button>
                </div>
                
                <!-- 标签内容区 -->
                <div class="tab-contents">
                    <!-- 微信支付 -->
                    <div class="tab-content active" id="wechat-tab">
                        <?php if ($this->options->weixin): ?>
                            <div class="qrcode-wrapper">
                                <div class="qrcode-container">
                                    <img src="<?php echo htmlspecialchars($this->options->weixin(), ENT_QUOTES, 'UTF-8'); ?>" alt="微信支付二维码">
                                </div>
                                <p class="tab-desc">扫描上方二维码</p>
                            </div>
                        <?php else: ?>
                            <div class="no-qrcode">
                                <i class="fa fa-qrcode fa-3x"></i>
                                <p>未设置微信收款码</p>
                            </div>
                        <?php endif; ?>
                    </div>
                
                    <!-- 支付宝 -->
                    <div class="tab-content" id="alipay-tab">
                        <?php if ($this->options->zfb): ?>
                            <div class="qrcode-wrapper">
                                <div class="qrcode-container">
                                    <img src="<?php echo htmlspecialchars($this->options->zfb(), ENT_QUOTES, 'UTF-8'); ?>" alt="支付宝二维码">
                                </div>
                                <p class="tab-desc">扫描上方二维码</p>
                            </div>
                        <?php else: ?>
                            <div class="no-qrcode">
                                <i class="fa fa-qrcode fa-3x"></i>
                                <p>未设置支付宝收款码</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 底部提示 -->
            <div class="reward-footer">
                感谢您的支持！
            </div>
        </div>
    </div>
</div>

<!-- 模态框控制脚本（包含标签页切换逻辑） -->
<script>
// 获取元素
const rewardModal = document.getElementById('reward-modal');
const rewardContent = document.getElementById('reward-content');
const closeReward = document.getElementById('close-reward');
const tabButtons = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

// 显示模态框（分步触发动画）
function showReward() {
    // 先显示遮罩层，再通过过渡让其淡入
    rewardModal.style.display = 'flex';
    setTimeout(() => {
        rewardModal.style.opacity = '1'; // 遮罩层淡入
        rewardContent.style.transform = 'scale(1)'; // 内容区放大到正常尺寸
        rewardContent.style.opacity = '1'; // 内容区淡入
    }, 10); // 延迟10ms确保 display 切换生效
    
    document.body.style.overflow = 'hidden'; // 禁止页面滚动
}

// 关闭模态框（反向触发动画）
function hideReward() {
    // 先让内容区缩小+淡出，再隐藏遮罩层
    rewardModal.style.opacity = '0'; // 遮罩层淡出
    rewardContent.style.transform = 'scale(0.9)'; // 内容区缩小
    rewardContent.style.opacity = '0'; // 内容区淡出
    
    setTimeout(() => {
        rewardModal.style.display = 'none'; // 动画结束后隐藏容器
        document.body.style.overflow = ''; // 恢复页面滚动
    }, 300); // 与 CSS 过渡时间保持一致（300ms）
}

// 标签页切换逻辑
tabButtons.forEach(button => {
    button.addEventListener('click', () => {
        // 移除所有标签和内容的active类
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // 给当前点击的标签添加active类
        button.classList.add('active');
        
        // 显示对应的内容
        const tabId = button.getAttribute('data-tab');
        const activeTab = document.getElementById(`${tabId}-tab`);
        activeTab.classList.add('active');
        
        // 添加标签切换动画
        activeTab.style.animation = 'none';
        setTimeout(() => {
            activeTab.style.animation = 'fadeIn 0.3s ease';
        }, 10);
    });
});

// 点击关闭按钮关闭模态框
closeReward.addEventListener('click', hideReward);

// 点击遮罩层空白处关闭模态框
rewardModal.addEventListener('click', function(e) {
    if (e.target === rewardModal) {
        hideReward();
    }
});

// 按ESC键关闭模态框
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && rewardModal.style.display === 'flex') {
        hideReward();
    }
});
</script>
<?php endif; ?>