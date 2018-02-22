<?php namespace Aike\Web\Model\Controllers;

use DB;
use Auth;
use Input;
use Request;
use Validator;

use Aike\Web\Model\Model;
use Aike\Web\Index\Controllers\DefaultController;

class ModelController extends DefaultController
{
    public function indexAction()
    {
        $limit = Input::get('limit', 25);
        $rows = Model::paginate($limit);
        $rows->items(array_nest($rows->items()));

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
            return $this->success('index', '恭喜你，操作成功。');
        }
    }
}
