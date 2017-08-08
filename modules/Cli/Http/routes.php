<?php

Route::group(['middleware' => 'web', 'prefix' => 'cli', 'namespace' => 'Modules\Cli\Http\Controllers'], function()
{
	Route::get('/', 'CliController@index');
});