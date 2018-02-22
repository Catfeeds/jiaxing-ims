<?php namespace Aike\Web\Index;

use DB;

use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Database\Query\Builder as QueryBuilder;

class BaseModel extends Eloquent
{
    public $timestamps = false;

    /**
     * 设置不允许批量赋值的字段
     */
    protected $guarded = ['id'];

    /**
     * 获取连接的新查询生成器实例
     *
     * @return \App\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();
        return new QueryBuilder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );
    }

    /**
     * 重写日期格式
     */
    protected function getDateFormat()
    {
        return 'U';
    }

    public function getDates()
    {
        return [];
    }

    public function scopeWithAt($query, $relation, array $columns)
    {
        return $query->with([$relation => function ($query) use ($columns) {
            $query->select(array_merge(['id'], $columns));
        }]);
    }

    public function step()
    {
        return $this->belongsTo('Aike\Web\Model\Step', 'step_number', 'number');
    }

    public function scopeGetStep($query, $number = 1)
    {
        $table = $this->table;
        return \Aike\Web\Model\Model::leftJoin('model_step', 'model_step.model_id', '=', 'model.id')
        ->where('model.table', $table)
        ->where('model_step.number', $number)
        ->select('model_step.*')
        ->first();
    }

    public function scopeGetSteps($query)
    {
        $table = $this->table;
        return \Aike\Web\Model\Model::leftJoin('model_step', 'model_step.model_id', '=', 'model.id')
        ->where('model.table', $table)
        ->select('model_step.*')
        ->orderBy('model_step.number', 'asc')
        ->get();
    }
    
    public function scopeStepAt($query)
    {
        $table = $this->table;

        // 过滤草稿状态
        $query->whereRaw('('.$table.'.created_by=? and '.$table.'.step_number=1 or '.$table.'.step_number>1)', [auth()->id()]);

        return $query->with(['step' => function ($q) use ($table) {
            $q->LeftJoin('model', 'model.id', '=', 'model_step.model_id')
            ->where('model.table', $table)
            ->select('model_step.*');
        }]);
    }

    /**
     * 查询 Dialog 字段显示的值，其他模型可复写此方法
     */
    public function scopeDialog($query, $value, $column)
    {
        return $query->whereIn('id', $value)->get(['id', $column]);
    }

    /**
     * 取得所有层级
     *
     * @var string $columns 选择字段
     */
    public function scopeTree($query, $select = ['node.*'])
    {
        $table = $this->table;

        $rows = $this->from(DB::raw($this->from.' as node, '.$this->from.' as parent'))
        ->select($select)
        ->selectRaw('(COUNT(parent.id)-1) level')
        ->whereRaw('node.lft BETWEEN parent.lft AND parent.rgt')
        ->groupBy('node.id')
        ->orderBy('node.lft', 'asc');

        /*
        $select = 'node.'.str_replace(',', ',node.', $select);
        $rows = DB::select(
            'SELECT '.$select.',(COUNT(parent.id)-1) level FROM '.$table.' node, '.$table.' parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
            GROUP BY node.id
            ORDER BY node.lft ASC'
        );
        */

        $result = array();

        if (is_array($rows)) {
            foreach ($rows as $row) {
                $row['layer'] = str_repeat('|&ndash;', $row['level']);
                $result[$row['id']] = $row;
            }
        }
        return $result;
    }
    
    /**
     * 取得指定层级集
     *
     * @var int $id 条件编号
     * $type int 0.包含自己的所有子类, 1.包含自己所有父类
     */
    public function scopeTreeById($query, $id, $type = 0)
    {
        $table = $this->table;
        $rows = $this->from(DB::raw($table.' as node, '.$table.' as parent'))
        ->whereRaw($type == 0 ? 'node.lft BETWEEN parent.lft AND parent.rgt' : 'parent.rgt BETWEEN node.lft AND node.rgt')
        ->where('parent.id', $id)
        ->groupBy('node.id')
        ->orderBy('node.lft', 'asc')
        ->select(['node.*'])
        ->get();

        return $rows;
        /*
        $on = array(
            'node.lft BETWEEN parent.lft AND parent.rgt',
            'parent.rgt BETWEEN node.lft AND node.rgt'
        );
        return \DB::select(
            'SELECT node.* FROM '.$table.' node, '.$table.' parent
            WHERE '.$on[$type].'
            AND parent.id = ?
            GROUP BY node.id
            ORDER BY node.lft ASC',
            array($id)
        );
        */
    }

