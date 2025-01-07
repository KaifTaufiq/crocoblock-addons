(function ($, AddonSettings) {
  "use strict";
  Vue.component("crocoblock-addons-settings", {
    template: "#crocoblock-addons-settings",
    data: function () {
      return {
        addons: AddonSettings.addons,
        modules: AddonSettings.modules,
        callbacks: AddonSettings.callbacks,
        activeModules: AddonSettings.active_addons,
        moduleDetails: false,
        saving: false,
        result: false,
        // errorMessage: "",
        // successMessage: "",
      };
    },
    methods: {
      isActive: function (addon) {
        return 0 <= this.activeModules.indexOf(addon);
      },
      saveModules: function () {
        var self = this;
        self.saving = true;
        jQuery
          .ajax({
            url: window.ajaxurl,
            type: "POST",
            dataType: "json",
            data: {
              action: "crocoblock_addons_save_addons",
              addons: self.activeModules,
              _nonce: AddonSettings._nonce,
            },
          })
          .done(function (response) {
            self.saving = false;
            if (response.success) {
              self.result = "success";

              if (!response.data.reload) {
                self.successMessage = AddonSettings.messages.saved;
              } else {
                self.successMessage = AddonSettings.messages.saved_and_reload;

                setTimeout(function () {
                  window.location.reload();
                }, 4000);
              }
            } else {
              self.result = "error";
              self.errorMessage = "Error!";

              if (response.data && response.data.message) {
                self.errorMessage += " " + response.data.message;
              }
            }
            self.hideNotice();
          })
          .fail(function (e, textStatus) {});
      },
      switchActive: function (event, module) {
        if (this.isActive(module.value)) {
          var index = this.activeModules.indexOf(module.value);
          this.activeModules.splice(index, 1);
        } else {
          this.activeModules.push(module.value);
        }
      },
      hideNotice: function () {
        var self = this;
        setTimeout(function () {
          self.result = false;
          self.errorMessage = "";
        }, 8000);
      },
    },
  });
})(jQuery, CrocoClockAddonsSettings);
