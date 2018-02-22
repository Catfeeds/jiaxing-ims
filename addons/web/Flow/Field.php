<?php namespace Aike\Web\Flow;

use Schema;
use DB;
use URL;
use Auth;
use Config;

use Module;

use Aike\Web\Index\BaseModel;
use Aike\Web\Index\Attachment;
use Aike\Web\Setting\Setting;

class Field extends BaseModel
{
    protected $table = 'flow_field';

    public function model()
    {
        return $this->belongsTo(Model::class);
    }

    public static function title()
    {
        return [
            'text'     => '单行文本',
            'textarea' => '多行文本',
            'password' => '密码文本',
            'option'   => '选项菜单',
            'radio'    => '单选按钮',
            'select'   => '下拉菜单',
            'checkbox' => '复选框',
            'dialog'   => '对话框',
            'dataset'  => '数据集',
            'auto'     => '宏控件',
            'calc'     => '计算控件',
            'editor'   => '编辑器',
            'date'     => '日期时间',
            'image'    => '单图上传',
            'file'     => '文件上传',
            'files'    => '多文件上传',
            'address'  => '省市区',
            'label'    => '标签',
            'sn'       => '流水号',
        ];
    }

    public static function tr_align($setting = '')
    {
        $setting['align'] = isset($setting['align']) ? $setting['align'] : '';
        $rows = ['left' => 'left', 'center' => 'center', 'right' => 'right'];
        return Field::tr_select($rows, 'align', 'setting[align]', $setting['align']);
    }

    public static function tr_select($data, $title, $name, $value)
    {
        $str = '<tr>
	      	<td align="right">'.$title.'</td>
	      	<td><select class="form-control input-inline input-sm" name="'.$name.'">
                <option value=""> - </option>';
        foreach ($data as $k => $v) {
            $selected = $k == $value ? ' selected' : '';
            $str .= '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
        }
        $str .= '</select>
            </td>
	    </tr>';
        
        return $str;
    }

