(function ($, MigrationSettings) {
  "use strict";
  Vue.component("crocoblock-addons-migration", {
    template: "#crocoblock-addons-migration",
    data: function () {
        return {
            settings: {},
            nonce: MigrationSettings._nonce,
            title: MigrationSettings.title,
            description: MigrationSettings.description,
            saveLabel: MigrationSettings.save_label,
            savingLabel: MigrationSettings.saving_label,
            saving: false,
            isBusy: false,
        };
    },
    methods: {
        migrateDatabase: function() {
            var self = this;
            self.saving = true;

            jQuery.ajax({
                url: window.ajaxurl,
                type: "POST",
                dataType: "json",
                data: {
                    action: "crocoblock_addons_migration",
                    nonce: self.nonce,
                },
            }).done(function(response) {
                if (!response.success) {
                    self.$CXNotice.add({
                      message: response.data.message,
                      type: "error",
                      duration: 15000,
                    });
                    
                    self.saving = false;
                  } else {
                    self.$CXNotice.add({
                      message: self.savingLabel + ' Reloading Page in 3 seconds',
                      type: "success",
                      duration: 7000,
                    });
                    self.saving = false;
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                  }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.$CXNotice.add({
                    message: errorThrown,
                    type: "error",
                    duration: 15000,
                  });
                  self.saving = false;
            })
            // timeout to show the saving label
            setTimeout(() => {
                self.saving = false;
            }, 3000);
        },
        isDisabled: function() {
            return this.isBusy;
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
    }
  });
})(jQuery, CrocoblockAddonsMigration);
