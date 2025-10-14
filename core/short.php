<?php
/**
 * 解析文章内容，处理多种短代码和格式问题
 *
 * 该函数会对传入的文章内容进行一系列处理，包括解析回复内容、
 * 修复首行空格显示问题、处理视频和音频短代码，以及调整折叠面板格式，
 * 最后将处理后的内容输出。
 *
 * @param object $post 包含文章信息的对象，其中 content 属性为文章内容
 * @param mixed $login 登录状态相关参数，当前函数未使用该参数
 */
function _parseContent($post, $login)
{
    // 获取文章的原始内容
    $content = $post->content;
    // 调用 _parseReply 函数对文章内容进行回复解析处理
    $content = _parseReply($content);

    // 修复首行空格不显示的情况，使用正则表达式匹配所有 <p> 标签及其内容
    $content = preg_replace_callback('/<p.*?>.*?<\/p>/S', static function ($match) {
        // 将匹配到的内容中连续的 4 个空格替换为全角空格
        return preg_replace('/\s{4}/', "&emsp;", $match[0]);
    }, $content);

    // 处理折叠面板短代码，使用正则表达式匹配 <details> 标签及其内容
    $content = preg_replace('/<details><summary>(.*?)<\/summary><br>(.*?)<\/details>/s', '<details><summary>$1</summary><div class="details-content">$2</div></details>', $content);

    // 输出处理后的文章内容
    echo $content;
}