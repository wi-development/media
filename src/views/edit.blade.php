@extends('dashboard::layouts.master')

@section('content')

        <!--Page Title-->
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <div id="page-title">
            <h1 class="page-header text-overflow">Edit media</h1>

            <!--Searchbox-->
            <div class="searchbox">
                <div class="input-group custom-search-form">
                    <input type="text" class="form-control" placeholder="Search..">
							<span class="input-group-btn">
								<button class="text-muted" type="button"><i class="fa fa-search"></i></button>
							</span>
                </div>
            </div>
        </div>
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <!--End page title-->


        <!--Breadcrumb-->
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <ol class="breadcrumb">
            <li><a href="/admin">Dashboard</a></li>
            <li><a href="{{route('admin::media.index')}}">Media overzicht</a></li>
            <li class="active">Edit</li>
        </ol>
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <!--End breadcrumb-->




        <!--Page content-->
        <!--===================================================-->
        <div id="page-content">
            <div class="row">
                <?php
                $frmHeader = "Profiel wijzigen van '".$medium->translations->first()->name."'";
                ?>
                @include('errors.list')
                <!-- BASIC FORM ELEMENTS -->
                    {{ Form::model($medium,['method'=>'PATCH', 'route'=>array('admin::media.update',$medium->id), 'class'=>'forxm-horizontal foxrm-padding  dz-clickable']) }}
                        {{--@include('errors.sitemap')--}}
                        @include('media::partials.form_use_main_lng', ['submitButtonText' => 'Aanpassen','frmHeader' => ''.$frmHeader.''])
                    {{ Form::close() }}
                <!-- END BASIC FORM ELEMENTS -->
            </div>
        </div>
        <!--===================================================-->
        <!--End page content-->

@endsection










