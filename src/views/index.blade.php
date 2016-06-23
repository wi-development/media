@extends('dashboard::layouts.master')


@section('content')



        <!--Page Title-->
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <div id="page-title">
            <h1 class="page-header text-overflow">{{$tableConfig['header']}}</h1>
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
            <li><a href="{{route('admin::dashboard')}}">dashboard</a></li>
            <li class="active">media</li>
        </ol>
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <!--End breadcrumb-->


        <!--Page content-->
        <!--===================================================-->
        <div id="page-content">
            @include('flash::message')
            <div class="panel">

                <div class="panel-heading">
                    <div class="panel-control wi-control">
                        <span id="createPage">
                            <meta name="csrf-token" content="{{ csrf_token() }}">
                            <meta name="upload-route" content="{{ route('admin::media.upload') }}">
                            <meta name="media-destroy-bulk" content="{{ route('admin::media.destroy.bulk') }}">
                            <meta name="media-destroy" content="{{ route('admin::media.destroy') }}">

                            <button data-target="#demo" data-toggle="collapse" class="btn btn-warning btn-labeled fa fa-cog btn-defxault" type="button" aria-expanded="false">Media toevoegen</button>
                        </span>
                    </div>
                    <h3 class="panel-title">
                        Media:
                        <small>{!! $breadcrumbAsHTML !!}</small>
                    </h3>
                </div>

                <!-- Laravel/DataTables Table - Filtering -->
                <!--===================================================-->

                <div class="panel-body">


                    <div class="collapse" id="demo" aria-expanded="false" style="height: 0px;">


                        <div class="panel panel-default">
                            <!-- Default panel contents -->
                            <div class="panel-heading hide">
                                Afbeelding uploaden
                            </div>
                            <div class="panel-body">

                                <?php
                                $test_array = ['nl'];
                                foreach ($test_array as $key => $language){?>
                                <div class="dropzone-container" data-language_id="{{$language}}" style="border:1px none blue">
                                    <!-- The global file processing state -->


                                    <div class="fileupload-process">
                                        <div aria-valuenow="0" aria-valuemax="100" aria-valuemin="0" role="progressbar" class="total-progress progress progress-striped active"
                                             style="opacity: 0;">
                                            <div data-dz-uploadprogress="" style="width: 0%;" class="progress-bar progress-bar-success"
                                                 id="test_{{$language}}"
                                            ></div>
                                        </div>
                                    </div>



                                    <!---  Field --->
                                    <div class="dropzone clearfixx fileInput">
                                        <div class="dz-default dz-message">
                                    <span>
                                        Drop files here to upload<br>
                                        or<br>
                                        <div class="fileinput-button btn btn-default btn-sm">Select files</div>

                                    </span>
                                        </div>

                                        <div class="fallback hide">
                                            <input name="file" type="file" multiple />
                                        </div>

                                    </div>


                                    <div class="table table-striped files rowx" style="border:1px none red;">
                                        <?php
                                        if ($key == 0){?>
                                        <div id="dropzone_preview_template" class="file-row col-lg-3">
                                            <!-- This is used as the file preview template -->


                                            <div>
                                                <span class="preview"><img data-dz-thumbnail /></span>
                                            </div>
                                            <div>
                                                <p class="name" data-dz-name></p>
                                                <strong class="error text-danger" data-dz-errormessage></strong>
                                            </div>
                                            <div>
                                                <p class="size" data-dz-size></p>
                                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                                    <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                                </div>
                                                <div class="dz-success-mark hide"><span>✔</span></div>
                                                <div class="dz-error-mark hide"><span>✘</span></div>
                                            </div>


                                            <div>
                                                <button class="btn btn-primary start">
                                                    <i class="xglyphicon xglyphicon-upload"></i>
                                                    <span>Start</span>
                                                </button>
                                                <button data-dz-remove class="btn btn-warning cancel">
                                                    <i class="gxlyphicon xglyphicon-ban-circle"></i>
                                                    <span>Cancel</span>
                                                </button>
                                                <button data-dz-remove class="btn btn-danger delete">
                                                    <i class="xxglyphicon xglyphicon-trash"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </div>
                                        </div>
                                        <?php
                                        }?>
                                    </div>


                                    <div style="border: 1px none green; margin-bottom:20px" class="clearfix">

                                        <div class="fileinput-button btn btn-default btn-sm pull-right">Select files</div>

                                        <div class="btn btn-default btn-sm pull-right submit-all">Uploaden</div>


                                        <div class="btn btn-default btn-sm pull-right cancel-all">Cancel</div>
                                    </div>
                                </div>
                                <?php
                                }?>






                            </div>

                        </div>




                    </div>



                    <table class="table table-bordered table-hover toggle-circle table-striped-uit sortable-uit showExtraData" id="users-table" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="toggleCheckAll" autocomplete="off"></th>
                            <th>Bestand</th>
                            <th>User</th>
                            <th>Gemaakt op</th>
                            <th>Gewijzigd op</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Bestand</th>
                            <th>User</th>
                            <th>Gemaakt op</th>
                            <th>Gewijzigd op</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!--===================================================-->
                <!-- End Laravel/DataTables - Filtering -->
            </div>
        </div>
        <!--===================================================-->
        <!--End page content-->



