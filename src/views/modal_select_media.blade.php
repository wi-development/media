<div class="panel-body">
    <div class="modal-content">
        <div class="modal-header">
            <div class="pull-right search">
                <input type="text"
                       placeholder="Zoeken"
                       autocomplete="off"
                       class="form-control"
                       ng-model="sharedData.searchNameValue"
                       ng-model-options='{ debounce: 500 }'
                       ng-change='setSearchNameValue()'>
            </div>
            <h3 class="modal-title">Media overzicht</h3>

        </div>
        <div class="modal-body" style="padding-top: 0px">

            <div class="bootstrap-tablex">
                <div class="fixed-table-toolbar hidden">
                    <div class="bars pull-left">
                    </div>
                    <div class="columns columns-right btn-group pull-right">
                    </div>
                    <div class="pull-right search">
                    </div>
                </div>
                <div class="fixed-table-container-uit">
                    <div class="fixed-table-headerx hidden">

                    </div>
                    <div class="fixed-table-bodyx">
                        <table border="0" style="width:100%;" class="table ng-media-table table-striped table-bordered table-hover dataTable">
                            <thead>
                            <tr>
                                <th style="width:14px" class="id" ng-click="setOrderByValue('id');" class="id">#</th>
                                <th style="width:100px" class="text-center">Afbeelding</th>
                                <th class="sorting name" ng-click="setOrderByValue('name');">Naam</th>
                                <th style="width:115px" class="sorting updated_by_user_name" ng-click="setOrderByValue('updated_by_user_name');">User</th>
                                <th style="width:150px" class="sorting updated_at sorting_desc" ng-click="setOrderByValue('updated_at');">Date</th>
                                <th style="width:1%">Actie</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="medium in mediaLibrary[this_form_media_field.mediaType]">
                                <td>
                                    [[((paginator[this_form_media_field.mediaType].showFrom)+$index)]]
                                    <div class="hidden">
                                    [[medium.translations[''+this_form_media_field.locale+''].id]] -
                                    [[medium.translations[''+this_form_media_field.locale+''].media_id]]<br>
                                    [[medium.translations[''+this_form_media_field.locale+''].locale_id]]
                                    </div>
                                </td>
                                <td>
                                    <span style="border-radius:12px;position:relative;display:block;border:1px solid silver;height:72px;">
                                    <span class="thumbnailx" style="position:relative;overflow: auto;width:100px;height:70px;display:inline-block;background:white;
                                    border:4px solid white;border-radius: 12px;xpadding: 4px">
                                        <img ng-if="this_form_media_field.mediaType == 'image'"

                                                ng-src="/[[medium.translations[''+this_form_media_field.locale+''].path]]"
                                             class="img-thumbnailx img-responsivex" style="min-width:100px;height:100px;position: absolute">
                                        <img ng-if="this_form_media_field.mediaType == 'file'"

                                             ng-src="/[[medium.translations[''+this_form_media_field.locale+''].thumbnail]]"
                                             class="img-thumbnailx img-responsivex" style="min-width:100px;height:100px;position: absolute">
                                    </span></span>
                                </td>
                                <td>
                                    [[medium.translations[''+this_form_media_field.locale+''].name]]
                                    <ANY ng-if="this_form_media_field.mediaType == 'file'">
                                        <br>/[[medium.translations[''+this_form_media_field.locale+''].path]]
                                        <br><a href="[[medium.translations[''+this_form_media_field.locale+''].path]]" class="btn-link hidden" target="_blank">bekijk<i class="fa fa-eye"></i></a></a>
                                        <br>                    <a type="button"
                                                                   href="/[[medium.translations[''+this_form_media_field.locale+''].path]]"
                                                                   target="_blank"
                                                                   tooltip-placement="top" uib-tooltip="bekijk de pdf" type="button" style="margin-right: 6px" class="btn btn-xs btn-link btn-default add-tooltip ng-binding"><i class="fa fa-eye"></i></a>

                                    </ANY>
                                </td>
                                <td style="white-space: nowrap">
                                    [[medium.updated_by_user.name]]</td>
                                <td>
                                    <span class="text-muted"><i class="fa fa-clock-o"></i> [[medium.updated_to_formatted_date_string]]</span>
                                    <br>[[medium.updated_at_diff_for_humans]]<br>
                                    <br>[[medium.updated_at]]
                                </td>

                                <td><div><a ng-click="addMediaTranslations(medium.translations);" class="btn btn-success btn-labeled-x">
                                            <i class="fa fa-plus fa-1x"></i> voeg toe</a></div>


                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="fixed-table-pagination" style="display: block;">
                        <div class="pull-left pagination-detail"><span
                                    class="pagination-info">Toont [[paginator[this_form_media_field.mediaType].showFrom]] t/m [[paginator[this_form_media_field.mediaType].showTo]] van [[paginator[this_form_media_field.mediaType].bigTotalItems]] items</span><span
                                    class="page-list hidden"><span class="btn-group dropup"><button data-toggle="dropdown"
                                                                                                    class="btn btn-default dropdown-toggle"
                                                                                                    type="button"><span
                                                class="page-size">5</span> <span class="caret"></span></button><ul
                                            role="menu" class="dropdown-menu">
                                        <li class="active"><a href="javascript:void(0)">5</a></li>
                                        <li><a href="javascript:void(0)">10</a></li>
                                        <li><a href="javascript:void(0)">20</a></li>
                                    </ul></span> records per page</span></div>
                        <div class="pull-right pagination">
                            <uib-pagination
                                    ng-click="paginate(paginator[this_form_media_field.mediaType].bigCurrentPage)"

                                    total-items="paginator[this_form_media_field.mediaType].bigTotalItems"
                                    ng-model="paginator[this_form_media_field.mediaType].bigCurrentPage"
                                    items-per-page="paginator[this_form_media_field.mediaType].itemsPerPage"
                                    max-size="maxSize"
                                    class="pagination-sm" boundary-links="true" rotate="true"
                                    num-pages="numPages"
                            ></uib-pagination>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary hidden" type="button" ng-click="okTEST()">OK</button>
            <button class="btn btn-primary hidden" type="button" ng-click="ok()">OK</button>
            <button class="btn btn-warning" type="button" ng-click="cancel()">Sluiten</button>
        </div>
    </div>
