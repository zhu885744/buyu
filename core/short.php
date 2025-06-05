<?php

function _parseContent($post, $login)
{
    $content = $post->content;
    $content = _parseReply($content);
    // 修复首行空格不显示的情况
    $content = preg_replace_callback('/<p.*?>.*?<\/p>/S', static function ($match) {
        return preg_replace('/\s{4}/', "&emsp;", $match[0]);
    }, $content);

    // 处理视频短代码
    $content = preg_replace_callback('/\[video src="(.*?)" poster="(.*?)"( autoplay="true")?\]/', function ($matches) {
        $src = $matches[1];
        $poster = $matches[2];
        $autoplay = isset($matches[3])? ' autoplay' : '';
        return '<div class="custom-video-container"><video src="'. $src. '" poster="'. $poster. '"'.$autoplay.' controls></video></div>';
    }, $content);

    // 处理音频短代码
    $content = preg_replace_callback('/\[audio name="(.*?)" artist="(.*?)" url="(.*?)" cover="(.*?)"\]/', function ($matches) {
        $name = $matches[1];
        $artist = $matches[2];
        $url = $matches[3];
        $cover = $matches[4];
        return '<div class="custom-audio-container"><audio controls><source src="'. $url. '" type="audio/mpeg">您的浏览器不支持音频播放。</audio></div>';
    }, $content);

    // 处理折叠面板短代码
    $content = preg_replace('/<details><summary>(.*?)<\/summary><br>(.*?)<\/details>/s', '<details><summary>$1</summary><div class="details-content">$2</div></details>', $content);

    echo $content;
}