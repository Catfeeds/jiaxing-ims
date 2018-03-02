(function($) {

    'use strict';

    $.fn.searchForm = function(options) {

        var self     = this;
        var element  = [];
        var data     = options.data;
        var advanced = data.advanced || 0;
        var assign   = false;
        var values   = {};

        function init() {

            if(advanced == true) {

                $.each(data.field, function(i) {
                    var type = $('#search-field-' + i).data('type');
                    setValues(type, i);
                });
                
            } else {
                var e = self.find('#search-field-0');
                e.val(data['field'][0]);
                var type = e.find('option:selected').data('type');
                setValues(type, 0);

                e.on('change', function() {
                    assign = true;
                    var type = $(this).find('option:selected').data('type');
                    element[0].value.empty();
                    setValues(type, 0);
                });
            }
        }

        function setValues(type, i) {

            element[i] = {
                condition: self.find('#search-condition-'+i),
                value: self.find('#search-value-'+i)
            };

            setCondition(i, type, data['condition'][i] || '');

            if(type.indexOf('.') > 0) {

                if(type.indexOf('flow_step.') == 0) {
                    handle.flow_step.call(self, i, type);
                } else {
                    handle.dict.call(self, i, type);
                }

            } else {
                handle[type].call(self, i, data['type'][i]);
            }
        }

        function setCondition(i, type, selected) {

            var e = element[i].condition.empty();
            var condition = {};

            if(assign == true) {
                selected = '';
            }

            condition.number = [
                {key:"eq",value:'等于'},
                {key:"neq",value:'不等于'},
                {key:"gt",value:'大于'},
                {key:"lt",value:'小于'},
            ];

            condition.date = [
                {key:"eq",value:'等于'},
                {key:"neq",value:'不等于'},
                {key:"gt",value:'大于'},
                {key:"lt",value:'小于'},
            ];

            condition.second = [
                {key:"eq",value:'等于'},
                {key:"neq",value:'不等于'},
                {key:"gt",value:'大于'},
                {key:"lt",value:'小于'},
            ];

            condition.text = [
                {key:"like",value:'包含'},
                {key:"not_like",value:'不包含'},
                {key:"eq",value:'等于'},
                {key:"neq",value:'不等于'},
                {key:"empty",value:'为空'},
                {key:"not_empty",value:'不为空'}
            ];

            if(type == 'second' || type == 'text' || type == 'number' || type == 'date') {

                var value = selected || condition[type][0].key;

                $.map(condition[type], function(row) {
                    e.append('<option value="'+row.key+'">'+row.value+'</option>');
                });
                e.parent('div').show();

            } else if(type == 'birthday') {
                e.append('<option value="birthday">birthday</option>');
                e.parent('div').hide();
                var value = 'birthday';

            } else if(type == 'date2') {
                e.append('<option value="date2">date2</option>');
                e.parent('div').hide();
                var value = 'date2';

            } else if(type == 'second2') {
                e.append('<option value="second2">second2</option>');
                e.parent('div').hide();
                var value = 'second2';

            } else if(type == 'text2') {
                e.append('<option value="text2">text2</option>');
                e.parent('div').hide();
                var value = 'text2';
            } else if(type == 'select') {
                e.append('<option value="eq">eq</option>');
                e.parent('div').hide();
                var value = 'eq';

            } else {
                e.append('<option value="eq">eq</option>');
                e.parent('div').hide();
                var value = 'eq';
            }
            e.val(value);

            toggleValue(i, value);
            e.on('change', function() {
                toggleValue(i, $(this).val());
            });
        }

        function toggleValue(i, value) {
            var e = element[i].value;

            if(value == 'empty' || value == 'not_empty') {
                e.hide();
            } else {
                e.show();
            }
        }

        function attr(i, id) {

            var value = 'search-value';
            var name  = 'search';

            var res = {};

            var id_0 = '';
            var id_1 = '';

            if(id != undefined) {
                id_0 = '-' + id;
                id_1 = '_' + id;
            }

            res.id   = value + '-' + i + id_0;
            res.name = name + '_' + i + id_1;

            if(assign == false) {

                if(id_0) {
                    res.value = data['search'][i][id] || '';
                } else {
                    res.value = data['search'][i] || '';
                }

            } else {
                res.value = '';
            }

            return res;
        }

        function _option(rows) {
            var type = $.type(rows);
            var option = advanced == true ? '<option value=""> - </option>' : '';
            if(type == 'array' || type == 'object') {
                $.map(rows, function(row) {
                    option += '<option value="'+row.id+'">'+row.name+'</option>';
                });
            } else {
                option = option.concat(rows);
            }
            return option;
        }

        self._select = function(rows, i, id, space) {
            var a = attr(i, id);
            var d = _option(rows);
            var e = $('<select name="'+a.name+'" id="'+a.id+'" class="form-control input-sm">'+d+'</select>');
            element[i].value.append(e);
            if(a.value) {
                e.val(a.value);
            }
        }

        self._text = function(i, id, space) {
            var a = attr(i, id);
            var placeholder = self.find('#search-label-' + i).val() || '';
            var e = $('<input name="'+a.name+'" placeholder="'+ placeholder +'" id="'+a.id+'" value="'+a.value+'" type="text" class="form-control input-sm">');
            element[i].value.append(e);
        }

        self._date = function(i, id, space) {
            var a = attr(i, id);
            var e = $('<input name="'+a.name+'" id="'+a.id+'" value="'+a.value+'" type="text" data-toggle="date" class="form-control input-sm">');
            element[i].value.append(e);
        }

        self._date2 = function(i, id, space) {
            var a0 = attr(i, 0);
            var a1 = attr(i, 1);
            var e = $('<table class="table"><tr><td><input name="'+a0.name+'" id="'+a0.id+'" value="'+a0.value+'" type="text" data-toggle="date" class="form-control input-sm"></td><td class="date-apart"> - </td><td><input name="'+a1.name+'" id="'+a1.id+'" value="'+a1.value+'" type="text" data-toggle="date" class="form-control input-sm"></td></tr></table>');
            element[i].value.append(e);
        }

        self._second2 = function(i, id, space) {
            var a0 = attr(i, 0);
            var a1 = attr(i, 1);
            var e = $('<table class="table"><tr><td><input name="'+a0.name+'" id="'+a0.id+'" value="'+a0.value+'" type="text" data-toggle="date" class="form-control input-sm"></td><td class="date-apart"> - </td><td><input name="'+a1.name+'" id="'+a1.id+'" value="'+a1.value+'" type="text" data-toggle="date" class="form-control input-sm"></td></tr></table>');
            element[i].value.append(e);
        }

        self._birthday = function(i, id, space) {
            var a0 = attr(i, 0);
            var a1 = attr(i, 1);
            var e = $('<input name="'+a0.name+'" id="'+a0.id+'" value="'+a0.value+'" type="text" data-toggle="date" data-format="MM-dd" class="form-control input-sm"> - <input name="'+a1.name+'" id="'+a1.id+'" value="'+a1.value+'" type="text" data-toggle="date" data-format="MM-dd" class="form-control input-sm">');
            element[i].value.append(e);
        }

        var handle = {
            empty: function(i) {
                element[i].value.empty();
            },
            text: function(i) {
                self._text(i);
            },
            text2: function(i) {
                self._text(i);
            },
            number: function(i) {
                self._text(i);
            },
            date: function(i) {
                self._date(i);
            },
            date2: function(i) {
                self._date2(i);
            },
            birthday: function(i) {
                self._birthday(i);
            },
            second: function(i) {
                self._date(i);
            },
            second2: function(i) {
                self._second2(i);
            },
            select: function(i, type) {
                var list = type.data || [];
                if(list.length) {
                    var res = [];
                    $.each(list, function(k, v) {
                        res.push({id: v.id, name: v.text});
                    });
                    self._select(res, i);
                } else {
                    if(type.url) {
                        $.post(app.url(type.url), function(res) {
                            var option = '';
                            $.map(res.data, function(row) {
                                option += '<option value="'+row.id+'">'+row.text+'</option>';
                            });
                            self._select(option, i);
                        });
                    }
                }
            },
            role: function(i) {
                $.post(app.url('user/role/dialog'), function(res) {
                    var option = '';
                    $.map(res, function(row) {
                        option += '<option value="'+row.id+'">'+row.text+'</option>';
                    });
                    self._select(option, i);
                });
            },
            department: function(i) {
                $.post(app.url('user/department/dialog'), function(res) {
                    var option = '';
                    $.map(res, function(row) {
                        option += '<option value="'+row.id+'">'+row.text+'</option>';
                    });
                    self._select(option, i);
                });
            },
            owner: function(i) {
                $.get(app.url('customer/customer/owner'),function(res) {
                    self._select(res, i);
                });
            },
            dict: function(i, key) {
                $.get(app.url('index/api/option', {key:key}),function(res) {
                    self._select(res, i);
                });
            },
            flow_step: function(i, key) {
                var keys = key.split('.');
                $.get(app.url('flow/step/steps', {table:keys[1]}),function(res) {
                    self._select(res, i);
                }, 'json');
            },
            address: function(i) {
                var province = attr(i, 0);
                var city     = attr(i, 1);
                var e        = '<select name="'+province.name+'" id="'+province.id+'" class="form-control input-sm"></select>&nbsp;<select name="'+city.name+'" id="'+city.id+'" class="form-control input-sm"></select>';
                element[i].value.append(e);
                new pcas(province.id, city.id, province.value, city.value);
            },
            region: function(i) {

                var province = attr(i, 0);
                var city     = attr(i, 1);
                var county   = attr(i, 2);

                var province_id = province.value;
                var city_id     = city.value;
                var county_id   = county.value;

                element[i].value.append('<select name="'+province.name+'" id="'+province.id+'" class="form-control input-sm"></select>&nbsp;<select name="'+city.name+'" id="'+city.id+'" class="form-control input-sm"></select>&nbsp;<select name="'+county.name+'" id="'+county.id+'" class="form-control input-sm"></select>');

                $.get(app.url('index/api/region',{layer:1}), function(res) {

                    var option = '';
                    $.map(res, function(row) {
                        option += '<option value="'+row.id+'">'+row.name+'</option>';
                    });
                    var e = $('#'+province.id).html(option);
                    if(province_id) {
                        e.val(province_id);
                    }

                    _city(i);
                    _county(i);

                    self.on('change', '#'+province.id, function() {
                        province_id = this.value;
                        city_id     = 0;
                        county_id   = 0;
                        _city(i);
                        _county(i);
                    });
                    
                    self.on('change', '#'+city.id, function() {
                        city_id   = this.value;
                        county_id = 0;
                        _county(i);
                    });

                });

                function _city(i, space) {
                    $.get(app.url('index/api/region', {layer:2,parent_id:province_id}), function(res) {
                        var option = '';
                        $.map(res, function(row) {
                            option += '<option value="'+row.id+'">'+row.name+'</option>';
                        });
                        var e = $('#'+city.id).html(option);
                        if(city_id) {
                            e.val(city_id);
                        }
                    });
                }

                function _county(i, space) {
                    $.get(app.url('index/api/region', {layer:3,parent_id:city_id}), function(res) {
                        var option = '';
                        $.map(res, function(row) {
                            option += '<option value="'+row.id+'">'+row.name+'</option>';
                        });
                        var e = $('#'+county.id).html(option);
                        if(county_id) {
                            e.val(county_id);
                        }
                    });
                }
            },
            circle: function(i) {
                var circle   = self.attr(i, 0);
                var customer = self.attr(i, 1);

                var circle_id   = circle.value;
                var customer_id = customer.value;

                element[i].value.append('<select name="'+circle.name+'" id="'+circle.id+'" class="form-control input-sm"></select>&nbsp;<select name="'+customer.name+'" id="'+customer.id+'" class="form-control input-sm"></select>');
                
                $.post(app.url('customer/circle/dialog'), function(res) {
                    var option = '<option value=""> - </option>';
                    $.map(res, function(row) {
                        option += '<option value="'+row.id+'">'+row.name+'</option>';
                    });
                    var e = $('#'+circle.id).html(option);
                    if(circle_id) {
                        e.val(circle_id);
                    }
                    _customer(i);
                });

                self.on('change', '#'+circle.id, function() {
                    circle_id   = this.value;
                    customer_id = 0;
                    _customer(i);
                });

                function _customer(i) {
                    var option = '<option value=""> - </option>';
                    if(circle_id) {
                        $.post(app.url('customer/customer/dialog', {limit: 500, circle_id: circle_id}), function(res) {
                            $.map(res.data, function(row) {
                                option += '<option value="'+row.id+'">'+row.text+'</option>';
                            });
                            var e = $('#'+customer.id).html(option);
                            if(customer_id) {
                                e.val(customer_id);
                            }
                        });
                    } else {
                        $('#'+customer.id).html(option);
                    }
                }
            }
        }

        self.attr    = attr;
        self.element = element;
        self.options = options;

        if(typeof(options.init) == 'function') {
            options.init.call(this, handle);
        }
        
        init();

        return this;
    }

})(jQuery);