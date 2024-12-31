<?php
namespace CrocoblockAddons;
if (!defined('WPINC')) {
    die;
}

if (!class_exists('Dashboard')) {
    class Dashboard
    {
        public $nonce_action = 'crocoblock-addons-settings';

        public function __construct()
        {
            add_action('jet-engine/dashboard/tabs/after-skins', [$this, 'settings_tab']);
            add_action('jet-engine/dashboard/assets', [$this, 'settings_assets']);
        }

        public function get_nonce_action()
        {
            return $this->nonce_action;
        }

        public function settings_tab()
        {
        ?>
            <cx-vui-tabs-panel name="crocoblock_addons" label="<?php _e('Crocblock Addons', 'crocoblock-addons'); ?>"
                key="crocoblock_addons">
                <keep-alive>
                    <crocoblock-addons-settings></crocoblock-addons-settings>
                </keep-alive>
            </cx-vui-tabs-panel>
        <?php
        }

        public function settings_assets()
        {
            wp_enqueue_script(
                'crocoblock-addons-settings',
                crocoblock_addon()->plugin_url( 'dashboard/assets/js/dashboard.js' ),
                ['cx-vue-ui'],
                crocoblock_addon()->get_version(),
                true
            );

            wp_localize_script(
                'crocoblock-addons-settings',
                'CrocoClockAddonsSettings',
                [
                    'addons' => crocoblock_addon()->addons->get_all_addons_for_js(true),
                    'active_addons' => crocoblock_addon()->addons->get_active_addons(),
                    '_nonce' => wp_create_nonce($this->nonce_action),
                    'messages'          => array(
                    'saved'            => __('Saved!', 'crocoblock-addons'),
                    'saved_and_reload' => __('Saved! One of activated/deactivated addon requires page reloading. Page will be reloaded automatically in few seconds.', 'crocoblock-addons'),
                    ),
                ]
            );

            add_action('admin_footer', [$this, 'print_templates']);
        }

        public function print_templates(){
            ?>
        <script type="text/x-template" id="crocoblock-addons-settings">
            <cx-vui-component-wrapper
						label="<?php _e('Addons or Widgets', 'jet-engine'); ?>"
						description="<?php _e('Enable/disable Addons or Widgets from Crocoblock Addons', 'jet-engine'); ?>"
						:wrapper-css="[ 'vertical-fullwidth' ]"
					>
            <div class="cx-vui-inner-panel" style="padding: 10px 10px 20px 10px;">
      <div tabindex="0" class="cx-vui-repeater">
          <div class="cx-vui-repeater__items jet-engine-modules">
              <div class="cx-vui-repeater-item cx-vui-panel cx-vui-repeater-item--is-collpased" v-for="module in internalModules" style="align-content: center;">
                  <div class="cx-vui-repeater-item__heading cx-vui-repeater-item__heading--is-collpased">
                      <div class="cx-vui-repeater-item__heading-start">
                          <cx-vui-switcher :prevent-wrap="true" :value="isActive( module.value )" @input="switchActive( $event, module )"></cx-vui-switcher>
                          <div class="cx-vui-repeater-item__title je-module-heading" @click="switchActive( $event, module )">
                              {{ module.label }}
                          </div>
                      </div>
                      <div class="cx-vui-repeater-item__heading-end">
                          <div class="jet-engine-module-info" @click="moduleDetails = module.value" v-if="moduleDetails !== module.value">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M10 2c4.42 0 8 3.58 8 8s-3.58 8-8 8-8-3.58-8-8 3.58-8 8-8zm1 4c0-.55-.45-1-1-1s-1 .45-1 1 .45 1 1 1 1-.45 1-1zm0 9V9H9v6h2z"/></g></svg>
                                  <div class="cx-vui-tooltip">
                                      <?php _e('Click here to get more info', 'jet-engine'); ?>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="jet-engine-module-box" v-if="moduleDetails === module.value">
                          <div class="jet-engine-module-box-overlay" @click="moduleDetails = null"></div>
                              <div class="jet-engine-module">
                                      <div class="jet-engine-module-info-close" @click="moduleDetails = null">
                                          <svg width="20" height="20" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.00671L8.00671 7L12 10.9933L10.9933 12L7 8.00671L3.00671 12L2 10.9933L5.99329 7L2 3.00671L3.00671 2L7 5.99329L10.9933 2L12 3.00671Z"></path></svg>
                                      </div>
                                      <keep-alive>
                                          <jet-video-embed v-if="module.embed && moduleDetails === module.value" :embed="module.embed">
                                      </keep-alive>
                                      <div class="jet-engine-module-content">
                                          <div class="jet-engine-details" v-if="module.details" v-html="module.details"></div>
                                          <div class="jet-engine-links" v-if="module.links.length">
                                              <div class="jet-engine-links__title">
                                                  <?php _e('Useful links:', 'jet-engine'); ?>
                                              </div>
                                              <div class="jet-engine-links__item" v-for="link in module.links">
                                                  <a :href="link.url" target="_blank" class="jet-engine-links__link">
                                                      <svg v-if="link.is_video" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M19 15V5c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h13c1.1 0 2-.9 2-2zM8 14V6l6 4z"/></g></svg>
                                                      <svg v-else width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M10 10L2.54 7.02 3 18H1l.48-11.41L0 6l10-4 10 4zm0-5c-.55 0-1 .22-1 .5s.45.5 1 .5 1-.22 1-.5-.45-.5-1-.5zm0 6l5.57-2.23c.71.94 1.2 2.07 1.36 3.3-.3-.04-.61-.07-.93-.07-2.55 0-4.78 1.37-6 3.41C8.78 13.37 6.55 12 4 12c-.32 0-.63.03-.93.07.16-1.23.65-2.36 1.36-3.3z"/></g></svg>
                                                      {{ link.label }}
                                                  </a>
                                              </div>
                                          </div>
                                          <div class="jet-engine-module-supports">
                                              <div :class="{ 'jet-module-icon': true, 'jet-module-icon--elementor': true, 'jet-module-icon--inactive': ! module.isElementor }">
                                                  <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 11.1111C0 4.97462 4.97504 0 11.1111 0H88.8889C95.025 0 100 4.97462 100 11.1111V88.8889C100 95.0254 95.025 100 88.8889 100H11.1111C4.97504 100 0 95.0254 0 88.8889V11.1111Z" fill="url(#paint0_linear)"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M38.6677 27.8688H30.0005V72.1311H38.6677V27.8688ZM73.3338 54.4174V45.5649H47.3349V54.4174H73.3338ZM73.3338 27.8688V36.7213H47.3349V27.8688H73.3338ZM73.3338 72.1117V63.2592H47.3349V72.1117H73.3338Z" fill="white"></path><defs><linearGradient id="paint0_linear" x1="100" y1="0" x2="1.68772" y2="102.124" gradientUnits="userSpaceOnUse"><stop stop-color="#E9325D"></stop><stop offset="0.484375" stop-color="#982F67"></stop><stop offset="1" stop-color="#392B73"></stop></linearGradient></defs></svg>&nbsp;&nbsp;&nbsp;
                                                  <div class="jet-module-label" v-if="module.isElementor">
                                                      <?php _e('Elementor views supported', 'jet-engine'); ?>
                                                  </div>
                                                  <div class="jet-module-label" v-else>
                                                      <?php _e('Elementor views not supported', 'jet-engine'); ?>
                                                  </div>
                                              </div>
                                              <div :class="{ 'jet-module-icon': true, 'jet-module-icon--blocks': true, 'jet-module-icon--inactive': ! module.isBlocks }">
                                                  <svg viewBox="0 0 48 51" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M46.2744 17.134C45.3301 16.492 44.0461 16.7563 43.4039 17.7006C39.6651 23.3279 31.7339 23.63 31.3185 23.63C31.2429 23.63 31.2052 23.63 31.1296 23.63C21.3479 23.63 17.6088 31.9766 17.4578 32.3165C17.0046 33.3739 17.4956 34.5825 18.5153 35.0358C18.7797 35.1491 19.0819 35.2246 19.3461 35.2246C20.1393 35.2246 20.8946 34.7714 21.2346 33.9782C21.2723 33.9028 23.8404 28.162 30.4497 27.7845V38.4725C30.1855 40.7764 29.0902 42.5892 27.164 43.9488C25.1623 45.3463 22.4809 46.0638 19.1951 46.0638C15.2673 46.0638 12.0571 44.7043 9.67767 42.0227C7.26067 39.3412 6.05209 35.5268 6.05209 30.6169L6.0898 18.8336C6.27866 14.4903 7.44938 11.0534 9.67767 8.59855C12.0948 5.91712 15.2673 4.55755 19.1951 4.55755C22.4809 4.55755 25.1623 5.27512 27.164 6.6724C29.1658 8.06984 30.2988 9.99599 30.4876 12.4886C30.4876 12.5641 30.4876 12.6774 30.4876 12.753C30.4876 14.1881 31.6583 15.359 33.0935 15.359C34.5286 15.359 35.6995 14.1881 35.6995 12.753C35.6995 12.6774 35.6995 12.5641 35.6995 12.4886C35.3218 8.7497 33.6222 5.80384 30.563 3.57554C27.5039 1.34725 23.6894 0.251953 19.0819 0.251953C13.6055 0.251953 9.18667 2.06482 5.82537 5.65269C2.65293 9.01398 0.991216 13.4329 0.802356 18.8713C0.802356 19.249 0.764648 19.6268 0.764648 20.0043L0.802356 30.6169H0.764648C0.764648 36.6219 2.46407 41.4184 5.82537 45.0064C9.18667 48.5936 13.6055 50.4065 19.0819 50.4065C23.6894 50.4065 27.5039 49.3115 30.563 47.0835C33.3579 45.0441 35.0196 42.3627 35.5862 39.0391L35.6995 27.1802C39.1364 26.3492 43.8195 24.4609 46.7652 20.0043C47.5207 19.0602 47.2562 17.776 46.2744 17.134Z" fill="currentColor"/></svg>&nbsp;&nbsp;&nbsp;
                                                  <div class="jet-module-label" v-if="module.isBlocks">
                                                      <?php _e('Blocks views supported', 'jet-engine'); ?>
                                                  </div>
                                                  <div class="jet-module-label" v-else>
                                                      <?php _e('Blocks views not supported', 'jet-engine'); ?>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="jet-enigne-modules-save">
                      <cx-vui-button
                          button-style="accent"
                          size="mini"
                          :loading="saving"
                          @click="saveModules"
                      >
                          <span
                              slot="label"
                              v-html="'<?php _e('Save', 'jet-engine'); ?>'"
                          ></span>
                      </cx-vui-button>
                      &nbsp;&nbsp;&nbsp;&nbsp;
                      <span
                          class="cx-vui-inline-notice cx-vui-inline-notice--success"
                          v-if="'success' === result"
                          v-html="successMessage"
                      ></span>
                      <span
                          class="cx-vui-inline-notice cx-vui-inline-notice--error"
                          v-if="'error' === result"
                          v-html="errorMessage"
                      ></span>
                  </div>
          </div>
          </cx-vui-component-wrapper>
</script>

<?php
        }
    }
}
