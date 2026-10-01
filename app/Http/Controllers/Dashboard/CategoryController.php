<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $items = Category::withCount('posts')->orderBy('name')->paginate(15);

        return view('dashboard.taxonomy.index', [
            'items' => $items,
            'resource' => 'categories',
            'singular' => 'Category',
        ]);
    }

    public function create()
    {
        return $this->form('categories', 'Category');
    }

    public function store(Request $request)
    {
        Category::create($this->validatedData($request));

        return redirect()->route('dashboard.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return $this->form('categories', 'Category', $category);
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validatedData($request, $category));

        return redirect()->route('dashboard.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('dashboard.categories.index')->with('success', 'Category deleted.');
    }

    private function form(string $resource, string $singular, ?Category $item = null)
    {
        return view('dashboard.taxonomy.form', compact('resource', 'singular', 'item'));
    }

    private function validatedData(Request $request, ?Category $category = null): array
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:120']]);
        $baseSlug = Str::slug($validated['name']) ?: Str::lower(Str::random(8));
        $slug = $baseSlug;
        $suffix = 2;

        while (Category::where('slug', $slug)->when($category, fn ($query) => $query->whereKeyNot($category->id))->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return ['name' => $validated['name'], 'slug' => $slug];
    }
}
