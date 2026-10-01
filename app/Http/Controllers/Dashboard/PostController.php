<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::query()
            ->when(!$request->user()->isAdmin(), fn ($query) => $query->where('user_id', $request->user()->id))
            ->with('category:id,name')
            ->latest()
            ->paginate(15);

        return view('dashboard.posts.index', compact('posts'));
    }

    public function create()
    {
        Gate::authorize('create', Post::class);

        return view('dashboard.posts.create', [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(PostRequest $request)
    {
        Gate::authorize('create', Post::class);

        $post = $request->user()->posts()->create($request->safe()->except(['tags', 'image']));
        $post->tags()->sync($request->input('tags', []));
        $this->replaceImage($post, $request->file('image'));

        return redirect()->route('dashboard.posts.index')->with('success', 'تم حفظ المقال بنجاح');
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        return view('dashboard.posts.edit', [
            'post' => $post->load('tags'),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(PostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $post->update($request->safe()->except(['tags', 'image']));
        $post->tags()->sync($request->input('tags', []));
        $this->replaceImage($post, $request->file('image'));

        return redirect()->route('dashboard.posts.index')->with('success', 'تم تحديث المقال بنجاح');
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);
        $this->deleteImages($post);
        $post->delete();

        return redirect()->route('dashboard.posts.index')->with('success', 'تم حذف المقال بنجاح');
    }

    private function replaceImage(Post $post, ?UploadedFile $uploadedImage): void
    {
        if (!$uploadedImage) {
            return;
        }

        $path = $uploadedImage->store('posts', 'public');

        try {
            $post->images()->create([
                'path' => $path,
                'alt_text' => $post->title,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        $this->deleteImages($post, exceptPath: $path);
    }

    private function deleteImages(Post $post, ?string $exceptPath = null): void
    {
        $images = $post->images()->when($exceptPath, fn ($query) => $query->where('path', '!=', $exceptPath))->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }
}
