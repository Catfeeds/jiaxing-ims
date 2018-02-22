<!DOCTYPE html>
<html class="bg-white">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>字段编辑器</title>
        <link href="{{$asset_url}}/css/app.css" rel="stylesheet" type="text/css" />
        <script src="{{$asset_url}}/js/app.js" type="text/javascript"></script>
        <script src="{{$asset_url}}/vendor/ueditor/dialogs/internal.js" type="text/javascript"></script>
    </head>
    <body>

    <div class="container">

        <div class="m-t">

            <div class="form-group">
                <label class="control-label">字段名称 <span class="text-danger">*</span></label>
                <input type="text" id="txtName" name="name" placeholder="必填项" class="form-control input-sm">
            </div>

            <div class="form-group">
                <label class="control-label">默认值</label>
                <input id="txtValue" name="value" class="form-control input-sm" placeholder="无则不填" type="text">
            </div>

            <div class="form-group">
                <label class="control-label">字段样式</label>
                
                <div class="row">

                    <div class="col-xs-6">
                        <div class="input-group">
                            <div class="input-group-addon">宽</div>
                            <input id="txtWidth" name="width" class="form-control input-sm" type="text" value="150">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="input-group">
                            <div class="input-group-addon">字体</div>
                            <input id="txtFontSize" name="font-size" class="form-control input-sm" type="text">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <label><input id="hidden" name="hidden" type="checkbox"> 隐藏</label>
            </div>

        </div>
    </div>

        <script type="text/javascript">
            var oNode = null;
            var options = {};
            window.onload = function() {

                if(UE.plugins['model-field'].editdom) {

                    oNode = UE.plugins['model-field'].editdom;

                    $G('txtName').value = oNode.getAttribute('title');
                    $G('txtValue').value = oNode.getAttribute('value');
                    var nHidden = oNode.getAttribute('hide');
                    if (nHidden == '1') {
                        $G('hidden').checked = true;
                    }  else {
                        nHidden = '0';
                    }
                    var sItemId = oNode.getAttribute('name').substr(5);
                    var sFontSize = oNode.style.fontSize;
                    $G('txtFontSize').value = sFontSize.substr(0, sFontSize.length - 2);//这里的substr是为了去掉末尾的'px'
                    var sSizeWidth = oNode.style.width;
                    $G('txtWidth').value = sSizeWidth.substr(0, sSizeWidth.length - 2);
                }
            }
            dialog.oncancel = function() {
                if(UE.plugins['model-field'].editdom) {
                    delete UE.plugins['model-field'].editdom;
                }
            };
            dialog.onok = function() {

                if($G('txtName').value == '')
                {
                    alert('请输入控件名称');
                    return false;
                }
                if(!oNode) {
                    var sUrl = parent.myform.count_item.value;
                    var nItemId = null;
                    ajax.request(sUrl, {async:false,timeout:60000,onsuccess:function(xhr) {
                            try {
                                nItemId = xhr.responseText;
                                var html = '<input class="text" type="text"';

                                html += ' title = "' + $G('txtName').value + '"';
                                html += ' name = "data_' + nItemId + '"';
                                html += ' value = "' + $G('txtValue').value + '"';
                                if ( $G('hidden').checked ) {
                                    html += ' hide = "1"';
                                } else {
                                    html += ' hide = "0"';
                                }
                                html += ' style = "';
                                if( $G('txtFontSize').value != '') {
                                    html += 'font-size:' + $G('txtFontSize').value + 'px;';
                                }

                                if( $G('txtWidth').value != '') {
                                    html += 'width:' + $G('txtWidth').value + 'px;';
                                }

                                html += '"';

                                var options = UE.utils.unhtml(workflow_set_options());
                                html += ' data-options="' + options +'">';

                                editor.execCommand('insertHtml', html);
                            } catch (e) {
                                return;
                            }
                        },
                        error:function () {
                            alert('Request TimeOut');
                        }
                    });

                } else {

                    oNode.setAttribute('title', $G('txtName').value);
                    oNode.setAttribute('value', $G('txtValue').value);
                    if( $G('hidden').checked) {
                        oNode.setAttribute('hide', 1);
                    } else {
                        oNode.setAttribute('hide', 0);
                    }
                    var style = '';
                    if( $G('txtFontSize').value != '') {
                        style += 'font-size:' + $G('txtFontSize').value + 'px;';
                    }
                    if( $G('txtWidth').value != '') {
                        style += 'width:' + $G('txtWidth').value + 'px;';
                    }
                    oNode.setAttribute('style', style);
                    delete UE.plugins['model-field'].editdom; // 使用后清空这个对象，变回新增模式
                }
            };
        </script>
    </body>
</html>