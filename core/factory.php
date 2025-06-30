<?php
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
require_once("phpmailer.php");
require_once("smtp.php");

/* 评论拦截功能 */
Typecho_Plugin::factory('Widget_Feedback')->comment = array('Intercept', 'message');
class Intercept
{
    public static function message($comment)
    {
        error_log("Intercept::message function is called.");
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
            if (_checkSensitiveWords(Helper::options()->JSensitiveWords, $comment['text'])) {
                $action = Helper::options()->JSensitiveWordsAction;
                switch ($action) {
                    case 'none':
                        // 无动作，不做处理
                        break;
                    case 'waiting':
                        $comment['status'] = 'waiting';
                        break;
                    case 'fail':
                        throw new Typecho_Widget_Exception('评论包含敏感词汇，评论失败', 403);
                        break;
                }
            }
        }
        
        // 判断评论是否至少包含一个中文
        if (Helper::options()->JLimitOneChinese === "on") {
            if (preg_match("/[\x{4e00}-\x{9fa5}]/u", $comment['text']) == 0) {
                $comment['status'] = 'waiting';
            }
        }

        Typecho_Cookie::delete('__typecho_remember_text');
        return $comment;
    }
}

/* 评论邮件通知 */
if (
    Helper::options()->JCommentMail === 'on' &&
    Helper::options()->JCommentMailHost &&
    Helper::options()->JCommentMailPort &&
    Helper::options()->JCommentMailFromName &&
    Helper::options()->JCommentMailAccount &&
    Helper::options()->JCommentMailPassword &&
    Helper::options()->JCommentSMTPSecure
) {
    Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('Email', 'send');
}

class Email
{
    public static function send($comment)
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->CharSet = 'UTF-8';
        //$mail->SMTPDebug = 3; // 或 3，调试级别更高
        $mail->SMTPSecure = Helper::options()->JCommentSMTPSecure;
        $mail->Host = Helper::options()->JCommentMailHost;
        $mail->Port = Helper::options()->JCommentMailPort;
        $mail->FromName = Helper::options()->JCommentMailFromName;
        $mail->Username = Helper::options()->JCommentMailAccount;
        $mail->From = Helper::options()->JCommentMailAccount;
        $mail->Password = Helper::options()->JCommentMailPassword;
        $mail->isHTML(true);
        $text = $comment->text;
        $text = preg_replace('/\{!\{([^\"]*)\}!\}/', '<img style="max-width: 100%;vertical-align: middle;" src="$1"/>', $text);
        $html = ''; // 这里应该有具体的 HTML 内容

        /* 被回复的人不是自己时，发送邮件 */
        if ($parentMail != $comment->mail) {
            $mail->Body = strtr(
                $html,
                array(
                    "{title}" => '您在 [' . $comment->title . '] 的评论有了新的回复！',
                    "{subtitle}" => '博主：[ ' . $comment->author . ' ] 在《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》上回复了您:',
                    "{content}" => $text,
                )
            );
            $mail->addAddress($parentMail);
            $mail->Subject = '您在 [' . $comment->title . '] 的评论有了新的回复！';
            $mail->send();
        }
        /* 如果是游客发的评论 */
        if (!$comment->authorId) {
            /* 如果是直接发表的评论，不是回复别人，那么发送邮件给博主 */
            if ($comment->parent == 0) {
                $db = Typecho_Db::get();
                $authoInfo = $db->fetchRow($db->select()->from('table.users')->where('uid = ?', $comment->ownerId));
                $authorMail = $authoInfo['mail'];
                if ($authorMail) {
                    $mail->Body = strtr(
                        $html,
                        array(
                            "{title}" => '您的文章 [' . $comment->title . '] 收到一条新的评论！',
                            "{subtitle}" => $comment->author . ' [' . $comment->ip . '] 在您的《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》上发表评论:',
                            "{content}" => $text,
                        )
                    );
                    $mail->addAddress($authorMail);
                    $mail->Subject = '您的文章 [' . $comment->title . '] 收到一条新的评论！';
                    $mail->send();
                }
                /* 如果发表的评论是回复别人 */
            } else {
                $db = Typecho_Db::get();
                $parentInfo = $db->fetchRow($db->select('mail')->from('table.comments')->where('coid = ?', $comment->parent));
                $parentMail = $parentInfo['mail'];
                /* 被回复的人不是自己时，发送邮件 */
                if ($parentMail != $comment->mail) {
                    $mail->Body = strtr(
                        $html,
                        array(
                            "{title}" => '您在 [' . $comment->title . '] 的评论有了新的回复！',
                            "{subtitle}" => $comment->author . ' 在《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》上回复了您:',
                            "{content}" => $text,
                        )
                    );
                    $mail->addAddress($parentMail);
                    $mail->Subject = '您在 [' . $comment->title . '] 的评论有了新的回复！';
                    $mail->send();
                }
            }
        }
    }
}