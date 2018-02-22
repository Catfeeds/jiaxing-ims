<div class="panel">

<div class="wrapper">
    <a class="btn btn-info btn-sm" href="{{url('index',['model_id'=>$model_id])}}">字段管理</a>
</div>

<form class="form-horizontal" method="post" action="{{url()}}" id="myform" name="myform">
<table class="table table-form b-t m-b-none">
	<tr>
		<td align="right" width="10%">模型名</td>
		<td>
			<select class="form-control input-inline input-sm" name="model_id" id="model_id">
				@foreach($models as $v)
					<option value="{{$v['id']}}" @if($row['model_id'] == $v['id']) selected @endif>{{$v['name']}}</option>
				@endforeach
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">字段别名<span style="color:red;">*</span></td>
		<td><input class="form-control input-inline input-sm" type="text" name="name" value="{{$row['name']}}" id="name" onblur="app.pinyin('name','field');" required="required" />
			<span class="help-inline">例如文章主题。</span>
		</td>
	</tr>
	<tr>
		<td align="right">字段名<span style="color:red;">*</span></td>
		<td><input class="form-control input-inline input-sm" type="text" id="field" name="field" value="{{$row['field']}}" required="required" />
			<span class="help-inline">只能由英文字母、数字和下划线组成，并且仅能字母开头。</span>
	</tr>

	<tr>
		<td align="right">字段类别<span style="color:red;">*</span></td>
		<td>
			<select class="form-control input-inline input-sm" name="form_type" id="form_type" onchange="formType(this.value)" required="required">
				<option value=""> - </option>
				{{:$fields = Aike\Web\Flow\Field::title()}}
				@foreach($fields as $id => $field)
					<option value="{{$id}}" @if($row['form_type'] == $id) selected @endif>{{$field}}</option>
				@endforeach
			</select>
		</td>
	</tr>

	<tbody id="content">
		@if($row['id'])
			<?php
                $setting = json_decode($row['setting'], true);
                $function = "form_".$row['form_type'];
            ?>
			{{Aike\Web\Flow\Field::$function($setting)}}
		@endif

	</tbody>

	<!--
	<tbody id="control">
		<tr id="optionTR" class="hide">
          <td align="right">选项</td>
          <td>
            <div class="input-group">
              <input type="text" name="options[value][]" id="options[value][]" value="" class="form-control input-sm" placeholder="选项值的代码，可以使用字母跟数字组合" />
              <span class="input-group-addon bg-white b-lr-n">:</span>
              <input type="text" name="options[text][]" id="options[text][]" value="" class="form-control input-sm" placeholder="选项的文字说明" />
              <span class="input-group-btn">
                <a href="javascript:;" class="btn btn-sm btn-default option-add"><i class="icon-plus">+</i></a>
				<a href="javascript:;" class="btn btn-sm btn-default option-del"><i class="icon-minus">-</i></a>
              </span>
            </div> 
          </td>
        </tr>

        <tr id="sqlTR" class="hide">
            <td align="right">SQL</td>
            <td>
                <textarea name="sql" id="sql" rows="4" class="form-control input-sm" placeholder="直接写入一条SQL查询语句，只能进行查询操作，禁止其他SQL操作。查询结果是键值对。查询语句的第一个字段作为键，第二个字段作为值，其它字段会被忽略。如果只有一个字段，这个字段同时作为键和值。"></textarea>
            </td>
        </tr>

		<div id="optionTpl" class="hide">
			<div class="input-group">
				<input type="text" name="options[value][]" id="options[value][]" value="" class="form-control input-inline input-sm" placeholder=选项值的代码，可以使用字母跟数字组合 />
				<span class="input-group-addon bg-white b-lr-n">:</span>
				<input type="text" name="options[text][]" id="options[text][]" value="" class="form-control input-inline input-sm" placeholder=选项的文字说明 />
				<div class="input-group-btn">
					<a href="javascript:;" class="btn btn-sm btn-default option-add"><i class="icon-plus">+</i></a>
					<a href="javascript:;" class="btn btn-sm btn-default option-del"><i class="icon-minus">-</i></a>
				</div>
			</div> 
		</div>
	</tbody>
	-->

	<tbody id="hidetbody">
		<tr>
			<td align="right">字段类型<span style="color:red;">*</span></td>
			<td>
				<select class="form-control input-inline input-sm" name="type" onchange="setlength(this.value)" id="type">
					<option value="">-</option>
					<option value="BIGINT" @if($row['type'] == 'BIGINT') selected @endif>十位整型(BIGINT)</option>
					<option value="INT" @if($row['type'] == 'INT') selected @endif>十位整型(INT)</option>
					<option value="MEDIUMINT" @if($row['type'] == 'MEDIUMINT') selected @endif>八位整型(MEDIUMINT)</option>
					<option value="SMALLINT" @if($row['type'] == 'SMALLINT') selected @endif>五位整型(SMALLINT)</option>
					<option value="TINYINT" @if($row['type'] == 'TINYINT') selected @endif>三位整型(TINYINT)</option>
					<option value="">-</option>
					<option value="DECIMAL" @if($row['type'] == 'DECIMAL') selected @endif>小数类型(DECIMAL)</option>
					<option value="">-</option>
					<option value="DATE" @if($row['type'] == 'DATE') selected @endif>日期类型(DATE)</option>
					<option value="DATETIME" @if($row['type'] == 'DATETIME') selected @endif>时间类型(DATETIME)</option>
					<option value="CHAR" @if($row['type'] == 'CHAR') selected @endif>字符类型(CHAR)</option>
					<option value="VARCHAR" @if($row['type'] == 'VARCHAR') selected @endif>文字类型(VARCHAR)</option>
					<option value="TEXT" @if($row['type'] == 'TEXT') selected @endif>文本类型(TEXT)</option>
				</select>
				<span class="help-inline">请慎重，修改类型可能导致数据丢失</span>
			</td>
		</tr>
		<tr>
			<td align="right">字段长度<span style="color:red;">*</span></td>
			<td>
				<input class="form-control input-inline input-sm" type="text" id="length" name="length" value="{{$row['length']}}">
				<span class="help-inline">注意长度值不能超界</span>
			</td>
		</tr>
		<tr>
			<td align="right">字段索引</td>
			<td>
				<select class="form-control input-inline input-sm" name="index">
					<option value=""> - </option>
					<option value="UNIQUE" @if($row['index'] == 'UNIQUE') selected @endif>唯一索引</option>
					<option value="INDEX" @if($row['index'] == 'INDEX') selected @endif>普通索引</option>
				</select>
				<span class="help-inline">（可选）请慎重，必须理解索引的概念</span>
			</td>
		</tr>
	</tbody>
	<tr>
		<td align="right">字段提示</td>
		<td>
			<input class="form-control input-inline input-sm" type="text" name="tips" value="{{$row['tips']}}">
			<span class="help-inline">显示在字段别名下方作为表单输入提示</span>
		</td>
	</tr>
	
	<!--
	<tr>
		<td align="right">验证规则</td>
		<td>
			<select name="validate[]" data-placeholder="选择验证规则" multiple class="form-control inline-xs-4 input-inline input-sm chosen-select">
				@foreach($regulars as $key => $regular)
					<option value="{{$key}}" @if(in_array($key, (array)$row['validate'])) selected @endif>{{$regular}}</option>
				@endforeach
			</select>
			<span class="help-inline">通过此正则校验表单提交的数据合法性，如果不想校验数据请留空</span>
		</td>
	</tr>
	-->
	
	<tr>
		<td align="right">字段隐藏</td>
		<td>
			<label class="radio-inline"><input type="radio" @if($row['is_hide'] == 0) checked @endif value="0" name="is_hide"> 否 </label>
			<label class="radio-inline"><input type="radio" @if($row['is_hide'] == 1) checked @endif value="1" name="is_hide"> 是 </label>
		</td>
	</tr>
	
	<tr>
		<td align="right">字段排序</td>
		<td>
			<label class="radio-inline"><input type="radio" @if($row['is_sort'] == 0) checked @endif value="0" name="is_sort"> 否 </label>
			<label class="radio-inline"><input type="radio" @if($row['is_sort'] == 1) checked @endif value="1" name="is_sort"> 是 </label>
		</td>
	</tr>
	<tr>
		<td align="right">字段搜索</td>
		<td>
			<label class="radio-inline"><input type="radio" @if($row['is_search'] == 0) checked @endif value="0" name="is_search"> 否 </label>
			<label class="radio-inline"><input type="radio" @if($row['is_search'] == 1) checked @endif value="1" name="is_search"> 是 </label>
		</td>
	</tr>
	<tr>
		<td align="right">列表显示</td>
		<td>
			<label class="radio-inline"><input type="radio" @if($row['is_index'] == 0) checked @endif value="0" name="is_index"> 否 </label>
			<label class="radio-inline"><input type="radio" @if($row['is_index'] == 1) checked @endif value="1" name="is_index"> 是 </label>
		</td>
	</tr>
	<!--
	<tr>
		<td align="right">添加显示</td>
		<td>
			<label class="radio-inline"><input type="radio" @if($row['is_create'] == 0) checked @endif value="0" name="is_create"> 否 </label>
			<label class="radio-inline"><input type="radio" @if($row['is_create'] == 1) checked @endif value="1" name="is_create"> 是 </label>
		</td>
	</tr>
	<tr>
		<td align="right">字段只读</td>
		<td>
			<label class="radio-inline"><input type="radio" @if($row['is_read'] == 0) checked @endif value="0" name="is_read"> 否 </label>
			<label class="radio-inline"><input type="radio" @if($row['is_read'] == 1) checked @endif value="1" name="is_read"> 是 </label>
		</td>
	</tr>
	-->
	<tr>
		<td align="left" colspan="2">
			<input name="id" type="hidden" value="{{$row['id']}}">
			<input name="parent_id" type="hidden" value="{{$parent_id}}">
			<button type="button" onclick="history.back();" class="btn btn-default">返回</button>
			<button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</button>
		</td>
	</tr>