</div>


<div class="hidden">

<div class="panel-body" style="padding-bottom:0px">
    gekozen afbeelding:
    <hr>
    <div ng-repeat="mediatranslation in form_media_field.values[this_form_media_field.id]" class="pull-left">
    <div class="hide">
        lId: [[mediatranslation.locale_id]]<br>
        mtId:[[mediatranslation.id]]<br>
        mediaId:[[mediatranslation.media_id]]<br>
        lId: [[this_form_media_field.locale]]<br>
        formName:[[this_form_media_field.fieldName]]
    </div>
    <input type="hidden" name="[[field_id]][]" id="[[field_id]][]" value="[[mediatranslation.id]]">

    <img ng-src="/[[mediatranslation.thumbnail]]" class="thumbnail ng-cloak" style="width:100px;margin-right:8px;">
    <input type="button"
           ng-click="removeMedium(this_form_media_field,mediatranslation.media_id)"
           class="
                       btn btn-xs
                       btn-danger btn-active-dark

                       ng-cloak"
           value="delete">
    <a type="button"
       href="/admin/media/image/[[mediatranslation.media_id]]/edit"
       class="
                       btn btn-xs
                       btn-primary btn-active-dark

                       ng-cloak"
       target="_blank"
       xxng-click="open_modal_create_media('lg',this_form_media_field)"
       value="wijzigen">wijzigen [[mediatranslation.media_id]]</a>
