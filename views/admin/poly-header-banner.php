<div class="poly-admin-header-banner">
<?php
    $polyBanner = POLYHelper::getBanner('header');
    if (!empty($polyBanner)) {
        echo $polyBanner['content'];
    } else {
?>
        <style>
            .poly-default-banner-box {
                width: 100%;
                background-color: #6001d2;
                height: 100px;
                margin-bottom: 20px;
                color: white;
            }
            .poly-default-banner-box--bg-image {
                background-size: contain;
                background-position: center;
                background-repeat: no-repeat;
            }
            .poly-default-banner-box--logo-block {
                padding:4px 0px 0px 20px;
                float: left;
            }
            .poly-default-banner-box--logo {
                background-image: url('<?php echo POLY_IMAGES_URL.'/admin/banner/logo.png'; ?>');
                width: 100px;
                height: 100px;
            }
            .poly-default-banner-box--logo-title {
                text-align: center;
                margin-top: -5px;
            }
            .poly-default-banner-box--title-block {
                padding-top: 20px;
            }
            .poly-default-banner-box--title-block-icon {
                background-image: url('<?php echo POLY_IMAGES_URL.'/admin/banner/polyathlon.png'; ?>');
                width: 220px;
                height: 70px;
                margin: 0 auto;
            }
            .poly-default-banner-box--menu-block {
                float: right;
            }
            .poly-default-banner-box--menu-block-help {
                background-image: url('<?php echo POLY_IMAGES_URL.'/admin/banner/support.png'; ?>');
                margin-top: -60px;
                width: 45px;
                height: 45px;
                margin-right: 20px;
                display: block;
            }
            .poly-default-banner-box--menu-block-help:hover {
                opacity: 0.8;
            }
            .poly-default-banner-box--menu-block-help:active,
            .poly-default-banner-box--menu-block-help:focus {
                -webkit-box-shadow: none;
                -moz-box-shadow: none;
                box-shadow: none;
            }
        </style>
        <div class="poly-default-banner-box">
            <div class="poly-default-banner-box--logo-block">
                <div class="poly-default-banner-box--logo poly-default-banner-box--bg-image"></div>
                <!-- <div class="poly-default-banner-box--logo-title">FREE</div> -->
            </div>
            <div class="poly-default-banner-box--title-block">
                <div class="poly-default-banner-box--title-block-icon poly-default-banner-box--bg-image"></div>
            </div>
            <div class="poly-default-banner-box--menu-block">
                <a href="https://wordpress.org/support/plugin/schedule-wp/" target="_blank" class="poly-default-banner-box--menu-block-help poly-default-banner-box--bg-image"></a>
            </div>
        </div>
<?php
    }
?>
</div>
