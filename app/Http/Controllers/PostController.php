<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // =========================
    // عرض كل الـ Posts
    // =========================
    public function index(Request $request)
    {
        $posts = Post::published();

        // عدد التعليقات
        $posts = $posts->withCount('comments');

        // العلاقات
        $posts = $posts->with([
            'author:id,name',
            'category:id,name,slug',
            'tags:id,name',
            'images',
        ]);

        // Search
        if ($request->filled('q')) {
            $term = $request->string('q')->toString();

            $posts = $posts->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")
                      ->orWhere('body', 'like', "%{$term}%");
            });
        }

        // ترتيب + Pagination
        $posts = $posts
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        // Categories
        $categories = Category::withCount([
            'posts' => function ($query) {
                $query->published();
            }
        ])
        ->orderByDesc('posts_count')
        ->get();

        // Tags
        $tags = Tag::withCount([
            'posts' => function ($query) {
                $query->published();
            }
        ])
        ->orderByDesc('posts_count')
        ->limit(12)
        ->get();

        return view(
            'posts.index',
            compact('posts', 'categories', 'tags')
        );
    }


    // =========================
    // عرض Post واحد
    // =========================
    public function show(Post $post)
    {
        // لازم يكون منشور
        abort_unless(
            $post->published_at?->lte(now()),
            404
        );

        // View Counter
        $viewKey = 'post_viewed_' . $post->id;

        $lastCountedAt = session()->get($viewKey);

        if (
            !is_int($lastCountedAt) ||
            $lastCountedAt <= now()->subMinutes(30)->timestamp
        ) {
            $post->increment('views');

            session()->put(
                $viewKey,
                now()->timestamp
            );
        }

        // العلاقات
        $post->load([
            'author',
            'category',
            'tags',
            'comments.user',
            'images',
        ]);

        // Related Posts
        $related = Post::published()
            ->where('category_id', $post->category_id)
            ->whereKeyNot($post->id)
            ->withCount('comments')
            ->limit(3)
            ->get();

        return view(
            'posts.show',
            compact('post', 'related')
        );
    }


    // =========================
    // Posts حسب Category
    // =========================
    public function byCategory(Category $category)
    {
        $posts = $category->posts()
            ->published()
            ->withCount('comments')
            ->with([
                'author:id,name',
                'category:id,name,slug',
                'tags:id,name',
                'images',
            ])
            ->latest('published_at')
            ->paginate(9);

        $categories = Category::withCount([
            'posts' => function ($query) {
                $query->published();
            }
        ])
        ->get();

        $tags = Tag::withCount([
            'posts' => function ($query) {
                $query->published();
            }
        ])
        ->limit(12)
        ->get();

        return view(
            'posts.index',
            compact(
                'posts',
                'category',
                'categories',
                'tags'
            )
        );
    }


    // =========================
    // Posts حسب Tag
    // =========================
    public function byTag(Tag $tag)
    {
        $posts = $tag->posts()
            ->published()
            ->withCount('comments')
            ->with([
                'author:id,name',
                'category:id,name,slug',
                'tags:id,name',
                'images',
            ])
            ->latest('published_at')
            ->paginate(9);

        $categories = Category::withCount([
            'posts' => function ($query) {
                $query->published();
            }
        ])
        ->get();

        $tags = Tag::withCount([
            'posts' => function ($query) {
                $query->published();
            }
        ])
        ->limit(12)
        ->get();

        return view(
            'posts.index',
            compact(
                'posts',
                'tag',
                'categories',
                'tags'
            )
        );
    }


    // =========================
    // إضافة Comment
    // =========================
    public function storeComment(
        Request $request,
        Post $post
    ) {
        // لازم يكون Post منشور
        abort_unless(
            $post->published_at?->lte(now()),
            404
        );

        // Validation
        $validated = $request->validate([
            'body' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        // إنشاء Comment
        $post->comments()->create([
            'user_id' => $request->user()?->id,
            'body' => $validated['body'],
        ]);

        return back()->with(
            'success',
            'تم إضافة تعليقك.'
        );
    }


    // =========================
    // حذف Comment
    // =========================
    public function destroyComment(
        Request $request,
        Post $post,
        Comment $comment
    ) {
        // التأكد أن الـ Comment تابع للـ Post
        abort_unless(
            $post->is($comment->post),
            404
        );

        $user = $request->user();

        // Admin أو صاحب التعليق فقط
        if (
            !$user ||
            (
                !$user->isAdmin() &&
                (int) $comment->user_id !== (int) $user->id
            )
        ) {
            abort(
                403,
                'غير مسموح لك بحذف هذا التعليق.'
            );
        }

        // حذف Comment
        $comment->delete();

        return back()->with(
            'success',
            'تم حذف التعليق بنجاح.'
        );
    }
}
