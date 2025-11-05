<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Widget\Helper\Form\Element\Textarea;
use Typecho\Widget\Helper\Form\Element\Select;
use Typecho\Widget\Helper\Form\Element\Checkbox;
use Typecho\Widget\Helper\Form\Element\Radio;

/* buyu主题核心文件 */
require_once "core/core.php";

/**
 * 主题后台设置
 * @param Typecho_Widget_Helper_Form $form
 */
function themeConfig($form)
{
    // 数据库字段初始化
    initDatabaseFields();
?>

<link rel="stylesheet" href="<?php echo get_theme_url('assets/typecho/config/css/buyu.config.css?v=1.3.1'); ?>">
<script src="<?php echo get_theme_url('assets/typecho/config/js/buyu.config.js?v=1.3.1'); ?>"></script>
<div class="buyu_config">
  <div>
    <div class="buyu_config__aside">
      <div class="logo">buyu <?php echo _getVersion() ?></div>
      <ul class="tabs">
        <li class="item" data-current="buyu_global"><?php _e('全局设置'); ?></li>
        <li class="item" data-current="buyu_image"><?php _e('图片设置'); ?></li>
        <li class="item" data-current="buyu_post"><?php _e('文章设置'); ?></li>
        <li class="item" data-current="buyu_comments"><?php _e('评论设置'); ?></li>
      </ul>
      <?php require_once 'core/backup.php'; ?>
    </div>
  </div>
<?php
    // 定义表单字段配置
    $fieldGroups = [
        'buyu_global' => getGlobalFields(),
        'buyu_image' => getImageFields(),
        'buyu_comments' => getCommentFields(),
        'buyu_post' => getPostFields()
    ];

    // 批量添加表单字段
    foreach ($fieldGroups as $groupClass => $fields) {
        foreach ($fields as $fieldName => $config) {
            $element = createFormElement($fieldName, $config);
            $element->setAttribute('class', "buyu_content {$groupClass}");
            $form->addInput($element);
        }
    }
}

/**
 * 初始化数据库字段
 */
function initDatabaseFields()
{
    try {
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        $requiredFields = ['views', 'agree'];
        
        // 检查是否有内容表记录
        $contentExists = $db->fetchRow($db->select(['cid'])->from('table.contents')->limit(1));
        if (!$contentExists) {
            return; // 没有内容记录，无需添加字段
        }
        
        // 获取表结构信息
        $tableInfo = $db->query("DESCRIBE `{$prefix}contents`", Typecho_Db::READ);
        $existingFields = [];
        
        while ($row = $db->fetchRow($tableInfo)) {
            $existingFields[] = $row['Field'];
        }
        
        // 添加缺失的字段
        foreach ($requiredFields as $field) {
            if (!in_array($field, $existingFields)) {
                $db->query("ALTER TABLE `{$prefix}contents` ADD `{$field}` INT DEFAULT 0;");
            }
        }
    } catch (Exception $e) {
        Typecho_Log::write('主题配置错误: ' . $e->getMessage(), Typecho_Log::ERROR);
    }
}

/**
 * 创建表单元素
 * @param string $fieldName 字段名
 * @param array $config 字段配置
 * @return Typecho_Widget_Helper_Form_Element
 */
function createFormElement($fieldName, array $config)
{
    $className = "Typecho\\Widget\\Helper\\Form\\Element\\{$config['type']}";
    
    // 根据元素类型处理参数
    if ($config['type'] === 'Select') {
        $element = new $className(
            $fieldName,
            $config['options'],
            $config['default'],
            $config['label'],
            $config['desc']
        );
    } else {
        $element = new $className(
            $fieldName,
            null,
            $config['default'],
            $config['label'],
            $config['desc']
        );
    }
    
    // 处理多值模式
    if (!empty($config['multiMode'])) {
        $element = $element->multiMode();
    }
    
    return $element;
}

/**
 * 获取全局设置字段
 * @return array
 */
