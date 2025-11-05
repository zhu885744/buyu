<?php
/* 获取主题当前版本号 */
function _getVersion(): string
{
    return "v1.3.1";
}

/**
 * 获取静态资源URL（主题设置支持自定义静态资源CDN地址）
 * @param string $path 资源相对路径
 * @return string 完整资源URL
 */
function get_theme_url(string $path): string
{
    if (empty($path)) {
        return '';
    }

    $options = Typecho_Widget::widget('Widget_Options');
    $cdnUrl = $options->JAssetsURL ?? '';

    if (!empty($cdnUrl)) {
        return rtrim($cdnUrl, '/') . '/' . ltrim($path, '/');
    }

    return Typecho_Common::url($path, $options->themeUrl);
}

/**
 * 为超链接添加新窗口属性
 * @param string|null $content HTML内容
 * @return string 处理后的内容
 */
function a_class_replace(?string $content): string
{
    if (empty($content)) {
        return '';
    }

    // 安全匹配超链接，避免XSS
    $pattern = '/<a(.*?)href=(["\'])([^"\']+)\2(.*?)>/i';
    return preg_replace_callback($pattern, function (array $matches): string {
        // 已存在target则不修改
        if (preg_match('/target=["\']?_blank["\']?/i', $matches[1] . $matches[4])) {
            return $matches[0];
        }
        // 过滤危险属性
        $attrs = preg_replace('/\s+on\w+=[^>]+/', '', $matches[1] . $matches[4]);
        return "<a{$attrs} href={$matches[2]}{$matches[3]}{$matches[2]} target=\"_blank\">";
    }, $content);
}

/**
 * 处理文章内容渲染
 * 
 * @param string $content 文章内容HTML
 * @param string $title 文章标题，用于图片alt属性
 * @param bool $allowRelativeUrls 是否允许相对路径图片
 * @return string 处理后的文章内容
 */
function processContent(string $content, string $title, bool $allowRelativePath = false): string
{
    // 更精确的图片标签匹配正则，考虑单引号和双引号的情况
    $pattern = '/<img\s+[^>]*src=(["\'])(.*?)\1[^>]*>/i';
    
    return preg_replace_callback($pattern, function (array $matches) use ($title, $allowRelativePath): string {
        // 验证匹配结果结构
        if (!isset($matches[1], $matches[2])) {
            return $matches[0];
        }
        
        $src = $matches[2];
        $quote = $matches[1]; // 保留原始的引号类型
        
        // 验证URL有效性
        if (filter_var($src, FILTER_VALIDATE_URL) || ($allowRelativePath && isRelativePath($src))) {
            $escapedSrc = htmlspecialchars($src, ENT_QUOTES);
            $escapedTitle = htmlspecialchars($title, ENT_QUOTES);
            
            // 构建新的图片标签
            return '<a data-fancybox="gallery" data-src="' . $escapedSrc . '" class="index-img">' .
                   '<img data-src="' . $escapedSrc . '" src="' . $escapedSrc . '" ' .
                   'loading="lazy" alt="' . $escapedTitle . '" title="点击查看大图">' .
                   '</a>';
        }
        
        // URL无效且不允许相对路径时返回原始标签
        return $matches[0];
    }, $content);
}

/**
 * 判断路径是否为相对路径
 * 
 * @param string $path 要检查的路径
 * @return bool 是否为相对路径
 */
function isRelativePath(string $path): bool
{
    return !preg_match('/^[a-zA-Z]+:\/\//', $path) && strpos($path, '/') !== 0;
}

/**
 * 将文章发布时间转换为友好的时间差显示
 * @param \Typecho\Date $date 文章发布时间对象
 * @return string 格式化后的时间字符串 
 */
function time_ago(\Typecho\Date $date): string
{
    // 获取当前时间（使用与文章相同的时区）
    $now = new \Typecho\Date(time());
    // 计算时间差（秒）
    $time_diff = $now->timeStamp - $date->timeStamp;
    // 处理未来时间（避免显示负数）
    if ($time_diff < 0) {
        return '刚刚发布';
    }
    // 时间单位常量
    $minute = 60;     // 1分钟
    $hour = 3600;     // 1小时
    $day = 86400;     // 1天
    $month = 2592000; // 30天（近似）
    $year = 31536000; // 365天（近似）
    // 根据时间差选择合适的显示格式
    if ($time_diff < $minute) {
        return $time_diff . "秒前发布";
    } elseif ($time_diff < $hour) {
        return (int)floor($time_diff / $minute) . "分钟前发布";
    } elseif ($time_diff < $day) {
        return (int)floor($time_diff / $hour) . "小时前发布";
    } elseif ($time_diff < $month) {
        return (int)floor($time_diff / $day) . "天前发布";
    } elseif ($time_diff < $year) {
        return (int)floor($time_diff / $month) . "个月前发布";
    } else {
        return $date->format('Y年m月d日'); // 超过1年直接显示完整日期
    }
}

/* 判断是否是手机 */
function _isMobile()
{
  if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
    return true;
  if (isset($_SERVER['HTTP_VIA'])) {
    return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
  }
  if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
    if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
      return true;
  }
  if (isset($_SERVER['HTTP_ACCEPT'])) {
    if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
      return true;
    }
  }
  return false;
}

/* 根据评论agent获取浏览器类型 */
function _getAgentBrowser($agent)
{
  if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
    $outputer = 'Internet Explore';
  } else if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
    $outputer = 'FireFox';
  } else if (preg_match('/Maxthon([\d]*)\/([^\s]+)/i', $agent, $regs)) {
    $outputer = 'MicroSoft Edge';
  } else if (preg_match('#360([a-zA-Z0-9.]+)#i', $agent, $regs)) {
    $outputer = '360 Fast Browser';
  } else if (preg_match('/Edge([\d]*)\/([^\s]+)/i', $agent, $regs)) {
    $outputer = 'MicroSoft Edge';
  } else if (preg_match('/UC/i', $agent)) {
    $outputer = 'UC Browser';
  } else if (preg_match('/QQ/i', $agent, $regs) || preg_match('/QQ Browser\/([^\s]+)/i', $agent, $regs)) {
    $outputer = 'QQ Browser';
  } else if (preg_match('/UBrowser/i', $agent, $regs)) {
    $outputer = 'UC Browser';
  } else if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
    $outputer = 'Opera';
  } else if (preg_match('/Chrome([\d]*)\/([^\s]+)/i', $agent, $regs)) {
    $outputer = 'Google Chrome';
  } else if (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
    $outputer = 'Safari';
  } else {
    $outputer = 'Google Chrome';
  }
  echo $outputer;
}

