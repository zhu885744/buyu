<?php
/**
 * 过滤短代码
 *
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

    // 输出处理后的文章内容
    echo $content;
}