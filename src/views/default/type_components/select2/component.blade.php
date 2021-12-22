<?php
    if (@$form['php_code_before']) {
        eval($form['php_code_before']);
    }

    $preeval = $form['datatable_where'];
    eval("\$preeval = \"$preeval\";");
    $form['datatable_where'] = $preeval;

?>

@if($form['datatable'])
    
    @if($form['relationship_table'])
        @push('bottom')
            <script type="text/javascript">
                $(function () {
                    $('#{{$name}}').select2();
                })
            </script>
        @endpush
    @else
        @if($form['datatable_ajax'] == true)

            <?php

            $datatable = @$form['datatable'];
            $where = @$form['datatable_where'];
            $format = @$form['datatable_format'];

            $raw = explode(',', $datatable);
            $url = CRUDBooster::mainpath("find-data");

            $table1 = $raw[0];
            $column1 = $raw[1];

            @$table2 = $raw[2];
            @$column2 = $raw[3];

            @$table3 = $raw[4];
            @$column3 = $raw[5];
            ?>

            @push('bottom')
                <script>
                    $(function () {
                        $('#{{$name}}').select2({
                            placeholder: {
                                id: '-1',
                                text: '{{cbLang('text_prefix_option')}} {{$form['label']}}'
                            },
                            allowClear: true,
                            ajax: {
                                url: '{!! $url !!}',
                                delay: 250,
                                data: function (params) {
                                    var query = {
                                        q: params.term,
                                        format: "{{$format}}",
                                        table1: "{{$table1}}",
                                        column1: "{{$column1}}",
                                        table2: "{{$table2}}",
                                        column2: "{{$column2}}",
                                        table3: "{{$table3}}",
                                        column3: "{{$column3}}",
                                        where: "{!! addslashes($where) !!}"
                                    }
                                    return query;
                                },
                                processResults: function (data) {
                                    return {
                                        results: data.items
                                    };
                                }
                            },
                            escapeMarkup: function (markup) {
                                return markup;
                            },
                            minimumInputLength: 1,
                            @if($value)
                            initSelection: function (element, callback) {
                                var id = $(element).val() ? $(element).val() : "{{$value}}";
                                if (id !== '') {
                                    $.ajax('{{$url}}', {
                                        data: {
                                            id: id,
                                            format: "{{$format}}",
                                            table1: "{{$table1}}",
                                            column1: "{{$column1}}",
                                            table2: "{{$table2}}",
                                            column2: "{{$column2}}",
                                            table3: "{{$table3}}",
                                            column3: "{{$column3}}"
                                        },
                                        dataType: "json"
                                    }).done(function (data) {
                                        callback(data.items[0]);
                                        $('#<?php echo $name?>').html("<option value='" + data.items[0].id + "' selected >" + data.items[0].text + "</option>");
                                    });
                                }
                            }

                            @endif
                        });

                    })
                </script>
            @endpush

        @else
            @push('bottom')
                <script type="text/javascript">
                    $(function () {
                        $('#{{$name}}').select2();
                    })
                </script>
            @endpush
        @endif
    @endif
@else

    @push('bottom')
        <script type="text/javascript">
            $(function () {
                $('#{{$name}}').select2();
            })
        </script>
    @endpush

@endif

