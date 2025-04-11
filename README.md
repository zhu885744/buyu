## Typecho **buyu** 单栏主题

这是一款基于 typecho 默认模版二次开发的 Typecho 单栏主题。<br>
用户交流群：[点击加入](https://qm.qq.com/q/PVln74J0UU)<br>
主题宗旨：简洁、简洁、还是简洁！

## 如何使用

下载 "主题压缩文件" 将压缩包上传至`/usr/themes/`目录，然后解压。<br>
然后进入Typecho后台，选择控制台>外观>启用

## 目录结构

```
css 主题css文件夹
  ├── buyu.grid.css 主题响应式布局
  ├── buyu.Lightbox.css 图片灯箱
  ├── buyu.style.css 主题核心css
  ├── buyu.OwO.min.css 评论表情
  └────────────────
js 主题js文件夹
  ├── buyu.Lightbox.js 图片灯箱
  ├── buyu.style.js 主题核心js
  ├── OwO.min.js 评论表情
  ├── OwO.min.js.map 评论表情
  ├───────────────
public 主题公共文件夹
  ├── comments.php 主题评论
  ├── footer.php 主页页脚
  ├── header.php 主题顶部导航栏
  ├────────────────
  ├── 404.php 404页面
  ├── archive.php 搜索和分类结果页面
  ├── archives.php 归档页面
  ├── friends.php 友情链接页面
  ├── functions.php 主题核心文件
  ├── index.php 首页页面
  ├── OwO.json 主题评论表情配置文件
  ├── page.php 独立页面
  ├── post.php 文章页面
  ├── screenshot.png 主题略缩图片
```

所有源代码应该放在 `/usr/themes/buyu` 目录下。

## 更新日志
[1.2.4] 更新内容如下
- 新增：视频/音频/折叠面板短代码
- 新增：主题设置备份
- 优化：首页文章列表样式
- 优化：全新的归档页面样式
- 优化：全新的 404 页面样式
- 优化：文章折叠面板的动画体验
- 优化：主题css颜色使用公共样式
- 优化：主题页脚样式
- 修复：404页面相关css代码造成的布局错乱bug
- 修复：修改了设置粗体文字后显示是紫色的问题