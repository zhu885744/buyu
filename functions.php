<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
use Typecho\Widget\Helper\Form\Element\Radio;
use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Widget\Helper\Form\Element\Checkbox;
use Typecho\Widget\Helper\Form\Element\Textarea;
/* buyu主题核心文件 */
require_once("core/core.php");
/**
 * 主题后台设置
*/
function themeConfig($form)
{
$_db = Typecho_Db::get();
  $_prefix = $_db->getPrefix();
  try {
    // 检查 `views` 字段是否存在，如果不存在则添加
    if (!array_key_exists('views', $_db->fetchRow($_db->select()->from('table.contents')->page(1, 1)))) {
      $_db->query('ALTER TABLE `' . $_prefix . 'contents` ADD `views` INT DEFAULT 0;');
    }
    // 检查 `agree` 字段是否存在，如果不存在则添加
    if (!array_key_exists('agree', $_db->fetchRow($_db->select()->from('table.contents')->page(1, 1)))) {
      $_db->query('ALTER TABLE `' . $_prefix . 'contents` ADD `agree` INT DEFAULT 0;');
    }
  } catch (Exception $e) {
    Typecho_Log::write('主题配置错误: ' . $e->getMessage(), Typecho_Log::ERROR);
  }
?>

<link rel="stylesheet" href="<?php echo get_theme_url('assets/typecho/config/css/buyu.config.css'); ?>">
<script src="<?php echo get_theme_url('assets/typecho/config/js/buyu.config.js'); ?>"></script>
<div class="buyu_config">
  <div>
    <div class="buyu_config__aside">
      <div class="logo">buyu <?php echo _getVersion() ?></div>
      <ul class="tabs">
        <li class="item" data-current="buyu_global">全局设置</li>
        <li class="item" data-current="buyu_image">图片设置</li>
        <li class="item" data-current="buyu_post">文章设置</li>
        <li class="item" data-current="buyu_comments">评论设置</li>
      </ul>
      <?php require_once('core/backup.php'); ?>
    </div>
  </div>
<?php
  /* ---------------- 全局设置开始 ------------------ */
  $JAssetsURL = new Typecho_Widget_Helper_Form_Element_Text('JAssetsURL',NULL,NULL,'自定义静态资源CDN地址','介绍：自定义静态资源CDN地址，不填则走本地资源 <br />教程：<br />1. 将整个assets目录上传至你的CDN <br />2. 填写静态资源地址访问的前缀 <br />');
  $JAssetsURL->setAttribute('class', 'buyu_content buyu_global');
  $form->addInput($JAssetsURL);
  /* --------------------------------------- */
  $ICPbeian = new Typecho_Widget_Helper_Form_Element_Text('ICPbeian', NULL, NULL, _t('ICP备案号'), _t('在这里输入ICP备案号,留空则不显示'));
	$ICPbeian->setAttribute('class', 'buyu_content buyu_global');
  $form->addInput($ICPbeian);
  /* --------------------------------------- */
	$gonganbeian = new Typecho_Widget_Helper_Form_Element_Text('gonganbeian', NULL, NULL, _t('公安联网备案号'), _t('在这里输入公安联网备案号,留空则不显示'));
  $gonganbeian->setAttribute('class', 'buyu_content buyu_global');
	$form->addInput($gonganbeian);
  /* --------------------------------------- */
  $gravatarUrl = new Typecho_Widget_Helper_Form_Element_Text('gravatarUrl',NULL,'https://weavatar.com/avatar/', _t('自定义Gravatar头像源地址'),_t('请输入Gravatar头像源地址（末尾无需加斜杠）。<br>推荐镜像：<br>https://weavatar.com/avatar/'));
  $gravatarUrl->setAttribute('class', 'buyu_content buyu_global');
  $form->addInput($gravatarUrl);
  /* --------------------------------------- */
  $CustomCSS = new Typecho_Widget_Helper_Form_Element_Textarea('CustomCSS', NULL, NULL, _t('自定义css'), _t('在这里填入你的自定义css（直接填入css，无需&lt;style&gt;标签）'));
  $CustomCSS->setAttribute('class', 'buyu_content buyu_global');
	$form->addInput($CustomCSS);
  /* --------------------------------------- */
  $JCustomScript = new Typecho_Widget_Helper_Form_Element_Textarea('JCustomScript',NULL,NULL,'自定义JS','请填写自定义JS内容，例如网站统计等，填写时无需填写script标签。');
  $JCustomScript->setAttribute('class', 'buyu_content buyu_global');
  $form->addInput($JCustomScript);
  /* --------------------------------------- */
  $JFooter_Left = new Typecho_Widget_Helper_Form_Element_Textarea('JFooter_Left',NULL,'','自定义底部栏内容','介绍：用于增加底部栏内容<br>例如：&lt;a href="/"&gt;首页&lt;/a&gt; &lt;a href="/"&gt;关于&lt;/a&gt;');
  $JFooter_Left->setAttribute('class', 'buyu_content buyu_global');
  $form->addInput($JFooter_Left);
  /* --------------------------------------- */
  $CustomContent = new Typecho_Widget_Helper_Form_Element_Textarea('CustomContent', NULL, NULL, _t('底部自定义内容'), _t('位于底部，footer之后body之前，适合放置一些JS内容，如网站统计代码等（若开启全站Pjax，目前支持Google和百度统计的回调，其余统计代码可能会不准确）'));
  $CustomContent->setAttribute('class', 'buyu_content buyu_global');
	$form->addInput($CustomContent);
  /* ---------------- 全局设置结束 ------------------ */
  /* ---------------------------------------------- */
  /* ---------------- 图片设置开始 ----------------- */
  $favicon = new Typecho_Widget_Helper_Form_Element_Text('favicon', null, null, _t('站点 favicon 地址'), _t('在这里填入一个图片 URL 地址, 以在网站标题前加上一个 favicon 图标，支持Base64 地址'));
  $favicon->setAttribute('class', 'buyu_content buyu_image');
  $form->addInput($favicon);
  /* ---------------- 图片设置结束 ------------------ */
  /* ---------------------------------------------- */
  /* ---------------- 评论设置开始 ----------------- */
  $JCommentStatus = new Typecho_Widget_Helper_Form_Element_Select('JCommentStatus', array('on' => '开启（默认）', 'off' => '关闭'), '3', '开启或关闭全站评论', '介绍：用于一键开启关闭所有页面的评论 <br>注意：此处的权重优先级最高 <br>若关闭此项而文章内开启评论，评论依旧为关闭状态');
  $JCommentStatus->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JCommentStatus->multiMode());
  /* --------------------------------------- */
  $JSensitiveWordsAction = new Typecho_Widget_Helper_Form_Element_Select('JSensitiveWordsAction',array('none' => '无动作（默认）','waiting' => '标记为待审核','fail' => '评论失败'),'none','评论敏感词操作','介绍：选择当评论中包含敏感词汇时的操作');
  $JSensitiveWordsAction->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JSensitiveWordsAction->multiMode());
  /* --------------------------------------- */
  $JSensitiveWords = new Typecho_Widget_Helper_Form_Element_Textarea('JSensitiveWords',NULL,'傻逼 || 推广 || 群发 || 广告','评论敏感词（非必填）','介绍：用于设置评论敏感词汇，如果用户评论包含这些词汇，则将会把评论设置为待审核状态<br>示例：你妈死了 || 傻逼 || 推广 || 群发 || 广告<br>注意：多个词汇中间请用 || 符号间隔');
  $JSensitiveWords->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JSensitiveWords);
  /* --------------------------------------- */
  $JLimitOneChinese = new Typecho_Widget_Helper_Form_Element_Select('JLimitOneChinese',array('off' => '关闭（默认）', 'on' => '开启'),'off','是否开启评论至少包含一个中文','介绍：开启后如果评论内容未包含一个中文，则将会把评论设置为待审核状态 <br />其他：用于屏蔽国外机器人刷的全英文垃圾广告信息');
  $JLimitOneChinese->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JLimitOneChinese->multiMode());
  /* --------------------------------------- */
  $JTextLimit = new Typecho_Widget_Helper_Form_Element_Text('JTextLimit',NULL,NULL,'限制用户评论最大字数','介绍：如果用户评论的内容超出字数限制，则将会把发送评论按钮置为失败禁止点击状态 <br />其他：请输入数字格式，不填写则不限制');
  $JTextLimit->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JTextLimit->multiMode());
  /* --------------------------------------- */
  $JCommentMail = new Typecho_Widget_Helper_Form_Element_Select('JCommentMail',array('off' => '关闭（默认）', 'on' => '开启'),'off','是否开启评论邮件通知','介绍：开启后评论内容将会进行邮箱通知 <br />注意：此项需要您完整无错的填写下方的邮箱设置！！ <br />其他：下方例子以QQ邮箱为例，推荐使用QQ邮箱');
  $JCommentMail->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JCommentMail->multiMode());
  /* --------------------------------------- */
  $JCommentMailHost = new Typecho_Widget_Helper_Form_Element_Text('JCommentMailHost',NULL,NULL,'邮箱服务器地址','例如：smtp.qq.com');
  $JCommentMailHost->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JCommentMailHost->multiMode());
  /* --------------------------------------- */
  $JCommentSMTPSecure = new Typecho_Widget_Helper_Form_Element_Select('JCommentSMTPSecure',array('ssl' => 'ssl（默认）', 'tsl' => 'tsl'),'ssl','加密方式','介绍：用于选择登录鉴权加密方式');
  $JCommentSMTPSecure->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JCommentSMTPSecure->multiMode());
  /* --------------------------------------- */
  $JCommentMailPort = new Typecho_Widget_Helper_Form_Element_Text('JCommentMailPort',NULL,NULL,'邮箱服务器端口号','例如：465');
  $JCommentMailPort->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JCommentMailPort->multiMode());
  /* --------------------------------------- */
  $JCommentMailFromName = new Typecho_Widget_Helper_Form_Element_Text('JCommentMailFromName',NULL,NULL,'发件人昵称','例如：帅气的象拔蚌');
  $JCommentMailFromName->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JCommentMailFromName->multiMode());
  /* --------------------------------------- */
  $JCommentMailAccount = new Typecho_Widget_Helper_Form_Element_Text('JCommentMailAccount',NULL,NULL,'发件人邮箱','例如：2323333339@qq.com');
  $JCommentMailAccount->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JCommentMailAccount->multiMode());
  /* --------------------------------------- */
  $JCommentMailPassword = new Typecho_Widget_Helper_Form_Element_Text('JCommentMailPassword',NULL,NULL,'邮箱授权码','介绍：这里填写的是邮箱生成的授权码 <br>获取方式（以QQ邮箱为例）：<br>QQ邮箱 > 设置 > 账户 > IMAP/SMTP服务 > 开启 <br>其他：这个可以百度一下开启教程，有图文教程'
  );
  $JCommentMailPassword->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JCommentMailPassword->multiMode());
  /* ---------------- 评论设置结束 ----------------- */
  /* --------------------------------------------- */
  /* ---------------- 文章设置开始 ---------------- */
  $JEditor = new Typecho_Widget_Helper_Form_Element_Select('JEditor',array('on' => '开启（默认）','off' => '关闭',),'on','是否启用主题自带编辑器','介绍：开启后，文章编辑器将替换成主题自带编辑器 <br>其他：目前编辑器处于开发阶段，如果想继续使用原生编辑器，关闭此项即可');
  $JEditor->setAttribute('class', 'buyu_content buyu_post');
  $form->addInput($JEditor->multiMode());
  /* --------------------------------------- */
  $like = new Typecho_Widget_Helper_Form_Element_Select('like', array('off' => '关闭（默认）', 'on' => '开启'), '3', '文章点赞', '开启后将在文章底部显示点赞按钮，默认关闭');
  $like->setAttribute('class', 'buyu_content buyu_post');
  $form->addInput($like);
  /* --------------------------------------- */
  $tip = new Typecho_Widget_Helper_Form_Element_Select('tip', array('off' => '关闭（默认）', 'on' => '开启'), '3', '文章打赏', '开启后将在文章底部显示打赏按钮，默认关闭');
  $tip->setAttribute('class', 'buyu_content buyu_post');
  $form->addInput($tip);
  /* --------------------------------------- */
  $weixin = new Typecho_Widget_Helper_Form_Element_Text('weixin', NULL, NULL, _t('微信收款码链接'), _t('在这里输入微信收款码链接,留空则不显示'));
	$weixin->setAttribute('class', 'buyu_content buyu_post');
  $form->addInput($weixin);
  /* --------------------------------------- */
  $zfb = new Typecho_Widget_Helper_Form_Element_Text('zfb', NULL, NULL, _t('支付宝收款码链接'), _t('在这里输入支付宝收款码链接,留空则不显示'));
	$zfb->setAttribute('class', 'buyu_content buyu_post');
  $form->addInput($zfb);
  /* --------------------------------------- */
  $copyright = new Typecho_Widget_Helper_Form_Element_Select('copyright', array('off' => '关闭（默认）', 'on' => '开启'), '3', '文章底部版权', '开启后将在文章底部显示版权信息，默认关闭');
  $copyright->setAttribute('class', 'buyu_content buyu_post');
  $form->addInput($copyright);
  /* ---------------- 文章设置结束 ---------------- */
}