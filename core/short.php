<?php
function customExcerpt(
    $content,
    $length = 150,
    $suffix = '...',
    $keepTags = false,
    $allowedTags = '<strong><em><b><i><a>',
    $smartCut = true,
    $filterPatterns = []
) {
    // 过滤代码块和特殊内容
    $codePatterns = [
        // 过滤<pre><code>代码块、script标签、PHP代码块
        '/<(pre\s*[^>]*><code\s*[^>]*|script|\\?php)[\s\S]*?<\/(code><\/pre|script|\\?)>/is',
        // 过滤单行代码标记（`代码` 或 ```代码```）
        '/`{1,3}.*?`{1,3}/is',
        // 过滤函数/变量定义等代码片段（简化正则，避免过度匹配）
        '/(function|var|let|const|if|for|while)\s*\([^)]*\)\s*\{[^}]+\}/is',
        // 过滤无意义字符（版本号、连续标点等）
        '/[a-zA-Z0-9_-]+\d+\.\d+\.\d+[^\s]*/is',
        '/([^\w\s])\1{2,}/is'
    ];
    $content = preg_replace($codePatterns, '', $content);

    // 处理<!-- more -->手动分隔符
    $morePos = strpos($content, '<!-- more -->');
    if ($morePos !== false) {
        $content = substr($content, 0, $morePos);
    }

    // 过滤广告等自定义内容
    $defaultFilters = [
        '/【广告】[\s\S]*?【结束】/i',
        '/<img[^>]*alt="广告"[^>]*>/i',
        '/<div class="watermark">[\s\S]*?<\/div>/i'
    ];
    $allFilters = array_merge($defaultFilters, $filterPatterns);
    $content = preg_replace($allFilters, '', $content);

    // 处理HTML标签
    if (!$keepTags) {
        $content = strip_tags($content);
    } else {
        // 清除未闭合的标签，避免HTML混乱
        $content = strip_tags($content, $allowedTags);
        $content = preg_replace('/<[^>]*$/', '', $content);
    }

    // 清理短代码
    $shortcodePatterns = [
        '/\[(\w+)\s+(?:"[^"]*"|\'[^\']*\'|[^"\'])*\]([\s\S]*?)\[\/\1\]/is',
        '/\[(\w+)\s+(?:"[^"]*"|\'[^\']*\'|[^"\'])*\s*\/?\]/is',
        '/\{(\w+)\s+(?:"[^"]*"|\'[^\']*\'|[^"\'])*\}([\s\S]*?)\{\/\1\}/is',
        '/\{(\w+)\s+(?:"[^"]*"|\'[^\']*\'|[^"\'])*\s*\/\}/is'
    ];
    $maxIterations = 5; // 限制最大迭代次数，防止死循环
    $iteration = 0;
    do {
        $lastContent = $content;
        foreach ($shortcodePatterns as $i => $pattern) {
            $content = preg_replace($pattern, $i % 2 == 0 ? '$2' : '', $content);
        }
        $iteration++;
    } while ($content !== $lastContent && $iteration < $maxIterations);

    // 内容净化（合并空格、解码HTML实体、trim）
    $content = htmlspecialchars_decode($content, ENT_QUOTES);
    $content = preg_replace('/\s+/', ' ', $content);
    $content = trim($content);

    // 处理空内容
    if (empty($content)) {
        return '暂无摘要';
    }

    // 截断处理
    $contentLength = mb_strlen($content, 'UTF-8');
    if ($contentLength <= $length) {
        return $content;
    }

    // 智能截断：优先按中文标点分割，避免截断词语
    $excerpt = mb_substr($content, 0, $length, 'UTF-8');
    if ($smartCut) {
        // 中文标点优先级：空格 > ，/。 > 其他标点
        $cutPositions = [
            mb_strrpos($excerpt, ' ', 0, 'UTF-8'),    // 空格
            mb_strrpos($excerpt, '，', 0, 'UTF-8'),   // 中文逗号
            mb_strrpos($excerpt, '。', 0, 'UTF-8'),   // 中文句号
            mb_strrpos($excerpt, ',', 0, 'UTF-8'),    // 英文逗号
            mb_strrpos($excerpt, '.', 0, 'UTF-8')     // 英文句号
        ];
        // 筛选有效截断位置（取最后一个有效位置）
        foreach ($cutPositions as $pos) {
            if ($pos !== false && $pos > 0) {
                $excerpt = mb_substr($excerpt, 0, $pos, 'UTF-8');
                break;
            }
        }
    }

    return $excerpt . $suffix;
}