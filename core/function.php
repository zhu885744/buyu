<?php
/* 获取主题当前版本号 */
function _getVersion()
{
  return "v1.3.1";
}

// 定义全局函数 get_theme_url 用于获取静态资源 URL
function get_theme_url($path) {
    $options = Typecho_Widget::widget('Widget_Options');
    $cdnUrl = $options->JAssetsURL;
    if (!empty($cdnUrl)) {
        return rtrim($cdnUrl, '/') . '/' . ltrim($path, '/');
    }
    return Typecho_Common::url($path, $options->themeUrl);
}

// 文章内容内的超链接点击后新窗口打开
function a_class_replace($content){
  $content = preg_replace('#<a(.*?) href="([^"]*/)?(([^"/]*)\.[^"]*)"(.*?)>#',
    '<a$1 href="$2$3"$5 target="_blank">', $content);
  return $content;
}

/**
 * 处理文章内容渲染
 * 
 * @param string $content 文章内容HTML
 * @param string $title 文章标题，用于图片alt属性
 * @param bool $allowRelativeUrls 是否允许相对路径图片
 * @return string 处理后的文章内容
 */
function processContent($content, $title, $allowRelativePath = false) {
    // 更精确的图片标签匹配正则，考虑单引号和双引号的情况
    $pattern = '/<img\s+[^>]*src=(["\'])(.*?)\1[^>]*>/i';
    
    return preg_replace_callback($pattern, function ($matches) use ($title, $allowRelativePath) {
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
function isRelativePath($path) {
    // 简单判断：不包含协议且不以/开头的路径视为相对路径
    return !preg_match('/^[a-zA-Z]+:\/\//', $path) && strpos($path, '/') !== 0;
}

/**
 * 将文章发布时间转换为友好的时间差显示
 * @param \Typecho\Date $date 文章发布时间对象
 * @return string 格式化后的时间字符串 
 */
function time_ago($date) {
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
        return floor($time_diff / $minute) . "分钟前发布";
    } elseif ($time_diff < $day) {
        return floor($time_diff / $hour) . "小时前发布";
    } elseif ($time_diff < $month) {
        return floor($time_diff / $day) . "天前发布";
    } elseif ($time_diff < $year) {
        return floor($time_diff / $month) . "个月前发布";
    } else {
        return $date->format('Y年m月d日'); // 超过1年直接显示完整日期
    }
}

// 评论者等级、评论博主标签显示
function dengji($i) {
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
        echo '<span class="comment-badge badge-admin">博主</span>';
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
        echo '<span class="comment-badge badge-lv1">Lv.1</span>';
    } elseif ($rbq < 30) {    // 10-29条
        echo '<span class="comment-badge badge-lv2">Lv.2</span>';
    } elseif ($rbq < 60) {    // 30-59条
        echo '<span class="comment-badge badge-lv3">Lv.3</span>';
    } elseif ($rbq < 100) {   // 60-99条
        echo '<span class="comment-badge badge-lv4">Lv.4</span>';
    } elseif ($rbq < 150) {   // 100-149条
        echo '<span class="comment-badge badge-lv5">Lv.5</span>';
    } else {                  // 150条及以上
        echo '<span class="comment-badge badge-soulmate">知己</span>';
    }
}

// 附件页面和作者页面重定向到404页面
function redirect_404(){
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
if (isset($_GET['action']) && ($_GET['action'] == 'like' || $_GET['action'] == 'get_like') && isset($_GET['cid'])) {
    $cid = intval($_GET['cid']);
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    // 简单IP限制
    if ($_GET['action'] == 'like') {
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
            $ip = $_SERVER['REMOTE_ADDR'];
            $key = 'like_' . $cid . '_' . md5($ip);
        }

        if (isset($_SESSION[$key])) {
            header('Content-Type: application/json');
            echo json_encode(['success'=>false, 'msg'=>'您已经点过赞啦！']);
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
        'count' => intval($row['agree'])
    ]);
    exit;
}

/* 判断评论敏感词是否在字符串内 */
function _checkSensitiveWords($words_str, $str)
{
  $words = explode("||", $words_str);
  if (empty($words)) {
    return false;
  }
  foreach ($words as $word) {
    if (false !== strpos($str, trim($word))) {
      return true;
    }
  }
  return false;
}

/* 文章编辑器添加字符统计 */
Typecho_Plugin::factory('admin/write-post.php')->bottom = array('myyodu', 'one');
Typecho_Plugin::factory('admin/write-page.php')->bottom = array('myyodu', 'one');
class myyodu {
    public static function one()
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
        Words = document.getElementById('text').value;
        var W = new Object();
        var Result = new Array();
        var iNumwords = 0;
        var sNumwords = 0;
        var sTotal = 0;
        var iTotal = 0;
        var eTotal = 0;
        var otherTotal = 0;
        var bTotal = 0;
        var inum = 0;
      var znum = 0;
      var gl = 0;
      var paichu = 0;
        for (i = 0; i < Words.length; i++) {
            var c = Words.charAt(i);
            if (c.match(/[\u4e00-\u9fa5]/) || c.match(/[\u0800-\u4e00]/) || c.match(/[\uac00-\ud7ff]/)) {
                if (isNaN(W[c])) {
                    iNumwords++;
                    W[c] = 1;
                }
                iTotal++;
            }
        }
        for (i = 0; i < Words.length; i++) {
            var c = Words.charAt(i);
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

function getGravatar($email, $s = 96, $d = 'mp', $r = 'g', $img = false, $atts = array()){
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
function get_post_view($archive)
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
            $views = array();
        }else{
            $views = explode(',', $views);
        }
        // 获取请求头信息
        $referer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : '';
        $currentUrl = $_SERVER['REQUEST_URI'];
        if (!in_array($cid,$views) && ($referer === '' || strpos($referer, $currentUrl) === false)) {
            $db->query($db->update('table.contents')->rows(array('views' => (int) $row['views'] + 1))->where('cid =?', $cid));
            array_push($views, $cid);
            $views = implode(',', $views);
            // 设置 Cookie 过期时间为 1 天（86400 秒）
            Typecho_Cookie::set('extend_contents_views', $views, time() + 86400);
        }
    }
    echo $row['views'];
}

/* 获取评论ip属地 */
function convertip($ip){  
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
  $ip = explode('.', $ip);  
  $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];   
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
    $Middle= intval(($EndNum + $BeginNum) / 2);  
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
 * 自定义参数解析函数，用于替代Typecho_Common::parseQuery()
 * 将查询字符串转换为关联数组
 */
function custom_parse_query($str) {
    $params = array();
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
            if (($value[0] == '"' && $value[strlen($value)-1] == '"') || 
                ($value[0] == "'" && $value[strlen($value)-1] == "'")) {
                $value = substr($value, 1, -1);
            }
            
            $params[$key] = $value;
        }
    }
    
    return $params;
}