/* 根据评论agent获取设备类型 */
function _getAgentOS($agent)
{
  $os = "Linux";
  if (preg_match('/win/i', $agent)) {
    if (preg_match('/nt 6.0/i', $agent)) {
      $os = 'Windows Vista';
    } else if (preg_match('/nt 6.1/i', $agent)) {
      $os = 'Windows 7';
    } else if (preg_match('/nt 6.2/i', $agent)) {
      $os = 'Windows 8';
    } else if (preg_match('/nt 6.3/i', $agent)) {
      $os = 'Windows 8.1';
    } else if (preg_match('/nt 5.1/i', $agent)) {
      $os = 'Windows XP';
    } else if (preg_match('/nt 10.0/i', $agent)) {
      $os = 'Windows 10';
    } else {
      $os = 'Windows X64';
    }
  } else if (preg_match('/android/i', $agent)) {
    if (preg_match('/android 9/i', $agent)) {
      $os = 'Android Pie';
    } else if (preg_match('/android 8/i', $agent)) {
      $os = 'Android Oreo';
    } else {
      $os = 'Android';
    }
  } else if (preg_match('/ubuntu/i', $agent)) {
    $os = 'Ubuntu';
  } else if (preg_match('/linux/i', $agent)) {
    $os = 'Linux';
  } else if (preg_match('/iPhone/i', $agent)) {
    $os = 'iPhone';
  } else if (preg_match('/mac/i', $agent)) {
    $os = 'MacOS';
  } else if (preg_match('/fusion/i', $agent)) {
    $os = 'Android';
  } else {
    $os = 'Linux';
  }
  echo $os;
}

// 评论者等级、评论博主标签显示
function dengji(?string $i): void
{
    $db = Typecho_Db::get();
    $adminAuthorId = 1;
    
    if (empty($i)) {
        $admin = $db->fetchRow($db->select('mail')->from('table.users')->where('uid = ?', $adminAuthorId));
        $i = $admin['mail'] ?? '';
    }
    
    // 优先判断博主身份
    $author = $db->fetchRow($db->select('authorId')->from('table.comments')->where('mail = ?', $i)->limit(1));
    $authorId = $author['authorId'] ?? 0;
    if ($authorId == $adminAuthorId) {
        echo '<span class="shortcode-badge badge-purple">博主</span>';
        return;
    }
    
    // 查询评论数量
    $mail = $db->fetchRow($db->select(array('COUNT(cid)' => 'rbq'))
        ->from('table.comments')
        ->where('mail = ?', $i)
        ->where('authorId = ?', '0'));
    $rbq = $mail['rbq'] ?? 0; 
    
    // 提高后的等级门槛（评论数要求更高，递增幅度更大）
    if ($rbq < 10) {          // 1-9条
        echo '<span class="shortcode-badge badge-default">Lv.1</span>';
    } elseif ($rbq < 30) {    // 10-29条
        echo '<span class="shortcode-badge badge-success">Lv.2</span>';
    } elseif ($rbq < 60) {    // 30-59条
        echo '<span class="shortcode-badge badge-warning">Lv.3</span>';
    } elseif ($rbq < 100) {   // 60-99条
        echo '<span class="shortcode-badge badge-danger">Lv.4</span>';
    } elseif ($rbq < 150) {   // 100-149条
        echo '<span class="shortcode-badge badge-info">Lv.5</span>';
    } else {                  // 150条及以上
        echo '<span class="shortcode-badge badge-orange">知己</span>';
    }
}

// 附件页面和作者页面重定向到404页面
function redirect_404(): void
{
    $request = Typecho_Request::getInstance();
    $pathInfo = $request->getPathInfo();
    // 使用正则表达式匹配路径
    if (preg_match('/^\/(attachment\/\d+|author\/\w+)/i', $pathInfo)) {
        // 调用 404 页面
        $options = Typecho_Widget::widget('Widget_Options');
        $url = $options->siteUrl . '404';
        header("Location: $url");
        exit;
    }
}
// 在页面加载之前调用
Typecho_Plugin::factory('Widget_Archive')->beforeRender = 'redirect_404';

// 文章点赞逻辑
if (isset($_GET['action']) && ($_GET['action'] === 'like' || $_GET['action'] === 'get_like') && isset($_GET['cid'])) {
    $cid = (int)$_GET['cid'];
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    // 简单IP限制
    if ($_GET['action'] === 'like') {
        // 检查会话是否已经启动
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user = Typecho_Widget::widget('Widget_User');
        if ($user->hasLogin()) {
            // 登录用户使用 uid 记录点赞状态
            $uid = $user->uid;
            $key = 'like_' . $cid . '_' . $uid;
        } else {
            // 未登录用户使用 IP 记录点赞状态
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $key = 'like_' . $cid . '_' . md5($ip);
        }

        if (isset($_SESSION[$key])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'msg' => '您已经点过赞啦！']);
            exit;
        }
        $db->query("UPDATE `{$prefix}contents` SET `agree` = `agree` + 1 WHERE `cid` = $cid");
        $_SESSION[$key] = 1;
    }
    // 获取最新点赞数
    $row = $db->fetchRow($db->select('agree')->from('table.contents')->where('cid = ?', $cid));
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'count' => (int)($row['agree'] ?? 0)
    ]);
    exit;
}

/* 判断评论敏感词是否在字符串内 */
function _checkSensitiveWords(string $words_str, string $str): bool
{
    $words = explode("||", $words_str);
    if (empty($words)) {
        return false;
    }
    foreach ($words as $word) {
        $trimmedWord = trim($word);
        if ($trimmedWord !== '' && strpos($str, $trimmedWord) !== false) {
            return true;
        }
    }
    return false;
}

