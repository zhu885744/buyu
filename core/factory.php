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
 */
Typecho_Plugin::factory('Widget_Feedback')->comment = array('Intercept', 'message');
class Intercept
{
    public static function message($comment)
    {
        // 判断用户评论内容是否超过了最大字数限制
        if (Helper::options()->JTextLimit) {
            // 准确计算包含emoji和颜文字的评论内容的长度
            $contentLength = mb_strlen(preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}]/u', 'x', $comment['text']), 'UTF-8');
            if ($contentLength > Helper::options()->JTextLimit) {
                throw new Typecho_Widget_Exception('评论内容超过了最大字数限制', 403);
            }
        }

        // 判断评论内容是否包含敏感词
        if (Helper::options()->JSensitiveWords) {
            if (self::_checkSensitiveWords(Helper::options()->JSensitiveWords, $comment['text'])) {
                $action = Helper::options()->JSensitiveWordsAction ?? 'none';
                switch ($action) {
                    case 'waiting':
                        $comment['status'] = 'waiting';
                        break;
                    case 'fail':
                        throw new Typecho_Widget_Exception('评论包含敏感词汇，评论失败', 403);
                        break;
                }
            }
        }
        
        // 判断评论用户昵称是否至少包含一个中文
        if (Helper::options()->JNicknameNeedChinese === "on") {
            // 优先从数组获取昵称
            $nickname = !empty($comment['author']) ? $comment['author'] : '';
            // 去除首尾空格，避免空格干扰
            $nickname = trim($nickname);
            
            // 使用Unicode中文属性匹配（支持所有中文、繁体、生僻字）
            $hasChinese = preg_match("/[\p{Han}]/u", $nickname);
            
            // 调试日志（需要时开启）
            // trigger_error("昵称检测: [{$nickname}] " . ($hasChinese ? '含中文' : '不含中文'), E_USER_NOTICE);
            
            if (empty($nickname) || $hasChinese == 0) {
                $comment['status'] = 'waiting';
            }
        }

        // 判断评论内容是否至少包含一个中文
        if (Helper::options()->JLimitOneChinese === "on") {
            $content = trim($comment['text']); // 去除首尾空格
            // 使用Unicode中文属性匹配所有中文
            $hasChinese = preg_match("/[\p{Han}]/u", $content);
            
            // 调试日志（需要时开启）
            // trigger_error("内容检测: [{$content}] " . ($hasChinese ? '含中文' : '不含中文'), E_USER_NOTICE);
            
            if ($hasChinese == 0) {
                $comment['status'] = 'waiting';
            }
        }

