<?php

/* 获取主题当前版本号 */
function _getVersion()
{
  return "v1.2.4";
};

/* 主题初始化 */
function themeInit($archive){
  Helper::options()->commentsAntiSpam = false; //关闭反垃圾
  Helper::options()->commentsCheckReferer = false; //关闭检查评论来源URL与文章链接是否一致判断(否则会无法评论)
  Helper::options()->commentsMaxNestingLevels = '999'; //最大嵌套层数
  Helper::options()->commentsPageDisplay = 'first'; //强制评论第一页
  Helper::options()->commentsOrder = 'DESC'; //将最新的评论展示在前面    
  if ($archive->is('author')) {
    $archive->parameter->pageSize = 6; // 作者页面每6篇文章分页一次
  }
  if ($archive->is('category','av')) {
  $archive->parameter->pageSize = 6; // 分类缩略名为av的分类列表每6篇文章分页一次
  }
  $archive->content = a_class_replace($archive->content);//文章内容，让a_class_replace函数处理
  
  //文章描述中暴露短代码的问题
  if ($archive->is('single')){
    //注意：这里就是需要过滤短代码的正则表达，请根据实际情况自行修改。
    $archiv = preg_replace('/{bl av="(.+?)"}/sm','',$archive->getDescription());
    //这个方法是typecho提供设置描述的方法
    $archive->setDescription($archiv);
  }
}

function a_class_replace($content){
  $content = preg_replace('#<a(.*?) href="([^"]*/)?(([^"/]*)\.[^"]*)"(.*?)>#',
    '<a$1 href="$2$3"$5 target="_blank">', $content);//给文章每个超链接点击后新窗口打开，原理就是用正则替换文章内容
  return $content;
}

// 文章图片逻辑
function processContent($content, $title) {
    $pattern = '/\<img.*?src\=\"(.*?)\"[^>]*>/i';
    $replacement = '<a href="$1" class="index-img" data-fancybox><img data-src="$1" loading="lazy" alt="' . $title . '" title="点击查看大图"></a>';
    return preg_replace($pattern, $replacement, $content);
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

/* 通过邮箱生成头像地址 */
function _getAvatarByMail($mail)
{
  $gravatarsUrl = Helper::options()->JCustomAvatarSource ? Helper::options()->JCustomAvatarSource : 'https://weavatar.com/avatar/';
  $mailLower = strtolower($mail);
  $md5MailLower = md5($mailLower);
  $qqMail = str_replace('@qq.com', '', $mailLower);
  if (strstr($mailLower, "qq.com") && is_numeric($qqMail) && strlen($qqMail) < 11 && strlen($qqMail) > 4) {
    echo 'https://thirdqq.qlogo.cn/g?b=qq&nk=' . $qqMail . '&s=100';
  } else {
    echo $gravatarsUrl . $md5MailLower . '?d=mm';
  }
};

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

/** 获取评论者ip属地 */
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

// 视频短代码处理函数
function video_shortcode($atts) {
    // 默认参数
    $default_atts = array(
        'src' => '',          // 视频地址
        'width' => '640',     // 视频宽度
        'height' => '360',    // 视频高度
        'poster' => '',       // 视频封面
        'preload' => 'none',  // 视频预加载方式（none, metadata, auto）
        'controls' => 'true'  // 是否显示控制栏
    );
    $atts = array_merge($default_atts, $atts);

    // 如果未提供视频地址，返回空字符串
    if (empty($atts['src'])) {
        return '';
    }

    // 构建视频标签
    $video_html = '<div class="custom-video-container">';
    $video_html .= '<video width="' . htmlspecialchars($atts['width'], ENT_QUOTES) . '"';
    $video_html .= ' height="' . htmlspecialchars($atts['height'], ENT_QUOTES) . '"';
    $video_html .= ' preload="' . htmlspecialchars($atts['preload'], ENT_QUOTES) . '"';
    $video_html .= !empty($atts['poster']) ? ' poster="' . htmlspecialchars($atts['poster'], ENT_QUOTES) . '"' : '';
    $video_html .= $atts['controls'] === 'true' ? ' controls' : '';
    $video_html .= '>';
    $video_html .= '<source src="' . htmlspecialchars($atts['src'], ENT_QUOTES) . '" type="video/mp4">';
    $video_html .= '你的浏览器不支持视频播放。';
    $video_html .= '</video>';
    $video_html .= '</div>';

    return $video_html;
}

// 音频短代码处理函数
function audio_shortcode($atts) {
    $default_atts = array(
        'name' => '未知音频',
        'artist' => '未知艺术家',
        'url' => '',
        'cover' => ''
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

// 短代码解析函数
function parse_shortcodes($content) {
    $shortcodes = array(
        'video' => 'video_shortcode',
        'audio' => 'audio_shortcode'
    );

    foreach ($shortcodes as $tag => $function) {
        $pattern = '/\['.$tag.'(.*?)\]/is';
        preg_match_all($pattern, $content, $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $key => $match) {
                $atts = array();
                if (isset($matches[1][$key])) {
                    preg_match_all('/(\w+)\s*=\s*"([^"]*)"/', $matches[1][$key], $att_matches);
                    if (!empty($att_matches[1])) {
                        foreach ($att_matches[1] as $i => $att_name) {
                            $atts[$att_name] = $att_matches[2][$i];
                        }
                    }
                }
                $content = str_replace($match, call_user_func($function, $atts), $content);
            }
        }
    }
    return $content;
}

// 为Typecho文章内容添加短代码过滤器
Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = function($content, $widget, $lastResult) {
    $content = empty($lastResult) ? $content : $lastResult;
    return parse_shortcodes($content);
};