/* 文章编辑器添加字符统计 */
Typecho_Plugin::factory('admin/write-post.php')->bottom = ['myyodu', 'one'];
Typecho_Plugin::factory('admin/write-page.php')->bottom = ['myyodu', 'one'];
class myyodu {
    public static function one(): void
    {
    ?>
<style>
.field.is-grouped{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:start;-ms-flex-pack:start;justify-content:flex-start;  -ms-flex-wrap: wrap;flex-wrap: wrap;}.field.is-grouped>.control{-ms-flex-negative:0;flex-shrink:0}.field.is-grouped>.control:not(:last-child){margin-bottom:.5rem;margin-right:.75rem}.field.is-grouped>.control.is-expanded{-webkit-box-flex:1;-ms-flex-positive:1;flex-grow:1;-ms-flex-negative:1;flex-shrink:1}.field.is-grouped.is-grouped-centered{-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center}.field.is-grouped.is-grouped-right{-webkit-box-pack:end;-ms-flex-pack:end;justify-content:flex-end}.field.is-grouped.is-grouped-multiline{-ms-flex-wrap:wrap;flex-wrap:wrap}.field.is-grouped.is-grouped-multiline>.control:last-child,.field.is-grouped.is-grouped-multiline>.control:not(:last-child){margin-bottom:.75rem}.field.is-grouped.is-grouped-multiline:last-child{margin-bottom:-.75rem}.field.is-grouped.is-grouped-multiline:not(:last-child){margin-bottom:0}.tags{-webkit-box-align:center;-ms-flex-align:center;align-items:center;display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:start;-ms-flex-pack:start;justify-content:flex-start}.tags .tag{margin-bottom:.5rem}.tags .tag:not(:last-child){margin-right:.5rem}.tags:last-child{margin-bottom:-.5rem}.tags:not(:last-child){margin-bottom:1rem}.tags.has-addons .tag{margin-right:0}.tags.has-addons .tag:not(:first-child){border-bottom-left-radius:0;border-top-left-radius:0}.tags.has-addons .tag:not(:last-child){border-bottom-right-radius:0;border-top-right-radius:0}.tag{-webkit-box-align:center;-ms-flex-align:center;align-items:center;background-color:#f5f5f5;border-radius:3px;color:#4a4a4a;display:-webkit-inline-box;display:-ms-inline-flexbox;display:inline-flex;font-size:.75rem;height:2em;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;line-height:1.5;padding-left:.75em;padding-right:.75em;white-space:nowrap}.tag .delete{margin-left:.25em;margin-right:-.375em}.tag.is-white{background-color:#fff;color:#0a0a0a}.tag.is-black{background-color:#0a0a0a;color:#fff}.tag.is-light{background-color:#fff;color:#363636}.tag.is-dark{background-color:#363636;color:#f5f5f5}.tag.is-primary{background-color:#00d1b2;color:#fff}.tag.is-info{background-color:#3273dc;color:#fff}.tag.is-success{background-color:#23d160;color:#fff}.tag.is-warning{background-color:#ffdd57;color:rgba(0,0,0,.7)}.tag.is-danger{background-color:#ff3860;color:#fff}.tag.is-large{font-size:1.25rem}.tag.is-delete{margin-left:1px;padding:0;position:relative;width:2em}.tag.is-delete:after,.tag.is-delete:before{background-color:currentColor;content:"";display:block;left:50%;position:absolute;top:50%;-webkit-transform:translateX(-50%) translateY(-50%) rotate(45deg);transform:translateX(-50%) translateY(-50%) rotate(45deg);-webkit-transform-origin:center center;transform-origin:center center}.tag.is-delete:before{height:1px;width:50%}.tag.is-delete:after{height:50%;width:1px}.tag.is-delete:focus,.tag.is-delete:hover{background-color:#e8e8e8}.tag.is-delete:active{background-color:#dbdbdb}.tag.is-rounded{border-radius:290486px}
</style>
<script language="javascript">
    var EventUtil = function() {};
    EventUtil.addEventHandler = function(obj, EventType, Handler) {
        if (obj.addEventListener) {
            obj.addEventListener(EventType, Handler, false);
        }
        else if (obj.attachEvent) {
            obj.attachEvent('on' + EventType, Handler);
        } else {
            obj['on' + EventType] = Handler;
        }
    }
    if (document.getElementById("text")) {
        EventUtil.addEventHandler(document.getElementById('text'), 'propertychange', CountChineseCharacters);
        EventUtil.addEventHandler(document.getElementById('text'), 'input', CountChineseCharacters);
    }
    function showit(Word) {
        alert(Word);
    }
    function CountChineseCharacters() {
        const Words = document.getElementById('text').value;
        const W = new Object();
        let iNumwords = 0;
        let sNumwords = 0;
        let sTotal = 0;
        let iTotal = 0;
        let eTotal = 0;
        let otherTotal = 0;
        let bTotal = 0;
        let inum = 0;
        let znum = 0;
        let gl = 0;
        let paichu = 0;
        for (let i = 0; i < Words.length; i++) {
            const c = Words.charAt(i);
            if (c.match(/[\u4e00-\u9fa5]/) || c.match(/[\u0800-\u4e00]/) || c.match(/[\uac00-\ud7ff]/)) {
                if (isNaN(W[c])) {
                    iNumwords++;
                    W[c] = 1;
                }
                iTotal++;
            }
        }
        for (let i = 0; i < Words.length; i++) {
            const c = Words.charAt(i);
            if (c.match(/[^\x00-\xff]/)) {
                if (isNaN(W[c])) {
                    sNumwords++;
                }
                sTotal++;
            } else {
                eTotal++;
            }
            if (c.match(/[0-9]/)) {
                inum++;
            }
            if (c.match(/[a-zA-Z]/)) {
                znum++;
            }
            if (c.match(/[\s]/)) {
                gl++;
            }
            if (c.match(/[　◕‿↑↓←→↖↗↘↙↔↕。《》、【】“”•‘’❝❞′……—―‐〈〉„╗╚┐└‖〃「」‹›『』〖〗〔〕∶〝〞″≌∽≦≧≒≠≤≥㏒≡≈✓✔◐◑◐◑✕✖★☆₸₹€₴₰₤₳र₨₲₪₵₣₱฿₡₮₭₩₢₧₥₫₦₠₯○㏄㎏㎎㏎㎞㎜㎝㏕㎡‰〒々℃℉ㄅㄆㄇㄈㄉㄊㄋㄌㄍㄎㄏㄐㄑㄒㄓㄔㄕㄖㄗㄘㄙㄚㄛㄜㄝㄞㄟㄠㄡㄢㄣㄤㄥㄦㄧㄨㄩ]/)) {
                paichu++;
            }
        }
        document.getElementById('hanzi').innerText = iTotal - paichu;
        document.getElementById('zishu').innerText = inum + iTotal - paichu;
        document.getElementById('biaodian').innerText = sTotal - iTotal + eTotal - inum - znum - gl + paichu;
        document.getElementById('zimu').innerText = znum;
        document.getElementById('shuzi').innerText = inum;
        document.getElementById("zifu").innerHTML = iTotal * 2 + (sTotal - iTotal) * 2 + eTotal;
    }
</script>
<script> 
$(document).ready(function(){
$("#wmd-editarea").append('<div class="field is-grouped"style="margin-top: 15px;"><span class="tag">共计：</span><div class="control"><div class="tags has-addons"><span class="tag is-dark" id="zishu">0</span> <span class="tag is-primary">个字数</span></div></div><div class="control"><div class="tags has-addons"><span class="tag is-dark" id="zifu">0</span> <span class="tag is-primary">个字符</span></div></div><span class="tag">包含：</span><div class="control"><div class="tags has-addons"><span class="tag is-light" id="hanzi">0</span> <span class="tag is-danger">个文字</span></div></div><div class="control"><div class="tags has-addons"><span class="tag is-light" id="biaodian">0</span> <span class="tag is-info">个符号</span></div></div><div class="control"><div class="tags has-addons"><span class="tag is-light" id="zimu">0</span> <span class="tag is-success">个字母</span></div></div><div class="control"><div class="tags has-addons"><span class="tag is-light" id="shuzi">0</span> <span class="tag is-warning">个数字</span></div></div></div>');
CountChineseCharacters();
});
</script>
<?php
    }
}

function getGravatar(string $email, int $s = 96, string $d = 'mp', string $r = 'g', bool $img = false, array $atts = []): string
{
    // 获取主题配置
    $options = Typecho_Widget::widget('Widget_Options')->themeOptions;
    
    $url = '';
    
    // QQ邮箱匹配（严格模式）
    if (preg_match('/^(\d{5,13})@qq\.com$/', strtolower(trim($email)), $matches)) {
        $url = 'https://q2.qlogo.cn/headimg_dl?dst_uin=' . $matches[1] . '&spec=' . $s;
    } else {
        // 自定义源
        $defaultGravatar = 'https://weavatar.com/avatar/';
        // 优先使用配置，否则用默认
        $gravatarBase = (!empty($options->gravatarUrl) ? rtrim($options->gravatarUrl, '/') . '/' : $defaultGravatar);
        $emailHash = md5(strtolower(trim($email)));
        $url = $gravatarBase . $emailHash . "?s=$s&d=$d&r=$r";
    }
    
    // 处理图片标签
    if ($img) {
        $url = '<img src="' . htmlspecialchars($url) . '"';
        foreach ($atts as $key => $val) {
            $url .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($val) . '"';
        }
        $url .= ' />';
    }
    
    return $url;
}

//文章阅读量
function get_post_view($archive): void
{
    $cid    = $archive->cid;
    $db     = Typecho_Db::get();
    $prefix = $db->getPrefix();
    if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')->page(1,1)))) {
        $db->query('ALTER TABLE `'. $prefix. 'contents` ADD `views` INT(10) DEFAULT 0;');
        echo 0;
        return;
    }
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid =?', $cid));
    if ($archive->is('single')) {
        $views = Typecho_Cookie::get('extend_contents_views');
        if(empty($views)){
            $views = [];
        }else{
            $views = explode(',', $views);
        }
        // 获取请求头信息
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '';
        if (!in_array($cid,$views) && ($referer === '' || strpos($referer, $currentUrl) === false)) {
            $db->query($db->update('table.contents')->rows(['views' => (int) $row['views'] + 1])->where('cid =?', $cid));
            $views[] = $cid;
            $views = implode(',', $views);
            // 设置 Cookie 过期时间为 1 天（86400 秒）
            Typecho_Cookie::set('extend_contents_views', $views, time() + 86400);
        }
    }
    echo $row['views'];
}

