<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?php $this->options->favicon(); ?>">
    <title><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('搜索到包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s的个人主页')
        ), '', ' - ');?><?php if($this->getCurrentPage()>1) _e("第 %d 页-", $this->getCurrentPage());?><?php $this->options->title();?></title>
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.grid.css');?>">
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/css/buyu.style.css');?>">
    <link rel="stylesheet" href="<?php echo get_theme_url('assets/font-awesome/font-awesome.min.css');?>">
    <link href="<?php echo get_theme_url('assets/css/buyu.fancybox.css'); ?>" rel="stylesheet">
    <script src="<?php echo get_theme_url('assets/js/buyu.message.js'); ?>"></script>
    <style type="text/css">
      /* 主题设置自定义css */
      <?php $this->options->CustomCSS(); ?>
    </style>
    <?php $this->header('generator=&template=&pingback=&xmlrpc=&wlw=');?>
</head>
<body>

<header id="header" class="clearfix">
    <div class="container">
        <div class="row">
            <div class="site-name col-mb-12" style="text-align: center;">
                <?php if ($this->options->logoUrl): ?>
                    <a id="logo" href="<?php $this->options->siteUrl(); ?>" >
                        <img src="<?php $this->options->logoUrl() ?>" width="100px" alt="<?php $this->options->title() ?>"/>
                    </a>
                <?php else: ?>
                    <a id="logo" href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?></a>
                    <p class="description"><?php $this->options->description() ?></p>
                <?php endif; ?>
            </div>
            <div class="col-mb-12">
                <nav id="nav-menu" class="clearfix" role="navigation">
                    <a<?php if ($this->is('index')):?> class="current"<?php endif;?> href="<?php $this->options->siteUrl();?>"><?php _e('首页');?></a>
                    <?php \Widget\Contents\Page\Rows::alloc()->to($pages);?>
                    <?php while ($pages->next()):?>
                    <a<?php if ($this->is('page', $pages->slug)):?> class="current"<?php endif;?> href="<?php $pages->permalink();?>" title="<?php $pages->title();?>"><?php $pages->title();?></a>
                    <?php endwhile;?>
                </nav>
                <form id="search" method="post" action="<?php $this->options->siteUrl();?>" role="search">
                    <label for="s" class="sr-only"><?php _e('搜索关键字');?></label>
                    <input type="text" id="s" name="s" class="text" placeholder="<?php _e('输入关键字搜索');?>"/>
                </form>
            </div>
        </div>
    </div>
</header>

<div id="body">
    <div class="container">
        <div class="row">