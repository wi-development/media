    <div class="modal-header" style="padding-bottom:0px;">
        <h3 class="modal-title" style="padding-top:0px;">Uploaden</h3>
    </div>
    <div class="modal-body" style="padding-top:0px;">


        <?php
        $test_array = ['nl'];
        foreach ($test_array as $key => $language){?>
            <div drop-zone
                 max-file-size="5"
                 auto-process="false"
                 message="Drop file here modal create blade"
                 mimetypes="image/*"
                 id="file-dropzone{{$key}}"
            >
            </div>
        <?php
        }?>



    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="button" ng-click="ok()">Sluiten</button>
        <button class="btn btn-warning hide" type="button" ng-click="cancel()">Cancel</button>
    </div>

