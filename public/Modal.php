<?php if ($this->options->tip !== "off") : ?>
<!-- 打赏模态框容器 -->
<div id="reward-modal" role="dialog" aria-modal="true" aria-labelledby="reward-modal-title">
    <!-- 模态框内容区 -->
    <div id="reward-content">
        <!-- 模态框头部 -->
        <div class="reward-header">
            <h2 id="reward-modal-title">打赏作者</h2>
            <!-- 关闭按钮 -->
            <button id="close-reward" aria-label="关闭打赏模态框">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
        </div>

        <!-- 模态框主体内容 -->
        <div class="reward-container">
            <!-- 打赏方式标签页 -->
            <div class="payment-tabs">
                <!-- 标签切换栏 -->
                <div class="tab-buttons">
                    <button class="tab-btn active" data-tab="wechat" aria-selected="true">
                        <i class="fa fa-weixin fa-lg" aria-hidden="true"></i>&nbsp;微信
                    </button>
                    <button class="tab-btn" data-tab="alipay" aria-selected="false">
                        <i class="fa fa-credit-card fa-lg" aria-hidden="true"></i>&nbsp;支付宝
                    </button>
                </div>
                
                <!-- 标签内容区 -->
                <div class="tab-contents">
                    <!-- 微信支付 -->
                    <div class="tab-content active" id="wechat-tab" aria-labelledby="wechat-tab-label">
                        <?php if ($this->options->weixin): ?>
                            <div class="qrcode-wrapper">
                                <div class="qrcode-container">
                                    <img src="<?php echo htmlspecialchars($this->options->weixin(), ENT_QUOTES, 'UTF-8'); ?>" 
                                         alt="微信支付二维码" 
                                         loading="lazy">
                                </div>
                                <p class="tab-desc">请扫描上方二维码</p>
                            </div>
                        <?php else: ?>
                            <div class="no-qrcode">
                                <i class="fa fa-qrcode fa-3x" aria-hidden="true"></i>
                                <p>未设置微信收款码</p>
                            </div>
                        <?php endif; ?>
                    </div>
                
                    <!-- 支付宝 -->
                    <div class="tab-content" id="alipay-tab" aria-labelledby="alipay-tab-label">
                        <?php if ($this->options->zfb): ?>
                            <div class="qrcode-wrapper">
                                <div class="qrcode-container">
                                    <img src="<?php echo htmlspecialchars($this->options->zfb(), ENT_QUOTES, 'UTF-8'); ?>" 
                                         alt="支付宝二维码" 
                                         loading="lazy">
                                </div>
                                <p class="tab-desc">请扫描上方二维码</p>
                            </div>
                        <?php else: ?>
                            <div class="no-qrcode">
                                <i class="fa fa-qrcode fa-3x" aria-hidden="true"></i>
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
<?php endif; ?>