<?php

/* 过滤短代码 */
require_once('short.php');

/**
 * 获取用于检测 XSS 风险的正则表达式模式数组
 *
 * 该函数返回一个包含多个正则表达式模式的数组，这些模式用于匹配可能存在 XSS 风险的字符串，
 * 如事件处理函数、JavaScript 函数、危险的 HTML 属性、HTML 实体编码绕过以及 JavaScript 伪协议等。
 *
 * @return array 包含 XSS 检测正则表达式模式的数组
 */
function getXSSPatterns()
{
    return array(
        // 匹配 HTML 事件处理函数，这些函数可能被用于执行恶意 JavaScript 代码
        '/onabort/is',   // 当音频/视频的加载已放弃时触发
        '/onblur/is',    // 当元素失去焦点时触发
        '/onchange/is',  // 当元素的值发生改变时触发
        '/onclick/is',   // 当元素被点击时触发
        '/ondblclick/is',// 当元素被双击时触发
        '/onerror/is',   // 当错误发生时触发
        '/onfocus/is',   // 当元素获得焦点时触发
        '/onkeydown/is', // 当用户按下键盘按键时触发
        '/onkeypress/is',// 当用户按下并松开键盘按键时触发
        '/onkeyup/is',   // 当用户松开键盘按键时触发
        '/onload/is',    // 当页面或图像加载完成时触发
        '/onmousedown/is',// 当用户按下鼠标按钮时触发
        '/onmousemove/is',// 当鼠标指针在元素内移动时触发
        '/onmouseout/is',// 当鼠标指针移出元素时触发
        '/onmouseover/is',// 当鼠标指针移到元素上时触发
        '/onmouseup/is', // 当用户松开鼠标按钮时触发
        '/onreset/is',   // 当表单重置时触发
        '/onresize/is',  // 当窗口或框架被调整大小时触发
        '/onselect/is',  // 当文本被选中时触发
        '/onsubmit/is',  // 当表单提交时触发
        '/onunload/is',  // 当页面卸载时触发

        // 匹配可能执行恶意代码的 JavaScript 函数
        '/eval/is',      // 用于执行 JavaScript 代码字符串
        '/ascript:/is', // 匹配可能的 JavaScript 协议简写形式

        // 匹配可能存在风险的 HTML 属性，这些属性可能被用于注入恶意代码
        '/style=/is',    // 匹配 style 属性，可能用于注入 CSS 表达式执行恶意代码
        '/width=/is',    // 匹配 width 属性，可能被滥用
        '/width:/is',    // 匹配 CSS 中的 width 属性，可能被滥用
        '/height=/is',   // 匹配 height 属性，可能被滥用
        '/height:/is',   // 匹配 CSS 中的 height 属性，可能被滥用
        '/src=/is',      // 匹配 src 属性，可能用于加载恶意资源

        // 匹配 HTML 实体编码，防止通过实体编码绕过 XSS 检测
        '/&(#x?[a-f0-9]+);?/i',

        // 匹配 JavaScript 伪协议，防止通过该协议执行恶意代码
        '/javascript:/is',
    );
}

/**
 * 获取允许的 HTML 标签和属性白名单
 *
 * 该函数返回一个关联数组，数组的键为允许的 HTML 标签名，
 * 对应的值为该标签允许使用的属性数组。若某个标签对应的值为空数组，
 * 则表示该标签不允许使用任何属性。
 *
 * @return array 包含允许的 HTML 标签和属性的关联数组
 */
function getWhiteList()
{
    // 返回允许的 HTML 标签及其对应的允许属性列表
    return array(
        // 允许 <a> 标签，允许使用 href 和 title 属性
        'a' => array('href', 'title'),
        // 允许 <img> 标签，允许使用 src、alt、title、width 和 height 属性
        'img' => array('src', 'alt', 'title', 'width', 'height'),
        // 允许 <p> 标签，不允许使用任何属性
        'p' => array(),
        // 允许 <br> 标签，不允许使用任何属性
        'br' => array(),
        // 允许 <strong> 标签，不允许使用任何属性
        'strong' => array(),
        // 允许 <em> 标签，不允许使用任何属性
        'em' => array(),
        // 允许 <u> 标签，不允许使用任何属性
        'u' => array(),
        // 允许 <s> 标签，不允许使用任何属性
        's' => array(),
        // 允许 <ul> 标签，不允许使用任何属性
        'ul' => array(),
        // 允许 <ol> 标签，不允许使用任何属性
        'ol' => array(),
        // 允许 <li> 标签，不允许使用任何属性
        'li' => array(),
    );
}