        // 删除记录评论内容的 Cookie
        Typecho_Cookie::delete('__typecho_remember_text');
        return $comment;
    }

    /**
    * 敏感词检查函数
    */
    private static function _checkSensitiveWords($sensitiveWords, $content)
    {
        if (empty($sensitiveWords)) {
            return false;
        }
        
        // 支持多种分隔符，统一转为逗号后分割
        $sensitiveWords = str_replace('||', ',', $sensitiveWords);
        $words = array_map('trim', explode(',', $sensitiveWords));
        $words = array_filter($words);
        
        // 预处理评论内容
        $content = mb_strtolower($content);
        $content = self::convertFullWidthToHalfWidth($content);
        
        foreach ($words as $word) {
            if (empty($word)) continue;
            
            $word = mb_strtolower($word);
            $word = self::convertFullWidthToHalfWidth($word);
            
            if (preg_match('/' . preg_quote($word, '/') . '/ui', $content)) {
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
 * 根据评论的不同情况（待审核、已通过、回复他人、游客评论等），
 * 向相应的用户发送邮件通知，待审核评论仅通知站长
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
            // 获取评论审核状态（Typecho默认字段：waiting-待审核，approved-已通过）
            $commentStatus = $comment->status;
            // 创建PHPMailer实例
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 0;
            $mail->SMTPSecure = Helper::options()->JCommentSMTPSecure;
            $mail->Host = Helper::options()->JCommentMailHost;
            $mail->Port = (int)Helper::options()->JCommentMailPort;
            $mail->FromName = Helper::options()->JCommentMailFromName;
            $mail->Username = Helper::options()->JCommentMailAccount;
            $mail->From = Helper::options()->JCommentMailAccount;
            $mail->Password = Helper::options()->JCommentMailPassword;
            $mail->isHTML(true);

            // 邮件HTML模板
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
            <meta charset="UTF-8">
            <title>{title}</title>
            <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { line-height: 1.7; color: #444; background-color: #f8f9fa; padding: 20px 0; }
            .container { max-width: 720px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; }
            .mail-header { background: #165DFF; padding: 24px 30px; color: #fff; }
            .brand { display: flex; align-items: center; gap: 12px; }
            .brand-name { font-size: 18px; font-weight: 600; }
            .mail-content { padding: 30px; }
            .mail-title { font-size: 22px; color: #222; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0; }
            .subtitle { color: #666; margin-bottom: 24px; font-size: 15px; }
            .comment-card { background: #f9fafb; border-radius: 8px; padding: 20px; margin: 20px 0 30px; border-left: 4px solid #165DFF; font-size: 15px; }
            .comment-content { line-height: 1.8; color: #333; }
            .action-btn { display: inline-block; background: #165DFF; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 500; margin: 10px 0 25px; transition: background 0.3s; }
            .action-btn:hover { background: #0E42D2; }
            .mail-footer { padding: 20px 30px; background: #f9fafb; border-top: 1px solid #f0f0f0; font-size: 14px; color: #888; }
            .footer-note { margin-bottom: 12px; }
            .unsubscribe { color: #165DFF; text-decoration: none; }
            .unsubscribe:hover { text-decoration: underline; }
            @media (max-width: 600px) {
                .container { width: 95%; margin: 0 auto; }
                .mail-header, .mail-content, .mail-footer { padding: 20px 15px; }
                .mail-title { font-size: 18px; }
                .action-btn { width: 100%; text-align: center; }
            }
            </style>
            </head>
            <body>
            <div class="container">
            <div class="mail-header">
                <div class="brand"><div class="brand-name">{title}</div></div>
            </div>
            <div class="mail-content">
                <p class="subtitle">{subtitle}</p>
                <div class="comment-card"><div class="comment-content">{content}</div></div>
                <a href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank" class="action-btn">查看评论详情</a>
            </div>
            <div class="mail-footer">
                <p class="footer-note">这是自动发送的通知邮件，如有疑问可通过站点内的联系方式找到我</p>
            </div>
            </div>
            </body>
            </html>';

            // 处理评论内容中的图片标签
            $text = $comment->text;
            $text = preg_replace('/\{!\{([^\"]*)\}!\}/', '<img style="max-width: 100%;vertical-align: middle;" src="$1"/>', $text);

            // 待审核评论处理（仅通知站长）
            if ($commentStatus === 'waiting') {
                $db = Typecho_Db::get();
                $authorInfo = $db->fetchRow($db->select('mail')->from('table.users')->where('uid = ?', $comment->ownerId));
                $adminMail = !empty($authorInfo) ? $authorInfo['mail'] : '';
                
                if (!empty($adminMail)) {
                    // 区分待审核的是新评论还是回复
                    if ($comment->parent == 0) {
                        $subject = '您的文章 [' . $comment->title . '] 收到一条待审核评论';
                        $subtitle = '用户：[ ' . $comment->author . ' ]（IP：' . $comment->ip . '）<br>在《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》发表了新评论，待您审核：';
                    } else {
                        $parentInfo = $db->fetchRow($db->select('author')->from('table.comments')->where('coid = ?', $comment->parent));
                        $parentAuthor = !empty($parentInfo['author']) ? $parentInfo['author'] : '未知用户';
                        $subject = '您的文章 [' . $comment->title . '] 收到一条待审核回复';
                        $subtitle = '用户：[ ' . $comment->author . ' ]（IP：' . $comment->ip . '）<br>在《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》中回复了 [' . $parentAuthor . '] 的评论，待您审核：';
                    }

                    $mail->Body = strtr($html, [
                        "{title}" => $subject,
                        "{subtitle}" => $subtitle,
                        "{content}" => $text
                    ]);
                    $mail->addAddress($adminMail);
                    $mail->Subject = $subject;
                    $mail->send();
                    $mail->clearAddresses();
                }
                return; // 待审核评论不执行后续逻辑
            }

            // 已通过审核的评论处理
            if ($commentStatus === 'approved') {
                $parentMail = '';
                $parentAuthorId = '';
                $parentAuthorName = '';

                // 获取被回复人信息
                if ($comment->parent != 0) {
                    $db = Typecho_Db::get();
                    $parentInfo = $db->fetchRow($db->select('mail', 'authorId', 'author')->from('table.comments')->where('coid = ?', $comment->parent));
                    $parentMail = !empty($parentInfo) ? $parentInfo['mail'] : '';
                    $parentAuthorId = !empty($parentInfo) ? $parentInfo['authorId'] : '';
                    $parentAuthorName = !empty($parentInfo['author']) ? $parentInfo['author'] : '未知用户';
                }

                // 向被回复人发送通知
                if ($parentMail != $comment->mail && !empty($parentMail)) {
                    $mail->Body = strtr($html, [
                        "{title}" => '您在 [' . $comment->title . '] 的评论有了新的回复',
                        "{subtitle}" => '用户：[ ' . $comment->author . ' ] <br>在《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》上回复了您:',
                        "{content}" => $text
                    ]);
                    $mail->addAddress($parentMail);
                    $mail->Subject = '您在 [' . $comment->title . '] 的评论有了新的回复';
                    $mail->send();
                    $mail->clearAddresses();
                }

                // 向站长发送通知
                $db = Typecho_Db::get();
                $authorInfo = $db->fetchRow($db->select('mail')->from('table.users')->where('uid = ?', $comment->ownerId));
                $adminMail = !empty($authorInfo) ? $authorInfo['mail'] : '';

                if (!empty($adminMail)) {
                    // 游客直接评论
                    if (empty($comment->authorId) && $comment->parent == 0) {
                        $mail->Body = strtr($html, [
                            "{title}" => '您的文章 [' . $comment->title . '] 收到新评论',
                            "{subtitle}" => $comment->author . ' [' . $comment->ip . '] <br>在您的《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》上发表评论:',
                            "{content}" => $text
                        ]);
                        $mail->addAddress($adminMail);
                        $mail->Subject = '您的文章 [' . $comment->title . '] 收到新评论';
                        $mail->send();
                        $mail->clearAddresses();
                    }
                    // 游客间回复
                    else if (empty($comment->authorId) && $comment->parent != 0 && empty($parentAuthorId)) {
                        $mail->Body = strtr($html, [
                            "{title}" => '您的文章 [' . $comment->title . '] 有游客间新回复',
                            "{subtitle}" => '游客 [' . $comment->author . '] 回复了游客 [' . $parentAuthorName . '] 的评论：',
                            "{content}" => $text
                        ]);
                        $mail->addAddress($adminMail);
                        $mail->Subject = '您的文章 [' . $comment->title . '] 有游客间新回复';
                        $mail->send();
                        $mail->clearAddresses();
                    }
                }
            }

        } catch (Exception $e) {
            trigger_error("邮件发送失败: " . $e->getMessage(), E_USER_NOTICE);
        }
    }
}