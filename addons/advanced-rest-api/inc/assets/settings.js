(function ($, AdvancedRestApi) {
    "use strict";
    Vue.component("crocoblock-addons-advanced-rest-api", {
      template: "#crocoblock-addons-advanced-rest-api",
      data: function () {
        return {
          items: AdvancedRestApi.listings,
          nonce: AdvancedRestApi._nonce,
          editID: false,
          isBusy: false,
        }
      },
      methods: {
        setEdit: function (itemID) {
          if (itemID === this.editID) {
            this.editID = false;
          } else {
            this.editID = itemID;
          }
        },
      },
      mounted: function () {
  
      }
    });
  
    Vue.component("crocoblock-addons-advanced-rest-api-item", {
      template: "#crocoblock-addons-advanced-rest-api-item",
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
          settings: {
            keyDisplay: "",
          },
          nonce: AdvancedRestApi._nonce,
          dropdown: AdvancedRestApi.dropdown_options,
          saving: false,
          saveLabel: AdvancedRestApi.save_label,
          savingLabel: AdvancedRestApi.saving_label,
        }
      },
      methods: {
        addNewQueryParameter: function (event , key, defaultQueryParameter) {
          var field = defaultQueryParameter || {};
  
          field.key = this.generateThreeLetterString();
          field.keyDisplay = `{${field.key}}`;
          field.collapsed = false;
  
          if( !this.settings[key]) {
            this.$set(this.settings, key, []);
          }
  
          this.settings[key].push(field);
        },
        generateThreeLetterString: function() {
          const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
          let result = "";
          for (let i = 0; i < 3; i++) {
              const randomIndex = Math.floor(Math.random() * letters.length);
              result += letters[randomIndex];
          }
          return result;
        },
        copyKey: async function (key) {
          try {
            // Try modern Clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
              await navigator.clipboard.writeText(`{${key}}`);
            } else {
              // Fallback for older browsers
              const textArea = document.createElement("textarea");
              textArea.value = `{${key}}`;
              textArea.style.position = "fixed";
              textArea.style.left = "-999999px";
              document.body.appendChild(textArea);
              textArea.focus();
              textArea.select();
              console.log(textArea);
              
              try {
                document.execCommand('copy');
              } catch (err) {
                throw new Error('Failed to copy using execCommand');
              } finally {
                textArea.remove();
              }
            }
        
            this.$CXNotice.add({
              message: `Key ${key} copied`,
              type: "success",
              duration: 7000,
            });
          } catch (error) {
            console.error('Copy failed:', error);
            this.$CXNotice.add({
              message: 'Failed to copy key - ' + error.message,
              type: "error",
              duration: 7000,
            });
          }
        },
        deleteRepeaterField: function (index, key) {
          this.settings[key].splice(index, 1);
        },
        isDisabled: function() {
          return this.isBusy;
        },
        saveAdvancedRestAPI: function() {
          var self = this;
          self.saving = true;
  
          jQuery
            .ajax({
              url: window.ajaxurl,
              type: "POST",
              dataType: "json",
              data: {
                action: "crcoblock_save_advanced_rest_api",
                nonce: self.nonce,
                item: self.settings,
              },
            }).done(function (response) {
              if (!response.success) {
                self.$CXNotice.add({
                  message: response.data.message,
                  type: "error",
                  duration: 15000,
                });
                self.saving = false;
              } else {
                self.$CXNotice.add({
                  message: 'Saved',
                  type: "success",
                  duration: 7000,
                });
                self.saving = false;
              }
            }).fail(function (jqXHR, textStatus, errorThrown) {
              self.$CXNotice.add({
                message: errorThrown,
                type: "error",
                duration: 15000,
              });
              self.saving = false;
            });
        },
        setRepeaterFieldProp: function (parentKey, index, key, value) {
          var field = this.settings[parentKey][index];
  
          field[key] = value;
  
          this.settings[parentKey].splice(index, 1, field);
        },
        isCollapsed: function (field) {
          if (undefined === field.collapsed || true === field.collapsed) {
            return true;
          } else {
            return false;
          }
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
        this.settings.isSingle = Boolean(this.settings.isSingle);
        this.settings.isPOST = Boolean(this.settings.isPOST);
        this.settings.query_parameters.forEach((query_param, index) => {
          query_param.keyDisplay = `{${query_param.key}}`;
          query_param.debugShortcode = query_param.debugShortcode === true || query_param.debugShortcode === "true";
        }); 
      }
    });
  })(jQuery, CrocoBlockAdvancedRestApiSettings);
  