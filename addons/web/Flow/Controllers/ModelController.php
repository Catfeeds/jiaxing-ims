<?php namespace Aike\Web\Flow\Controllers;

use DB;
use Auth;
use Input;
use Request;
use Validator;

use Aike\Web\Flow\Model;
use Aike\Web\Flow\Field;
use Aike\Web\Flow\Step;
use Aike\Web\Flow\StepLog;

use Aike\Web\Index\Controllers\DefaultController;

class ModelController extends DefaultController
{
    public function indexAction()
    {
        if (Request::method() == 'POST') {
            $sorts = Input::get('sort');
            foreach ($sorts as $id => $sort) {
                $field = Model::find($id);
                $field->sort = $sort;
                $field->save();
            }
            Model::treeRebuild();
            return $this->success('index', '恭喜你，操作成功。');
        }

        $model = Model::orderBy('lft', 'asc');
        $rows = $model->paginate(50);

        $items = $rows->items();
        array_nest($items);

        $rows->items($items);

        return $this->display([
            'rows' => $rows,
        ]);
    }

    public function createAction()
    {
        if (Request::method() == 'POST') {
            $gets = Input::get();

            $rules = [
                'name'  => 'required',
                'table' => 'required',
            ];
            $v = Validator::make($gets, $rules);

            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }

            $model = Model::findOrNew($gets['id']);
            $model->fill($gets);
            $model->save();
            $model->treeRebuild();
            return $this->success('index', '恭喜你，操作成功。');
        }

        $id     = Input::get('id');
        $row    = Model::find($id);
        $models = Model::where('parent_id', 0)->get();
        
        return $this->display([
            'row'    => $row,
            'models' => $models,
        ]);
    }

    public function deleteAction()
    {
        $id = Input::get('id');
        if ($id > 0) {
            Model::find($id)->delete();
            Field::where('model_id', $id)->delete();
            Step::where('model_id', $id)->delete();
            StepLog::where('model_id', $id)->delete();
            return $this->success('index', '恭喜你，操作成功。');
        }
    }
}
