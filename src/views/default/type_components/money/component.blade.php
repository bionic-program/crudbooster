<div class="form-group {{$header_group_class}} {{ ($errors->first($name))?'has-error': '' }} {{$col_group_width?:'col-sm-12'}}" id="form-group-{{$name}}" style="{{@$form['style']}}">
    <label class="control-label {{$col_label_width?:'col-sm-2'}}" style="{{@$form['label_style']}}">{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! cbLang('this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="no-padding {{$col_width?:'col-sm-10'}}" style="{{@$form['control_style']}}">
        <input type="text" title="{{$form['label']}}" {{$style}} {{$required}} {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class="form-control inputMoney"
               name="{{$name}}" id="{{$name}}" value="{{$value}}">
        <div class="text-danger">{!! $errors->first($name)?'<i class="fa fa-info-circle"></i> '.$errors->first($name):'' !!}</div>
        <p class="help-block">{{ @$form['help'] }}</p>
    </div>
</div>

@if($form['formula'])
<?php
    $formula = $form['formula'];
    $formula_function_name = 'formulaField'.str_slug($form['id'], '');
    $script_onchange = "";
    preg_match_all("/\[([^\]]*)\]/", $formula, $matches);
    foreach ($matches[1] as $key => $value) {
        $script_onchange .= "
                                $('#$value').change(function() {
                                    $formula_function_name();
                                });
                                ";
         $formula = str_replace("[".$value."]", "(\$('#".$value."').val().replace(/,/g,'') != '' ? parseFloat(\$('#".$value."').val().replace(/,/g,'')) : 0)", $formula);
    }
?>
@push('bottom')
    <script type="text/javascript">
        @if($script_onchange != "")
        function {{ $formula_function_name }}() {
            var v = {!! $formula !!};
            //alert($('#cut_value').val().replace(',',''));
            $("#{{$form['id']}}").val(v).trigger('blur');
        }
        
        $(function () {
            {!! $script_onchange !!}
        })
        @endif
    </script>
@endpush
@endif