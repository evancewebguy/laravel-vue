<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Testing\Fluent\AssertableJson;
use Mockery;

class BookAPITest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_all_books_with_author_when_no_id_is_passed()
    {
        $author = Author::factory()->create();
        $bookA = Book::factory()->create(['author_id' => $author->id, 'title' => 'A']);
        $bookB = Book::factory()->create(['author_id' => $author->id, 'title' => 'B']);

        $response = $this->getJson('/api/v1/books');

        $response->assertOk()
            ->assertJsonCount(2)
            ->assertJson(fn (AssertableJson $json) =>
                $json->first(fn ($item) =>
                    $item->where('id', $bookB->id) // order DESC by id in controller
                         ->where('author.id', $author->id)
                )->etc()
            );
    }

    /** @test */
    public function it_returns_a_single_book_with_author_when_id_is_passed()
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $book->id,
            ])->assertJsonPath('author.id', $author->id);
    }

    /** @test */
    public function it_creates_a_book_with_valid_data()
    {
        $author = Author::factory()->create();

        $payload = [
            'author_id'    => $author->id,
            'title'        => 'New Book',
            'genre'        => 'Fiction',
            'isbn'         => 'ISBN-12345',
            'published_at' => now()->toDateTimeString(),
        ];

        $response = $this->postJson('/api/v1/books', $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('books', [
            'title' => 'New Book',
            'isbn'  => 'ISBN-12345',
        ]);
    }

    /** @test */
    public function it_validates_when_creating_a_book()
    {
        // no author exists
        $payload = [
            'author_id' => 999999,
            'title'     => '', // required
        ];

        $response = $this->postJson('/api/v1/books', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['author_id', 'title']);
    }

    /** @test */
    public function it_updates_a_book_if_found()
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id, 'title' => 'Old Title']);

        $payload = ['title' => 'Updated Title'];

        $response = $this->putJson("/api/v1/books/{$book->id}", $payload);

        $response->assertOk();
        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'Updated Title']);
    }

    /** @test */
    public function it_throws_exception_when_updating_missing_book()
    {
        $response = $this->putJson('/api/v1/books/999999', ['title' => 'X']);

        // Your controller throws Exception('Could not find book.')
        // By default exceptions bubble into a 500. If you map them to 404, adjust this expectation.
        $response->assertStatus(500);
    }

    /** @test */
    public function it_deletes_a_book_if_found()
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /** @test */
    public function it_returns_all_books_when_search_query_is_empty()
    {
        $author = Author::factory()->create();
        Book::factory()->count(3)->create(['author_id' => $author->id]);

        $response = $this->getJson('/api/v1/books/search'); // no q param

        $response->assertOk()
            ->assertJsonCount(3);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * This test mocks Book::search(...) to avoid calling an external Typesense instance.
     * We use Mockery's "alias" to stub the static call. Because PHP class alias mocking
     * can conflict when the class is already loaded, we run this test in a separate process.
     */
    public function it_searches_books_and_returns_models_with_author()
    {
        // Prepare real books in DB
        $author = Author::factory()->create();
        $book1 = Book::factory()->create(['author_id' => $author->id, 'title' => 'Veritatis One']);
        $book2 = Book::factory()->create(['author_id' => $author->id, 'title' => 'Veritatis Two']);

        // Build a fake "search results" collection that the controller expects.
        // The controller does: $searchResults = Book::search($query)->get();
        // Then $bookIds = $searchResults->pluck('id');
        $fakeSearchResults = collect([
            (object) ['id' => $book2->id],
            (object) ['id' => $book1->id],
        ]);

        // Mock the static Book::search(...) call to return an object with get() method returning our collection.
        // Using Mockery aliasing: 'alias:App\Models\Book'
        $mock = Mockery::mock('alias:App\Models\Book');
        $mock->shouldReceive('search')
             ->with('Veritatis')
             ->andReturn((object) [
                 'get' => function () use ($fakeSearchResults) {
                     return $fakeSearchResults;
                 }
             ]);

        // Call route
        $response = $this->getJson('/api/v1/books/search?q=Veritatis');

        $response->assertOk()
                 // We expect the order to follow book2, book1 (as we mocked)
                 ->assertJsonPath('0.id', $book2->id)
                 ->assertJsonPath('1.id', $book1->id);
    }
}
