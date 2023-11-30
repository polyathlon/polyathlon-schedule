<?php
    $iconStyle = 'fa-angle-';
    $leftArrClass = substr($iconStyle, -1) == '-' ? $iconStyle.'left' : $iconStyle;
    $rightArrClass = substr($iconStyle, -1) == '-' ? $iconStyle.'right' : $iconStyle;
?>

<style>

    #poly-slider-<?php echo $poly_schedules->id; ?> .owl-carousel {
        padding-left: 0;
        padding-right: 0
    }

    #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-image-wrapper {
        height: 400px
    }

    #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-image {
        background-size: cover;
        background-position: center
    }

    #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-prev, #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-next {
        top: 200px
    }

    #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-prev, #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-next {
        padding: 34px;
        margin-left: 20px;
        margin-right: 20px
    }

    #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-prev i, #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-next i {
        color: #e2e2e2;
        font-size: 60px
    }

    #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-prev:hover i, #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-next:hover i, #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-prev:active i, #poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-next:active i {
        color: #fff
    }

</style>

<div id="poly-slider-<?php echo $poly_schedules->id; ?>" class="poly-slider-layout">
    <a class="poly-slider-ctrl poly-slider-ctrl-prev"><i class="fa <?php echo $leftArrClass; ?>"></i></a>
    <a class="poly-slider-ctrl poly-slider-ctrl-next"><i class="fa <?php echo $rightArrClass; ?>"></i></a>
    <div class="owl-carousel">
        <?php
            foreach ($poly_schedules->projects as $poly_project) {
                $coverInfo = POLYHelper::decode2Obj(POLYHelper::decode2Str($poly_project->cover));
                if (empty($coverInfo)) {
                    continue;
                }
                $url = isset($poly_project->url) ? $poly_project->url : "";
                $title = isset($poly_project->title) ? POLYHelper::decode2Str($poly_project->title) : "";

                $coverInfo = POLYHelper::decode2Obj(POLYHelper::decode2Str($poly_project->cover));
                $coverType = !isset($coverInfo->type) ? POLYAttachmentType::PICTURE : $coverInfo->type;
                $meta = POLYHelper::getAttachementMeta($coverInfo->id, $poly_schedules->options[POLYOption::kThumbnailQuality]);
                $metaOriginal = POLYHelper::getAttachementMeta($coverInfo->id);
            ?>

                    <div class="poly-slider-cell">
                        <div class="poly-slider-image-wrapper">
                            <?php
                                $imgHtml = '<div class="poly-slider-image" style="background-image: url('.$meta['src'].'"></div>';
                                $blank = ($poly_schedules->options[POLYOption::kLoadUrlBlank]) ? ' target="blank" ' : '';
                                echo !empty($url) ? '<a href="' . $url . '" '.$blank.'>'.$imgHtml.'</a>' : $imgHtml;
                            ?>
                        </div>
                    </div>
            <?php

            }
        ?>
    </div>
</div>


<script>
    jQuery(document).ready(function(){

        jQuery('#poly-slider-<?php echo $poly_schedules->id; ?> .owl-carousel').owlCarousel({
            lazyLoad: false,
            items: 1,
            margin: 10,
            center: false,
            loop: true,
            autoplay: false,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            autoHeight: false,
            mouseDrag: true,
            touchDrag: true,
            nav: false,
            slideBy: 1,
            dots: false,
            dotsEach: false,
            animateOut: '',
            animateIn: ''
        });

        jQuery('#poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-prev').click(function() {
            jQuery(this).closest('.poly-slider-layout').find('.owl-carousel').trigger('prev.owl.carousel');
        });

        jQuery('#poly-slider-<?php echo $poly_schedules->id; ?> .poly-slider-ctrl-next').click(function() {
            jQuery(this).closest('.poly-slider-layout').find('.owl-carousel').trigger('next.owl.carousel');
        });

        jQuery(window).resize(function(){
            poly_AdjustSlider(jQuery("#poly-slider-<?php echo $poly_schedules->id; ?>"));
        });

        function poly_AdjustSlider(slider) {
            if (slider.width() <= 600) {
                slider.addClass('poly-slider-mobile');
            } else {
                slider.removeClass('poly-slider-mobile');
            }
        }
        poly_AdjustSlider(jQuery("#poly-slider-<?php echo $poly_schedules->id; ?>"));

    });
</script>