@section('css.head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.css" rel="stylesheet">
@endsection






@section('scripts.footer')

    <!--Demo script [ DEMONSTRATION ]
    <script src="/nifty/js/demo/nifty-demo.min.js"></script>
-->
    <!--Panel [ SAMPLE ]
    <script src="/nifty/js/demo/ui-panels.js"></script>
-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.js"></script>

    <script>
        Dropzone.autoDiscover = false;

        $(document).ready(function() {
            $(".dropzone-container-uit").each(function (key,dzContainer) {
                    //console.info($(this).get(0));
                    //console.info($(this).children('.dropzone').get(0));
                    new Dropzone($(this).children('.dropzone').get(0), {
                        url: "/admin/media/upload",
                        //paramName:'photo',
                        maxFiles:2,
                        addRemoveLinks:true,
                        autoProcessQueue: false,
                        //clickable: ".fileinput-button, #fileInput_nl",
                        headers: {
                            'X-CSRF-Token': $('input[name="_token"]').val()
                        },
                        init: function () {
                            var myDropzone = this;
                            var media_type = $('input[name="media_type"]').val();
                            var mySubmitButton = $(dzContainer).find('input[type="submit"]');


        /*
                            var thumbnail = $('.dropzone .dz-preview.dz-file-preview .dz-image:last');
                            var thumbnail = $(dzContainer).find('.dropzone .img-thumbnail:last');


                            console.info(thumbnail);
                            //switch (file.type) {
                            switch (media_type) {
                                case 'file':
                                    thumbnail.css('background', 'url(img/pdf.png');
                                    thumbnail.attr('src','/media/icons/application/pdf.png');
                                    break;
                                case 'application/pdf':
                                    thumbnail.css('background', 'url(img/pdf.png');
                                    break;
                                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                                    thumbnail.css('background', 'url(img/doc.png');
                                    break;
                            }
        */


                            //to do
                            var myKindInput = $("#translations\\["+dzContainer.dataset.language_id+"\\]\\[kind\\]");


                            mySubmitButton.click(function (e) {
                                e.preventDefault();
                                //console.info(myDropzone);
                                myDropzone.processQueue();
                            });
                            myDropzone.on('success', function (file, response) {
                                var media_properties = [
                                    'name',
                                    'path',
                                    'size',
                                    //'kind',
                                    'extension'
                                ];
                                myKindInput.val(response.data.mimetype);
                                for (i = 0; i < media_properties.length; i++) {
                                    $("#translations\\["+dzContainer.dataset.language_id+"\\]\\["+media_properties[i]+"\\]").val(response.data[media_properties[i]]);
                                }
                                if (media_type == 'image'){
                                    var image_properties = [
                                    'width',
                                    'height',
                                    'orientation',
                                    'provider',
                                    'size1',
                                    'ratio'];
                                    for (i = 0; i < image_properties.length; i++) {
                                        $("#translations\\["+dzContainer.dataset.language_id+"\\]\\[images\\]\\["+image_properties[i]+"\\]").val(response.data[image_properties[i]]);
                                    }

                                }
                                //var myKindInput = $("#translations\\["+dzContainer.dataset.language_id+"\\].find('dropzone .dz-preview.dz-file-preview .dz-image:last')");
                                var myThumbnail = $('.dropzone .dz-preview.dz-file-preview .dz-image:last');
                                console.info(myThumbnail);

                                //myPathInput.val(file.extension);
                                //myPathInput.val(file.m);

                                //myExtensionInput
                                //$('#name_nl').val(file.name);

                                /*
                                 myDropzone.removeAllFiles();
                                 $('#name').val("");
                                 */
                            });
                            myDropzone.on('error', function (file, error ,xhr) {
                                 //console.info('onError');
                                console.warn(error.message);
                                //console.info(xhr);

                                //myDropzone.removeAllFiles();
                                //$('#name_nl').val(file.name);
                            });

                            myDropzone.on('addedfile', function (file) {
                                if (media_type == 'file'){
                                    $(file.previewElement).find('img').attr('src','/media/icons/application/pdf.png')
                                    console.warn($(file.previewElement).find('img'));
                                }
                                //console.info('onError');


                                //console.info(xhr);

                                //myDropzone.removeAllFiles();
                                //$('#name_nl').val(file.name);
                            });

                            // Update the total progress bar
                            myDropzone.on("totaluploadprogress", function(progress) {
                               $(".dropzone-container .progress-bar").css('width',progress+'%');
                                       //.style.width = progress + "%";
                                //console.warn($("#total-progress .progress-bar"));
                                console.info(progress);
                            });

                            myDropzone.on("queuecomplete", function(progress) {
                                $(".dropzone-container .progress-bar").attr('opacity','0');
                                console.info('klaar');

                            });



                            //console.warn(dzContainer.dataset.language_id);
                            //console.info(myDropzone);
                        },
                        sending: function (file, xhr, formData) {
                            formData.append("media_type", "<?php echo($medium->media_type)?>");
                            //formData.append("_token", $('input[name="_token"]').val());
                            $(".dropzone-container .progress-bar").attr('opacity','1');
                            //$("#total-progress").attr('opacity','1');
                            console.info('sending '+dzContainer.dataset.language_id+'');
                        }


                    });
                });

            test = true;
            if (test){
                    var dzContainer = $('.dropzone-container');
                    var dzContainer = $('#media_edit_create_form_dropzone');
                    new Dropzone(dzContainer.children('.dropzone').get(0), {
                        url: '{{route('admin::media.upload')}}',
                        //paramName:'photo',
                        maxFiles:1,
                        addRemoveLinks:true,
                        autoProcessQueue: false,
                        clickable: ".fileinput-button, #fileInput_nl",
                        headers: {
                            'X-CSRF-Token': $('input[name="_token"]').val()
                        },
                        init: function () {
                            var myDropzone = this;
                            var media_type = $('input[name="media_type"]').val();
                            var mySubmitButton = $(dzContainer).find('input[type="submit"]');

                            var allLanguageCodes = "<?php echo($enabledLocalesString)?>";
                            allLanguageCodes = allLanguageCodes.split(",");
                            //var choosenLanguageCode = $('#active_language_tab').val();
                            mySubmitButton.click(function (e) {
                                e.preventDefault();
                                //console.info(myDropzone);
                                myDropzone.processQueue();
                            });
                            myDropzone.on('success', function (file, response) {
                                //console.error($('#media_edit_create_form_dropzone')[0].dataset.language_id);
                                var choosenLanguageCode = $('#active_language_tab').val();
                                choosenLanguageCode = choosenLanguageCode.split(",");

                                //console.warn(allLanguageCodes);
                                var media_properties = [
                                    'name',
                                    'path',
                                    'size',
                                    'mime_type',
                                    'extension'
                                ];
                                var image_properties = [
                                    'width',
                                    'height',
                                    'orientation',
                                    'provider',
                                    'size1',
                                    'ratio'
                                ];


                                var localesToUpdate = choosenLanguageCode;

                                if ($('#use_main_lng_media_for_all_lng').prop('checked')) {
                                    localesToUpdate = allLanguageCodes;
                                }

                                console.info(localesToUpdate.length);

                                for (var x = 0; x < localesToUpdate.length; x++){

                                    for (i = 0; i < media_properties.length; i++) {
                                        $("#translations\\[" + localesToUpdate[x] + "\\]\\[" + media_properties[i] + "\\]").val(response.data[media_properties[i]]);
                                    }

                                    $("#translations\\[" + localesToUpdate[x] + "\\]\\[thumbnail\\]").attr("src", "/" + response.data.thumbnail);

                                    if (media_type == 'image') {
                                        for (i = 0; i < image_properties.length; i++) {
                                            $("#translations\\[" + localesToUpdate[x] + "\\]\\[images\\]\\[" + image_properties[i] + "\\]").val(response.data[image_properties[i]]);
                                        }
                                    }
                                }

                                var myThumbnail = $('.dropzone .dz-preview.dz-file-preview .dz-image:last');
                                //$(".dropzone-container .progress-bar").attr('opacity','1');
                                myDropzone.removeAllFiles();
                                //console.info(myThumbnail);

                                //myPathInput.val(file.extension);
                                //myPathInput.val(file.m);

                                //myExtensionInput
                                //$('#name_nl').val(file.name);

                                /*
                                 myDropzone.removeAllFiles();
                                 $('#name').val("");
                                 */
                            });
                            myDropzone.on('error', function (file, error ,xhr) {
                                //console.info('onError');
                                console.warn(error.message);
                                //console.info(xhr);

                                //myDropzone.removeAllFiles();
                                //$('#name_nl').val(file.name);
                            });

                            myDropzone.on('addedfile', function (file) {

                                //return false;
                                if (media_type == 'file'){
                                    $(file.previewElement).find('img').attr('src','/media/icons/application/pdf.png');
                                    //console.warn($(file.previewElement).find('img'));
                                }
                                //console.info('onError');


                                //console.info(xhr);

                                //myDropzone.removeAllFiles();
                                //$('#name_nl').val(file.name);
                            });

                            // Update the total progress bar
                            myDropzone.on("totaluploadprogress", function(progress) {
                                //$(".dropzone-container .progress").css('height','20px');
                                //$(".dropzone-container .progress").css('opacity','1');

                                $(".dropzone-container .progress-bar").css('width',progress+'%');

                                //.style.width = progress + "%";
                                //console.warn($("#total-progress .progress-bar"));
                                //console.info(progress);
                            });

                            myDropzone.on("queuecomplete", function(progress) {
                                //$(".dropzone-container .progress-bar").attr('opacity','0');
                                //console.info('klaar');

                            });



                            //console.warn(dzContainer.dataset.language_id);
                            //console.info(myDropzone);
                        }
                        ,

                        complete: function (file) {
                            //$(".dropzone-container .progress-bar").attr('opacity','1');
                            $(".dropzone-container .progress-bar").css('width','0%');
                            //$(".dropzone-container .progress").css('height','0');
                            //$(".dropzone-container .progress").css('opacity','0');

                        },


                        sending: function (file, xhr, formData) {
                            formData.append("media_type", "<?php echo($medium->media_type)?>");
                            formData.append("active_language_tab", ""+$('#active_language_tab').val()+"");
                            formData.append("media_id", "<?php echo($medium->id)?>");

                            if ($('#use_main_lng_media_for_all_lng').prop('checked')){
                                formData.append("use_main_lng_media_for_all_lng", $('#use_main_lng_media_for_all_lng').prop('checked'));
                            }


                            //formData.append("_token", $('input[name="_token"]').val());
                            //$(".dropzone-container .progress-bar").attr('opacity','1');
                            //$("#total-progress").attr('opacity','1');
                            //console.info('sending '+dzContainer.dataset.language_id+'');
                        }



                    });

            }
        })
    </script>
@stop
