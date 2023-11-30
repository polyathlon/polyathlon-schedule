<?php

function poly_infoBox($poly_project){
    $output = "";

    if( (isset($poly_project->title) && $poly_project->title !== '' ) || (isset($poly_project->description) && $poly_project->description !== '' )){
        $output .= "<div class='lg-info'>";

        if(isset($poly_project->title) && $poly_project->title !== '' ){
            $title = POLYHelper::decode2Str($poly_project->title);
            $output .= "<h4>".$title."</h4>";
        }

        if(isset($poly_project->description) && $poly_project->description !== '' ){
            $desc = POLYHelper::decode2Str($poly_project->description);
            $output .= "<p>".$desc."</p>";
        }
        $output .= "</div>";
    }

    $output = htmlentities($output);
    $output = str_replace("\n",'</br>',$output);

    return $output;
}

$gridType =  isset($poly_schedules->extoptions['type']) ? $poly_schedules->extoptions['type'] : POLYGridType::ALBUM;
$showTitle = ($gridType == POLYGridType::SCHEDULE || $gridType == POLYGridType::ALBUM || $gridType == POLYGridType::GALLERY || $gridType == POLYGridType::TEAM);
$showDesc = false;


?>

<style>
    /* Schedule Options Configuration Goes Here*/
    #gallery .tile:hover{
        cursor: <?php echo $poly_schedules->options[POLYOption::kMouseType]; ?> !important;
    }

    /* - - - - - - - - - - - - - - -*/
    /* Tile Hover Customizations */

    /* Customize overlay background */
    #gallery .poly-tile-inner .overlay,
    #gallery .tile .caption {
        background-color: <?php echo POLYHelper::hex2rgba($poly_schedules->options[POLYOption::kTileOverlayColor].$poly_schedules->options[POLYOption::kTileOverlayOpacity]) ?> !important;
    }

    #gallery .poly-tile-inner.poly-details-bg .details {
        background-color: <?php echo POLYHelper::hex2rgba($poly_schedules->options[POLYOption::kTileOverlayColor].$poly_schedules->options[POLYOption::kTileOverlayOpacity]) ?> !important;
    }

    #gallery .poly-tile-inner .details h3 {
        color: <?php echo $poly_schedules->options[POLYOption::kTileTitleColor] ?>;
        text-align: center;
        font-size: 18px;
    }

    #gallery .poly-tile-inner .details p {
        color: <?php echo $poly_schedules->options[POLYOption::kTileDescColor] ?>;
        text-align: center;
        font-size: 11px;
    }

    <?php if(!$showDesc): ?>
    #gallery .poly-tile-inner .details h3 {
        margin-bottom: 0px;
    }
    <?php endif; ?>

</style>
<?php $isCatalog = (!empty($poly_schedules->extoptions) && !empty($poly_schedules->extoptions['type']) && $poly_schedules->extoptions['type'] == POLYGridType::CATALOG); ?>

