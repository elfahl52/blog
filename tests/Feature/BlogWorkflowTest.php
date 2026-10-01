<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_post_page_increments_views_once_per_session_window(): void
    {
        $post = Post::factory()->create([
            'title' => 'A published article',
            'published_at' => now()->subHour(),
            'views' => 0,
        ]);

        $this->get(route('posts.show', $post))->assertOk()->assertSee('A published article');
        $this->get(route('posts.show', $post))->assertOk();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'views' => 1,
        ]);
    }

    public function test_new_admin_articles_are_published_by_default_and_appear_on_homepage(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => Role::Admin]);
        $publishDate = now();
        $publishDateInput = $publishDate->format('Y-m-d\TH:i');

        $this->actingAs($admin)
            ->get(route('dashboard.posts.create'))
            ->assertOk()
            ->assertSee($publishDateInput, false);

        $this->post(route('dashboard.posts.store'), [
            'title' => 'New homepage article',
            'body' => 'This article should be visible.',
            'published_at' => $publishDateInput,
            'image' => UploadedFile::fake()->createWithContent(
                'cover.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC')
            ),
        ])->assertRedirect(route('dashboard.posts.index'));

        $post = Post::where('title', 'New homepage article')->firstOrFail();
        $image = $post->images()->firstOrFail();

        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSee('New homepage article')
            ->assertSee(url('/storage/' . $image->path), false)
            ->assertSee('alt="New homepage article"', false);
    }

    public function test_dashboard_identifies_future_articles_as_scheduled(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        Post::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Future article',
            'published_at' => now()->addDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.posts.index'))
            ->assertOk()
            ->assertSee('Scheduled')
            ->assertDontSee('Draft');
    }

    public function test_unpublished_posts_are_not_publicly_accessible(): void
    {
        $post = Post::factory()->create(['published_at' => null]);

        $this->get(route('posts.show', $post))->assertNotFound();
    }

    public function test_homepage_navigation_changes_for_guests_and_signed_in_users(): void
    {
        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSee('Sign in')
            ->assertSee('Register');

        $author = User::factory()->create(['role' => Role::Author]);

        $this->actingAs($author)->get(route('posts.index'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Sign out')
            ->assertDontSee('Sign in');
    }

    public function test_guest_can_submit_a_valid_comment_to_a_published_post(): void
    {
        $post = Post::factory()->create(['published_at' => now()->subHour()]);

        $this->post(route('posts.comments.store', $post), ['body' => 'A useful article.'])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => null,
            'body' => 'A useful article.',
        ]);
    }

    public function test_comment_owner_and_admin_can_delete_comments_but_other_users_cannot(): void
    {
        $post = Post::factory()->create(['published_at' => now()->subHour()]);
        $owner = User::factory()->create(['role' => Role::User]);
        $other = User::factory()->create(['role' => Role::User]);
        $admin = User::factory()->create(['role' => Role::Admin]);

        $comment = $post->comments()->create([
            'user_id' => $owner->id,
            'body' => 'Delete me if you own me.',
        ]);

        $this->actingAs($other)
            ->delete(route('posts.comments.destroy', ['post' => $post, 'comment' => $comment]))
            ->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);

        $this->actingAs($owner)
            ->delete(route('posts.comments.destroy', ['post' => $post, 'comment' => $comment]))
            ->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);

        $comment = $post->comments()->create([
            'user_id' => $owner->id,
            'body' => 'Admin should be able to remove this.',
        ]);

        $this->actingAs($admin)
            ->delete(route('posts.comments.destroy', ['post' => $post, 'comment' => $comment]))
            ->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_category_and_tag_pages_filter_published_articles(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $post = Post::factory()->create([
            'category_id' => $category->id,
            'title' => 'Filtered article',
            'published_at' => now()->subHour(),
        ]);
        $post->tags()->attach($tag);

        $this->get(route('posts.category', $category))->assertOk()->assertSee('Filtered article');
        $this->get(route('posts.tag', $tag))->assertOk()->assertSee('Filtered article');
    }

    public function test_login_accepts_email_and_redirects_regular_users_to_the_blog(): void
    {
        $user = User::factory()->create([
            'email' => 'reader@example.com',
            'password' => 'correct-horse-battery',
            'role' => Role::User,
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'correct-horse-battery',
        ])->assertRedirect(route('posts.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_creates_a_regular_user_and_logs_them_in(): void
    {
        $this->post(route('register'), [
            'name' => 'New Reader',
            'email' => 'new-reader@example.com',
            'password' => 'a-long-test-password',
            'password_confirmation' => 'a-long-test-password',
        ])->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'New Reader',
            'email' => 'new-reader@example.com',
            'role' => Role::User->value,
        ]);
        $this->assertAuthenticated();
    }

    public function test_registration_shows_validation_errors_when_user_is_not_created(): void
    {
        $this->from(route('register'))->post(route('register'), [
            'name' => 'New Reader',
            'email' => 'new-reader@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertRedirect(route('register'))->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'new-reader@example.com']);
        $this->get(route('register'))->assertOk()->assertSee('password field must be at least 8 characters');
    }

    public function test_login_shows_authentication_errors(): void
    {
        $this->from(route('login'))->post(route('login'), [
            'email' => 'missing@example.com',
            'password' => 'incorrect-password',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->get(route('login'))->assertOk()->assertSee('بيانات الدخول غير صحيحة');
    }

    public function test_author_can_open_dashboard_but_regular_user_is_forbidden(): void
    {
        $author = User::factory()->create(['role' => Role::Author]);
        $this->actingAs($author)->get(route('dashboard.posts.index'))->assertOk();

        $reader = User::factory()->create(['role' => Role::User]);
        $this->actingAs($reader)->get(route('dashboard.posts.index'))->assertForbidden();
    }

    public function test_author_can_create_update_and_delete_their_post(): void
    {
        $author = User::factory()->create(['role' => Role::Author]);
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $this->actingAs($author);

        $this->post(route('dashboard.posts.store'), [
            'title' => 'Draft article',
            'category_id' => $category->id,
            'excerpt' => 'Short summary',
            'body' => 'Article content',
            'published_at' => null,
            'tags' => [$tag->id],
        ])->assertRedirect(route('dashboard.posts.index'));

        $post = Post::where('title', 'Draft article')->firstOrFail();
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id, 'tag_id' => $tag->id]);

        $this->put(route('dashboard.posts.update', $post), [
            'title' => 'Updated article',
            'category_id' => $category->id,
            'excerpt' => 'Updated summary',
            'body' => 'Updated content',
            'published_at' => null,
            'tags' => [],
        ])->assertRedirect(route('dashboard.posts.index'));

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated article']);
        $this->assertDatabaseMissing('post_tag', ['post_id' => $post->id, 'tag_id' => $tag->id]);

        $this->delete(route('dashboard.posts.destroy', $post))
            ->assertRedirect(route('dashboard.posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_admin_can_manage_categories_and_tags_but_authors_cannot(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $this->actingAs($admin);

        $this->post(route('dashboard.categories.store'), ['name' => 'Design'])
            ->assertRedirect(route('dashboard.categories.index'));
        $category = Category::where('name', 'Design')->firstOrFail();
        $this->assertSame('design', $category->slug);

        $this->put(route('dashboard.categories.update', $category), ['name' => 'Product Design'])
            ->assertRedirect(route('dashboard.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'slug' => 'product-design']);

        $this->post(route('dashboard.tags.store'), ['name' => 'Laravel'])
            ->assertRedirect(route('dashboard.tags.index'));
        $tag = Tag::where('name', 'Laravel')->firstOrFail();
        $this->assertSame('laravel', $tag->slug);

        $this->delete(route('dashboard.categories.destroy', $category))
            ->assertRedirect(route('dashboard.categories.index'));
        $this->delete(route('dashboard.tags.destroy', $tag))
            ->assertRedirect(route('dashboard.tags.index'));

        $author = User::factory()->create(['role' => Role::Author]);
        $this->actingAs($author)->get(route('dashboard.categories.index'))->assertForbidden();
        $this->actingAs($author)->get(route('dashboard.tags.index'))->assertForbidden();
    }

    public function test_author_can_upload_replace_and_delete_an_article_image(): void
    {
        Storage::fake('public');
        $author = User::factory()->create(['role' => Role::Author]);
        $this->actingAs($author);

        $this->post(route('dashboard.posts.store'), [
            'title' => 'Article with image',
            'body' => 'Article body',
            'image' => UploadedFile::fake()->createWithContent(
                'cover.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC')
            ),
        ])->assertRedirect(route('dashboard.posts.index'));

        $post = Post::where('title', 'Article with image')->firstOrFail();
        $oldImage = $post->images()->firstOrFail();
        Storage::disk('public')->assertExists($oldImage->path);

        $this->put(route('dashboard.posts.update', $post), [
            'title' => 'Article with image',
            'body' => 'Updated article body',
            'image' => UploadedFile::fake()->createWithContent(
                'replacement.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC')
            ),
        ])->assertRedirect(route('dashboard.posts.index'));

        $newImage = $post->images()->firstOrFail();
        $this->assertNotSame($oldImage->path, $newImage->path);
        Storage::disk('public')->assertMissing($oldImage->path);
        Storage::disk('public')->assertExists($newImage->path);

        $this->delete(route('dashboard.posts.destroy', $post))->assertRedirect(route('dashboard.posts.index'));
        $this->assertDatabaseMissing('images', ['id' => $newImage->id]);
        Storage::disk('public')->assertMissing($newImage->path);
    }
}
