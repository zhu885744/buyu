<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
use Typecho\Widget\Helper\Form\Element\Radio;
use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Widget\Helper\Form\Element\Checkbox;
use Typecho\Widget\Helper\Form\Element\Textarea;
/* buyu主题核心文件 */
require_once("core/core.php");

// 自定义函数，用于获取 CSS 文件的 URL
function get_css_url($file_name) {
    $options = Typecho_Widget::widget('Widget_Options');
    return $options->themeUrl('assets/typecho/config/css/' . $file_name);
}

// 自定义函数，用于获取 JS 文件的 URL
function get_js_url($file_name) {
    $options = Typecho_Widget::widget('Widget_Options');
    return $options->themeUrl('assets/typecho/config/js/' . $file_name);
}

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

<link rel="stylesheet" href="<?php echo get_css_url('buyu.config.css'); ?>">
<script src="<?php echo get_js_url('buyu.config.js'); ?>"></script>
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
  $JCommentStatus = new Typecho_Widget_Helper_Form_Element_Select('JCommentStatus', array('on' => '开启（默认）', 'off' => '关闭'), '3', '开启或关闭全站评论', '介绍：用于一键开启关闭所有页面的评论 <br>注意：此处的权重优先级最高 <br>若关闭此项而文章内开启评论，评论依旧为关闭状态');
  $JCommentStatus->setAttribute('class', 'buyu_content buyu_comments');
  $form->addInput($JCommentStatus->multiMode());
  /* --------------------------------------- */
  $favicon = new Typecho_Widget_Helper_Form_Element_Text('favicon', null, null, _t('站点 favicon 地址'), _t('在这里填入一个图片 URL 地址, 以在网站标题前加上一个 favicon 图标，支持Base64 地址'));
  $favicon->setAttribute('class', 'buyu_content buyu_image');
  $form->addInput($favicon);
  /* --------------------------------------- */
  $logoUrl = new \Typecho\Widget\Helper\Form\Element\Text('logoUrl',null,'/usr/themes/buyu/assets/img/logo.png', _t('站点导航栏 LOGO 地址'),_t('在这里填入一个图片 URL 地址, 以在导航栏加上一个 LOGO，支持Base64 地址'));
  $logoUrl->setAttribute('class', 'buyu_content buyu_image');
  $form->addInput($logoUrl);
  /* --------------------------------------- */
  $ICPbeian = new Typecho_Widget_Helper_Form_Element_Text('ICPbeian', NULL, NULL, _t('ICP备案号'), _t('在这里输入ICP备案号,留空则不显示'));
	$ICPbeian->setAttribute('class', 'buyu_content buyu_global');
  $form->addInput($ICPbeian);
  /* --------------------------------------- */
	$gonganbeian = new Typecho_Widget_Helper_Form_Element_Text('gonganbeian', NULL, NULL, _t('公安联网备案号'), _t('在这里输入公安联网备案号,留空则不显示'));
  $gonganbeian->setAttribute('class', 'buyu_content buyu_global');
	$form->addInput($gonganbeian);
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