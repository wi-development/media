<?php

namespace WI\Media;

use Illuminate\Database\Eloquent\Model;

class MediaTranslation extends Model
{

    protected $table = 'mediatranslations';

    protected $fillable = [
        'created_by_user_id',
        'updated_by_user_id',
        'locale_id',
        'title',
        'description',
        'name',
        'extension',
        'mime_type',
        'size',
        'path',
        'thumbnail',
        'type'
    ];


    //public function mediable()
    //{
    //    return $this->morphTo('mediable');
    //}

   // public function image()
   // {
   //     return $this->belongsTo('App\Media\Image', 'mediable_id');
   // }


    //touch partent timestamps
    protected $touches = array('media');

    public function setTitleAttribute($title)
    {
        $this->attributes['title'] = trim($title) !== '' ? $title : null;
    }

    public function media(){
        return $this->belongsTo('WI\Media\Media');
    }

    public function locale(){
        return $this->belongsTo('WI\Locale\Locale');
    }

    public function images(){
        return $this->hasOne('WI\Media\Image','mediatranslation_id');
    }

    public function files(){
        return $this->hasOne('WI\Media\File','mediatranslation_id');
    }

    public function videos(){
        return $this->hasOne('WI\Media\Video','mediatranslation_id');
    }

    public function created_by_user(){
        return $this->belongsTo('WI\User\User','created_by_user_id');
    }

    public function updated_by_user(){
        return $this->belongsTo('WI\User\User','updated_by_user_id');
    }

}
