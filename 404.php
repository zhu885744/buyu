<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('public/header.php'); ?>
    <style>
      .col-mb-12.col-8#main {
            background-color: #fff;
            padding: 40px;
            order-radius: 10px;
            text-align: center;
       }

       .error-page h2 {
            font-size: 36px;
            color: #e74c3c;
            margin-bottom: 20px;
            animation: fadeIn 1s ease;
        }

       .error-page p {
            font-size: 18px;
            margin-bottom: 30px;
            animation: fadeIn 1s ease;
        }

       .error-page form#search {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

       .error-page form#search input[type="text"] {
            width: 100%;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

       .error-page form#search label {
            display: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>

    <div class="col-mb-12 col-8" id="main" role="main">
        <div class="error-page">
            <h2 class="post-title">404 - <?php _e('页面没找到'); ?></h2>
            <p><?php _e('你想查看的页面已被隐藏或删除了, 要不要搜索看看？ '); ?></p>
            <form id="search" method="post" action="<?php $this->options->siteUrl();?>" role="search">
                <label for="s" class="sr-only"><?php _e('搜索关键字');?></label>
                <input type="text" id="s" name="s" class="text" placeholder="<?php _e('输入关键字搜索');?>"/>
            </form>
        </div>
    </div>
<?php $this->need('public/footer.php'); ?>