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

// Show All User
Route::get('/', 'UserController@index')->name('user.index');

// Create User
Route::get('/create', 'UserController@create')->name('user.create');

// Edit User
Route::get('/edit/{filename}', 'UserController@edit')->name('user.edit');

// Delete User
Route::delete('/delete/{filename}', 'UserController@delete')->name('user.delete');

// Save & Update User
Route::post('/store', 'UserController@store')->name('user.store');

// Backup Data
Route::post('/backup', 'UserController@backup')->name('user.backup');

// Restore Data
Route::post('/restore', 'UserController@restore')->name('user.restore');

// Reset Data
Route::post('/reset', 'UserController@reset')->name('user.reset');