@endsection

@section('css.head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/t/bs/dt-1.10.11,rr-1.1.1/datatables.min.css" rel="stylesheet">
@endsection

    @section('scripts.footer')

        <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.js"></script>
        <script src="/js/wi/dropzone/myDropzone.js"></script>

    <!--<script src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.11/js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/t/bs/dt-1.10.11,rr-1.1.1/datatables.min.js"></script>
    -->

    <script type="text/javascript" src="/js/wi/datatables/datatables-tonny.js"></script>
        <!--<script src="/js/dashboard.js"></script>-->
    <script src="/js/wi-data.js"></script>
        <script>
            var tableConfig = {

                urlIndex: '{{ route('admin::media.index.all.data')}}',
                urlDataRoot: '{{ route('admin::media.index.all.data') }}',
                urlData: '{{ route('admin::media.index.all.data',['sitemap_parent_id'=>1]) }}',


                urlMediaUpload:'{{ route('admin::media.upload') }}',
                urlMediaDestroyBulk:'{{ route('admin::media.destroy.bulk') }}',
                urlMediaDestroy:'{{ route('admin::media.destroy') }}',
                csrf_token: '{{ csrf_token() }}',


                customSearchButtonValue:'status',
                customSearchColumn:'media.media_type',
                customSearchColumnValues:{!! $tableConfig['customSearchColumnValues'] !!},
                allowSortable:'{{$tableConfig['allowSortable']}}',
                orderColumnInit:3,
                orderColumnInitType:'desc',
                columns: [
                    {data: 'check', name: 'check',visible:true, searchable: false,width:'1%',orderable:false},
                    //{data: 'id', name: 'id',visible:true, searchable: false,class:'dragpointer',width:'1%'},
                    //{data: 'path', name: 'path',visible:true, searchable: false,class:'dragpointer',width:'1%'},
                    {data: 'filename', name: 'mt.name',visible:true},
                    //{data: 'published_at', name: 'sitemaptranslations.published_at'},
                    {data: 'media_created_name', name: 'm_cu.name',searchable: true,width:'140px' },
                    {data: 'media_created_at', name: 'media.created_at',searchable: true,width:'140px' },
                    {data: 'media_updated_at', name: 'media.updated_at',orderable: false, searchable: false,visible:false },
                    {data: 'media_type', name: 'media.media_type', orderable: true, searchable: true,visible:true}
                ]
            };
            //console.info({!! $tableConfig['customSearchColumnValues'] !!});
            setTable(tableConfig);

            //});
        </script>
        @if (Session::has('flash_notification.message'))
            <script src="/nifty/js/demo/ui-panels.js"></script>
            <script>

                $.niftyNoty({
                    type: 'info',
                    container : '#page-content',
                    html : '<h4 class="alert-title">{{ Session::get('flash_notification.level') }}</h4><p class="alert-message">{{ Session::get('flash_notification.message') }}</p><div class="mar-top"><button type="button" class="btn btn-info" data-dismiss="noty">Close this notification</button></div>',
                    closeBtn : false
                });

                /*
                 $.niftyNoty({
                 type: 'purple',
                 container : 'floating',
                 title : 'Update gelukt!',
                 message : 'De volgorde is aangepast.',
                 closeBtn : false,
                 timer : 2000
                 //,
                 //onShow:function(){
                 //	alert("onShow Callback");
                 //}
                 });
                 */
            </script>
        @endif



    @endsection




