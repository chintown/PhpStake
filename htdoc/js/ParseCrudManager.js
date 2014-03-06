var ParseCrudManager = CrudManager.extend({
    username: null,
    init: function(config) {
        this._super(config);
        this.username = config.username;
    },
    executeAddWithUI: function (parseClass) {
        var parseInstance = new parseClass();
        var self = this;
        var dict = this.exportFromModal(this.addModal, 'add');
        delete dict['id']; // new record has empty id

        $.each(dict, function(key, value) {
            parseInstance.set(key, value);
        });

        parseInstance.save().then(
            function(parseInstance) {
                dict = $.extend({id: parseInstance.id}, dict); // this is a trick for make guarantee the order of key
                self.panelBody.append(self.genBodyRow(
                    self,
                    self.getNextRowId(),
                    self.convertToInternal(dict)
                ));

                self.editingCallback();
            },
            function(error) {
                ajaxMsgError('Failed to create new object, with error code: ' + JSON.stringify(error));
            }
        );
    },
    executeUpdateWithUI: function (parseClass, $row) {
        var parseInstance = new parseClass();
        var self = this;
        var dict = this.exportFromModal(this.editModal, 'edit');
        dict['objectId'] = dict['id'];
        delete dict['id'];

        parseInstance.id = dict['objectId'];
        $.each(dict, function(key, value) {
            parseInstance.set(key, value);
        });

        parseInstance.save().then(function(parseInstance) {
            var data = self.convertToInternal(dict);
            var $updatedRow = self.genBodyRow(self, parseInt($row.attr('data-id')), data);
            replaceDom($updatedRow, $row);

            self.editingCallback();
        }, function(error) {
            ajaxMsgError('Failed to update project, '+dict['objectId']+', with error code: ' + JSON.stringify(error));
        });
    },
    executeRemoveWithUI: function (parseClass, $row) {
        var parseInstance = new parseClass();
        var self = this;
        var id = $row.find('.id').text().trim();

        parseInstance.id = id;

        parseInstance.destroy().then(function(parseInstance) {
            self.editingCallback();

            $row.remove();
        }, function(parseInstance, error) {
            ajaxMsgError('Failed to remove project, '+id+', with error code: ' + JSON.stringify(error));
        });
    }
});