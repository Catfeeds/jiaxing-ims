<div class="wrapper-sm">

    <form method="post" class="form-horizontal" action="{{url()}}" id="user-group-form" name="user_group_form">

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">门店名称</label>
            <div class="col-sm-10">
                <input type="text" id="name" name="name" class="form-control input-sm" value="{{$row['name']}}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="tel">门店电话</label>
            <div class="col-sm-10">
                <input type="text" id="tel" name="tel" class="form-control input-sm" value="{{$row['tel']}}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="address">门店地址</label>
            <div class="col-sm-10">

                <div class="input-group">
                <input type="text" id="address" name="address" class="form-control input-sm" value="{{$row['address']}}">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" id="click_map"><i class="fa fa-map-marker"></i> 获取经纬度</button>
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="lat">门店经度</label>
            <div class="col-sm-10">
                <input type="text" id="lat" name="lat" class="form-control input-sm" value="{{$row['lat']}}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="lng">门店纬度</label>
            <div class="col-sm-10">
                <input type="text" id="lng" name="lng" class="form-control input-sm" value="{{$row['lng']}}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="range_type">范围方式</label>
            <div class="col-sm-10">
                <label class="radio-inline"><input type="radio" name="range_type" class="range_type_radio" value="0" @if($row['range_type'] == '0') checked @endif>按区域</label>
                <label class="radio-inline"><input type="radio" name="range_type" class="range_type_radio" value="1" @if($row['range_type'] == '1') checked @endif>按距离</label>
            </div>
        </div>

        <div class="form-group range_type" id="range_type_0" style="display:none;">
            <label class="col-sm-2 control-label" for="lng">服务区域</label>
            <div class="col-sm-10">
                <input type="text" id="area" name="area" class="form-control input-sm" value="{{$row['area']}}">
            </div>
        </div>

        <div class="form-group range_type" id="range_type_1" style="display:none;">
            <label class="col-sm-2 control-label" for="lng">服务距离</label>
            <div class="col-sm-10">
                <input type="text" id="distance" name="distance" class="form-control input-sm" value="{{$row['distance']}}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="lng">营业时间 <i class="fa fa-question-circle hinted" title="注：如 8:00-9:00"></i></label>
            <div class="col-sm-10">
                <input type="text" id="start_time" name="start_time" class="form-control input-sm input-inline" value="{{$row['start_time']}}" />
                 - 
                <input type="text" id="end_time" name="end_time" class="form-control input-sm input-inline" value="{{$row['end_time']}}" /> 
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="bank_card">银行卡号</label>
            <div class="col-sm-10">
                <input type="text" id="bank_card" name="bank_card" class="form-control input-sm" value="{{$row['bank_card']}}" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="bank_card">门店图片</label>
            <div class="col-sm-10">
                <input type="text" id="image" name="image" class="form-control input-sm" value="{{$row['image']}}" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="use_share">共享权限 </label>
            <div class="col-sm-10">
                <label class="checkbox-inline"><input type="checkbox" id="use_share" name="use_share" value="{{$row['use_share']}}" /> 分店是否接收共享产品和服务</label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="remark">备注</label>
            <div class="col-sm-10">
                <textarea cols="2" id="remark" name="remark" class="form-control input-sm"></textarea>
            </div>
        </div>

        <input type="hidden" name="id" value="{{$row['id']}}">
            
    </form>
</div>

<script type="text/javascript">
var range_type = "{{$row['range_type']}}";
$(function() {
    $('#range_type_' + range_type).show();
    $('.range_type_radio').on('click', function() {
        $('.range_type').hide();
        $('#range_type_' + $(this).val()).show();
    });

    $('#click_map').on('click', function() {
        viewDialog('获取经纬度', '{{url("map")}}');
        $('#range_type_' + $(this).val()).show();
    });

});
</script>