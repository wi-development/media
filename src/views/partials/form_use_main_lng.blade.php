{!! Form::hidden('active_language_tab', (session()->has('active_language_tab') ? session()->get('active_language_tab') : 'nl'), ['class' => 'form-control','id' => 'active_language_tab']) !!}


<div class="col-lg-7">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{$frmHeader}}</h3>
        </div>
        <div class="panel-body">






            <div class="xxform-tab">
                @include('flash::message')


                <div class="panel panel-default">
                    <!-- Default panel contents -->
                    <div class="panel-heading">

                        <div class="panel-control">
                            <div class="demo-panel-ref-btn btn btn-default" data-toggle="panel-overlay" data-target="#demo-panel-collapse"><i class="fa fa-refresh"></i></div>
                            <div class="btn btn-default" data-target="#demo-panel-collapse1" data-toggle="collapse"><i class="fa fa-chevron-down"></i></div>
                            <div class="btn btn-default" data-dismiss="panel"><i class="fa fa-times"></i></div>
                        </div>
                        <h3 class="panel-title">Afbeelding uploaden</h3>
                    </div>

                    <div id="demo-panel-collapse1" class="collapse">
                        <div class="panel-body">
                            <div id="media_edit_create_form_dropzone" class="dropzone-container" data-language_id="{{(session()->has('active_language_tab') ? session()->get('active_language_tab') : 'nl')}}">


                                <div>
                                    <p class="size" data-dz-size></p>
                                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                        <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                    </div>
                                </div>

                                <!---  Field --->
                                <div id="fileInput_nl" class="dropzone clearfix fileInput">

                                    <div class="fallback hide">
                                        <input name="file" type="file" multiple />
                                    </div>
                                </div>
                                <div>
                                    <hr>
                                    <div class="fileinput-button">value</div>
                                    <input type="submit" value="Uploaden" id="submit-all_nl"
                                           class="btn btn-default btn-sm pull-right"
                                    />

                                </div>
                            </div>
                        </div>
                    </div>

                </div>









                <!-- Nav tabs || $key == $translation->locale->identifier -->
                <ul class="nav nav-tabs nav-justified-off" role="tablist">
                    @foreach($enabledLocales as $locale)
                        <?php
                        $key = $locale->languageCode;
                        $tClass = "";
                        if ($key==(session()->has('active_language_tab') ? session()->get('active_language_tab') : 'nl')){$tClass = " active";}
                        if (empty($medium->translations[''.$locale->languageCode.'']->id)){$tClass .= " new-locale";}
                        if (array_key_exists($key,$errors->getMessages())){$tClass .= " has-error";}
                        ?>
                        <li role="presentation" class="{{$tClass}}"><a href="#{{$key}}" aria-controls="{{$key}}" role="tab" data-toggle="tab">{{$medium->media_type}} {{$locale->name}}</a></li>
                    @endforeach
                </ul>








                <!-- Tab panes -->
                <div class="tab-content">


                    <?php
                    //$allLocales = [];
                    //$post_type = $post->template->db_table_name;
                    //dc($enabledLocales);
                    //foreach($sitemap->translations as $key => $translation){
                    foreach($enabledLocales as $locale){

                    //dc($locale);
                    $key = $locale->languageCode;
                    $language_id = $key;//for error list //or $key
                    $tClass = "";if ($key==(session()->has('active_language_tab') ? session()->get('active_language_tab') : 'nl')){$tClass = " active";}
                    //temp dummy data
                    //TO DO MODEL SHOULD HAVE THUMBNAIL

                    //echo $thumbNail;
                    ?>

                    <?php
                    $mediaTranslation = $medium->translations[$key];

                    $thumbNail = '/'.$mediaTranslation->path;
                    if ($medium->media_type == 'file'){
                        $thumbNail =  "/media/icons/application/default.png";
                        switch ($mediaTranslation->extension) {
                            case 'pdf':
                            case 'doc':
                            case 'docx':
                            case 'xls':
                            case 'xlsx':
                                $thumbNail =  "/media/icons/application/".$mediaTranslation->extension.".png";
                                break;
                            case 'gif':
                            case 'jpg':
                            case 'jpeg':
                            case 'png':
                                $thumbNail = str_replace('poly/images','media/images','/'.$mediaTranslation->path);
                                $thumbNail = str_replace('poly/files','media/files',$thumbNail);
                                break;
                            default:
                                $thumbNail =  "/media/icons/application/default.png";
                        }
                    }



                    ?>
                    <div role="tabpanel" class="tab-pane{{$tClass}} row" id="{{$key}}">
                        @include('errors.sitemaptranslation')
                                <!--- Title Field --->
                        <div class="col-md-12">

                            <div class="form-group">
                                {!! Form::label('translations['.$key.'][thumbnail]', 'thumbnail:') !!}
                                <div>
                                    <img src="{{$thumbNail}}" id="translations[{{$key}}][thumbnail]" alt="" class="img-thumbnail img-responsivex pullx-left" style="widxth: 100px">
                                </div>
                            </div>
                            <!--- Slug Field --->
                            <div class="form-group">
                                {!! Form::label('translations['.$key.'][title]', 'title:') !!}
                                {!! Form::text('translations['.$key.'][title]', null, ['class' => 'form-control']) !!}
                            </div>

                            @if ($medium->media_type == 'image')

                                <div class="form-group">
                                    {!! Form::label('translations['.$key.'][images][alt]', '[images][alt]:') !!}
                                    {!! Form::text('translations['.$key.'][images][alt]', null, ['class' => 'form-control']) !!}
                                </div>

                                @endif
                                        <!--- Intro Field --->
                                <div class="form-group">
                                    {!! Form::label('translations['.$key.'][description]', 'description '.$key.'') !!}
                                    {!! Form::textarea('translations['.$key.'][description]', null, ['class' => 'form-control']) !!}
                                </div>
                        </div>

                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Info</h3>
                                </div>

                                <div class="panel-body form-horizontal">

                                    <div class="form-group-sm">
                                        {!! Form::label('translations['.$key.'][name]', 'name:', ['class' => 'control-label']) !!}
                                        {!! Form::text('translations['.$key.'][name]', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>

                                    <div class="form-group-sm">
                                        {!! Form::label('translations['.$key.'][extension]', 'extension:', ['class' => 'control-label']) !!}
                                        {!! Form::text('translations['.$key.'][extension]', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>

                                    <div class="form-group-sm">
                                        {!! Form::label('translations['.$key.'][kind]', 'kind:', ['class' => 'control-label']) !!}
                                        {!! Form::text('translations['.$key.'][kind]', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>

                                    <div class="form-group-sm">
                                        {!! Form::label('translations['.$key.'][mime_type]', 'mime_type:', ['class' => 'control-label']) !!}
                                        {!! Form::text('translations['.$key.'][mime_type]', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>

                                    <div class="form-group-sm">
                                        {!! Form::label('translations['.$key.'][size]', 'size:', ['class' => 'control-label']) !!}
                                        {!! Form::text('translations['.$key.'][size]', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>

                                    <div class="form-group-sm">
                                        {!! Form::label('translations['.$key.'][path]', 'path:', ['class' => 'control-label']) !!}
                                        {!! Form::text('translations['.$key.'][path]', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>

                                    @include('media::partials._form_'.$medium->media_type.'')

                                </div>

                            </div>
                        </div>



                        {{$medium->media_type}} form {{$key}}

                    </div>
                    <?php
                    }//endforeach ?>
                </div>
            </div>




        </div>
    </div>
</div>

<div class="col-lg-5">




    <?php

    $status_list = [];
    $sitemap_list = [];
    $template_list = [];
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" role="button" data-toggle="collapse" href="#collapseExamplex" aria-expanded="false" aria-controls="collapseExamplex">Publiceren</h3>
        </div>
        <div id="collapseExamplex" class="collapse in" aria-expanded="true">
            <div class="panel-body" style="padding-top: 20px;">
                {!! Form::hidden('media_type', $medium->media_type) !!}

                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('use_main_lng_media_for_all_lng', 1,$use_main_lng_media_for_all_lng, ['id' => 'use_main_lng_media_for_all_lng','class' => 'form-control-uit']) !!}
                        Gebruik nederlandse afbeelding voor alle talen
                    </label>
                </div>

                <hr>

                <small>
                    {{--$post->published_at--}}
                    <!--- Published at Field --->
                    <div class="form-group">
                        {!! Form::label('created_at', 'Created at:') !!}
                        <br>{{$medium->created_at}}
                        <br>{{$medium->created_by_user->name}}
                    </div>




                    <div class="form-group">
                        {!! Form::label('updated_at', 'Updated at:') !!}
                        <br>{{$medium->updated_at}}
                        <br>{{$medium->updated_by_user->name}}
                    </div>
                </small>


            </div>
            <div class="panel-footer clearfix">
                <div class="form-group">
                    {!! Form::submit($submitButtonText, ['class' => 'btn btn-primary-uit btn-default pull-right btn-sm-uit']) !!}
                </div>
            </div>
        </div>
    </div>

</div>











