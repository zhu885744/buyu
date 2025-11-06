<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE HTML>
<html lang="zh-CN" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="dns-prefetch" href="https://weavatar.com/">
    <link rel="icon" type="image/png" href="<?php $this->options->favicon(); ?>">
    <title><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('搜索到包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s的个人主页')
        ), '', ' - ');
        if($this->getCurrentPage() > 1) _e("第 %d 页-", $this->getCurrentPage());$this->options->title();?>
    </title>
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.mode.css?v=1.3.1');?>">
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.style.css?v=1.3.1');?>">
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/font-awesome/font-awesome.min.css?v=1.3.1');?>">
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.fancybox.css?v=1.3.1'); ?>"defer>
    <script type="text/javascript" src="<?php echo get_theme_url('assets/js/buyu.message.js?v=1.3.1'); ?>"></script>
    <style type="text/css">
        <?php if ($this->options->pcBackgroundUrl): // PC端背景图配置 ?>
        @media screen and (min-width: 768px) {
            body {
                /* 转义URL特殊字符，防止CSS语法错误 */
                background-image: url("<?php echo htmlspecialchars($this->options->pcBackgroundUrl, ENT_QUOTES); ?>");
                background-size: cover;
                background-position: center top;
                background-attachment: fixed;
                background-repeat: no-repeat;
                /* 加载失败时显示默认背景色 */
                background-color: #f9f9f9;
                /* 平滑过渡效果 */
                transition: background-image 0.3s ease-in-out;
            }
        }
        <?php endif; ?>

        <?php if ($this->options->wapBackgroundUrl): // 移动端背景图配置 ?>
        @media screen and (max-width: 767px) {
            body {
                background-image: url("<?php echo htmlspecialchars($this->options->wapBackgroundUrl, ENT_QUOTES); ?>");
                background-size: cover;
                background-position: center center;
                background-attachment: fixed;
                background-repeat: no-repeat;
                background-color: #f9f9f9;
                transition: background-image 0.3s ease-in-out;
            }
        }
        <?php endif; ?>

        <?php 
            if ($this->options->CustomCSS): 
                echo htmlspecialchars($this->options->CustomCSS, ENT_NOQUOTES); 
            endif; 
        ?>
    </style>
    <?php $this->header('generator=&template=&pingback=&xmlrpc=&wlw=');?>
</head>
<body>

<!-- 顶部导航栏 -->
<header id="header" class="header">
    <div class="container">
        <!-- 导航容器 -->
        <nav class="nav-wrapper">
            <!-- 汉堡菜单：移动端触发按钮，优先加载 -->
            <button class="menu-toggle" id="menuToggle">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </button>
            <!-- 站点Logo -->
            <a href="<?php $this->options->siteUrl(); ?>" class="logo site-logo" title="<?php $this->options->title(); ?>">
                <?php $this->options->title() ?>
            </a>
            <!-- 主导航菜单：桌面端默认显示，移动端隐藏 -->
            <ul class="main-menu site-nav" id="mainMenu" aria-label="主导航">
                <!-- 移动端侧边栏标题：仅移动端显示 -->
                <li class="sidebar-title site-nav__sidebar-title">
                    <span class="sidebar-title__main"><?php $this->options->title() ?></span>
                    <span class="sidebar-title__desc"><?php $this->options->description() ?></span>
                </li>
                <!-- 首页导航项 -->
                <li class="site-nav__item">
                    <a href="<?php $this->options->siteUrl(); ?>" class="site-nav__link <?php if($this->is('index')) echo 'current'; ?>" title="<?php _e('首页'); ?>">
                        <?php _e('首页'); ?>
                    </a>
                </li>
                <!-- 分类下拉菜单：动态获取分类 -->
                <li class="site-nav__item has-dropdown">
                    <a href="#" class="site-nav__link" title="<?php _e('分类'); ?>">
                        <?php _e('分类'); ?>
                    </a>
                    <!-- 分类下拉列表 -->
                    <ul class="dropdown site-nav__dropdown" aria-label="分类列表">
                        <?php $this->widget('Widget_Metas_Category_List')->to($categories); ?>
                        <?php while ($categories->next()): ?>
                            <?php if (!$categories->parent): // 仅显示顶级分类 ?>
                                <li class="site-nav__dropdown-item">
                                    <a 
                                        href="<?php $categories->permalink(); ?>" 
                                        class="site-nav__dropdown-link <?php if ($this->is('category', $categories->slug)) echo 'current'; ?>"
                                        title="<?php $categories->name(); ?> (<?php $categories->count(); ?>)"
                                    >
                                        <?php $categories->name(); ?>
                                        <!-- 分类文章数量 -->
                                        <?php if ($categories->count): ?>
                                            <span class="category-count site-nav__category-count">(<?php $categories->count(); ?>)</span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </ul>
                </li>
                
                <!-- 独立页面导航项：动态获取页面 -->
                <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                <?php while($pages->next()): ?>
                    <li class="site-nav__item">
                        <a href="<?php $pages->permalink(); ?>" class="site-nav__link <?php if($this->is('page', $pages->slug)) echo 'current'; ?>" title="<?php $pages->title(); ?>">
                            <?php $pages->title(); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
            
            <!-- 浅色深色模式切换按钮 -->
            <button id="theme-toggle" class="theme-toggle-btn" aria-label="切换深色/浅色模式">
                <i class="fa fa-sun-o light-icon" aria-hidden="true"></i>
                <i class="fa fa-moon-o dark-icon" aria-hidden="true"></i>
            </button>
        </nav>
    </div>
</header>