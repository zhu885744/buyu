<?php
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
require_once("phpmailer.php");
require_once("smtp.php");

/**
 * 评论拦截处理函数，用于对用户评论进行多项规则检查
 *
 * 该函数会对用户评论内容进行字数限制检查、敏感词检查以及中文内容检查，
 * 根据不同的检查结果进行相应处理，如抛出异常、设置评论状态等。
 *
 * @param array $comment 包含评论信息的数组，其中 'text' 为评论内容
 * @return array 处理后的评论信息数组
 * @throws Typecho_Widget_Exception 当评论内容超过字数限制或包含敏感词且处理动作设置为失败时抛出
 */
Typecho_Plugin::factory('Widget_Feedback')->comment = array('Intercept', 'message');
class Intercept
{
    public static function message($comment)
    {
        // 修复：将错误级日志改为通知级，避免刷屏（原error_log会触发错误日志）
        trigger_error("Intercept::message function is called.", E_USER_NOTICE);

        // 判断用户评论内容是否超过了最大字数限制
        if (Helper::options()->JTextLimit) {
            // 准确计算包含emoji和颜文字的评论内容的长度，将emoji和颜文字统一视为一个字符
            $contentLength = mb_strlen(preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}]/u', 'x', $comment['text']), 'UTF-8');
            // 若评论内容长度超过最大限制
            if ($contentLength > Helper::options()->JTextLimit) {
                // 抛出异常，提示评论内容超过最大字数限制
                throw new Typecho_Widget_Exception('评论内容超过了最大字数限制', 403);
            }
        }

        // 判断评论内容是否包含敏感词
        if (Helper::options()->JSensitiveWords) {
            // 调用 _checkSensitiveWords 函数检查评论内容是否包含敏感词（已实现该函数）
            if (self::_checkSensitiveWords(Helper::options()->JSensitiveWords, $comment['text'])) {
                // 获取敏感词处理动作配置
                $action = Helper::options()->JSensitiveWordsAction ?? 'none'; // 增加默认值，避免未配置时报错
                switch ($action) {
                    case 'none':
                        // 无动作，不做处理
                        break;
                    case 'waiting':
                        // 将评论状态设置为待审核
                        $comment['status'] = 'waiting';
                        break;
                    case 'fail':
                        // 抛出异常，提示评论包含敏感词汇，评论失败
                        throw new Typecho_Widget_Exception('评论包含敏感词汇，评论失败', 403);
                        break;
                }
            }
        }
        
        // 判断评论是否至少包含一个中文
        if (Helper::options()->JLimitOneChinese === "on") {
            // 使用正则表达式检查评论内容是否包含中文
            if (preg_match("/[\x{4e00}-\x{9fa5}]/u", $comment['text']) == 0) {
                // 若不包含中文，将评论状态设置为待审核
                $comment['status'] = 'waiting';
            }
        }

