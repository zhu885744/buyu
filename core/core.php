<?php

/* 公用函数 */
require_once('function.php');

/* 过滤内容函数 */
require_once('parse.php');

/* 插件方法 */
require_once('factory.php');

/* 主题初始化 */
function themeInit($archive){
    Helper::options()->commentsAntiSpam = false; //关闭反垃圾
    Helper::options()->commentsCheckReferer = false; //关闭检查评论来源URL与文章链接是否一致判断(否则会无法评论)
    Helper::options()->commentsMaxNestingLevels = '999'; //最大嵌套层数
    Helper::options()->commentsPageDisplay = 'first'; //强制评论第一页
    Helper::options()->commentsOrder = 'DESC'; //将最新的评论展示在前面    
    if ($archive->is('author')) {
        $archive->parameter->pageSize = 6; // 作者页面每6篇文章分页一次
    }
    if ($archive->is('category','av')) {
        $archive->parameter->pageSize = 6; // 分类缩略名为av的分类列表每6篇文章分页一次
    }
    $archive->content = a_class_replace($archive->content);//文章内容，让a_class_replace函数处理
    
    // 过滤短代码的正则表达式，可根据实际情况修改
    $shortcodeRegex = '/{bl av="(.+?)"}/sm';
    
    // 处理文章描述中的短代码
    if ($archive->is('single') || $archive->is('index')) {
        $description = preg_replace($shortcodeRegex, '', $archive->getDescription());
        $archive->setDescription($description);
    }
}