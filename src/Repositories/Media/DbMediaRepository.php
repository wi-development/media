<?php

namespace WI\Media\Repositories\Media;

use WI\Core\Repositories\DbRepository;
use WI\Media\Media;
use WI\Locale\Locale;


use DB;

#use SitemapRepositoryInterface;


/**
 * @property Sitemap model
 */
class DbMediaRepository extends DbRepository implements MediaRepositoryInterface
{


    /**
     * @var Sitemap
     */
    protected $model;
    //private $locale;


    /**
     * DbSitemapRepository constructor.
     */
    public function __construct(Media $media)
    {
        parent::__construct();
        $this->model = $media;
        /*dc($this->model);
        $this->model = $media;
        dc('asdf');
        dc($this->model);
        dc('asdf');
        //dc('construc Media Repo');
        //parent::__construct();
        */

    }





    //1
    public function getAllMedia($paginate = false){
        //Media->translation->[users]
        $input = "";
        $media = Media::join('mediatranslations','mediatranslations.media_id','=','media.id')//for orderBy
        ->where('mediatranslations.locale_id', auth()->user()->locale->id)
            //->where('media_type','image')
            ->with(
                'created_by_user',
                'translations.created_by_user',
                'translations.updated_by_user'
            //'translations.images'
            )
            ->with(
                (
                array('translations'=>function($query) use ($input){
                    $query->where('locale_id', auth()->user()->locale->id);
                }
                ))
            )
            ->orderBy('mediatranslations.media_id', 'desc')
            ->select(['media.*']);
        if ($paginate){
            return $media->paginate($paginate);
        }
        return $media->get();
    }

    //2
    public function getAllMediaByType($media_type,$paginate = false){
        //Media->translation->[users]
        $input = "";
        $media = Media::join('mediatranslations','mediatranslations.media_id','=','media.id')//for orderBy
        ->where('mediatranslations.locale_id', auth()->user()->locale->id)
            ->where('media_type',$media_type)
            ->with(
                'created_by_user',
                'translations.created_by_user',
                'translations.updated_by_user'
            //'translations.images'
            )
            ->with(
                (
                array('translations'=>function($query) use ($input){
                    $query->where('locale_id', auth()->user()->locale->id);
                }
                ))
            )
            ->orderBy('mediatranslations.media_id', 'desc')
            ->select(['media.*']);
        //->limit(15);
        if ($paginate){
            return $media->paginate($paginate);
        }
        return $media->get();
    }

    //3 for select from mediaLibrary ng.modal
    public function getAllMediaAndAllTranslationsByType($media_type,$paginate = false){
        //Media->translation->[users]

        $input = "";
        $media = Media::join('mediatranslations','mediatranslations.media_id','=','media.id')//for orderBy
        ->join('users','users.id','=','media.updated_by_user_id')
            ->where('mediatranslations.locale_id', auth()->user()->locale->id)
            ->where('media_type',$media_type)
            ->with(
                'updated_by_user',
                'translations.locale',
                'translations.created_by_user',
                'translations.updated_by_user'
            //'translations.images'
            )
            ->with(

                ['translations'=>function($query) use ($input){
                    $query->whereHas('locale', function ($q) { // ...1 subquery to filter the active locales
                        $q->where('status','!=','disabled');
                    });
                }]
            );
        if (request()->has('id')){
            $media->orderBy('mediatranslations.media_id', ''.request()->get('id').'');
        }
        if (request()->has('name')){
            $media->orderBy('mediatranslations.name', ''.request()->get('name').'');
        }
        if (request()->has('updated_at')){
            $media->orderBy('media.updated_at', ''.request()->get('updated_at').'');
        }

        if (request()->has('updated_by_user_name')){
            $media->orderBy('users.name', ''.request()->get('updated_by_user_name').'');
        }

        if (request()->has('search_name')){
            //$media->where('mediatranslations.name', 'LIKE' ,'%'.request()->get('search_name').'%');
            //$media->orWhere('users.name', 'LIKE' ,'%'.request()->get('search_name').'%');
            $media->whereRaw(('(mediatranslations.name LIKE \'%'.request()->get('search_name').'%\' OR users.name LIKE \'%'.request()->get('search_name').'%\') '));
        }

        $media->select(['media.*']);
        //->limit(15);

        if ($paginate){
            return $media->paginate($paginate);
        }
        //dc($media->get());
        return $media->get();
    }