// 视频短代码处理函数
function video_shortcode($atts) {
    // 使用自定义参数解析函数处理传入的属性
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    
    // 默认参数
    $default_atts = array(
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
    );
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
                volume: ' . floatval($atts['volume']) . ',
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
function audio_shortcode($atts) {
    // 使用自定义参数解析函数处理传入的属性
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    
    $default_atts = array(
        'name' => '未知音频',      // 音频名称
        'artist' => '未知艺术家', // 音频作者
        'url' => '',            // 音频链接
        'cover' => ''          // 音频封面
    );
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
 * 折叠面板短代码处理函数（默认只显示下箭头）
 */
function collapse_shortcode($atts, $content = null) {
    // 确保内容存在
    if (empty($content)) {
        return '<div class="error-message">折叠面板内容不能为空</div>';
    }
    
    // 解析短代码参数
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    
    // 提取并验证参数
    $title = isset($atts['title']) ? $atts['title'] : '折叠面板';
    $open = isset($atts['open']) ? filter_var($atts['open'], FILTER_VALIDATE_BOOLEAN) : false;
    $type = isset($atts['type']) ? $atts['type'] : 'default';
    
    // 验证有效的类型
    $validTypes = ['default', 'success', 'warning', 'danger', 'info', 'primary'];
    if (!in_array($type, $validTypes)) {
        $type = 'default';
    }
    
    // 生成唯一ID
    $panelId = 'collapse-panel-' . uniqid();
    
    // 构建class属性
    $classes = ['shortcode-collapse', 'collapse-' . $type];
    if ($open) {
        $classes[] = 'collapse-open';
    }
    
    // 过滤标题，防止XSS
    $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    
    // 处理内容中的短代码
    $widget = Typecho_Widget::widget('Widget_Abstract_Contents');
    $parsedContent = parse_shortcodes($content, $widget, '');
    
    // 构建HTML结构（固定只输出下箭头，通过CSS旋转实现状态变化）
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
function attachment_shortcode($atts) {
    // 确保atts是数组
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    
    // 提取并验证参数，使用PHP原生安全过滤函数
    $url = isset($atts['url']) ? htmlspecialchars($atts['url'], ENT_QUOTES, 'UTF-8') : '';
    $title = isset($atts['title']) ? htmlspecialchars($atts['title'], ENT_QUOTES, 'UTF-8') : '下载附件';
    $size = isset($atts['size']) ? htmlspecialchars($atts['size'], ENT_QUOTES, 'UTF-8') : '';
    $icon = isset($atts['icon']) ? htmlspecialchars($atts['icon'], ENT_QUOTES, 'UTF-8') : '📎';
    $type = isset($atts['type']) ? htmlspecialchars($atts['type'], ENT_QUOTES, 'UTF-8') : '';
    $new = isset($atts['new']) ? filter_var($atts['new'], FILTER_VALIDATE_BOOLEAN) : false;
    $target = isset($atts['target']) ? htmlspecialchars($atts['target'], ENT_QUOTES, 'UTF-8') : '_blank';
    
    // 验证链接是否为空
    if (empty($url)) {
        return '<div class="shortcode-attachment error">附件链接不能为空</div>';
    }
    
    // 验证链接目标是否有效
    $validTargets = ['_self', '_blank', '_parent', '_top'];
    if (!in_array($target, $validTargets)) {
        $target = '_blank';
    }
    
    // 自动为新窗口添加安全属性
    $rel = 'noopener noreferrer';
    if ($target !== '_blank') {
        $rel = '';
    }
    
    // 构建class属性
    $classes = ['shortcode-attachment'];
    if ($new) {
        $classes[] = 'new';
    }
    
    // 构建data属性（用于CSS文件类型识别）
    $dataAttributes = '';
    if (!empty($type)) {
        $dataAttributes = ' data-type="' . $type . '"';
    }
    
    // 开始构建HTML
    $html = '<div class="' . implode(' ', $classes) . '"' . $dataAttributes . '>';
    $html .= '<div class="attachment-icon">' . $icon . '</div>';
    $html .= '<div class="attachment-info">';
    $html .= '<div class="attachment-title">' . $title . '</div>';
    
    // 添加文件大小信息
    if (!empty($size)) {
        $html .= '<div class="attachment-size">' . $size . '</div>';
    }
    
    $html .= '</div>'; // 关闭.attachment-info
    
    // 构建下载链接
    $html .= '<a href="' . $url . '" class="attachment-download"';
    $html .= ' target="' . $target . '"';
    if (!empty($rel)) {
        $html .= ' rel="' . $rel . '"';
    }
    $html .= '>下载</a>';
    
    $html .= '</div>'; // 关闭.shortcode-attachment
    
    return $html;
}

/**
 * 徽章短代码处理函数
 */
function badge_shortcode($atts, $content = null) {
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
    
    // 添加轮廓样式类
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
 * 按钮短代码处理函数（支持轮廓按钮）
 */
function button_shortcode($atts, $content = null) {
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
    
    // 处理轮廓按钮的特殊类名结构
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
function progress_shortcode($atts) {
    // 使用自定义参数解析函数
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    $percent = isset($atts['percent']) ? intval($atts['percent']) : 0;
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
 * tabs标签页短代码处理函数
 */
function tabs_shortcode($atts, $content = null) {
    static $tabIndex = 0;
    $tabIndex++; // 确保每个标签页组ID唯一
    
    // 解析外层tabs参数
    $atts = is_array($atts) ? $atts : custom_parse_query($atts);
    $defaultSelected = isset($atts['selected']) ? intval($atts['selected']) : 1;
    $defaultSelected = max(1, $defaultSelected); // 确保至少为1
    
    // 提取所有tab子标签
    preg_match_all('/\{tab\s+name="([^"]+)"\}(.*?)\{\/tab\}/s', $content, $matches);
    $tabNames = $matches[1];
    $tabContents = $matches[2];
    
    // 验证标签页内容
    if (empty($tabNames)) {
        return '<div class="error-message">标签页内容不能为空</div>';
    }
    
    // 修正默认选中项（防止超出范围）
    $totalTabs = count($tabNames);
    $selectedIndex = $defaultSelected - 1; // 转换为0基索引
    $selectedIndex = max(0, min($totalTabs - 1, $selectedIndex));
    
    // 生成唯一ID
    $tabsId = 'tabs-group-' . $tabIndex;
    
    // 构建标签页HTML
    $html = '<div class="shortcode-tabs" id="' . $tabsId . '">';
    
    // 标签页导航
    $html .= '<div class="tabs-nav">';
    $html .= '<ul class="tabs-list">';
    foreach ($tabNames as $i => $name) {
        $activeClass = ($i == $selectedIndex) ? 'tabs-item-active' : '';
        $html .= '<li class="tabs-item ' . $activeClass . '" data-index="' . $i . '">';
        $html .= htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $html .= '</li>';
    }
    $html .= '</ul>';
    $html .= '</div>';
    
    // 标签页内容
    $html .= '<div class="tabs-content">';
    foreach ($tabContents as $i => $content) {
        $activeClass = ($i == $selectedIndex) ? 'tabs-panel-active' : '';
        // 处理内容中的嵌套短代码
        $widget = Typecho_Widget::widget('Widget_Abstract_Contents');
        $parsedContent = parse_shortcodes(trim($content), $widget, '');
        $html .= '<div class="tabs-panel ' . $activeClass . '" data-index="' . $i . '">';
        $html .= $parsedContent;
        $html .= '</div>';
    }
    $html .= '</div>';
    
    $html .= '</div>';
    return $html;
}

/**
 * 短代码解析函数
 */
function parse_shortcodes($content, $widget, $lastResult) {
    $content = empty($lastResult) ? $content : $lastResult;
    
    if (!$widget instanceof Widget_Abstract_Contents || !$widget->isMarkdown) {
        return $content;
    }

    // 解析标签页（tabs）短代码
    $content = preg_replace_callback('/\{tabs(.*?)\}(.*?)\{\/tabs\}/s', function($matches) use ($widget) {
        $atts = custom_parse_query($matches[1]);
        $content = parse_shortcodes($matches[2], $widget, '');
        return tabs_shortcode($atts, $content);
    }, $content);
    
    // 解析附件下载卡片短代码
    $content = preg_replace_callback('/\[attachment\s+(.*?)\]/', function($matches) {
        // 使用自定义参数解析函数
        $atts = custom_parse_query($matches[1]);
        return attachment_shortcode($atts);
    }, $content);
    
    // 解析徽章短代码
    $content = preg_replace_callback('/\[badge\s+(.*?)\](.*?)\[\/badge\]/s', function($matches) {
        // 使用自定义参数解析函数
        $atts = custom_parse_query($matches[1]);
        return badge_shortcode($atts, $matches[2]);
    }, $content);
    
    // 解析按钮短代码
    $content = preg_replace_callback('/\[button\s+(.*?)\](.*?)\[\/button\]/s', function($matches) {
        // 使用自定义参数解析函数
        $atts = custom_parse_query($matches[1]);
        return button_shortcode($atts, $matches[2]);
    }, $content);
    
    // 解析进度条短代码
    $content = preg_replace_callback('/\[progress\s+(.*?)\]/', function($matches) {
        // 使用自定义参数解析函数
        $atts = custom_parse_query($matches[1]);
        return progress_shortcode($atts);
    }, $content);
    
    // 解析视频短代码
    $content = preg_replace_callback('/\[video\s+(.*?)\]/', function($matches) {
        // 使用自定义参数解析函数
        $atts = custom_parse_query($matches[1]);
        return video_shortcode($atts);
    }, $content);
    
    // 解析音频短代码
    $content = preg_replace_callback('/\[audio\s+(.*?)\]/', function($matches) {
        // 使用自定义参数解析函数
        $atts = custom_parse_query($matches[1]);
        return audio_shortcode($atts);
    }, $content);

    // 折叠面板短代码解析
    $content = preg_replace_callback('/\[collapse\s+(.*?)\](.*?)\[\/collapse\]/s', function($matches) use ($widget) {
        $atts = custom_parse_query($matches[1]);
        // 手动处理内容中的短代码
        $content = parse_shortcodes($matches[2], $widget, '');
        return collapse_shortcode($atts, $content);
    }, $content);
    
    return $content;
}

// 注册短代码过滤器
Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = 'parse_shortcodes';