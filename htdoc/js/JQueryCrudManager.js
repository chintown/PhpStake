var JqueryCrudManager = CrudManager.extend({
    executeAddWithUI: function (url) {
        var self = this;
        var dict = this.extractFromModal(this.addModal, 'add');
        var params = this.extractToBackend(dict);

        $.ajax({
            url: url
            , type: 'POST'
            , data: params
            , success: function(res) {
                self.panelBody.append(self.genBodyRow(
                    self,
                    self.getNextRowId(),
                    self.convertToInternal(dict)
                ));

                self.editingCallback();
            }
            , error: self.onBackendError.bind(self, 'add')
        });
    },
    executeUpdateWithUI: function (url, $row) {
        var self = this;
        var dict = this.extractFromModal(this.editModal, 'edit');
        var params = this.extractToBackend(dict);

        $.ajax({
            url: url +'' + dict['id']
            , type: 'PUT'
            , data: params
            , success: function(res) {
                var data = self.convertToInternal(dict);
                var $updatedRow = self.genBodyRow(self, parseInt($row.attr('data-id')), data);
                replaceDom($updatedRow, $row);

                self.editingCallback();
            }
            , error: self.onBackendError.bind(self, 'update')
        });
    },
    executeRemoveWithUI: function (url, $row) {
        var self = this;
        var id = $row.find('.id').text().trim();

        $.ajax({
            url: url +'' + id
            , type: 'DELETE'
            , success: function(res) {
                self.editingCallback();

                $row.remove();
            }
            , error: self.onBackendError.bind(self, 'remove')
        });
    }
});