    //3.1 for select and order from mediaLibrary ng.modal
    public function getAllMediaAndAllTranslationsByTypeOrder($media_type,$paginate = false){
        //Media->translation->[users]


        $input = "";
        $media = Media::join('mediatranslations','mediatranslations.media_id','=','media.id')//for orderBy
        ->where('mediatranslations.locale_id', auth()->user()->locale->id)
            ->where('media_type',$media_type)
            ->with(
                'created_by_user',
                'translations.locale',
                'translations.created_by_user',
                'translations.updated_by_user'
            //'translations.images'
            )
            ->with(
                (
                array('translations'=>function($query) use ($input){
                    //uit: get all translations
                    //$query->where('locale_id', auth()->user()->locale->id);
                }
                ))
            )
            ->orderBy('mediatranslations.media_id', 'ASC')
            ->select(['media.*']);
        //->limit(15);

        if ($paginate){
            return $media->paginate($paginate);
        }

        return $media->get();
    }




    /* NEW */












    /**
     *
     * @return mixed:
     *  \Illuminate\Database\Eloquent\Builder
     *  ----  with: \Illuminate\Database\Query\Builder
     *  ----  with: \App\Media
     */
    private function getMediaTranslationBuilder(){
        $mediaListBuilder = Media::join('mediatranslations as mt','media.id','=','mt.media_id');
        return $mediaListBuilder;
    }

    private function getMediaUserBuilder(){
        $mediaListBuilder = Media::join('users as cu','media.created_at_id','=','cu.id')
                            ->join('users as uu','media.updated_at_id','=','uu.id');
        return $mediaListBuilder;
    }



    //return (Media::join('mediatranslations as mt','media.id','=','mt.media_id')->get());
    // mixed:
    // \Illuminate\Database\Eloquent\Collection
    // \---- with: array|static[]
    // \---- ----- with: App\Media (single StdClass)

    private function getMediaTranslationListAsStdObject(){
        $mediaList = Media::join('mediatranslations as mt','media.id','=','mt.media_id');
        //$mediaList = Db::table('media')->join('mediatranslations as mt','media.id','=','mt.media_id');

        $mediaList = $mediaList->get();
        return ($mediaList);
    }

    public function getDataTableBuilder(){
        $mediaListBuilder = Media::join('mediatranslations as mt','media.id','=','mt.media_id')
                                    ->join('users as m_cu','media.created_by_user_id','=','m_cu.id')
                                    ->join('users as m_uu','media.updated_by_user_id','=','m_uu.id');

        $mediaListBuilder = $mediaListBuilder->where('mt.locale_id',1);


        $mediaListBuilder = $mediaListBuilder->select([
            'm_cu.name as media_created_name',
            'm_uu.name as media_updated_name',
            'media.created_at as media_created_at',
            'media.updated_at as media_updated_at',

            'media.id as id',
            'media.media_type as media_type',

            'mt.title as title',
            'mt.description as description',

            'mt.path as path',
            'mt.thumbnail as thumbnail',
	        'mt.name as filename',

            'mt.extension as extension',
            'mt.mime_type as mime_type',
            'mt.size as size'
        ]);

        return $mediaListBuilder;
    }

    public function getDataTable(){
        return $this->getDataTableBuilder()->get();
    }


    //
    // \Illuminate\Database\Eloquent\Collection|static[]
    // \---- with: array|static[]
    // \---- ----- with: App\Media (ORM) (relations)
    // \---- ----- ----- with: relations
    public function getMediaTranslationsByIds($ids){
        $media = Media::with(['translations'])->whereIn('id',$ids)->get();
        return $media;
    }






//override
    public function getByIdx($id){
        return "DbSitemapRepo";
    }

    public function forUser(Media $media)
    {
        /*
        return Task::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();
        */
        //Sitemap
    }


    //1
    public function getAllMediaPaginate($perPage = 15){
        return $this->getAllMedia($perPage);
    }

    //2
    public function getAllMediaByTypeAndPaginate($media_type,$perPage = 15){
        return $this->getAllMediaByType($media_type,$perPage);
    }

