<script type="text/javascript">

var delivery_time = {{(int)$order['delivery_time']}};

function reloadStore() {
    store.reload();
}
function saveStore() {
    var data = {order_id:{{(int)$order['id']}},updated:[],deleted:[]};
    
    var updated = store.getUpdatedRecords();
    
    Ext.each(updated, function(record) {
        data.updated.push(record.data);
        record.commit();
    });
    var deleted = store.getRemovedRecords();
    Ext.each(deleted, function(record) {
        data.deleted.push(record.data);
    });

    data.updated = JSON.stringify(data.updated);
    data.deleted = JSON.stringify(data.deleted);

    Ext.Ajax.request({
        url: '{{url("product_edit")}}',
        params: data,
        method:'POST',
        // 默认30秒
        timeout: 2000,
        success: function(response) {
            var res = Ext.decode(response.responseText);
            if (res.status) {
                reloadStore();
                $.toastr('success', '保存编辑成功。', '保存编辑');
            } else {
                Ext.MessageBox.alert('错误提醒', '保存编辑失败!');
                store.rejectChanges();
            }
        }
    });
}

function removeStore() {
    var data = grid.getSelectionModel().getSelection();
    Ext.MessageBox.confirm('删除产品', '确定产品删除?', function(btn) {
        if (btn == 'yes') {
            Ext.Array.each(data, function(item) {
                store.remove(item);
            });
            saveStore();
        }
    });
}

var store, grid;

Ext.onReady(function() {

    var columns = [
    	new Ext.grid.RowNumberer(),
    {
        text: '编号',
        dataIndex: 'id',
        dataType: 'int',
        hidden: true,
    },{
        text: '订单类型',
        dataIndex: 'type',
        dataType: 'string',
        align: 'center',
        width: 90
    },{
        text: '产品类别',
        dataIndex: 'category_name',
        dataType: 'string',
        width: 100
    },{
        text: '产品名称',
        dataIndex: 'name',
        dataType: 'string',
        width: 160
    }, {
        text: '产品规格',
        dataIndex: 'spec',
        dataType: 'string',
        width: 90
    }, {
        text: '产品条码',
        dataIndex: 'barcode',
        dataType: 'string',
        width: 120
    }, {
        text: '产品单位',
        dataIndex: 'unit',
        dataType: 'string',
        align: 'center',
        width: 90
    }, {
        text: '订单基点',
        dataIndex: 'level_amount',
        dataType: 'string',
        align: 'right',
        width: 90
    }, {
        text : '产品单价',
        dataIndex : 'price',
        dataType: 'string',
        align: 'right',
        width: 90
    },{
        text: '订单数量',
        dataIndex: 'amount',
        dataType: 'float',
        align: 'right',
        width: 90,
        summaryType: 'sum'
    },{
        text: '历史月量1.5',
        dataIndex: 'history_number',
        dataType: 'int',
        align: 'right',
        width: 110,
        renderer: function(value, metaData, record) {
            // 历史月销小于1.5
            if ((record.data.history_number * 3) < record.data.amount) {
                return '<span style="color:red;font-weight:bold;">' +value+ '</span>';
            } else {
                return value;
            }
        },
        summaryType: 'sum'
    },{
        text: '订单金额',
        dataIndex: 'money',
        dataType: 'float',
        align: 'right',
        width: 90,
        summaryType: 'sum'
    }, {
        text: '运费金额',
        dataIndex: 'freight_money',
        dataType: 'float',
        align: 'right',
        width: 90,
        summaryType: 'sum'
    }, {
        text: '订单重量(t)',
        dataIndex: 'weight',
        dataType: 'float',
        align:'right',
        width: 105,
        summaryType: 'sum'
    }, {
        text: '实发数量',
        dataIndex: 'fact_amount',
        dataType: 'float',
        align: 'right',
        width: 90,
        renderer: function(value, metaData, record) {
            var val = Ext.util.Format.number(value, '0.00');
            // 实发数量为0红色显示
            if (delivery_time > 0 && value == 0) {
                return '<span style="color:red;font-weight:bold;">' +val+ '</span>';
            } else {
                return val;
            }
        },
        summaryType: 'sum'
    }, {
        text: '实发金额',
        dataIndex: 'fact_money',
        dataType: 'float',
        align: 'right',
        width: 90,
        summaryType: 'sum'
    },{
        text: '实发重量(t)',
        dataIndex: 'fact_weight',
        dataType: 'float',
        align: 'right',
        width: 105,
        summaryType: 'sum'
    },{
        text: '差异数量',
        dataIndex: 'diff_amount',
        dataType: 'float',
        align: 'right',
        width: 90,
        summaryType: 'sum'
    },{
        text: '支持金额',
        dataIndex: 'remark_money',
        dataType: 'float',
        align: 'right',
        width: 90,
        summaryType: 'sum'
    },{
        text: '客户库存',
        dataIndex: 'inventory',
        dataType: 'int',
        align: 'right',
        width: 90,
        summaryType: 'sum'
    },{
        text: '生产批号',
        dataIndex: 'batch_number',
        dataType: 'string',
        width: 90
    },{
        text: '备注',
        dataIndex : 'content',
        dataType: 'string',
        width: 85
    },{
        text: '合同',
        dataIndex: 'contract',
        dataType: 'string',
        align: 'center',
        width: 60
    }];

    var editor = {{json_encode($flow['fields'])}};
    var fields = [];
    Ext.Array.each(columns, function(item, index) {

        if(item.dataType == 'int') {
            item['renderer'] = item.renderer ? item.renderer : Ext.util.Format.numberRenderer('0');
        }
        if(item.dataType == 'float') {
            item['renderer'] = item.renderer ? item.renderer : Ext.util.Format.numberRenderer('0.00');
        }

        // 编辑器
        Ext.Array.each(editor.edit, function(field) {

            if(item.dataIndex == field) {
                item['editor'] = {
                    //allowBlank: false
                }
            }
        });

        // 隐藏
        Ext.Array.each(editor.hidden, function(field) {
            if(item.dataIndex == field) {
                item['hidden'] = true;
            }
        });

        // 合计计算格式
        if(item.summaryType) {

            if(item.dataType == 'int') {
                item['summaryRenderer'] = Ext.util.Format.numberRenderer('0')
            }
            if(item.dataType == 'float') {
                item['summaryRenderer'] = Ext.util.Format.numberRenderer('0.00')
            }
        }

        // 生成模型字段
        fields.push({
            name: item.dataIndex,
            type: item.dataType
        });

        columns[index] = item;
    });

    Ext.define('Product', {
        extend: 'Ext.data.Model',
        fields: fields,
    });

    store = new Ext.data.Store({
        model: 'Product',
        proxy: {
            type: 'ajax',
            url: DATA_URL,
            reader: {
                type : 'json',
                root : 'rows'
            }
        },
        listeners: {},
        autoLoad: false
    });

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });

    grid = Ext.create('Ext.grid.Panel', {
        //frame: true,
        height: 320,
        width: '100%',
        border: true,
        style:'background-color:#fff;',
        store : store,
        renderTo: 'dd',
        plugins: [cellEditing],
        loadMask: {
            msg: '正在加载数据，请稍侯...'
        },
        columnLines : true,
        features: [{
            ftype: 'summary',
            dock: 'bottom'
        }],
        columns: {
            defaults: {
                menuDisabled: true,
                //sortable: false,
            }, items: columns
        }
        //tbar: tbar
    });
    store.load();
});
</script>

<div id="dd"></div>