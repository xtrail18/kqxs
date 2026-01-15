@extends('admin.layouts.master')

@section('title')
    <title>Admin | Term Page</title>
@endsection

@section('content')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="/admin"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class=""><a href="{{ route('admin.pages.term.edit') }}">Term Page</a></li>
        </ol>
        <ul class="right-button">
            <li>
                <a class="btn btn-block btn-success text-bold" id="submit">
                    <i class="fa fa-save mr-1" aria-hidden="true"></i> SAVE
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </section>

    <section class="content">
        <form action="{{ route('admin.pages.term.upsert') }}" method="POST" id="formData">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <i class="fa fa-info" style="padding-right: 7px;"></i>Term - General Information
                            </h3>
                        </div>
                        <div class="box-body">
                            <div class="row">

                                <div class="col-sm-6">
                                    <div class="form-group clearfix">
                                        <label class="control-label">Title: <strong class="red">*</strong></label>
                                        <input type="text" required name="title"
                                            value="{{ old('title', $page->title ?? 'Điều khoản sử dụng') }}">
                                    </div>
                                </div>

                            </div>

                            <div class="form-group clearfix">
                                <label class="control-label">Meta Title:</label>
                                <input type="text" name="meta_title"
                                    value="{{ old('meta_title', $page->meta_title ?? 'Điều khoản sử dụng - Xổ Số') }}">
                            </div>

                            <div class="form-group clearfix">
                                <label class="control-label">Meta Description:</label>
                                <textarea name="meta_description" rows="3">{{ old('meta_description', $page->meta_description ?? 'Các điều khoản sử dụng trang tin tức nóng, scandal, drama tại Xổ Số.') }}</textarea>
                            </div>

                            <div class="form-group clearfix">
                                <label class="control-label">Content: <strong class="red">*</strong></label>
                                <textarea required name="content" id="content-editor" rows="12">{!! old(
                                    'content',
                                    $page->content ?? '<h2>Điều khoản sử dụng</h2><p>Vui lòng đọc kỹ trước khi sử dụng dịch vụ của chúng tôi.</p>',
                                ) !!}</textarea>
                            </div>

                            <input type="hidden" name="hidden" value="{{ old('hidden', $page->hidden ?? 0) }}">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="hide"></button>
        </form>
    </section>

    <script>
        $(document).ready(function() {
            $('#submit').click(function() {
                $('form').find('[type="submit"]').trigger('click');
            });
        });
    </script>
@endsection

