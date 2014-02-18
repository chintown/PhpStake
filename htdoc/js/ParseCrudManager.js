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

        parseInstance.save(null, {
            success: function(parseInstance) {
                dict = $.extend({id: parseInstance.objectId}, dict); // this is a trick for make guarantee the order of key
                self.panelBody.append(self.genBodyRow(
                    self,
                    self.getNextRowId(),
                    self.convertToInternal(dict)
                ));

                self.editingCallback();
            },
            error: function(parseInstance, error) {
                ajaxMsgError('Failed to create new object, with error code: ' + error.description);
            }
        });
    },
    executeUpdateWithUI: function (parseClass, $row) {
        var self = this;
        var dict = this.exportModal(this.editModal, 'edit');
        dict['objectId'] = dict['id'];
        delete dict['id'];

        var queryInstance = new Parse.Query(parseClass);
        queryInstance.equalTo("objectId", dict['objectId']);
        queryInstance.first({
            success: function(matchedInstance) {
                $.each(dict, function(key, value) {
                    matchedInstance.set(key, value);
                });
                matchedInstance.save(null, {
                    success: function(parseInstance) {
                        var data = self.convertToInternal(dict);
                        var $updatedRow = self.genBodyRow(self, parseInt($row.attr('data-id')), data);
                        replaceDom($updatedRow, $row);

                        self.editingCallback();
                    },
                    error: function(parseInstance, error) {
                        ajaxMsgError('Failed to update project, '+dict['objectId']+', with error code: ' + error.description);
                    }
                });
            },
            error: function(matchedInstance, error) {
                ajaxMsgError('Failed to find project, '+dict['objectId']+', with error code: ' + error.description);
            }
        });
    },
    executeRemoveWithUI: function (parseClass, $row) {
        var self = this;
        var id = $row.find('.id').text().trim();

        var queryInstance = new Parse.Query(parseClass);
        queryInstance.equalTo("objectId", id);
        queryInstance.first({
            success: function(matchedInstance) {
                matchedInstance.destroy({
                    success: function(parseInstance) {
                        self.editingCallback();

                        $row.remove();
                    },
                    error: function(parseInstance, error) {
                        ajaxMsgError('Failed to remove project, '+id+', with error code: ' + error.description);
                    }
                });
            },
            error: function(matchedInstance, error) {
                ajaxMsgError('Failed to find project, '+dict['objectId']+', with error code: ' + error.description);
            }
        });
    }
});