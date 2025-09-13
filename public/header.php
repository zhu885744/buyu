<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="dns-prefetch" href="https://weavatar.com/">
    <link rel="icon" type="image/png" href="<?php $this->options->favicon(); ?>">
    <title><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('搜索到包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s的个人主页')
        ), '', ' - ');?><?php if($this->getCurrentPage()>1) _e("第 %d 页-", $this->getCurrentPage());?><?php $this->options->title();?></title>
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.grid.css?v=1.3.1');?>">
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.style.css?v=1.3.1');?>">
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/font-awesome/font-awesome.min.css?v=1.3.1');?>">
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.fancybox.css?v=1.3.1'); ?>">
    <script type="text/javascript" src="<?php echo get_theme_url('assets/js/buyu.message.js?v=1.3.1'); ?>"></script>
    <style type="text/css">
      /* 主题设置自定义css */
      <?php $this->options->CustomCSS(); ?>
    </style>
    <?php $this->header('generator=&template=&pingback=&xmlrpc=&wlw=');?>
</head>
<body>

<header id="header">
    <div class="container">
        <div class="nav-wrapper">
            <!-- Logo -->
            <a href="<?php $this->options->siteUrl(); ?>" class="logo"><?php $this->options->title() ?></a>
            <!-- 汉堡菜单按钮（移动端显示） -->
            <button class="menu-toggle" id="menuToggle">
               <i class="fa fa-bars"></i>
            </button>

            <!-- 主导航菜单 -->
            <ul class="main-menu" id="mainMenu">
                <!-- 手机端侧边栏标题（仅在移动端显示） -->
                <li class="sidebar-title">
                    <span><?php $this->options->title() ?></span>
                    <span class="subtitle"><?php $this->options->description() ?></span>
                </li>
               
                <li><a<?php if($this->is('index')): ?> class="current"<?php endif; ?> href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a></li>
                <!-- 自动获取分类并生成下拉菜单 -->
                <li class="has-dropdown">
                    <a href="#"><?php _e('分类'); ?></a>
                    <ul class="dropdown">
                        <?php $this->widget('Widget_Metas_Category_List')->to($categories); ?>
                        <?php while ($categories->next()): ?>
                            <?php if (!$categories->parent): // 只显示顶级分类 ?>
                            <li>
                                <a href="<?php $categories->permalink(); ?>" 
                                   <?php if ($this->is('category', $categories->slug)): ?>class="current"<?php endif; ?>>
                                    <?php $categories->name(); ?>
                                    <?php if ($categories->count): ?>
                                        <span class="category-count">(<?php $categories->count(); ?>)</span>
                                    <?php endif; ?>
                                </a>
                                
                                <!-- 子分类嵌套 -->
                                <?php if ($categories->hasChildren()): ?>
                                    <ul class="sub-dropdown">
                                        <?php $children = $categories->getAllChildren($categories->mid); ?>
                                        <?php foreach ($children as $child): ?>
                                        <li>
                                            <a href="<?php echo $child['permalink']; ?>"
                                               <?php if ($this->is('category', $child['slug'])): ?>class="current"<?php endif; ?>>
                                                <?php echo $child['name']; ?>
                                                <?php if ($child['count']): ?>
                                                    <span class="category-count">(<?php echo $child['count']; ?>)</span>
                                                <?php endif; ?>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </ul>
                </li>
                <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                <?php while($pages->next()): ?>
                <li><a<?php if($this->is('page', $pages->slug)): ?> class="current"<?php endif; ?> href="<?php $pages->permalink(); ?>" title="<?php $pages->title(); ?>"><?php $pages->title(); ?></a></li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</header>
    
<div class="nav-overlay"></div> 
<div id="body">
    <div class="container">
        <div class="row">