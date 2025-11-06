<?php

/* 公用函数 */
require_once('function.php');

/* 过滤内容函数 */
require_once('parse.php');

/* 插件方法 */
require_once('factory.php');

/**
 * 主题初始化函数，在主题加载时执行相关配置和内容处理操作
 *
 * 该函数用于设置主题的评论相关选项，根据页面类型调整分页设置，
 * 对文章内容进行处理，并过滤文章描述中的短代码。
 *
 * @param Typecho_Widget_Archive $archive 当前页面的归档对象，包含页面相关信息
 */
function themeInit($archive){
    // 关闭评论反垃圾功能，避免误判导致正常评论被拦截
    Helper::options()->commentsAntiSpam = false; 
    // 关闭检查评论来源URL与文章链接是否一致的判断，防止因该检查导致无法评论
    Helper::options()->commentsCheckReferer = false; 
    // 设置评论的最大嵌套层数为999层
    Helper::options()->commentsMaxNestingLevels = '999'; 
    // 将最新的评论展示在前面
    Helper::options()->commentsOrder = 'DESC'; 
    
    // 判断当前页面是否为作者页面
    if ($archive->is('author')) {
        // 若为作者页面，设置每6篇文章进行一次分页
        $archive->parameter->pageSize = 6; 
    }
    // 调用 a_class_replace 函数处理文章内容，并更新归档对象中的文章内容
    $archive->content = a_class_replace($archive->content);

    // 定义用于过滤短代码的正则表达式，可根据实际需求修改
    $shortcodeRegex = '/{bl av="(.+?)"}/sm';
    
    // 判断当前页面是否为文章详情页或首页
    if ($archive->is('single') || $archive->is('index')) {
        // 使用正则表达式移除文章描述中的短代码
        $description = preg_replace($shortcodeRegex, '', $archive->getDescription());
        // 更新归档对象中的文章描述
        $archive->setDescription($description);
    }
}