<!--Here Goes HTML-->
<div class="poly-wrapper">
    <div id="gallery">
        <div id="ftg-items" class="ftg-items">
            <?php foreach($poly_schedules->projects as $poly_project): ?>
                <div id="poly-tile-<?php echo $poly_project->id?>" class="tile" data-url="<?php echo isset($poly_project->url) ? $poly_project->url : ""?>">
                    <?php if ($gridType == POLYGridType::CLIENT_LOGOS) { ?>
                    <div class="poly-tile-inner details27 image01">
                    <?php } else { ?>
                    <div class="poly-tile-inner details33 poly-details-bg image01">
                    <?php } ?>

                    <?php if($isCatalog) { ?>
                    <div class="poly-additional-block1">
                        <?php
                        $title = isset($poly_project->title) ? POLYHelper::decode2Str($poly_project->title) : "";
                        if (!empty($title)) {
                            echo '<h3 class="poly-catalog-title">'.$title.'</h3>';
                        }
                        ?>
                    </div>
                    <?php } ?>

                    <?php
                        $coverInfo = POLYHelper::decode2Str($poly_project->cover);
                        $coverInfo = POLYHelper::decode2Obj($coverInfo);
                        $meta = POLYHelper::getAttachementMeta($coverInfo->id, $poly_schedules->options[POLYOption::kThumbnailQuality]);

                        if (isset($poly_project->details)) {
                            $poly_project->details = json_decode($poly_project->details);
                            $catalogPrice = (isset($poly_project->details) && isset($poly_project->details->price)) ? $poly_project->details->price : "";
                            $catalogSale = (isset($poly_project->details) && isset($poly_project->details->sale)) ? $poly_project->details->sale : "";
                        }
                    ?>

                    <a id="<?php echo $poly_project->id ?>" class="tile-inner">
                        <?php if ($isCatalog && !empty($catalogSale)) { ?>
                            <div class='poly-badge-box poly-badge-pos-RT'><div class="poly-badge"><span><?php echo '-'.$catalogSale.'%'; ?></span></div></div>
                        <?php } ?>
                        <img class="poly-item poly-tile-img" src="<?php echo $meta['src'] ?>" data-width="<?php echo $meta['width']; ?>" data-height="<?php echo $meta['height']; ?>" />
                        <?php
                        $html = '';
                        if ($showTitle || $showDesc) {
                            $html .= "<div class='overlay'></div>";
                            $title = isset($poly_project->title) ? POLYHelper::decode2Str($poly_project->title) : "";
                            $desc = isset($poly_project->description) ? POLYHelper::decode2Str($poly_project->description) : "";
                            $desc = POLYHelper::truncWithEllipsis($desc, 15);

                            if ($title != '' || $desc != '') {
                                $html .= "<div class='details'>";
                                if ($showTitle) {
                                    $html .= "<h3>{$title}</h3>";
                                }
                                if ($showDesc) {
                                    $html .= "<p>{$desc}</p>";
                                }
                                $html .= "</div>";
                            }
                        } else {
                            if ($gridType != POLYGridType::CLIENT_LOGOS && $gridType != POLYGridType::CATALOG) {
                                $html .= '<div class="caption"></div>';
                            }
                        }
                        echo $html;
                        ?>
                    </a>
                    <?php if ($isCatalog) { ?>
                        <div class="poly-additional-block2">
                            <?php
                            if (isset($poly_project->details)) {
                                $sale = '';
                                $overline = '';
                                if (!empty($catalogSale) && !empty($catalogPrice)) {
                                    $sale = "$" . number_format((float)($catalogPrice - $catalogPrice * $catalogSale / 100), 2, '.', '');
                                    $overline = 'style="text-decoration: line-through;"';
                                    echo "<p><span {$overline}> "."$"."{$catalogPrice} </span> &nbsp;<span>{$sale}</span></p>";
                                } elseif (!empty($catalogPrice)) {
                                    echo "<p><span>"."$"."{$catalogPrice}</span></p>";
                                }
                            }
                            ?>
                            <?php if (!empty($poly_project->url)) { ?><p><button class="poly-product-buy-button" onclick="poly_loadHref('<?php echo (!empty($poly_project->url) ? $poly_project->url : '#'); ?>', true)">BUY NOW</button></p><?php } ?>
                        </div>
                    <?php } ?>
                    </div>

                    <?php if(($gridType == POLYGridType::ALBUM || $gridType == POLYGridType::SCHEDULE) && !$poly_schedules->options[POLYOption::kDirectLinking]) : ?>

                    <ul id="poly-light-gallery-<?php echo $poly_project->id; ?>" class="poly-light-gallery" style="display: none;" data-sub-html="<?php echo poly_infoBox( $poly_project)?>" data-url="<?php echo isset($poly_project->url) ? $poly_project->url : ''; ?>">
                        <?php
                            $meta = POLYHelper::getAttachementMeta($coverInfo->id);
                            $metaThumb = POLYHelper::getAttachementMeta($coverInfo->id, "medium");
                        ?>

                        <li data-src="<?php echo $meta['src']; ?>" >
                            <a href="#">
                                <img src="<?php echo $metaThumb['src']; ?>" />
                            </a>
                        </li>

                        <?php foreach($poly_project->pics as $pic): ?>
                            <?php if(!empty($pic)): ?>
                                <?php
                                    $picInfo = POLYHelper::decode2Str($pic);
                                    $picInfo = POLYHelper::decode2Obj($picInfo);

                                    $meta = POLYHelper::getAttachementMeta($picInfo->id);
                                    $metaThumb = POLYHelper::getAttachementMeta($picInfo->id, "medium");
                                ?>

                                <li data-src="<?php echo $meta['src']; ?>">
                                    <a href="#">
                                        <img src="<?php echo $metaThumb['src']; ?>" />
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if($gridType != POLYGridType::ALBUM && $gridType != POLYGridType::SCHEDULE && !$poly_schedules->options[POLYOption::kDirectLinking]) : ?>
                <ul id="poly-light-gallery" class="poly-light-gallery" style="display: none;" >
                <?php foreach($poly_schedules->projects as $poly_project): ?>
                    <?php
                        $coverInfo = POLYHelper::decode2Str($poly_project->cover);
                        $coverInfo = POLYHelper::decode2Obj($coverInfo);
                        $meta = POLYHelper::getAttachementMeta($coverInfo->id, $poly_schedules->options[POLYOption::kThumbnailQuality]);
                        $meta = POLYHelper::getAttachementMeta($coverInfo->id);
                        $metaThumb = POLYHelper::getAttachementMeta($coverInfo->id, "medium");
                    ?>

                    <li id="poly-light-gallery-item-<?php echo $poly_project->id; ?>" data-src="<?php echo $meta['src']; ?>" data-sub-html="<?php echo poly_infoBox( $poly_project)?>" data-url="<?php echo isset($poly_project->url) ? $poly_project->url : ''; ?>">
                        <a href="#">
                            <img src="<?php echo $metaThumb['src']; ?>" />
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
    $approxTileWidth = ( isset($poly_schedules->options[POLYOption::kTileApproxWidth]) && !empty($poly_schedules->options[POLYOption::kTileApproxWidth]) ) ? $poly_schedules->options[POLYOption::kTileApproxWidth] : 220;
    $approxTileHeight = ( isset($poly_schedules->options[POLYOption::kTileApproxHeight]) &&  !empty($poly_schedules->options[POLYOption::kTileApproxHeight]) ) ? $poly_schedules->options[POLYOption::kTileApproxHeight] : 220;
    $minTileWidth = ( isset($poly_schedules->options[POLYOption::kTileMinWidth]) && !empty($poly_schedules->options[POLYOption::kTileMinWidth]) ) ? $poly_schedules->options[POLYOption::kTileMinWidth] : 200;
