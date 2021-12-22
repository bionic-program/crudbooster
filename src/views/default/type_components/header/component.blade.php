<div id='header{{$index}}' data-collapsed="{{ ($form['collapsed']===true)?'true':'false' }}" class='header-title form-divider col-sm-12'>
    <h4>
        <strong><i class='{{$form['icon']?:"fa fa-check-square-o"}}'></i> {{$form['label']}}</strong>&nbsp;&nbsp;<small>{{ @$form['help'] }}</small>
        <span class='pull-right icon'><i class='fa fa-minus-square-o'></i></span>
    </h4>
</div>