{{-- Summernote RTE assets + initializer. Include sekali per halaman yang punya .rte-editor --}}
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        .rte-editor {
            display: none;
        }

        .note-editor.note-frame {
            border: 1px solid var(--border, #dee2e6);
            border-radius: .375rem;
        }

        .note-editor .note-toolbar {
            background: #f8f9fa;
            border-bottom: 1px solid var(--border, #dee2e6);
        }

        .note-editor .note-editable {
            min-height: 150px;
            font-family: inherit;
            font-size: .92rem;
            line-height: 1.7;
        }

        .note-editor .note-editable p {
            margin: 0 0 .5rem;
        }

        .note-btn {
            font-size: .8rem;
        }

        .note-modal .note-modal-title,
        .note-modal .close {
            color: #000;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-lite.min.js"></script>
    <script>
        (function() {
            function initRTE() {
                if (typeof jQuery === 'undefined' || !jQuery.fn.summernote) {
                    return setTimeout(initRTE, 60);
                }
                var $ = jQuery;

                $('.rte-editor').each(function() {
                    var $ta = $(this);
                    var placeholder = $ta.data('placeholder') || 'Tulis di sini...';
                    $ta.summernote({
                        placeholder: placeholder,
                        tabsize: 2,
                        height: 180,
                        fontNames: ['Arial', 'Times New Roman', 'Fira Sans', 'Calibri', 'Courier New',
                            'Georgia', 'Tahoma', 'Verdana', 'Comic Sans MS'
                        ],
                        fontNamesIgnoreCheck: ['Fira Sans', 'Times New Roman'],
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                            ['fontname', ['fontname']],
                            ['fontsize', ['fontsize']],
                            ['color', ['forecolor', 'backcolor']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'hr']],
                            ['view', ['fullscreen', 'codeview', 'help']],
                        ],
                        callbacks: {
                            // Blokir penyisipan gambar ke dalam teks (drag-drop / paste / upload).
                            // Foto verifikasi harus lewat section "Dokumentasi Foto" agar disimpan sebagai file,
                            // bukan base64 di dalam kolom teks. Ini juga sesuai format DLHK (halaman dokumentasi terpisah).
                            onImageUpload: function(files) {
                                alert(
                                    'Penyisipan gambar di editor dinonaktifkan. Silakan unggah foto melalui bagian "Dokumentasi Foto".');
                            },
                            onMediaDelete: function(target) {
                                target.remove();
                            }
                        }
                    });
                });

                // Sinkronisasi: Summernote sudah menulis balik ke <textarea> otomatis,
                // tapi kita pastikan textarea kosong bila konten hanya <p><br></p>
                $('form').on('submit', function() {
                    $('.rte-editor').each(function() {
                        var html = $(this).summernote('code');
                        var text = $('<div>').html(html).text().trim();
                        if (text === '' && $(this).summernote('isEmpty')) {
                            $(this).val('');
                        } else {
                            $(this).val(html);
                        }
                    });
                });
            }
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initRTE);
            else initRTE();
        })();
    </script>
@endpush
