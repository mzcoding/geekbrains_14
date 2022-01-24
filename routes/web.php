<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;

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

//news
Route::group(['as' => 'admin.', 'prefix' => 'admin'], function() {
   Route::view('/', 'admin.index')->name('index');
   Route::resource('/categories', AdminCategoryController::class);
   Route::resource('/news', AdminNewsController::class);
});

Route::get('/newslist', [NewsController::class, 'index'])
	->name('news.index');
Route::get('/news/{id}', [NewsController::class, 'show'])
	->where('id', '\d+')
	->name('news.show');

Route::get('sql', function() {
	dump(
	/*\DB::table('news')
		->join('categories_has_news as chn', 'news.id', '=', 'chn.news_id')
		->join('categories', 'chn.category_id', '=', 'categories.id')
		->select('news.*', 'categories.title as categoryTitle')
		->get()*/

		\DB::table('news')
			//->where('id', '>', 5)
			//->where('isImage', '=',  false)
			/*->where([
				['title', 'like', '%'. request()->get('q'). '%'],
				['id', '<', 10]
			])
			->orWhere('author', '=', 'Admin')*/
			->whereNotBetween('id', [7,9])
			->orderBy('id', 'desc')
			->get()
	);
});