
    <div class="modal-header">
        <h3 class="modal-title">Create Media</h3>
    </div>
    <div class="modal-body">


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
        <button class="btn btn-primary" type="button" ng-click="okTEST()">OK</button>
        <button class="btn btn-primary" type="button" ng-click="ok()">OK</button>
        <button class="btn btn-warning" type="button" ng-click="cancel()">Cancel</button>
    </div>

