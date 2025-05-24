<?php
/**
 * 这是一款基于 typecho 默认模版二次开发的 Typecho 单栏主题
 *
 * @package buyu
 * @author 不语
 * @version 1.2.7
 * @link https://zhuxu.asia/archives/118/
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('public/header.php');
?>
<?php $this->need('public/article.php'); ?>
<?php $this->need('public/footer.php'); ?>