function getGlobalFields()
{
    return [
        'JAssetsURL' => [
            'type' => 'Text',
            'label' => _t('自定义静态资源CDN地址'),
            'desc' => _t('介绍：自定义静态资源CDN地址，不填则走本地资源 <br />教程：<br />1. 将整个assets目录上传至你的CDN存储桶 <br />2. 填写静态资源地址访问的前缀 <br />3. 链接不要带有/assets'),
            'default' => null
        ],
        'ICPbeian' => [
            'type' => 'Text',
            'label' => _t('ICP备案号'),
            'desc' => _t('在这里输入ICP备案号,留空则不显示'),
            'default' => null
        ],
        'gonganbeian' => [
            'type' => 'Text',
            'label' => _t('公安联网备案号'),
            'desc' => _t('在这里输入公安联网备案号,留空则不显示'),
            'default' => null
        ],
        'gravatarUrl' => [
            'type' => 'Text',
            'label' => _t('自定义Gravatar头像源地址'),
            'desc' => _t('请输入Gravatar头像源地址（末尾无需加斜杠）。<br>推荐镜像：<br>https://weavatar.com/avatar/'),
            'default' => 'https://weavatar.com/avatar/'
        ],
        'CustomCSS' => [
            'type' => 'Textarea',
            'label' => _t('自定义css'),
            'desc' => _t('在这里填入你的自定义css（直接填入css，无需&lt;style&gt;标签）'),
            'default' => null
        ],
        'JCustomScript' => [
            'type' => 'Textarea',
            'label' => _t('自定义JS'),
            'desc' => _t('请填写自定义JS内容，例如网站统计等，填写时无需填写script标签。'),
            'default' => null
        ],
        'JFooter_Left' => [
            'type' => 'Textarea',
            'label' => _t('自定义底部栏内容'),
            'desc' => _t('介绍：用于增加底部栏内容<br>例如：&lt;a href="/"&gt;首页&lt;/a&gt; &lt;a href="/"&gt;关于&lt;/a&gt;'),
            'default' => ''
        ],
        'CustomContent' => [
            'type' => 'Textarea',
            'label' => _t('底部自定义内容'),
            'desc' => _t('位于底部，footer之后body之前，适合放置一些JS内容，如网站统计代码等（若开启全站Pjax，目前支持Google和百度统计的回调，其余统计代码可能会不准确）'),
            'default' => null
        ]
    ];
}

/**
 * 获取图片设置字段
 * @return array
 */
function getImageFields()
{
    return [
        // favicon图标地址配置
        'favicon' => [
            'type' => 'Text',
            'label' => _t('站点 favicon 地址'),
            'desc' => _t('在这里填入一个图片 URL 地址, 以在网站标题前加上一个 favicon 图标'),
            'default' => null
        ],
        // PC端背景图地址配置
        'pcBackgroundUrl' => [
            'type' => 'Text',
            'label' => _t('PC端背景图地址'),
            'desc' => _t('在这里填入PC端页面背景图的 URL 地址，留空则不显示'),
            'default' => null
        ],
        // WAP端背景图地址配置
        'wapBackgroundUrl' => [
            'type' => 'Text',
            'label' => _t('移动端背景图地址'),
            'desc' => _t('在这里填入移动端背景图的 URL 地址，留空则不显示'),
            'default' => null
        ]
    ];
}

/**
 * 获取文章设置字段
 * @return array
 */
function getPostFields()
{
    return [
        'stickyPosts' => [
            'type' => 'Text',
            'label' => _t('文章置顶ID'),
            'desc' => _t('设置需要置顶的文章ID，多个ID用竖线「 | 」分隔（例如：1|3|5）<br>置顶文章仅在首页第一页显示，按填写顺序排列'),
            'default' => ''
        ],
        'like' => [
            'type' => 'Select',
            'options' => [
                'off' => _t('关闭（默认）'),
                'on' => _t('开启')
            ],
            'label' => _t('文章点赞'),
            'desc' => _t('开启后将在文章底部显示点赞按钮，默认关闭'),
            'default' => 'off'
        ],
        'tip' => [
            'type' => 'Select',
            'options' => [
                'off' => _t('关闭（默认）'),
                'on' => _t('开启')
            ],
            'label' => _t('文章打赏'),
            'desc' => _t('开启后将在文章底部显示打赏按钮，默认关闭'),
            'default' => 'off'
        ],
        'weixin' => [
            'type' => 'Text',
            'label' => _t('微信收款码链接'),
            'desc' => _t('在这里输入微信收款码链接,留空则不显示'),
            'default' => null
        ],
        'zfb' => [
            'type' => 'Text',
            'label' => _t('支付宝收款码链接'),
            'desc' => _t('在这里输入支付宝收款码链接,留空则不显示'),
            'default' => null
        ],
        'copyright' => [
            'type' => 'Select',
            'options' => [
                'off' => _t('关闭（默认）'),
                'on' => _t('开启')
            ],
            'label' => _t('文章底部版权'),
            'desc' => _t('开启后将在文章底部显示版权信息，默认关闭'),
            'default' => 'off'
        ]
    ];
}

/**
 * 获取评论设置字段
 * @return array
 */
