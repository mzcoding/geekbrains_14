<?php

namespace Tests\Feature;

use App\Models\News;
use Tests\TestCase;

class NewsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        $response = $this->get(route('news.index'));

        $response->assertStatus(200);
    }
	public function testShow()
	{
		$news = News::factory()->create();
		$response = $this->get(route('news.show', ['news' => $news]));

		$response->assertStatus(200);
	}
}