    public function scopeToTree($query, $text = 'name', $selected = 0, $state = 'closed')
    {
        if ($selected > 0) {
            $selected = $query->treeSinglePath($selected);
        }
        $nodes = $query->get()->toArray();

        // 格式化的树
        $tree = [];

        //临时扁平数据
        $map = [];

        foreach ($nodes as $node) {
            $node['text'] = $node[$text];
            $node['state'] = ($state == 'closed' && empty($selected[$node['id']])) ? 'closed' : 'open';
            $map[$node['id']] = $node;
        }
        unset($selected);

        foreach ($nodes as $node) {
            if (isset($map[$node['parent_id']])) {
                $map[$node['parent_id']]['children'][] = &$map[$node['id']];
            } else {
                $tree[] = &$map[$node['id']];
            }
        }
        unset($map, $nodes);
        return $tree;
    }

    /**
     * 将所有子节点的ID压入根节点
     */
    public function scopeToChild($query, array $select = array('*'))
    {
        $items = $query->get($select)->keyBy('id')->toArray();
        /*$parent = [];
        foreach ($items as &$item) {
            $id = $item['id'];
            $item['child'][$id] = $id;
            $parent[$item['parent_id']][$id] = $id;
        }
        */
        $id = 0;
        foreach ($items as &$item) {
            $path = explode(',', $item['path']);
            $item['parent'] = $path;

            //$items[$parent_id]['a'] = array_merge($path, (array)$items[$parent_id]['a']);

            //print_r($path);

            //$child_id  = $parent[$item['id']] ? $parent[$item['id']] : [$item['id']];
            //$parent_id = $items[$item['parent_id']]['child'] ? $items[$item['parent_id']]['child'] : [$item['id']];
            //$items[$item['parent_id']]['child'] = array_merge($parent_id, $child_id);
            /*
            if ($item['parent_id'] == 0) {
                $id = $item['id'];
                $item['child'] = [$id];
            } else {
                $items[$id]['child'][] = $item['id'];
            }
            */
        }
        return $items;
    }

    /**
     * 返回当前节点的完整路径
     */
    public function scopeTreeSinglePath($query, $id)
    {
        $table = $this->table;
        $rows = $this->from(DB::raw($table.' as node, '.$table.' as parent'))
        ->whereRaw('node.lft BETWEEN parent.lft AND parent.rgt')
        ->where('node.id', $id)
        ->orderBy('node.lft', 'asc')
        ->select(['parent.*'])
        ->get()->keyBy('id');
        /*
        $maps = DB::select('
            SELECT parent.* FROM '.$table.' node, '.$table.' parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
            AND node.id = ?
            ORDER BY node.lft', [$id]);

        $rows = [];
        foreach ($maps as $map) {
            $rows[$map['id']] = $map;
        }
        */
        return $rows;
    }

    /**
     * 重建树形结构的左右值
     *
     * @var $parent_id 构建的开始id
     */
    public function scopeTreeRebuild($query, $parent_id = 0, $left = 0)
    {
        // 左值 +1 是右值
        $right = $left + 1;

        // 获得这个节点的所有子节点
        $rows = $this->where('parent_id', $parent_id)
        ->orderBy('sort', 'asc')
        ->get(['id', 'parent_id', 'lft', 'rgt']);

        if (sizeof($rows)) {
            foreach ($rows as $row) {
                // 这个节点的子$right是当前的右值，这是由treeRebuild函数递增
                $right = $this->TreeRebuild($row->id, $right);
            }
        }

        // 更新左右值
        $this->where('id', $parent_id)->orderBy('sort', 'asc')
        ->update(['lft'=>$left, 'rgt'=>$right]);

        // 返回此节点的右值+1
        return $right + 1;
    }
}
