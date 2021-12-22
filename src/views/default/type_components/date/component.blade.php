<div class='form-group form-datepicker {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }} {{$col_group_width?:"col-sm-12"}}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
    <label class="control-label {{$col_label_width?:'col-sm-2'}}" style="{{@$form['label_style']}}">{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! cbLang('this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="no-padding {{$col_width?:'col-sm-10'}}" style="{{@$form['control_style']}}">
        <div class="input-group">
            <span class="input-group-addon open-datetimepicker"><a><i class='fa fa-calendar '></i></a></span>
            <input type='text' title="{{$form['label']}}" readonly
                   {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control notfocus input_date' name="{{$name}}" id="{{$name}}"
                   value='{{$value}}'/>
            <input type="hidden" id="format_{{$name}}" value="{{@$form['format']?:'yyyy-mm-dd'}}">
        </div>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
</div>
