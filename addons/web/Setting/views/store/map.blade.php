<style>
#allmap {
    width: 100%;
    height: 420px;
    overflow: hidden;
    margin:0;font-family:"微软雅黑";
}
.BMap_Marker img {
    display: none;
}
#myPageTop {
    position: absolute;
    top: 5px;
    right: 10px;
    background: #fff none repeat scroll 0 0;
    border: 1px solid #ccc;
    margin: 10px auto;
    padding:6px;
    font-family: "Microsoft Yahei", "微软雅黑", "Pinghei";
    font-size: 14px;
}
#myPageTop label {
    margin: 0 20px 0 0;
    color: #666666;
    font-weight: normal;
}
#myPageTop input {
    width: 170px;
}
#myPageTop .column2{
    padding-left: 25px;
}
</style>

<div id="allmap"></div>
<div id="myPageTop">
<table>
    <tr>
        <!--<td>-->
        <!--<label>按关键字搜索：</label>-->
        <!--</td>-->
        <td class="column2">
            <label>左击获取经纬度：</label>
        </td>
    </tr>
    <tr>
        <!--<td>-->
        <!--<input type="text" placeholder="请输入关键字进行搜索" id="tipinput">-->
        <!--</td>-->
        <td class="column2">
            <input type="text" readonly="true" id="lnglat">
        </td>
    </tr>
</table>
</div>
<script type="text/javascript">
$(function() {
    var map = new BMap.Map("allmap", {
        resizeEnable: true
    });
    map.centerAndZoom(new BMap.Point(103.850875,30.069575), 11);
    map.enableScrollWheelZoom();
    map.enableContinuousZoom();

    var local = new BMap.LocalSearch(map, {
		renderOptions:{map: map}
    });
    var addr = $("#address").val();
    local.search(addr);
    
    function showInfo(e) {
        document.getElementById("lnglat").value = e.point.lng+ ',' + e.point.lat;
        $("#lat").val(e.point.lng);
        $("#lng").val(e.point.lat);
    }
    map.addEventListener("click", showInfo);
});
</script>