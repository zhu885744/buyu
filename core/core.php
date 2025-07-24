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
    // 强制评论显示第一页
    Helper::options()->commentsPageDisplay = 'first'; 
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

/**
 * 为主题添加自定义字段
 *
 * 该函数会检查当前页面是否为后台页面编辑页面，
 * 若为后台页面编辑页面，则添加一个用于输入自定义相册图片 JSON 数据的文本域字段。
 *
 * @param Typecho_Widget_Helper_Layout $layout 布局对象，用于添加表单元素
 */
function themeFields($layout)
{
    // 获取当前请求的脚本名称
    $currentScript = $_SERVER['SCRIPT_NAME'];
    // 检查当前页面是否为独立页面编辑页面
    if (strpos($currentScript, '/admin/write-page.php') !== false) {
        // 添加相册图片字段（JSON输入）
        $album_images = new Typecho_Widget_Helper_Form_Element_Textarea(
            'album_images',  // 字段名，用于在后续代码中引用该字段的值
            null,            // 验证规则，此处不设置验证规则
            null,            // 默认值，此处不设置默认值
            _t('自定义相册'),  // 字段标签，显示在表单中该字段的名称
            // 帮助说明，提示用户输入 JSON 格式的图片数据，并给出示例，同时说明该选项仅对相册独立页面有效
            _t('请输入 JSON 格式的图片数据，示例：<br>[{"url":"图片链接1","alt":"描述1","title":"卡片标题"},<br>{"url":"图片链接2","alt":"描述2","title":"卡片标题"}]<br>注意：本选项仅对相册独立页面有效')
        );
        
        // 调整文本域尺寸：rows控制高度（行数），cols控制宽度（字符数），style补充CSS样式
        $album_images->input->setAttribute('rows', 8);       // 设置文本域高度为 8 行（原 5 行，可按需修改）
        $album_images->input->setAttribute('cols', 60);      // 设置文本域宽度为 60 字符（默认较窄，加宽显示）
        // 设置文本域自适应宽度，最大宽度为 600px
        $album_images->input->setAttribute('style', 'width: 100%; max-width: 600px;');  
        
        // 将相册图片字段添加到布局中
        $layout->addItem($album_images);
    }
}