/**
 * 检查文本是否包含 XSS 风险
 *
 * 该函数会遍历预定义的 XSS 匹配模式，检查传入的文本是否包含这些风险特征。
 * 若文本包含任一风险特征，则判定为存在 XSS 风险；若文本去除 HTML 标签后为空，也判定为有风险。
 *
 * @param string $text 需要检查的文本内容
 * @return bool 若存在 XSS 风险返回 true，否则返回 false
 */
function _checkXSS($text)
{
    // 获取预定义的 XSS 过滤规则模式数组
    $patterns = getXSSPatterns();
    // 遍历所有 XSS 匹配模式
    foreach ($patterns as $pattern) {
        // 使用正则表达式匹配文本，检查是否存在匹配项
        if (preg_match($pattern, $text)) {
            // 若存在匹配项，说明文本包含 XSS 风险，返回 true
            return true;
        }
    }
    // 去除文本中的 HTML 标签，若去除后文本为空，则视为存在风险，返回 true；否则返回 false
    return empty(strip_tags($text));
}

/**
 * 过滤 HTML 文本，仅保留白名单中的标签和属性
 *
 * @param string $text 需要过滤的 HTML 文本
 * @return string 过滤后的 HTML 文本
 */
function filterHTML($text)
{
    // 获取允许的 HTML 标签和属性白名单
    $whiteList = getWhiteList();
    // 创建一个新的 DOMDocument 对象，用于处理 HTML 文档
    $dom = new DOMDocument();
    // 加载 HTML 文本到 DOMDocument 中，添加 UTF-8 编码声明，使用 @ 抑制加载过程中的错误
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $text);
    // 创建 DOMXPath 对象，用于在 DOM 文档中进行 XPath 查询
    $xpath = new DOMXPath($dom);
    // 使用 XPath 查询选取文档中的所有元素
    $elements = $xpath->query('//*');

    // 遍历文档中的所有元素
    foreach ($elements as $element) {
        // 获取当前元素的标签名
        $tagName = $element->tagName;
        // 检查当前标签名是否不在白名单中
        if (!isset($whiteList[$tagName])) {
            // 若不在白名单中，将该元素从其父节点中移除
            $element->parentNode->removeChild($element);
            // 跳过当前元素，继续处理下一个元素
            continue;
        }
        // 获取当前标签允许的属性列表
        $allowedAttributes = $whiteList[$tagName];
        // 获取当前元素的所有属性
        $attributes = $element->attributes;
        // 检查当前元素是否有属性
        if ($attributes) {
            // 遍历当前元素的所有属性
            foreach ($attributes as $attribute) {
                // 检查当前属性名是否不在允许的属性列表中
                if (!in_array($attribute->name, $allowedAttributes)) {
                    // 若不在允许列表中，移除该属性
                    $element->removeAttribute($attribute->name);
                }
            }
        }
    }

    // 将处理后的 DOM 文档转换为 HTML 字符串
    $filteredHTML = $dom->saveHTML();
    // 移除添加的 XML 编码声明和 DOCTYPE 声明
    $filteredHTML = str_replace(array('<?xml encoding="UTF-8">', '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'), '', $filteredHTML);
    // 返回过滤后的 HTML 文本
    return $filteredHTML;
}

/**
 * 解析评论回复内容，检查 XSS 风险并进行过滤处理
 *
 * 该函数会先检查传入的评论回复内容是否存在 XSS 风险，若存在则输出拦截提示；
 * 若不存在风险，则对内容进行回复解析和 HTML 过滤处理后输出。
 *
 * @param string $text 评论回复的原始文本内容
 */
function _parseCommentReply($text)
{
    // 调用 _checkXSS 函数检查文本是否包含 XSS 风险
    if (_checkXSS($text)) {
        // 若存在 XSS 风险，输出拦截提示信息
        echo "该回复疑似异常，已被系统拦截！";
    } else {
        // 若不存在 XSS 风险，调用 _parseReply 函数对回复内容进行解析
        $text = _parseReply($text);
        // 调用 filterHTML 函数对解析后的内容进行 HTML 过滤，仅保留白名单中的标签和属性
        $text = filterHTML($text);
        // 输出过滤后的评论回复内容
        echo $text;
    }
}