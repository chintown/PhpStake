function createParseAclUmask022() {
    var acl = new Parse.ACL(Parse.User.current());
    acl.setPublicReadAccess(true);
    return acl;
}
function createParseAclUmask000() {
    var acl = new Parse.ACL();
    acl.setPublicReadAccess(true);
    acl.setPublicWriteAccess(true);
    return acl;
}

var ParseCrudManager = CrudManager.extend({
    objectId: null,
    aclCreator: createParseAclUmask022,
    init: function(config) {
        this._super(config);
        this.objectId = config.objectId;
    },
    executeAddWithUI: function (parseClass) {
        var parseInstance = new parseClass();
        var self = this;
        var dict = this.extractFromModal(this.addModal, 'add');
        var params = this.extractToBackend(dict, this.addModal);

        $.each(params, function(key, value) {
            parseInstance.set(key, value);
        });

        parseInstance.setACL(this.aclCreator());

        parseInstance.save().then(
            function(parseInstance) {
                dict = $.extend(dict, {id: parseInstance.id}); // this is a trick for make guarantee the order of key
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
        var dict = this.extractFromModal(this.editModal, 'edit');
        var params = this.extractToBackend(dict, this.editModal);

        parseInstance.id = dict['id'];
        $.each(params, function(key, value) {
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