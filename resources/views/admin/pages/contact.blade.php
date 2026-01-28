@extends('admin.layouts.master')
@section('title')
    <title>Admin | Contact Page</title>
@endsection
@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="/admin"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class=""><a href="{{ route('admin.pages.contact.edit') }}">Contact Page</a></li>
        </ol>
        <ul class="right-button">
            <li><a class="btn btn-block btn-success text-bold" id="submit"><i class="fa fa-save mr-1"></i> SAVE</a></li>
        </ul>
        <div class="clearfix"></div>
    </section>

    <section class="content">
        <form action="{{ route('admin.pages.contact.upsert') }}" method="POST" id="formData">
            {{ csrf_field() }}
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-info" style="padding-right: 7px;"></i>Contact - General
                        Information</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group clearfix">
                                <label class="control-label">Title: <strong class="red">*</strong></label>
                                <input type="text" required name="title"
                                    value="{{ old('title', $page->title ?? 'Liên hệ') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="control-label">Meta Title:</label>
                        <input type="text" name="meta_title"
                            value="{{ old('meta_title', $page->meta_title ?? 'Liên hệ - Xổ Số') }}">
                    </div>

                    <div class="form-group clearfix">
                        <label class="control-label">Meta Description:</label>
                        <textarea name="meta_description" rows="3">{{ old('meta_description', $page->meta_description ?? 'Liên hệ gửi tin nóng, tin độc quyền cho Xổ Số.') }}</textarea>
                    </div>

                    <div class="form-group clearfix">
                        <label class="control-label">Content: <strong class="red">*</strong></label>
                        <textarea name="content" id="content-input" style="display:none">{!! old('content', $page->content ?? '<h2>Liên hệ Xổ Số</h2><p>Email: support@chuyenhot.net</p>') !!}</textarea>
                        <div id="content-editor">{!! old('content', $page->content ?? '<h2>Liên hệ Xổ Số</h2><p>Email: support@chuyenhot.net</p>') !!}</div>
                    </div>

                    <input type="hidden" name="hidden" value="{{ old('hidden', $page->hidden ?? 0) }}">
                </div>
            </div>
            <button type="submit" class="hide"></button>
        </form>
    </section>

    <script>
        $(function() {
            $('#submit').on('click', function() {
                $('form').find('[type="submit"]').trigger('click');
            });
        });
    </script>
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
                    alert('Upload thất bại: ' + err.message);
                }
            };
        }

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
            placeholder: 'Nhập nội dung...'
        });

        // Sync real-time: mỗi khi nội dung thay đổi, cập nhật textarea ẩn
        contentQuill.on('text-change', function() {
            document.getElementById('content-input').value = contentQuill.root.innerHTML;
        });
        // Sync lần đầu khi Quill init
        document.getElementById('content-input').value = contentQuill.root.innerHTML;
    </script>

    <style>
        #content-editor {
            min-height: 540px;
        }
        #content-editor .ql-editor {
            font-size: 16px;
            line-height: 1.75;
        }
    </style>
@endsection