function getCommentFields()
{
    return [
        'JCommentStatus' => [
            'type' => 'Select',
            'options' => [
                'on' => _t('开启（默认）'), 
                'off' => _t('关闭')
            ],
            'label' => _t('开启或关闭全站评论'),
            'desc' => _t('介绍：用于一键开启关闭所有页面的评论 <br>注意：此处的权重优先级最高 <br>若关闭此项而文章内开启评论，评论依旧为关闭状态'),
            'default' => 'on',
            'multiMode' => true
        ],
        'JSensitiveWordsAction' => [
            'type' => 'Select',
            'options' => [
                'none' => _t('无动作（默认）'),
                'waiting' => _t('标记为待审核'),
                'fail' => _t('评论失败')
            ],
            'label' => _t('评论敏感词操作'),
            'desc' => _t('介绍：选择当评论中包含敏感词汇时的操作'),
            'default' => 'none',
            'multiMode' => true
        ],
        'JSensitiveWords' => [
            'type' => 'Textarea',
            'label' => _t('评论敏感词（非必填）'),
            'desc' => _t('介绍：用于设置评论敏感词汇，如果用户评论包含这些词汇，则将会把评论设置为待审核状态<br>示例：你妈死了 || 傻逼 || 推广 || 群发 || 广告<br>注意：多个词汇中间请用 || 符号间隔'),
            'default' => '傻逼 || 推广 || 群发 || 广告'
        ],
        'JLimitOneChinese' => [
            'type' => 'Select',
            'options' => [
                'off' => _t('关闭（默认）'), 
                'on' => _t('开启')
            ],
            'label' => _t('是否开启评论内容至少包含一个中文'),
            'desc' => _t('介绍：开启后如果评论内容未包含一个中文，则将会把评论设置为待审核状态 <br />其他：用于屏蔽国外机器人刷的全英文垃圾广告信息'),
            'default' => 'off',
            'multiMode' => true
        ],
        'JNicknameNeedChinese' => [
            'type' => 'Select',
            'options' => [
                'off' => _t('关闭（默认）'),
                'on' => _t('开启')
            ],
            'label' => _t('是否开启评论用户昵称至少包含一个中文'),
            'desc' => _t('介绍：开启后如果评论昵称未包含至少一个中文（汉字/中文标点），则评论自动进入待审核状态 <br />其他：用于过滤纯英文/数字/符号的垃圾评论昵称，减少无效评论'),
            'default' => 'off',  
            'multiMode' => true
        ],
        'JTextLimit' => [
            'type' => 'Text',
            'label' => _t('限制用户评论最大字数'),
            'desc' => _t('介绍：如果用户评论的内容超出字数限制，则将会把发送评论按钮置为失败禁止点击状态 <br />其他：请输入数字格式，不填写则不限制'),
            'default' => null,
            'multiMode' => true
        ],
        'JCommentMail' => [
            'type' => 'Select',
            'options' => [
                'off' => _t('关闭（默认）'), 
                'on' => _t('开启')
            ],
            'label' => _t('是否开启评论邮件通知'),
            'desc' => _t('介绍：开启后评论内容将会进行邮箱通知 <br />注意：此项需要您完整无错的填写下方的邮箱设置！！ <br />其他：下方例子以QQ邮箱为例，推荐使用QQ邮箱'),
            'default' => 'off',
            'multiMode' => true
        ],
        'JCommentMailHost' => [
            'type' => 'Text',
            'label' => _t('邮箱服务器地址'),
            'desc' => _t('例如：smtp.qq.com'),
            'default' => null,
            'multiMode' => true
        ],
        'JCommentSMTPSecure' => [
            'type' => 'Select',
            'options' => [
                'ssl' => _t('ssl（默认）'), 
                'tsl' => _t('tsl')
            ],
            'label' => _t('加密方式'),
            'desc' => _t('介绍：用于选择登录鉴权加密方式'),
            'default' => 'ssl',
            'multiMode' => true
        ],
        'JCommentMailPort' => [
            'type' => 'Text',
            'label' => _t('邮箱服务器端口号'),
            'desc' => _t('例如：465'),
            'default' => null,
            'multiMode' => true
        ],
        'JCommentMailFromName' => [
            'type' => 'Text',
            'label' => _t('发件人昵称'),
            'desc' => _t('例如：帅气的象拔蚌'),
            'default' => null,
            'multiMode' => true
        ],
        'JCommentMailAccount' => [
            'type' => 'Text',
            'label' => _t('发件人邮箱'),
            'desc' => _t('例如：2323333339@qq.com'),
            'default' => null,
            'multiMode' => true
        ],
        'JCommentMailPassword' => [
            'type' => 'Text',
            'label' => _t('邮箱授权码'),
            'desc' => _t('介绍：这里填写的是邮箱生成的授权码 <br>获取方式（以QQ邮箱为例）：<br>QQ邮箱 > 设置 > 账户 > IMAP/SMTP服务 > 开启 <br>其他：这个可以百度一下开启教程，有图文教程'),
            'default' => null,
            'multiMode' => true
        ]
    ];
}