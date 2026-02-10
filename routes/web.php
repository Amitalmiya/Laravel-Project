<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/upload', 'FileUploadController@index')->name('upload.form');
Route::post('/upload', 'FileUploadController@upload')->name('upload.file');
Route::get('/files', 'FileUploadController@list')->name('files.list');
Route::get('/download/{filename}', 'FileUploadController@download')->name('file.download');
Route::delete('/delete/{filename}', 'FileUploadController@delete')->name('file.delete');
Route::delete('/bulk-delete', 'FileUploadController@bulkDelete')->name('file.bulkDelete');