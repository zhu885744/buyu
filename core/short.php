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

    // 处理视频短代码，使用正则表达式匹配 [video] 短代码
    $content = preg_replace_callback('/\[video src="(.*?)" poster="(.*?)"( autoplay="true")?\]/', function ($matches) {
        // 获取视频源地址
        $src = $matches[1];
        // 获取视频封面地址
        $poster = $matches[2];
        // 判断是否设置自动播放，若设置则添加 autoplay 属性
        $autoplay = isset($matches[3])? ' autoplay' : '';
        // 将短代码替换为自定义的视频播放容器和 video 标签
        return '<div class="custom-video-container"><video src="'. $src. '" poster="'. $poster. '"'.$autoplay.' controls></video></div>';
    }, $content);

    // 处理音频短代码，使用正则表达式匹配 [audio] 短代码
    $content = preg_replace_callback('/\[audio name="(.*?)" artist="(.*?)" url="(.*?)" cover="(.*?)"\]/', function ($matches) {
        // 获取音频名称
        $name = $matches[1];
        // 获取音频艺术家
        $artist = $matches[2];
        // 获取音频文件地址
        $url = $matches[3];
        // 获取音频封面地址
        $cover = $matches[4];
        // 将短代码替换为自定义的音频播放容器和 audio 标签
        return '<div class="custom-audio-container"><audio controls><source src="'. $url. '" type="audio/mpeg">您的浏览器不支持音频播放。</audio></div>';
    }, $content);

    // 处理折叠面板短代码，使用正则表达式匹配 <details> 标签及其内容
    $content = preg_replace('/<details><summary>(.*?)<\/summary><br>(.*?)<\/details>/s', '<details><summary>$1</summary><div class="details-content">$2</div></details>', $content);

    // 输出处理后的文章内容
    echo $content;
}