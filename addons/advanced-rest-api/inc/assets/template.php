<?php

/**
 * Advanced Rest API dashboard template
 */
?>
<script type="text/x-template" id="crocoblock-addons-advanced-rest-api">
    <div>
        <div class="cx-vui-component">
            <div class="cx-vui-component-meta">
                <a href="https://whitehatdevs.com/community/" target="_blank" class="jet-engine-dash-help-link">
                    <?php require crocoblock_addon()->addons->addons_path('advanced-rest-api/inc/assets/icon/meta.svg') ?>
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
                                <?php require crocoblock_addon()->addons->addons_path('advanced-rest-api/inc/assets/icon/dropdown.svg') ?>
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
    </div>
</script>
<script type="text/x-template" id="crocoblock-addons-advanced-rest-api-item">
    <div>
        <cx-vui-input label="API Endpoint URL" description="URL for the API endpoints to get items from" :wrapper-css="[ 'equalwidth' ]" size="fullwidth" v-model="settings.url"></cx-vui-input>
        <cx-vui-switcher label="Single Rest API Endpoint" description="Enable Single Rest API Item" :wrapper-css="[ 'equalwidth' ]" size="fullwidth" v-model="settings.isSingle"></cx-vui-switcher>
        <cx-vui-switcher label="Enable POST Request" description="By Deafult Jet Engine Makes GET REST API Requests, You can Enable it to be POST" :wrapper-css="[ 'equalwidth' ]" size="fullwidth" v-model="settings.isPOST"></cx-vui-switcher>
        <cx-vui-component-wrapper :wrapper-css="[ 'fullwidth-control' ]">
            <div class="cx-vui-inner-panel">
                <cx-vui-repeater button-label="Add Query Parameter" button-size="mini" button-style="accent" v-model="settings.query_parameters" @add-new-item="addNewQueryParameter( $event , 'query_parameters', { 'key': '' , 'from': '' , 'query_var' : '' , 'shortcode' : '' , 'debugShortcode' : '' } )">
                    <cx-vui-repeater-item v-for="( header, index ) in settings.query_parameters" :title="settings.query_parameters[ index ].keyDisplay" :subtitle="'Paste the key with { } in API URL to be replaced the Fetched Value'" :collapsed="isCollapsed( header )" @clone-item="copyKey( settings.query_parameters[ index ].key )" @delete-item="deleteRepeaterField( $event, 'query_parameters' )"  :index="index" :key="header._id">
                        <cx-vui-select label="Get Value From" :wrapper-css="[ 'equalwidth' ]" size="fullwidth" :options-list="dropdown" v-model="settings.query_parameters[ index ].from"></cx-vui-select>
                        <cx-vui-input label="URL Query Variable" :wrapper-css="[ 'equalwidth' ]" size="fullwidth" v-if="settings.query_parameters[ index ].from === 'query_var'" :value="settings.query_parameters[ index ].query_var" @input="setRepeaterFieldProp( 'query_parameters', index, 'query_var', $event )"></cx-vui-input>
                        <cx-vui-switcher label="Debug Shortcode" description="Print the Shortcode Result in Console Log" :wrapper-css="[ 'equalwidth' ]" v-model="settings.query_parameters[ index ].debugShortcode" :conditions="[ { input: settings.query_parameters[ index ].from, compare: 'equal', value: 'shortcode' } ]"></cx-vui-switcher>
                        <cx-vui-textarea label="Shortcode" description="Shortcode with [ ] " :wrapper-css="[ 'equalwidth' ]" size="fullwidth" v-if="settings.query_parameters[ index ].from === 'shortcode'" :value="settings.query_parameters[ index ].shortcode" @input="setRepeaterFieldProp( 'query_parameters', index, 'shortcode', $event )"></cx-vui-textarea>
                    </cx-vui-repeater-item>
                </cx-vui-repeater>
            </div>
        </cx-vui-component-wrapper>
        <cx-vui-component-wrapper :wrapper-css="[ 'equalwidth' ]">
            <cx-vui-button button-style="accent" :loading="saving" :disabled="isDisabled()" @click="saveAdvancedRestAPI">
                <span slot="label">
                    {{ buttonLabel() }}
                </span>
            </cx-vui-button>
        </cx-vui-component-wrapper>
    </div>
</script>