?>

<!--Here Goes JS-->
<script>
    (function($) {
        $(document).ready(function(){

            var tileParams = {};

            if(<?php echo ($gridType == POLYGridType::CLIENT_LOGOS || $gridType == POLYGridType::TEAM) ? 1 : 0 ?>) {
                tileParams.approxTileWidth = <?php echo $approxTileWidth; ?>;
                tileParams.approxTileHeight = <?php echo $approxTileHeight; ?>;
                tileParams.minTileWidth = <?php echo $minTileWidth; ?>;
            }

            if(<?php echo ($gridType == POLYGridType::CATALOG) ? 1 : 0 ?>) {
                tileParams.addBlock1Height = 40;
                tileParams.addBlock2Height = 100;
            }
            jQuery('#gallery').polyTiledLayer(tileParams);

            $( ".poly-light-gallery" ).each(function() {
              var id = $( this ).attr("id");
              $("#" + id).lightGallery({
                mode: 'slide',
                useCSS: true,
                cssEasing: 'ease', //'cubic-bezier(0.25, 0, 0.25, 1)',//
                easing: 'linear', //'for jquery animation',//
                speed: 600,
                addClass: '',

                closable: true,
                loop: true,
                auto: false,
                pause: 6000,
                escKey: true,
                controls: true,
                hideControlOnEnd: false,

                preload: 1, //number of preload slides. will exicute only after the current slide is fully loaded. ex:// you clicked on 4th image and if preload = 1 then 3rd slide and 5th slide will be loaded in the background after the 4th slide is fully loaded.. if preload is 2 then 2nd 3rd 5th 6th slides will be preloaded.. ... ...
                showAfterLoad: true,
                selector: null,
                index: false,

                lang: {
                    allPhotos: 'All photos'
                },
                counter: false,

                exThumbImage: false,
                thumbnail: true,
                showThumbByDefault:false,
                animateThumb: true,
                currentPagerPosition: 'middle',
                thumbWidth: 150,
                thumbMargin: 10,


                mobileSrc: false,
                mobileSrcMaxWidth: 640,
                swipeThreshold: 50,
                enableTouch: true,
                enableDrag: true,

                vimeoColor: 'CCCCCC',
                youtubePlayerParams: false, // See: https://developers.google.com/youtube/player_parameters,
                videoAutoplay: true,
                videoMaxWidth: '855px',

                dynamic: false,
                dynamicEl: [],

                // Callbacks el = current plugin
                onOpen        : function(el) {}, // Executes immediately after the gallery is loaded.
                onSlideBefore : function(el) {}, // Executes immediately before each transition.
                onSlideAfter  : function(el) {}, // Executes immediately after each transition.
                onSlideNext   : function(el) {}, // Executes immediately before each "Next" transition.
                onSlidePrev   : function(el) {}, // Executes immediately before each "Prev" transition.
                onBeforeClose : function(el) {}, // Executes immediately before the start of the close process.
                onCloseAfter  : function(el) {}, // Executes immediately once lightGallery is closed.
                onOpenExternal  : function(el, index) {
                    if($(el).attr('data-url')) {
                        var href = $(el).attr("data-url");
                    } else {
                        var href = $("#poly-light-gallery li").eq(index).attr('data-url');
                    }
                    if(href) {
                        poly_loadHref(href,true);
                    }else {
                        return false;
                    }

                }, // Executes immediately before each "open external" transition.
                onToggleInfo  : function(el) {
                  var $info = $(".lg-info");
                  if($info.css("opacity") == 1){
                    $info.fadeTo("slow",0);
                  }else{
                    $info.fadeTo("slow",1);
                  }
                } // Executes immediately before each "toggle info" transition.
              });
            });

            jQuery(".tile").on('click', function (event){
                if(jQuery(event.target).hasClass('poly-product-buy-button') || jQuery(event.target).hasClass('poly-product-checkout-button')) {
                    return false;
                }
                <?php if($poly_schedules->options[POLYOption::kDirectLinking]){ ?>
                event.preventDefault();
                var url = jQuery(this).attr("data-url");
                if(url != '') {
                    var blank = (<?php echo $gridType == POLYGridType::CLIENT_LOGOS ? 1 : 0; ?>) ? true : false;
                    poly_loadHref(url, blank);
                } else {
                    return false;
                }
                <?php } ?>

                event.preventDefault();
                if(jQuery(event.target).hasClass("fa") && !jQuery(event.target).hasClass("zoom")) return;

                <?php if($gridType == POLYGridType::ALBUM || $gridType == POLYGridType::SCHEDULE) { ?>
                var tileId = jQuery(this).attr("id");
                var target = jQuery("#" + tileId + " .poly-light-gallery li:first");
                <?php } else { ?>
                var tileId = jQuery(".tile-inner", jQuery(this)).attr("id");
                var target = jQuery("#poly-light-gallery-item-"+tileId);
                <?php } ?>
                target.trigger( "click" );
            });

        });
    })( jQuery );

</script>