        // 删除记录评论内容的 Cookie
        Typecho_Cookie::delete('__typecho_remember_text');
        // 返回处理后的评论信息数组
        return $comment;
    }

    /**
    * 敏感词检查函数（增强版）
    * @param string $sensitiveWords 敏感词列表（逗号分隔）
    * @param string $content 评论内容
    * @return bool 是否包含敏感词
    */
    private static function _checkSensitiveWords($sensitiveWords, $content)
    {
    // 调试：记录原始配置和评论
    //trigger_error("敏感词原始配置: [{$sensitiveWords}] | 评论内容: [{$content}]", E_USER_NOTICE);
    
    if (empty($sensitiveWords)) {
        return false;
    }
    
    // 支持多种分隔符（逗号、||），统一转为逗号后分割
    $sensitiveWords = str_replace('||', ',', $sensitiveWords);
    $words = array_map('trim', explode(',', $sensitiveWords));
    $words = array_filter($words); // 过滤空值
    
    // 调试：记录分割后的敏感词列表
    //trigger_error("分割后敏感词列表: " . print_r($words, true), E_USER_NOTICE);
    
    // 预处理评论内容（小写、全角转半角）
    $content = mb_strtolower($content);
    $content = self::convertFullWidthToHalfWidth($content);
    
    foreach ($words as $word) {
        if (empty($word)) continue;
        
        // 预处理敏感词
        $word = mb_strtolower($word);
        $word = self::convertFullWidthToHalfWidth($word);
        
        // 正则匹配（支持部分匹配，如"广告"匹配"小广告"）
        if (preg_match('/' . preg_quote($word, '/') . '/ui', $content)) {
            //trigger_error("匹配到敏感词: [{$word}]", E_USER_NOTICE);
            return true;
        }
    }
    
    return false;
    }

    /**
    * 全角字符转半角字符
    */
    private static function convertFullWidthToHalfWidth($str)
    {
    $fullWidthChars = array(
        '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
        '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
        'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
        'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
        'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
        'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
        'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
        'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
        'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
        'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
        'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
        'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
        'ｙ' => 'y', 'ｚ' => 'z', '！' => '!', '？' => '?', '＝' => '=',
        '＋' => '+', '－' => '-', '＊' => '*', '／' => '/', '（' => '(',
        '）' => ')', '＜' => '<', '＞' => '>', '，' => ',', '．' => '.',
        '；' => ';', '：' => ':', '＂' => '"', '＇' => '\'', '［' => '[',
        '］' => ']', '｛' => '{', '｝' => '}', '＼' => '\\', '｜' => '|',
        '％' => '%', '＠' => '@', '＃' => '#', '＄' => '$', '＆' => '&',
        '＿' => '_', '～' => '~', '`' => '`', '^' => '^'
    );
    
    return strtr($str, $fullWidthChars);
    }
}

/**
 * 发送评论邮件通知
 *
 * 根据评论的不同情况（回复他人、游客直接评论、游客回复他人评论等），
 * 向相应的用户发送邮件通知。
 */
if (
    Helper::options()->JCommentMail === 'on' &&
    !empty(Helper::options()->JCommentMailHost) &&
    !empty(Helper::options()->JCommentMailPort) &&
    !empty(Helper::options()->JCommentMailFromName) &&
    !empty(Helper::options()->JCommentMailAccount) &&
    !empty(Helper::options()->JCommentMailPassword) &&
    !empty(Helper::options()->JCommentSMTPSecure)
) {
    Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('Email', 'send');
}