@section('script')
    {{-- TinyMCE --}}
    <script src="https://cdn.tiny.cloud/1/f3mhhzhbg82qi276p3tkpleoswpsxfgssraxfrdcscwv4qe8/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

    <script>
        /**
         * Open Laravel File Manager (LFM) trong popup, trả URL qua window.SetUrl
         */
        function lfm(options, cb) {
            const route_prefix = (options && options.prefix) ? options.prefix : '/filemanager';
            const type = (options && options.type) ? options.type : 'file';

            // Callback LFM sẽ gọi khi Confirm
            window.SetUrl = function(items) {
                const file_path = items.map(item => item.url).join(',');
                cb(file_path, items);
                window.SetUrl = undefined; // cleanup
            };

            window.open(route_prefix + '?type=' + type, 'FileManager',
                'width=900,height=600,top=100,left=100,scrollbars=1');
        }

        // --- TinyMCE cho nội dung chính ---
        tinymce.init({
            selector: '#content-editor',
            height: 560,
            language: 'vi',
            convert_urls: false,
            browser_spellcheck: true,
            contextmenu: false,

            // Plugins đầy đủ, có autosave, quickbars, wordcount, codesample, table nâng cao...
            plugins: 'code preview searchreplace autolink autosave directionality visualblocks visualchars fullscreen ' +
                'image link media table advtable lists charmap pagebreak nonbreaking anchor insertdatetime advlist ' +
                'wordcount emoticons quickbars codesample',

            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough forecolor backcolor ' +
                '| alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media ' +
                'table | codesample charmap emoticons | removeformat | code preview fullscreen',

            quickbars_selection_toolbar: 'bold italic underline | quicklink blockquote | bullist numlist | h2 h3',
            quickbars_insert_toolbar: 'image media table hr',

            // Cấu hình link
            default_link_target: '_blank',
            link_default_protocol: 'https',
            link_title: true,
            rel_list: [{
                    title: 'nofollow',
                    value: 'nofollow'
                },
                {
                    title: 'noopener',
                    value: 'noopener'
                },
                {
                    title: 'noreferrer',
                    value: 'noreferrer'
                }
            ],
            target_list: [{
                    title: 'Mở tab mới',
                    value: '_blank'
                },
                {
                    title: 'Trong tab hiện tại',
                    value: ''
                }
            ],

            // Hỗ trợ nhúng iframe/video (YouTube, TikTok, Facebook...).
            // Lưu ý: bạn nên tự chịu trách nhiệm CSP và sandbox nếu cần.
            extended_valid_elements: 'iframe[src|width|height|frameborder|scrolling|allowfullscreen|webkitallowfullscreen|' +
                'mozallowfullscreen|allow|referrerpolicy|loading|style|class],video[src|controls|preload|' +
                'poster|width|height],source[src|type],script[src|type|async|defer]',
            valid_children: '+figure[iframe|video|source]',

            // Làm sạch nội dung dán từ Word/Website
            paste_data_images: false,
            paste_webkit_styles: 'none',
            paste_remove_styles_if_webkit: true,
            paste_merge_formats: true,
            paste_as_text: false, // giữ định dạng cơ bản
            cleanup_on_startup: true,
            verify_html: true,

            // Giao diện nội dung trong editor (giúp preview gần giống ngoài frontend)
            content_style: `
                :root{ --body-font: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji","Segoe UI Emoji"; }
                body{ font-family: var(--body-font); line-height:1.75; font-size:16px; color:#222; }
                h1,h2,h3,h4{ line-height:1.35; margin: 1.25em 0 .6em; }
                img{ max-width:100%; height:auto; }
                figure{ margin: 1em auto; text-align:center; }
                iframe, video{ max-width:100%; aspect-ratio:16/9; height:auto; }
                table{ width:100%; border-collapse: collapse; }
                table td, table th{ border:1px solid #e5e7eb; padding:8px; }
                pre, code{ background:#f5f5f5; border-radius:6px; padding:.2em .4em; }
            `,

            // Upload ảnh trực tiếp từ TinyMCE -> Laravel route
            automatic_uploads: true,
            images_upload_credentials: true,
            images_reuse_filename: false,

            images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                fetch(`{{ route('admin.tinymce.upload') }}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    credentials: 'same-origin'
                }).then(async (res) => {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const data = await res.json();
                    if (!data.location) throw new Error('Invalid response');
                    resolve(data.location); // TinyMCE yêu cầu trả về URL file
                }).catch(err => reject('Upload thất bại: ' + err.message));
            }),

            // File/Media picker dùng LFM
            file_picker_types: 'image file media',
            file_picker_callback: (callback, value, meta) => {
                let type = 'file';
                if (meta.filetype === 'image') type = 'image';
                lfm({
                    type,
                    prefix: '/filemanager'
                }, (url, items) => {
                    const first = items?.[0] ?? null;
                    callback(url, {
                        text: first?.name || '',
                        title: first?.name || ''
                    });
                });
            },

            // Autosave – tránh mất nội dung khi reload tab
            autosave_interval: '30s',
            autosave_prefix: 'tinymce-autosave-{path}{query}-{id}-',
            autosave_restore_when_empty: true,
            autosave_retention: '30m',

            // Tối ưu trải nghiệm
            image_caption: true,
            image_advtab: true,
            toolbar_sticky: true,
            statusbar: true,
            branding: false,
        });

        // Gửi form khi bấm nút SAVE
        $(function() {
            $('#submit').on('click', function() {
                $('form').find('[type="submit"]').trigger('click');
            });
        });
    </script>
@endsection
