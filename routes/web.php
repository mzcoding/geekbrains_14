<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Account\IndexController as AccountController;
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
Route::group(['middleware' => 'auth'], function() {
Route::get('/account', AccountController::class)
	->name('account');

Route::get('/logout', function() {
	Auth::logout();
	return redirect()->route('login');
})->name('account.logout');

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => 'admin'], function() {
   Route::view('/', 'admin.index')->name('index');
   Route::resource('/categories', AdminCategoryController::class);
   Route::resource('/news', AdminNewsController::class);
});
});

Route::get('/news', [NewsController::class, 'index'])
	->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])
	->where('news', '\d+')
	->name('news.show');

Route::get('sql', function() {
	dd(
		\App\Models\News::find(2)->categories()->where('id', '>', 10)->toSql()
	);
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

Route::get('/collection', function() {
	$arr = [
		1,5,6,7,8,9,9,18
	];
	$arr2 = [
		['names' => [
			'Ann', 'Bill', 'Jhon', 'Mike', 'Pike', 'July'
		]],
		['ages'  =>  [
			10, 25, 16, 32, 44, 56
		]
		]
	];
	$collection = collect($arr);
	$collection2 = collect($arr2);

	dd(
		$collection2->where('ages', '>', 20)
	);
});

Route::get('/session', function() {
	 if(session()->has('test')) {
		 //dd(session()->all(), session()->get('test'));
		 session()->forget('test');
	 }

	 session(['test' => rand(1,1000)]);
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
