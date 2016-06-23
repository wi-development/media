<?php

namespace WI\Media;

#use App\Media\Image;
use Illuminate\Database\Eloquent\Model;
use DB;



use Intervention\Image\Facades\Image as ImageIntervention;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Media extends Model
{
    //
    protected $fillable = [
        'created_by_user_id',
        'updated_by_user_id',
        'media_type',
        'status',
        'order_by_number'
    ];

    protected $baseDir = 'media/test';

    private $media_types = ['image','file','video'];





    public function getUpdatedAtAsDiffForHumansValue() {

        //return 'tat';
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['updated_at'])->diffForHumans();
        //return date('m/d/Y', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAsToFormattedDateStringValue() {

        //return 'tat';
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['updated_at'])->toFormattedDateString();
        //return date('m/d/Y', strtotime($this->attributes['created_at']));
    }



    public function translations()
    {
        return $this->hasMany('WI\Media\MediaTranslation')
            ->orderBy('locale_id', 'DESC')
            ;
    }

    public function translation()
    {
        return $this->hasOne('App\MediaTranslation')->where('locale_id',config('app.locale_id'));
    }

    public function qbtranslation()
    {
        return $this->join('mediatranslations as mt','media.id','=','mt.media_id');
    }

    public function created_by_user(){
        return $this->belongsTo('Wi\User\User','created_by_user_id');
    }

    public function updated_by_user(){
        return $this->belongsTo('WI\User\User','updated_by_user_id');
    }


    public function getMediaTypes(){
        return $this->media_types;
    }


    public function getMediable($mime_type)
    {
        $file_type = strpos($mime_type, 'image');
        //not
        if ($file_type === false) {
            $file_type = 'file';
        }
        //image
        else {
            $file_type = 'image';

        }


        $className = "WI\\Media\\" . ucfirst($file_type) . "";

        if( ! class_exists($className)) {
            throw new \RuntimeException('Incorrect media type');
        }
        return new $className;
    }


    public function getName($uploadedFile){
        return pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME).'_'.time().'.'.$uploadedFile->getClientOriginalExtension();
    }

    /**
     * @param UploadedFile $file
     */
    public static function fromForm(UploadedFile $uploadedFile){
        $media = new static;
        $mediable = $media->getMediable($uploadedFile->getClientMimeType());
        return $mediable->moveFile($uploadedFile);
    }



    public function setDummyDataForTranslation($media,$enabledLocale){

        //dc($media);
        $media->translations[$enabledLocale->languageCode] = new MediaTranslation();

                $media->translations[$enabledLocale->languageCode]->media_id = $media->id;
                $media->translations[$enabledLocale->languageCode]->locale_id = $enabledLocale->id;

                $media->translations[$enabledLocale->languageCode]->title = 'new nameYY1 MODEL, new enabled locale '.$enabledLocale->languageCode.'';
        /*                $media->translations[$enabledLocale->identifier]->slug = 'new slugYY1 MODEL, new enabled locale '.$enabledLocale->identifier.'';
                */
    }
}

/*
class Image extends Media{
    public function store(){
        return "store Image";
    }
}

class Video extends Media{
    public function store(){
        return "store Video";
    }
}*/