    //3 for select from mediaLibrary ng.modal
    public function getAllMediaAndAllTranslationsByTypeAndPaginate($media_type,$perPage = 15){
        $media = $this->getAllMediaAndAllTranslationsByType($media_type,$perPage);
        foreach($media as $key => $medium){
            $this->resetKeyTranslationCollectionByLocaleIdentifier($medium);
        }
        return $media;
    }

    //3.,1 for select and order from mediaLibrary ng.modal
    public function getAllMediaAndAllTranslationsByTypeAndPaginateOrder($media_type,$perPage = 15){
        $media = $this->getAllMediaAndAllTranslationsByTypeOrder($media_type,$perPage);
        foreach($media as $key => $medium){
            $this->resetKeyTranslationCollectionByLocaleIdentifier($medium);
        }
        return $media;
    }








    public function getMediumByType($id,$media_type){
        //Media->translation->[users]
        $input = "";
        $media = Media
            //::join('mediatranslations','mediatranslations.media_id','=','media.id')//for orderBy
        //->where('mediatranslations.locale_id', auth()->user()->locale->id)
            //->where('media_type',$media_type)
            ::with(
                'created_by_user',
                'translations.created_by_user',
                'translations.updated_by_user',
                'translations.'.$media_type.'s'
            )
            ->with(
                ['translations'=>function($query) use ($input){
                    $query->whereHas('locale', function ($q) { // ...1 subquery to filter the active locales
                        $q->where('status','!=','disabled');
                    });
                }]
            )
            //->orderBy('mediatranslations.media_id', 'asc')
            ->findOrFail($id,['media.*']);
        $this->resetKeyTranslationCollectionByLocaleIdentifier($media);
        //dc($media);
        return $media;
    }



    public function getMediumTranslationByIdAndType($id,$media_type){
        //Media->translation->[users]
        $input = "";
        $media = Media
            //::join('mediatranslations','mediatranslations.media_id','=','media.id')//for orderBy
            //->where('mediatranslations.locale_id', auth()->user()->locale->id)
            //->where('media_type',$media_type)
            ::with(
                'created_by_user',
                'translations.created_by_user',
                'translations.updated_by_user',
                'translations.'.$media_type.'s'
            )
            ->with(
                (
                array('translations'=>function($query) use ($input){
                    $query->where('locale_id', auth()->user()->locale->id);
                }
                ))
            )
            //->orderBy('mediatranslations.media_id', 'asc')
            ->findOrFail($id,['media.*']);
        $this->resetKeyTranslationCollectionByLocaleIdentifier($media);

        return $media;
    }


    public function getMediumAndAllTranslationsByIdAndType($id,$media_type){
        //Media->translation->[users]
        $input = "";
        $media = Media
            //::join('mediatranslations','mediatranslations.media_id','=','media.id')//for orderBy
            //->where('mediatranslations.locale_id', auth()->user()->locale->id)
            //->where('media_type',$media_type)
            ::with(
                'created_by_user',
                'translations.created_by_user',
                'translations.updated_by_user',
                'translations.'.$media_type.'s'
            )
            ->with(
                (
                array('translations'=>function($query) use ($input){
                    //$query->where('locale_id', auth()->user()->locale->id);
                }
                ))
            )
            //->orderBy('mediatranslations.media_id', 'asc')
            ->findOrFail($id,['media.*']);
        $this->resetKeyTranslationCollectionByLocaleIdentifier($media);
        return $media;
    }




    private function resetKeyTranslationCollectionByLocaleIdentifierUIT($post){
        dc($this->enabledLocales);
        foreach($this->enabledLocales as $key => $enabledLocale) {
            if (isset($post[$key])) {
                //fout
                //$post[$enabledLocale->identifier] = $post[$key];//
                $post[$post[$key]->locale->identifier] = $post[$key];//GOED!
                unset($post[$key]);
            }
            else{//new enabled locale after created sitemapTranslation
                //dc('RET sasdf');
                /*
                $post[$enabledLocale->identifier] = new SitemapTranslation();
                $post[$enabledLocale->identifier]->sitemap_id = $sitemap->id;
                $post[$enabledLocale->identifier]->locale_id = $enabledLocale->id;
                $post[$enabledLocale->identifier]->name = 'new name, new enabled locale '.$enabledLocale->identifier.'';
                $post[$enabledLocale->identifier]->slug = 'new slug, new enabled locale '.$enabledLocale->identifier.'';
                */
            }
        }
        return $post;
    }

