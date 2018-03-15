<?php namespace Aike\Web\Stock;

use Aike\Web\Index\BaseModel;

class Stock extends BaseModel
{
    protected $table = 'stock';

    public function lines()
    {
        return $this->hasMany('Aike\Web\Stock\StockLine');
    }

    public function type()
    {
        return $this->belongsTo('Aike\Web\Stock\StockType');
    }

    /**
     * 检查库存仓库数据
     */
    public static function checkStock($row)
    {
        // 出库类型不检查数量
        if ($row->type->type == 2) {
            return;
        }

        $lines = StockLine::with(['warehouse', 'product'])
        ->where('stock_id', $row->id)
        ->get();

        $productIds = $lines->pluck('product_id');

        $store_id = auth()->user()->store_id;
        $res = StockWarehouse::leftJoin('warehouse', 'warehouse.id', '=', 'stock_warehouse.warehouse_id')
        ->where('warehouse.store_id', $store_id)
        ->whereIn('stock_warehouse.product_id', $productIds)
        ->get(['stock_warehouse.*']);

        $warehouses = [];
        foreach ($res as $v) {
            $warehouses[$v->warehouse_id][$v->product_id] = $v;
        }

        foreach ($lines as $line) {
            $warehouse_id = $line->warehouse_id;
            $product_id   = $line->product_id;

            $warehouse = $warehouses[$warehouse_id][$product_id];
            if ($warehouse['stock_quantity'] < $line->quantity) {
                abort_error($line->warehouse->name.'中的商品['.$line->product->name.']的库存不足。');
            }
        }
    }

    /**
     * 重建库存仓库数据
     */
    public static function rebuildStock($row)
    {
        $store_id = auth()->user()->store_id;
        // 查询库存历史记录
        $res = StockLine::leftJoin('stock', 'stock.id', '=', 'stock_line.stock_id')
        ->leftJoin('stock_type', 'stock_type.id', '=', 'stock.type_id')
        ->groupBy('stock_type.type', 'stock_line.warehouse_id', 'stock_line.product_id')
        ->selectRaw('stock_line.product_id,stock_line.warehouse_id,stock_type.type,sum(stock_line.quantity) as quantity, sum(stock_line.cost_money) as cost_money')
        ->where('stock.store_id', $store_id)
        ->where('stock.status', 1)
        ->get();

        // 计算出入总金额和数量
        $costs = [];
        foreach ($res as $v) {
            $warehouse_id = $v->warehouse_id;
            $product_id   = $v->product_id;
            if ($v->type == 1) {
                $costs[$warehouse_id][$product_id]['quantity'] += $v->quantity;
                $costs[$warehouse_id][$product_id]['money'] += $v->cost_money;
            } else {
                $costs[$warehouse_id][$product_id]['quantity'] -= $v->quantity;
                $costs[$warehouse_id][$product_id]['money'] -= $v->cost_money;
            }
        }

        // 更新存货成本表
        $lines = StockLine::where('stock_id', $row->id)->get();

        foreach ($lines as $line) {
            $warehouse_id = $line['warehouse_id'];
            $product_id   = $line['product_id'];

            $row = StockWarehouse::firstOrNew([
                'warehouse_id' => $warehouse_id,
                'product_id'   => $product_id
            ]);
            $row->last_price       = $line['cost_price'];
            $row->stock_cost       = $costs[$warehouse_id][$product_id]['money'] / $costs[$warehouse_id][$product_id]['quantity'];
            $row->stock_money      = $costs[$warehouse_id][$product_id]['money'];
            $row->stock_quantity   = $costs[$warehouse_id][$product_id]['quantity'];
            $row->virtual_quantity = $row->stock_quantity;
            $row->save();
        }
    }
}
