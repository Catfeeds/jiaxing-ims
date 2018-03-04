<?php namespace Aike\Web\Stock;

use Aike\Web\Index\BaseModel;

class Stock extends BaseModel
{
    protected $table = 'stock';

    public function lines()
    {
        return $this->hasMany('Aike\Web\Stock\StockLine');
    }

    /**
     * 入库
     */
    public static function incStock($lines)
    {
        // 查询库存历史记录
        $rows = StockLine::leftJoin('stock', 'stock.id', '=', 'stock_line.stock_id')
        ->leftJoin('stock_type', 'stock_type.id', '=', 'stock.type_id')
        ->groupBy('stock_type.type', 'stock_line.warehouse_id', 'stock_line.product_id')
        ->selectRaw('stock_line.product_id,stock_line.warehouse_id,stock_type.type,sum(stock_line.quantity) as quantity, sum(stock_line.cost_money) as cost_money')
        ->where('stock.status', 1)
        ->get();

        // 计算出入总金额和数量
        $costs = [];
        foreach ($rows as $row) {
            $warehouse_id = $row->warehouse_id;
            $product_id   = $row->product_id;
            if ($row->type == 1) {
                $costs[$warehouse_id][$product_id]['quantity'] += $row['quantity'];
                $costs[$warehouse_id][$product_id]['money'] += $row['cost_money'];
            } else {
                $costs[$warehouse_id][$product_id]['quantity'] -= $row['quantity'];
                $costs[$warehouse_id][$product_id]['money'] -= $row['cost_money'];
            }
        }

        // 更新存货成本表
        foreach ($lines as $line) {
            $warehouse_id = $line['warehouse_id'];
            $product_id   = $line['product_id'];

            $row = StockWarehouse::firstOrNew([
                'warehouse_id' => $warehouse_id,
                'product_id'   => $product_id
            ]);
            $row->last_price = $line['price'];
            $row->stock_cost = $costs[$warehouse_id][$product_id]['money'] / $costs[$warehouse_id][$product_id]['quantity'];
            $row->stock_quantity = $costs[$warehouse_id][$product_id]['quantity'];
            $row->virtual_quantity = $row->stock_quantity;
            $row->save();
        }
    }

    /**
     * 出库
     */
    public static function decStock($id)
    {
        $row = Stock::with('lines')->find($id);

        $productIds   = $row->lines->pluck('product_id', 'product_id');
        $warehouseIds = $row->lines->pluck('warehouse_id', 'warehouse_id');

        $warehouses = Warehouse::whereIn('id', $warehouseIds)->get()->keyBy('id');
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($row->lines as $line) {
            $warehouse_id = $line->warehouse_id;
            $product_id   = $line->product_id;

            $row = StockWarehouse::firstOrNew([
                'warehouse_id' => $warehouse_id,
                'product_id'   => $product_id
            ]);

            if ($row->stock_quantity < $line->quantity) {
                return '['.$warehouses[$warehouse_id]->name.']中的商品['.$products[$product_id]->name.']的库存不足。';
            }
            $row->stock_quantity = $row->stock_quantity - $line['quantity'];
            $row->virtual_quantity = $row->stock_quantity;
            $row->save();
        }
        return true;
    }

    /**
     * 返还库存
     */
    public static function returnStock($id)
    {
        $row = Stock::with('lines')->find($id);
        foreach ($row->lines as $line) {
            $warehouse_id = $line->warehouse_id;
            $product_id   = $line->product_id;

            $row = StockWarehouse::firstOrNew([
                'warehouse_id' => $warehouse_id,
                'product_id'   => $product_id
            ]);
            $row->stock_quantity = $row->stock_quantity + $line->quantity;
            $row->virtual_quantity = $row->stock_quantity;
            $row->save();
        }
        return true;
    }
}