class Email
{
    public static function send($comment)
    {
        try {
            // 创建一个新的 PHPMailer 实例
            $mail = new PHPMailer(true); // 启用异常模式
            // 设置邮件发送方式为 SMTP
            $mail->isSMTP();
            // 开启 SMTP 认证
            $mail->SMTPAuth = true;
            // 设置邮件字符编码为 UTF-8
            $mail->CharSet = 'UTF-8';
            // 关闭调试模式（生产环境禁用，调试时可改为2）
            $mail->SMTPDebug = 0;
            // 设置 SMTP 安全协议
            $mail->SMTPSecure = Helper::options()->JCommentSMTPSecure;
            // 设置 SMTP 服务器地址
            $mail->Host = Helper::options()->JCommentMailHost;
            // 设置 SMTP 服务器端口（确保为数字）
            $mail->Port = (int)Helper::options()->JCommentMailPort;
            // 设置发件人姓名
            $mail->FromName = Helper::options()->JCommentMailFromName;
            // 设置 SMTP 用户名
            $mail->Username = Helper::options()->JCommentMailAccount;
            // 设置发件人邮箱
            $mail->From = Helper::options()->JCommentMailAccount;
            // 设置 SMTP 密码（授权码）
            $mail->Password = Helper::options()->JCommentMailPassword;
            // 设置邮件内容为 HTML 格式
            $mail->isHTML(true);

            // 修复：补充邮件HTML内容（原代码为空，导致邮件无内容）
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
            <meta charset="UTF-8">
            <title>{title}</title>
            <style>
            /* 基础样式重置 */
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
            line-height: 1.7; 
            color: #444; 
            background-color: #f8f9fa; 
            padding: 20px 0;
            }
            /* 容器样式 */
            .container { 
            max-width: 720px; 
            margin: 0 auto; 
            background: #fff; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
            overflow: hidden;
            }
            /* 头部品牌区域 */
            .mail-header { 
            background: #165DFF; 
            padding: 24px 30px; 
            color: #fff;
            }
            .brand { 
            display: flex; 
            align-items: center; 
            gap: 12px;
            }
            .brand-name { 
            font-size: 18px; 
            font-weight: 600;
            }
            /* 内容区域 */
            .mail-content { 
            padding: 30px; 
            }
            .mail-title { 
            font-size: 22px; 
            color: #222; 
            margin-bottom: 20px; 
            padding-bottom: 15px; 
            border-bottom: 1px solid #f0f0f0;
            }
            .subtitle { 
            color: #666; 
            margin-bottom: 24px; 
            font-size: 15px;
            }
            /* 评论内容卡片 */
            .comment-card { 
            background: #f9fafb; 
            border-radius: 8px; 
            padding: 20px; 
            margin: 20px 0 30px; 
            border-left: 4px solid #165DFF; 
            font-size: 15px;
            }
            .comment-content { 
            line-height: 1.8; 
            color: #333;
            }
            /* 操作按钮 */
            .action-btn { 
            display: inline-block; 
            background: #165DFF; 
            color: #fff; 
            padding: 12px 24px; 
            border-radius: 6px; 
            text-decoration: none; 
            font-weight: 500; 
            margin: 10px 0 25px; 
            transition: background 0.3s;
            }
            .action-btn:hover { 
            background: #0E42D2; 
            }
            /* 底部信息 */
            .mail-footer { 
            padding: 20px 30px; 
            background: #f9fafb; 
            border-top: 1px solid #f0f0f0; 
            font-size: 14px; 
            color: #888;
            }
            .footer-note { 
            margin-bottom: 12px; 
            }
            .unsubscribe { 
            color: #165DFF; 
            text-decoration: none;
            }
            .unsubscribe:hover { 
            text-decoration: underline;
            }
            /* 响应式适配 */
            @media (max-width: 600px) {
            .container { 
                width: 95%; 
                margin: 0 auto;
            }
            .mail-header, .mail-content, .mail-footer { 
                padding: 20px 15px;
            }
            .mail-title { 
                font-size: 18px;
            }
            .action-btn { 
                width: 100%; 
                text-align: center;
            }
            }
            </style>
            </head>
            <body>
            <div class="container">
            <!-- 邮件头部品牌区域 -->
            <div class="mail-header">
            <div class="brand">
                <div class="brand-name">{title}</div>
            </div>
            </div>

            <!-- 邮件主要内容 -->
            <div class="mail-content">
            <p class="subtitle">{subtitle}</p>

            <div class="comment-card">
                <div class="comment-content">{content}</div>
            </div>
            
            <a href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank" class="action-btn">查看评论详情</a>
            </div>

            <!-- 邮件底部信息 -->
            <div class="mail-footer">
            <p class="footer-note">这是自动发送的通知邮件，如有疑问可通过站点内联系方式找我</p>
            </div>
            </div>
            </body>
            </html>';

            // 获取评论内容并处理图片标签
            $text = $comment->text;
            $text = preg_replace('/\{!\{([^\"]*)\}!\}/', '<img style="max-width: 100%;vertical-align: middle;" src="$1"/>', $text);

            // 定义被回复人的邮箱
            $parentMail = '';

            // 如果评论是回复别人，获取被回复人的邮箱
            if ($comment->parent != 0) {
                $db = Typecho_Db::get();
                // 修复：增加查询结果判断，避免空值
                $parentInfo = $db->fetchRow($db->select('mail')->from('table.comments')->where('coid = ?', $comment->parent));
                $parentMail = !empty($parentInfo) ? $parentInfo['mail'] : '';
            }

            /* 被回复的人不是自己时，发送邮件 */
            if ($parentMail != $comment->mail && !empty($parentMail)) {
                // 替换邮件正文中的占位符
                $mail->Body = strtr(
                    $html,
                    array(
                        "{title}" => '您在 [' . $comment->title . '] 的评论有了新的回复！',
                        "{subtitle}" => '博主：[ ' . $comment->author . ' ] <br>在《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》上回复了您:',
                        "{content}" => $text,
                    )
                );
                $mail->addAddress($parentMail);
                $mail->Subject = '您在 [' . $comment->title . '] 的评论有了新的回复！';
                $mail->send();
                $mail->clearAddresses(); // 修复：清除收件人，避免累积
            }

            /* 如果是游客发的评论（无作者ID） */
            if (empty($comment->authorId)) {
                /* 直接发表的评论（不是回复），发送邮件给博主 */
                if ($comment->parent == 0) {
                    $db = Typecho_Db::get();
                    // 修复：增加查询结果判断
                    $authoInfo = $db->fetchRow($db->select()->from('table.users')->where('uid = ?', $comment->ownerId));
                    $authorMail = !empty($authoInfo) ? $authoInfo['mail'] : '';

                    if (!empty($authorMail)) {
                        $mail->Body = strtr(
                            $html,
                            array(
                                "{title}" => '您的文章 [' . $comment->title . '] 收到一条新的评论！',
                                "{subtitle}" => $comment->author . ' [' . $comment->ip . '] <br>在您的《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》上发表评论:',
                                "{content}" => $text,
                            )
                        );
                        $mail->addAddress($authorMail);
                        $mail->Subject = '您的文章 [' . $comment->title . '] 收到一条新的评论！';
                        $mail->send();
                        $mail->clearAddresses(); // 修复：清除收件人
                    }
                } else {
                    /* 游客回复他人评论，发送邮件给被回复人 */
                    $db = Typecho_Db::get();
                    $parentInfo = $db->fetchRow($db->select('mail')->from('table.comments')->where('coid = ?', $comment->parent));
                    $parentMail = !empty($parentInfo) ? $parentInfo['mail'] : '';

                    if ($parentMail != $comment->mail && !empty($parentMail)) {
                        $mail->Body = strtr(
                            $html,
                            array(
                                "{title}" => '您在 [' . $comment->title . '] 的评论有了新的回复！',
                                "{subtitle}" => $comment->author . ' <br>在《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》上回复了您:',
                                "{content}" => $text,
                            )
                        );
                        $mail->addAddress($parentMail);
                        $mail->Subject = '您在 [' . $comment->title . '] 的评论有了新的回复！';
                        $mail->send();
                        $mail->clearAddresses(); // 修复：清除收件人
                    }
                }
            }
        } catch (Exception $e) {
            // 记录邮件发送失败原因（使用通知级日志）
            trigger_error("邮件发送失败: " . $e->getMessage(), E_USER_NOTICE);
        }
    }
}

/* 加强版文章编辑器 */
if (Helper::options()->JEditor !== 'off') {
  Typecho_Plugin::factory('admin/write-post.php')->richEditor  = array('Editor', 'Edit');
  Typecho_Plugin::factory('admin/write-page.php')->richEditor  = array('Editor', 'Edit');
}

class Editor
{
  public static function Edit()
  {
?>
    <link href="<?php echo get_theme_url('assets/css/buyu.APlayer.css?v=1.3.1'); ?>" rel="stylesheet" />
    <script src="<?php echo get_theme_url('assets/js/buyu.APlayer.js?v=1.3.1'); ?>"></script>
<?php
  }
}