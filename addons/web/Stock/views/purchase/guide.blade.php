<style>
    /*库存*/
.kus{
	margin-top: 74px;
}
.kus img{
	width: 60%;
	margin-left: 79px;
}
.kus img{
	-webkit-transform:scale(1,1);
	-webkit-transition-duration: 0.5s;
}


.kus img:hover{
	-webkit-transform:scale(1.05,1.05);
	-webkit-transition-duration: 0.5s;
	cursor: pointer;

}

.kusIn{
    margin-top: 350px;
}

.kusIn img{
    width: 60%;
    margin-left: 79px;
}
.kusIn img{
    -webkit-transform:scale(1,1);
    -webkit-transition-duration: 0.5s;
}


.kusIn img:hover{
    -webkit-transform:scale(1.05,1.05);
    -webkit-transition-duration: 0.5s;
    cursor: pointer;

}

.kus1{
	width: 30%;float: left;position: relative;
}
.kus1 div{
	position: absolute;top: 18%;left: 45%;z-index:14;
}
.kus1 div p:nth-child(1){
	font-size: 20px;color: #fd875a;text-align: center;
}
.kus1 div p:nth-child(2){
	font-size: 16px;text-align: center;
}

@media only screen and (max-width: 1280px){
	/*.kus1 div{
	position: absolute;top: 34px;left: 45%;
	}*/
	.kus1 div p:nth-child(1){
	font-size: 16px;
	}
	.kus1 div p:nth-child(2){
		font-size: 14px;
	}
	.kusIn img{
		margin-left: 67px;
	}
	.kus img{
		margin-left: 67px;
	}
}
/*@media only screen and (min-width: 1400px){
	.kusIn img{
		margin-left: 57px;
	}
	.kus img{
		margin-left: 57px;
	}
}*/
/*库存统计*/
.kuCtong{
	width: 100%;
	background: #fff;
}
.kuCtong_body{
	width: 100%;
	padding-top: 25px;
	background: #fff;
	height: 96%;
	float: left;
	margin-bottom: 30px;
	
}
.kus_select{
	-webkit-appearance: none;
	background: #5395e1;
	color: #fff;
	font-size: 14px;
	line-height: 20px;
	padding:5px 20px 5px 5px;
	margin-left: 5px;
	border-radius: 5px;
	font-weight: 700;
	background: url(../images/xiaselec_03.png) no-repeat #5395e1 right center;
	cursor: pointer;
	border: none;
	float:left;
}
</style>

<div class="panel no-border">

    @include('purchase/menu')

    <form id="search-form-simple" class="search-form form-inline" action="{{url()}}" method="get">
        @include('searchForm3')
    </form>
        
    <div class="list-jqgrid">

    <div class="kus">
        <div style="width: 5%;float: left;">
            &nbsp;
        </div>
        <div class="kus1">
            <div>
                <p>本日采购金额</p>
                <p>0元</p>
            </div>
            <img src="{{$asset_url}}/images/web/ggg1_03.png"/>
        </div>
        <div class="kus1">
            <div>
                <p style="color: #61b7ef;">本月采购额</p>
                <p>809.00元</p>
            </div>
            <img src="{{$asset_url}}/images/web/ggg2_03.png"/>
        </div>
        <div class="kus1">
            <div>
                <p style="color: #a58bd3;">累计采购金额</p>
                <p>7318168.10元</p>
            </div>
            <img src="{{$asset_url}}/images/web/ggg3_03.png"/>
        </div>
        <div style="width: 5%;float: left;">
            &nbsp;
        </div>
    </div>
    </div>
        
</div>

<script>

var $table = null;
var params = paramsSimple = {{json_encode($search['query'])}};
var search = null;
var searchSimple = null;

(function($) {
    
    var data = {{json_encode($search['forms'])}};

    search = $('#search-form-advanced').searchForm({
        data: data,
        init: function(e) {
            var self = this;
        }
    });

    searchSimple = $('#search-form-simple').searchForm({
        data: data,
        init: function(e) {
            var self = this;
        }
    });
    searchSimple.find('#search-submit').on('click', function() {
        var query = searchSimple.serializeArray();
        $.map(query, function(row) {
            paramsSimple[row.name] = row.value;
        });
        $table.jqGrid('setGridParam', {
            postData: paramsSimple,
            page: 1
        }).trigger('reloadGrid');

        return false;
    });

    $('.list-jqgrid').height(getPanelHeight());

})(jQuery);

function getPanelHeight() {
    var list = $('.list-jqgrid').position();
    return top.iframeHeight - list.top - 12;
}

$(window).on('resize', function() {
    $('.list-jqgrid').height(getPanelHeight());
});
</script>