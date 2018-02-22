<form class="form-horizontal" name="myform" id="myform" method="post">

    <div class="table-responsive">
        <table class="table table-form m-b-none">
            <thead>
                <tr>
                    <th align="center">生效时间</th>
                    <th align="right">单价</th>
                    <th align="left">备注</th>
                </tr>
            </thead>

            <tbody>
                @foreach($prices as $price)
                <tr>
                    <td align="center">
                        @datetime($price->date)
                    </td>
                    <td align="right">
                        {{$price->price}}
                    </td>
                    <td align="left">
                        {{$price->description}}
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>

</form>