</table>

</form>

</div>

<script type="text/javascript">
$(function() {

	$(".chosen-select").chosen({width:"200px"});

	var css      = 0;
	var value    = 0;
	var optionTR = 0;
	var sql      = 0;
	var text     = 0;
	var textarea = 0;

	switch(css) {
		case 'text':
			$('#css').show();
			break;
		case 2:
			break;
		default:
	}

    /* Toggle options. */
    $('#control').change(function()
    {   
        var control = $(this).val();

        $('.optionType').toggle(control == 'select');

        $('#optionType').change(function()
        {
            $('#optionTR').toggle(control == 'select' && $(this).val() == 'custom');
            $('#sqlTR').toggle(control == 'select' && $(this).val() == 'sql');
            $('#varsTR').toggle(control == 'select' && $(this).val() == 'sql' && $.trim($('#varsTD').html()) != '');
        })

        $('#optionTR').toggle((control == 'select' && $('#optionType').val() == 'custom') || control == 'radio' || control == 'checkbox');
        $('#sqlTR').toggle(control == 'select' && $('#optionType').val() == 'sql');
        $('#varsTR').toggle(control == 'select' && $('#optionType').val() == 'sql' && $.trim($('#varsTD').html()) != '');
    }); 

    $('#control').change();

    /* Add a option. */
    $('#optionTR').on('click', '.option-add', function()
    {
        $(this).parents('.input-group').after($('#optionTpl').html());
    }); 

    /* Delete a option. */
    $('#optionTR').on('click', '.option-del', function()
    {   
        if($(this).parents('td').find('div.input-group').size() == 1)
        {   
            $(this).parents('.input-group').find('input').val('');
        }   
        else
        {   
            $(this).parents('.input-group').remove();
        }   
    }); 

    $(document).on('click', '[name=requestType]', function()
    {
        $('#selectList').toggle($(this).val() == 'select');
    });

    $(document).on('click', '.delSqlVar', function()
    {
        $('#sql').val($('#sql').val().replace(' $' + $(this).parents('.varControl').attr('id') + ' ', ''));
        $(this).parents('.varControl').remove();
        fixVarControls();
    });
});

function formType(type)
{
	$('#optionTR').toggle(type == 'dialog');
    $("#content").html('loading...');
	$.get("{{url('type')}}?type="+type, function(data) {
		$("#content").html(data);
	});
}
function setlength(value) {
	var type = {
		BIGINT:'10',
		INT:'10',
		MEDIUMINT:'8',
		SMALLINT:'5',
		TINYINT:'3',
		DECIMAL:'10,2',
		VARCHAR:'255',
		CHAR:'50',
		DATE:'',
		DATETIME:'',
		TEXT:'0',
	};
	if (value) {
		$('#length').val(type[value]);
	}
}
</script>
