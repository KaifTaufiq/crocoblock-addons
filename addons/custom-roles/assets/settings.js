(function ($, CustomRoles) {
  "use strict";
  Vue.component("crocoblock-addons-custom-roles", {
    template: "#crocoblock-addons-custom-roles",
    data: function () {
        return {
          items: CustomRoles.items,
          nonce: CustomRoles._nonce,
          editID: false,
          isBusy: false,
          deleteID: false,
        };
      },
    methods: {
      setEdit: function (itemID) {
        if (itemID === this.editID) {
          this.editID = false;
        } else {
          this.editID = itemID;
        }
      },
      newCustomRole: function( event, isSample ) {
        var self = this;
        self.isBusy = true;
        
        var item = {
            'name' : '',
            'conditions' : [],
        }

        if ( isSample) {
            item = JSON.parse( JSON.stringify( CustomRoles.sample_item ) );
        }

        jQuery.ajax({
            url: window.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'crocoblock_addons_custom_role_save',
                nonce: self.nonce,
                item: item,
                item_id: false,
            }
        }).done( function( response) {
            if ( !response.success) {
                if ( response.data ) {
                    self.$CXNotice.add( {
                        message: response.data.message || 'Unknown error',
                        type: 'error',
                        duration: 15000,
                    } );
                }
            } else {
                item.id = response.data.item_id;
                self.items.push( item );
                self.setEdit( item.id );

                self.$CXNotice.add( {
                    message: response.data.message,
                    type: 'success',
                    duration: 7000,
                } );
            }

            self.isBusy = false;
        }).fail( function( jqXHR, textStatus, errorThrown) {

            self.$CXNotice.add( {
                message: errorThrown,
                type: 'error',
                duration: 15000,
            } );

            self.isBusy = false;

        });
      },
      deleteCustomRole: function( itemID, itemIndex ) {
        var self = this;

        self.items.splice( itemIndex, 1 );

        jQuery.ajax({
            url: window.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'crocoblock_addons_custom_role_delete',
                nonce: self.nonce,
                item_id: itemID,
            }
        }).done( function( response ) {
            if ( !response.success) {
                self.$CXNotice.add( {
                    message: response.data.message || 'Unknown error',
                    type: 'error',
                    duration: 15000,
                } );
            } else {
                self.$CXNotice.add( {
                    message: response.data.message,
                    type: 'success',
                    duration: 7000,
                } );
            }
        } ).fail( function( jqXHR, textStatus, errorThrown ) {
            self.$CXNotice.add( {
                message: errorThrown,
                type: 'error',
                duration: 15000,
            } );
        } );
      }
    },
  });

  Vue.component("crocoblock-addons-custom-roles-item", {
    template: "#crocoblock-addons-custom-roles-item",
    props: {
        value: {
            type: Object,
            default: function () {
            return {};
            },
        },
        isBusy: {
            type: Boolean,
            default: false,
        },
    },
    data: function () {
        return {
            settings: {},
            nonce: CustomRoles._nonce,
            saving: false,
            saveLabel: CustomRoles.save_label,
            savingLabel: CustomRoles.saving_label,
            dropdown: CustomRoles.dropdown_options,
        };
    },
    methods: {
        isCollapsed: function (field) {
            if (undefined === field.collapsed || true === field.collapsed) {
                return true;
            } else {
                return false;
            }
        },
        isDisabled: function() {
            return this.isBusy;
        },
        setRepeaterFieldProp: function (parentKey, index, key, value) {
            var field = this.settings[parentKey][index];
    
            field[key] = value;
    
            this.settings[parentKey].splice(index, 1, field);
        },
        buttonLabel: function() {
            if ( this.isSaving() ) {
                return this.savingLabel;
            } else {
                return this.saveLabel;
            }
        },
        isSaving: function() {
            return this.saving || this.isBusy;
        },
        saveCustomRole: function() {
            var self = this;
            self.saving = true;

            jQuery.ajax({
                url: window.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'crocoblock_addons_custom_role_save',
                    nonce: self.nonce,
                    item: self.settings,
                }
            }).done( function( response ) {
                if ( !response.success) {
                    if ( response.data ) {
                        self.$CXNotice.add( {
                            message: response.data.message || 'Unknown error',
                            type: 'error',
                            duration: 15000,
                        } );
                    }
                } else {
                    self.$CXNotice.add( {
                        message: response.data.message,
                        type: 'success',
                        duration: 7000,
                    } );
                }
                self.saving = false;
            }).fail( function( jqXHR, textStatus, errorThrown ) {
                self.$CXNotice.add( {
                    message: errorThrown,
                    type: 'error',
                    duration: 15000,
                } );
                self.saving = false;
            });
        }
    },
    mounted: function () {
        this.settings = this.value;
    }
  });
})(jQuery, CrocoBlockCustomRolesSettings);
