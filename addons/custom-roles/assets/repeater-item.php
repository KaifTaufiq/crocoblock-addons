<div>
    <cx-vui-input label="Role Name" description="Name the Role" :wrapper-css="[ 'equalwidth' ]" size="fullwidth" v-model="settings.name"></cx-vui-input>
    <cx-vui-select label="Capabilities" description="Role from which to Get Capabilities from"  :wrapper-css="[ 'equalwidth' ]" size="fullwidth" :options-list="dropdown" v-model="settings.from"></cx-vui-select> 
    <cx-vui-component-wrapper :wrapper-css="[ 'equalwidth' ]">
        <cx-vui-button button-style="accent" :loading="saving" :disabled="isDisabled()" @click="saveCustomRole">
            <span slot="label">
                {{ buttonLabel() }}
            </span>
        </cx-vui-button>
    </cx-vui-component-wrapper>
</div>