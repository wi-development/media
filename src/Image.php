<?php

namespace WI\Media;


use Intervention\Image\Facades\Image as ImageIntervention;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Image extends Media
{
    protected $fillable = [
        'height',
        'width',
        'orientation',
        'provider',
        'alt',
        'ratio',
        'contentDetailsUit'
    ];

    protected $baseDir = 'media/images';

    public $timestamps = false;



    public function media()
    {
        return $this->morphOne('WI\Media\MediaTranslation', 'mediable');
    }

    /**
     * Get all of the image's media.
     */
    public function mediatranslation()
    {
        $this->belongsTo('WI\Media\MediaTranslation');
        //return $this->morphMany('App\Media', 'mediable');
    }

    //ratio
    private function gcd ($a, $b) {
        return ($b == 0) ? $a : $this->gcd($b, $a%$b);
    }

    private function gcd1($w,$h){

        $r = $this->gcd($w,$h);
        $r = ($w/$r.":".$h/$r);
        return $r;
    }
    public function moveFile(UploadedFile $uploadedFile){

        $name = $this->getName($uploadedFile);
        $baseDir = public_path().'/'.$this->baseDir;
        $file = $uploadedFile->move($baseDir, $name); //FileObject or FileException

        $this->image = [
            //mediatranslation
            'name' => ''.$name.'',
            'path' => ''.$this->baseDir.'/'.$name,
            'thumbnail' => ''.$this->baseDir.'/thumbnails/'.$name,
            'size' => ''.$file->getSize().'',
            'mime_type' => ''.$file->getMimeType().'',
            'extension' => ''.$file->getExtension().'',
            //'mediableObject' => class_basename(Image::class)
            'media_type' => 'image' //mediatranslation->images() mediatranslation->{media_type}s()
        ];

        $this->createThumbnail($name);
        return $this->image;
    }

    public function createThumbnail($name){
        $baseDir = public_path().'/'.$this->baseDir;
        $image = ImageIntervention::make($baseDir.'/'.$name);

        //dc('tn - '.$name);


        //$array = array_add($array, 'key', 'value');

        $this->image = array_merge($this->image, [
            //image
            'width' => $image->width(),
            'height' => $image->height(),
            'orientation' => 'landscape',
            'provider' => 'local',
            'ratio' => $this->gcd1($image->width(),$image->height())
        ]);

        /*
        $this->image = [
            //image
            'width' => $image->width(),
            'height' => $image->height(),
            'orientation' => 'landscape',
            'provider' => 'local',
            'ratio' => $this->gcd($image->width(),$image->height())
        ];
        */

        $image->fit(200)->save($baseDir.'/thumbnails/'.$name);


    }
}