</div>
</div>
<hr>
    <div class="modal-header">
        <h3 class="modal-title">Media overzicht
            <small>[[this_form_media_field.buttonSelectValue]]</small>
        </h3>

    </div>
    <div id="modelID" class="modal-body" style="padding-top: 0px">

        <div class="bootstrap-tablex">
            <div class="fixed-table-toolbar">
                <div class="bars pull-left">
                </div>
                <div class="columns columns-right btn-group pull-right">

                </div>
                <div class="pull-right search">


                    <input type="text"
                           placeholder="Zoeken"
                           autocomplete="off"
                           class="form-control"
                           ng-model="sharedData.searchNameValue"
                           ng-model-options='{ debounce: 500 }'
                           ng-change='setSearchNameValue()'>
                </div>
            </div>
            <div class="fixed-table-container-uit">
                <div class="fixed-table-headerx">





                </div>
                <div class="fixed-table-bodyx">
                    <table border="0" style="width:100%;" class="table ng-media-table table-striped-x table-bordered table-hover dataTable">
                        <thead>
                        <tr>
                            <th style="width:30px" class="sorting id sorting_asc" ng-click="setOrderByValue('id');" class="id">Id</th>
                            <th style="width:167px">Afbeelding</th>
                            <th style="width:20%" class="sorting updated_at" ng-click="setOrderByValue('updated_at');">Datum</th>
                            <th class="sorting name" ng-click="setOrderByValue('name');">Naam</th>
                            <th style="width:1%">Actie</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="medium in mediaLibrary[this_form_media_field.mediaType]">
                            <td>
                                [[medium.translations[''+this_form_media_field.locale+''].id]] -
                                [[medium.translations[''+this_form_media_field.locale+''].media_id]]

                            </td>
                            <td>    <span class="thumbnail" style="height:100px">
                    <img ng-src="/[[medium.translations[''+this_form_media_field.locale+''].thumbnail]]"
                         class="img-thumbnail img-responsive">
                    </span>
                            </td>
                            <td>
                                [[medium.updated_at_diff_for_humans]]<br>
                                [[medium.updated_at]]
                            </td>
                            <td>
                                [[medium.translations[''+this_form_media_field.locale+''].name]]
                            </td>
                            <td><div><a ng-click="addMediaTranslations(medium.translations);" class="btn btn-success btn-labeled-x">
                                        <i class="fa fa-plus fa-1x"></i> voeg toe</a></div></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="fixed-table-pagination" style="display: block;">
                    <div class="pull-left pagination-detail"><span
                                class="pagination-info">Showing 1 to 5 of 25 rows</span><span
                                class="page-list"><span class="btn-group dropup"><button data-toggle="dropdown"
                                                                                         class="btn btn-default dropdown-toggle"
                                                                                         type="button"><span
                                            class="page-size">5</span> <span class="caret"></span></button><ul
                                        role="menu" class="dropdown-menu">
                                    <li class="active"><a href="javascript:void(0)">5</a></li>
                                    <li><a href="javascript:void(0)">10</a></li>
                                    <li><a href="javascript:void(0)">20</a></li>
                                </ul></span> records per page</span></div>
                    <div class="pull-right pagination">
                        <uib-pagination
                                ng-click="paginate(paginator[this_form_media_field.mediaType].bigCurrentPage)"

                                total-items="paginator[this_form_media_field.mediaType].bigTotalItems"
                                ng-model="paginator[this_form_media_field.mediaType].bigCurrentPage"
                                items-per-page="paginator[this_form_media_field.mediaType].itemsPerPage"
                                max-size="maxSize"
                                class="pagination-sm" boundary-links="true" rotate="true"
                                num-pages="numPages"
                        ></uib-pagination>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary hidden" type="button" ng-click="okTEST()">OK</button>
        <button class="btn btn-primary hidden" type="button" ng-click="ok()">OK</button>
        <button class="btn btn-warning" type="button" ng-click="cancel()">Sluiten</button>
    </div>


</div>