/* 获取评论ip属地 */
function convertip(string $ip): string
{  
    $ip1num = 0; 
    $ip2num = 0; 
    $ipAddr1 =""; 
    $ipAddr2 =""; 
    $dat_path = './qqwry.dat';  // qqwry.dat 文件需放置在typecho根目录  
    if(!preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) {  
        return '可能来自火星';  
    }   
    if(!$fd = @fopen($dat_path, 'rb')){  
        return '可能来自火星';  
    }   
    $ipParts = explode('.', $ip);  
    $ipNum = $ipParts[0] * 16777216 + $ipParts[1] * 65536 + $ipParts[2] * 256 + $ipParts[3];   
    $DataBegin = fread($fd, 4);  
    $DataEnd = fread($fd, 4);  
    $ipbegin = implode('', unpack('L', $DataBegin));  
    if($ipbegin < 0) $ipbegin += pow(2, 32);  
    $ipend = implode('', unpack('L', $DataEnd));  
    if($ipend < 0) $ipend += pow(2, 32);  
    $ipAllNum = ($ipend - $ipbegin) / 7 + 1;  
    $BeginNum = 0;  
    $EndNum = $ipAllNum;   
    while($ip1num>$ipNum || $ip2num<$ipNum) {  
        $Middle= (int)(($EndNum + $BeginNum) / 2);  
        fseek($fd, $ipbegin + 7 * $Middle);  
        $ipData1 = fread($fd, 4);  
        if(strlen($ipData1) < 4) {  
            fclose($fd);  
            return 'System Error';  
        } 
        $ip1num = implode('', unpack('L', $ipData1));  
        if($ip1num < 0) $ip1num += pow(2, 32);  
       
        if($ip1num > $ipNum) {  
            $EndNum = $Middle;  
            continue;  
        }  
        $DataSeek = fread($fd, 3);  
        if(strlen($DataSeek) < 3) {  
            fclose($fd);  
            return 'System Error';  
        }  
        $DataSeek = implode('', unpack('L', $DataSeek.chr(0)));  
        fseek($fd, $DataSeek);  
        $ipData2 = fread($fd, 4);  
        if(strlen($ipData2) < 4) {  
            fclose($fd);  
            return 'System Error';  
        }  
        $ip2num = implode('', unpack('L', $ipData2));  
        if($ip2num < 0) $ip2num += pow(2, 32);   
        if($ip2num < $ipNum) {  
            if($Middle == $BeginNum) {  
                fclose($fd);  
                return 'Unknown';  
            }  
            $BeginNum = $Middle;  
        }  
    }   
    $ipFlag = fread($fd, 1);  
    if($ipFlag == chr(1)) {  
        $ipSeek = fread($fd, 3);  
        if(strlen($ipSeek) < 3) {  
            fclose($fd);  
            return 'System Error';  
        }  
        $ipSeek = implode('', unpack('L', $ipSeek.chr(0)));  
        fseek($fd, $ipSeek);  
        $ipFlag = fread($fd, 1);  
    }  
    if($ipFlag == chr(2)) {  
        $AddrSeek = fread($fd, 3);  
        if(strlen($AddrSeek) < 3) {  
            fclose($fd);  
            return 'System Error';  
        }  
        $ipFlag = fread($fd, 1);  
        if($ipFlag == chr(2)) {  
            $AddrSeek2 = fread($fd, 3);  
            if(strlen($AddrSeek2) < 3) {  
                fclose($fd);  
                return 'System Error';  
            }  
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));  
            fseek($fd, $AddrSeek2);  
        } else {  
            fseek($fd, -1, SEEK_CUR);  
        }  
        while(($char = fread($fd, 1)) != chr(0))  
        $ipAddr2 .= $char;  
        $AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));  
        fseek($fd, $AddrSeek);  
        while(($char = fread($fd, 1)) != chr(0))  
        $ipAddr1 .= $char;  
    } else {  
        fseek($fd, -1, SEEK_CUR);  
        while(($char = fread($fd, 1)) != chr(0))  
        $ipAddr1 .= $char;  
        $ipFlag = fread($fd, 1);  
        if($ipFlag == chr(2)) {  
            $AddrSeek2 = fread($fd, 3);  
            if(strlen($AddrSeek2) < 3) {  
                fclose($fd);  
                return 'System Error';  
            }  
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));  
            fseek($fd, $AddrSeek2);  
        } else {  
            fseek($fd, -1, SEEK_CUR);  
        }  
        while(($char = fread($fd, 1)) != chr(0)){  
            $ipAddr2 .= $char;  
        }  
    }  
    fclose($fd);   
    if(preg_match('/http/i', $ipAddr2)) {  
        $ipAddr2 = '';  
    }  
    $ipaddr = "$ipAddr1 $ipAddr2";  
    $ipaddr = preg_replace('/CZ88.NET/is', '', $ipaddr);  
    $ipaddr = preg_replace('/^s*/is', '', $ipaddr);  
    $ipaddr = preg_replace('/s*$/is', '', $ipaddr);  
    if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {  
        $ipaddr = '可能来自火星';  
    }
    $ipaddr = iconv('gbk', 'utf-8//IGNORE', $ipaddr); 
    return $ipaddr;  
}

