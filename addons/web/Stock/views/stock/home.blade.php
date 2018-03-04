<style type="text/css">
.row-sm { margin-top: 150px; margin-left: -8px; margin-right: -8px; }
.row-sm > div { padding-left: 8px; padding-right: 8px; }
.row-sm > div > .panel {
    margin-bottom: 16px !important;
    border-radius: 5px !important;
    text-align: center;
}
.row-todo .panel { text-align: center; max-width:240px; margin:0 auto;  padding-bottom: 30px; position: relative; }
.todo-logo { text-align: center; color: #fff; padding-top:50px; }
.todo-logo .fa { font-size: 60px; }
.todo-text { padding: 20px; font-weight: 400; }
.todo-text .px { padding-top: 10px; }
@media (max-width: 767px) {
    .row-sm { margin-top: 0; }
}
.text-md { font-size: 26px; font-family: font-family: "Microsoft YaHei UI","Microsoft YaHei","WenQuanYi Micro Hei","Helvetica Neue",Helvetica,Arial,"Hiragino Sans GB","Hiragino Sans GB W3",sans-serif; }
</style>

<div class="">
    <div class="row row-sm row-todo">

        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            
            <div class="panel">
                <a href="javascript:parent.addTab('/stock/supplier/index', 'stock_supplier', '供应商管理');">
                <div class="todo-logo hidden-xs" style="color:#2c83e4;">
                    <i class="fa fa-group"></i>
                </div>
                <div class="todo-text">
                    <div class="text-md" style="color:#2c83e4;">供应商管理</div>
                </div>
                </a>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <div class="panel">
                <a href="javascript:parent.addTab('/stock/product/index', 'stock_product', '商品管理');">
                <div class="todo-logo hidden-xs" style="color:#fd875a;">
                    <i class="fa fa-cubes"></i>
                </div>
                <div class="todo-text">
                    <div class="text-md" style="color:#fd875a;">商品管理</div>
                </div>
                </a>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <div class="panel">
                <a href="javascript:parent.addTab('/stock/warehouse/index', 'stock_warehouse', '仓库管理');">
                <div class="todo-logo hidden-xs" style="color:#27c24c;">
                    <i class="fa fa-home"></i>
                </div>
                <div class="todo-text">
                    <div class="text-md" style="color:#27c24c;">仓库管理</div>
                </div>
                </a>
            </div>
        </div>
    </div>
</div>