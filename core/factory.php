<?php
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
require_once("phpmailer.php");
require_once("smtp.php");

/* 加强评论拦截功能 */
Typecho_Plugin::factory('Widget_Feedback')->comment = array('Intercept', 'message');
class Intercept
{
    public static function message($comment)
    {
        error_log("Intercept::message function is called.");
        // 判断用户评论内容是否超过了最大字数限制
        if (Helper::options()->JTextLimit && mb_strlen($comment['text'], 'UTF-8') > Helper::options()->JTextLimit) {
            throw new Typecho_Widget_Exception('评论内容超过了最大字数限制，请缩短评论字数后再提交', 403);
        } else {
            // 判断评论内容是否包含敏感词
            if (Helper::options()->JSensitiveWords) {
                if (_checkSensitiveWords(Helper::options()->JSensitiveWords, $comment['text'])) {
                    $comment['status'] = 'waiting';
                }
            }
            // 判断评论是否至少包含一个中文
            if (Helper::options()->JLimitOneChinese === "on") {
                if (preg_match("/[\x{4e00}-\x{9fa5}]/u", $comment['text']) == 0) {
                    $comment['status'] = 'waiting';
                }
            }
        }
        Typecho_Cookie::delete('__typecho_remember_text');
        return $comment;
    }
}

/* 邮件通知 */
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
    $html = '
            <style>.buyu{width:550px;margin:0 auto;border-radius:8px;overflow:hidden;font-family:"Helvetica Neue",Helvetica,"PingFang SC","Hiragino Sans GB","Microsoft YaHei","微软雅黑",Arial,sans-serif;box-shadow:0 2px 12px 0 rgba(0,0,0,0.1);word-break:break-all}.buyu_title{color:#fff;background:linear-gradient(-45deg,rgba(9,69,138,0.2),rgba(68,155,255,0.7),rgba(117,113,251,0.7),rgba(68,155,255,0.7),rgba(9,69,138,0.2));background-size:400% 400%;background-position:50% 100%;padding:15px;font-size:15px;line-height:1.5}</style>
            <div class="buyu"><div class="buyu_title">{title}</div><div style="background: #fff;padding: 20px;font-size: 13px;color: #666;"><div style="margin-bottom: 20px;line-height: 1.5;">{subtitle}</div><div style="padding: 15px;margin-bottom: 20px;line-height: 1.5;background: repeating-linear-gradient(145deg, #f2f6fc, #f2f6fc 15px, #fff 0, #fff 25px);">{content}</div><div style="line-height: 2">请注意：此邮件由系统自动发送，请勿直接回复。<br>若此邮件不是您请求的，请忽略并删除！</div></div></div>
        ';
    /* 如果是博主发的评论 */
    if ($comment->authorId == $comment->ownerId) {
      /* 发表的评论是回复别人 */
      if ($comment->parent != 0) {
        $db = Typecho_Db::get();
        $parentInfo = $db->fetchRow($db->select('mail')->from('table.comments')->where('coid = ?', $comment->parent));
        $parentMail = $parentInfo['mail'];
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
      }
      /* 如果是游客发的评论 */
    } else {
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