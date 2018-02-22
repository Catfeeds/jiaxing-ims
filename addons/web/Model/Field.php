<?php namespace Aike\Web\Model;

use Aike\Web\Index\BaseModel;
use Schema;

class Field extends BaseModel
{
    protected $table = 'model_field';

    public function model()
    {
        return $this->belongsTo('Aike\Web\Model\Model');
    }

    public function title()
    {
        return [
            'text'     => '单行文本',
            'textarea' => '多行文本',
            'password' => '密码文本',
            'radio'    => '单选按钮',
            'select'   => '下拉菜单',
            'checkbox' => '复选框',
            'dialog'   => '对话框',
            'auto'     => '宏控件',
            'calc'     => '计算控件',
            'editor'   => '编辑器',
            'date'     => '日期时间',
            'image'    => '单图上传',
            'file'     => '文件上传',
            'files'    => '多文件上传',
        ];
    }

    public function tr_align($setting = '')
    {
        $setting['align'] = isset($setting['align']) ? $setting['align'] : '';
        $rows = ['left' => 'left', 'center' => 'center', 'right' => 'right'];
        return Field::tr_select($rows, 'align', 'setting[align]', $setting['align']);
    }

    public function tr_select($data, $title, $name, $value)
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
    public function form_text($setting = '')
    {
        return '<tr>
	      	<td align="right">长度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['size']) ? $setting['size'] : '100') . '" name="setting[size]"> <font color="gray">px</font></td>
	    </tr>
        <tr>
	      	<td align="right">CSS</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['css']) ? $setting['css'] : '') . '" name="setting[css]"> <font color="gray">input-inline</font></td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]"></td>
	    </tr>';
    }

    public function form_calc($setting = '')
    {
        return '<tr>
	      	<td align="right">长度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['size']) ? $setting['size'] : '100') . '" name="setting[size]"> <font color="gray">px</font></td>
	    </tr>
        <tr>
	      	<td align="right">CSS</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['css']) ? $setting['css'] : '') . '" name="setting[css]"> <font color="gray">input-inline</font></td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . (isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]"></td>
	    </tr>';
    }

    public function form_auto($setting = '')
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
	    </tr>';
        $str .= Field::tr_align($setting);
        $str .= '<tr>
	      	<td align="right">默认值</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' .(isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]"></td>
	    </tr>';
        return $str;
    }

    public function form_password($setting = '')
    {
        return '<tr>
	      	<td align="right">长度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' .(isset($setting['size']) ? $setting['size'] : '100') . '" name="setting[size]"><font color="gray">px</font></td>
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' .(isset($setting['default']) ? $setting['default'] : '') . '" name="setting[default]"></td>
	    </tr>';
    }

    public function form_textarea($setting = '')
    {
        return '<tr>
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
	    </tr>
		<tr>
	      	<td align="right">默认值</td>
	      	<td><textarea name="setting[default]" rows="2" class="form-control inline-xs-4 input-sm">' . (isset($setting['default']) ? $setting['default'] : '') . '</textarea></td>
	    </tr>';
    }

    public function form_editor($setting = '')
    {
        $t = isset($setting['type']) && $setting['type'] ?  1 : (!isset($setting['type']) ? 1 : 0);
        $w = isset($setting['width'])  ? $setting['width']  : '100';
        $h = isset($setting['height']) ? $setting['height'] : '300';
        return '<tr>
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
	    </tr>
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
    }

    public function form_select($setting = '')
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

    public function form_radio($setting = '')
    {
        return Field::form_select($setting);
    }

    public function form_checkbox($setting = '')
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

    public function form_image($setting = '')
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

    public function form_file($setting = '')
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

    public function form_files($setting = '')
    {
        return Field::form_file($setting);
    }

    // 选择控件
    public function form_dialog($setting = '')
    {
        $single = isset($setting['single']) && $setting['single'] == 1 ?  1 : (!isset($setting['single']) ? 1 : 0);
        $type   = isset($setting['type']) && $setting['type'] ? $setting['type']  : 'user';
        $width  = isset($setting['width']) && $setting['width'] ? $setting['width'] : 150;
        $rows   = array(
            'user'       => '用户列表',
            'position'   => '职位列表',
            'role'       => '角色列表',
            'department' => '部门列表',
            'product'    => '产品列表',
            'custom'     => '自定义',
            'sql'        => 'SQL',
        );
        $str = '<tr>
	    <td align="right">数据源</td><td><select class="form-control input-inline input-sm" name="setting[type]">';
        foreach ($rows as $key => $row) {
            $selected = isset($setting['type']) && $key == $setting['type'] ? ' selected' : '';
            $str .= '<option value="'.$key.'"'.$selected.'>'.$row.'</option>';
        }
        $str .= '</select></td></tr>
        
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

    public function form_date($setting = '')
    {
        $type_option  = isset($setting['type_option']) && $setting['type_option']   ? $setting['type_option']  : 'yyyy-MM-dd HH:mm:ss';
        $type_display = isset($setting['type_display']) && $setting['type_display'] ? $setting['type_display'] : 'Y-m-d H:i:s';
        $width = isset($setting['width']) && $setting['width'] ? $setting['width'] : 160;
        return '<tr>
	      	<td align="right">宽度</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . $width . '" name="setting[width]">
	      		<font color="gray">px</font>
	      	</td>
	    </tr>
		<tr>
	      	<td align="right">操作格式</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . $type_option . '" name="setting[type_option]">
	      		<font color="gray">格式: yyyy-MM-dd HH:mm:ss 表示: 2001-02-13 11:20:20</font>
	      	</td>
	    </tr>
		<tr>
	      	<td align="right">显示格式</td>
	      	<td><input type="text" class="form-control input-inline input-sm" value="' . $type_display . '" name="setting[type_display]">
	      		<font color="gray">格式: Y-m-d H:i:s 表示: 2001-02-13 11:20:20</font>
	      	</td>
	    </tr>
		<tr>
	    	<td align="right">默认值</td>
	    	<td>
	      		<label class="radio-inline"><input type="radio" value=1 name="setting[default]" ' . (isset($setting['default']) && $setting['default'] == 1 ? 'checked' : '') . '> 当前时间</label>
		  		<label class="radio-inline"><input type="radio" value=0 name="setting[default]"' . (isset($setting['default']) && $setting['default'] == 0 ? 'checked' : '') . '> 空</label>
	    	</td>
	    </tr>';
    }

    public function get_content_value($content)
    {
        return $content;
    }

    /**
     * 以下函数作用于发布内容部分
     */

    public function content_input($name, $content = '', $field = '')
    {
        $setting    = isset($field['setting']) ? string2array($field['setting']) : $field;    //配置
        $pattern    = isset($field['pattern']) && $field['pattern'] ? ' pattern="' . $field['pattern'] . '"' : '';    //正则判断
        $required    = isset($field['not_null']) && $field['not_null'] ? ' required' : '';    //必填字段
        $content    = is_null($content) ? $setting['default'] : $content;
        $style        = isset($setting['size']) ? " style='width:" . ($setting['size'] ? $setting['size'] : 150) . "px;'": '';
        return '<input type="text" value="' . $content . '" class="input-text" name="data[' . $name . ']" ' . $style . ' ' . $required . $pattern . ' />';
    }

    public function content_user($name, $content = '', $field = '')
    {
        $setting    = isset($field['setting']) ? string2array($field['setting']) : $field;    //配置
        $pattern    = isset($field['pattern']) && $field['pattern'] ? ' pattern="' . $field['pattern'] . '"' : '';    //正则判断
        $required    = isset($field['not_null']) && $field['not_null'] ? ' required' : '';    //必填字段
        $content    = is_null($content) ? $setting['default'] : $content;
        $style        = isset($setting['size']) ? " style='width:" . ($setting['size'] ? $setting['size'] : 150) . "px;'": '';
        
        $name = 'data['.$field['field'].']';
        return html::selectBox($setting['type'], $field['field'], $name, $content, $setting['single']);
    }

    public function content_password($name, $content = '', $field = '')
    {
        $setting    = isset($field['setting']) ? string2array($field['setting']) : $field;    //配置
        $content    = is_null($content) ? $setting['default'] : $content;
        $required    = isset($field['not_null']) && $field['not_null'] ? ' required' : '';    //必填字段
        $style        = isset($setting['size']) ? " style='width:" . ($setting['size'] ? $setting['size'] : 150) . "px;'": '';
        return '<input type="password" value="' . $content . '" class="input-text" name="data[' . $name . ']" ' . $style . ' ' . $required . ' />';
    }

    public function content_textarea($name, $content = '', $field = '')
    {
        $setting    = isset($field['setting']) ? string2array($field['setting']) : $field;    //配置
        $pattern    = isset($field['pattern']) && $field['pattern'] ? ' pattern="' . $field['pattern'] . '"' : '';    //正则判断
        $required    = isset($field['not_null']) && $field['not_null'] ? ' required' : '';    //必填字段
        $content    = is_null($content) ? $setting['default'] : $content;    //内容
        $style        = isset($setting['width']) && $setting['width'] ? 'width:' . $setting['width'] . 'px;' : '';    //宽度
        $style        .= isset($setting['height']) && $setting['height'] ? 'height:' . $setting['height'] . 'px;' : '';    //高度
        return '<textarea style="' . $style . '" name="data[' . $name . ']" ' . $required . $pattern . '>' . $content . '</textarea>';
    }

    public function content_editor($name, $content = '', $field = '')
    {
        return ueditor($name, $content);
    }

    public function content_select($name, $content = '', $field = '')
    {
        $setting    = isset($field['setting']) ? string2array($field['setting']) : $field;    //配置
        $content    = is_null($content) ? $setting['default'] : $content;
        $required    = isset($field['not_null']) && $field['not_null'] ? ' required' : '';    //必填字段
        $select    = explode(chr(13), $setting['content']);
        $str    = "<select id='fc_" . $name . "' name='data[" . $name . "]' " . $required . ">";
        foreach ($select as $t) {
            $n    = $v = $selected = '';
            list($n, $v) = explode('|', $t);
            $v    = is_null($v) ? trim($n) : trim($v);
            $selected = $v == $content ? ' selected' : '';
            $str.= "<option value='" . $v . "'" . $selected . ">" . $n . "</option>";
        }
        return $str . '</select>';
    }

    public function content_radio($name, $content = '', $field = '')
    {
        $setting    = isset($field['setting']) ? string2array($field['setting']) : $field;    //配置
        $content    = is_null($content) ? $setting['default'] : $content;
        $required    = isset($field['not_null']) && $field['not_null'] ? ' required' : '';    //必填字段
        $select    = explode(chr(13), $setting['content']);
        $str    = '';
        foreach ($select as $t) {
            $n    = $v = $selected = '';
            list($n, $v) = explode('|', $t);
            $v    = is_null($v) ? trim($n) : trim($v);
            $selected = $v==$content ? ' checked' : '';
            $str.= $n . '&nbsp;<input type="radio" name="data[' . $name . ']" value="' . $v . '" ' . $selected . ' ' . $required . '/>&nbsp;&nbsp;';
        }
        return $str;
    }

    public function content_checkbox($name, $content = '', $field = '')
    {
        $setting    = isset($field['setting']) ? string2array($field['setting']) : $field;    //配置
        $default    = $setting['default'];
        $content    = is_null($content) ? ($default ? explode(',', $default) : '') : string2array($content);
        $select    = explode(chr(13), $setting['content']);
        $str    = '';
        foreach ($select as $t) {
            $n    = $v = $selected = '';
            list($n, $v) = explode('|', $t);
            $v    = is_null($v) ? trim($n) : trim($v);
            $selected = is_array($content) && in_array($v, $content) ? ' checked' : '';
            $str.= $n . '&nbsp;<input type="checkbox" name="data[' . $name . '][]" value="' . $v . '" ' . $selected . ' />&nbsp;&nbsp;';
        }
        return $str;
    }

    public function content_image($name, $content = '', $field = '')
    {
        $setting  = isset($field['setting']) ? string2array($field['setting']) : $field;    //配置
        $required    = isset($field['not_null']) && $field['not_null'] ? ' required' : '';    //必填字段
        $size    = (int)$setting['size'];
        $height    = isset($setting['height']) ? $setting['height'] : '';
        $width    = isset($setting['width']) ? $setting['width'] : '';
        $str    = '<input type="text" class="input-text" size="50" value="' . $content . '" name="data[' . $name . ']" id="fc_' . $name . '" ' . $required . ' />
	    <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="preview(\'fc_' . $name . '\')" value="' . trans('a-image') . '" />
	    <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="uploadImage(\'fc_' . $name . '\',\'' . $width . '\',\'' . $height . '\',\'' . $size . '\')" value="' . trans('a-mod-119') . '" />';
        return $str;
    }

    public function content_file($name, $content = '', $field = '')
    {
        $setting    = isset($field['setting']) ? string2array($field['setting']) : $field;    //配置
        $required    = isset($field['not_null']) && $field['not_null'] ? ' required' : '';    //必填字段
        $type    = base64_encode($setting['type']);
        $size    = (int)$setting['size'];
        return '<input type="text" class="input-text" size="50" value="' . $content . '" name="data[' . $name . ']" id="fc_' . $name . '" ' . $required . ' />
	    <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="file_info(\'fc_' . $name . '\')" value="' . trans('a-mod-164') . '" />
	    <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="uploadFile(\'fc_' . $name . '\',\'' . $type . '\',\'' . $size . '\')" value="' . trans('a-mod-120') . '" />';
    }

    public function content_files($name, $content = '', $field = '')
    {
        $setting = isset($field['setting']) ? string2array($field['setting']) : $field;    //配置
        $table = $setting['table'] ? $setting['table'] : 'attachment';
        $queue_id = 'uploadify_'.$field['id'];
        $config = config('config');
        $asset_url = asset_url();
        $uploadifySettings = json_encode(array('id'=>$field['id'], 'PHPSESSID' => session_id(), 'model'=> $table, 'path'=> $setting['path'], 'upload_type'=> $config['upload_type'], 'upload_max' => $config['upload_max']*1024*1024));
        
        $str = '';
        if (defined('WANG_FILES_LD') == false) {
            $str .= '<link href="'.$asset_url.'vendor/uploadify/single.css" rel="stylesheet" type="text/css" />
			<script type="text/javascript" src="'.$asset_url.'vendor/swfobject.js"></script>
			<script type="text/javascript" src="'.$asset_url.'vendor/uploadify/swfupload.js"></script>
			<script type="text/javascript" src="'.$asset_url.'vendor/uploadify/jquery.uploadify.3.2.js?ver='.time().'"></script>
			<script type="text/javascript" src="'.$asset_url.'vendor/uploadify/uploadify.js"></script>';
            define('WANG_FILES_LD', true);
        }

        $str .= '
		<script type="text/javascript">$(document).ready(function(){uploadify('.$uploadifySettings.');});</script>
		<input type="file" name="'.$queue_id.'" id="'.$queue_id.'" />
		<div class="uploadify-info">支持格式 ('.$config['upload_type'].')，单文件最大'.str::fileSize($config["upload_max"]*1024*1024).'</div>
		<div class="clear"></div>
		<div id="fileQueue_'.$field['id'].'">';

        $queue = DB::table($table)->queue($content);
        if ($queue) {
            foreach ($queue as $n => $row) {
                $view   = "uploadifyView('{$queue_id}','file_queue_{$row['id']}');";
                $cancel = "uploadifyCancel('{$queue_id}','file_queue_{$row['id']}');";

                $str .= '<div id="file_queue_'.$row['id'].'" class="uploadify-queue-item">
		        <span class="fileName"><a onclick="'.$view.'" href="javascript:;">'.$row['title'].'</a></span>
		        <span class="fileSize">('.str::fileSize($row["size"]).')</span>
		        <span class="cancel"><a onclick="'.$cancel.'" href="javascript:;">删除</a></span>
		        <input type="hidden" class="id" name="data['.$field['field'].'][]" value="'.$row['id'].'" />
		    	</div><div class="clear"></div>';
            }
        }

        // 读取草稿
        $draft = DB::table($table)->draft(Auth::id());

        if ($draft && defined('WANG_FILES_DRAFT_LD') == false) {
            foreach ($draft as $n => $row) {
                $view   = "uploadifyView('{$queue_id}','queue_draft_{$row['id']}');";
                $cancel = "uploadifyCancel('{$queue_id}','queue_draft_{$row['id']}');";

                $str .= '<div id="queue_draft_'.$row['id'].'" class="uploadify-queue-item">
			    <span class="fileName"><strong class="orange" title="草稿附件">!</strong> <a onclick="'.$view.'" href="javascript:;">'.$row['title'].'</a></span>
				<span class="fileSize">('.str::fileSize($row["size"]).')</span>
				<span class="cancel"><a onclick="'.$cancel.'" href="javascript:;">删除</a></span>
				<input type="hidden" class="id" name="data['.$field['field'].'][]" value="'.$row['id'].'" />
				</div><div class="clear"></div>';
            }
            define('WANG_FILES_DRAFT_LD', true);
        }
        $str .= '</div>';
        return $str;
    }

    public function content_date($name, $content = '', $field = '')
    {
        $setting  = isset($field['setting']) ? string2array($field['setting']) : $field; //配置
        $type     = isset($setting['type']) ? $setting['type'] : 'yyyy-MM-dd HH:mm:ss';
        $width    = isset($setting['width']) ? $setting['width'] : 160;
        
        $time_sign   = array('yyyy'=>'Y','yy'=>'y','MM'=>'m','mm'=>'m','M'=>'M','m'=>'n','dd'=>'d','d'=>'j','DD'=>'l','D'=>'jS','W'=>'W','HH'=>'H','hh'=>'h','H'=>'G','h'=>'g','ii'=>'i','ss'=>'s','z'=>'z','c'=>'c','r'=>'r','a'=>'a','t'=>'t','A'=>'A');
        $time_format = strtr($type, $time_sign);

        $content = empty($content) ? ($setting['default'] == 1 ? date($time_format): '') : date($time_format, $content);

        $onclick  = "datePicker({dateFmt:'".$type."'});";
        return '<input type="text" onclick="'.$onclick.'" name="data['.$field['field'].']" readonly="" class="date input-text" style="width:'. $width .'px;" value="' .$content. '" id="'. $field['field'] .'" />';
    }
}