    /**
     * 以下函数作用于字段添加/修改部分
     */
    public static function form_text($setting = '')
    {
        $str = '';
        $str .= Field::tr_align($setting);
        $str .= '<tr>
        <td align="right">宽度</td>
        <td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['width']) ? $setting['width'] : '') . '" name="setting[width]"> <font color="gray">px</font></td>
        </tr><tr>
	      	<td align="right">css</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['css']) ? $setting['css'] : '') . '" name="setting[css]"> <font color="gray">input-inline</font></td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]"> </td>
        </tr>
        <tr>
	      	<td align="right">行计事件</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['row_count']) ? $setting['row_count'] : '') . '" name="setting[row_count]"></td>
        </tr>
        <tr>
            <td align="right">总计事件</td>
            <td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['total_count']) ? $setting['total_count'] : '') . '" name="setting[total_count]"></td>
        </tr>';
        /*
        $count = isset($setting['row_count']) && $setting['row_count'] ? $setting['row_count'] : '';
        $str .= Field::tr_select(['sum' => 'SUM'], '行计', 'setting[row_count]', $count);

        $count = isset($setting['count']) && $setting['count'] ? $setting['count'] : '';
        $str .= Field::tr_select(['sum' => 'SUM'], '合计', 'setting[count]', $count);
        */
        return $str;
    }

    public static function form_calc($setting = '')
    {
        $str = '';
        $str .= Field::tr_align($setting);
        $str .= '<tr>
            <td align="right">宽度</td>
            <td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['width']) ? $setting['width'] : '') . '" name="setting[width]"> <font color="gray">px</font></td>
        </tr><tr>
	      	<td align="right">css</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['css']) ? $setting['css'] : '') . '" name="setting[css]"> <font color="gray">input-inline</font></td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]"></td>
	    </tr>';
        return $str;
    }

    public static function form_auto($setting = '')
    {
        $t = isset($setting['type']) && $setting['type'] ? $setting['type'] : '';
        $types = [
            'sys_date'       => '当前日期，形如 1999-01-01',
            'sys_date_cn'    => '当前日期，形如 2009年1月1日',
            'sys_date_cn_s1' => '当前日期，形如 2009年',
            'sys_date_cn_s2' => '当前年份，形如 2009',
            'sys_date_cn_s3' => '当前日期，形如 2009年1月',
            'sys_date_cn_s4' => '当前日期，形如 1月1日',
            'sys_time'       => '当前时间',
            'sys_datetime'   => '当前日期+时间',
            'sys_week'       => '当前星期中的第几天，形如 星期一',
            'sys_userid'     => '当前用户ID',
            'sys_nickname'   => '当前用户姓名',
            'sys_department_name'      => '当前用户部门',
            'sys_user_position'        => '当前用户职位',
            'sys_user_position_assist' => '当前用户辅助职位',
            'sys_nickname_date'        => '当前用户姓名+日期',
            'sys_nickname_datetime'    => '当前用户姓名+日期+时间',
            'sys_sql'                  => '来自sql查询语句',
        ];

        $str  = Field::tr_select($types, '类型', 'setting[type]', $t);
        $str .= '<tr>
	      	<td align="right">宽度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' .(isset($setting['width']) ? $setting['width'] : '') . '" name="setting[width]"> <font color="gray">px</font></td>
	    </tr><tr>
            <td align="right">css</td>
            <td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['css']) ? $setting['css'] : '') . '" name="setting[css]"> <font color="gray">input-inline</font></td>
        </tr>';
        $str .= Field::tr_align($setting);
        $str .= '<tr>
	      	<td align="right">默认值</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' .(isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]"></td>
	    </tr>';
        return $str;
    }

    public static function form_password($setting = '')
    {
        $str = '<tr>
	      	<td align="right">宽度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' .(isset($setting['width']) ? $setting['width'] : '') . '" name="setting[width]"><font color="gray">px</font></td>
	    </tr><tr>
            <td align="right">css</td>
            <td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['css']) ? $setting['css'] : '') . '" name="setting[css]"> <font color="gray">input-inline</font></td>
        </tr>';
        $str .= Field::tr_align($setting);
        $str .= '<tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' .(isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]"></td>
	    </tr>';
        return $str;
    }

    public static function form_textarea($setting = '')
    {
        $str = '<tr>
	      	<td align="right">宽度</td>
	      	<td>
	      		<input type="text" class="form-control input-inline input-sm" value="' .(isset($setting['width']) ? $setting['width'] : '400') . '" name="setting[width]">
	      		<font color="gray">px</font>
	      	</td>
	    </tr>
		<tr>
	      	<td align="right">高度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' .(isset($setting['height']) ? $setting['height'] : '90') . '" name="setting[height]">
	      		<font color="gray">px</font>
	      	</td>
	    </tr>';
        $str .= Field::tr_align($setting);
        $str .= '<tr>
	      	<td align="right">默认值</td>
	      	<td><textarea name="setting[default]" rows="2" class="form-control inline-xs-4 input-sm">' . (isset($setting['default']) ? $setting['default'] : '') . '</textarea></td>
	    </tr>';
        return $str;
    }

    public static function form_editor($setting = '')
    {
        $t = isset($setting['type']) && $setting['type'] ?  1 : 0;
        $w = isset($setting['width'])  ? $setting['width']  : '100';
        $h = isset($setting['height']) ? $setting['height'] : '300';
        $str = '<tr>
	      	<td align="right">宽度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . $w. '" name="setting[width]">
	      		<font color="gray">%</font>
	      	</td>
	    </tr>
	    <tr>
	      	<td align="right">高度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . $h . '" name="setting[height]">
	      		<font color="gray">px</font>
	      	</td>
	    </tr>';
        $str .= Field::tr_align($setting);
        $str .= '<tr>
	    <tr>
	      	<td align="right">类型</td>
	      	<td>
	      		<label class="radio-inline"><input type="radio" value=1 name="setting[type]" ' . ($t == 1 ? 'checked' : '') . '> 完整模式</label>
		  		<label class="radio-inline"><input type="radio" value=0 name="setting[type]"' . ($t == 0 ? 'checked' : '') . '> 简洁模式</label>
	      	</td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td><textarea name="setting[default]" rows="3" class="form-control inline-xs-4 input-sm">' . (isset($setting['default']) ? $setting['default'] : '') . '</textarea></td>
	    </tr>';
        return $str;
    }

    public static function form_select($setting = '')
    {
        return '<tr>
	      	<td align="right">选项列表</td>
	      	<td><textarea name="setting[content]" rows="3" class="form-control inline-xs-4 input-sm">' . (isset($setting['content']) ? $setting['content'] : '') . '</textarea>
	      		<font color="gray">格式：选项名称1|选项值1 (回车换行)</font>
	      	</td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]"></td>
	    </tr>';
    }

    // 地址选项
    public static function form_address($setting = '')
    {
        return '<tr>
	      	<td align="right">选项列表</td>
	      	<td><textarea name="setting[content]" rows="3" class="form-control inline-xs-4 input-sm">' . (isset($setting['content']) ? $setting['content'] : '') . '</textarea>
	      		<font color="gray">格式：选项名称1|选项值1 (回车换行)</font>
	      	</td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]"></td>
	    </tr>';
    }

    // 标签
    public static function form_label($setting = '')
    {
        return Field::tr_align($setting);
    }

    // 单据编号
    public static function form_sn($setting = '')
    {
        $str = '<tr>
	      	<td align="right">宽度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['width']) ? $setting['width'] : '') . '" name="setting[width]"> <font color="gray">px</font></td>
	    </tr>';
        $str .= Field::tr_align($setting);
        $str .= '<tr>
	      	<td align="right">css</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['css']) ? $setting['css'] : '') . '" name="setting[css]"> <font color="gray">input-inline</font></td>
	    </tr>
        <tr>
	      	<td align="right">规则</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['rule']) ? $setting['rule'] : '') . '" name="setting[rule]"> <font color="gray">格式: {Y}{M}{D}-{SN,4}</font></td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]"></td>
	    </tr>';
        return $str;
    }

    // 单选按钮
    public static function form_radio($setting = '')
    {
        return Field::form_select($setting);
    }

    // 多选按钮
    public static function form_checkbox($setting = '')
    {
        return '<tr>
	      	<td align="right">选项列表</td>
	      	<td><textarea name="setting[content]" rows="3" class="form-control inline-xs-4 input-sm">' . (isset($setting['content']) ? $setting['content'] : '') . '</textarea>
	      		<font color="gray">格式：选项名称1|选项值1 (回车换行)</font>
	      	</td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td>
	      		<input type="text" class="form-control inline-xs-4 input-sm" value="' . (isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]">
		  		<font color="gray">多个选中值以分号分隔“,”，格式：选中值1,选中值2</font>
		  	</td>
	    </tr>';
    }

    // 图片上传
    public static function form_image($setting = '')
    {
        return '<tr>
	      	<td align="right">宽度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['width']) ? $setting['width'] : '200') . '" name="setting[width]">
	      		<font color="gray">px</font>
	      	</td>
	    </tr>
		<tr>
	      	<td align="right">高度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['height']) ? $setting['height'] : '160') . '" name="setting[height]">
	      		<font color="gray">px</font>
	      	</td>
	    </tr>
		<tr>
	      	<td align="right">大小</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['size']) ? $setting['size'] : '2') . '" name="setting[size]">
	      		<font color="gray">MB</font>
	      	</td>
	    </tr>';
    }

    // 文件上传
    public static function form_file($setting = '')
    {
        return '<tr>
	      	<td align="right">格式</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['type']) ? $setting['type'] : '') . '" name="setting[type]">
	      		<font color="gray">多个格式以,号分开，如：zip,rar,tar</font>
	      	</td>
	    </tr>
	    <tr>
	      	<td align="right">文件表名</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['table']) ? $setting['table'] : '') . '" name="setting[table]">
	      		<font color="gray">例如: attachment</font>
	      	</td>
	    </tr>
	    <tr>
	      	<td align="right">文件子路径</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['path']) ? $setting['path'] : '') . '" name="setting[path]">
	      		<font color="gray">例如: calendar</font>
	      	</td>
	    </tr>
	    <tr>
	      	<td align="right">大小</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['size']) ? $setting['size'] : '2') . '" name="setting[size]">
	      		<font color="gray">MB</font>
	      	</td>
	    </tr>';
    }

    // 多文件上传
    public static function form_files($setting = '')
    {
        return Field::form_file();
    }

    // 选项菜单
    public static function form_option($setting = '')
    {
        $single = isset($setting['single']) && $setting['single'] == 1 ?  1 : 0;
        
        $str = '<tr>
	      	<td align="right">宽度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['width']) ? $setting['width'] : '') . '" name="setting[width]">
	      		<font color="gray">px</font>
	      	</td>
        </tr>
        <tr>
            <td align="right">css</td>
            <td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['css']) ? $setting['css'] : '') . '" name="setting[css]"> <font color="gray">input-inline</font></td>
        </tr>
        </tr>';
        $str .= Field::tr_align($setting);
        $str .= '<tr>
	      	<td align="right">值</td>
	      	<td>
	      		<input class="form-control input-inline input-sm" value="' . (isset($setting['value']) ? $setting['value'] : '') . '" name="setting[value]">
		  		<font color="gray">选项值key</font>
	      	</td>
	    </tr>
        <tr>
	      	<td align="right">值字段</td>
	      	<td>
	      		<input class="form-control input-inline input-sm" value="' . (isset($setting['id']) ? $setting['id'] : 'id') . '" name="setting[id]">
		  		<font color="gray">格式：此表字段=选项字段</font>
	      	</td>
	    </tr>
        <tr>
	      	<td align="right">显示字段</td>
	      	<td>
	      		<input class="form-control input-inline input-sm" value="' . (isset($setting['name']) ? $setting['name'] : 'name') . '" name="setting[name]">
		  		<font color="gray">格式：此表字段=选项字段</font>
	      	</td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td>
	      		<input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]">
		  		<font color="gray">多个选中值以分号分隔“,”，格式：选中值1,选中值2</font>
		  	</td>
	    </tr>
	    <tr>
	      	<td align="right">其他选项</td>
	      	<td>
	      		<label class="radio-inline"><input type="radio" value=1 name="setting[single]" ' . (isset($setting['single']) && $setting['single'] == 1 ? 'checked' : '') . '> 单选</label>
		  		<label class="radio-inline"><input type="radio" value=0 name="setting[single]"' . (isset($setting['single']) && $setting['single'] == 0 ? 'checked' : '') . '> 多选</label>
	      	</td>
	    </tr>
        ';
        return $str;
    }

    // 数据集
    public static function form_dataset($setting = '')
    {
        $single  = isset($setting['single']) && $setting['single'] == 1 ? 1 : 0;
        $type    = isset($setting['type']) && $setting['type'] ? $setting['type']  : 'user';
        $width   = isset($setting['width']) && $setting['width'] ? $setting['width'] : '';
        $display = isset($setting['display']) && $setting['display'] ? $setting['display'] : '';

        $types = [];
        $dialogs = Module::dialogs();
        foreach ($dialogs as $table => $dialog) {
            $types[$table] = $dialog['name'];
        }

        $types['custom'] = '自定义';
        $types['sql']    = 'SQL';

        $displays = array(
            'dialog' => '弹窗',
            'select' => '列表',
        );

        $str = Field::tr_select($types, '数据源', 'setting[type]', $type);

        $str .= Field::tr_align($setting);
        $str .= '<tr>
	      	<td align="right">值字段</td>
	      	<td>
	      		<input class="form-control input-inline input-sm" value="' . (isset($setting['id']) ? $setting['id'] : '') . '" name="setting[id]">
		  		<font color="gray">格式：此表字段=选项字段</font>
	      	</td>
	    </tr>
        <tr>
	      	<td align="right">显示字段</td>
	      	<td>
	      		<input class="form-control input-inline input-sm" value="' . (isset($setting['name']) ? $setting['name'] : '') . '" name="setting[name]">
		  		<font color="gray">格式：此表字段=选项字段</font>
	      	</td>
	    </tr>
        <tr>
	      	<td align="right">映射字段</td>
	      	<td>
	      		<textarea class="form-control input-inline input-sm" name="setting[map]" rows="3">' . (isset($setting['map']) ? $setting['map'] : '') . '</textarea>
		  		<font color="gray">格式：此表字段=选项字段 (回车换行)</font>
	      	</td>
	    </tr>';

        $str .= Field::tr_select($displays, '显示类型', 'setting[display]', $display);

        $str .= '<tr>
	      	<td align="right">默认值</td>
	      	<td>
	      		<input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]">
		  		<font color="gray">多个选中值以分号分隔“,”，格式：选中值1,选中值2</font>
		  	</td>
	    </tr>
	    <tr>
	      	<td align="right">其他选项</td>
	      	<td>
	      		<label class="radio-inline"><input type="radio" value=1 name="setting[single]" ' . (isset($setting['single']) && $setting['single'] == 1 ? 'checked' : '') . '> 单选</label>
		  		<label class="radio-inline"><input type="radio" value=0 name="setting[single]"' . (isset($setting['single']) && $setting['single'] == 0 ? 'checked' : '') . '> 多选</label>
	      	</td>
	    </tr>
        <tr>
	      	<td align="right">宽度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['width']) ? $setting['width'] : '') . '" name="setting[width]"> <font color="gray">px</font></td>
	    </tr>';
        return $str;
    }

    // 对话框
    public static function form_dialog($setting = '')
    {
        $single = isset($setting['single']) && $setting['single'] == 1 ? 1 : 0;
        $type   = isset($setting['type']) && $setting['type'] ? $setting['type']  : '';
        $width  = isset($setting['width']) && $setting['width'] ? $setting['width'] : '';

        $types = [];
        $dialogs = Module::dialogs();
        foreach ($dialogs as $table => $dialog) {
            $types[$table] = $dialog['name'];
        }
        $types['sql'] = 'SQL';

        $str = Field::tr_select($types, '数据源', 'setting[type]', $type);
        $str .= Field::tr_align($setting);
        $str .= '<tr>
        <td align="right">宽度</td>
        <td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['width']) ? $setting['width'] : '') . '" name="setting[width]">
            <font color="gray">px</font>
        </td>
        </tr>
        <tr>
            <td align="right">css</td>
            <td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['css']) ? $setting['css'] : '') . '" name="setting[css]"> <font color="gray">input-inline</font></td>
        </tr>
        <tr>
	      	<td align="right">值字段</td>
	      	<td>
	      		<input class="form-control input-inline input-sm" value="' . (isset($setting['id']) ? $setting['id'] : '') . '" name="setting[id]">
		  		<font color="gray">格式：此表字段=选项字段</font>
	      	</td>
	    </tr>
        <tr>
	      	<td align="right">显示字段</td>
	      	<td>
	      		<input class="form-control input-inline input-sm" value="' . (isset($setting['name']) ? $setting['name'] : '') . '" name="setting[name]">
		  		<font color="gray">格式：此表字段=选项字段</font>
	      	</td>
	    </tr>
        <tr>
	      	<td align="right">映射字段</td>
	      	<td>
	      		<textarea class="form-control input-inline input-sm" name="setting[mapping]" rows="3">' . (isset($setting['mapping']) ? $setting['mapping'] : '') . '</textarea>
		  		<font color="gray">格式：此表字段=选项字段 (回车换行)</font>
	      	</td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td>
	      		<input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]">
		  		<font color="gray">多个选中值以分号分隔“,”，格式：选中值1,选中值2</font>
		  	</td>
	    </tr>
	    <tr>
	      	<td align="right">其他选项</td>
	      	<td>
	      		<label class="radio-inline"><input type="radio" value=1 name="setting[single]" ' . (isset($setting['single']) && $setting['single'] == 1 ? 'checked' : '') . '> 单选</label>
		  		<label class="radio-inline"><input type="radio" value=0 name="setting[single]"' . (isset($setting['single']) && $setting['single'] == 0 ? 'checked' : '') . '> 多选</label>
	      	</td>
	    </tr>
        ';
        return $str;
    }

    // 日期
    public static function form_date($setting = '')
    {
        $type = isset($setting['type']) && $setting['type'] ? $setting['type'] : 'Y-m-d H:i:s';
        $save = isset($setting['save']) && $setting['save'] ? $setting['save'] : 'date';
        $str = '<tr>
	      	<td align="right">宽度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['width']) ? $setting['width'] : '') . '" name="setting[width]">
	      		<font color="gray">px</font>
	      	</td>
            </tr><tr>
            <td align="right">css</td>
            <td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['css']) ? $setting['css'] : '') . '" name="setting[css]"> <font color="gray">input-inline</font></td>
        </tr>';
        $str .= Field::tr_align($setting);
        $str .= '<tr>
	      	<td align="right">输入格式</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . $type . '" name="setting[type]">
	      		<font color="gray">格式: Y-m-d H:i:s 表示: 2001-02-13 11:20:20</font>
	      	</td>
	    </tr>';
        $str .= Field::tr_select(['date' => '日期', 'u' => '时间戳'], '保存格式', 'setting[save]', $save);
        $str .= '<tr>
	    	<td align="right">默认值</td>
	    	<td>
	      		<label class="radio-inline"><input type="radio" value=1 name="setting[default]"' . (isset($setting['default']) && $setting['default'] == 1 ? 'checked' : '') . '> 当前时间</label>
		  		<label class="radio-inline"><input type="radio" value=0 name="setting[default]"' . (isset($setting['default']) && $setting['default'] == 0 ? 'checked' : '') . '> 空</label>
	    	</td>
	    </tr>';
        return $str;
    }

    // 字段扩展设置
    public static function content_field($field)
    {
        // 配置
        $setting = isset($field['setting']) ? json_decode($field['setting'], true) : $field;

        $field['data'] = $field['table'].'['.$field['field'].']';
        $field['key']  = $field['table'].'_'.$field['field'];

        $attribute = $field['attribute'];

        $attribute['class'] = ['form-control','input-sm'];

        if ($field['form_type'] == 'textarea') {
        } else {
            // $attribute['class'][] = 'input-inline';
        }

        if ($setting['css']) {
            $attribute['class'][] = $setting['css'];
        }

        if ($setting['width']) {
            $attribute['style'][] = 'width:'.$setting['width'].'px';
        }

        if ($setting['height']) {
            $attribute['style'][] = 'height:'.$setting['height'].'px';
        }

        if ($field['validate']) {
            $attribute['validate'] = $field['validate'];
        }

        $attribute['id']    = $field['key'];
        $attribute['name']  = $field['data'];

        $field['attribute'] = $attribute;
        $field['setting']   = $setting;

        return $field;
    }

    // 字段属性组合
    public static function content_attribute($attributes)
    {
        foreach ($attributes as $k => $v) {
            if ($k == 'class') {
                $attributes[$k] = $k.'="'.join(' ', $v).'"';
            } elseif ($k == 'style') {
                $attributes[$k] = $k.'="'.join(';', $v).'"';
            } else {
                $attributes[$k] = $k.'="'.$v.'"';
            }
        }
        return join(' ', $attributes);
    }

    public static function content_label($field, $content = '')
    {
        return $content;
    }

    /**
     * 以下函数作用于发布内容部分
     */
    public static function content_text($field, $content = '')
    {
        $field = Field::content_field($field);
        
        $type = $field['is_hide'] == 0 ? 'text' : 'hidden';

        if ($field['is_read'] == 1 && $field['is_hide'] == 1) {
            return '';
        }

        if ($field['is_read']) {
            $field['attribute']['readonly'] = 'readonly';
        }
        return $field['is_show'] ? $content : '<input type="'.$type.'" value="' . $content . '" ' . Field::content_attribute($field['attribute']) . ' />';
    }

    public static function content_sn($field, $content = '')
    {
        $field = Field::content_field($field);
        $setting = $field['setting'];

        $time = time();
        $user = auth()->user();

        $items = [
            '{y}'  => date('y', $time),
            '{Y}'  => date('Y', $time),
            '{M}'  => date('m', $time),
            '{D}'  => date('d', $time),
            '{H}'  => date('H', $time),
            '{I}'  => date('i', $time),
            '{S}'  => date('s', $time),
            '{U}'  => $user['nickname'],
            '{UD}' => $user->department['title'],
            '{UR}' => $user->role['title'],
            '{UP}' => $user->position['title'],
        ];

        // 生成单据编码
        if (preg_match('/{SN,(\d+)}/', $setting['rule'], $sn)) {
            $items['{SN,'.$sn[1].'}'] = str_pad((int)$field['data_sn'] + 1, $sn[1], '0', STR_PAD_LEFT);
        }

        if ($content == '') {
            $content = str_replace(array_keys($items), array_values($items), $setting['rule']);
        }

        if ($field['is_read']) {
            $field['attribute']['readonly'] = 'readonly';
        }
        return $field['is_show'] ? $content : '<input type="text" value="' . $content . '" ' . Field::content_attribute($field['attribute']) . ' />';
    }

    public static function content_auto($field, $content = '')
    {
        $field = Field::content_field($field);
        $setting = $field['setting'];

        $t = isset($setting['type']) ? $setting['type'] : '';

        $time = time();
        $user = auth()->user();

        $items = [
            '{Y}'    => date('Y', $time),
            '{M}'    => date('m', $time),
            '{D}'    => date('d', $time),
            '{H}'    => date('H', $time),
            '{I}'    => date('i', $time),
            '{S}'    => date('s', $time),
            'sys_nickname' => $user['nickname'],
            'sys_nickname_datetime' => $user['nickname'].' '.date('Y-m-d H:i'),
            'sys_department_name'   => $user->department['title'],
            '{UR}'   => $user->role['title'],
            '{UP}'   => $user->position['title'],
        ];

        if ($field['is_read']) {
            $field['attribute']['readonly'] = 'readonly';
        } else {
            if ($field['is_show'] == 0) {
                $content = $items[$t];
            }

            if ($field['is_auto']) {
                $field['attribute']['readonly'] = 'readonly';
            }
        }

        return $field['is_show'] ? $content : '<input type="text" value="' . $content . '" ' . Field::content_attribute($field['attribute']) . ' />';
    }

    public static function content_address($field, $content = '')
    {
        $field = Field::content_field($field);

        if ($field['is_show']) {
            return $content;
        }

        $_content = explode("\n", $content);

        $class = ['form-control','input-inline','input-sm'];

        if ($field['is_read']) {
            $class[] = 'readonly';
        }
        $attribute[] = 'class="'. join(' ', $class).'"';

        $attr = join(' ', $attribute);

        $field['attribute']['placeholder'] = '街道';

        $str = '<select '.$attr.' id="'.$field['key'].'_0" name="'.$field['data'].'[0]"></select>';
        $str .= '&nbsp;<select '.$attr.' id="'.$field['key'].'_1" name="'.$field['data'].'[1]"></select>';
        $str .= '&nbsp;<select '.$attr.' id="'.$field['key'].'_2" name="'.$field['data'].'[2]"></select>';
        $str .= '&nbsp;<input '.$attr.' type="text" id="'.$field['key'].'_3" name="'.$field['data'].'[3]" placeholder="街道" value="' . $_content[3] . '" />';
        
        $pcas = 'new pcas("'.$field['key'].'_0", "'.$field['key'].'_1", "'.$field['key'].'_2", "'.$_content[0].'", "'.$_content[1].'", "'.$_content[2].'");';
        $str .= '<script type="text/javascript">'.$pcas.'</script>';
        
        return $str;
    }

    public static function content_password($field, $content = '')
    {
        $field = Field::content_field($field);

        if ($field['is_read']) {
            $field['attribute']['readonly'] = 'readonly';
        }
        return $field['is_show'] ? $content : '<input type="password" value="' . $content . '" ' . Field::content_attribute($field['attribute']) . ' />';
    }

    public static function content_textarea($field, $content = '')
    {
        $field = Field::content_field($field);

        if ($field['is_read']) {
            $field['attribute']['readonly'] = 'readonly';
        }
        return $field['is_show'] ? $content : '<textarea ' . Field::content_attribute($field['attribute']) . '>' . $content . '</textarea>';
    }

    public static function content_editor($field, $content = '')
    {
        return ueditor($name, $content);
    }

    public static function content_select($field, $content = '')
    {
        $field = Field::content_field($field);
        $str = '<select ' . Field::content_attribute($field['attribute']) . '>';
        foreach ($select as $t) {
            $n    = $v = $selected = '';
            list($n, $v) = explode('|', $t);
            $v    = is_null($v) ? trim($n) : trim($v);
            $selected = $v == $content ? ' selected' : '';
            $str.= "<option value='" . $v . "'" . $selected . ">" . $n . "</option>";
        }
        return $str . '</select>';
    }

    public static function content_dataset($field, $content = '')
    {
        $field   = Field::content_field($field);
        $setting = $field['setting'];

        if ($field['is_show']) {
            return $content;
        }

        $content = $content == 0 ? '' : $content;
        return \Dialog::user($setting['type'], $field['data'], $content);
    }

    public static function content_dialog($field, $content = '')
    {
        $field   = Field::content_field($field);
        $setting = $field['setting'];
        
        list($_name, $__name) = explode(':', $setting['name']);

        $dialog = Module::dialogs($setting['type']);

        $value = $content == 0 ? '' : $content;

        $rows = '';

        if ($value) {
            $ids = explode(',', $value);

            $table = $dialog['table'];
            $join  = $dialog['join'];

            if ($join) {
                $rows = DB::table($table)
                ->LeftJoin('user', 'user.id', '=', $table.'.user_id')
                ->whereIn($table.'.id', $ids)
                ->pluck($dialog['field'])->implode(',');
            } else {
                $rows = DB::table($table)
                ->whereIn('id', $ids)
                ->pluck($dialog['field'])->implode(',');
            }
        }

        $name = $field['data'];
        $id   = str_replace(['[',']'], ['_',''], $field['data']);

        $multi = (int)!$setting['single'];

        if ($field['is_show']) {
            return $rows;
        } else {
            if ($field['is_hide'] == 1) {
                return '<input type="hidden" value="' . $content . '" ' . Field::content_attribute($field['attribute']) . ' />';
            } else {
                $width = '100%';

                if ($field['is_read']) {
                    if ($setting['css'] == 'input-inline') {
                        $width = '153px';
                    }
                    if ($setting['width']) {
                        $width = $setting['width'].'px';
                    }

                    $html[] = '<div class="select-group" style="width:'.$width.';"><div class="form-control input-sm readonly" id="'.$id.'_text">'.$rows.'</div>';
                } else {
                    if ($setting['css'] == 'input-inline') {
                        $width = '225px';
                    }
                    if ($setting['width']) {
                        $width = $setting['width'].'px';
                    }

                    $option = "dialogUser('$dialog[name]','$dialog[url]','$id','$multi');";
                    $html[] = '<div class="select-group input-group" style="width:'.$width.';"><div class="form-control input-inline input-sm" style="cursor:pointer;" onclick="'.$option .'" id="'.$id.'_text">'.$rows.'</div>';
                    $html[] = '<div class="input-group-btn">';
                    $html[] = '<button type="button" onclick="'.$option.'" class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>';
                    $html[] = '<button type="button" onclick="selectClear(\''.$id.'\');" class="btn btn-sm btn-default"><i class="icon icon-trash"></i></button>';
                    $html[] = '</div>';
                }
                $html[] = '<input type="hidden" id="'.$id.'" name="'.$name.'" value="'.$value.'">';
                $html[] = '</div>';

                return join("\n", $html);
            }
        }
    }

    public static function content_option($field, $content = '')
    {
        $field   = Field::content_field($field);
        $setting = $field['setting'];

        // 子表
        if ($field['is_show']) {
            return option($setting['value'], $content);
        }

        if ($setting['single'] == 0) {
            $field['attribute']['multiple'] = 'multiple';
        }

        if ($field['is_read']) {
            $field['attribute']['readonly'] = 'readonly';
            $field['attribute']['onfocus']  = 'this.defaultIndex=this.selectedIndex;';
            $field['attribute']['onchange'] = 'this.selectedIndex=this.defaultIndex;';
        }

        $id = $field['attribute']['id'];

        $width = '100%';
        if ($setting['css'] == 'input-inline') {
            $width = '153px';
        }
        if ($setting['width']) {
            $width = $setting['width'].'px';
        }

        $placeholder_text_multiple = '请选择'.$field['name'];

        //$js = '<script>$(function() {$("#'.$id.'").chosenField({placeholder_text_multiple:" - ",width:"'.$width.'"}); });</script>';

        $str = $js.'<select ' . Field::content_attribute($field['attribute']) . '>';
        $str .= "<option value=''> - </option>";

        $options = option($setting['value']);
        foreach ($options as $option) {
            $selected = $option[$setting['id']] == $content ? ' selected' : '';
            $str.= "<option value='" . $option[$setting['id']] . "'" . $selected . ">" . $option[$setting['name']] . "</option>";
        }
        return $str . '</select>';
    }

    public static function content_date($field, $content = '')
    {
        $field   = Field::content_field($field);
        $setting = $field['setting'];

        /*
        $type      = isset($setting['type']) ? $setting['type'] : 'yyyy-MM-dd HH:mm:ss';
        $time_sign = array('yyyy'=>'Y','yy'=>'y','MM'=>'m','mm'=>'m','M'=>'M','m'=>'n','dd'=>'d','d'=>'j','DD'=>'l','D'=>'jS','W'=>'W','HH'=>'H','hh'=>'h','H'=>'G','h'=>'g','ii'=>'i','ss'=>'s','z'=>'z','c'=>'c','r'=>'r','a'=>'a','t'=>'t','A'=>'A');
        */

        $type = isset($setting['type']) ? $setting['type'] : 'Y-m-d H:i:s';
        $save = isset($setting['save']) ? $setting['save'] : 'date';

        $time_sign = array(
            'Y'  => 'yyyy',
            'y'  => 'yy',
            'm'  => 'mm',
            'm'  => 'MM',
            'M'  => 'M',
            'n'  => 'm',
            'd'  => 'dd',
            'j'  => 'd',
            'l'  => 'DD',
            'jS' => 'D',
            'W'  => 'W',
            'H'  => 'HH',
            'h'  => 'hh',
            'G'  => 'H',
            'g'  => 'h',
            'i'  => 'mm',
            's'  => 'ss',
            'z'  => 'z',
            'c'  => 'c',
            'r'  => 'r',
            'a'  => 'a',
            't'  => 't',
            'A'  => 'A'
        );
        $time_format = strtr($type, $time_sign);

        $content = empty($content) ? ($setting['default'] == 1 ? date($type): '') : ($save == 'date' ? $content : date($type, $content));

        if ($field['is_show']) {
            return $content;
        }

        if ($field['is_read']) {
            $field['attribute']['readonly'] = 'readonly';
        } else {
            $field['attribute']['onclick'] = "datePicker({dateFmt:'".$time_format."'});";
        }
        return '<input type="text" value="' .$content. '" ' . Field::content_attribute($field['attribute']) . ' />';
    }

    public static function content_radio($field, $content = '')
    {
        $field = Field::content_field($field);
        $str = '';
        foreach ($select as $t) {
            $attribute = $field['attribute'];
            $n    = $v = $selected = '';
            list($n, $v) = explode('|', $t);
            $v    = is_null($v) ? trim($n) : trim($v);
            if ($v == $content) {
                $attribute['checked'] = 'checked';
            }
            $str.= $n . '&nbsp;<input type="radio" name="'. $field['data'] . '" value="' . $v . '" ' . Field::content_attribute($attribute) . ' />&nbsp;&nbsp;';
        }
        return $str;
    }

    public static function content_checkbox($name, $content = '', $field = '')
    {
        // 配置
        $setting = isset($field['setting']) ? json_decode($field['setting'], true) : $field;
        $default = $setting['default'];
        $content = is_null($content) ? ($default ? explode(',', $default) : '') : string2array($content);
        $select  = explode(chr(13), $setting['content']);
        $str     = '';
        foreach ($select as $t) {
            $n    = $v = $selected = '';
            list($n, $v) = explode('|', $t);
            $v    = is_null($v) ? trim($n) : trim($v);
            $selected = is_array($content) && in_array($v, $content) ? ' checked' : '';
            $str.= $n . '&nbsp;<input type="checkbox" name="data[' . $name . '][]" value="' . $v . '" ' . $selected . ' />&nbsp;&nbsp;';
        }
        return $str;
    }

    public static function content_image($name, $content = '', $field = '')
    {
        // 配置
        $setting  = isset($field['setting']) ? json_decode($field['setting'], true) : $field;
        // 必填字段
        $required = isset($field['not_null']) && $field['not_null'] ? ' required' : '';
        $size     = (int)$setting['size'];
        $height   = isset($setting['height']) ? $setting['height'] : '';
        $width    = isset($setting['width']) ? $setting['width'] : '';
        $str      = '<input type="text" class="input-text" size="50" value="' . $content . '" name="data[' . $name . ']" id="fc_' . $name . '" ' . $required . ' />
	    <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="preview(\'fc_' . $name . '\')" value="' . trans('a-image') . '" />
	    <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="uploadImage(\'fc_' . $name . '\',\'' . $width . '\',\'' . $height . '\',\'' . $size . '\')" value="' . trans('a-mod-119') . '" />';
        return $str;
    }

    public static function content_file($name, $content = '', $field = '')
    {
        // 配置
        $setting  = isset($field['setting']) ? json_decode($field['setting'], true) : $field;
        // 必填字段
        $required = isset($field['not_null']) && $field['not_null'] ? ' required' : '';
        $type     = base64_encode($setting['type']);
        $size     = (int)$setting['size'];
        return '<input type="text" class="input-text" size="50" value="' . $content . '" name="data[' . $name . ']" id="fc_' . $name . '" ' . $required . ' />
	    <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="file_info(\'fc_' . $name . '\')" value="' . trans('a-mod-164') . '" />
	    <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="uploadFile(\'fc_' . $name . '\',\'' . $type . '\',\'' . $size . '\')" value="' . trans('a-mod-120') . '" />';
    }

    public static function content_files($field, $content = '')
    {
        $field   = Field::content_field($field);
        $setting = $field['setting'];

        $table = isset($setting['table']) && $setting['table'] ? $setting['table'] : 'attachment';

        $attachment = \Aike\Web\Index\Attachment::edit($content);

        $_setting = Setting::pluck('value', 'key');

        if ($field['is_read'] || $field['is_show']) {
            $str = '<div id="fileQueue" class="uploadify-queue">';

            if (count($attachment['main'])) {
                foreach ($attachment['main'] as $n => $file) {
                    $str .= '<div id="file_queue_'.$n.'" class="uploadify-queue-item">
                        <span class="file-name"><span class="icon icon-paperclip"></span> <a href="javascript:uploader.file(\'file_queue_'.$n.'\');">'.$file['name'].'</a></span>
                        <span class="file-size">('.human_filesize($file['size']).')</span>';

                    if (in_array($file['type'], ['pdf'])) {
                        $str .= '<a href="'.URL::to('uploads').'/'.$file['path'].'" class="btn btn-xs btn-default" target="_blank">预览</a>';
                    } elseif (in_array($file['type'], ['jpg','png','gif','bmp'])) {
                        $str .= '<a class="btn btn-xs btn-default" onclick="imageBox(\'preview\', \'附件预览\', \''.URL::to('uploads').'/'.$file['path'].'\');">预览</a>';
                    } else {
                        $str .= '<a class="btn btn-xs btn-default" href="'.url('index/attachment/download', ['id'=>$file['id']]).'">下载</a>';
                    }
                    $str .= '</div><div class="clear"></div>';
                }
            }

            $str .= '</div>';
            return $str;
        } else {
            $str = '<script id="uploader-item-tpl" type="text/html">
                <div id="file_draft_<%=fileId%>" class="uploadify-queue-item">
                    <span class="file-name"><span class="text-danger" title="草稿状态">!</span> <a href="javascript:uploader.file(\'file_draft_<%=id%>\');"><%=name%></a></span>
                    <span class="file-size">(<%=size%>)</span>
                    <span class="insert"><a class="option" href="javascript:uploader.insert(\'file_draft_<%=id%>\');">插入编辑器</a></span>
                    <span class="cancel"><a class="option" href="javascript:uploader.cancel(\'file_draft_<%=id%>\');">删除</a></span>
                    <input type="hidden" class="id" name="'. $field['data'] . '[]" value="<%=id%>" />
                </div>
                <div class="clear"></div>
            </script>
            <div class="uploadify-queue">
                <a class="btn btn-sm btn-info" href="javascript:viewBox(\'attachment\', \'文件上传\', \''.url('index/attachment/uploader', ['path' => \Request::module()]).'\');"><i class="fa fa-cloud-upload"></i> 文件上传</a>
                <span class="uploader-size">&nbsp;文件大小限制'.$_setting['upload_max'].' MB</span>
                <div class="clear"></div>
                <div id="fileQueue" class="uploadify-queue">';
                
            if (count($attachment['main'])) {
                foreach ($attachment['main'] as $n => $file) {
                    $str .= '<div id="file_queue_'.$n.'" class="uploadify-queue-item">
                            <span class="file-name"><span class="icon icon-paperclip"></span> <a href="javascript:uploader.file(\'file_queue_'.$n.'\');">'.$file['name'].'</a></span>
                            <span class="file-size">('.human_filesize($file['size']).')</span>
                            <span class="insert"><a class="option" href="javascript:uploader.insert(\'file_queue_'.$n.'\');">插入编辑器</a></span>
                            <span class="cancel"><a class="option" href="javascript:uploader.cancel(\'file_queue_'.$n.'\');">删除</a></span>
                            <input type="hidden" class="id" name="'. $field['data'] . '[]" value="'.$file['id'].'">
                        </div>
                        <div class="clear"></div>';
                }
            }

            $str .= '</div>
                <div id="fileQueueDraft">';

            if (count($attachment['draft'])) {
                foreach ($attachment['draft'] as $n => $file) {
                    $str .= '<div id="queue_draft_'.$n.'" class="uploadify-queue-item">
                            <span class="file-name"><span class="text-danger" title="草稿附件">!</span> <a href="javascript:uploader.file(\'queue_draft_'.$n.'\');">'.$file['name'].'</a></span>
                            <span class="file-size">('.human_filesize($file['size']).')</span>
                            <span class="insert"><a class="option" href="javascript:uploader.insert(\'queue_draft_'.$n.'\');">插入编辑器</a></span>
                            <span class="cancel"><a class="option" href="javascript:uploader.cancel(\'queue_draft_'.$n.'\');">删除</a></span>
                            <input type="hidden" class="id" name="'. $field['data'] . '[]" value="'.$file['id'].'">
                        </div>
                        <div class="clear"></div>';
                }
            }
            $str .= '</div></div>';
            return $str;
        }
    }
}
