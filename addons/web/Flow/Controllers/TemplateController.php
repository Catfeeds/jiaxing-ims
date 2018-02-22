<?php namespace Aike\Web\Flow\Controllers;

use DB;
use Auth;
use Input;
use Request;
use Validator;

use Aike\Web\Flow\Model;
use Aike\Web\Flow\Field;
use Aike\Web\Flow\Template;

use Aike\Web\Index\Controllers\DefaultController;

class TemplateController extends DefaultController
{
    public $permission = ['create'];

    public function indexAction()
    {
        if (Request::method() == 'POST') {
            $sorts = Input::get('sort');
            $i = 0;
            foreach ($sorts as $sort) {
                Template::where('id', $sort)->update(['sort' => $i]);
                $i ++;
            }
            return $this->json('恭喜你，操作成功。', true);
        }

        $model_id = Input::get('model_id');

        $rows = Template::where('model_id', $model_id)
        ->orderBy('sort', 'asc')
        ->get();

        $models = DB::table('flow')->where('parent_id', 0)->orderBy('lft', 'asc')->get();
        $model = Model::find($model_id);

        return $this->display([
            'rows'     => $rows,
            'model_id' => $model_id,
            'models'   => $models,
            'model'    => $model,
        ]);
    }

    public function createAction()
    {
        $gets = Input::get();

        if (Request::method() == 'POST') {
            $rules = [
                'name' => 'required',
            ];
            $v = Validator::make($gets, $rules);

            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }

            $gets['type']   = join(',', (array)$gets['type']);
            $gets['client'] = join(',', (array)$gets['client']);
            $gets['tpl'] = json_encode($gets['columns'], JSON_UNESCAPED_UNICODE);
            unset($gets['columns']);

            $model = Template::findOrNew($gets['id']);
            $model->fill($gets);
            $model->save();
            return $this->json('恭喜你，操作成功。', true);
        }

        $model_id = Input::get('model_id');

        $model  = DB::table('flow')->find($model_id);
        $fields = DB::table('flow_field')->where('model_id', $model_id)->get();

        $template = DB::table('flow_template')->find($gets['id']);

        // 子模型
        $childrens = DB::table('flow')->where('parent_id', $model_id)->get();
        foreach ($childrens as $children) {
            $children['fields'] = DB::table('flow_field')->where('model_id', $children['id'])->get();
            $fields[] = $children;
        }

        $models = DB::table('flow')->where('parent_id', 0)->orderBy('lft', 'asc')->get();

        $template['tpl']    = $template['tpl'] == 'null' ? '[]' : $template['tpl'];
        $template['type']   = explode(',', $template['type']);
        $template['client'] = explode(',', $template['client']);

        return $this->display([
            'template' => $template,
            'model'    => $model,
            'model_id' => $model_id,
            'fields'   => $fields,
            'tpl'      => $tpl,
            'models'   => $models,
        ]);
    }

    public function deleteAction()
    {
        $id = Input::get('id');
        if ($id > 0) {
            DB::table('flow_template')->where('id', $id)->delete();
            return $this->success('index', '恭喜你，操作成功。');
        }
    }
}