/**
 * 自定义参数解析函数
 * 将查询字符串转换为关联数组
 */
function custom_parse_query(string $str): array
{
    $params = [];
    if (empty($str)) {
        return $params;
    }
    
    // 分割参数对
    $pairs = explode(' ', trim($str));
    
    foreach ($pairs as $pair) {
        // 分割键值对
        $pos = strpos($pair, '=');
        if ($pos !== false) {
            $key = trim(substr($pair, 0, $pos));
            $value = trim(substr($pair, $pos + 1));
            
            // 去除值的引号
            if (($value !== '' && $value[0] === '"' && $value[strlen($value)-1] === '"') || 
                ($value !== '' && $value[0] === "'" && $value[strlen($value)-1] === "'")) {
                $value = substr($value, 1, -1);
            }
            
            $params[$key] = $value;
        }
    }
    
    return $params;
}

/**
 * tabs标签页短代码处理函数
 */
function tabs_shortcode(array $atts, ?string $content = null): string
{
    static $tabIndex = 0;
    $tabIndex++;
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    $defaultSelected = isset($atts['selected']) ? (int)$atts['selected'] : 1;
    $defaultSelected = max(1, $defaultSelected);
    
    // 使用更精确的模式捕获tab内容
    preg_match_all('/\{tab\s+name="([^"]+)"\}(.*?)\{\/tab\}/s', (string)$content, $matches, PREG_SET_ORDER);
    
    $tabNames = [];
    $tabContents = [];
    
    foreach ($matches as $match) {
        if (isset($match[1], $match[2])) {
            // 对每个标签内容进行独立的短代码解析
            $tabNames[] = $match[1];
            $widget = Typecho_Widget::widget('Widget_Abstract_Contents');
            $tabContents[] = parse_shortcodes(trim($match[2]), $widget, '');
        }
    }
    
    if (empty($tabNames)) {
        return '<div class="error-message">标签页内容不能为空</div>';
    }
    
    $totalTabs = count($tabNames);
    $selectedIndex = $defaultSelected - 1;
    $selectedIndex = max(0, min($totalTabs - 1, $selectedIndex));
    
    $tabsId = 'tabs-group-' . $tabIndex;
    
    $html = '<div class="shortcode-tabs" id="' . $tabsId . '">';
    $html .= '<div class="tabs-nav">';
    $html .= '<ul class="tabs-list">';
    foreach ($tabNames as $i => $name) {
        $activeClass = ($i == $selectedIndex) ? 'tabs-item-active' : '';
        $html .= '<li class="tabs-item ' . $activeClass . '" data-index="' . $i . '" data-tabs-id="' . $tabsId . '">';
        $html .= htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $html .= '</li>';
    }
    $html .= '</ul>';
    $html .= '</div>';
    
    $html .= '<div class="tabs-content">';
    foreach ($tabContents as $i => $content) {
        $activeClass = ($i == $selectedIndex) ? 'tabs-panel-active' : '';
        $html .= '<div class="tabs-panel ' . $activeClass . '" data-index="' . $i . '" data-tabs-id="' . $tabsId . '">';
        $html .= $content;
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

// 视频短代码处理函数
function video_shortcode(array $atts): string
{
    // 使用自定义参数解析函数处理传入的属性
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    
    // 默认参数
    $default_atts = [
        'src' => '',          // 视频地址
        'poster' => '',       // 视频封面
        'width' => '100%',    // 视频宽度
        'autoplay' => 'false',// 是否自动播放
        'loop' => 'false',    // 是否循环播放
        'preload' => 'auto',  // 预加载策略
        'lang' => 'zh-cn',    // 语言设置
        'mutex' => 'true',    // 是否互斥播放
        'theme' => '#b7daff', // 主题颜色
        'hotkey' => 'true',   // 是否启用热键
        'volume' => 0.7       // 音量大小
    ];
    $atts = array_merge($default_atts, $atts);

    // 如果未提供视频地址，返回空字符串
    if (empty($atts['src'])) {
        return '<p>视频地址未提供。</p>';
    }

    // 生成唯一的容器 ID
    $containerId = 'dplayer-' . uniqid();

    // 构建 DPlayer 播放器的 HTML 和 JavaScript
    $html = '<div id="' . $containerId . '" style="width: ' . htmlspecialchars($atts['width'], ENT_QUOTES) . ';"></div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dp = new DPlayer({
                container: document.getElementById("' . $containerId . '"),
                autoplay: ' . ($atts['autoplay'] === 'true' ? 'true' : 'false') . ',
                loop: ' . ($atts['loop'] === 'true' ? 'true' : 'false') . ',
                preload: "' . htmlspecialchars($atts['preload'], ENT_QUOTES) . '",
                lang: "' . htmlspecialchars($atts['lang'], ENT_QUOTES) . '",
                mutex: ' . ($atts['mutex'] === 'true' ? 'true' : 'false') . ',
                theme: "' . htmlspecialchars($atts['theme'], ENT_QUOTES) . '",
                hotkey: ' . ($atts['hotkey'] === 'true' ? 'true' : 'false') . ',
                volume: ' . (float)$atts['volume'] . ',
                video: {
                    url: "' . htmlspecialchars($atts['src'], ENT_QUOTES) . '",
                    pic: "' . htmlspecialchars($atts['poster'], ENT_QUOTES) . '"
                }
            });
        });
    </script>';

    return $html;
}

