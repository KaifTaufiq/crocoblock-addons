(function ($, ConditionalFormatting) {
  "use strict";
  Vue.component("crocoblock-addons-conditional-formatting", {
    template: "#crocoblock-addons-conditional-formatting",
    data: function () {
      return {
        items: ConditionalFormatting.listings,
        nonce: ConditionalFormatting._nonce,
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
      newFormatting: function( event, isSample ) {
        var self = this;
        self.isBusy = true;
        
        var item = {
            'name' : '',
            'conditions' : [],
        }

        if ( isSample) {
            item = JSON.parse( JSON.stringify( ConditionalFormatting.sample_item ) );
        }

        jQuery.ajax({
            url: window.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'crocoblock_addons_conditional_formatting_save',
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
      deleteFormatting: function( itemID, itemIndex ) {
        var self = this;

        self.items.splice( itemIndex, 1 );

        jQuery.ajax({
            url: window.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'crocoblock_addons_conditional_formatting_delete',
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

  Vue.component("crocoblock-addons-conditional-formatting-item", {
    template: "#crocoblock-addons-conditional-formatting-item",
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
        nonce: ConditionalFormatting._nonce,
        saving: false,
        saveLabel: ConditionalFormatting.save_label,
        savingLabel: ConditionalFormatting.saving_label,
      };
    },
    methods: {
        isDisabled: function() {
            return this.isBusy;
        },
        addNewCondition: function(event , key, defaultConditions){
            var field = defaultConditions || {};
  
            field.id = Math.round( Math.random() * 1000000 );
            field.collapsed = false;
    
            if( !this.settings[key]) {
                this.$set(this.settings, key, []);
            }
    
            this.settings[key].push(field);
        },
        isCollapsed: function (field) {
            if (undefined === field.collapsed || true === field.collapsed) {
                return true;
            } else {
                return false;
            }
        },
        setRepeaterFieldProp: function (parentKey, index, key, value) {
            var field = this.settings[parentKey][index];
    
            field[key] = value;
    
            this.settings[parentKey].splice(index, 1, field);
        },
        saveConditionalFormatting: function() {
            var self = this;
            self.saving = true;

            jQuery.ajax({
                url: window.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'crocoblock_addons_conditional_formatting_save',
                    nonce: self.nonce,
                    item: self.settings,
                }
            }).done( function( response) {
                if ( !response.success) {
                    self.$CXNotice.add( {
                        message: response.data.message,
                        type: 'error',
                        duration: 15000,
                    } );
                    self.saving = false;
                } else {
                    self.$CXNotice.add( {
                        message: 'Saved',
                        type: 'success',
                        duration: 7000,
                    } );
                    self.saving = false;
                }
            }).fail( function( jqXHR, textStatus, errorThrown) {
                self.$CXNotice.add( {
                    message: errorThrown,
                    type: 'error',
                    duration: 15000,
                } );
                self.saving = false;
            });
        },
        cloneCondition: function ( index, key ) {
            var field = JSON.parse( JSON.stringify( this.settings[key][index] ) );
            field.collapsed = false;
            this.settings[key].push( field );
        },
        deleteCondition: function (index, key) {
            this.settings[key].splice(index, 1);
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
    },
    mounted: function () {
        this.settings = this.value;
    }
  });
})(jQuery, CrocoBlockConditionalFormattingSettings);
