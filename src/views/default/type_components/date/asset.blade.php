@push('bottom')

    @if (App::getLocale() != 'en')
        <script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datepicker/locales/bootstrap-datepicker.'.App::getLocale().'.js') }}"
                charset="UTF-8"></script>
    @endif
    <script type="text/javascript">
        var lang = '{{App::getLocale()}}';
        $(function () {
            $('.input_date').each(function( index ) {
                $(this).datepicker({
                    todayBtn: "linked",
                    clearBtn: true,
                    format: $("#format_"+$(this).attr("id")).val(),
                    @if (in_array(App::getLocale(), ['ar', 'fa']))
                    rtl: true,
                    @endif
                    language: lang
                });
            });

            $('.open-datetimepicker').click(function () {
                $(this).next('.input_date').datepicker('show');
            });

        });

    </script>
@endpush