<?php

/* 过滤短代码 */
require_once('short.php');

// 定义 XSS 过滤规则
function getXSSPatterns()
{
    return array(
        // 事件处理函数
        '/onabort/is',
        '/onblur/is',
        '/onchange/is',
        '/onclick/is',
        '/ondblclick/is',
        '/onerror/is',
        '/onfocus/is',
        '/onkeydown/is',
        '/onkeypress/is',
        '/onkeyup/is',
        '/onload/is',
        '/onmousedown/is',
        '/onmousemove/is',
        '/onmouseout/is',
        '/onmouseover/is',
        '/onmouseup/is',
        '/onreset/is',
        '/onresize/is',
        '/onselect/is',
        '/onsubmit/is',
        '/onunload/is',
        // JavaScript 函数
        '/eval/is',
        '/ascript:/is',
        // HTML 属性
        '/style=/is',
        '/width=/is',
        '/width:/is',
        '/height=/is',
        '/height:/is',
        '/src=/is',
        // HTML 实体编码绕过
        '/&(#x?[a-f0-9]+);?/i',
        // JavaScript 伪协议
        '/javascript:/is',
    );
}

// 定义允许的 HTML 标签和属性白名单
function getWhiteList()
{
    return array(
        'a' => array('href', 'title'),
        'img' => array('src', 'alt', 'title', 'width', 'height'),
        'p' => array(),
        'br' => array(),
        'strong' => array(),
        'em' => array(),
        'u' => array(),
        's' => array(),
        'ul' => array(),
        'ol' => array(),
        'li' => array(),
    );
}

// 检查文本是否包含 XSS 风险
function _checkXSS($text)
{
    $patterns = getXSSPatterns();
    // 直接检查是否有匹配项
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text)) {
            return true;
        }
    }
    // 若文本为空，视为有风险
    return empty(strip_tags($text));
}

// 过滤 HTML 标签，只允许白名单中的标签和属性
function filterHTML($text)
{
    $whiteList = getWhiteList();
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $text);
    $xpath = new DOMXPath($dom);
    $elements = $xpath->query('//*');

    foreach ($elements as $element) {
        $tagName = $element->tagName;
        if (!isset($whiteList[$tagName])) {
            $element->parentNode->removeChild($element);
            continue;
        }
        $allowedAttributes = $whiteList[$tagName];
        $attributes = $element->attributes;
        if ($attributes) {
            foreach ($attributes as $attribute) {
                if (!in_array($attribute->name, $allowedAttributes)) {
                    $element->removeAttribute($attribute->name);
                }
            }
        }
    }

    $filteredHTML = $dom->saveHTML();
    $filteredHTML = str_replace(array('<?xml encoding="UTF-8">', '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'), '', $filteredHTML);
    return $filteredHTML;
}

/* 过滤评论回复 */
function _parseCommentReply($text)
{
    if (_checkXSS($text)) {
        echo "该回复疑似异常，已被系统拦截！";
    } else {
        $text = _parseReply($text);
        $text = filterHTML($text);
        echo $text;
    }
}