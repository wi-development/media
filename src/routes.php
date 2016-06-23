<?php

/*
       |--------------------------------------------------------------------------
       | Media Routes roles = Administrator
       |--------------------------------------------------------------------------
       |
       */


//TO DO
//upload media
Route::post('/media/upload',                    ['as' => 'media.upload','uses' => 'MediaController@upload']);

//edit form
Route::get('/media/{id}/edit',                  ['as' => 'media.edit','uses' => 'MediaController@edit']);
//Route::get('/media/{media}/edit',             ['as' => 'admin.media.edit' ,'uses' => 'MediaController@edit']);
Route::get('/media/{media_type}/{id}/edit',     ['as' => 'media.type.edit', 'uses' => 'MediaController@edit']);


//add form
Route::get('/media/create',                     ['as' => 'admin.media.create' ,'uses' => 'MediaController@selectMediaTypeBeforeCreate']);
Route::get('/media/{media_type}/create',        ['as' => 'admin.media.media_type.create' ,'uses' => 'MediaController@create']);



//modal views (for ng modal ui)
Route::get('/media/modal_select_media',         ['as' => 'api.modal.select.media',          'uses' => 'MediaController@ngTemplateSelectMediaModal']);//returns ng template view

//index for modal create media
Route::get('/media/modal_create_media',         ['as' => 'api.modal.create.media',          'uses' => 'MediaController@ngTemplateSelectMediaCreate']);




//index for modal select mediaLibrary
//returns json object for ng modal view
Route::get('api/media/modal',                   ['as' => 'api.media.modal',          'uses' => 'MediaController@apiIndex']);
Route::get('api/media/modal/{media_type}',      ['as' => 'api.media.modal.type',     'uses' => 'MediaController@apiIndex']);

Route::get('api/media/modaltest/{media_type}',  'MediaController@apiIndexOrder');





//index
Route::get('/media/all/data',                   ['as' => 'media.index.all.data',        'uses' => 'MediaController@indexAllData']);
#Route::get('/media/all',                       ['as' => 'media.index.all',             'uses' => 'MediaController@getIndexAll']);
//also used by ng directive form_media)field.html
Route::get('/media',                            ['as' => 'media.index',                 'uses' => 'MediaController@getIndexAll']);


#even uit.. package
#Route::get('/media', ['as' => 'media.index' ,'uses' => 'MediaController@index']);
#Route::get('/media/{media_type}', 'MediaController@index');







//database
Route::post('/media',                           ['as' => 'media.store',                 'uses' => 'MediaController@store']);
Route::patch('/media/{media}',                  ['as' => 'media.update',                'uses' => 'MediaController@update']);
//Route::put('/media/{media}',                  ['as' => '',                            'uses' => 'MediaController@update']);
Route::delete('/media',                         ['as' => 'media.destroy.bulk',          'uses' => 'MediaController@destroyBulk']);
Route::delete('/media/{media?}',                ['as' => 'media.destroy',               'uses' => 'MediaController@destroy']);



//show uit cms
//Route::get('/media/{media}', ['as' => 'admin.media.show' ,'uses' => 'MediaController@show']);
