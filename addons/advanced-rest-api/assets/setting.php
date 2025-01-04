<?php

/**
 * Advanced Rest API dashboard template
 */
?>
<div>
    <div class="cx-vui-component">
        <div class="cx-vui-component-meta">
            <a href="https://whitehatdevs.com/community/" target="_blank" class="jet-engine-dash-help-link">
                <?php require crocoblock_addon()->addons->addons_path('advanced-rest-api/assets/icon/meta.svg') ?>
                What is this and how it works?
            </a>
        </div>
    </div>
    <div class="cx-vui-inner-panel" v-if="items.length > 0">
        <div tabindex="0" class="cx-vui-repeater">
            <div class="cx-vui-repeater__items">
                <div :class="{ 'cx-vui-repeater-item': true, 'cx-vui-panel': true, 'cx-vui-repeater-item--is-collpased': editID !== item.id }" v-for="( item, index ) in items">
                    <div :class="{ 'cx-vui-repeater-item__heading': true, 'cx-vui-repeater-item__heading--is-collpased': editID !== item.id }">
                        <div class="cx-vui-repeater-item__heading-start" @click="setEdit( item.id )">
                            <?php require crocoblock_addon()->addons->addons_path('advanced-rest-api/assets/icon/dropdown.svg') ?>
                            <div class="cx-vui-repeater-item__title">{{ item.name }}</div>
                            <div class="cx-vui-repeater-item__subtitle">{{ item.url }}</div>
                        </div>
                    </div>
                    <div :class="{ 'cx-vui-repeater-item__content': true, 'cx-vui-repeater-item__content--is-collpased': editID !== item.id }">
                        <crocoblock-addons-advanced-rest-api-item :value="item"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="cx-vui-inner-panel" v-if="items.length == 0">
        <div
            class="cx-vui-subtitle"
            v-html="'<?php _e('No REST API Endpoints found.', 'crocoblock-addons'); ?>'"
        ></div>
    </div>
</div>
