<?php

namespace App\Support;

/**
 * Sanitizer HTML tanpa dependency eksternal untuk output editor Summernote.
 * Mengizinkan tag & atribut yang aman (termasuk table, gambar base64, font, warna, align)
 * dan membuang script/handler event/URI berbahaya.
 */
class HtmlSanitizer
{
    protected static array $allowedTags = [
        'p',
        'br',
        'div',
        'span',
        'strong',
        'b',
        'em',
        'i',
        'u',
        's',
        'strike',
        'sub',
        'sup',
        'ol',
        'ul',
        'li',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'blockquote',
        'pre',
        'code',
        'hr',
        'a',
        'font',
        'table',
        'thead',
        'tbody',
        'tfoot',
        'tr',
        'td',
        'th',
        'img',
    ];

    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        // Placeholder kosong Summernote/Quill
        $trim = trim($html);
        if (in_array($trim, ['<p><br></p>', '<p><br/></p>', '<p></p>', '<br>', '<br/>'], true)) {
            return '';
        }

        // 1) Buang elemen berbahaya beserta isinya
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button|textarea|select)[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button|textarea|select)[^>]*/?>#i', '', $html);

        // 2) Whitelist tag
        $allowed = '<' . implode('><', self::$allowedTags) . '>';
        $html = strip_tags($html, $allowed);

        // 3) Buang atribut event handler (on*) dan javascript: URI
        $html = preg_replace('#\s*on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);

        // 4) Filter atribut per-tag (whitelist atribut aman)
        $html = preg_replace_callback('#<([a-z][a-z0-9]*)\b([^>]*)>#i', function ($m) {
            $tag  = strtolower($m[1]);
            $attr = $m[2];
            $keep = '';

            // style: hanya properti aman
            if (preg_match('#style\s*=\s*("|\')(.*?)\1#is', $attr, $s)) {
                $safe = [];
                foreach (explode(';', $s[2]) as $decl) {
                    if (preg_match('#\s*(color|background-color|background|text-align|font-size|font-family|font-weight|font-style|text-decoration|width|height|border|border-collapse|padding|margin|vertical-align)\s*:\s*([^;]+)#i', $decl, $d)) {
                        $val = trim($d[2]);
                        // tolak url()/expression() berbahaya
                        if (!preg_match('#(url\s*\(|expression\s*\(|javascript:)#i', $val)) {
                            $safe[] = strtolower(trim($d[1])) . ':' . $val;
                        }
                    }
                }
                if ($safe) $keep .= ' style="' . htmlspecialchars(implode(';', $safe), ENT_QUOTES) . '"';
            }

            // class: hanya note-* / ql-* (alignment Summernote/Quill)
            if (preg_match('#class\s*=\s*("|\')(.*?)\1#i', $attr, $c)) {
                $classes = array_filter(explode(' ', $c[2]), fn($cl) => preg_match('#^(note|ql)-#', trim($cl)));
                if ($classes) $keep .= ' class="' . implode(' ', $classes) . '"';
            }

            // href untuk <a>
            if ($tag === 'a' && preg_match('#href\s*=\s*("|\')(.*?)\1#i', $attr, $h)) {
                $url = trim($h[2]);
                if (preg_match('#^(https?:|mailto:|/|\#)#i', $url)) {
                    $keep .= ' href="' . htmlspecialchars($url, ENT_QUOTES) . '" target="_blank" rel="noopener"';
                }
            }

            // <img>: src (hanya http/https/path relatif; base64 data-URI DITOLAK untuk cegah bloat DB), alt, width, height
            if ($tag === 'img') {
                if (preg_match('#src\s*=\s*("|\')(.*?)\1#is', $attr, $src)) {
                    $url = trim($src[2]);
                    if (preg_match('#^(https?:|/)#i', $url)) {
                        $keep .= ' src="' . htmlspecialchars($url, ENT_QUOTES) . '"';
                    } else {
                        // base64/data-URI atau skema lain -> drop seluruh tag img
                        return '';
                    }
                } else {
                    return '';
                }
                foreach (['alt', 'width', 'height'] as $a) {
                    if (preg_match('#' . $a . '\s*=\s*("|\')(.*?)\1#i', $attr, $mm)) {
                        $keep .= ' ' . $a . '="' . htmlspecialchars($mm[2], ENT_QUOTES) . '"';
                    }
                }
                $keep .= ' style="max-width:100%;height:auto;"';
            }

            // <font>: face, color (legacy Summernote)
            if ($tag === 'font') {
                foreach (['face', 'color'] as $a) {
                    if (preg_match('#' . $a . '\s*=\s*("|\')(.*?)\1#i', $attr, $mm)) {
                        $keep .= ' ' . $a . '="' . htmlspecialchars($mm[2], ENT_QUOTES) . '"';
                    }
                }
            }

            // atribut tabel
            if (in_array($tag, ['table', 'td', 'th', 'tr'])) {
                foreach (['colspan', 'rowspan', 'align', 'valign'] as $a) {
                    if (preg_match('#' . $a . '\s*=\s*("|\')(.*?)\1#i', $attr, $mm)) {
                        $keep .= ' ' . $a . '="' . htmlspecialchars($mm[2], ENT_QUOTES) . '"';
                    }
                }
            }

            return '<' . $tag . $keep . '>';
        }, $html);

        return trim($html);
    }
}