<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }} {{$col_group_width?:"col-sm-12"}}' id='form-group-{{$name}}' style="{{@$form['style']}}">
    <label class="control-label {{$col_label_width?:'col-sm-2'}}" style="{{@$form['label_style']}}">{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! cbLang('this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="no-padding {{$col_width?:'col-sm-10'}}" style="{{@$form['control_style']}}">
        <select style='width:100%' class='form-control' id="{{$name}}"
                {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} name="{{$name}}{{($form['relationship_table'])?'[]':''}}" {{ ($form['relationship_table'])?'multiple="multiple"':'' }} >
            @if($form['dataenum'])
                <option value=''>{{cbLang('text_prefix_option')}} {{$form['label']}}</option>
                <?php
                $dataenum = $form['dataenum'];
                $dataenum = (is_array($dataenum)) ? $dataenum : explode(";", $dataenum);
                ?>
                @foreach($dataenum as $enum)
                    <?php
                    $val = $lab = '';
                    if (strpos($enum, '|') !== FALSE) {
                        $draw = explode("|", $enum);
                        $val = $draw[0];
                        $lab = $draw[1];
                    } else {
                        $val = $lab = $enum;
                    }

                    $select = ($value == $val) ? "selected" : "";
                    ?>
                    <option {{$select}} value='{{$val}}'>{{$lab}}</option>
                @endforeach
            @endif

            @if($form['datatable'])
                @if($form['relationship_table'])
                    <?php
                    $exploded = explode(',', $form['datatable']);
                    $select_table = $exploded[0];
                    $select_title = trim(str_replace("|", ",", $exploded[1]));
                    $select_where = $form['datatable_where'];
                    $pk = CRUDBooster::findPrimaryKey($select_table);

                    if(count($exploded) > 2){
                        $pk = $exploded[2];
                    }

                    $result = DB::table($select_table)->select(\DB::raw($pk.",".$select_title));
                    if ($select_where) {
                        $result->whereraw($select_where);
                    }

                    if (strpos($select_title, ' as ') !== false) {
                        $as = explode(" as ", $select_title);
                        $orderCol = $as[0];
                        $label = $as[1];
                    }else{
                        $orderCol = $select_title;
                        $label = $select_title;
                    }

                    
                    $result = $result->orderbyRaw($orderCol.' asc')->get();

					
					if($form['datatable_orig'] != ''){
                        $params = explode("|", $form['datatable_orig']);
                        if(!isset($params[2])) $params[2] = "id";
                        $value = DB::table($params[0])->where($params[2], $id)->first()->{$params[1]};
                        $value = explode(",", $value);
                    } else {
                        $foreignKey = CRUDBooster::getForeignKey($table, $form['relationship_table']);
                        $foreignKey2 = CRUDBooster::getForeignKey($select_table, $form['relationship_table']);
                        $value = DB::table($form['relationship_table'])->where($foreignKey, $id);
                        $value = $value->pluck($foreignKey2)->toArray();
                    }

                    foreach ($result as $r) {
                        $option_label = $r->{$label};
                        $option_value = $r->{$pk};
                        $selected = (is_array($value) && in_array($r->$pk, $value)) ? "selected" : "";
                        echo "<option $selected value='$option_value'>$option_label</option>";
                    }
                    ?>
                @else
                    @if($form['datatable_ajax'] == false)
                        <option value=''>{{cbLang('text_prefix_option')}} {{$form['label']}}</option>
                        <?php
                        $exploded = explode(',', $form['datatable']);
                        $select_table = $exploded[0];
                        $select_title = str_replace("|", ",", $exploded[1]);
                        $select_where = $form['datatable_where'];
                        $datatable_format = $form['datatable_format'];
                        if(count($exploded) > 2){
                            $select_table_pk = $exploded[2];
                        }else{
                            $select_table_pk = CRUDBooster::findPrimaryKey($select_table);
                        }
                        $result = DB::table($select_table)->select($select_table_pk, \DB::raw($select_title));
                        if ($datatable_format) {
                            $result->addSelect(DB::raw("CONCAT(".$datatable_format.") as $select_title"));
                        }
                        if ($select_where) {
                            $result->whereraw($select_where);
                        }
                        if (CRUDBooster::isColumnExists($select_table, 'deleted_at')) {
                            $result->whereNull('deleted_at');
                        }

                        if (strpos($select_title, ' as ') !== false) {
                            $orderCol = explode(" as ", $select_title)[0];
                        }else{
                            $orderCol = $select_title;
                        }
                        
                        $result = $result->orderbyRaw($orderCol.' asc')->get();

                        foreach ($result as $r) {
                            $option_label = $r->{$select_title};
                            $option_value = $r->$select_table_pk;
                            $selected = ($option_value == $value) ? "selected" : "";
                            echo "<option $selected value='$option_value'>$option_label</option>";
                        }
                        ?>
                    <!--end-datatable-ajax-->
                    @endif

                <!--end-relationship-table-->
                @endif

            <!--end-datatable-->
            @endif
        </select>
        <div class="text-danger">
            {!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}
        </div><!--end-text-danger-->
        <p class='help-block'>{{ @$form['help'] }}</p>

    </div>
</div>

@if(isset($form['value']))
    <?php 
        $valuejoined = is_array($form['value']) ? implode(",", $form['value']) : $form['value'];
    ?>
    @push('bottom')
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded",function(){
                var value = ('{{$valuejoined}}').split(',');
                $('#{{$name}}').val(value).trigger('change');
            });
        </script>
    @endpush
@endif

