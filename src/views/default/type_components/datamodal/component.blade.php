<?php
    if (@$form['php_code_before']) {
        eval($form['php_code_before']);
    }

    $preeval = $form['datamodal_where'];
    eval("\$preeval = \"$preeval\";");
    $form['datamodal_where'] = $preeval;

?>
<div class='form-group form-datepicker {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }} {{$col_group_width?:"col-sm-12"}}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
    <label class="control-label {{$col_label_width?:'col-sm-2'}}" style="{{@$form['label_style']}}">{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! cbLang('this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="no-padding {{$col_width?:'col-sm-10'}}" style="{{@$form['control_style']}}">

        <?php
        if($value != null && $value != "" && $value != @$form['datamodal_extra_value']){
            $data = DB::table($form['datamodal_table'])->where((isset($form['datamodal_result_value']) ? $form['datamodal_result_value'] : 'id'), $value)->first();
            if(isset($form['datamodal_result_label'])){
                $exploded = explode(',', $form['datamodal_result_label']);
                $datamodal_value = "";
                foreach ($exploded as $str) {
                    if(strpos($str, '"') !== false || strpos($str, "'") !== false){
                        $datamodal_value = $datamodal_value.substr($str, 1, strlen($str)-2);
                    }else{
                        $datamodal_value = $datamodal_value.$data->$str;
                    }
                }
            }else{
                $datamodal_field = explode(',', $form['datamodal_columns'])[0];
                $datamodal_value = $data->$datamodal_field;
            }
        }else if($value == @$form['datamodal_extra_value']){
            $datamodal_value = $value;    
        }else{
            $datamodal_value = "";
        }

        ?>

        <div id='{{$name}}' class="input-group">
            <input type="hidden" name="{{$name}}" class="input-id" value="{{$value}}">
            <input type="text" class="form-control input-label" {{$required?"required":""}} value="{{$datamodal_value}}" style="pointer-events: none">
            <span class="input-group-btn">
            <?php if(isset($form['datamodal_extra_value'])){ 
                $extra_value = $form['datamodal_extra_value'];
                $extra_type = $form['datamodal_extra_type'];
                ?>
                <button title='{{@$form["datamodal_extra_help"]}}' style="padding-left: 7px;padding-right: 7px" type="button" class='btn btn-primary' data-type="{{$extra_type}}" data-value="{{$extra_value}}" onclick="runExtra_{{$name}}(this)">
                    <i class='{{@$form["datamodal_extra_icon"]}}'></i>
                </button>
            <?php } ?>
            <button class="btn btn-primary" onclick="showModal{{$name}}()" type="button"><i class='fa fa-search'></i> {{trans('crudbooster.datamodal_browse_data')}}</button>
            <button style="padding-left: 7px;padding-right: 7px" type="button" class='btn btn-danger' onclick="clearInput_{{$name}}()">
            <i class='fa fa-times'></i>
            </button>
                <?php if(strlen($form['datamodal_module_path']) > 1){ ?>
                <a class="btn btn-info" href="{{CRUDBooster::adminPath()}}/{{$form['datamodal_module_path']}}" target="_blank"><i
                            class='fa fa-edit'></i> {{$form['label']}}</a>
                <?php } ?>
      </span>
        </div><!-- /input-group -->

        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
</div>

<?php
    $extra_name = $form['datamodal_result_value_extra'];
?>

@push('bottom')
    <script type="text/javascript">
        var url_{{$name}} = "{{CRUDBooster::mainpath('modal-data')}}?table={{$form['datamodal_table']}}&columns={{$form['datamodal_columns']}}&name_column={{$name}}&where={{urlencode($form['datamodal_where'])}}&select_to={{ urlencode($form['datamodal_select_to']) }}&columns_name_alias={{ urlencode($form['datamodal_columns_alias']) }}&result_label={{ urlencode($form['datamodal_result_label']) }}&result_value={{ urlencode($form['datamodal_result_value']) }}&result_value_extra={{ urlencode($form['datamodal_result_value_extra']) }}";

        
        function clearInput_{{$name}}(){
            var id = "{{$name}}";
            $("#"+id+" input[type='hidden']").val("").trigger("input");
            $("#"+id+" input[type='text']").val("");
        }

        function runExtra_{{$name}}(self){
            var id = "{{$name}}";
            var type = $(self).data("type");
            var value = $(self).data("value");
            if(type == "url"){
                var win = window.open(value, '_blank');
                win.focus();
            }else{
                setInput_{{$name}}(value);
            }
        }

        function setInput_{{$name}}(data){
            var id = "{{$name}}";
            $("#"+id+" input[type='hidden']").val(data).trigger("input");
            $("#"+id+" input[type='text']").val(data);
        }

        function showModal{{$name}}() {
            $('#iframe-modal-{{$name}}').attr('src', url_{{$name}});
            $('#modal-datamodal-{{$name}}').modal('show');
        }

        function hideModal{{$name}}() {
            $('#modal-datamodal-{{$name}}').modal('hide');
        }

        function selectAdditionalData{{$name}}(select_to_json) {
            $.each(select_to_json, function (key, val) {
                console.log('#' + key + ' = ' + val);
                if (key == 'datamodal_id') {
                    $('#{{$name}} .input-id').val(val).trigger('blur').trigger('input');
                }
                if (key == 'datamodal_extra') {
                    $('#{{$extra_name}}').val(val).trigger('blur').trigger('input');
                }
                if (key == 'datamodal_label') {
                    $('#{{$name}} .input-label').val(val).trigger('blur');
                }
                $('#' + key).val(val).trigger('change');
                $('#{{$name}}' + key).trigger('blur');
            })
            hideModal{{$name}}();
        }
    </script>


    <div id='modal-datamodal-{{$name}}' class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog {{ $form['datamodal_size']=='large'?'modal-lg':'' }} " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class='fa fa-search'></i> {{cbLang('datamodal_browse_data')}} | {{$form['label']}}</h4>
                </div>
                <div class="modal-body">
                    <iframe id='iframe-modal-{{$name}}' style="border:0;height:{{$form['datamodal_height']?: "430px"}};width: 100%" src=""></iframe>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endpush
