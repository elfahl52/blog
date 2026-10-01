<div class="mb-3">
    <label class="form-label" for="title">Title</label>
    <input class="form-control" id="title" name="title" value="{{ old('title', $post->title ?? '') }}" maxlength="255" required>
</div>
<div class="mb-3">
    <label class="form-label" for="excerpt">Excerpt</label>
    <textarea class="form-control" id="excerpt" name="excerpt" rows="2" maxlength="500">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label" for="body">Article body</label>
    <textarea class="form-control" id="body" name="body" rows="14" required>{{ old('body', $post->body ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label" for="image">Article image</label>
    <input class="form-control" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">
    <div class="form-text">JPEG, PNG, or WebP. Maximum size: 4 MB.</div>
    @if (isset($post) && $post->images->isNotEmpty())
        <img class="img-thumbnail mt-3" src="{{ url('/storage/' . $post->images->first()->path) }}" alt="Current article image" style="max-width: 240px">
    @endif
</div>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label" for="category_id">Category</label>
        <select class="form-select" id="category_id" name="category_id">
            <option value="">No category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $post->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="published_at">Publish date</label>
        <input class="form-control" id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', isset($post) ? ($post->published_at?->format('Y-m-d\TH:i') ?? '') : now()->format('Y-m-d\TH:i')) }}">
        <div class="form-text">Leave blank to save as a draft.</div>
    </div>
</div>
<fieldset class="mb-4">
    <legend class="form-label fs-6">Tags</legend>
    <div class="d-flex flex-wrap gap-3">
        @php($selectedTags = old('tags', isset($post) ? $post->tags->modelKeys() : []))
        @foreach ($tags as $tag)
            <label class="form-check">
                <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, $selectedTags))>
                <span class="form-check-label">{{ $tag->name }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
