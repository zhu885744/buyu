<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('public/header.php'); ?>

<div class="col-mb-12 col-8 profile-container" id="main" role="main">
    <article class="post">
        <div itemprop="articleBody">
            <div class="post-content" id="post-<?php $this->cid(); ?>">
                <?php if($this->user->uid==$this->author->uid && $this->user->hasLogin()): ?>
                    <?php Typecho_Widget::widget('Widget_Stat')->to($stat); ?>
                <!-- 用户信息卡片 -->
                <div class="user-info-card">
                    <div class="card-header">
                        <h3>用户信息</h3>
                    </div>
                    <div class="card-body">
                        <div class="user-info-row">
                            <span class="info-label">用户名:</span>
                            <span class="info-value"><?php $this->user->screenName(); ?></span>
                        </div>
                        <div class="user-info-row">
                            <span class="info-label">用户组:</span>
                            <span class="info-value"><?php echo $this->user->group; ?></span>
                        </div>
                        <div class="user-info-row">
                            <span class="info-label">注册日期:</span>
                            <span class="info-value"><?php echo date('Y-m-d H:i:s', $this->user->created); ?></span>
                        </div>
                    </div>
                </div>

                    <!-- 修改信息表单 -->
                    <div id="profile" class="profile-form-container">
                        <h2 class="form-title">修改信息</h2>
                        
                        <form id="profile-form" action="<?php $this->options->profileAction(); ?>" method="post" class="form-horizontal">
                            <div class="form-group">
                                <label for="screenName" class="col-sm-2 control-label">昵称</label>
                                <div class="col-sm-10">
                                    <input type="text" id="screenName" name="screenName" value="<?php echo htmlspecialchars($this->user->screenName); ?>" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="mail" class="col-sm-2 control-label">邮箱</label>
                                <div class="col-sm-10">
                                    <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($this->user->mail); ?>" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="url" class="col-sm-2 control-label">个人主页</label>
                                <div class="col-sm-10">
                                    <input type="url" id="url" name="url" value="<?php echo htmlspecialchars($this->user->url); ?>" class="form-control">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="col-sm-2 control-label">个人简介</label>
                                <div class="col-sm-10">
                                    <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($this->user->description); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">保存修改</button>
                                </div>
                            </div>
                        </form>
                        
                        <!-- 密码修改表单 -->
                        <h2 class="form-title">密码修改</h2>
                        
                        <form id="password-form" action="<?php $this->options->profileAction(); ?>" method="post" class="form-horizontal">
                            <input type="hidden" name="do" value="password">
                            
                            <div class="form-group">
                                <label for="password" class="col-sm-2 control-label">当前密码</label>
                                <div class="col-sm-10">
                                    <input type="password" id="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="newPassword" class="col-sm-2 control-label">新密码</label>
                                <div class="col-sm-10">
                                    <input type="password" id="newPassword" name="newPassword" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirmPassword" class="col-sm-2 control-label">确认新密码</label>
                                <div class="col-sm-10">
                                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">修改密码</button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>    
        </div>
    </article>
</div>

<?php $this->need('public/footer.php'); ?>

<!-- 添加自定义样式 -->
<style>
    .user-info-card {
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .card-header h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }

    .user-info-row {
        display: flex;
        padding: 8px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .user-info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        width: 120px;
        color: #666;
        font-weight: 500;
    }
    
    .info-value {
        flex: 1;
        color: #333;
    }
    
    .profile-form-container {
        border-radius: 8px;
        margin-top: 20px;
    }
    
    .form-title {
        font-size: 20px;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .form-group {
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    
    .col-sm-2 {
        width: 150px;
    }
    
    .control-label {
        font-weight: 500;
        color: #666;
    }
    
    .col-sm-10 {
        flex: 1;
    }
    
    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #428bca;
        box-shadow: 0 0 5px rgba(66, 139, 202, 0.5);
    }
    
    .btn {
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        border: none;
    }
    
    .btn-primary {
        background: #428bca;
        color: #fff;
    }
    
    .btn-primary:hover {
        background: #3071a9;
    }
    
    .col-sm-offset-2 {
        margin-left: 150px;
    }
</style>

<!-- 添加JavaScript验证 -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 表单验证
        const passwordForm = document.getElementById('password-form');
        if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                if (newPassword !== confirmPassword) {
                    alert('两次输入的新密码不一致！');
                    e.preventDefault();
                    return false;
                }
                
                if (newPassword.length < 6) {
                    alert('新密码长度至少需要6个字符！');
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
</script>