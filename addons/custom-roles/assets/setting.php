<?php

/**
 * Custom Roles dashboard template
 */
?>
<div>
    <div class="cx-vui-component">
        <div class="cx-vui-component-meta">
            <a href="https://whitehatdevs.com/community/" target="_blank" class="jet-engine-dash-help-link">
                <?php require crocoblock_addon()->addons->addons_path('custom-roles/assets/icon/meta.svg') ?>
                What is this and how it works?
            </a>
        </div>
    </div>
    <div class="cx-vui-inner-panel">
        <div tabindex="0" class="cx-vui-repeater">
            <div class="cx-vui-repeater__items">
                <div :class="{ 'cx-vui-repeater-item': true, 'cx-vui-panel': true, 'cx-vui-repeater-item--is-collpased': editID !== item.id }" v-for="( item, index ) in items">
                    <div :class="{ 'cx-vui-repeater-item__heading': true, 'cx-vui-repeater-item__heading--is-collpased': editID !== item.id }">
                        <div class="cx-vui-repeater-item__heading-start" @click="setEdit( item.id )">
                            <?php require crocoblock_addon()->addons->addons_path('custom-roles/assets/icon/dropdown.svg') ?>
                            <div class="cx-vui-repeater-item__title">{{ item.name }}</div>
                            <div class="cx-vui-repeater-item__subtitle">
                                {{ !item.slug ? 'Role Not Created' : (item.name.toLowerCase().replace(/\s+/g, '_') !== item.slug ? item.slug + ' (the slug of role cannot change)' : item.slug) }}
                            </div>
                        </div>
                        <div class="cx-vui-repeater-item__heading-end">
                            <div class="cx-vui-repeater-item__clean" @click="deleteID = item.id">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="16" height="16" transform="matrix(1 0 0 -1 0 16)" fill="white"></rect><path d="M2.28564 14.192V3.42847H13.7142V14.192C13.7142 14.6685 13.5208 15.0889 13.1339 15.4533C12.747 15.8177 12.3005 15.9999 11.7946 15.9999H4.20529C3.69934 15.9999 3.25291 15.8177 2.866 15.4533C2.4791 15.0889 2.28564 14.6685 2.28564 14.192Z"></path><path d="M14.8571 1.14286V2.28571H1.14282V1.14286H4.57139L5.56085 0H10.4391L11.4285 1.14286H14.8571Z"></path></svg>
                                    <div class="cx-vui-tooltip" v-if="deleteID === item.id">
                                        <?php _e( 'Are you sure?', 'jet-engine' ); ?>
                                        <br><span class="cx-vui-repeater-item__confrim-del" @click.stop="deleteCustomRole( item.id, index )"><?php _e( 'Yes', 'jet-engine' ); ?></span>&nbsp;/&nbsp;<span class="cx-vui-repeater-item__cancel-del" @click.stop="deleteID = false"><?php _e( 'No', 'jet-engine' ); ?></span>
									</div>
                            </div>
                        </div>
                    </div>
                    <div :class="{ 'cx-vui-repeater-item__content': true, 'cx-vui-repeater-item__content--is-collpased': editID !== item.id }">
                        <crocoblock-addons-custom-roles-item :value="item"/>
                    </div>
                </div>
            </div>
            <div class="cx-vui-repeater__actions">
                <cx-vui-button
                    button-style="accent-border"
                    size="mini"
                    :disabled="isBusy"
                    @click="newCustomRole"
                >
                    <span
                        slot="label"
                        v-html="'<?php _e( '+ New Role', 'crocoblock-addons' ); ?>'"
                    ></span>
                </cx-vui-button>
            </div>
        </div>
    </div>
</div>