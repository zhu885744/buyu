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
        // 记录日志，表明 Intercept::message 函数被调用
        error_log("Intercept::message function is called.");

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
            // 调用 _checkSensitiveWords 函数检查评论内容是否包含敏感词
            if (_checkSensitiveWords(Helper::options()->JSensitiveWords, $comment['text'])) {
                // 获取敏感词处理动作配置
                $action = Helper::options()->JSensitiveWordsAction;
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
}

/**
* 发送评论邮件通知
*
* 根据评论的不同情况（回复他人、游客直接评论、游客回复他人评论等），
* 向相应的用户发送邮件通知。
*
* @param object $comment 包含评论信息的对象，包含评论内容、作者、标题等信息
*/
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
        // 创建一个新的 PHPMailer 实例
        $mail = new PHPMailer();
        // 设置邮件发送方式为 SMTP
        $mail->isSMTP();
        // 开启 SMTP 认证
        $mail->SMTPAuth = true;
        // 设置邮件字符编码为 UTF-8
        $mail->CharSet = 'UTF-8';
        // 注释掉的调试级别设置，可按需开启
        //$mail->SMTPDebug = 3; // 或 3，调试级别更高
        // 设置 SMTP 安全协议
        $mail->SMTPSecure = Helper::options()->JCommentSMTPSecure;
        // 设置 SMTP 服务器地址
        $mail->Host = Helper::options()->JCommentMailHost;
        // 设置 SMTP 服务器端口
        $mail->Port = Helper::options()->JCommentMailPort;
        // 设置发件人姓名
        $mail->FromName = Helper::options()->JCommentMailFromName;
        // 设置 SMTP 用户名
        $mail->Username = Helper::options()->JCommentMailAccount;
        // 设置发件人邮箱
        $mail->From = Helper::options()->JCommentMailAccount;
        // 设置 SMTP 密码
        $mail->Password = Helper::options()->JCommentMailPassword;
        // 设置邮件内容为 HTML 格式
        $mail->isHTML(true);
        // 获取评论内容
        $text = $comment->text;
        // 将评论中的特定标记替换为图片标签
        $text = preg_replace('/\{!\{([^\"]*)\}!\}/', '<img style="max-width: 100%;vertical-align: middle;" src="$1"/>', $text);
        // 初始化邮件 HTML 内容，此处应补充具体内容
        $html = ''; // 这里应该有具体的 HTML 内容

        // 定义被回复人的邮箱，此处变量未定义，后续应先获取该值
        $parentMail = '';

        /* 被回复的人不是自己时，发送邮件 */
        if ($parentMail != $comment->mail) {
            // 替换邮件正文中的占位符
            $mail->Body = strtr(
                $html,
                array(
                    "{title}" => '您在 [' . $comment->title . '] 的评论有了新的回复！',
                    "{subtitle}" => '博主：[ ' . $comment->author . ' ] 在《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》上回复了您:',
                    "{content}" => $text,
                )
            );
            // 添加收件人邮箱
            $mail->addAddress($parentMail);
            // 设置邮件主题
            $mail->Subject = '您在 [' . $comment->title . '] 的评论有了新的回复！';
            // 发送邮件
            $mail->send();
        }

        /* 如果是游客发的评论 */
        if (!$comment->authorId) {
            /* 如果是直接发表的评论，不是回复别人，那么发送邮件给博主 */
            if ($comment->parent == 0) {
                // 获取数据库实例
                $db = Typecho_Db::get();
                // 查询文章作者信息
                $authoInfo = $db->fetchRow($db->select()->from('table.users')->where('uid = ?', $comment->ownerId));
                // 获取文章作者邮箱
                $authorMail = $authoInfo['mail'];
                // 若作者邮箱存在
                if ($authorMail) {
                    // 替换邮件正文中的占位符
                    $mail->Body = strtr(
                        $html,
                        array(
                            "{title}" => '您的文章 [' . $comment->title . '] 收到一条新的评论！',
                            "{subtitle}" => $comment->author . ' [' . $comment->ip . '] 在您的《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》上发表评论:',
                            "{content}" => $text,
                        )
                    );
                    // 添加收件人邮箱
                    $mail->addAddress($authorMail);
                    // 设置邮件主题
                    $mail->Subject = '您的文章 [' . $comment->title . '] 收到一条新的评论！';
                    // 发送邮件
                    $mail->send();
                }
                /* 如果发表的评论是回复别人 */
            } else {
                // 获取数据库实例
                $db = Typecho_Db::get();
                // 查询被回复评论的作者邮箱
                $parentInfo = $db->fetchRow($db->select('mail')->from('table.comments')->where('coid = ?', $comment->parent));
                // 获取被回复人的邮箱
                $parentMail = $parentInfo['mail'];
                /* 被回复的人不是自己时，发送邮件 */
                if ($parentMail != $comment->mail) {
                    // 替换邮件正文中的占位符
                    $mail->Body = strtr(
                        $html,
                        array(
                            "{title}" => '您在 [' . $comment->title . '] 的评论有了新的回复！',
                            "{subtitle}" => $comment->author . ' 在《 <a style="color: #12addb;text-decoration: none;" href="' . substr($comment->permalink, 0, strrpos($comment->permalink, "#")) . '" target="_blank">' . $comment->title . '</a> 》上回复了您:',
                            "{content}" => $text,
                        )
                    );
                    // 添加收件人邮箱
                    $mail->addAddress($parentMail);
                    // 设置邮件主题
                    $mail->Subject = '您在 [' . $comment->title . '] 的评论有了新的回复！';
                    // 发送邮件
                    $mail->send();
                }
            }
        }
    }
}