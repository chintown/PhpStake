var ParseCrudManager = CrudManager.extend({
    username: null,
    init: function(config) {
        this._super(config);
        this.username = config.username;
    },
    executeAddWithUI: function (parseClass) {
        var parseInstance = new parseClass();
        var self = this;
        var dict = this.exportModal(this.addModal, 'add');
        delete dict['id']; // new record has empty id

        $.each(dict, function(key, value) {
            parseInstance.set(key, value);
        });

        parseInstance.save().then(
            function(parseInstance) {
                dict = $.extend({id: parseInstance.objectId}, dict); // this is a trick for make guarantee the order of key
                self.panelBody.append(self.genBodyRow(
                    self,
                    self.getNextRowId(),
                    self.convertToInternal(dict)
                ));

                self.editingCallback();
            },
            function(error) {
                ajaxMsgError('Failed to create new object, with error code: ' + error.description);
            }
        );
    },
    executeUpdateWithUI: function (parseClass, $row) {
        var self = this;
        var dict = this.exportModal(this.editModal, 'edit');
        dict['objectId'] = dict['id'];
        delete dict['id'];

        var queryInstance = new Parse.Query(parseClass);
        queryInstance.equalTo("objectId", dict['objectId']);
        queryInstance.first().then(function(matchedInstance) {
            $.each(dict, function(key, value) {
                matchedInstance.set(key, value);
            });
            return matchedInstance.save();
        }, function(error) {
            ajaxMsgError('Failed to update project, '+dict['objectId']+', with error code: ' + error.description);
        }).then(function(parseInstance) {
            var data = self.convertToInternal(dict);
            var $updatedRow = self.genBodyRow(self, parseInt($row.attr('data-id')), data);
            replaceDom($updatedRow, $row);

            self.editingCallback();
        });
    },
    executeRemoveWithUI: function (parseClass, $row) {
        var self = this;
        var id = $row.find('.id').text().trim();

        var queryInstance = new Parse.Query(parseClass);
        queryInstance.equalTo("objectId", id);
        queryInstance.first().then(function(matchedInstance) {
            matchedInstance.destroy();
        }, function(error) {
            ajaxMsgError('Failed to find project, '+dict['objectId']+', with error code: ' + error.description);
        }).then(function(parseInstance) {
            self.editingCallback();

            $row.remove();
        }, function(parseInstance, error) {
            ajaxMsgError('Failed to remove project, '+id+', with error code: ' + error.description);
        });
    }
});