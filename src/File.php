<?php

namespace WI\Media;

use WI\Media\Media;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image as ImageIntervention;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends Media
{
    protected $fillable = [
        'orientation',
        'application',
        'contentDetailsUit'
    ];

    public $timestamps = false;


    protected $baseDir = 'media/files';

    public function media()
    {
        return $this->morphOne('App\MediaTranslation', 'mediable');
    }

    public function mediatranslation()
    {
        $this->belongsTo('App\MediaTranslation');
        //return $this->morphMany('App\Media', 'mediable');
    }

    //???
    public function mediaUit()
    {
        $this->belongsTo('App\Media');
        //return $this->morphMany('App\Media', 'mediable');
    }


    private function getFileIcon($fileExtension){
        $fileExtensionPath = 'media/icons/application/';
        switch ($fileExtension) {
            case 'pdf':
            case 'doc':
            case 'docx':
            case 'xls':
            case 'xlsx':
                $thumbNail =  $fileExtensionPath.$fileExtension.".png";
                break;
            default:
                $thumbNail =  $fileExtensionPath."default.png";
        }

        return $thumbNail;
    }


    public function moveFile(UploadedFile $uploadedFile){
        $name = $this->getName($uploadedFile);
        $file = $uploadedFile->move($this->baseDir, $name); //FileObject or FileException

        $this->file = [
            //mediatranslation
            'name' => ''.$name.'',
            'path' => ''.$this->baseDir.'/'.$name,

            //'thumbnail' => ''.$this->baseDir.'/thumbnail/'.$name,

            'size' => ''.$file->getSize().'',
            'mime_type' => ''.$file->getMimeType().'',
            'extension' => ''.$file->getExtension().'',
            //'mediableObject' => class_basename(File::class),
            'media_type' => 'file', //mediatranslation->images() mediatranslation->{media_type}s()
            //file
            'orientation' => 'unknown',
            'application' => 'unknown',
            'contentDetails' => '{}',
            'thumbnail' => $this->getFileIcon($file->getExtension())

            //,'media_type' => 'file' //mediatranslation->files() mediatranslation->{media_type}s()
        ];


        //$this->createThumbnail($name);
        return $this->file;
    }
}
