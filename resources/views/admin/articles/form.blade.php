@extends('admin.layouts.master')

@section('title')
    <title>Admin | {{ $article ? 'Chỉnh sửa bài viết' : 'Thêm bài viết' }}</title>
@endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="/admin"><i class="fa fa-home"></i>Admin</a></li>
            <li><a href="{{ route('admin.articles.index') }}">Bài viết</a></li>
            <li class="active">{{ $article ? 'Chỉnh sửa' : 'Thêm mới' }}</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    <section class="content">
        <div class="box box-solid">
            <div class="box-body">
                <form action="{{ $article ? route('admin.articles.update', $article->id) : route('admin.articles.store') }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($article)
                        @method('PUT')
                    @endif

                    <div class="row">
                        {{-- Tiêu đề --}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" id="article-title" required
                                       value="{{ old('title', $article->title ?? '') }}">
                            </div>
                        </div>

                        {{-- Slug --}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control" id="article-slug" required
                                       value="{{ old('slug', $article->slug ?? '') }}" placeholder="Tự động tạo nếu để trống">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Chuyên mục chính --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Chuyên mục <span class="text-danger">*</span></label>
                                <select name="genre_id" class="form-control select2" required>
                                    <option value="">-- Không chọn --</option>
                                    @foreach ($genres as $genre)
                                        <option value="{{ $genre->id }}"
                                            {{ (string)old('genre_id', $article->genre_id ?? '') === (string)$genre->id ? 'selected' : '' }}>
                                            {{ $genre->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Mặc định sẽ được sync vào bảng article_genres.</small>
                            </div>
                        </div>

                        {{-- Ngày đăng --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Ngày đăng <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="published_at" class="form-control" required
                                       value="{{ old('published_at', optional($article)->published_at ? \Carbon\Carbon::parse($article->published_at)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>

                        {{-- Ghim nổi bật --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Ghim nổi bật</label>
                                <select name="highlight" class="form-control select2">
                                    <option value="0" {{ (int)old('highlight', $article->highlight ?? 0) === 0 ? 'selected' : '' }}>Không</option>
                                    <option value="1" {{ (int)old('highlight', $article->highlight ?? 0) === 1 ? 'selected' : '' }}>Có</option>
                                </select>
                            </div>
                        </div>

                        {{-- Ẩn bài viết --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Ẩn bài viết</label>
                                <select name="hidden" class="form-control select2">
                                    <option value="0" {{ (int)old('hidden', $article->hidden ?? 0) === 0 ? 'selected' : '' }}>Hiện</option>
                                    <option value="1" {{ (int)old('hidden', $article->hidden ?? 0) === 1 ? 'selected' : '' }}>Ẩn</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Tóm tắt --}}
                    <div class="form-group">
                        <label>Tóm tắt nội dung </label>
                        <textarea name="excerpt" id="excerpt-input" style="display:none">{!! old('excerpt', $article->excerpt ?? '') !!}</textarea>
                        <div id="excerpt-editor">{!! old('excerpt', $article->excerpt ?? '') !!}</div>
                    </div>

                    {{-- Nội dung bài viết --}}
                    <div class="form-group">
                        <label>Nội dung bài viết</label>
                        <textarea name="content" id="content-input" style="display:none">{!! old('content', $article->content ?? '') !!}</textarea>
                        <div id="content-editor">{!! old('content', $article->content ?? '') !!}</div>
                        <small class="text-muted">
                            Có thể dán trực tiếp <code>&lt;iframe&gt;</code> YouTube/Vimeo/… hoặc <code>&lt;video&gt;</code>, URL <code>.mp4</code>.
                        </small>
                    </div>

                    {{-- SEO --}}
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" class="form-control"
                               value="{{ old('meta_title', $article->meta_title ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description', $article->meta_description ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control"
                               value="{{ old('meta_keywords', $article->meta_keywords ?? '') }}">
                    </div>

                    <div class="row">
                        {{-- URL gốc --}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>URL gốc</label>
                                <input type="text" name="url" class="form-control"
                                       value="{{ old('url', $article->url ?? '') }}">
                            </div>
                        </div>

                        {{-- Nguồn/copyright --}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Copyright</label>
                                <input type="text" name="copyright" class="form-control"
                                       value="{{ old('copyright', $article->copyright ?? '') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Copy_at --}}
                    <div class="form-group">
                        <label>Copy at</label>
                        <input type="text" name="copy_at" class="form-control"
                               value="{{ old('copy_at', $article->copy_at ?? '') }}">
                    </div>

                    <div class="row">
                        {{-- Ảnh đại diện --}}
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Ảnh đại diện</label>
                                <input type="file" name="avatar_file" id="avatar_file" class="form-control"
                                       accept="image/*" style="margin-bottom:8px">
                                <img id="avatar-preview"
                                     src="{{ $article && $article->avatar ? asset_media($article->avatar) : '' }}"
                                     style="margin-top:10px;max-height:160px;">
                                <p>
                                    <small class="text-muted">
                                        Ảnh sẽ lưu vào <code>images/thumbs/</code> dạng <code>{slug}-avatar-*.webp</code>.
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-danger">Huỷ</a>
                        <button type="submit" class="btn btn-success">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('script')
    {{-- Quill.js CDN (miễn phí, BSD license) --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <script>
        // --- Image upload handler cho Quill ---
        function quillImageHandler(quillInstance) {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();
            input.onchange = async () => {
                const file = input.files[0];
                if (!file) return;
                const formData = new FormData();
                formData.append('file', file);
                try {
                    const res = await fetch('{{ route("admin.tinymce.upload") }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const data = await res.json();
                    const url = data.url || data.location;
                    if (!url) throw new Error('Invalid response');
                    const range = quillInstance.getSelection(true);
                    quillInstance.insertEmbed(range.index, 'image', url);
                    quillInstance.setSelection(range.index + 1);
                } catch (err) {
                    if (typeof toastr !== 'undefined') toastr.error('Upload thất bại: ' + err.message);
                }
            };
        }

        // --- Content Editor (đầy đủ tính năng) ---
        const contentQuill = new Quill('#content-editor', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [2, 3, 4, false] }],
                        [{ 'font': [] }, { 'size': ['small', false, 'large', 'huge'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'align': [] }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
                        ['link', 'image', 'video', 'blockquote', 'code-block'],
                        ['clean']
                    ],
                    handlers: {
                        image: function() { quillImageHandler(contentQuill); }
                    }
                }
            },
            placeholder: 'Nhập nội dung bài viết...'
        });

        // --- Excerpt Editor (tính năng cơ bản) ---
        const excerptQuill = new Quill('#excerpt-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            },
            placeholder: 'Tóm tắt nội dung...'
        });

        // Sync real-time: mỗi khi nội dung thay đổi, cập nhật textarea ẩn
        contentQuill.on('text-change', function() {
            document.getElementById('content-input').value = contentQuill.root.innerHTML;
        });
        excerptQuill.on('text-change', function() {
            document.getElementById('excerpt-input').value = excerptQuill.root.innerHTML;
        });

        // Sync lần đầu khi Quill init
        document.getElementById('content-input').value = contentQuill.root.innerHTML;
        document.getElementById('excerpt-input').value = excerptQuill.root.innerHTML;
    </script>

    <script>
        @if (!$article)
        function changeToSlug(str) {
            let slug = (str || '').toLowerCase()
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                .replace(/đ/g, "d")
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/\-+/g, '-')
                .replace(/^\-+|\-+$/g, '');
            return slug;
        }
        document.addEventListener('DOMContentLoaded', function() {
            const titleInput = document.getElementById('article-title');
            const slugInput = document.getElementById('article-slug');
            if (titleInput && slugInput) {
                titleInput.addEventListener('input', function() {
                    if (!slugInput.value) slugInput.value = changeToSlug(titleInput.value);
                });
            }
        });
        @endif

        // Preview ảnh avatar
        document.getElementById('avatar_file')?.addEventListener('change', e => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = ev => document.getElementById('avatar-preview').src = ev.target.result;
            reader.readAsDataURL(file);
        });

        // ======= Custom validate + Toastr error (kèm required avatar) =======
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action]');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                const errors = [];

                const title = form.querySelector('[name="title"]').value.trim();
                const slug = form.querySelector('[name="slug"]').value.trim();
                const genre = form.querySelector('[name="genre_id"]').value;
                const published = form.querySelector('[name="published_at"]').value.trim();

                // Lấy nội dung từ Quill
                const excerptText = excerptQuill.getText().trim();
                const contentText = contentQuill.getText().trim();
                const contentHtml = contentQuill.root.innerHTML;
                const hasMedia = /<(img|video|iframe)\b/i.test(contentHtml);

                // Avatar required: nếu không có avatar sẵn và cũng không chọn mới
                const avatarInput = document.getElementById('avatar_file');
                const hasExistingAvatar = avatarInput?.dataset.existingAvatar === '1';
                const hasNewAvatar = avatarInput?.files && avatarInput.files.length > 0;

                if (!title) errors.push('Thiếu Tiêu đề');
                if (!slug) errors.push('Thiếu Slug');
                if (!genre) errors.push('Thiếu Chuyên mục');
                if (!published) errors.push('Thiếu Ngày đăng');
                if (!excerptText) errors.push('Thiếu Tóm tắt nội dung');
                if (!contentText && !hasMedia) errors.push('Thiếu Nội dung bài viết');
                if (!hasExistingAvatar && !hasNewAvatar) errors.push('Thiếu Ảnh đại diện');

                if (errors.length) {
                    e.preventDefault();
                    errors.forEach(msg => toastr.error(msg));
                    return false;
                }

                const btn = document.getElementById('btn-submit');
                if (btn) {
                    btn.disabled = true;
                    btn.innerText = 'Đang lưu...';
                }
            });
        });
    </script>

    <style>
        #content-editor {
            min-height: 500px;
        }
        #excerpt-editor {
            min-height: 150px;
        }
        #content-editor .ql-editor,
        #excerpt-editor .ql-editor {
            font-size: 16px;
            line-height: 1.75;
        }
    </style>
@endsection