    public function getMediaTypes(){
        return $this->model->getMediaTypes();
    }

    public function getMediaTypesForList(){
        $retval = [];
        foreach ($this->model->getMediaTypes() as $media_type){
            $retval[$media_type] = $media_type;
        }
        return $retval;
    }


















    public function ___getAllMediaByType(){
        //Media->translation->[users,images]
        $input = "";
        $media = Media::join('mediatranslations','mediatranslations.media_id','=','media.id')//for orderBy
        ->where('mediatranslations.locale_id', auth()->user()->locale->id)
            ->where('media_type','image')
            ->with(
                'created_by_user',
                'translations.created_by_user',
                'translations.updated_by_user',
                'translations.images')//.locale
            ->with(
                (
                array('translations'=>function($query) use ($input){
                    $query->where('locale_id', 4);
                    /*$query->whereHas('locale', function ($q) { // ...1 subquery to filter the active locales
                        $q->where('name', ''.app()->getLocale().'');
                    });*/
                }
                    //,'template.components.references.translations.locale','references'
                ))
            )
            ->orderBy('mediatranslations.media_id', 'desc')
            ->select([
                'media.*',
                /*'media.media_type','media.status','media.order_by_number',
                'mediatranslations.id as mediatranslations_id',
                'mediatranslations.media_id',
                'mediatranslations.locale_id',
                'mediatranslations.title',
                'mediatranslations.description',
                'mediatranslations.name',
                'mediatranslations.extension',
                'mediatranslations.kind',
                'mediatranslations.size',
                'mediatranslations.path',
                'mediatranslations.created_by_user_id',
                'mediatranslations.updated_by_user_id',
                //'mediatranslations.*'*/
            ])
            ->paginate();
    }











    public function getMediaByUnion()
    {
        $images = \DB::table('media as m')
            ->select('m.*')
            ->addSelect('mt.*')
            //->addSelect('m.id as testid')
            //->select(\DB::raw('m.*, mt.*,m.id as test'))
            ->join('mediatranslations as mt', 'm.id', '=', 'mt.media_id')
            ->join('images as i', 'mt.id', '=', 'i.mediatranslation_id')
            ->orderBy('m.id')
            ->where('mt.locale_id', '1');

        $files = \DB::table('media as m')
            ->select('m.*')
            ->addSelect('mt.*')
            //->addSelect('m.id as testid')
            //->select(\DB::raw('m.*, mt.*,m.id as test'))
            ->join('mediatranslations as mt', 'm.id', '=', 'mt.media_id')
            ->join('files as f', 'mt.id', '=', 'f.mediatranslation_id')
            ->orderBy('m.id')
            //->union($images)
            ->where('mt.locale_id', '1');

        //$files->orderBy('media_id','desc')->get();
        //dc($files->orderBy('media_id','desc')->get());
        //return "test";

        //500 = 23 ms
        $test = $images->unionAll($files)->orderBy('status','desc')->get();
        $test = $this->getPaginator($test,10);
        return $test;
    }

