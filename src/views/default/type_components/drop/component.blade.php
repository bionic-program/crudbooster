<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }} {{$col_group_width?:"col-sm-12"}}' id='form-group-{{$name}}' style="{{@$form['style']}}">
    <label class="col-sm-2 control-label {{$col_label_width?:'col-sm-2'}}" style="{{@$form['label_style']}}">{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="no-padding {{$col_width?:'col-sm-10'}}" style="{{@$form['control_style']}}">
        <div id="{{$name}}" class="dropzone" style="width: 100%;height: 85%;overflow-y: scroll;">
            <div class="dz-message needsclick">
                {{ @$form['help'] }}
            </div>
            <input name="{{$name}}" type="hidden"/>
            <input name="{{$name}}_old" type="hidden"/>
        </div>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'></p>
    </div>

</div>

<script type="text/javascript">
    function deleteFileFrom{{$name}}(file){
      var joined = $("input[name='{{$name}}']").val().split(",");
      var index = joined.indexOf(file);
      if (index !== -1) joined.splice(index, 1);
      $("input[name='{{$name}}']").val(joined.join(","));
    }

    Dropzone.options.myAwesomeDropzone = false;
    Dropzone.autoDiscover = false;
    document.addEventListener('DOMContentLoaded', function () {
        new Dropzone("#{{$name}}", {
          url: "{{@$form['url']}}",
          createImageThumbnails: true,
          thumbnailWidth: 200,
          thumbnailHeight: 200,
          thumbnailMethod: 'contain',
          resizeQuality: 0.5,
          paramName: "{{$name}}", // The name that will be used to transfer the file
          maxFilesize: 10, // MB
          resizeMimeType:"{{@$form['forced_mime']}}",
          acceptedFiles: "{{@$form['required_mime']}}",
          addRemoveLinks: true,
          headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
          },
          success: function(status,response) {
            var value = $("input[name='{{$name}}']").val();
            $("input[name='{{$name}}']").val(value+(value != "" ? ",":"")+response.data);
            // Now attach this new element some where in your page
          },
          queuecomplete: function(file){

          },
          init: function(){
            var self = this;
            var existing_files = "{{@$form['files']}}";
            if(existing_files != ""){
                existing_files = existing_files.split(",");
                existing_files.forEach(function(file){
                    let min = 1;
                    let max = 100;
                    let v = Math.floor(Math.random() * (max - min + 1)) + min;
                    let filename = file.replace(/^.*[\\\/]/, '');
                    let mockFile = { dataURL: file+"?v="+v, url: file+"?v="+v, kind: 'image', name: filename, size: 0, type: 'image/jpeg', accepted:true }; 
                    self.files.push(mockFile);      
                    self.emit('addedfile', mockFile);
                    self.createThumbnailFromUrl(mockFile,200,200,'contain',true,function(thumbnail){
                      self.emit('thumbnail', mockFile, thumbnail);
                      self.emit('complete', mockFile);
                    },"anonymous");

                    var value = $("input[name='{{$name}}']").val();
                    $("input[name='{{$name}}']").val(value+(value != "" ? ",":"")+filename);
                    $("input[name='{{$name}}_old']").val($("input[name='{{$name}}']").val());
                });
            }
            $("#{{$name}}").sortable({
                 items:'.dz-preview',
                 cursor: 'grab',
                 opacity: 0.5,
                 containment: "#{{$name}}",
                 distance: 20,
                 tolerance: 'pointer',
                 stop: function () {
                   var queue = self.getAcceptedFiles();
                   newQueue = [];
                   $('#{{$name}} .dz-preview .dz-filename [data-dz-name]').each(function (count, el) {           
                         var name = el.innerHTML;
                         queue.forEach(function(file) {
                            if (file.name === name) {
                                newQueue.push(file);
                             }
                         });
                   });
                   self.files = newQueue;
                   var selected = "";
                   newQueue.forEach(function(file){
                        console.log(file);
                        if(selected != ""){
                            selected += ",";
                        }
                        if(file.xhr){
                          var obj = JSON.parse(file.xhr.response);
                          selected += obj.data;
                        }else{
                          selected += file.name;
                        }
                   });
                   $("input[name='{{$name}}']").val(selected);
                 }
             });
          },
          accept: function(file, done) {
            done();
          },
          removedfile: function(file){
            if(file.xhr){
                var obj = JSON.parse(file.xhr.response);
                if(obj){
                    deleteFileFrom{{$name}}(obj.data);
                }
            }else{
              deleteFileFrom{{$name}}(file.name);
            }
            file.previewElement.remove();
          }
        });
    });


</script>