<div>
    <cx-vui-input label="Name" description="Name the Conditional Formatting" :wrapper-css="[ 'equalwidth' ]" size="fullwidth" v-model="settings.name"></cx-vui-input>
    <cx-vui-component-wrapper :wrapper-css="[ 'fullwidth-control' ]">
        <div class="cx-vui-inner-panel">
            <cx-vui-repeater button-label="Add Condition" button-size="mini" button-style="accent" v-model="settings.conditions" @add-new-item="addNewCondition( $event , 'conditions', { 'from': 'processing' , 'to': 'Processing' } )">
                <cx-vui-repeater-item v-for="( header, index ) in settings.conditions" :title="settings.conditions[ index ].from" :subtitle="'If the fetched value is ' + settings.conditions[ index ].from + ' it will format it to ' + settings.conditions[ index ].to" :collapsed="isCollapsed( header )" @clone-item="cloneCondition( $event , 'conditions' )" @delete-item="deleteCondition( $event, 'conditions' )" :index="index" :key="header._id">
                    <cx-vui-input label="if Fetched Value is" :wrapper-css="[ 'equalwidth' ]" size="fullwidth" :value="settings.conditions[ index ].from" @input="setRepeaterFieldProp( 'conditions', index, 'from', $event )"></cx-vui-input>
                    <cx-vui-input label="Return" :wrapper-css="[ 'equalwidth' ]" size="fullwidth" :value="settings.conditions[ index ].to" @input="setRepeaterFieldProp( 'conditions', index, 'to', $event )"></cx-vui-input>
                </cx-vui-repeater-item>
            </cx-vui-repeater>
        </div>
    </cx-vui-component-wrapper>
    <cx-vui-component-wrapper :wrapper-css="[ 'equalwidth' ]">
        <cx-vui-button button-style="accent" :loading="saving" :disabled="isDisabled()" @click="saveConditionalFormatting">
            <span slot="label">
                {{ buttonLabel() }}
            </span>
        </cx-vui-button>
    </cx-vui-component-wrapper>
</div>