    public function getMediaByRawUnion(){
        //snelst //500 = 11 GOED
        $media = \DB::raw('
            SELECT * FROM
            (select m.* from media as m
            INNER JOIN mediatranslations as mt ON m.id = mt.media_id
            INNER JOIN images as i ON mt.id = i.mediatranslation_id
            WHERE mt.locale_id = 1) images
            UNION
            SELECT files.* FROM
            (select m.* from media as m
            INNER JOIN mediatranslations as mt ON m.id = mt.media_id
            INNER JOIN files as f ON mt.id = f.mediatranslation_id
            WHERE mt.locale_id = 1) files
            ORDER BY id');

        $media = \DB::select('SELECT images.* FROM (SELECT m.* FROM media AS m INNER JOIN mediatranslations AS mt ON m.id = mt.media_id WHERE mt.locale_id = 1) images UNION SELECT files.* FROM (SELECT m.* FROM media AS m INNER JOIN mediatranslations AS mt ON m.id = mt.media_id WHERE mt.locale_id = 1) files');


        //500 = 15
        $media = \DB::select('
            SELECT images.* FROM
            (select m.*, i.id as image_id from media as m
            INNER JOIN mediatranslations as mt ON m.id = mt.media_id
            INNER JOIN images as i ON mt.id = i.mediatranslation_id
            WHERE mt.locale_id = 1) images
            UNION
            SELECT files.* FROM
            (select mx.*, fx.application as file_id from media as mx
            INNER JOIN mediatranslations as mtx ON mx.id = mtx.media_id
            INNER JOIN files as fx ON mtx.id = fx.mediatranslation_id
            WHERE mtx.locale_id = 1) files
            ORDER BY image_id
        ');
        //traag 500 record 37 ms
        $media = \DB::select('
            SELECT * FROM
            (
                SELECT * FROM
                (
                    (select m.id as idx, m.media_type, mt.* from media as m
                        INNER JOIN mediatranslations as mt ON m.id = mt.media_id
                        INNER JOIN images as i ON mt.id = i.mediatranslation_id
                        WHERE mt.locale_id = 1) imagesx
                )
            UNION
                SELECT * FROM
                (
                    (select m1.id as idx, m1.media_type, mt1.* from media as m1
                        INNER JOIN mediatranslations as mt1 ON m1.id = mt1.media_id
                        INNER JOIN files as i1 ON mt1.id = i1.mediatranslation_id
                        WHERE mt1.locale_id = 1) filesx
                )
            )
            as t
            WHERE media_type = \'file\'
            ORDER BY idx ASC
        ');






        //$media = $this->getPaginator($media,10);
        return $media;
    }



    public function getAllMediaMerge($paginate = false){

        //dc($this->model->getMediaTypes());
        $merged_collection = collect();
        foreach ($this->model->getMediaTypes() as $mediaType){
            $col = $this->getAllMediaByActiveLocaleAndType($mediaType);
            $merged_collection = $merged_collection->merge($col);
        }


        //$merged_collection = $merged_collection->toArray();
        //$merged_collection = collect($merged_collection);
        //dc($merged_collection);

       // dc($merged_collection);



/*
        $projects = \App\Project::all();
        $news = \App\Post::all();
        $foo = \App\Foo::all();

        $all = $projects->merge($news)->merge($foo);
*/


         //   dc($all_sorted);
        //$merged_collection = $merged_collection->sortBy('translations[0].name');

       //dc($merged_collection);
        //$user->exams()->orderBy('date', 'asc')->get()


        if (is_int($paginate)){

            //order key values and use for paginate() item counter
            //Reset the keys on the underlying array.
            $merged_collection = $merged_collection->values();
            //dc($merged_collection);
            $merged_collection = $this->getPaginator($merged_collection->all(),$paginate);
            //dc($merged_collection);
        }




        return $merged_collection;
    }

    public function getAllMediaByActiveLocaleAndType($type){
        $input = "";
        $media = Media::with('translations.locale')->with((
        array('translations'=>function($query) use ($input){
            $query->whereHas('locale', function ($q) { // ...1 subquery to filter the active locales
                $q->where('status','<>' ,'disabled');
            });
        },'translations.'.$type.'s','created_by_user'
            //,'references'
        )))
            ->with(
                array('translations' => function($q1) {  // 1 query for template of the sitemap with nested collections
                    $q1->whereHas('locale', function ($q2) { // ...1 subquery to filter locale by active language
                        $q2->where('identifier', ''.app()->getLocale().'');
                    });
                })
            )
            //->findOrFail($id);
            ->where('media_type',''.$type.'')
            ->get();


        //dc($media->first()->translations);






        return $media;
    }



    public function getAll(){
/*
        //$this->model->with;
        $sitemap = Sitemap::with(['translations' => function($q) {  // 1 query for photos with...
            $q->whereHas('locale', function ($q) { // ...1 subquery to filter the photos by related tags' name
                $q->where('name', ''.app()->getLocale().'');
                //$q->where('status', 'enabled');
            });
        },'template'])->paginate(30);//->get()

        //dc($sitemap);

        return $sitemap;
*/
    }

    public function getAllByActiveLocale(){
        /*
        $sitemap = Sitemap::with(['translations' => function($q) {  // 1 query for photos with...
            $q->whereHas('locale', function ($q) { // ...1 subquery to filter the photos by related tags' name
                $q->where('name', ''.app()->getLocale().'');
                //$q->where('status', 'enabled');
            });
        },'template'])->paginate(5);//->get()
        //dc(get_class($sitemap));
        //dc($sitemap);
        return $sitemap;
        */
    }
}