// 音频短代码处理函数
function audio_shortcode(array $atts): string
{
    // 使用自定义参数解析函数处理传入的属性
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    
    $default_atts = [
        'name' => '未知音频',      // 音频名称
        'artist' => '未知艺术家', // 音频作者
        'url' => '',            // 音频链接
        'cover' => ''          // 音频封面
    ];
    $atts = array_merge($default_atts, $atts);

    if ($atts['url']) {
        // 生成唯一的容器 ID 和变量名
        $containerId = 'aplayer-' . uniqid();
        $variableName = 'ap_' . uniqid();
        return '<div id="' . $containerId . '"></div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const ' . $variableName . ' = new APlayer({
                    container: document.getElementById("' . $containerId . '"),
                    audio: [{
                        name: "' . htmlspecialchars($atts['name'], ENT_QUOTES) . '",
                        artist: "' . htmlspecialchars($atts['artist'], ENT_QUOTES) . '",
                        url: "' . htmlspecialchars($atts['url'], ENT_QUOTES) . '",
                        cover: "' . htmlspecialchars($atts['cover'], ENT_QUOTES) . '"
                    }]
                });
            });
        </script>';
    }
    return '';
}

/**
 * 折叠面板短代码处理函数
 */
function collapse_shortcode(array $atts, ?string $content = null): string
{
    // 确保内容存在
    if (empty($content)) {
        return '<div class="error-message">折叠面板内容不能为空</div>';
    }
    
    // 解析短代码参数
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    
    // 提取并验证参数
    $title = isset($atts['title']) ? $atts['title'] : '折叠面板';
    $open = isset($atts['open']) ? filter_var($atts['open'], FILTER_VALIDATE_BOOLEAN) : false;
    
    // 生成唯一ID
    $panelId = 'collapse-panel-' . uniqid();
    
    // 构建class属性
    $classes = ['shortcode-collapse'];
    if ($open) {
        $classes[] = 'collapse-open';
    }
    
    // 过滤标题，防止XSS
    $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    
    // 处理内容中的短代码
    $widget = Typecho_Widget::widget('Widget_Abstract_Contents');
    $parsedContent = parse_shortcodes($content, $widget, '');
    
    // 构建HTML结构
    $html = '<div class="' . implode(' ', $classes) . '">';
    $html .= '<div class="collapse-header" role="button" tabindex="0" aria-controls="' . $panelId . '" aria-expanded="' . ($open ? 'true' : 'false') . '">';
    $html .= '<span class="collapse-title">' . $title . '</span>';
    $html .= '<span class="collapse-icon"><i class="fa fa-chevron-down"></i></span>';
    $html .= '</div>';
    $html .= '<div id="' . $panelId . '" class="collapse-content ' . ($open ? '' : 'hidden') . '">';
    $html .= $parsedContent;
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

/**
 * 附件下载卡片短代码处理函数
 */
function attachment_shortcode(array $atts): string
{
    // 确保atts是数组
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    
    // 提取并验证参数，使用PHP原生安全过滤函数
    $url = isset($atts['url']) ? htmlspecialchars($atts['url'], ENT_QUOTES, 'UTF-8') : '';
    $title = isset($atts['title']) ? htmlspecialchars($atts['title'], ENT_QUOTES, 'UTF-8') : '下载附件';
    $size = isset($atts['size']) ? htmlspecialchars($atts['size'], ENT_QUOTES, 'UTF-8') : '';
    // 支持Font Awesome图标，默认使用fa-file-o
    $icon = isset($atts['icon']) ? htmlspecialchars($atts['icon'], ENT_QUOTES, 'UTF-8') : 'fa-file-o';
    $type = isset($atts['type']) ? htmlspecialchars($atts['type'], ENT_QUOTES, 'UTF-8') : '';
    
    // 验证链接是否为空
    if (empty($url)) {
        return '<div class="shortcode-attachment error">附件链接不能为空</div>';
    }
    
    // 链接默认在当前窗口打开，不设置rel属性
    $target = '_self';
    $rel = '';
    
    // 构建class属性（移除new相关类）
    $classes = ['shortcode-attachment'];
    
    // 构建data属性（用于CSS文件类型识别）
    $dataAttributes = '';
    if (!empty($type)) {
        $dataAttributes = ' data-type="' . $type . '"';
    }
    
    // 根据文件类型自动设置图标（如果未指定）
    if ($icon === 'fa-file-o' && !empty($type)) {
        $typeIcons = [
            'pdf' => 'fa-file-pdf-o',
            'doc' => 'fa-file-word-o',
            'xls' => 'fa-file-excel-o',
            'zip' => 'fa-file-zip-o',
            'img' => 'fa-file-image-o',
            'video' => 'fa-file-video-o',
            'audio' => 'fa-file-audio-o',
            'txt' => 'fa-file-text-o'
        ];
        if (isset($typeIcons[$type])) {
            $icon = $typeIcons[$type];
        }
    }
    
    // 开始构建HTML
    $html = '<div class="' . implode(' ', $classes) . '"' . $dataAttributes . '>';
    $html .= '<div class="attachment-icon"><i class="fa ' . $icon . '" aria-hidden="true"></i></div>';
    $html .= '<div class="attachment-info">';
    $html .= '<div class="attachment-title">' . $title . '</div>';
    
    // 添加文件大小信息
    if (!empty($size)) {
        $html .= '<div class="attachment-size"><i class="fa fa-database" aria-hidden="true"></i> ' . $size . '</div>';
    }
    
    $html .= '</div>'; // 关闭.attachment-info
    
    // 构建下载链接（固定在当前窗口打开）
    $html .= '<a href="' . $url . '" class="attachment-download"';
    $html .= ' target="' . $target . '"';
    if (!empty($rel)) {
        $html .= ' rel="' . $rel . '"';
    }
    $html .= '><i class="fa fa-download" aria-hidden="true"></i></a>';
    
    $html .= '</div>'; // 关闭.shortcode-attachment
    
    return $html;
}

/**
 * 徽章短代码处理函数
 */
function badge_shortcode(array $atts, ?string $content = null): string
{
    // 确保内容存在
    if (empty($content)) {
        return '';
    }
    
    // 使用自定义参数解析函数
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    
    // 提取并验证参数
    $type = isset($atts['type']) ? $atts['type'] : 'default';
    $color = isset($atts['color']) ? $atts['color'] : '';
    $size = isset($atts['size']) ? $atts['size'] : '';
    $outline = isset($atts['outline']) ? filter_var($atts['outline'], FILTER_VALIDATE_BOOLEAN) : false;
    
    // 验证有效的类型
    $validTypes = ['default', 'success', 'warning', 'danger', 'info', 'orange', 'cyan', 'purple'];
    if (!in_array($type, $validTypes)) {
        $type = 'default';
    }
    
    // 验证有效的尺寸
    $validSizes = ['', 'sm', 'lg'];
    if (!in_array($size, $validSizes)) {
        $size = '';
    }
    
    // 构建class属性
    $classes = ['shortcode-badge', 'badge-' . $type];
    
    // 添加尺寸类
    if (!empty($size)) {
        $classes[] = 'badge-' . $size;
    }
    
    // 添加边框样式类
    if ($outline) {
        $classes[] = 'badge-outline';
    }
    
    $class = implode(' ', $classes);
    
    // 处理自定义颜色（使用PHP原生函数过滤）
    $style = '';
    if (!empty($color)) {
        // 简单验证颜色格式（十六进制或rgb/rgba）
        if (preg_match('/^#([0-9a-fA-F]{3}){1,2}$/', $color) || 
            preg_match('/^rgb\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*\)$/', $color) ||
            preg_match('/^rgba\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*,\s*[0-1](\.\d+)?\s*\)$/', $color)) {
            $style = ' style="background-color: ' . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . ';"';
        }
    }
    
    // 过滤内容确保安全
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    
    return '<span class="' . $class . '"' . $style . '>' . $content . '</span>';
}

/**
 * 按钮短代码处理函数
 */
function button_shortcode(array $atts, ?string $content = null): string
{
    // 确保内容存在，避免空按钮
    if (empty($content)) {
        $content = '按钮';
    }
    
    // 使用自定义参数解析函数
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    
    // 提取并过滤参数，设置默认值
    $url = isset($atts['url']) ? Typecho_Common::safeUrl($atts['url']) : '#';
    $type = isset($atts['type']) ? $atts['type'] : 'default';
    $size = isset($atts['size']) ? $atts['size'] : '';
    $target = isset($atts['target']) ? $atts['target'] : '_self';
    $block = isset($atts['block']) ? filter_var($atts['block'], FILTER_VALIDATE_BOOLEAN) : false;
    $rel = isset($atts['rel']) ? $atts['rel'] : '';
    
    // 验证链接目标是否有效
    $validTargets = ['_self', '_blank', '_parent', '_top'];
    if (!in_array($target, $validTargets)) {
        $target = '_self';
    }
    
    // 自动为外部链接添加noopener noreferrer
    if ($target === '_blank' && empty($rel)) {
        $rel = 'noopener noreferrer';
    }
    
    // 定义所有有效的按钮类型
    $validTypes = [
        'default', 'blue', 'red', 'orange', 'yellow', 
        'green', 'cyan', 'purple',
        'outline-blue', 'outline-red', 'outline-orange', 
        'outline-yellow', 'outline-green', 'outline-cyan', 'outline-purple'
    ];
    
    // 验证按钮类型，如果无效则使用默认值
    if (!in_array($type, $validTypes)) {
        $type = 'default';
    }
    
    // 构建class属性
    $class = ['shortcode-button'];
    
    // 处理边框按钮的特殊类名结构
    if (strpos($type, 'outline-') === 0) {
        $class[] = 'button-outline';
        $class[] = 'button-' . $type;
    } else {
        $class[] = 'button-' . $type;
    }
    
    // 添加尺寸类
    if (!empty($size) && in_array($size, ['sm', 'lg'])) {
        $class[] = 'button-' . $size;
    }
    
    // 添加块级类
    if ($block) {
        $class[] = 'button-block';
    }
    
    $class = implode(' ', $class);
    
    $output = '<a href="' . $url . '" class="' . $class . '"';
    $output .= ' target="' . htmlspecialchars($target) . '"';
    if (!empty($rel)) {
        $output .= ' rel="' . htmlspecialchars($rel) . '"';
    }
    $output .= '>';
    
    // 添加内容
    $output .= $content;
    
    // 关闭标签
    $output .= '</a>';
    
    return $output;
}

/**
 * 进度条短代码处理函数
 */
function progress_shortcode(array $atts): string
{
    // 使用自定义参数解析函数
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    $percent = isset($atts['percent']) ? (int)$atts['percent'] : 0;
    $title = isset($atts['title']) ? $atts['title'] : '';
    $type = isset($atts['type']) ? $atts['type'] : 'default';
    $striped = isset($atts['striped']) ? filter_var($atts['striped'], FILTER_VALIDATE_BOOLEAN) : false;
    $animated = isset($atts['animated']) ? filter_var($atts['animated'], FILTER_VALIDATE_BOOLEAN) : false;
    
    if ($percent < 0) $percent = 0;
    if ($percent > 100) $percent = 100;
    
    $class = 'shortcode-progress progress-' . $type;
    if ($striped) {
        $class .= ' progress-striped';
    }
    if ($animated) {
        $class .= ' progress-animated';
    }
    
    $html = '<div class="' . $class . '">';
    
    if (!empty($title)) {
        $html .= '<div class="progress-title">' . $title . ' (' . $percent . '%)</div>';
    }
    
    $html .= '<div class="progress-bar-container">
                <div class="progress-bar" style="width: ' . $percent . '%;">
                    <span class="progress-text">' . $percent . '%</span>
                </div>
              </div>
            </div>';
    
    return $html;
}

/**
 * 短代码解析函数
 */
function parse_shortcodes(?string $content, Widget_Abstract_Contents $widget, ?string $lastResult): string
{
    $content = empty($lastResult) ? $content : $lastResult;
    $content = (string)$content; // 确保内容为字符串类型
    
    if (!$widget instanceof Widget_Abstract_Contents || !$widget->isMarkdown) {
        return $content;
    }

    // 提取出所有代码块并存储，避免内部短代码被解析
    $codeBlocks = [];
    $content = preg_replace_callback(
        '/<pre(?:\s+[^>]*)?><code(?:\s+[^>]*)?>([\s\S]*?)<\/code><\/pre>/i',
        function(array $matches) use (&$codeBlocks): string {
            $placeholder = 'CODE_BLOCK_PLACEHOLDER_' . count($codeBlocks);
            $codeBlocks[$placeholder] = $matches[0];
            return $placeholder;
        },
        $content
    );

    $content = preg_replace_callback('/\[collapse\s+(.*?)\](.*?)\[\/collapse\]/s', function(array $matches) use ($widget): string {
        // 使用更严格的非贪婪匹配，添加界定符
        if (preg_match('/\[collapse\s+([^\]]*)\](.*?)\[\/collapse\]/s', $matches[0], $innerMatches)) {
            $atts = custom_parse_query($innerMatches[1]);
            $content = parse_shortcodes($innerMatches[2], $widget, '');
            return collapse_shortcode($atts, $content);
        }
        return $matches[0];
    }, $content);
    
    $content = preg_replace_callback('/\{tabs(.*?)\}(.*?)\{\/tabs\}/s', function(array $matches) use ($widget): string {
        if (preg_match('/\{tabs\s*([^}]*)\}(.*?)\{\/tabs\}/s', $matches[0], $innerMatches)) {
            $atts = custom_parse_query($innerMatches[1]);
            $content = parse_shortcodes($innerMatches[2], $widget, '');
            return tabs_shortcode($atts, $content);
        }
        return $matches[0];
    }, $content);
    
    $content = preg_replace_callback('/\[attachment\s+(.*?)\]/', function(array $matches): string {
        $atts = custom_parse_query($matches[1]);
        return attachment_shortcode($atts);
    }, $content);
    
    $content = preg_replace_callback('/\[badge\s+(.*?)\](.*?)\[\/badge\]/s', function(array $matches): string {
        $atts = custom_parse_query($matches[1]);
        return badge_shortcode($atts, $matches[2]);
    }, $content);
    
    $content = preg_replace_callback('/\[button\s+(.*?)\](.*?)\[\/button\]/s', function(array $matches): string {
        $atts = custom_parse_query($matches[1]);
        return button_shortcode($atts, $matches[2]);
    }, $content);
    
    $content = preg_replace_callback('/\[progress\s+(.*?)\]/', function(array $matches): string {
        $atts = custom_parse_query($matches[1]);
        return progress_shortcode($atts);
    }, $content);
    
    $content = preg_replace_callback('/\[video\s+(.*?)\]/', function(array $matches): string {
        $atts = custom_parse_query($matches[1]);
        return video_shortcode($atts);
    }, $content);
    
    $content = preg_replace_callback('/\[audio\s+(.*?)\]/', function(array $matches): string {
        $atts = custom_parse_query($matches[1]);
        return audio_shortcode($atts);
    }, $content);

    // 恢复原始代码块
    foreach ($codeBlocks as $placeholder => $codeBlock) {
        $content = str_replace($placeholder, $codeBlock, $content);
    }
    
    return $content;
}

// 注册短代码过滤器
Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = 'parse_shortcodes';