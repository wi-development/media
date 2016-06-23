<?php

namespace WI\Media;


use WI\Media\Repositories\Media\MediaRepositoryInterface;
#use App\Media;

use WI\Locale\Locale; //kan weg??

#use App\Media\Image;


use App\Providers\Html\HtmlServiceProvider;
use Collective\Html\HtmlFacade;
use Datatables;
use File;
use Flash;


use Html;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Intervention\Image\Facades\Image as ImageIntervention;
use App\Mediatranslation;

use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Input;
use League\Flysystem\Exception;


use Response;
use Route;
use Session;
use Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaController extends Controller
{
    private $media;
    private $locale;
    /**
     * MediaController constructor.
     * @param MediaRepositoryInterface $media
     * @param Locale $locale
     */
    public function __construct(MediaRepositoryInterface $media, Locale $locale)
    {
        $this->media = $media;
        $this->locale = $locale;
    }


    /**
     * @param null $media_type
     * @return \Illuminate\View\View
     */
    public function index($media_type = null)
    {
        if($media_type)
        {
            $media = $this->media->getAllMediaByTypeAndPaginate($media_type,10);
        }
        else{
            $media = $this->media->getAllMediaPaginate(50);
            $media_type = 'all';
        }

        $pagination = ($media instanceof LengthAwarePaginator);
        $columnNames = null;
        //NEW
        return view('admin.media.index-add',compact('media','columnNames','pagination','media_type'));
        //ORG
        //return view('admin.media.index',compact('media','columnNames','pagination','media_type'));
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getIndexAll(){


        //$sitemap = Sitemap::with('translation','template')->where('id',$sitemap_id)->first();


        $tableConfig = [];
        $tableConfig['allowSortable'] = false;
        $tableConfig['header'] = 'Overzicht van alle media';
        $tableConfig['customSearchColumnValues'] = "['image','file','video']";

        Session::put(
            'previous_route', [
            'name'=>null,
            //'url'=>(route(Route::currentRouteName(),['sitemap_parent_id'=>$sitemap_id])),
            'url'=>(route(Route::currentRouteName())),
            'anchorText'=> $tableConfig['header']
        ]);

        if(request()->ajax()){
            //$breadcrumbAsHTML = $this->sitemap->getBreadcrumbForMenuIndexTableAjax($breadcrumb,'alle pagina\'s');
            //return response()->json(['name' => 'Abigail', 'state' => 'CA']);
            return Response::json([
                'sitemap'=>'$sitemap->toArray()',
                'allowed_child_templates'=>'$allowed_child_templates->toArray()',
                'allowed_child_templates_as_html'=>'$allowed_child_templates_as_html',
                //'session_test'=> Session::all(),
                'breadcrumbAsHTML'=>'$breadcrumbAsHTML',
                'tableConfig'=>$tableConfig
            ]);
        }
        else{
            //$breadcrumbAsHTML = $this->sitemap->getBreadcrumbForMenuIndexTableAjax($breadcrumb,'alle pagina\'s');
            //$breadcrumbAsHTML = $this->sitemap->getBreadcrumbForMenuIndexTable($breadcrumb);
        }

        $media = collect([]);
        $allowed_child_templates = null;
        $breadcrumbAsHTML = null;

        return view('media::index',compact('media','tableConfig','allowed_child_templates','breadcrumbAsHTML'));

    }


    /**
     * return Media Json Object for the Yajra\DataTable
     *
     *
     * @param Request $request
     * @param int $sitemap_parent_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAllData(Request $request, $sitemap_parent_id = 0)
    {

        //$media = Media::fromForm('teast');
        //dc($media);
        //return "view";
        $datatable =  Datatables::of($this->media->getDataTableBuilder());
        //$datatable->setRowId('sortable_'.'{{$id}}');
        $datatable->addColumn('check', '<input type="checkbox" name="selected_dt_row[]" value="{{ $id }}">{{ $id }}');
        //$datatable->addColumn('check', '<label class="form-checkbox form-normal form-primary active form-text"><input type="checkbox" name="selected_dt_row[]" value="{{ $id }}"></label>{{ $id }}');
        $datatable->editColumn('filename', function ($media) {



            $r = "<div class=\"extraData\" style='display:none;'>";



            /*
             * $r .= Html::getEditButton($media);
                $r .= Html::getEditButton($media);
         */

            //$r .= "<span class='pulxl-right'>".$test->urlPath."</span><br>";

            $r .= "<a class=\"btn btn-success btn-labeled-x\" href=\"".route('admin::media.type.edit',['media_type'=>$media->media_type,'id'=>$media->id])."\" >
                    <i class=\"fa fa-pencil fa-1x\"></i> edit</a> ";

            $r .= "<a class=\"btn btn-danger btn-labeled-x setTable\" onclick=\"wiDeleteMedia(".$media->id.")\">
                    <i class=\"fa fa-trash fa-1x\"></i> delete</a> ";

            $r .= "<a class=\"btn btn-default btn-md btn-labeled-x\" href=\"/".$media->path."\" target=\"_blank\">
                    <i class=\"fa fa-search fa-1x\"></i> preview</a> ";

            $r .= "</div>";
            $thumbnail = '<span class="pull-left" style="margin-right: 5px;"><img src="/'.$media->thumbnail.'" style="width:100px;"></span>';
            $fileName = str_replace('.'.$media->extension.'','',$media->filename);
            return $thumbnail.'<strong>'.$fileName.'</strong><bR>'.$media->filename.'<br><strong style="position:absolute;margin-top:11px;">'.$r.'</strong>';
        });
        return ($datatable->make(true));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        $id = $request->route()->parameter('id') ? $request->route()->parameter('id') : null;

        $media_type = $request->route()->parameter('media_type') ? $request->route()->parameter('media_type') : null;

        if (is_null($media_type)){
            $media = Media::findOrFail($id);
            $media_type = $media->media_type;
        }
        $enabledLocales = $this->locale->getEnabled();
        $medium = $this->media->getMediumByType($id, $media_type);
        //dc($medium);
        $use_main_lng_media_for_all_lng = true;

        $enabledLocalesString = [];
        foreach($enabledLocales as $locale){
            array_push($enabledLocalesString,$locale->languageCode);
        }
        $enabledLocalesString = implode(",",$enabledLocalesString);
        $media = $medium;
        //dc($media);
        //return view('admin.media.__edit',compact('media','medium','enabledLocales','use_main_lng_media_for_all_lng'));  //ORG

        return view('media::edit',compact('medium','enabledLocales','enabledLocalesString','use_main_lng_media_for_all_lng'));            //NEW
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!(in_array($request->get('media_type'),$this->media->getMediaTypes()))){
            dd('geen media_type: '.$request->get('media_type').'');
        }
        //update the user
        $request->merge(array('updated_by_user_id' => auth()->user()->id));
        $retval = DB::transaction(function () use ($request,$id) {
            try
            {
                dc($request->all());
                $retval = "";
                $medium_type = $request->get('media_type');
                $medium = $this->media->getMediumByType($id, $medium_type);//incl resetKeyTranslation
                $medium->update($request->all());

                foreach($this->locale->getEnabled() as $key1 => $enabledLocale){
                    $key = $enabledLocale->languageCode;
                    //heeft post een translation, wanneer er nieuwe taal is toegevoegd niet
                    if (array_key_exists($key, $medium->translations->all())) {
                        $medium->translations[$key]->update($request->translations[$key]);
                        //dc($medium->translations[$key]);
                        $medium->translations[$key]->{$medium_type.'s'}->update($request->translations[$key][$medium_type.'s']);
                    }
                    //new language added, after post creation
                    else{//new enabled locale after created postTranslation
                        $translation = $request->translations[$key];
                        $translation = array_add($translation,'locale_id',$enabledLocale->id);
                        $translation = array_add($translation,'created_by_user_id',1);
                        $translation = array_add($translation,'updated_by_user_id',1);
                        $medium
                            ->translations()->create($translation)
                            ->{$medium_type}()->create($translation[$medium_type]);
                    }
                }
                return $retval;
            }
            catch (Exception $e)
            {
                dc("x ".$e->getMessage());
            }
        });
        //Flash::success('Your Media translation has been updated! '.$retval.'');
        //return "view";
        return redirect()->back();
    }




    //upload from dropzone..
    public function upload(Request $request){
        /*
            $this->validate($request,[
                'photo' => 'required|mimes:jpg,jpeg,png,bmp'
            ]);
        */

        $file = $request->file('file'); //Symfony\Component\HttpFoundation\File\UploadedFile

        $fileInfo = Media::fromForm($file);


        if ($fileInfo){
            $response['data'] = $fileInfo;

            $request->merge([
                'file_info' =>$fileInfo
            ]);


            if ($request->has('media_id')){
                //update media
                $response['created_medium'] = $this->updateAjax($request);
            }
            else{
                //create media
                $response['created_medium'] = $this->storeAjax($request);
            }

            $status = '200';
            return response()->json($response, $status);

        }
        else{
            return Response::json('error', 400);//???
        }
    }

    //used by upload
    private function storeAjax(Request $request)
    {

        $media_type = $request->get('media_type') ? $request->get('media_type') : null;

        //$media_type = 'image';
        //dc('media_type: '.$media_type);
        //dc(auth()->user()->id);
        //return true;
        //to getMediaTypes
        if (!(in_array($media_type,$this->media->getMediaTypes()))){
            //echo('geen media_type: '.$media_type.'');
            throw new Exception('geen media_type: '.$media_type.' (storeAjax)');
        }

        $request->merge([
            'created_by_user_id'=>auth()->user()->id,
            'updated_by_user_id'=>auth()->user()->id,
            'order_by_number'=>'0', //kan weg
            'status'=>'ajax', //kan weg
        ]);
        //dc($request->all());

        $created_medium = DB::transaction(function () use ($request,$media_type) {
            try
            {
                //dc('trye');
                //dc($media_type);

                //dc($request->all());
                //$test = Media::create($request->all());
                //dc($test);
                //return true;
                $created_medium = $this->media->getMediumByType(Media::create($request->all())->id, $media_type);
                //dc($created_medium);
                //dc('testsxxxxx');
                //dc(Media::create($request->all()));
                //return true;
                foreach($this->locale->getEnabled() as $key => $enabledLocale){
                    /*
                    if ($request->has('use_main_lng_media_for_all_lng')) {
                        //$lng_for_all = $request->get('active_language_tab');
                        //$lng_for_all = 'nl';
                        $localeRequest = $request->file_info;
                            //array_add($request->translations[$lng_for_all], 'file_info', $request->get('file_info'));
                    }
                    else{
                        $localeRequest = array_add($request->translations[$enabledLocale->languageCode], 'file_info', $request->get('file_info'));
                    }
                    */

                    $localeRequest = $request->file_info;
                    $localeRequest = array_add($localeRequest,'locale_id',$enabledLocale->id);
                    $localeRequest = array_add($localeRequest,'created_by_user_id',auth()->user()->id);
                    $localeRequest = array_add($localeRequest,'updated_by_user_id',auth()->user()->id);


                    $created_mediumTranslation = $created_medium->translations()->create($localeRequest);
                    //->{$media_type.'s'}()->create($translation[$media_type.'s']);
                    $created_mediumTranslation->{$media_type.'s'}()->create($localeRequest);



                }

                $medium = $this->media->getMediumAndAllTranslationsByIdAndType($created_medium->id, $created_medium->media_type);

                return $medium;
            }
            catch (\Exception $e)
            {
                throw new \Exception('Store AJAX : '.$e->getMessage().'
                    ');

                //dc('[{"Something went wrong: ": "'.$e->getMessage().'", "error ":"'.get_class($e).'","code": "'.$e->getCode().'"}]');
                //return '[{"Something went wrong: ": "'.$e->getMessage().'", "error ":"'.get_class($e).'","code": "'.$e->getCode().'"}]';

            }
        });
        return $created_medium;

        //return $request->all();

        //Flash::success('Your Medium translation has been created!');
        //return redirect()->back();
    }

    //used by upload
    private function updateAjax(Request $request)
    {
        $media_type = $request->get('media_type') ? $request->get('media_type') : null;

        //to getMediaTypes
        if (!(in_array($media_type,$this->media->getMediaTypes()))){
            echo('geen media_type: '.$media_type.'');
            throw new Exception('geen media_type: '.$media_type.' (storeAjax)');
        }

        $request->merge([
            'created_by_user_id'=>auth()->user()->id,
            'updated_by_user_id'=>auth()->user()->id,
            'order_by_number'=>'0', //kan weg
            'status'=>'ajax', //kan weg
        ]);

        //$created_medium = $this->media->getMediumByType(Media::create($request->all())->id, $media_type);
        //echo "TESTXX";
        //print_r_pre($created_medium);


        //echo ($request->all());
        //$inserted_media = Media::create($request->all());
        //$request->merge([
        //    'media_id'=>$inserted_media->id,
        //]);
        //return $request->all();
        $created_medium = $this->media->getMediumByType($request->get('media_id'), $media_type);
        //return($created_medium);
        $created_medium = DB::transaction(function () use ($request,$media_type) {
            try
            {
                $created_medium = $this->media->getMediumByType($request->get('media_id'), $media_type);
                //?
                //$request->merge([
                //    'created_medium'=>$created_medium,
                //]);
                foreach($this->locale->getEnabled() as $key => $enabledLocale){
                    $localeRequest = $request->file_info;
                    $localeRequest = array_add($localeRequest,'locale_id',$enabledLocale->id);
                    $localeRequest = array_add($localeRequest,'created_by_user_id',auth()->user()->id);
                    $localeRequest = array_add($localeRequest,'updated_by_user_id',auth()->user()->id);

                    if  (
                        ($request->has('use_main_lng_media_for_all_lng'))   ||

                        ((!($request->has('use_main_lng_media_for_all_lng'))) && ($request->get('active_language_tab') == $enabledLocale->languageCode))
                    )
                    {
                        //echo $enabledLocale->languageCode;
                        $mediaTranslation = $created_medium->translations[$enabledLocale->languageCode];
                        $mediaTranslation->update($localeRequest);
                        //return $mediaTranslation;
                        $mediaTranslation->{$media_type . 's'}->update($localeRequest);


                        //$created_mediumTranslation = $created_medium->translations()->create($localeRequest);
                        //->{$media_type.'s'}()->create($translation[$media_type.'s']);
                        //$created_mediumTranslation->{$media_type.'s'}()->create($localeRequest);
                    }
                }


                $medium = $this->media->getMediumAndAllTranslationsByIdAndType($created_medium->id, $created_medium->media_type);

                return $medium;

            }
            catch (\Exception $e)
            {
                return '{"Something went wrong: ": "'.$e->getMessage().'", "error ":"'.get_class($e).'","code": "'.$e->getCode().'"}';

            }
        });
        return $created_medium;

        //return $request->all();

        //Flash::success('Your Medium translation has been created!');
        //return redirect()->back();
    }






    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = DB::transaction(function () use ($id) {
            try {
                //$id = null;

                $deletedRecords = Media::destroy($id);
                if (!($deletedRecords == count($id))){
                    throw new \Exception('Niet alle media is verwijderd
                        <br>ids: '.(is_array($id) ? implode(",",$id) : $id).'
                    ');
                }

                if (request()->ajax()) {
                    $data = ['status' => 'succes', 'statusText' => 'Ok', 'responseText' => 'Delete is gelukt'];
                    return $data;
                }
                Flash::success('Delete is gelukt! ' . $id . '');
            } catch (\Exception $e) {
                if (request()->ajax()){
                    throw new \Exception('Verwijderen van Media is niet gelukt.<br>MediaController->destroy()
                        <br>'.$e->getMessage().' ');

                }
                Flash::error('Delete is mislukt!<br>' . $e->getMessage() . ' ' . $id . '');
            }
        });

        if(request()->ajax()){
            return response()->json($data, 200);
        }
        return redirect()->back();
    }


    private function getFilesForDeleteByIDs($mediaIDs){
        $public_path = public_path().'/';

        $media = $this->media->getMediaTranslationsByIds($mediaIDs);

        $delete_files = collect([]);

        //get all files to delete
        foreach ($media as $key => $medium){
            foreach ($medium->translations as $key1 => $translation){
                //dc($medium->media_type);
                switch ($medium->media_type) {
                    case 'image':
                        $file_path = $public_path.$translation->path;
                        $delete_files->push($file_path);
                        $file_path = $public_path.$translation->thumbnail;
                        $delete_files->push($file_path);
                        break;
                    case 'file':
                        $file_path = $public_path.$translation->path;
                        $delete_files->push($file_path);
                        break;
                    case 'video':

                        break;
                }
            }
        }
        $delete_files = $delete_files->unique();
        $delete_files = $delete_files->all();
        return $delete_files;
    }

    private function checkIfFilesExists($delete_files){
        foreach ($delete_files as $key => $delete_file){
            //check if files exists
            if (!(File::exists($delete_file))){
                throw new FileNotFoundException("File does not exist at path {$delete_file}<br>MediaController->checkIfFilesExists()");
            }
        }
        return true;
    }

    private function deleteFiles($delete_files){
        //$delete_files = ['filezxc1.jpg', 'filzxce2.jpg'];
        if (!(File::delete($delete_files))){
            throw new \Exception('Bestanden wel gevonden maar niet verwijderd (rechten?).
                <br>MediaController->deleteFiles()
                <br>delete_files:<br> '.(is_array($delete_files) ? implode("<br>",$delete_files) : $delete_files).'
            ');
        }
        return true;
    }


    /**
     * Remove the specified resource from storage.
     * Called form DataTable
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyBulk(Request $request)
    {

        //string to array
        $mediaIDs = $request->input('mediaIDs');

        $mediaIDs = (explode(",",$mediaIDs));

        //get files
        $files = $this->getFilesForDeleteByIDs($mediaIDs);

        //check files
        if ($this->checkIfFilesExists($files)){
            //delete files from disk
            $this->deleteFiles($files);

            //delete files from database
            $this->destroy($mediaIDs);
            //return false;
        }
        if (request()->ajax()) {
            $data = ['status' => 'succes', 'statusText' => 'Oke', 'responseText' => 'Bulk delete is gelukt'];
            return response()->json($data, 200);
        };
    }




/*NG MODAL VIEW*/

//api
    //mediaLibrary for select media ng.modal
    public function apiIndex($media_type=null){
        $medium = "als test";
        //dc($media_type);

        //$media_type = 'image';
        if($media_type)
        {
            //was, maar alleen maar 1 translation
            //$media = $this->media->getAllMediaByTypeAndPaginate($media_type,3);

            $media = $this->media->getAllMediaAndAllTranslationsByTypeAndPaginate($media_type,5);
        }
        //$media[0]->updated_at =
        //$media[0]->created_at_test = $media[0]->getUpdatedAtAsDiffForHumansValue($media[0]->created_at);


        //set updated_at_diff_for_humans for json
        foreach($media as $key => $medium){
            $medium->updated_at_diff_for_humans = $medium->getUpdatedAtAsDiffForHumansValue();
            $medium->updated_to_formatted_date_string = $medium->getUpdatedAtAsToFormattedDateStringValue();

        }

        $retval = collect([
            'media' => $media
            //,'paginate' => $this->renderAjax($media),
        ]);
        //echo response()->json($retval);
        //return(($retval));
        //dc($media->pluck('translations')->first()->all());
        //return 'view';
        return(response()->json($retval));
    }

    //mediaLibrary for select media ng.modal
    public function apiIndexOrder($media_type=null){
        $medium = "als test";
        //dc($media_type);
        //$media_type = 'image';
        //dc(request()->all());
        if($media_type)
        {
            //was, maar alleen maar 1 translation
            //$media = $this->media->getAllMediaByTypeAndPaginate($media_type,3);
            $media = $this->media->getAllMediaAndAllTranslationsByTypeAndPaginateOrder($media_type,3);


        }
        /*
        else{
            $media = $this->media->getAllMediaPaginate(2);
            $media_type = 'all';
        }

        $pagination = ($media instanceof LengthAwarePaginator);
        $columnNames = null;
*/
        $retval = collect([
            'media' => $media
            //,'paginate' => $this->renderAjax($media),
        ]);
        return($retval);
    }


//modal (ng modal view)
    //select media modal
    public function ngTemplateSelectMediaModal(){
        $media_type = false;
        return view('media::modal_select_media');
        //return view('admin.media.modal_select_media');
    }

//modal (ng modal view)
    public function ngTemplateSelectMediaCreate(){
        $media_type = false;
        return view('media::modal_create_media');
    }
















    /* NEW */


    #\Illuminate\Database\Query\Builder
    #Illuminate\Database\Query\Builder

    public function findById($id, array $relationships = []) {
        //return Survey::with($relationships)->find($id);
    }

    public function getQBMediaTranslation(){






        //return (Media::with(['translation']));        // \Illuminate\Database\Eloquent\Builder|static

        //return (Media::all());                        // \Illuminate\Database\Eloquent\Collection|static[]

        //return (DB::table('media'));                  // \Illuminate\Database\Query\Builder

        //return (DB::table('media')->get());           // array|static[]

        //dc(DB::table('media'));     // \Illuminate\Database\Query\Builder

        //$media = (Media::join('mediatranslations as mt','media.id','=','mt.media_id'));





        //return (Media::with(['translation']));
            // \Illuminate\Database\Eloquent\Builder|static
            // \----  with: \Illuminate\Database\Query\Builder
            // \----  with: \App\Media

//        (($test = Media::with(['translation'])->find(31)));
          //dc($test->getRelations());
            // \Illuminate\Database\Eloquent\Collection|static[]
            // \---- with: array|static[]
            // \---- ----- with: App\Media (ORM) (relations)
            // \---- ----- ----- with: relations


        //return (DB::table('media'));
            // \Illuminate\Database\Query\Builder


        //return (DB::table('media')->get());
            // array|static[]


        //return (Media::join('mediatranslations as mt','media.id','=','mt.media_id'));
            // mixed:
            // \Illuminate\Database\Eloquent\Builder
            // \----  with: \Illuminate\Database\Query\Builder
            // \----  with: \App\Media

        //return (Media::join('mediatranslations as mt','media.id','=','mt.media_id')->get());
            // mixed:
            // \Illuminate\Database\Eloquent\Collection
            // \---- with: array|static[]
            // \---- ----- with: App\Media (StdClass)

return Media::join('mediatranslations as mt','media.id','=','mt.media_id')->find(31);




//return "a";

        /*dc(Media::all());

        dc(DB::table('media')->find(31));

        dc(DB::table('media')->get());

        return "view";
        $media = DB::table('media')->join('mediatranslations as mt','media.id','=','mt.media_id');
        dc($media->get());
        $media = (Media::join('mediatranslations as mt','media.id','=','mt.media_id'));
        dc($media->get());
*/
        /*
         *
                //$media = Media::join('mediatranslations as mt','media.id','=','mt.media_id');
                //dc(Media::class);
                return $media;
        */
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function getElqMediaTranslationAsBO(){
        $media = Media::with(['translation']);
        return $media;
        //\Illuminate\Database\Eloquent\Collection|static

    }











    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function selectMediaTypeBeforeCreate(Request $request)
    {
        $choosen_media_type = $request->get('media_type');
        if ($choosen_media_type != null){
            return $this->create($request);
        }
        $media_types = $this->media->getMediaTypesForList();
        return view('admin.media.select',compact('media_types'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function UITcreate($request)
    {
        $choosen_media_type = $request->get('media_type');
        if ($choosen_media_type == null){
            return $this->selectMediaTypeBeforeCreate($request);//hmm?..
        }
        $enabledLocales = $this->locale->getEnabled();
        $medium = collect([]);
        $medium->media_type = $choosen_media_type;
        $use_main_lng_media_for_all_lng = true;
        return view('admin.media.create',compact('medium','enabledLocales','use_main_lng_media_for_all_lng'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function UITstore(Request $request)
    {
        $media_type = $request->get('media_type') ? $request->get('media_type') : null;
        if (!(in_array($media_type,$this->media->getMediaTypes()))){
            dd('geen media_type: '.$media_type.'');
        }
        $request->merge([
            'created_by_user_id'=>auth()->user()->id,
            'updated_by_user_id'=>auth()->user()->id
        ]);
        DB::transaction(function () use ($request,$media_type) {
            try
            {
                $created_medium = $this->media->getMediumByType(Media::create($request->all())->id, $media_type);
                foreach($this->locale->getEnabled() as $key => $enabledLocale){
                    $translation = $request->translations[$enabledLocale->identifier];
                    $translation = array_add($translation,'locale_id',$enabledLocale->id);
                    $translation = array_add($translation,'created_by_user_id',auth()->user()->id);
                    $translation = array_add($translation,'updated_by_user_id',auth()->user()->id);
                    $created_medium
                        ->translations()->create($translation)
                        ->{$media_type.'s'}()->create($translation[$media_type.'s']);
                }
            }
            catch (\Exception $e)
            {
                dd($e->getMessage());
            }
        });
        Flash::success('Your Medium translation has been created!');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }





























    /* VOOR SEEDER */

    public function uploadTestUIT($file){
        /*
            $this->validate($request,[
                'photo' => 'required|mimes:jpg,jpeg,png,bmp'
            ]);
        */


        //return $file;

        //$file = $request->file('file'); //Symfony\Component\HttpFoundation\File\UploadedFile

        $fileInfo = Media::fromForm($file);

        //return $fileInfo;
        //dc($fileInfo['mime_type']);
        $request = request();

        //$this->call('PATCH', 'admin/predict-score/fixtures', $data);
        //$response = $this->call('GET', 'user/profile');
        dc($request);


        /*
 * "media_type" => "image"
"file" => UploadedFile {#30

 * */



        if ($fileInfo){
            $response['data'] = $fileInfo;



            $fileInfo = [
                'directory' => 'directory',
                'name' => 'name',
                'path' => 'new',
                'size' => 'new->getSize()',
                'mime_type' => 'new->getMimeType()',
                'extension' => 'new->getExtension()',
                'size' => '100'
            ];
            $request->merge([
                //'file_info' => $fileInfo,
                //'path' => 'new'
                'media_type' => 'image'
            ]);


//            dc($request->all());
            $test = [];
            //dc($test);
            //$test = $request->all();
            \Auth::loginUsingId(1);
            //dc(Media::create($test));
            //return true;
            //dc($request->file_info);
//            return true;
            if ($request->has('media_id')){
                //update media
                $response['created_medium'] = $this->updateAjax($request);
            }
            else{
                //create media
                $response['created_medium'] = $this->storeAjax($request);
            }

            $status = '200';
            return response()->json($response, $status);

        }
        else{
            return Response::json('error', 400);//???
        }
    }

    //used by upload
    private function gcdUIT ($a, $b) {
        return ($b == 0) ? $a : $this->gcd($b, $a%$b);
    }

    //used by upload
    private function getFileIconUIT($fileExtension){
        $fileExtensionPath = 'media/icons/application/';
        switch ($fileExtension) {
            case 'pdf':
            case 'doc':
            case 'docx':
            case 'xls':
            case 'xlsx':
                $thumbNail =  $fileExtensionPath.$fileExtension.".png";
                break;
            case 'gif':
            case 'jpg':
            case 'jpeg':
            case 'png':
                $thumbNail = 'MC->getFileIcon';
                //$thumbNail = str_replace('poly/images','media/images','/'.$medium->translations[$key]->path);
                //$thumbNail = str_replace('poly/files','media/files',$thumbNail);
                break;
            default:
                $thumbNail =  $fileExtensionPath."default.png";
        }
        /*
                $image = ImageIntervention::make($thumbNail);
                $image = $image->widen(100, function ($constraint) {
                    $constraint->upsize();
                });

                $image->resizeCanvas(100, 75);
                $image->save($fileExtensionPath.'thumbnail/'.$fileExtension.".png");
        */

        return $thumbNail;
    }


   /*KAN WEG*/


    /**
     * Process datatables ajax request.
     * Used by 'admin.media.all.data' via 'this.getMenuIndex()'
     * @return \Illuminate\Http\JsonResponse
     */
    public function xtest(){

    }

    private function xtestUpdate($id, $test){
        $mt = Mediatranslation::find($id);
        if (($mt->mediable()->update($test->all()))){
            dc($mt->mediable);
        }
    }

    private function xtestMorph(){
        //http://stackoverflow.com/questions/33189347/get-data-from-polymorphic-relations-with-namespaces

        //$medium->translations[$key]->{$medium_type.'s'}->update

        //STORE
        //https://laracasts.com/discuss/channels/eloquent/multiple-polymorphic-relationships-with-the-same-controller

        $u = collect(['provider' => 'updateproviderNEW1234']);
        $this->testUpdate(121,$u); //image

        $u = collect(['application' => 'updateApplicationNEW1234']);
        $this->testUpdate(122,$u); //file

        dc(Media\Image::class);
        dc(Media\File::class);
        dc(Media\Video::class);


        return "views";
        $mt = Mediatranslation::find(121);     //image
        $u = collect(['provider' => 'updateproviderNEW']);
        dc($mt->mediable()->update($u->all()));

        $this->testUpdate(121,$u); //image

        $u = collect(['application' => 'updateApplicationNEW']);
        $this->testUpdate(122,$u); //file

        $mt1 = MediaTranslation::find(122);    //file
        //dc($mt1);
        $u = collect(['application' => 'updateApplicationNEW']);
        dc($mt1->mediable()->update($u->all()));


        //$mt = Mediatranslation::with(['mediable'])->where('id',121)->first();
        //dc($mt->mediable);

        $mt1 = Mediatranslation::with(['mediable'])->where('id',122)->first();
        dc($mt1->mediable);

        $mt1 = Mediatranslation::with(['mediable'])->where('id',121)->first();
        dc($mt1->mediable);


        return "view";

        $test = Mediatranslation::with(['image'])->where('id',121)->get()->all();
        //dc($test);


        $img = Image::with(['media'])->get();
        //dc($img);

        foreach ($img as $image) {
            //
            dc($image->media);
        }
        return 'view';

        return "view";
        //$mt = Mediatranslation::with('mediable')->get();
        $mt = Mediatranslation::with('mediable')->whereIn('id',['121','122'])->get();

        foreach ($mt as $t){
            //dc($t->mediable->orientation);

            dc(class_basename($t->mediable));


        }
        //dc($mt->first()->mediable);

        return "view";
        $mt = Mediatranslation::with('mediable')->whereIn('id',['121','122']);
        dc($mt->get());

        return "view";
        $media = Media::find(31);

        $media = $media->translation->mediable();
        dc($media->getResults());
        dc('-----');
        $mediaTranslation = MediaTranslation::find(122);
        //dc($mediaTranslation);
        $mediable = $mediaTranslation->mediable();
        dc($mediable->getResults());



        $image = Image::find(62);

        dc($image->media()->getResults());


        //foreach ($staff->photos as $photo) {
        //
        //}




        return "view";

    }


}
