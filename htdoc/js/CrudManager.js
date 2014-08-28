/*
    here's function lifecycle
    [displaying div table]
     \ 1. extractFromDisplay
     \ 2. renderEditModal
    [edit/add modal]
     | 3. extractFromModal  4. extractToBackend
    [executeUpdateWithUI] ----------------------[backend]
     | 4. convertToInternals
    [genBodyRows]
*/
var CrudManager = Class.extend({
    panel: null,
    panelBody: null,
    addModal: null,
    editModal: null,
    editingCallback: function () {},
    template_id: '',
    getNextRowId: function () {
        return this.panelBody.find('.crud-row').length - 1;
    },
    orderCriteria: {},

    init: function (config) {
        this.panel = $(config.containerSelector).addClass('crud-table');
        this.panel.css('position', 'relative'); // avoid leak of absolute children
        this.editingCallback = config.editingCallback || this.editingCallback;
        this.template_id = config.template_id || '';

        this.orderCriteria = this.decodeOrderCriteria(getLinkParam('order'));
    },
    render: function () {
        this.panel.append(this.genHead());
        this.panel.append(this.genBody());
    },
    renderAddModal: function (containerSelector, callback) {
        this.addModal = $(containerSelector);
        this.addModal.html('').append(this.genAdd());
        if (callback) {
            callback();
        }
    },
    renderEditModal: function (containerSelector, $row, callback) {
        this.editModal = $(containerSelector);
        var data = this.convertToInternal(this.extractFromDisplay($row));
        this.editModal.html('').append(this.genEdit(data));
        if (callback) {
            callback();
        }
    },
    executeAddWithUI: function (dpd) {
        var self = this;
        var dict = this.extractFromModal(this.addModal, 'add');
        var params = this.extractToBackend(dict);
        dpd.post(params, function (r) {
            self.editingCallback();

            try {
                r = eval('(' + r +')');
            } catch (e) {
                de.assert(false, "dpd post failed. ", r);
                // TODO show err msg
                return;
            }
            dict = $.extend(dict, {id: r['$id']}); // this is a trick for make guarantee the order of key
            var idx = self.panelBody.find('.crud-row').length - 1;
            var data = self.convertToInternal(dict);
            ////de.log(data, 'genBodyRow');
            self.panelBody.append(self.genBodyRow(self, idx, data));
        });
    },
    executeUpdateWithUI: function (dpd, $row) {
        var self = this;
        var dict = this.extractFromModal(this.editModal, 'edit');
        var params = this.extractToBackend(dict);
        dpd.put(dict['id'], params, function (r) {
            self.editingCallback();

            try {
                r = eval('(' + r +')');
            } catch (e) {
                de.assert(false, "dpd post failed. ", r);
                // TODO show err msg
                return;
            }

            var data = self.convertToInternal(dict);
            var $updatedRow = self.genBodyRow(self, parseInt($row.attr('data-id')), data);
            replaceDom($updatedRow, $row);
        });
    },
    executeRemoveWithUI: function (dpd, $row) {
        var self = this;
        var id = $row.find('.id').text().trim();
        dpd.del(id, function (r) {
            self.editingCallback();

            $row.remove();
        });
    },
    // -------------------------------------------------------------------------
    genDefaultRow: function () {
        return $('<div>');
    },
    genDefaultColumn: function (info) {
        return $('<div>').attr('name', info.k).addClass(info.k).text(info.v);
    },
    genDefaultColumns: function (data) {
        var self = this;
        return data.map(function (info) {
            return self.genDefaultColumn(info);
        });
    },
    genDefaultInputColumns: function (data) {
        return data.map(function (info) {
            var $label = $('<span>').addClass('k').text(info.kDisp);
            var $input = $('<input>').addClass('v').attr('name', info.k);
            var $wrap = $('<div>').addClass(info.k);
            if (info.v) {
                $input.val(info.v);
            }
            return $wrap.append($label).append($input);
        });
    },
    genGivenColumn: function ($templ, info) {
        var $more_fine_templ = (this.template_id !== '') ? $templ.filter('.' + info.k+'.'+this.template_id) : [];
        var $fine_templ = $templ.filter('.' + info.k);
        if ($more_fine_templ.length > 0) {
            return $more_fine_templ.tmpl(info);
        } else if ($fine_templ.length > 0) {
            try {
                return $fine_templ.tmpl(info);
            } catch (e) {
                console.error('can not render template with given data. please check whether there is an exception happened in js templateHelper.', $fine_templ.get(0), info);
            }
        } else {
            return $templ.tmpl(info);
        }
    },
    genGivenColumns: function (selector, data) {
        var self = this;
        var $templ = $(selector);
        if ($templ.length === 0) {
            return false;
        }
        return data.map(function (info) {
            return self.genGivenColumn($templ, info);
        });
    },
    // -------------------------------------------------------------------------
    getHeads: function () {
        // data loader for head
        return [{k: 'title', kDisp: '標題'},
            {k: 'content',  kDisp:'內容'},
            {k: 'comment',  kDisp:'註解'}
        ];
    },
    getBodies: function () {
        // data loader for body
        return [
            [
                {k: 'title', v: 'title 1'},
                {k: 'content',  v:'content 1'},
                {k: 'comment',  v:'comment 1'}
            ],
            [
                {k: 'title', v: 'title 2'},
                {k: 'content',  v:'content 2'},
                {k: 'comment',  v:'<!-- comment 2 -->'}
            ]
        ]
    },
    getHeadKeys: function () {
        var headKeys = [];
        $.each(this.getHeads(), function(idx, head) {
            headKeys.push(head['k']);
        });
        return headKeys;
    },
    // -------------------------------------------------------------------------
    genHead: function () {
        var self = this; // make this accessible in closure
        var tmplHead = $('.crud_head_row'); // user-defined
        var $head = (tmplHead.length === 0)
            ? this.genDefaultRow() // default
            : tmplHead.tmpl({});
        $head.addClass('crud-head');

        var data = this.getHeads(); // [{k:, kDisp:}, {}, ...]
        var $columns = this.genGivenColumns('.crud_head_column', data); // user-defined
        $columns = ($columns === false) ? this.genDefaultColumns(data) : $columns; // default
        // template-generated result does not have addClass method
        $.each($columns, function (idx, column) {
            var $column = $(column);
            var columnName = data[idx].k;
            $column
                .addClass('crud-column')
                .on('click', self.toggleSort.bind(self, $column, columnName));
            var existedOrder = self.getOrderCriterion(columnName);
            if (existedOrder !== '') {
                $column.attr('data-order', existedOrder);
            }
        });

        $head.append($columns);
        return $head;
    },
    genBodyRow:function (self, idx, data) {
        var tmplRow = $('.crud_body_row'); // user-defined
        if (tmplRow.length > 1) { // always get latter one for overriding
            tmplRow = $(tmplRow.get(tmplRow.length - 1));
        }
        var $row = (tmplRow.length === 0)
            ? self.genDefaultRow() // default
            : tmplRow.tmpl({});
        $row.addClass('crud-row').attr('data-id', idx);

        var $columns = this.genGivenColumns('.crud_body_column', data); // user-defined
        $columns = ($columns === false) ? this.genDefaultColumns(data) : $columns; // default
        // template-generated result does not have addClass method
        $.each($columns, function (idx, column) {
            $(column).addClass('crud-column').attr('title', data[idx].v);
        });

        var $mostInner = $row.find(' *:not(:has("*"))'); // http://stackoverflow.com/questions/4250893/find-the-inner-most-text-with-javascript-jquery-regardless-of-number-of-nested
        if ($mostInner.length !== 0) {
            // multiple wrapper
            if ($row.find('.crud_body_column_placeholder')) {
                $row.find('.crud_body_column_placeholder').append($columns);
            } else {
                $($mostInner.get(0)).append($columns);
            }
        } else {
            // single wrapper
            $row.append($columns);
        }

        var $tmplControl = $('.crud_control');
        if ($tmplControl.length > 1) { // always get latter one for overriding
            $tmplControl = $($tmplControl.get($tmplControl.length - 1));
        }
        var $control = $tmplControl.tmpl({});
        $row.append($control);

        return $row;
    },
    genBody: function () {
        var self = this; // make this accessible in closure
        var tmplBody = $('.crud_body'); // user-defined
        var $body = (tmplBody.length === 0)
            ? this.genDefaultRow() // default
            : tmplBody.tmpl({});
        $body.addClass('crud-body');

        var dataSets = this.getBodies();
        $.each(dataSets, function (idx, data) {
            $body.append(self.genBodyRow(self, idx, data));
        });

        this.panelBody = $body;
        return $body;
    },
    // -------------------------------------------------------------------------
    genAdd: function () {
        // generate input list for adding
        var tmplAdd = $('.crud_add'); // user-defined
        var $add = (tmplAdd.length === 0)
            ? this.genDefaultRow() // default
            : tmplAdd.tmpl({});
        $add.addClass('crud-add table');

        var data = this.getHeads(); // [{k:, kDisp:}, {}, ...]

        var $columns = this.genGivenColumns('.crud_add_column', data); // user-defined
        $columns = ($columns === false) ? this.genDefaultInputColumns(data) : $columns; // default
        // template-generated result does not have addClass method
        $.each($columns, function (idx, column) {
            $(column).addClass('crud-modal-column pair');
        });

        $add.append($columns);
        return $add;
    },
    // -------------------------------------------------------------------------
    genEdit: function (data) {
        // generate input list for editing
        var tmplEdit = $('.crud_edit'); // user-defined
        var $edit = (tmplEdit.length === 0)
            ? this.genDefaultRow() // default
            : tmplEdit.tmpl({});
        $edit.addClass('crud-edit table');

        var heads = this.getHeads(); // [{k:, kDisp:}, {}, ...]
        $.each(data, function (i, row) {
            row = $.extend(row, heads[i]);
        });

        var $columns = this.genGivenColumns('.crud_edit_column', data); // user-defined
        $columns = ($columns === false) ? this.genDefaultInputColumns(data) : $columns; // default
        // template-generated result does not have addClass method
        $.each($columns, function (idx, column) {
            $(column).addClass('crud-modal-column pair');
        });

        $edit.append($columns);
        return $edit;
    },
    // -------------------------------------------------------------------------
    getDisplayColumnByName: function ($row, name) {
        var $field = $row.find('.crud-column[name="'+name+'"]');
        if ($field.length === 0) {
            console.error('can not get column, '+name+', from $row', $row);
            return false;
        }
        return $field;
    },
    getModalColumnByClass: function ($row, clazz) {
        var $field = $row.find('.crud-modal-column.'+clazz+'');
        if ($field.length === 0) {
            console.error('can not get column, '+clazz+', from $row', $row);
            return false;
        }
        return $field;
    },
    extractFromDisplay: function ($row) {
        /* <div name="content" class="content crud-column">a</div>
            -> {content: "a", ...}
         */
        var dict = {};
        $row.find('.crud-column').each(function (idx, column) {
            var $column = $(column);
            var k = $column.attr('name');
            de.assert(k, 'can not extract name attribute from  ... must set it while creating', column);
            dict[k] = $column.text(); // do i need to trim this?
        });
        return dict;
    },
    extractFromModal: function (modal, source) {
        var data = {};
        if (!modal) {
            console.warn('undefined modal object found while extractFromModal');
            return data;
        }

        modal.find('.crud-modal-column').each(function (idx, column) {
            var $column = $(column);
            var $input = $column.find('input');
            if ($input.length === 0) {
                $input = $column.find('textarea');
            }
            de.assert($input.length !== 0, 'can not find input/textarea from column ... must set it while editing', column);
            var k = $input.attr('name');
            de.assert(k, 'input/textarea must be bound with name attribute', $input);
            data[k] = $input.val(); // do i need to trim this?
        });
        return data;
    },
    extractToBackend: function (dict) {
        var params =  $.extend({}, dict);
        delete params['id'];
        return params;
    },
    convertToInternal: function (dict) {
        /* {x:1,y:1}
         -> [{k:x,v:1}, {k:y,v:1}]
            NOTE that 1) keys does not existed in HEAD will be ignored!
                      2) missing value of existing key will be set as default
         */
        var info = [], head = this.getHeadKeys(), self = this;
        $.each(head, function (idx, field) {
            if (field in dict) {
                info.push({k: field, v: dict[field]});
            } else {
                info.push({k: field, v: ''});
            }
        });
        return info;
    },
    convertToInternals: function (dicts) {
        /* [{x:1,y:1}]
            -> [{k:x,v:1}, {k:y,v:1}]
         */
        var data = [];
        $.each(dicts, function (idx, dict) {
            $.each(dict, function (k, v) {
                data.push({k: k, v: v});
            });
        });
        return data;
    },
    // -------------------------------------------------------------------------
    decodeOrderCriteria: function (paramValue) {
        // +field1,-field2
        var orderCriteria = {};
        if (!paramValue) {
            return orderCriteria;
        }
        var parts = decodeURIComponent(paramValue).split(',');
        parts.forEach(function(part, idx) {
            orderCriteria['_'+part.substr(1)] = part.substr(0, 1); // prefix to avoid pollution
        });
        //console.log(orderCriteria);
        return orderCriteria;
    },
    encodeOrderCriteria: function () {
        var parts = [];
        for (var k in this.orderCriteria) {
            parts.push(this.orderCriteria[k]+ k.substr(1));
        }
        return parts.join(',');
    },
    getOrderCriterion: function (columnName) {
        if ('_'+columnName in this.orderCriteria) {
            return this.orderCriteria['_'+columnName];
        } else {
            return '';
        }
    },
    setOrderCriterion: function (columnName, orderCriteria) {
        this.orderCriteria['_'+columnName] = orderCriteria;
    },
    toggleSort: function ($column, columnName) {
        var orderSymbol = this.getOrderCriterion(columnName);
        orderSymbol = (orderSymbol === '') ? '+' : orderSymbol;
        orderSymbol = (orderSymbol === '-') ? '+' : '-';
        this.setOrderCriterion(columnName, orderSymbol);
        //$column.attr('data-order', orderSymbol);
        window.location.href = updateLinkParams({order: this.encodeOrderCriteria()})
    }
 });
