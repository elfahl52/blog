<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $items = Tag::withCount('posts')->orderBy('name')->paginate(15);

        return view('dashboard.taxonomy.index', [
            'items' => $items,
            'resource' => 'tags',
            'singular' => 'Tag',
        ]);
    }

    public function create()
    {
        return $this->form('tags', 'Tag');
    }

    public function store(Request $request)
    {
        Tag::create($this->validatedData($request));

        return redirect()->route('dashboard.tags.index')->with('success', 'Tag created.');
    }

    public function edit(Tag $tag)
    {
        return $this->form('tags', 'Tag', $tag);
    }

    public function update(Request $request, Tag $tag)
    {
        $tag->update($this->validatedData($request, $tag));

        return redirect()->route('dashboard.tags.index')->with('success', 'Tag updated.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('dashboard.tags.index')->with('success', 'Tag deleted.');
    }

    private function form(string $resource, string $singular, ?Tag $item = null)
    {
        return view('dashboard.taxonomy.form', compact('resource', 'singular', 'item'));
    }

    private function validatedData(Request $request, ?Tag $tag = null): array
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:120']]);
        $baseSlug = Str::slug($validated['name']) ?: Str::lower(Str::random(8));
        $slug = $baseSlug;
        $suffix = 2;

        while (Tag::where('slug', $slug)->when($tag, fn ($query) => $query->whereKeyNot($tag->id))->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return ['name' => $validated['name'], 'slug' => $slug];
    }
}
