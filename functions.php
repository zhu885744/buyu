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
    if (!array_key_exists('views', $_db->fetchRow($_db->select()->from('table.contents')->page(1, 1)))) {
      $_db->query('ALTER TABLE `' . $_prefix . 'contents` ADD `views` INT DEFAULT 0;');
    }
    if (!array_key_exists('agree', $_db->fetchRow($_db->select()->from('table.contents')->page(1, 1)))) {
      $_db->query('ALTER TABLE `' . $_prefix . 'contents` ADD `agree` INT DEFAULT 0;');
    }
  } catch (Exception $e) {
  }
?>

<div class="buyu_config">
  <div>
    <div class="buyu_config__aside">
      <h2 class="text">buyu <?php echo _getVersion() ?> 主题设置</h2>
      <?php require_once('core/backup.php'); ?>
    </div>
  </div>
<?php
  $JCommentStatus = new Typecho_Widget_Helper_Form_Element_Select('JCommentStatus', array('on' => '开启（默认）', 'off' => '关闭'), '3', '开启或关闭全站评论', '介绍：用于一键开启关闭所有页面的评论 <br>注意：此处的权重优先级最高 <br>若关闭此项而文章内开启评论，评论依旧为关闭状态');
  $form->addInput($JCommentStatus->multiMode());
  /* --------------------------------------- */
  $logoUrl = new \Typecho\Widget\Helper\Form\Element\Text('logoUrl',null,'/usr/themes/buyu/assets/img/logo.png', _t('站点导航栏 LOGO 地址'),_t('在这里填入一个图片 URL 地址, 以在导航栏加上一个 LOGO'));
  $form->addInput($logoUrl->addRule('url', _t('请填写一个合法的URL地址')));
  /* --------------------------------------- */
  $ICPbeian = new Typecho_Widget_Helper_Form_Element_Text('ICPbeian', NULL, NULL, _t('ICP备案号'), _t('在这里输入ICP备案号,留空则不显示'));
	$form->addInput($ICPbeian);
  /* --------------------------------------- */
	$gonganbeian = new Typecho_Widget_Helper_Form_Element_Text('gonganbeian', NULL, NULL, _t('公安联网备案号'), _t('在这里输入公安联网备案号,留空则不显示'));
	$form->addInput($gonganbeian);
  /* --------------------------------------- */
  $CustomCSS = new Typecho_Widget_Helper_Form_Element_Textarea('CustomCSS', NULL, NULL, _t('自定义css'), _t('在这里填入你的自定义css（直接填入css，无需&lt;style&gt;标签）'));
	$form->addInput($CustomCSS);
  /* --------------------------------------- */
  $JCustomScript = new Typecho_Widget_Helper_Form_Element_Textarea('JCustomScript',NULL,NULL,'自定义JS','请填写自定义JS内容，例如网站统计等，填写时无需填写script标签。');
  $form->addInput($JCustomScript);
  /* --------------------------------------- */
  $JFooter_Left = new Typecho_Widget_Helper_Form_Element_Textarea('JFooter_Left',NULL,'','自定义底部栏内容','介绍：用于增加底部栏内容<br>例如：&lt;a href="/"&gt;首页&lt;/a&gt; &lt;a href="/"&gt;关于&lt;/a&gt;');
  $form->addInput($JFooter_Left);
  /* --------------------------------------- */
  $CustomContent = new Typecho_Widget_Helper_Form_Element_Textarea('CustomContent', NULL, NULL, _t('底部自定义内容'), _t('位于底部，footer之后body之前，适合放置一些JS内容，如网站统计代码等（若开启全站Pjax，目前支持Google和百度统计的回调，其余统计代码可能会不准确）'));
	$form->addInput($CustomContent);
}

/*
function themeFields($layout)
{
    $logoUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'logoUrl',
        null,
        null,
        _t('站点LOGO地址'),
        _t('在这里填入一个图片URL地址, 以在导航栏加上一个LOGO')
    );
    $layout->addItem($logoUrl);
}
*/