var JqueryCrudManager = CrudManager.extend({
    executeAddWithUI: function (url, callback) {
        var self = this;
        var dict = this.extractFromModal(this.addModal, 'add');
        var params = this.extractToBackend(dict, this.addModal);

        $.ajax({
            url: url
            , type: 'POST'
            , data: params
            , success: function(res) {
                var $row = self.genBodyRow(
                    self,
                    self.getNextRowId(),
                    self.convertToInternal(dict)
                );
                self.panelBody.append($row);

                self.editingCallback();
                if (callback) callback(res, $row);
            }
            , error: self.onBackendError.bind(self, 'add')
        });
    },
    executeUpdateWithUI: function (url, $row, callback) {
        var self = this;
        var dict = this.extractFromModal(this.editModal, 'edit');
        var params = this.extractToBackend(dict, this.editModal);

        $.ajax({
            url: url +'' + dict['id']
            , type: 'PUT'
            , data: params
            , success: function(res) {
                var data = self.convertToInternal(dict);
                var $updatedRow = self.genBodyRow(self, parseInt($row.attr('data-id')), data);
                replaceDom($updatedRow, $row);

                self.editingCallback();
                if (callback) callback(res, $updatedRow);
            }
            , error: self.onBackendError.bind(self, 'update')
        });
    },
    executeRemoveWithUI: function (url, $row, callback) {
        var self = this;
        var id = $row.find('.id').text().trim();

        $.ajax({
            url: url +'' + id
            , type: 'DELETE'
            , success: function(res) {
                self.editingCallback();
                if (callback) callback(res, $row);

                $row.remove();
            }
            , error: self.onBackendError.bind(self, 'remove')
        });
    }
});