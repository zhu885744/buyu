<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- DNS预解析 -->
    <link rel="dns-prefetch" href="https://weavatar.com/">
    <!-- 站点图标 -->
    <link rel="icon" type="image/png" href="<?php $this->options->favicon(); ?>">
    <!-- 页面标题 -->
    <title>
        <?php 
        $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('搜索到包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s的个人主页')
        ), '', ' - ');
        if($this->getCurrentPage() > 1) _e("第 %d 页-", $this->getCurrentPage());
        $this->options->title();
        ?>
    </title>
    <!-- 样式文件加载：按依赖顺序排列 -->
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.grid.css?v=1.3.1');?>">
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.style.css?v=1.3.1');?>">
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/font-awesome/font-awesome.min.css?v=1.3.1');?>">
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.fancybox.css?v=1.3.1'); ?>">
    <script type="text/javascript" src="<?php echo get_theme_url('assets/js/buyu.message.js?v=1.3.1'); ?>"></script>
    <!-- 自定义CSS -->
    <style type="text/css">
        <?php $this->options->CustomCSS(); ?>
    </style>
    <?php $this->header('generator=&template=&pingback=&xmlrpc=&wlw=');?>
</head>
<body>

<!-- 头部导航 -->
<header id="pjax-header" class="header">
    <div class="container">
        <!-- 导航容器 -->
        <div class="nav-wrapper">
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
                    <a 
                        href="<?php $this->options->siteUrl(); ?>" 
                        class="site-nav__link <?php if($this->is('index')) echo 'current'; ?>"
                        title="<?php _e('首页'); ?>"
                    >
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
                        <a 
                            href="<?php $pages->permalink(); ?>" 
                            class="site-nav__link <?php if($this->is('page', $pages->slug)) echo 'current'; ?>"
                            title="<?php $pages->title(); ?>"
                        >
                            <?php $pages->title(); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</header>

<!-- 侧边导航遮罩层：移动端菜单展开时显示 -->
<div class="nav-overlay" id="navOverlay" aria-hidden="true"></div>