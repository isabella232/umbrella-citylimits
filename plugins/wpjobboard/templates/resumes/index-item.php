

<div class="wpjb-grid-row">
    <div class="wpjb-grid-col wpjb-col-logo">
        <?php if($resume->doScheme("image")): ?>
        <?php elseif($resume->getAvatarUrl()): ?>
        <div class="wpjb-img-36">
            <img src="<?php echo $resume->getAvatarUrl("36x36") ?>" alt="" class="wpjb-img-36" />
        </div>
        <?php else: ?>
        <div class="wpjb-img-36 wpjb-icon-none">
            <span class="wpjb-glyphs wpjb-icon-user wpjb-icon-36"></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="wpjb-grid-col wpjb-col-title wpjb-col-40">
        <span class="wpjb-line-major">
            <a href="<?php echo wpjr_link_to("resume", $resume) ?>"><?php esc_html_e(apply_filters("wpjb_candidate_name", $resume->getSearch(true)->fullname, $resume->id)) ?></a>
        </spn>
        
        <?php if($resume->doScheme("headline")): else: ?>
        <span class="wpjb-sub wpjb-sub-small"><?php esc_html_e($resume->headline) ?></span>
        <?php endif; ?>
    </div>

    <div class="wpjb-grid-col wpjb-col-location wpjb-col-35 wpjb-line-with-icon-left">
        <span class="wpjb-line-major">
            <span class="wpjb-glyphs wpjb-icon-location"></span>
            
            <?php if($resume->locationToString()): ?>
            <?php esc_html_e($resume->locationToString()) ?>
            <?php else: ?>
            &#2014;
            <?php endif; ?>
        </span>
    </div>

    <div class="wpjb-grid-col wpjb-col-date wpjb-grid-col-right wpjb-grid-col-last wpjb-col-10">
        <span class="wpjb-line-major">
            <?php echo wpjb_date_display("M, d", $resume->modified_at, true); ?>
        </span>
    </div>
</div>
