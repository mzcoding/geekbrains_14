<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\CreateRequest;
use App\Http\Requests\News\UpdateRequest;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$news = News::/*whereHas('categories')->*/with('categories')->paginate(5);

        return view('admin.news.index', [
			'newsList'   => $news
		]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$categories = Category::all();
        return view('admin.news.create', [
			'categories' => $categories
		]);
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param CreateRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
    public function store(CreateRequest $request)
    {
		$data = $request->validated() + [
			'slug' => \Str::slug($request->input('title'))
		];

		$created = News::create($data);
		if($created) {
			$created->categories()->attach($request->input('categories'));

			return redirect()->route('admin.news.index')
				->with('success', __('messages.admin.news.created.success'));
		}

		return back()->with('error', __('messages.admin.news.created.error'))
			->withInput();
    }

	/**
	 * Display the specified resource.
	 *
	 * @param News $news
	 * @return \Illuminate\Http\Response
	 */
    public function show(News $news)
    {
        //
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param News $news
	 * @return \Illuminate\Http\Response
	 */
    public function edit(News $news)
    {
		$categories = Category::all();
		$selectCategories = \DB::table('categories_has_news')
			->where('news_id', $news->id)
			->get()
			->map(fn($item) => $item->category_id)->toArray();


        return view('admin.news.edit', [
			'news' => $news,
			'categories' => $categories,
			'selectCategories' => $selectCategories
		]);
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateRequest $request
	 * @param News $news
	 * @return \Illuminate\Http\RedirectResponse
	 */
    public function update(UpdateRequest $request, News $news)
    {
		$data = $request->validated() + [
				'slug' => \Str::slug($request->input('title'))
		];

		$updated = $news->fill($data)->save();
		if($updated) {

			\DB::table('categories_has_news')
				->where('news_id', $news->id)
				->delete();

			foreach($request->input('categories') as $category) {
				\DB::table('categories_has_news')
					->insert([
						'category_id' => intval($category),
						'news_id' => $news->id
					]);
			}

			return redirect()->route('admin.news.index')
				->with('success', __('messages.admin.news.updated.success'));
		}

		return back()->with('error', __('messages.admin.news.updated.error'))
			->withInput();
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param News $news
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function destroy(News $news)
    {
        try{
			$news->delete();
			return response()->json('ok');
		}catch (\Exception $e) {
			\Log::error('News error destroy', [$e]);
			return response()->json('error', 400);
		}
    }
}
