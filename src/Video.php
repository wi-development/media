<?php

namespace App\Media;

use App\Media;
use Illuminate\Database\Eloquent\Model;

class Video extends Media
{
    public $timestamps = false;

    protected $baseDir = "media/video";



    public function media()
    {
        return $this->morphOne('App\MediaTranslation', 'mediable');
    }

    public function getThumbnail(){
        return "VIDEO THUMBNAIL";
    }


    public function mediaNiet()
    {
        $this->belongsTo('App\Media');
        //return $this->morphMany('App\Media', 'mediable');
    }
}
