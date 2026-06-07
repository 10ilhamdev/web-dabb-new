/**
 * CMS RichTextEditor - Self-built, free, MIT-style.
 * Vanilla JS + contenteditable. No external dependencies.
 *
 * Public API (compatible with the commercial richtexteditor.com bindings
 * already used across this project):
 *
 *   var editor = new RichTextEditor(selector, config);
 *   editor.getHTMLCode();
 *   editor.setHTMLCode(html);
 *   editor.getHTML();          // alias
 *   editor.setHTML(html);      // alias
 *   editor.insertHTML(html);
 *   editor.insertImageByUrl(url);
 *   editor.focus();
 *   editor.destroy();
 *
 * Config:
 *   {
 *     base_url: '/cms_rte',
 *     toolbar: 'default' | 'basic' | Array<row[]>,    // arrays accepted for compatibility
 *     editorBodyCssClass: 'rte-content-body',
 *     file_upload_handler: function(file, callback) { ... callback(url) ... },
 *     readOnly: false,
 *     height: '320px',
 *   }
 */
(function (global) {
    'use strict';

    if (global.RichTextEditor) return;

    // ---------------------------------------------------------------------
    // Utilities
    // ---------------------------------------------------------------------
    var uid = 0;
    function nextId() { return 'rte_' + (++uid) + '_' + Date.now().toString(36); }

    function el(tag, attrs, children) {
        var e = document.createElement(tag);
        if (attrs) {
            for (var k in attrs) {
                if (!Object.prototype.hasOwnProperty.call(attrs, k)) continue;
                var v = attrs[k];
                if (k === 'class') e.className = v;
                else if (k === 'style' && typeof v === 'object') {
                    for (var s in v) e.style[s] = v[s];
                } else if (k === 'html') e.innerHTML = v;
                else if (k === 'text') e.textContent = v;
                else if (k.indexOf('on') === 0 && typeof v === 'function') {
                    e.addEventListener(k.substring(2), v);
                } else if (v === true) e.setAttribute(k, '');
                else if (v !== false && v != null) e.setAttribute(k, v);
            }
        }
        if (children) {
            if (!Array.isArray(children)) children = [children];
            for (var i = 0; i < children.length; i++) {
                var c = children[i];
                if (c == null) continue;
                e.appendChild(typeof c === 'string' ? document.createTextNode(c) : c);
            }
        }
        return e;
    }

    function resolveTarget(selector) {
        if (!selector) return null;
        if (typeof selector === 'string') return document.querySelector(selector);
        return selector;
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function convertGoogleDriveUrl(url, type) {
        if (!url) return '';
        var match = url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/);
        var fileId = null;
        if (match) fileId = match[1];
        if (!fileId) {
            match = url.match(/[?&]id=([a-zA-Z0-9_-]+)/);
            if (match) fileId = match[1];
        }
        if (fileId) {
            if (type === 'video') {
                return 'https://drive.google.com/file/d/' + fileId + '/preview';
            }
            return 'https://lh3.googleusercontent.com/d/' + fileId;
        }
        return url;
    }

    // SVG icons (inline, monochrome — colored via CSS currentColor)
    var ICON = {
        bold: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 5h6a3.5 3.5 0 0 1 0 7H7zM7 12h7a3.5 3.5 0 0 1 0 7H7z"/></svg>',
        italic: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>',
        underline: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 4v7a6 6 0 0 0 12 0V4"/><line x1="4" y1="21" x2="20" y2="21"/></svg>',
        strike: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="12" x2="20" y2="12"/><path d="M16 6a4 4 0 0 0-4-2c-2.5 0-4 1.5-4 3.5S9.5 11 12 11"/><path d="M8 17c1 2 2.5 3 4 3 2.5 0 4-1.5 4-3.5"/></svg>',
        sub: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 14 10 14 10 20"/><polyline points="20 10 14 10 14 4"/><line x1="14" y1="10" x2="21" y2="3"/><line x1="3" y1="21" x2="10" y2="14"/></svg>',
        sup: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 10 10 10 10 4"/><polyline points="20 14 14 14 14 20"/><line x1="10" y1="14" x2="3" y2="21"/><line x1="21" y1="3" x2="14" y2="10"/></svg>',
        ul: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4.5" cy="6" r="1.2" fill="currentColor"/><circle cx="4.5" cy="12" r="1.2" fill="currentColor"/><circle cx="4.5" cy="18" r="1.2" fill="currentColor"/></svg>',
        ol: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-ol" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5"/><path d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635z"/></svg>',
        indent: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="11" y1="12" x2="21" y2="12"/><line x1="11" y1="18" x2="21" y2="18"/><polyline points="3,10 7,12 3,14"/></svg>',
        outdent: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="11" y1="12" x2="21" y2="12"/><line x1="11" y1="18" x2="21" y2="18"/><polyline points="7,10 3,12 7,14"/></svg>',
        alignLeft: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></svg>',
        alignCenter: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="6" y1="12" x2="18" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>',
        alignRight: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="6" y1="18" x2="21" y2="18"/></svg>',
        alignJustify: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
        link: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 14a5 5 0 0 0 7.07 0l3-3a5 5 0 0 0-7.07-7.07l-1.5 1.5"/><path d="M14 10a5 5 0 0 0-7.07 0l-3 3a5 5 0 0 0 7.07 7.07l1.5-1.5"/></svg>',
        unlink: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6l-2 2"/><path d="M6 18l2-2"/><path d="M11 14a4 4 0 0 0 5.66 0l2-2"/><path d="M13 10a4 4 0 0 0-5.66 0l-2 2"/><line x1="3" y1="3" x2="21" y2="21"/></svg>',
        toc: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="9" x2="17" y2="9"/><line x1="7" y1="13" x2="17" y2="13"/><line x1="7" y1="17" x2="13" y2="17"/><circle cx="5" cy="9" r="0.8" fill="currentColor"/><circle cx="5" cy="13" r="0.8" fill="currentColor"/><circle cx="5" cy="17" r="0.8" fill="currentColor"/></svg>',
        image: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>',
        video: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>',
        table: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/></svg>',
        hr: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="4" y1="12" x2="20" y2="12"/></svg>',
        quote: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 7H5c-1 0-2 1-2 2v4c0 1 1 2 2 2h2v1c0 2-2 3-2 3l1 1s4-1 4-5V9c0-1-.4-2-1-2zm10 0h-4c-1 0-2 1-2 2v4c0 1 1 2 2 2h2v1c0 2-2 3-2 3l1 1s4-1 4-5V9c0-1-.4-2-1-2z"/></svg>',
        code: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
        clean: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 17l7-13 7 13"/><line x1="3" y1="3" x2="21" y2="21"/></svg>',
        undo: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-counterclockwise" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2z"/><path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466"/></svg>',
        redo: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/></svg>',
        source: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 2 12 6 15"/><polyline points="18 9 22 12 18 15"/><line x1="14" y1="6" x2="10" y2="18"/></svg>',
        fullscreen: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9V3h6"/><path d="M21 9V3h-6"/><path d="M3 15v6h6"/><path d="M21 15v6h-6"/></svg>',
        chevron: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>',
        paint: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-palette" viewBox="0 0 16 16"><path d="M8 5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3m4 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M5.5 7a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m.5 6a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/><path d="M16 8c0 3.15-1.866 2.585-3.567 2.07C11.42 9.763 10.465 9.473 10 10c-.603.683-.475 1.819-.351 2.92C9.826 14.495 9.996 16 8 16a8 8 0 1 1 8-8m-8 7c.611 0 .654-.171.655-.176.078-.146.124-.464.07-1.119-.014-.168-.037-.37-.061-.591-.052-.464-.112-1.005-.118-1.462-.01-.707.083-1.61.704-2.314.369-.417.845-.578 1.272-.618.404-.038.812.026 1.16.104.343.077.702.186 1.025.284l.028.008c.346.105.658.199.953.266.653.148.904.083.991.024C14.717 9.38 15 9.161 15 8a7 7 0 1 0-7 7"/></svg>',
        textcolor: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 20h14"/><path d="M7 17l5-12 5 12"/><line x1="9" y1="13" x2="15" y2="13"/></svg>',
        copyformat: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16"><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/><path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/></svg>',
        pasteformat: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-check" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/><path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/></svg>',
        find: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
        searchblock: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="10" cy="10" r="3"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="7" y1="15" x2="17" y2="15"/></svg>',
        replace: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
        template: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>',
        tableEdit: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/></svg>',
        imageEdit: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
        emoji: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="9" cy="10" r="0.8" fill="currentColor"/><circle cx="15" cy="10" r="0.8" fill="currentColor"/><path d="M8 14.5c1 1.5 2.5 2.2 4 2.2s3-.7 4-2.2"/></svg>',
        document: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
        lineheight: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
        insertRowBefore: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="10" width="18" height="10" rx="1"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="10" x2="9" y2="20"/><line x1="15" y1="10" x2="15" y2="20"/><line x1="12" y1="2" x2="12" y2="8"/><line x1="9" y1="5" x2="15" y2="5"/></svg>',
        insertRowAfter: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="10" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="4" x2="9" y2="14"/><line x1="15" y1="4" x2="15" y2="14"/><line x1="12" y1="16" x2="12" y2="22"/><line x1="9" y1="19" x2="15" y2="19"/></svg>',
        insertColBefore: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="10" y="3" width="10" height="18" rx="1"/><line x1="10" y1="9" x2="20" y2="9"/><line x1="10" y1="15" x2="20" y2="15"/><line x1="15" y1="3" x2="15" y2="21"/><line x1="2" y1="12" x2="8" y2="12"/><line x1="5" y1="9" x2="5" y2="15"/></svg>',
        insertColAfter: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="4" y="3" width="10" height="18" rx="1"/><line x1="4" y1="9" x2="14" y2="9"/><line x1="4" y1="15" x2="14" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="16" y1="12" x2="22" y2="12"/><line x1="19" y1="9" x2="19" y2="15"/></svg>',
        deleteRow: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="6" width="18" height="12" rx="1"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="9" y1="6" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="18"/><line x1="9" y1="12" x2="15" y2="12" stroke="red"/><line x1="10" y1="10" x2="14" y2="14" stroke="red"/><line x1="14" y1="10" x2="10" y2="14" stroke="red"/></svg>',
        deleteCol: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="6" y="3" width="12" height="18" rx="1"/><line x1="6" y1="9" x2="18" y2="9"/><line x1="6" y1="15" x2="18" y2="15"/><line x1="12" y1="3" x2="12" y2="21"/><line x1="12" y1="9" x2="12" y2="15" stroke="red"/><line x1="10" y1="10" x2="14" y2="14" stroke="red"/><line x1="14" y1="10" x2="10" y2="14" stroke="red"/></svg>',
        deleteTable: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>',
        mergeCells: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/><path d="M11 7h2m-1-1v2m3-1h2m-1-1v2" stroke-width="1.5"/></svg>',
        splitCell: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="12" y1="3" x2="12" y2="21"/><line x1="3" y1="12" x2="21" y2="12"/></svg>',
        tableHeader: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="3" x2="9" y2="9"/></svg>',
        // New table-related icons (correct SVG icons for their functions)
        tableDelete: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>',
        tableInsert: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>',
        tableCellProp: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="9"/><line x1="15" y1="3" x2="15" y2="9"/><line x1="9" y1="9" x2="9" y2="21"/><line x1="15" y1="9" x2="15" y2="21"/></svg>',
        // tableRowDelete: row with minus sign — indicates remove one row
        tableRowDelete: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="6" width="18" height="12" rx="1"/><line x1="8" y1="4" x2="16" y2="4"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="18" x2="16" y2="18"/><line x1="12" y1="11" x2="12" y2="13" stroke-width="2.5"/></svg>',
        // tableColDelete: column with minus sign — indicates remove one column
        tableColDelete: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="6" y="3" width="12" height="18" rx="1"/><line x1="4" y1="8" x2="20" y2="8"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="16" x2="20" y2="16"/><line x1="11" y1="10" x2="13" y2="10" stroke-width="2.5"/></svg>',
        tableCellBg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="1"/><rect x="7" y="7" width="10" height="10" rx="1" fill="currentColor" opacity="0.3"/></svg>',
        tableCellAlign: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="7" y1="8" x2="14" y2="8"/><line x1="7" y1="16" x2="11" y2="16"/></svg>',
        // tableColWidth: horizontal arrows — means "fit/auto width"
        tableColWidth: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="12" x2="20" y2="12"/><polyline points="7 9 4 12 7 15"/><polyline points="17 9 20 12 17 15"/></svg>',
        zoomIn: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>',
        zoomOut: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>',
        zoomReset: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.35"/></svg>',
        fontSize: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><text x="3" y="18" font-size="14" font-weight="bold" fill="currentColor" stroke="none" font-family="serif">T</text><line x1="4" y1="20" x2="20" y2="20"/></svg>',
        gripIcon: '<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="5" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="15" cy="19" r="1.5"/></svg>',
        cellMerge: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/><line x1="7" y1="11" x2="17" y2="11"/></svg>',
        cellSplit: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="12" y1="3" x2="12" y2="21"/><line x1="3" y1="12" x2="21" y2="12"/></svg>',
        tableCellHighlight: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><rect x="9" y="9" width="6" height="6" fill="currentColor" stroke="none"/></svg>',
        tableRowHighlight: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><rect x="3" y="9" width="18" height="6" fill="currentColor" stroke="none"/></svg>',
        tableColHighlight: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><rect x="9" y="3" width="6" height="18" fill="currentColor" stroke="none"/></svg>',
        carousel: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11V9a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2"/><path d="M4 13v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/><path d="M7 12h10"/></svg>',
        list_alpha: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><text x="3" y="8.5" font-size="9" font-weight="bold" fill="currentColor" stroke="none" font-family="sans-serif">a</text><text x="3" y="14.5" font-size="9" font-weight="bold" fill="currentColor" stroke="none" font-family="sans-serif">b</text><text x="3" y="20.5" font-size="9" font-weight="bold" fill="currentColor" stroke="none" font-family="sans-serif">c</text></svg>',
        list_multilevel: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="12" y1="12" x2="21" y2="12"/><line x1="16" y1="18" x2="21" y2="18"/><text x="2" y="8.5" font-size="8" font-weight="bold" fill="currentColor" stroke="none" font-family="sans-serif">1</text><text x="6" y="14.5" font-size="8" font-weight="bold" fill="currentColor" stroke="none" font-family="sans-serif">a</text><text x="10" y="20.5" font-size="8" font-weight="bold" fill="currentColor" stroke="none" font-family="sans-serif">i</text></svg>',
        borders: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="12" y1="3" x2="12" y2="21"/></svg>',
        borderBottom: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="12" y1="3" x2="12" y2="21" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="3" y1="12" x2="21" y2="12" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="3" y1="21" x2="21" y2="21" stroke-width="2"/></svg>',
        borderTop: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="12" y1="3" x2="12" y2="21" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="3" y1="12" x2="21" y2="12" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="3" y1="3" x2="21" y2="3" stroke-width="2"/></svg>',
        borderLeft: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="12" y1="3" x2="12" y2="21" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="3" y1="12" x2="21" y2="12" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="3" y1="3" x2="3" y2="21" stroke-width="2"/></svg>',
        borderRight: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="12" y1="3" x2="12" y2="21" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="3" y1="12" x2="21" y2="12" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="21" y1="3" x2="21" y2="21" stroke-width="2"/></svg>',
        borderNone: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="1" stroke-dasharray="2 2" opacity="0.4"/><line x1="12" y1="3" x2="12" y2="21" stroke-dasharray="2 2" opacity="0.4"/><line x1="3" y1="12" x2="21" y2="12" stroke-dasharray="2 2" opacity="0.4"/></svg>',
        borderAll: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="12" y1="3" x2="12" y2="21"/><line x1="3" y1="12" x2="21" y2="12"/></svg>',
        borderOutside: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="12" y1="3" x2="12" y2="21" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="3" y1="12" x2="21" y2="12" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/></svg>',
        borderInside: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="12" y1="3" x2="12" y2="21"/><line x1="3" y1="12" x2="21" y2="12"/></svg>',
        borderInsideH: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="12" y1="3" x2="12" y2="21" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="3" y1="12" x2="21" y2="12"/></svg>',
        borderInsideV: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="12" y1="3" x2="12" y2="21"/><line x1="3" y1="12" x2="21" y2="12" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/></svg>',
        borderDiagonalDown: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="3" y1="3" x2="21" y2="21"/></svg>',
        borderDiagonalUp: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1" stroke-dasharray="2 2" stroke-width="1" opacity="0.4"/><line x1="3" y1="21" x2="21" y2="3"/></svg>',
        fontoptions: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/><circle cx="18" cy="18" r="3"/></svg>',
    };

    // ---------------------------------------------------------------------
    // Color palettes
    // ---------------------------------------------------------------------
    var COLORS = [
        '#000000', '#424242', '#636363', '#9C9C9C', '#CFCFCF', '#EFEFEF', '#F3F3F3', '#FFFFFF',
        '#FF0000', '#FF9C00', '#FFFF00', '#00FF00', '#00FFFF', '#0000FF', '#9C00FF', '#FF00FF',
        '#F4CCCC', '#FCE5CD', '#FFF2CC', '#D9EAD3', '#D0E0E3', '#CFE2F3', '#D9D2E9', '#EAD1DC',
        '#EA9999', '#F9CB9C', '#FFE599', '#B6D7A8', '#A2C4C9', '#9FC5E8', '#B4A7D6', '#D5A6BD',
        '#E06666', '#F6B26B', '#FFD966', '#93C47D', '#76A5AF', '#6FA8DC', '#8E7CC3', '#C27BA0',
        '#CC0000', '#E69138', '#F1C232', '#6AA84F', '#45818E', '#3D85C6', '#674EA7', '#A64D79',
        '#990000', '#B45F06', '#BF9000', '#38761D', '#134F5C', '#0B5394', '#351C75', '#741B47',
        '#660000', '#783F04', '#7F6000', '#274E13', '#0C343D', '#073763', '#20124D', '#4C1130'
    ];

    var FONT_FAMILIES = [
        { label: 'Default', value: '' },
        { label: 'Agency FB', value: '"Agency FB", sans-serif' },
        { label: 'Algerian', value: 'Algerian, serif' },
        { label: 'Arial', value: 'Arial, sans-serif' },
        { label: 'Arial Black', value: '"Arial Black", sans-serif' },
        { label: 'Arial Narrow', value: '"Arial Narrow", sans-serif' },
        { label: 'Arial Rounded MT Bold', value: '"Arial Rounded MT Bold", sans-serif' },
        { label: 'Bahnschrift', value: 'Bahnschrift, sans-serif' },
        { label: 'Baskerville', value: 'Baskerville, serif' },
        { label: 'Bell MT', value: '"Bell MT", serif' },
        { label: 'Berlin Sans FB', value: '"Berlin Sans FB", sans-serif' },
        { label: 'Bodoni MT', value: '"Bodoni MT", serif' },
        { label: 'Book Antiqua', value: '"Book Antiqua", serif' },
        { label: 'Bookman Old Style', value: '"Bookman Old Style", serif' },
        { label: 'Bradley Hand ITC', value: '"Bradley Hand ITC", cursive' },
        { label: 'Brush Script MT', value: '"Brush Script MT", cursive' },
        { label: 'Calibri', value: 'Calibri, sans-serif' },
        { label: 'Cambria', value: 'Cambria, serif' },
        { label: 'Candara', value: 'Candara, sans-serif' },
        { label: 'Castellar', value: 'Castellar, serif' },
        { label: 'Centaur', value: 'Centaur, serif' },
        { label: 'Century', value: 'Century, serif' },
        { label: 'Century Gothic', value: '"Century Gothic", sans-serif' },
        { label: 'Chiller', value: 'Chiller, fantasy' },
        { label: 'Colonna MT', value: '"Colonna MT", serif' },
        { label: 'Comic Sans MS', value: '"Comic Sans MS", cursive' },
        { label: 'Consolas', value: 'Consolas, monospace' },
        { label: 'Constantia', value: 'Constantia, serif' },
        { label: 'Cooper Black', value: '"Cooper Black", serif' },
        { label: 'Copperplate Gothic Bold', value: '"Copperplate Gothic Bold", serif' },
        { label: 'Corbel', value: 'Corbel, sans-serif' },
        { label: 'Courier New', value: '"Courier New", monospace' },
        { label: 'Elephant', value: 'Elephant, serif' },
        { label: 'Engravers MT', value: '"Engravers MT", serif' },
        { label: 'Eras ITC', value: '"Eras ITC", sans-serif' },
        { label: 'Felix Titling', value: '"Felix Titling", serif' },
        { label: 'Forte', value: 'Forte, cursive' },
        { label: 'Franklin Gothic Book', value: '"Franklin Gothic Book", sans-serif' },
        { label: 'Franklin Gothic Medium', value: '"Franklin Gothic Medium", sans-serif' },
        { label: 'Freestyle Script', value: '"Freestyle Script", cursive' },
        { label: 'French Script MT', value: '"French Script MT", cursive' },
        { label: 'Garamond', value: 'Garamond, serif' },
        { label: 'Georgia', value: 'Georgia, serif' },
        { label: 'Gigi', value: 'Gigi, cursive' },
        { label: 'Gill Sans MT', value: '"Gill Sans MT", sans-serif' },
        { label: 'Gloucester MT Extra Condensed', value: '"Gloucester MT Extra Condensed", serif' },
        { label: 'Goudy Old Style', value: '"Goudy Old Style", serif' },
        { label: 'Haettenschweiler', value: 'Haettenschweiler, sans-serif' },
        { label: 'Harlow Solid Italic', value: '"Harlow Solid Italic", cursive' },
        { label: 'Harrington', value: 'Harrington, fantasy' },
        { label: 'Helvetica', value: 'Helvetica, Arial, sans-serif' },
        { label: 'High Tower Text', value: '"High Tower Text", serif' },
        { label: 'Impact', value: 'Impact, sans-serif' },
        { label: 'Imprint MT Shadow', value: '"Imprint MT Shadow", serif' },
        { label: 'Informal Roman', value: '"Informal Roman", serif' },
        { label: 'Jokerman', value: 'Jokerman, fantasy' },
        { label: 'Juice ITC', value: '"Juice ITC", cursive' },
        { label: 'Kristen ITC', value: '"Kristen ITC", cursive' },
        { label: 'Kunstler Script', value: '"Kunstler Script", cursive' },
        { label: 'Lucida Bright', value: '"Lucida Bright", serif' },
        { label: 'Lucida Calligraphy', value: '"Lucida Calligraphy", cursive' },
        { label: 'Lucida Console', value: '"Lucida Console", monospace' },
        { label: 'Lucida Fax', value: '"Lucida Fax", serif' },
        { label: 'Lucida Handwriting', value: '"Lucida Handwriting", cursive' },
        { label: 'Lucida Sans', value: '"Lucida Sans", sans-serif' },
        { label: 'Lucida Sans Typewriter', value: '"Lucida Sans Typewriter", monospace' },
        { label: 'Lucida Sans Unicode', value: '"Lucida Sans Unicode", sans-serif' },
        { label: 'Magneto', value: 'Magneto, cursive' },
        { label: 'Maiandra GD', value: '"Maiandra GD", sans-serif' },
        { label: 'Matura MT Script Capitals', value: '"Matura MT Script Capitals", cursive' },
        { label: 'Mistral', value: 'Mistral, cursive' },
        { label: 'Modern No. 20', value: '"Modern No. 20", serif' },
        { label: 'Monotype Corsiva', value: '"Monotype Corsiva", cursive' },
        { label: 'Niagara Engraved', value: '"Niagara Engraved", serif' },
        { label: 'Niagara Solid', value: '"Niagara Solid", serif' },
        { label: 'OCR A Extended', value: '"OCR A Extended", monospace' },
        { label: 'Old English Text MT', value: '"Old English Text MT", serif' },
        { label: 'Onyx', value: 'Onyx, sans-serif' },
        { label: 'Palace Script MT', value: '"Palace Script MT", cursive' },
        { label: 'Palatino Linotype', value: '"Palatino Linotype", serif' },
        { label: 'Papyrus', value: 'Papyrus, fantasy' },
        { label: 'Parchment', value: 'Parchment, cursive' },
        { label: 'Perpetua', value: 'Perpetua, serif' },
        { label: 'Perpetua Titling MT', value: '"Perpetua Titling MT", serif' },
        { label: 'Playbill', value: 'Playbill, sans-serif' },
        { label: 'Poor Richard', value: '"Poor Richard", serif' },
        { label: 'Pristina', value: 'Pristina, cursive' },
        { label: 'Rage Italic', value: '"Rage Italic", cursive' },
        { label: 'Ravie', value: 'Ravie, cursive' },
        { label: 'Rockwell', value: 'Rockwell, serif' },
        { label: 'Script MT Bold', value: '"Script MT Bold", cursive' },
        { label: 'Segoe Print', value: '"Segoe Print", cursive' },
        { label: 'Segoe Script', value: '"Segoe Script", cursive' },
        { label: 'Segoe UI', value: '"Segoe UI", sans-serif' },
        { label: 'Showcard Gothic', value: '"Showcard Gothic", sans-serif' },
        { label: 'Snap ITC', value: '"Snap ITC", fantasy' },
        { label: 'Stencil', value: 'Stencil, sans-serif' },
        { label: 'Tahoma', value: 'Tahoma, sans-serif' },
        { label: 'Tempus Sans ITC', value: '"Tempus Sans ITC", sans-serif' },
        { label: 'Times New Roman', value: '"Times New Roman", Times, serif' },
        { label: 'Trebuchet MS', value: '"Trebuchet MS", sans-serif' },
        { label: 'Tw Cen MT', value: '"Tw Cen MT", sans-serif' },
        { label: 'Verdana', value: 'Verdana, Geneva, sans-serif' },
        { label: 'Viner Hand ITC', value: '"Viner Hand ITC", cursive' },
        { label: 'Vivaldi', value: 'Vivaldi, cursive' },
        { label: 'Vladimir Script', value: '"Vladimir Script", cursive' },
        { label: 'Wide Latin', value: '"Wide Latin", serif' }
    ];

    var FONT_SIZES = [
        { label: '8', value: '8pt' },
        { label: '9', value: '9pt' },
        { label: '10', value: '10pt' },
        { label: '11', value: '11pt' },
        { label: '12', value: '12pt' },
        { label: '14', value: '14pt' },
        { label: '16', value: '16pt' },
        { label: '18', value: '18pt' },
        { label: '20', value: '20pt' },
        { label: '22', value: '22pt' },
        { label: '24', value: '24pt' },
        { label: '26', value: '26pt' },
        { label: '28', value: '28pt' },
        { label: '36', value: '36pt' },
        { label: '48', value: '48pt' },
        { label: '72', value: '72pt' },
    ];

    var FONT_EFFECTS = [
        { label: 'Double Strikethrough', value: 'double-strike' },
        { label: 'Small Caps', value: 'small-caps' },
        { label: 'All Caps', value: 'all-caps' },
        { label: 'Sentence case', value: 'case-sentence' },
        { label: 'lowercase', value: 'case-lowercase' },
        { label: 'UPPERCASE', value: 'case-uppercase' },
        { label: 'Capitalize Each Word', value: 'case-capitalize' },
        { label: 'tOGGLE cASE', value: 'case-toggle' },
    ];

    var BLOCK_FORMATS = [
        { label: 'Paragraph', value: '<p>' },
        { label: 'Heading 1', value: '<h1>' },
        { label: 'Heading 2', value: '<h2>' },
        { label: 'Heading 3', value: '<h3>' },
        { label: 'Heading 4', value: '<h4>' },
        { label: 'Heading 5', value: '<h5>' },
        { label: 'Heading 6', value: '<h6>' },
        { label: 'Quote', value: '<blockquote>' },
        { label: 'Code', value: '<pre>' },
    ];

    var LINE_HEIGHTS = [
        { label: '100%', value: '100' },
        { label: '150%', value: '150' },
        { label: '200%', value: '200' },
        { label: '300%', value: '300' },
        { label: '400%', value: '400' },
        { label: '500%', value: '500' },
        { label: '600%', value: '600' },
    ];

    // ---------------------------------------------------------------------
    // Default toolbar layout (rows of groups; each group is array of buttons)
    // ---------------------------------------------------------------------
    var DEFAULT_TOOLBAR = [
        [
            { kind: 'dropdown', name: 'paragraph', label: 'Paragraph', items: BLOCK_FORMATS, action: 'block' },
            { kind: 'dropdown', name: 'font', label: 'Font', items: FONT_FAMILIES, action: 'fontName' },
            { kind: 'dropdown', name: 'size', label: 'Size', items: FONT_SIZES, action: 'fontSize' },
            { kind: 'dropdown', name: 'lineheight', title: 'Line Height', icon: ICON.lineheight, items: LINE_HEIGHTS, action: 'lineHeight' },
            { kind: 'dropdown', name: 'fontoptions', title: 'Font Effects', icon: ICON.fontoptions, items: FONT_EFFECTS, action: 'fontEffect' },
        ],
        [
            { kind: 'btn', name: 'bold', icon: ICON.bold, title: 'Bold (Ctrl+B)', cmd: 'bold' },
            { kind: 'btn', name: 'italic', icon: ICON.italic, title: 'Italic (Ctrl+I)', cmd: 'italic' },
            { kind: 'btn', name: 'underline', icon: ICON.underline, title: 'Underline (Ctrl+U)', cmd: 'underline' },
            { kind: 'btn', name: 'strike', icon: ICON.strike, title: 'Strikethrough', cmd: 'strikeThrough' },
            { kind: 'btn', name: 'sub', icon: ICON.sub, title: 'Subscript', cmd: 'subscript' },
            { kind: 'btn', name: 'sup', icon: ICON.sup, title: 'Superscript', cmd: 'superscript' },
        ],
        [
            { kind: 'color', name: 'forecolor', icon: ICON.textcolor, title: 'Text color', cmd: 'foreColor' },
            { kind: 'color', name: 'backcolor', icon: ICON.paint, title: 'Highlight', cmd: 'hiliteColor' },
            { kind: 'btn', name: 'copyformat', icon: ICON.copyformat, title: 'Copy Format', custom: 'copyformat' },
            { kind: 'btn', name: 'pasteformat', icon: ICON.pasteformat, title: 'Paste Format', custom: 'pasteformat' },
            { kind: 'btn', name: 'removeformat', icon: ICON.clean, title: 'Remove Format', cmd: 'removeFormat' },
        ],
        [
            { kind: 'btn', name: 'alignleft', icon: ICON.alignLeft, title: 'Align left', cmd: 'justifyLeft' },
            { kind: 'btn', name: 'aligncenter', icon: ICON.alignCenter, title: 'Align center', cmd: 'justifyCenter' },
            { kind: 'btn', name: 'alignright', icon: ICON.alignRight, title: 'Align right', cmd: 'justifyRight' },
            { kind: 'btn', name: 'alignjustify', icon: ICON.alignJustify, title: 'Justify', cmd: 'justifyFull' },
            { kind: 'btn', name: 'find', icon: ICON.find, title: 'Find & Replace', custom: 'find' },
            { kind: 'btn', name: 'searchblock', icon: ICON.searchblock, title: 'Insert Search Block (Kolom Pencarian)', custom: 'searchblock' },
        ],
        [
            { kind: 'btn', name: 'ul', icon: ICON.ul, title: 'Bullet list', cmd: 'insertUnorderedList' },
            { kind: 'btn', name: 'ol', icon: ICON.ol, title: 'Numbered list', cmd: 'insertOrderedList' },
            { kind: 'btn', name: 'list_alpha', icon: ICON.list_alpha, title: 'Alphabetical list', custom: 'list_alpha' },
            { kind: 'btn', name: 'list_multilevel', icon: ICON.list_multilevel, title: 'Multi-level list', custom: 'list_multilevel' },
            { kind: 'btn', name: 'outdent', icon: ICON.outdent, title: 'Decrease indent', cmd: 'outdent' },
            { kind: 'btn', name: 'indent', icon: ICON.indent, title: 'Increase indent', cmd: 'indent' },
            { kind: 'btn', name: 'quote', icon: ICON.quote, title: 'Quote', custom: 'blockquote' },
            { kind: 'btn', name: 'code', icon: ICON.code, title: 'Insert Code Block', custom: 'codeblock' },
        ],
        [
            { kind: 'btn', name: 'link', icon: ICON.link, title: 'Insert link', custom: 'link' },
            { kind: 'btn', name: 'unlink', icon: ICON.unlink, title: 'Remove link', cmd: 'unlink' },
            { kind: 'btn', name: 'toc', icon: ICON.toc, title: 'Insert Table of Contents (Daftar Isi)', custom: 'toc' },
            { kind: 'btn', name: 'image', icon: ICON.image, title: 'Insert image', custom: 'image' },
            { kind: 'btn', name: 'video', icon: ICON.video, title: 'Insert video', custom: 'video' },
            { kind: 'btn', name: 'carousel', icon: ICON.carousel, title: 'Insert Carousel', custom: 'carousel' },
            { kind: 'btn', name: 'document', icon: ICON.document, title: 'Insert Document', custom: 'document' },
            { kind: 'btn', name: 'table', icon: ICON.table, title: 'Insert table', custom: 'table' },
            { kind: 'btn', name: 'hr', icon: ICON.hr, title: 'Horizontal line', custom: 'hr' },
            { kind: 'btn', name: 'emoji', icon: ICON.emoji, title: 'Emoji', custom: 'emoji' },
        ],
        [
            { kind: 'btn', name: 'undo', icon: ICON.undo, title: 'Undo (Ctrl+Z)', custom: 'undo' },
            { kind: 'btn', name: 'redo', icon: ICON.redo, title: 'Redo (Ctrl+Y)', custom: 'redo' },
            { kind: 'btn', name: 'template', icon: ICON.template, title: 'Insert Template', custom: 'template' },
            { kind: 'btn', name: 'source', icon: ICON.source, title: 'View HTML source', custom: 'source' },
            { kind: 'btn', name: 'fullscreen', icon: ICON.fullscreen, title: 'Toggle full screen', custom: 'fullscreen' },
            { kind: 'btn', name: 'zoomOut', icon: ICON.zoomOut, title: 'Zoom Out', custom: 'zoomOut' },
            { kind: 'btn', name: 'zoomIn', icon: ICON.zoomIn, title: 'Zoom In', custom: 'zoomIn' },
            { kind: 'btn', name: 'zoomReset', icon: ICON.zoomReset, title: 'Reset Zoom', custom: 'zoomReset' },
        ],
    ];

    var BASIC_TOOLBAR = [
        [
            { kind: 'btn', name: 'bold', icon: ICON.bold, title: 'Bold', cmd: 'bold' },
            { kind: 'btn', name: 'italic', icon: ICON.italic, title: 'Italic', cmd: 'italic' },
            { kind: 'btn', name: 'underline', icon: ICON.underline, title: 'Underline', cmd: 'underline' },
        ],
        [
            { kind: 'btn', name: 'ul', icon: ICON.ul, title: 'Bullet list', cmd: 'insertUnorderedList' },
            { kind: 'btn', name: 'ol', icon: ICON.ol, title: 'Numbered list', cmd: 'insertOrderedList' },
            { kind: 'btn', name: 'list_alpha', icon: ICON.list_alpha, title: 'Alphabetical list', custom: 'list_alpha' },
            { kind: 'btn', name: 'list_multilevel', icon: ICON.list_multilevel, title: 'Multi-level list', custom: 'list_multilevel' },
        ],
        [
            { kind: 'btn', name: 'link', icon: ICON.link, title: 'Insert link', custom: 'link' },
            { kind: 'btn', name: 'image', icon: ICON.image, title: 'Insert image', custom: 'image' },
        ],
        [
            { kind: 'btn', name: 'clean', icon: ICON.clean, title: 'Clear formatting', cmd: 'removeFormat' },
        ],
    ];

    function resolveToolbar(cfg) {
        var t = cfg.toolbar;
        if (!t || t === 'default') return DEFAULT_TOOLBAR;
        if (t === 'basic') return BASIC_TOOLBAR;
        // Compatibility with Quill-style array configs: just use default.
        if (Array.isArray(t)) return DEFAULT_TOOLBAR;
        return DEFAULT_TOOLBAR;
    }

    // ---------------------------------------------------------------------
    // Selection helpers (saving/restoring caret across dialog interactions)
    // ---------------------------------------------------------------------
    function saveSelection(rootEl) {
        var sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return null;
        var range = sel.getRangeAt(0);
        if (!rootEl.contains(range.commonAncestorContainer)) return null;
        return range.cloneRange();
    }

    function restoreSelection(range) {
        if (!range) return;
        try {
            var sel = window.getSelection();
            if (sel) {
                sel.removeAllRanges();
                sel.addRange(range);
            }
        } catch (e) {
            console.warn('[RTE] restoreSelection failed', e);
        }
    }

    function placeCursorAtEnd(node) {
        var range = document.createRange();
        range.selectNodeContents(node);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    }

    // ---------------------------------------------------------------------
    // Modal helper
    // ---------------------------------------------------------------------
    function openModal(opts) {
        // opts: { title, body (DOM), onConfirm, confirmLabel, cancelLabel, wide }
        var backdrop = el('div', { class: 'rte-modal-backdrop' });
        var dialog = el('div', { class: 'rte-modal' + (opts.wide ? ' rte-modal-wide' : '') });
        var header = el('div', { class: 'rte-modal-header' }, [
            el('div', { class: 'rte-modal-drag-handle', html: '<svg width="16" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg"><line x1="1" y1="1" x2="15" y2="1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><line x1="1" y1="5" x2="15" y2="5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><line x1="1" y1="9" x2="15" y2="9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>' }),
            el('div', { class: 'rte-modal-title', text: opts.title || '' }),
            el('button', {
                type: 'button', class: 'rte-modal-close', title: 'Close',
                html: '&times;',
                onclick: close,
            }),
        ]);
        var body = el('div', { class: 'rte-modal-body' }, [opts.body]);
        var footer = el('div', { class: 'rte-modal-footer' }, [
            el('button', {
                type: 'button', class: 'rte-btn rte-btn-secondary',
                text: opts.cancelLabel || 'Cancel',
                onclick: close,
            }),
            el('button', {
                type: 'button', class: 'rte-btn rte-btn-primary',
                text: opts.confirmLabel || 'Insert',
                onclick: function () {
                    var keepOpen = false;
                    if (typeof opts.onConfirm === 'function') {
                        keepOpen = opts.onConfirm() === false;
                    }
                    if (!keepOpen) close();
                },
            }),
        ]);
        dialog.appendChild(header);
        dialog.appendChild(body);
        dialog.appendChild(footer);
        backdrop.appendChild(dialog);
        backdrop.addEventListener('mousedown', function (e) { if (e.target === backdrop) close(); });
        document.body.appendChild(backdrop);

        // --- Draggable modal (centered using flexbox) ---
        var isDragging = false;
        var dragOffX = 0, dragOffY = 0;

        // On mobile (< 640px), center using flexbox. On desktop, use absolute positioning for drag
        var isMobile = window.innerWidth < 640;
        if (isMobile) {
            // Mobile: let flexbox handle centering
            dialog.style.position = 'relative';
            dialog.style.margin = 'auto';
        } else {
            // Desktop: calculate centered position for dragging
            var initLeft = (backdrop.clientWidth - dialog.offsetWidth) / 2;
            var initTop = (backdrop.clientHeight - dialog.offsetHeight) / 2;
            dialog.style.position = 'absolute';
            dialog.style.left = Math.max(initLeft, 16) + 'px';
            dialog.style.top = Math.max(initTop, 16) + 'px';
            dialog.style.maxHeight = (backdrop.clientHeight - 32) + 'px';
            dialog.style.overflow = 'auto';
        }

        function onDragStart(e) {
            if (isMobile) return; // No dragging on mobile
            var target = e.target;
            // Don't start drag if clicking a button, input, or the close button
            while (target && target !== dialog) {
                if (target.tagName === 'BUTTON' || target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'SELECT') return;
                if (target.classList && (target.classList.contains('rte-modal-close') || target.classList.contains('rte-btn'))) return;
                target = target.parentNode;
            }

            isDragging = true;
            var clientX = e.touches ? e.touches[0].clientX : e.clientX;
            var clientY = e.touches ? e.touches[0].clientY : e.clientY;
            dragOffX = clientX - dialog.offsetLeft;
            dragOffY = clientY - dialog.offsetTop;
            document.addEventListener('mousemove', onDragMove);
            document.addEventListener('mouseup', onDragEnd);
            document.addEventListener('touchmove', onDragMove, { passive: false });
            document.addEventListener('touchend', onDragEnd);
            e.preventDefault();
            e.stopPropagation();
        }

        function onDragMove(e) {
            if (!isDragging) return;
            e.preventDefault();
            var clientX = e.touches ? e.touches[0].clientX : e.clientX;
            var clientY = e.touches ? e.touches[0].clientY : e.clientY;
            var newLeft = clientX - dragOffX;
            var newTop = clientY - dragOffY;
            // Clamp within backdrop bounds
            var maxLeft = backdrop.clientWidth - dialog.offsetWidth;
            var maxTop = backdrop.clientHeight - dialog.offsetHeight;
            newLeft = Math.max(0, Math.min(newLeft, maxLeft));
            newTop = Math.max(0, Math.min(newTop, maxTop));
            dialog.style.left = newLeft + 'px';
            dialog.style.top = newTop + 'px';
        }

        function onDragEnd() {
            isDragging = false;
            document.removeEventListener('mousemove', onDragMove);
            document.removeEventListener('mouseup', onDragEnd);
            document.removeEventListener('touchmove', onDragMove);
            document.removeEventListener('touchend', onDragEnd);
        }

        header.addEventListener('mousedown', onDragStart);
        header.addEventListener('touchstart', onDragStart, { passive: false });

        function close() {
            if (backdrop.parentNode) backdrop.parentNode.removeChild(backdrop);
            document.removeEventListener('keydown', onKey);
        }
        function onKey(e) { if (e.key === 'Escape') close(); }
        document.addEventListener('keydown', onKey);

        // Auto-focus first input
        setTimeout(function () {
            var f = dialog.querySelector('input,textarea,select');
            if (f) f.focus();
        }, 30);

        return { close: close, dialog: dialog };
    }

    // ---------------------------------------------------------------------
    // Editor class
    // ---------------------------------------------------------------------
    function RichTextEditor(selector, config) {
        if (!(this instanceof RichTextEditor)) return new RichTextEditor(selector, config);

        var target = resolveTarget(selector);
        if (!target) {
            console.error('[RTE] target not found for selector', selector);
            return;
        }

        this.config = Object.assign({
            base_url: '/cms_rte',
            toolbar: 'default',
            editorBodyCssClass: 'rte-content-body',
            file_upload_handler: null,
            readOnly: false,
            height: '500px',
        }, config || {});

        this.id = nextId();
        this._target = target;
        this._initialHTML = target.innerHTML || '';
        target.innerHTML = '';

        // ---- Zoom state ----
        this._zoom = 1;
        this._zoomMin = 0.3;
        this._zoomMax = 2.5;
        this._zoomStep = 0.1;

        // ---- Custom undo/redo history (step-by-step, not native browser) ----
        this._history = [];
        this._historyIndex = -1;
        this._historyMax = 100; // max steps to remember
        // Flag to skip history push during initial content injection
        this._initializing = true;
        // Guard flag to prevent overlay destruction during active drag
        this._isResizingOrMoving = false;

        this._build(target);
        this._bind();

        // Inject initial content (from container or from textarea source)
        if (target.tagName === 'TEXTAREA') {
            this.setHTMLCode(target.value || '');
        } else {
            this.setHTMLCode(this._initialHTML || '');
        }
        // Done initializing — now history tracking is active
        this._initializing = false;
        // Set initial history entry to the actual content (not empty)
        this._history = [this.content.innerHTML];
        this._historyIndex = 0;
        this._loadPrism();
    }

    RichTextEditor.prototype._build = function (target) {
        var self = this;
        var cfg = this.config;

        // Wrapper assumes the role of `target`'s container; we place wrapper after
        // target and hide target. This lets the original element remain in DOM
        // (handy for textarea form binding).
        var wrapper = el('div', { class: 'richtexteditor rte-modern', id: this.id });
        wrapper.style.height = typeof cfg.height === 'number' ? cfg.height + 'px' : cfg.height;
        wrapper.style.minHeight = '300px';
        if (target.tagName === 'TEXTAREA') {
            target.style.display = 'none';
            target.parentNode.insertBefore(wrapper, target.nextSibling);
        } else {
            // Replace contents inside target (target is just a placeholder div).
            target.appendChild(wrapper);
            target.style.setProperty('min-height', 'auto', 'important');
            target.style.setProperty('height', 'auto', 'important');
        }

        var toolbar = el('div', { class: 'rte-toolbar', role: 'toolbar' });
        wrapper.appendChild(toolbar);

        var contentWrap = el('div', { class: 'rte-content-wrap' });
        var content = el('div', {
            class: 'rte-content ' + (cfg.editorBodyCssClass || 'rte-content-body'),
            contenteditable: cfg.readOnly ? 'false' : 'true',
            spellcheck: 'true',
            'data-placeholder': cfg.placeholder || '',
            style: 'padding-bottom: 100px !important;',
        });
        contentWrap.appendChild(content);
        wrapper.appendChild(contentWrap);

        var sourceArea = el('textarea', { class: 'rte-source', spellcheck: 'false' });
        sourceArea.style.display = 'none';
        sourceArea.style.minHeight = '220px';
        wrapper.appendChild(sourceArea);

        var statusbar = el('div', { class: 'rte-statusbar' }, [
            el('span', { class: 'rte-status-path', text: 'body' }),
            el('span', { class: 'rte-status-counts', text: '0 words • 0 chars' }),
            el('span', {
                id: 'rte-zoom-label-' + this.id,
                class: 'rte-status-zoom',
                text: '100%',
                style: 'margin-left: auto; margin-right: 30px; font-size: 11px; color: #888; cursor: pointer; padding: 0 6px; user-select: none;',
                title: 'Zoom level — use zoom buttons in toolbar to adjust',
            }),
        ]);
        wrapper.appendChild(statusbar);

        this.wrapper = wrapper;
        this.toolbar = toolbar;
        this.contentWrap = contentWrap;
        this.content = content;
        this.sourceArea = sourceArea;
        this.statusbar = statusbar;
        this._buttons = {};
        this._dropdowns = [];

        // Build toolbar items — each toolbar row is its own flex container
        var rows = resolveToolbar(cfg);
        var totalRows = rows.length;
        var half = Math.ceil(totalRows / 2);

        var tbTop = el('div', { class: 'rte-tb-top' });
        var tbBottom = el('div', { class: 'rte-tb-bottom' });

        rows.forEach(function (row, idx) {
            var rowEl = el('div', { class: 'rte-tb-row' });
            row.forEach(function (item) { self._buildToolbarItem(rowEl, item); });
            if (idx < half) {
                tbTop.appendChild(rowEl);
            } else {
                tbBottom.appendChild(rowEl);
            }
        });

        toolbar.appendChild(tbTop);
        toolbar.appendChild(tbBottom);

        // Prevent toolbar clicks from stealing focus away from the contenteditable editor.
        // Without this, clicking a button (e.g. Bold) while text is selected in a list item
        // causes mousedown to blur the editor and clear the selection BEFORE the onclick runs.
        // We allow mousedown on input/textarea/select elements so they still receive focus normally.
        toolbar.addEventListener('mousedown', function (e) {
            var tag = e.target && e.target.tagName ? e.target.tagName.toUpperCase() : '';
            if (tag !== 'INPUT' && tag !== 'TEXTAREA' && tag !== 'SELECT') {
                e.preventDefault();
            }
        });

        // Hidden file input for image upload
        this._fileInput = el('input', { type: 'file', accept: 'image/*', style: { display: 'none' } });
        wrapper.appendChild(this._fileInput);

        // Drag handle button (bottom-right of status bar) for resizing the editor
        this._buildDragHandle(statusbar);
    };

    RichTextEditor.prototype._buildToolbarItem = function (groupEl, item) {
        var self = this;
        if (item.kind === 'btn') {
            var btn = el('button', {
                type: 'button',
                class: 'rte-tb-btn',
                title: item.title || item.name,
                'data-name': item.name,
                html: item.icon || item.label || item.name,
                onclick: function (e) {
                    e.preventDefault();
                    self._focusContent();
                    if (item.cmd) self.exec(item.cmd, item.value);
                    else if (item.custom) self._customAction(item.custom);
                    self._syncSource();
                    self._updateState();
                },
            });
            groupEl.appendChild(btn);
            this._buttons[item.name] = btn;
        } else if (item.kind === 'dropdown') {
            this._buildDropdown(groupEl, item);
        } else if (item.kind === 'color') {
            this._buildColorPicker(groupEl, item);
        }
    };

    RichTextEditor.prototype._buildDropdown = function (groupEl, item) {
        var self = this;
        var label;
        if (item.icon) {
            label = el('span', { class: 'rte-dd-icon', html: item.icon });
        } else {
            label = el('span', { class: 'rte-dd-label', text: item.label || item.name });
        }
        var btn = el('button', {
            type: 'button',
            class: 'rte-tb-btn rte-tb-dropdown',
            title: item.title || item.label,
            'data-name': item.name,
        }, [label, el('span', { class: 'rte-dd-caret', html: ICON.chevron })]);
        var panel = el('div', { class: 'rte-dropdown-panel' });

        // ── Helper: detect what value is currently active for this dropdown ──
        // Always reads from self._savedRange (snapshotted just before panel opens)
        // so detection works even when focus has moved to the dropdown panel.
        function getCurrentValue() {
            // Prefer savedRange.startContainer over live window.getSelection()
            // because focus may have shifted away from editor when panel is open
            var node = null;
            if (self._savedRange && self._savedRange.startContainer) {
                node = self._savedRange.startContainer;
            } else {
                var sel = window.getSelection();
                node = sel && sel.anchorNode;
            }
            if (!node) return null;

            var el2 = node.nodeType === Node.TEXT_NODE ? node.parentNode : node;

            if (item.action === 'block') {
                var blockTags = { H1: 1, H2: 1, H3: 1, H4: 1, H5: 1, H6: 1, P: 1, BLOCKQUOTE: 1, PRE: 1 };
                var cur = el2;
                while (cur && cur !== self.content) {
                    if (blockTags[cur.tagName]) return '<' + cur.tagName.toLowerCase() + '>';
                    cur = cur.parentNode;
                }
                return '<p>';
            } else if (item.action === 'fontName') {
                // Walk up DOM to find the nearest inline font-family style or <font face>
                // This works even when queryCommandValue can't be used (no live selection)
                var fEl = el2;
                while (fEl && fEl !== self.content) {
                    if (fEl.style && fEl.style.fontFamily) {
                        var first = fEl.style.fontFamily.split(',')[0].trim().replace(/["']/g, '').toLowerCase();
                        if (first) return first;
                    }
                    if (fEl.tagName === 'FONT' && fEl.getAttribute('face')) {
                        var firstFace = fEl.getAttribute('face').split(',')[0].trim().replace(/["']/g, '').toLowerCase();
                        if (firstFace) return firstFace;
                    }
                    fEl = fEl.parentNode;
                }
                // Fallback: queryCommandValue (only accurate when selection is in editor)
                try {
                    var raw = document.queryCommandValue('fontName') || '';
                    var first2 = raw.split(',')[0].trim().replace(/["']/g, '').toLowerCase();
                    return first2 || null;
                } catch (e) { return null; }
            } else if (item.action === 'fontSize') {
                var szEl = el2;
                while (szEl && szEl !== self.content) {
                    if (szEl.style && szEl.style.fontSize) {
                        var cssSize = szEl.style.fontSize.trim().toLowerCase();
                        if (cssSize.indexOf('pt') !== -1) {
                            return cssSize;
                        }
                        if (cssSize.indexOf('px') !== -1) {
                            var px = parseFloat(cssSize);
                            var pt = Math.round(px * 0.75);
                            return pt + 'pt';
                        }
                        if (cssSize === 'x-small') return '8pt';
                        if (cssSize === 'small') return '10pt';
                        if (cssSize === 'medium') return '12pt';
                        if (cssSize === 'large') return '14pt';
                        if (cssSize === 'x-large') return '18pt';
                        if (cssSize === 'xx-large') return '24pt';
                        if (cssSize === 'xxx-large') return '36pt';
                        break;
                    }
                    if (szEl.tagName === 'FONT' && szEl.getAttribute('size')) {
                        var val = szEl.getAttribute('size');
                        var fontValToPt = {
                            '1': '8pt',
                            '2': '10pt',
                            '3': '12pt',
                            '4': '14pt',
                            '5': '18pt',
                            '6': '24pt',
                            '7': '36pt'
                        };
                        return fontValToPt[val] || val;
                    }
                    szEl = szEl.parentNode;
                }
                try {
                    var val = document.queryCommandValue('fontSize');
                    if (val) {
                        var valStr = String(val).trim().toLowerCase();
                        var fontValToPt = {
                            '1': '8pt',
                            '2': '10pt',
                            '3': '12pt',
                            '4': '14pt',
                            '5': '18pt',
                            '6': '24pt',
                            '7': '36pt'
                        };
                        return fontValToPt[valStr] || valStr;
                    }
                } catch (e) { }
                return null;
            } else if (item.action === 'lineHeight') {
                // Walk up to block element and read its inline line-height
                var blk = el2;
                while (blk && blk !== self.content) {
                    var tag2 = blk.tagName;
                    if (tag2 && /^(P|DIV|H[1-6]|LI|TR|BLOCKQUOTE|PRE)$/.test(tag2)) break;
                    blk = blk.parentElement;
                }
                if (blk && blk !== self.content && blk.style && blk.style.lineHeight) {
                    // lineHeight stored as ratio e.g. 1.5 -> return '150'
                    var lhNum = parseFloat(blk.style.lineHeight);
                    if (!isNaN(lhNum)) return String(Math.round(lhNum * 100));
                }
                return null;
            }
            return null;
        }

        // Store all option elements so we can mark the active one when panel opens
        var optEls = [];

        item.items.forEach(function (opt) {
            var optStyle = null;
            if (item.action === 'fontName' && opt.value) {
                optStyle = { fontFamily: opt.value };
            } else if (item.action === 'block' && opt.value) {
                var cleanTag = opt.value.replace(/[<>]/g, '').toLowerCase();
                if (cleanTag === 'h1') {
                    optStyle = {
                        fontSize: '18px',
                        fontWeight: '700',
                        color: '#1e40af',
                        fontStyle: 'normal',
                        textDecoration: 'none'
                    };
                } else if (cleanTag === 'h2') {
                    optStyle = {
                        fontSize: '16px',
                        fontWeight: '600',
                        color: '#2563eb',
                        fontStyle: 'normal',
                        textDecoration: 'none'
                    };
                } else if (cleanTag === 'h3') {
                    optStyle = {
                        fontSize: '14px',
                        fontWeight: '600',
                        color: '#0f172a',
                        fontStyle: 'normal',
                        textDecoration: 'none'
                    };
                } else if (cleanTag === 'h4') {
                    optStyle = {
                        fontSize: '13px',
                        fontWeight: '600',
                        color: '#334155',
                        fontStyle: 'italic',
                        textDecoration: 'none'
                    };
                } else if (cleanTag === 'h5') {
                    optStyle = {
                        fontSize: '12px',
                        fontWeight: '400',
                        color: '#475569',
                        fontStyle: 'italic',
                        textDecoration: 'none'
                    };
                } else if (cleanTag === 'h6') {
                    optStyle = {
                        fontSize: '12px',
                        fontWeight: '400',
                        color: '#64748b',
                        fontStyle: 'normal',
                        textDecoration: 'none'
                    };
                } else if (cleanTag === 'blockquote') {
                    optStyle = {
                        borderLeft: '3px solid #cfd6df',
                        paddingLeft: '6px',
                        color: '#555',
                        fontStyle: 'italic'
                    };
                }
            }

            var optEl = el('div', {
                class: 'rte-dropdown-item',
                style: optStyle,
                'data-opt-value': opt.value || '',
                onclick: function (e) {
                    e.preventDefault();
                    self._focusContent();
                    if (item.action === 'block') {
                        self.exec('formatBlock', opt.value);
                    } else if (item.action === 'fontName') {
                        self._setFontFamily(opt.value || '');
                    } else if (item.action === 'fontSize') {
                        self._setFontSize(opt.value);
                    } else if (item.action === 'lineHeight') {
                        self._setLineHeight(opt.value);
                    } else if (item.action === 'fontEffect') {
                        self._applyFontEffect(opt.value);
                    }
                    if (!item.icon) label.textContent = opt.label;
                    closePanel();
                    self._syncSource();
                    self._updateState();
                },
            });
            // Build inner: checkmark span + label text span
            var checkSpan = el('span', { class: 'rte-dd-check', text: '\u2713' });
            var textSpan = el('span', { class: 'rte-dd-opt-text', text: opt.label });
            optEl.appendChild(checkSpan);
            optEl.appendChild(textSpan);
            panel.appendChild(optEl);
            optEls.push({ el: optEl, value: opt.value || '', label: opt.label });
        });

        var open = false;

        function markActiveItem() {
            var cur = getCurrentValue();
            var anyMatch = false;
            optEls.forEach(function (o) {
                var active = false;
                if (cur !== null) {
                    if (item.action === 'block') {
                        active = (o.value.toLowerCase() === (cur || '').toLowerCase());
                    } else if (item.action === 'fontName') {
                        var optFirst = o.value.split(',')[0].trim().replace(/["']/g, '').toLowerCase();
                        if (optFirst !== '') active = (cur === optFirst);
                    } else if (item.action === 'fontSize' || item.action === 'lineHeight') {
                        active = (String(o.value) === String(cur));
                    }
                }
                if (active) anyMatch = true;
                o.el.classList.toggle('rte-dd-active', !!active);
            });
            // Fallback for Default font if no specific font is matched
            if (!anyMatch && item.action === 'fontName') {
                optEls.forEach(function (o) {
                    if (o.value === '') o.el.classList.toggle('rte-dd-active', true);
                });
            }
        }

        // Expose a way to update the button label from _updateState
        function updateLabel() {
            if (item.icon) return; // icon-only dropdowns don't have text labels
            var cur = getCurrentValue();
            var match = null;
            if (cur !== null) {
                for (var i = 0; i < optEls.length; i++) {
                    var o = optEls[i];
                    var hit = false;
                    if (item.action === 'block') {
                        hit = (o.value.toLowerCase() === cur.toLowerCase());
                    } else if (item.action === 'fontName') {
                        var optFirst = o.value.split(',')[0].trim().replace(/["']/g, '').toLowerCase();
                        if (optFirst !== '') hit = (cur === optFirst);
                    } else if (item.action === 'fontSize' || item.action === 'lineHeight') {
                        hit = (String(o.value) === String(cur));
                    }
                    if (hit) { match = o; break; }
                }
            }
            if (match) {
                label.textContent = match.label;
            } else {
                if (item.action === 'fontName') label.textContent = 'Default';
                else if (item.action === 'block') label.textContent = 'Paragraph';
                else label.textContent = item.label || item.name;
            }
        }

        function openPanel() {
            // Snapshot selection BEFORE opening panel so lineHeight and other
            // actions can restore it correctly via _focusContent()
            self._snapshotSelection();
            // Close other panels
            self._dropdowns.forEach(function (d) { d.close(); });
            markActiveItem();
            var rect = btn.getBoundingClientRect();
            panel.style.position = 'fixed';
            panel.style.left = rect.left + 'px';
            panel.style.top = (rect.bottom + 2) + 'px';
            panel.style.minWidth = Math.max(rect.width, 160) + 'px';
            document.body.appendChild(panel);
            open = true;
            setTimeout(function () { document.addEventListener('mousedown', onDocClick); }, 0);
        }
        function closePanel() {
            if (!open) return;
            if (panel.parentNode) panel.parentNode.removeChild(panel);
            open = false;
            document.removeEventListener('mousedown', onDocClick);
        }
        function onDocClick(e) { if (!panel.contains(e.target) && e.target !== btn) closePanel(); }

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (open) closePanel(); else openPanel();
        });

        groupEl.appendChild(btn);
        this._dropdowns.push({ close: closePanel, updateLabel: updateLabel });
    };

    RichTextEditor.prototype._buildColorPicker = function (groupEl, item) {
        var self = this;
        var btn = el('button', {
            type: 'button',
            class: 'rte-tb-btn rte-tb-color',
            title: item.title,
            'data-name': item.name,
            html: item.icon,
        });
        var swatch = el('span', { class: 'rte-tb-color-swatch', style: { background: item.cmd === 'foreColor' ? '#000000' : '#ffeb3b' } });
        btn.appendChild(swatch);

        // Helper: get current color from cursor position by walking up the DOM
        function getCurrentColor() {
            var node = null;
            if (self._savedRange && self._savedRange.startContainer) {
                node = self._savedRange.startContainer;
            } else {
                var sel = window.getSelection();
                node = sel && sel.anchorNode;
            }
            if (!node) return null;
            var el2 = node.nodeType === Node.TEXT_NODE ? node.parentNode : node;
            var cur = el2;
            while (cur && cur !== self.content) {
                if (cur.style) {
                    if (item.cmd === 'foreColor' && cur.style.color) {
                        return cur.style.color;
                    } else if (item.cmd === 'hiliteColor' && cur.style.backgroundColor) {
                        return cur.style.backgroundColor;
                    }
                }
                // Also check font tag attributes
                if (cur.tagName === 'FONT' && cur.getAttribute('color') && item.cmd === 'foreColor') {
                    return cur.getAttribute('color');
                }
                cur = cur.parentNode;
            }
            return null;
        }

        // Normalize rgb(r,g,b) -> #rrggbb hex
        function rgbToHex(rgb) {
            if (!rgb) return null;
            if (rgb.charAt(0) === '#') return rgb;
            var m = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)/);
            if (!m) return null;
            return '#' + [m[1], m[2], m[3]].map(function (x) {
                return ('0' + parseInt(x, 10).toString(16)).slice(-2);
            }).join('');
        }

        var panel = el('div', { class: 'rte-dropdown-panel rte-color-panel' });
        var grid = el('div', { class: 'rte-color-grid' });
        COLORS.forEach(function (c) {
            grid.appendChild(el('button', {
                type: 'button',
                class: 'rte-color-cell',
                style: { background: c },
                title: c,
                onclick: function (e) {
                    e.preventDefault();
                    self._focusContent();
                    if (item.cmd === 'foreColor') {
                        self._setForeColor(c);
                    } else {
                        self._setHiliteColor(c);
                    }
                    swatch.style.background = c;
                    closePanel();
                    self._syncSource();
                    self._updateState();
                },
            }));
        });
        panel.appendChild(grid);
        var customRow = el('div', { class: 'rte-color-custom' });
        var input = el('input', { type: 'color', value: '#000000' });
        var apply = el('button', {
            type: 'button', class: 'rte-btn rte-btn-secondary rte-btn-sm', text: 'Apply',
            onclick: function (e) {
                e.preventDefault();
                self._focusContent();
                if (item.cmd === 'foreColor') {
                    self._setForeColor(input.value);
                } else {
                    self._setHiliteColor(input.value);
                }
                swatch.style.background = input.value;
                closePanel();
                self._syncSource();
                self._updateState();
            },
        });
        var clear = el('button', {
            type: 'button', class: 'rte-btn rte-btn-secondary rte-btn-sm', text: 'Remove',
            onclick: function (e) {
                e.preventDefault();
                self._focusContent();
                if (item.cmd === 'foreColor') {
                    document.execCommand('styleWithCSS', false, true);
                    document.execCommand('foreColor', false, 'inherit');
                    var sel2 = window.getSelection();
                    if (sel2 && sel2.rangeCount > 0) {
                        var range2 = sel2.getRangeAt(0);
                        var container2 = range2.commonAncestorContainer;
                        if (container2.nodeType === Node.TEXT_NODE) container2 = container2.parentNode;
                        var els = [];
                        var walker = document.createTreeWalker(container2, NodeFilter.SHOW_ELEMENT, null, false);
                        while (walker.nextNode()) {
                            var n = walker.currentNode;
                            if (range2.intersectsNode(n)) els.push(n);
                        }
                        els.push(container2);
                        els.forEach(function (n) {
                            if (n !== self.content && n.style) {
                                n.style.color = '';
                                if (!n.getAttribute('style') || n.getAttribute('style').trim() === '') {
                                    n.removeAttribute('style');
                                }
                            }
                            if (n.tagName === 'FONT') n.removeAttribute('color');
                        });
                    }
                    swatch.style.background = '#000000';
                } else {
                    document.execCommand('styleWithCSS', false, true);
                    document.execCommand('hiliteColor', false, 'transparent');
                    var sel3 = window.getSelection();
                    if (sel3 && sel3.rangeCount > 0) {
                        var range3 = sel3.getRangeAt(0);
                        var container3 = range3.commonAncestorContainer;
                        if (container3.nodeType === Node.TEXT_NODE) container3 = container3.parentNode;
                        var els3 = [];
                        var walker3 = document.createTreeWalker(container3, NodeFilter.SHOW_ELEMENT, null, false);
                        while (walker3.nextNode()) {
                            var n3 = walker3.currentNode;
                            if (range3.intersectsNode(n3)) els3.push(n3);
                        }
                        els3.push(container3);
                        els3.forEach(function (n3) {
                            if (n3 !== self.content && n3.style) {
                                n3.style.backgroundColor = '';
                                if (!n3.getAttribute('style') || n3.getAttribute('style').trim() === '') {
                                    n3.removeAttribute('style');
                                }
                            }
                        });
                    }
                    swatch.style.background = 'transparent';
                }
                closePanel();
                self._syncSource();
                self._updateState();
            },
        });
        customRow.appendChild(input);
        customRow.appendChild(apply);
        customRow.appendChild(clear);
        panel.appendChild(customRow);

        var open = false;
        function openPanel() {
            // Snapshot selection BEFORE panel opens so focus restore works correctly
            self._snapshotSelection();
            // Update custom color input to reflect current color at cursor
            var curColor = getCurrentColor();
            var hexColor = rgbToHex(curColor);
            if (hexColor) {
                input.value = hexColor;
                swatch.style.background = hexColor;
            }
            self._dropdowns.forEach(function (d) { d.close(); });
            var rect = btn.getBoundingClientRect();
            panel.style.position = 'fixed';
            panel.style.left = rect.left + 'px';
            panel.style.top = (rect.bottom + 2) + 'px';
            document.body.appendChild(panel);
            open = true;
            setTimeout(function () { document.addEventListener('mousedown', onDocClick); }, 0);
        }
        function closePanel() {
            if (!open) return;
            if (panel.parentNode) panel.parentNode.removeChild(panel);
            open = false;
            document.removeEventListener('mousedown', onDocClick);
        }
        function onDocClick(e) { if (!panel.contains(e.target) && e.target !== btn) closePanel(); }

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (open) closePanel(); else openPanel();
        });

        groupEl.appendChild(btn);
        // Expose updateSwatch so _updateState can sync swatch to cursor color
        this._dropdowns.push({
            close: closePanel, updateSwatch: function () {
                var c = getCurrentColor();
                var hex = rgbToHex(c);
                if (hex) {
                    swatch.style.background = hex;
                } else if (item.cmd === 'foreColor') {
                    swatch.style.background = '#000000';
                } else {
                    swatch.style.background = 'transparent';
                }
            }
        });
    };

    RichTextEditor.prototype._buildDragHandle = function (container) {
        var self = this;
        var wrap = el('div', { class: 'rte-drag-handle-wrap' });
        var handle = el('button', {
            type: 'button',
            class: 'rte-drag-handle',
            title: 'Drag to resize editor',
            'aria-label': 'Drag to resize editor',
            onmousedown: function (e) {
                e.preventDefault();
                e.stopPropagation();
                self._startResize(e);
            },
        });
        // Resize grip icon (diagonal arrows like richtexteditor.com)
        handle.innerHTML = '<svg viewBox="0 0 140 140" xmlns="http://www.w3.org/2000/svg" fill="#888"><rect x="100" y="0" width="20" height="20"/><rect x="75" y="25" width="20" height="20"/><rect x="100" y="25" width="20" height="20"/><rect x="50" y="50" width="20" height="20"/><rect x="75" y="50" width="20" height="20"/><rect x="100" y="50" width="20" height="20"/><rect x="25" y="75" width="20" height="20"/><rect x="50" y="75" width="20" height="20"/><rect x="75" y="75" width="20" height="20"/><rect x="100" y="75" width="20" height="20"/><rect x="0" y="100" width="20" height="20"/><rect x="25" y="100" width="20" height="20"/><rect x="50" y="100" width="20" height="20"/><rect x="75" y="100" width="20" height="20"/><rect x="100" y="100" width="20" height="20"/></svg>';
        wrap.appendChild(handle);
        container.appendChild(wrap);
    };

    RichTextEditor.prototype._startResize = function (e) {
        var self = this;
        var wrapper = this.wrapper;
        var contentWrap = this.contentWrap;

        // Remember initial sizes
        var startH = wrapper.offsetHeight;
        var startY = e.clientY;

        wrapper.classList.add('rte-dragging');
        wrapper.style.userSelect = 'none';

        function onMove(e) {
            var dy = e.clientY - startY;
            // Only resize height — width stays at 100% of parent
            var newH = Math.max(300, startH + dy);
            wrapper.style.height = newH + 'px';
            // Ensure wrapper never exceeds parent width
            var parentW = wrapper.parentElement ? wrapper.parentElement.clientWidth : wrapper.offsetWidth;
            if (wrapper.offsetWidth > parentW) {
                wrapper.style.width = parentW + 'px';
            }
        }

        function onUp() {
            wrapper.classList.remove('rte-dragging');
            wrapper.style.userSelect = '';
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
        }

        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    };

    RichTextEditor.prototype._setFontSize = function (val) {
        this._focusContent();
        document.execCommand('styleWithCSS', false, true);

        // Detect if the selection is inside a heading. If so, the new size
        // must be applied to the HEADING element itself — applying it to
        // a child <font>/<span> gets clobbered by the heading's font-size
        // CSS rule. The user is explicitly trying to change the heading size.
        var sel = window.getSelection();
        var headingAncestor = null;
        if (sel && sel.rangeCount > 0) {
            var node = sel.anchorNode;
            var walker = node && node.nodeType === Node.TEXT_NODE ? node.parentNode : node;
            while (walker && walker !== this.content) {
                if (walker.tagName && /^H[1-6]$/.test(walker.tagName)) {
                    headingAncestor = walker;
                    break;
                }
                walker = walker.parentNode;
            }
        }

        if (headingAncestor) {
            // Strip the dummy wrapper that execCommand would add so we don't
            // pollute the DOM, then set the size directly on the heading.
            document.execCommand('fontSize', false, '7');
            var allEls = this.content.querySelectorAll('*');
            allEls.forEach(function (elem) {
                var fs = elem.style && elem.style.fontSize ? elem.style.fontSize.trim().toLowerCase() : '';
                var isDummy = (fs === 'xxx-large' || fs === '-webkit-xxx-large' ||
                    fs === '48px' || fs === '36pt' ||
                    (elem.tagName === 'FONT' && elem.getAttribute('size') === '7'));
                if (isDummy) {
                    elem.removeAttribute('size');
                    elem.style.fontSize = '';
                }
            });
            headingAncestor.style.fontSize = val;
            // Clear inner fontSizes under the heading too to avoid overriding
            headingAncestor.querySelectorAll('*').forEach(function (child) {
                if (child.style) child.style.fontSize = '';
                if (child.tagName === 'FONT') child.removeAttribute('size');
            });
        } else {
            // Standard path: use a dummy font size '7' to wrap the selection,
            // then replace all dummy-marked elements with the real size.
            document.execCommand('fontSize', false, '7');
            var allEls2 = this.content.querySelectorAll('*');
            allEls2.forEach(function (elem) {
                var fs = elem.style && elem.style.fontSize ? elem.style.fontSize.trim().toLowerCase() : '';
                var isDummy = (fs === 'xxx-large' || fs === '-webkit-xxx-large' ||
                    fs === '48px' || fs === '36pt' ||
                    (elem.tagName === 'FONT' && elem.getAttribute('size') === '7'));
                if (isDummy) {
                    elem.removeAttribute('size');
                    elem.style.fontSize = val; // e.g. "12pt"
                    // Clear inner fontSizes under the dummy to avoid overriding
                    elem.querySelectorAll('*').forEach(function (child) {
                        if (child.style) child.style.fontSize = '';
                        if (child.tagName === 'FONT') child.removeAttribute('size');
                    });
                }
            });
        }

        this._syncSource();
        this._updateState();
    };

    RichTextEditor.prototype._setFontFamily = function (val) {
        this._focusContent();
        document.execCommand('styleWithCSS', false, true);

        // Detect if the selection is inside a heading.
        var sel = window.getSelection();
        var headingAncestor = null;
        if (sel && sel.rangeCount > 0) {
            var node = sel.anchorNode;
            var walker = node && node.nodeType === Node.TEXT_NODE ? node.parentNode : node;
            while (walker && walker !== this.content) {
                if (walker.tagName && /^H[1-6]$/.test(walker.tagName)) {
                    headingAncestor = walker;
                    break;
                }
                walker = walker.parentNode;
            }
        }

        if (headingAncestor) {
            document.execCommand('fontName', false, 'RTE-DUMMY-FONT');
            var allEls = this.content.querySelectorAll('*');
            allEls.forEach(function (elem) {
                var ff = elem.style && elem.style.fontFamily ? elem.style.fontFamily.trim().toLowerCase() : '';
                var isDummy = (ff.indexOf('rte-dummy-font') !== -1 || (elem.tagName === 'FONT' && elem.getAttribute('face') === 'RTE-DUMMY-FONT'));
                if (isDummy) {
                    if (elem.tagName === 'FONT') elem.removeAttribute('face');
                    elem.style.fontFamily = '';
                }
            });
            if (val && val !== 'inherit') {
                headingAncestor.style.fontFamily = val;
            } else {
                headingAncestor.style.fontFamily = '';
            }
            headingAncestor.querySelectorAll('*').forEach(function (child) {
                if (child.style) child.style.fontFamily = '';
                if (child.tagName === 'FONT') child.removeAttribute('face');
            });
        } else {
            document.execCommand('fontName', false, 'RTE-DUMMY-FONT');
            var allEls2 = this.content.querySelectorAll('*');
            allEls2.forEach(function (elem) {
                var ff = elem.style && elem.style.fontFamily ? elem.style.fontFamily.trim().toLowerCase() : '';
                var isDummy = (ff.indexOf('rte-dummy-font') !== -1 || (elem.tagName === 'FONT' && elem.getAttribute('face') === 'RTE-DUMMY-FONT'));
                if (isDummy) {
                    if (elem.tagName === 'FONT') elem.removeAttribute('face');
                    if (val && val !== 'inherit') {
                        elem.style.fontFamily = val;
                    } else {
                        elem.style.fontFamily = '';
                    }
                    elem.querySelectorAll('*').forEach(function (child) {
                        if (child.style) child.style.fontFamily = '';
                        if (child.tagName === 'FONT') child.removeAttribute('face');
                    });
                }
            });
        }

        this._syncSource();
        this._updateState();
    };

    RichTextEditor.prototype._setForeColor = function (val) {
        this._focusContent();
        document.execCommand('styleWithCSS', false, true);
        document.execCommand('foreColor', false, '#123456');
        var allEls = this.content.querySelectorAll('*');
        allEls.forEach(function (elem) {
            var color = elem.style && elem.style.color ? elem.style.color.trim().toLowerCase() : '';
            var colorClean = color.replace(/\s+/g, '');
            var isDummy = (colorClean === 'rgb(18,52,86)' || colorClean === '#123456' || (elem.tagName === 'FONT' && elem.getAttribute('color') === '#123456'));
            if (isDummy) {
                if (elem.tagName === 'FONT') elem.removeAttribute('color');
                elem.style.color = val;
                elem.querySelectorAll('*').forEach(function (child) {
                    if (child.style) child.style.color = '';
                    if (child.tagName === 'FONT') child.removeAttribute('color');
                });
            }
        });
        this._syncSource();
        this._updateState();
    };

    RichTextEditor.prototype._setHiliteColor = function (val) {
        this._focusContent();
        document.execCommand('styleWithCSS', false, true);
        document.execCommand('hiliteColor', false, '#654321');
        var allEls = this.content.querySelectorAll('*');
        allEls.forEach(function (elem) {
            var bg = elem.style && elem.style.backgroundColor ? elem.style.backgroundColor.trim().toLowerCase() : '';
            var bgClean = bg.replace(/\s+/g, '');
            var isDummy = (bgClean === 'rgb(101,67,33)' || bgClean === '#654321');
            if (isDummy) {
                elem.style.backgroundColor = val;
                elem.querySelectorAll('*').forEach(function (child) {
                    if (child.style) child.style.backgroundColor = '';
                });
            }
        });
        this._syncSource();
        this._updateState();
    };

    RichTextEditor.prototype._changeCase = function (type) {
        var sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return;
        var range = sel.getRangeAt(0);
        var content = range.cloneContents();
        if (!content) return;

        function traverse(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                var txt = node.nodeValue;
                if (type === 'uppercase') {
                    node.nodeValue = txt.toUpperCase();
                } else if (type === 'lowercase') {
                    node.nodeValue = txt.toLowerCase();
                } else if (type === 'sentence') {
                    var lower = txt.toLowerCase();
                    node.nodeValue = lower.replace(/(^\s*|[.!?]\s+)([a-z])/g, function (m, p1, p2) {
                        return p1 + p2.toUpperCase();
                    });
                } else if (type === 'capitalize') {
                    node.nodeValue = txt.replace(/\b([a-z])/g, function (m, p1) {
                        return p1.toUpperCase();
                    });
                } else if (type === 'toggle') {
                    var toggled = '';
                    for (var i = 0; i < txt.length; i++) {
                        var c = txt[i];
                        if (c === c.toUpperCase()) {
                            toggled += c.toLowerCase();
                        } else {
                            toggled += c.toUpperCase();
                        }
                    }
                    node.nodeValue = toggled;
                }
            } else {
                for (var i = 0; i < node.childNodes.length; i++) {
                    traverse(node.childNodes[i]);
                }
            }
        }

        traverse(content);
        range.deleteContents();
        range.insertNode(content);
        this._syncSource();
        this._updateState();
    };

    RichTextEditor.prototype._applyFontEffect = function (effect) {
        this._focusContent();
        document.execCommand('styleWithCSS', false, true);

        if (effect === 'double-strike') {
            document.execCommand('strikeThrough', false, null);
            var spans = this.content.querySelectorAll('strike, span[style*="line-through"]');
            spans.forEach(function (el) {
                if (el.style.textDecoration.indexOf('line-through') !== -1 || el.tagName === 'STRIKE') {
                    el.style.textDecoration = 'line-through double';
                }
            });
        } else if (effect === 'small-caps') {
            document.execCommand('fontSize', false, '7');
            // Query ALL elements - browser may wrap any tag with the dummy size
            var allEls = this.content.querySelectorAll('*');
            allEls.forEach(function (elem) {
                var fs = elem.style && elem.style.fontSize ? elem.style.fontSize.trim().toLowerCase() : '';
                var isDummy = (fs === 'xxx-large' || fs === '-webkit-xxx-large' ||
                    fs === '48px' || fs === '36pt' ||
                    (elem.tagName === 'FONT' && elem.getAttribute('size') === '7'));
                if (isDummy) {
                    elem.removeAttribute('size');
                    elem.style.fontSize = '';
                    elem.style.fontVariant = elem.style.fontVariant === 'small-caps' ? '' : 'small-caps';
                }
            });
        } else if (effect === 'all-caps') {
            document.execCommand('fontSize', false, '7');
            // Query ALL elements - browser may wrap any tag with the dummy size
            var allEls2 = this.content.querySelectorAll('*');
            allEls2.forEach(function (elem) {
                var fs = elem.style && elem.style.fontSize ? elem.style.fontSize.trim().toLowerCase() : '';
                var isDummy = (fs === 'xxx-large' || fs === '-webkit-xxx-large' ||
                    fs === '48px' || fs === '36pt' ||
                    (elem.tagName === 'FONT' && elem.getAttribute('size') === '7'));
                if (isDummy) {
                    elem.removeAttribute('size');
                    elem.style.fontSize = '';
                    elem.style.textTransform = elem.style.textTransform === 'uppercase' ? '' : 'uppercase';
                }
            });
        } else if (effect.indexOf('case-') === 0) {
            var caseType = effect.replace('case-', '');
            this._changeCase(caseType);
        }

        this._syncSource();
        this._updateState();
    };

    RichTextEditor.prototype._stripEditorStyles = function (root) {
        if (!root) return;
        var cells = root.querySelectorAll('td, th, table');
        cells.forEach(function (n) {
            if (n.classList.contains('rte-cell-selected')) {
                n.classList.remove('rte-cell-selected');
            }
            var styleAttr = n.getAttribute('style') || '';
            if (styleAttr) {
                if (styleAttr.indexOf('377dff') !== -1 || styleAttr.indexOf('55, 125, 255') !== -1 || styleAttr.indexOf('55,125,255') !== -1) {
                    n.style.outline = '';
                    n.style.outlineColor = '';
                    n.style.outlineWidth = '';
                    n.style.outlineStyle = '';
                    n.style.outlineOffset = '';
                    var bg = n.style.backgroundColor;
                    if (bg && (bg.indexOf('55, 125, 255') !== -1 || bg.indexOf('55,125,255') !== -1)) {
                        n.style.backgroundColor = '';
                    }
                }
            }
        });

        // Strip the delete buttons from code blocks so they are not saved in the database
        var deleteBtns = root.querySelectorAll('.rte-code-block-delete-btn');
        deleteBtns.forEach(function (btn) {
            if (btn.parentNode) btn.parentNode.removeChild(btn);
        });

        // Clean up any stale TOC highlights that might have been accidentally saved
        var staleMarks = root.querySelectorAll('.rte-toc-temp-mark');
        staleMarks.forEach(function (mark) {
            if (mark.parentNode) {
                mark.parentNode.replaceChild(document.createTextNode(mark.textContent), mark);
            }
        });

        // Clean up any stale inline TOC margins (typically pasted from a page with active TOC)
        var allEl = root.querySelectorAll('*');
        allEl.forEach(function (el) {
            var style = el.getAttribute('style') || '';
            if (style && style.indexOf('310px') !== -1) {
                var newStyle = style.replace(/margin-(left|right):\s*310px(\s*!important)?;?/gi, '');
                if (newStyle !== style) {
                    el.setAttribute('style', newStyle.trim());
                    if (!el.getAttribute('style')) {
                        el.removeAttribute('style');
                    }
                }
            }
        });
    };

    RichTextEditor.prototype._focusContent = function () {
        if (this._savedRange) {
            this.content.focus();
            try { restoreSelection(this._savedRange); } catch (e) { }
        } else {
            this.content.focus();
        }
    };

    RichTextEditor.prototype.exec = function (cmd, value) {
        var self = this;
        var activeTable = this._selectedTable;
        var sel = window.getSelection();
        var selInsideTable = false;
        if (activeTable && sel && sel.rangeCount > 0) {
            var node = sel.anchorNode;
            while (node && node !== this.content) {
                if (node === activeTable) {
                    selInsideTable = true;
                    break;
                }
                node = node ? node.parentNode : null;
            }
        }
        if (activeTable && !selInsideTable) {
            activeTable = null;
        }
        if (!activeTable) {
            if (sel && sel.rangeCount > 0) {
                var node = sel.anchorNode;
                while (node && node !== this.content) {
                    if (node && node.tagName === 'TABLE') {
                        activeTable = node;
                        break;
                    }
                    node = node ? node.parentNode : null;
                }
            }
        }

        // ---- Alignment: apply text-align directly to block elements ----
        // We bypass execCommand for justify because queryCommandState('justifyCenter') etc.
        // is completely unreliable in Chromium and gives wrong results.
        if (cmd === 'justifyLeft' || cmd === 'justifyCenter' || cmd === 'justifyRight' || cmd === 'justifyFull') {
            var textAlignValue = (cmd === 'justifyLeft' ? 'left' :
                cmd === 'justifyCenter' ? 'center' :
                    cmd === 'justifyRight' ? 'right' : 'justify');

            if (activeTable) {
                // Table cell content alignment
                var selectedCells = activeTable.querySelectorAll('.rte-cell-selected');
                if (selectedCells.length > 0) {
                    selectedCells.forEach(function (c) { c.style.textAlign = textAlignValue; });
                } else {
                    var cellRange = null;
                    var selForCell = window.getSelection();
                    if (selForCell && selForCell.rangeCount > 0) {
                        var tempRange = selForCell.getRangeAt(0);
                        if (activeTable.contains(tempRange.commonAncestorContainer)) {
                            cellRange = tempRange;
                        }
                    }
                    if (!cellRange && self._savedRange && activeTable.contains(self._savedRange.commonAncestorContainer)) {
                        cellRange = self._savedRange;
                    }
                    if (cellRange) {
                        var cellNode = cellRange.startContainer;
                        while (cellNode && cellNode !== activeTable) {
                            if (cellNode.tagName === 'TD' || cellNode.tagName === 'TH') {
                                cellNode.style.textAlign = textAlignValue;
                                break;
                            }
                            cellNode = cellNode.parentNode;
                        }
                    }
                }
                requestAnimationFrame(function () {
                    self._updateTableOverlayPosition();
                    if (self._repositionFloatTableToolbar) {
                        self._repositionFloatTableToolbar(activeTable);
                    }
                });
            } else {
                // Normal text alignment: apply text-align directly to all block elements in selection
                var range = self._savedRange;
                if (!range) {
                    var selAlign = window.getSelection();
                    if (selAlign && selAlign.rangeCount > 0) {
                        var tempRange = selAlign.getRangeAt(0);
                        if (self.content.contains(tempRange.commonAncestorContainer)) {
                            range = tempRange;
                        }
                    }
                }
                if (!range) {
                    var r = document.createRange();
                    r.selectNodeContents(self.content);
                    r.collapse(true);
                    range = r;
                }

                if (range) {
                    var blockTagsRe = /^(P|DIV|H[1-6]|LI|TD|TH|BLOCKQUOTE|PRE)$/;

                    // Collect all block elements that overlap with the selection
                    var blocksToAlign = [];

                    // Fast-path: if editor is empty/has no blocks, initialize it with a single aligned paragraph
                    if (self.content.textContent.trim() === '' && self.content.querySelectorAll('p, div, h1, h2, h3, h4, h5, h6, li, td, th').length === 0) {
                        self.content.innerHTML = '';
                        var p = document.createElement('p');
                        p.appendChild(document.createElement('br'));
                        p.style.textAlign = textAlignValue;
                        self.content.appendChild(p);
                        
                        var newRange = document.createRange();
                        newRange.selectNodeContents(p);
                        newRange.collapse(true);
                        var sel = window.getSelection();
                        if (sel) {
                            sel.removeAllRanges();
                            sel.addRange(newRange);
                        }
                        self._savedRange = newRange;
                        blocksToAlign.push(p);
                    } else {
                        var startNode = range.startContainer;
                        var ancestor = range.commonAncestorContainer;
                        if (ancestor.nodeType === Node.TEXT_NODE) ancestor = ancestor.parentNode;

                        // Find nearest block container of the selection
                        var blockTextTagsRe = /^(P|H[1-6]|LI|TD|TH|BLOCKQUOTE|PRE)$/;
                        var blockContainer = ancestor;
                        while (blockContainer && blockContainer !== self.content) {
                            if (blockContainer.tagName && (blockTextTagsRe.test(blockContainer.tagName) || blockContainer.tagName === 'DIV')) {
                                break;
                            }
                            blockContainer = blockContainer.parentNode;
                        }
                        if (!blockContainer) blockContainer = self.content;

                        if (blockContainer !== self.content && blockTextTagsRe.test(blockContainer.tagName)) {
                            // The selection is nested inside a single block container (P, H1-6, etc.). Style it directly.
                            blocksToAlign.push(blockContainer);
                        } else {
                            // The container is self.content or a DIV. It can contain direct inline children.
                            // Loop through direct children of this block container that intersect the range.
                            var childNodes = Array.prototype.slice.call(blockContainer.childNodes);
                            var intersectingNodes = [];

                            if (range.collapsed) {
                                var targetNode = range.startContainer;
                                if (targetNode === blockContainer) {
                                    var offset = range.startOffset;
                                    if (blockContainer.childNodes.length > 0) {
                                        if (offset >= blockContainer.childNodes.length) {
                                            intersectingNodes.push(blockContainer.childNodes[blockContainer.childNodes.length - 1]);
                                        } else {
                                            intersectingNodes.push(blockContainer.childNodes[offset]);
                                        }
                                    }
                                } else {
                                    // Walk up from targetNode until we find the child of blockContainer
                                    var walk = targetNode;
                                    while (walk && walk.parentNode !== blockContainer) {
                                        walk = walk.parentNode;
                                    }
                                    if (walk) {
                                        intersectingNodes.push(walk);
                                    }
                                }
                            } else {
                                childNodes.forEach(function (node) {
                                    var intersects = false;
                                    try {
                                        if (range.intersectsNode) {
                                            intersects = range.intersectsNode(node);
                                        } else {
                                            var nodeRange = document.createRange();
                                            nodeRange.selectNode(node);
                                            intersects = (range.compareBoundaryPoints(Range.END_TO_START, nodeRange) < 0 &&
                                                range.compareBoundaryPoints(Range.START_TO_END, nodeRange) > 0);
                                        }
                                    } catch (e) {
                                        intersects = false;
                                    }
                                    if (intersects) {
                                        intersectingNodes.push(node);
                                    }
                                });
                            }

                            intersectingNodes.forEach(function (node) {
                                if (node.nodeType === Node.ELEMENT_NODE && blockTagsRe.test(node.tagName)) {
                                    blocksToAlign.push(node);
                                } else {
                                    // Skip empty whitespace-only text nodes
                                    if (node.nodeType === Node.TEXT_NODE && !node.nodeValue.trim()) {
                                        return;
                                    }
                                    // Wrap this top-level inline/text node in a P element
                                    if (node.parentNode) {
                                        var p = document.createElement('p');
                                        node.parentNode.insertBefore(p, node);
                                        p.appendChild(node);
                                        blocksToAlign.push(p);
                                    }
                                }
                            });

                            // Fallback: if nothing was collected, find closest block parent of startNode
                            if (blocksToAlign.length === 0) {
                                var blk = startNode.nodeType === Node.TEXT_NODE ? startNode.parentNode : startNode;
                                var topLevel = blk;
                                while (blk && blk !== self.content) {
                                    if (blk.tagName && blockTagsRe.test(blk.tagName)) {
                                        blocksToAlign.push(blk);
                                        break;
                                    }
                                    topLevel = blk;
                                    blk = blk.parentNode;
                                }
                                // If we reached self.content and found no block parent, wrap the top-level inline element in a <p>
                                if (blocksToAlign.length === 0 && topLevel && topLevel !== self.content) {
                                    if (topLevel.nodeType === Node.TEXT_NODE && !topLevel.nodeValue.trim()) {
                                        // Do nothing for empty text
                                    } else {
                                        if (topLevel.parentNode) {
                                            var p = document.createElement('p');
                                            topLevel.parentNode.insertBefore(p, topLevel);
                                            p.appendChild(topLevel);
                                            blocksToAlign.push(p);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    blocksToAlign.forEach(function (b) {
                        b.style.textAlign = textAlignValue;
                    });
                }
            }

            this._syncSource();
            this._snapshotSelection();
            this._updateState();
            return;
        }

        if (cmd === 'indent' || cmd === 'outdent') {
            var sel = window.getSelection();
            var node = (sel && sel.rangeCount > 0) ? sel.anchorNode : null;
            var li = node ? (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('li') : null;
            if (li) {
                try {
                    document.execCommand('styleWithCSS', false, true);
                    document.execCommand(cmd, false, value);
                } catch (e) { }
                this._oListCleanup();
            } else {
                var blocks = [];
                var tagsRe = /^(H[1-6]|P|DIV|BLOCKQUOTE|PRE)$/;
                if (sel && sel.rangeCount > 0) {
                    var range = sel.getRangeAt(0);
                    var container = range.commonAncestorContainer;
                    if (container.nodeType === Node.TEXT_NODE) container = container.parentNode;

                    var startNode = range.startContainer;
                    var blk = startNode.nodeType === Node.TEXT_NODE ? startNode.parentNode : startNode;
                    while (blk && blk !== self.content) {
                        if (blk.tagName && tagsRe.test(blk.tagName)) { blocks.push(blk); break; }
                        blk = blk.parentElement;
                    }
                    if (blocks.length === 0 && !range.collapsed) {
                        var walker = document.createTreeWalker(container, NodeFilter.SHOW_ELEMENT, null, false);
                        while (walker.nextNode()) {
                            var n = walker.currentNode;
                            if (tagsRe.test(n.tagName) && range.intersectsNode(n)) {
                                if (blocks.indexOf(n) === -1) blocks.push(n);
                            }
                        }
                    }
                }
                if (blocks.length === 0) {
                    var blk = node ? (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('p, div, h1, h2, h3, h4, h5, h6, blockquote, pre') : null;
                    if (blk && blk !== self.content) blocks.push(blk);
                }

                blocks.forEach(function (block) {
                    var currentMargin = parseFloat(block.style.marginLeft) || 0;
                    if (cmd === 'indent') {
                        var newMargin = currentMargin + 40;
                        block.style.marginLeft = newMargin + 'px';
                    } else {
                        var newMargin = Math.max(0, currentMargin - 40);
                        block.style.marginLeft = newMargin ? newMargin + 'px' : '';
                    }
                });
            }
            this._syncSource();
            this._snapshotSelection();
            this._updateState();
            return;
        }

        if (cmd === 'removeFormat') {
            try {
                document.execCommand('styleWithCSS', false, true);
                document.execCommand('removeFormat', false, null);
                var sel = window.getSelection();
                if (sel && sel.rangeCount > 0) {
                    var range = sel.getRangeAt(0);
                    var container = range.commonAncestorContainer;
                    if (container.nodeType === Node.TEXT_NODE) container = container.parentNode;

                    var els = [];
                    var walker = document.createTreeWalker(container, NodeFilter.SHOW_ELEMENT, null, false);
                    while (walker.nextNode()) {
                        var n = walker.currentNode;
                        if (range.intersectsNode(n)) els.push(n);
                    }
                    els.push(container);

                    els.forEach(function (n) {
                        if (n !== self.content && n.style) {
                            n.style.fontFamily = '';
                            n.style.fontSize = '';
                            n.style.color = '';
                            n.style.backgroundColor = '';
                            n.style.background = '';
                            n.style.lineHeight = '';
                            n.style.fontWeight = '';
                            n.style.fontStyle = '';
                            n.style.textDecoration = '';
                            if (!n.getAttribute('style') || n.getAttribute('style').trim() === '') {
                                n.removeAttribute('style');
                            }
                        }
                        if (n.tagName === 'FONT') {
                            n.removeAttribute('size');
                            n.removeAttribute('color');
                            n.removeAttribute('face');
                        }
                    });
                }
            } catch (e) {
                console.warn('[RTE] removeFormat failed', e);
            }
            this._syncSource();
            this._updateState();
            return;
        }

        try {
            // hiliteColor needs styleWithCSS = true on some browsers
            document.execCommand('styleWithCSS', false, true);
            document.execCommand(cmd, false, value);
        } catch (e) {
            console.warn('[RTE] exec failed', cmd, value, e);
        }



        if (cmd === 'formatBlock') {
            try {
                var sel = window.getSelection();
                if (sel && sel.rangeCount > 0) {
                    var range = sel.getRangeAt(0);
                    var container = range.commonAncestorContainer;
                    if (container.nodeType === Node.TEXT_NODE) container = container.parentNode;

                    var blocks = [];
                    var tagsRe = /^(H[1-6]|P|DIV|BLOCKQUOTE|PRE)$/;

                    // 1. Walk up from start container to find the block element containing the cursor
                    var node = range.startContainer;
                    var pNode = node.nodeType === Node.TEXT_NODE ? node.parentNode : node;
                    while (pNode && pNode !== self.content) {
                        if (tagsRe.test(pNode.tagName)) {
                            blocks.push(pNode);
                            break;
                        }
                        pNode = pNode.parentNode;
                    }

                    // 2. Walk up from end container to find the block element (for selection spanning multiple blocks)
                    var nodeEnd = range.endContainer;
                    var pNodeEnd = nodeEnd.nodeType === Node.TEXT_NODE ? nodeEnd.parentNode : nodeEnd;
                    while (pNodeEnd && pNodeEnd !== self.content) {
                        if (tagsRe.test(pNodeEnd.tagName)) {
                            if (blocks.indexOf(pNodeEnd) === -1) blocks.push(pNodeEnd);
                            break;
                        }
                        pNodeEnd = pNodeEnd.parentNode;
                    }

                    // 3. Also gather any block elements that intersect the range (for multi-block selections)
                    if (!range.collapsed) {
                        var walker = document.createTreeWalker(container, NodeFilter.SHOW_ELEMENT, null, false);
                        while (walker.nextNode()) {
                            var n = walker.currentNode;
                            if (tagsRe.test(n.tagName) && range.intersectsNode(n)) {
                                if (blocks.indexOf(n) === -1) blocks.push(n);
                            }
                        }
                    }

                    blocks.forEach(function (block) {
                        block.style.fontFamily = '';
                        block.style.fontSize = '';
                        block.style.color = '';
                        block.style.backgroundColor = '';
                        block.style.background = '';
                        block.style.lineHeight = '';
                        block.style.fontWeight = '';
                        block.style.fontStyle = '';
                        block.style.textDecoration = '';
                        if (!block.getAttribute('style') || block.getAttribute('style').trim() === '') {
                            block.removeAttribute('style');
                        }
                        block.querySelectorAll('*').forEach(function (child) {
                            if (child.style) {
                                child.style.fontFamily = '';
                                child.style.fontSize = '';
                                child.style.color = '';
                                child.style.backgroundColor = '';
                                child.style.background = '';
                                child.style.lineHeight = '';
                                child.style.fontWeight = '';
                                child.style.fontStyle = '';
                                child.style.textDecoration = '';
                                if (!child.getAttribute('style') || child.getAttribute('style').trim() === '') {
                                    child.removeAttribute('style');
                                }
                            }
                            if (child.tagName === 'FONT') {
                                child.removeAttribute('size');
                                child.removeAttribute('color');
                                child.removeAttribute('face');
                            }
                        });
                    });
                }
            } catch (e) {
                console.warn('[RTE] formatBlock clean failed', e);
            }

            // Upgrade code blocks to premium layout immediately
            var originalSel = window.getSelection();
            var savedOffset = 0;
            var isInsidePre = false;
            if (originalSel && originalSel.rangeCount > 0) {
                var anchor = originalSel.anchorNode;
                var preParent = anchor && (anchor.nodeType === Node.TEXT_NODE ? anchor.parentNode : anchor).closest('pre');
                if (preParent) {
                    isInsidePre = true;
                    savedOffset = originalSel.anchorOffset;
                }
            }

            self._upgradeCarouselCaptions();

            if (isInsidePre) {
                var newPre = self.content.querySelector('pre');
                if (newPre) {
                    newPre.focus();
                    var range = document.createRange();
                    var textNode = newPre.firstChild || newPre;
                    try {
                        range.setStart(textNode, Math.min(savedOffset, textNode.length || 0));
                        range.collapse(true);
                        var sel = window.getSelection();
                        sel.removeAllRanges();
                        sel.addRange(range);
                    } catch (e) {
                        placeCursorAtEnd(newPre);
                    }
                }
            }
        }
        this._snapshotSelection();
    };

    RichTextEditor.prototype._customAction = function (action) {
        switch (action) {
            case 'list_alpha': return this._doListAlpha();
            case 'list_multilevel': return this._doListMultilevel();
            case 'link': return this._dialogLink();
            case 'toc': return this._dialogToc();
            case 'image': return this._dialogImage();
            case 'video': return this._dialogVideo();
            case 'carousel': return this._dialogCarousel();
            case 'table': return this._dialogTable();
            case 'hr': return this._insertHTML('<hr>');
            case 'blockquote': return this._dialogQuote();
            case 'codeblock': return this._dialogCode();
            case 'source': return this._toggleSource();
            case 'fullscreen': return this._toggleFullscreen();
            case 'emoji': return this._dialogEmoji();
            case 'copyformat': return this._doCopyFormat();
            case 'pasteformat': return this._doPasteFormat();
            case 'find': return this._dialogFindReplace();
            case 'searchblock': return this._insertSearchBlock();
            case 'template': return this._dialogTemplate();
            case 'document': return this._dialogInsertDocument();
            case 'undo': this._historyUndo(); return;
            case 'redo': this._historyRedo(); return;
            case 'zoomIn': this._zoomIn(); return;
            case 'zoomOut': this._zoomOut(); return;
            case 'zoomReset': this._zoomReset(); return;
        }
    };

    RichTextEditor.prototype._toggleBlockquote = function () {
        var sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return;
        var node = sel.anchorNode;
        var bq = node ? (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('blockquote') : null;
        if (bq) {
            // First try browser native formatBlock '<p>' which handles splitting if selection is partial
            this.exec('formatBlock', '<p>');
            // If the node is still inside bq (e.g. browser didn't unwrap it), do manual unwrap
            var stillBq = node ? (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('blockquote') : null;
            if (stillBq && stillBq === bq) {
                var frag = document.createDocumentFragment();
                while (bq.firstChild) {
                    frag.appendChild(bq.firstChild);
                }
                bq.parentNode.insertBefore(frag, bq);
                bq.parentNode.removeChild(bq);
            }
        } else {
            // Not in a blockquote, create one
            this.exec('formatBlock', '<blockquote>');
        }
        this._syncSource();
        this._updateState();
    };

    RichTextEditor.prototype._doListAlpha = function () {
        var sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return;
        var node = sel.anchorNode;
        var list = node ? (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('ol, ul') : null;
        if (list && list.tagName === 'OL' && list.type === 'a' && !list.classList.contains('rte-multilevel-list')) {
            // Already an alpha list, toggle off
            this.exec('insertOrderedList');
            this._syncSource();
            this._updateState();
            return;
        }
        if (!list) {
            this.exec('insertOrderedList');
            sel = window.getSelection();
            node = sel ? sel.anchorNode : null;
            list = node ? (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('ol, ul') : null;
        }
        if (list) {
            if (list.tagName === 'UL') {
                var ol = document.createElement('ol');
                ol.innerHTML = list.innerHTML;
                list.parentNode.replaceChild(ol, list);
                list = ol;
            }
            list.type = 'a';
            list.style.listStyleType = 'lower-alpha';
            list.classList.remove('rte-multilevel-list');
            var lis = list.querySelectorAll('li');
            lis.forEach(function (li) { li.style.listStyleType = ''; });
        }
        this._syncSource();
        this._updateState();
    };

    RichTextEditor.prototype._doListMultilevel = function () {
        var sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return;
        var node = sel.anchorNode;
        var list = node ? (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('ol, ul') : null;
        if (list && list.tagName === 'OL' && list.classList.contains('rte-multilevel-list')) {
            // Already a multilevel list, toggle off
            this.exec('insertOrderedList');
            this._syncSource();
            this._updateState();
            return;
        }
        if (!list) {
            this.exec('insertOrderedList');
            sel = window.getSelection();
            node = sel ? sel.anchorNode : null;
            list = node ? (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('ol, ul') : null;
        }
        if (list) {
            if (list.tagName === 'UL') {
                var ol = document.createElement('ol');
                ol.innerHTML = list.innerHTML;
                list.parentNode.replaceChild(ol, list);
                list = ol;
            }
            list.type = '1';
            list.style.listStyleType = 'decimal';
            list.classList.add('rte-multilevel-list');
            var lis = list.querySelectorAll('li');
            lis.forEach(function (li) { li.style.listStyleType = ''; });
            var sublists = list.querySelectorAll('ol, ul');
            sublists.forEach(function (sub) {
                if (sub.tagName === 'UL') {
                    var subOl = document.createElement('ol');
                    subOl.innerHTML = sub.innerHTML;
                    sub.parentNode.replaceChild(subOl, sub);
                    sub = subOl;
                }
                sub.removeAttribute('type');
                sub.style.listStyleType = '';
            });
        }
        this._syncSource();
        this._updateState();
    };

    RichTextEditor.prototype._oListCleanup = function () {
        if (!this.content) return;
        var multilevelLists = this.content.querySelectorAll('.rte-multilevel-list');
        multilevelLists.forEach(function (rootList) {
            var subUls = rootList.querySelectorAll('ul');
            subUls.forEach(function (ul) {
                var ol = document.createElement('ol');
                ol.innerHTML = ul.innerHTML;
                ul.parentNode.replaceChild(ol, ul);
            });
            var allSubOls = rootList.querySelectorAll('ol');
            allSubOls.forEach(function (subOl) {
                subOl.removeAttribute('type');
                subOl.style.listStyleType = '';
            });
        });
    };

    // -------- dialogs ---------
    RichTextEditor.prototype._dialogQuote = function () {
        var self = this;
        var sel = window.getSelection();
        var node = sel ? sel.anchorNode : null;
        var existingBq = node ? (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('blockquote') : null;
        var currentText = existingBq ? existingBq.textContent.replace('“', '').trim() : (sel && sel.toString() ? sel.toString().trim() : '');

        var initType = 'default';
        var initBg = '#f8fafc', initBorder = '#1d4ed8', initTextCol = '#334155', initBadge = '#1d4ed8';
        if (existingBq) {
            if (existingBq.classList.contains('rte-quote-blue')) initType = 'box_blue';
            else if (existingBq.classList.contains('rte-quote-green')) initType = 'box_green';
            else if (existingBq.classList.contains('rte-quote-gold')) initType = 'box_gold';
            else if (existingBq.classList.contains('rte-quote-custom')) {
                initType = 'custom';
                initBg = existingBq.style.backgroundColor || initBg;
                initBorder = existingBq.style.borderLeftColor || initBorder;
                initTextCol = existingBq.style.color || initTextCol;
                var bEl = existingBq.querySelector('.rte-quote-badge');
                if (bEl) initBadge = bEl.style.backgroundColor || initBadge;
            }
        }

        var typeSelect = el('select', { class: 'rte-form-input', name: 'quotetype' }, [
            el('option', { value: 'default', text: 'Style Standar (Bawaan Editor)', selected: initType === 'default' }),
            el('option', { value: 'box_blue', text: 'Kutipan Boks Dasar Hukum (Biru)', selected: initType === 'box_blue' }),
            el('option', { value: 'box_green', text: 'Kutipan Boks (Hijau)', selected: initType === 'box_green' }),
            el('option', { value: 'box_gold', text: 'Kutipan Boks (Emas / Kuning)', selected: initType === 'box_gold' }),
            el('option', { value: 'custom', text: 'Kustom Sendiri (Warna Latar & Border)', selected: initType === 'custom' }),
        ]);

        var customRow = el('div', { class: 'rte-form-group', style: 'margin-top:12px; display: ' + (initType === 'custom' ? 'block' : 'none') + ';' }, [
            el('label', { class: 'rte-form-label', text: 'Warna Latar Belakang' }),
            el('input', { type: 'color', class: 'rte-form-input', name: 'qbg', value: initBg, style: 'height:38px; padding:2px;' }),
            el('label', { class: 'rte-form-label', style: 'margin-top:10px;', text: 'Warna Garis Tepi (Border)' }),
            el('input', { type: 'color', class: 'rte-form-input', name: 'qborder', value: initBorder, style: 'height:38px; padding:2px;' }),
            el('label', { class: 'rte-form-label', style: 'margin-top:10px;', text: 'Warna Badge Ikon' }),
            el('input', { type: 'color', class: 'rte-form-input', name: 'qbadge', value: initBadge, style: 'height:38px; padding:2px;' }),
            el('label', { class: 'rte-form-label', style: 'margin-top:10px;', text: 'Warna Teks' }),
            el('input', { type: 'color', class: 'rte-form-input', name: 'qtextcol', value: initTextCol, style: 'height:38px; padding:2px;' }),
        ]);

        typeSelect.addEventListener('change', function () {
            customRow.style.display = typeSelect.value === 'custom' ? 'block' : 'none';
        });

        var textInput = el('textarea', { class: 'rte-form-input', rows: 5, name: 'quotetext', placeholder: 'Tuliskan atau edit isi kutipan di sini...' });
        textInput.value = currentText;

        var removeBtn = existingBq ? el('button', {
            type: 'button',
            class: 'rte-btn rte-btn-secondary',
            style: 'margin-top: 16px; width: 100%; background: #fee2e2; color: #991b1b; border-color: #fca5a5; font-weight: 600;',
            text: 'Hapus Quote (Kembalikan ke Teks Normal)',
            onclick: function () {
                var bd = document.querySelector('.rte-modal-backdrop');
                if (bd && bd.parentNode) bd.parentNode.removeChild(bd);
                self._toggleBlockquote();
            }
        }) : null;

        var bodyNodes = [
            el('label', { class: 'rte-form-label', text: 'Tipe / Style Kutipan' }),
            typeSelect,
            customRow,
            el('label', { class: 'rte-form-label', style: 'margin-top:12px;', text: 'Isi Teks Kutipan' }),
            textInput
        ];
        if (removeBtn) bodyNodes.push(removeBtn);

        var body = el('div', { class: 'rte-form' }, bodyNodes);

        openModal({
            title: existingBq ? 'Pengaturan Quote (Kutipan)' : 'Insert Quote (Kutipan)',
            body: body,
            confirmLabel: existingBq ? 'Terapkan' : 'Insert',
            onConfirm: function () {
                var qtype = body.querySelector('[name=quotetype]').value;
                var qtext = body.querySelector('[name=quotetext]').value.trim();
                if (!qtext) return false;

                var bg = '#f8fafc', border = '#1d4ed8', textCol = '#334155', badgeCol = '#1d4ed8', qClass = 'rte-quote-blue';
                if (qtype === 'box_green') {
                    bg = '#f0fdf4'; border = '#16a34a'; textCol = '#166534'; badgeCol = '#16a34a'; qClass = 'rte-quote-green';
                } else if (qtype === 'box_gold') {
                    bg = '#fefce8'; border = '#ca8a04'; textCol = '#854d0e'; badgeCol = '#ca8a04'; qClass = 'rte-quote-gold';
                } else if (qtype === 'custom') {
                    bg = body.querySelector('[name=qbg]').value;
                    border = body.querySelector('[name=qborder]').value;
                    badgeCol = body.querySelector('[name=qbadge]').value;
                    textCol = body.querySelector('[name=qtextcol]').value;
                    qClass = 'rte-quote-custom';
                }

                if (existingBq) {
                    if (qtype === 'default') {
                        existingBq.className = '';
                        existingBq.removeAttribute('style');
                        var badge = existingBq.querySelector('.rte-quote-badge');
                        if (badge) badge.parentNode.removeChild(badge);
                        var pEl = existingBq.querySelector('p');
                        if (pEl) pEl.textContent = qtext;
                        else existingBq.textContent = qtext;
                    } else {
                        existingBq.className = 'rte-quote-box ' + qClass;
                        existingBq.style.cssText = 'position: relative; margin: 2em 0 1.5em 0; padding: 20px 20px 20px 30px; background-color: ' + bg + '; border-left: 4px solid ' + border + '; border-radius: 0 8px 8px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); font-style: italic; color: ' + textCol + ';';

                        var badgeEl = existingBq.querySelector('.rte-quote-badge');
                        if (!badgeEl) {
                            badgeEl = document.createElement('div');
                            badgeEl.className = 'rte-quote-badge';
                            badgeEl.setAttribute('contenteditable', 'false');
                            badgeEl.textContent = '“';
                            existingBq.insertBefore(badgeEl, existingBq.firstChild);
                        }
                        badgeEl.style.cssText = 'position: absolute; top: -15px; left: 20px; width: 32px; height: 32px; background-color: ' + badgeCol + '; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: sans-serif; font-size: 20px; font-weight: bold; font-style: normal; box-shadow: 0 2px 4px rgba(0,0,0,0.2); user-select: none;';

                        var pEl = existingBq.querySelector('p');
                        if (!pEl) {
                            pEl = document.createElement('p');
                            pEl.style.cssText = 'margin: 0; line-height: 1.6;';
                            while (existingBq.childNodes.length > 1) {
                                pEl.appendChild(existingBq.childNodes[1]);
                            }
                            existingBq.appendChild(pEl);
                        }
                        pEl.textContent = qtext;
                    }
                    self._syncSource();
                    self._updateState();
                } else {
                    var html;
                    if (qtype === 'default') {
                        html = '<blockquote><p>' + escapeHtml(qtext) + '</p></blockquote>';
                    } else {
                        html = '<blockquote class="rte-quote-box ' + escapeHtml(qClass) + '" style="position: relative; margin: 2em 0 1.5em 0; padding: 20px 20px 20px 30px; background-color: ' + escapeHtml(bg) + '; border-left: 4px solid ' + escapeHtml(border) + '; border-radius: 0 8px 8px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); font-style: italic; color: ' + escapeHtml(textCol) + ';"><div class="rte-quote-badge" contenteditable="false" style="position: absolute; top: -15px; left: 20px; width: 32px; height: 32px; background-color: ' + escapeHtml(badgeCol) + '; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: sans-serif; font-size: 20px; font-weight: bold; font-style: normal; box-shadow: 0 2px 4px rgba(0,0,0,0.2); user-select: none;">“</div><p style="margin: 0; line-height: 1.6;">' + escapeHtml(qtext) + '</p></blockquote>';
                    }
                    self._insertHTML(html);
                }
            },
        });
    };

    RichTextEditor.prototype._dialogLink = function (existingBtn) {
        var self = this;
        var sel = window.getSelection();

        // Auto-detect existing link/button if not explicitly passed
        if (!existingBtn && sel && sel.rangeCount > 0) {
            var node = sel.anchorNode;
            while (node && node !== this.content) {
                if (node.tagName === 'A') {
                    existingBtn = node;
                    break;
                }
                node = node.parentNode;
            }
        }

        function rgbToHex(rgb) {
            if (!rgb) return '#000000';
            if (rgb.indexOf('#') === 0) return rgb;
            var match = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);
            if (!match) return '#000000';
            function hex(x) { return ("0" + parseInt(x).toString(16)).slice(-2); }
            return "#" + hex(match[1]) + hex(match[2]) + hex(match[3]);
        }

        var isEdit = !!existingBtn;
        var selectedText = isEdit ? (existingBtn.textContent || '') : (sel && sel.toString() ? sel.toString() : '');
        var rawHref = isEdit ? (existingBtn.getAttribute('href') || '') : 'https://';
        var isNewTab = isEdit ? (existingBtn.getAttribute('target') === '_blank') : true;

        // Detect existing in-page anchor link
        var existingIsAnchor = isEdit && rawHref.indexOf('#') === 0;

        var isBtn = isEdit ? existingBtn.classList.contains('rte-btn-link') : true;
        var bgVal = isEdit ? rgbToHex(existingBtn.style.backgroundColor || '#0d6efd') : '#0d6efd';
        var textcolorVal = isEdit ? rgbToHex(existingBtn.style.color || '#ffffff') : '#ffffff';

        var typeSelect = el('select', { class: 'rte-form-input', name: 'linktype' }, [
            el('option', { value: 'button', text: 'Button (Tombol)', selected: isBtn }),
            el('option', { value: 'text', text: 'Text (Teks Link)', selected: !isBtn }),
        ]);
        var colorRow = el('div', { class: 'rte-form-group', style: 'margin-top:10px; display: ' + (isBtn ? 'block' : 'none') + ';' }, [
            el('label', { class: 'rte-form-label', text: 'Warna Latar Button' }),
            el('input', { type: 'color', class: 'rte-form-input', name: 'bgcolor', value: bgVal, style: 'height:38px; padding:2px;' }),
            el('label', { class: 'rte-form-label', style: 'margin-top:10px;', text: 'Warna Teks Button' }),
            el('input', { type: 'color', class: 'rte-form-input', name: 'textcolor', value: textcolorVal, style: 'height:38px; padding:2px;' }),
        ]);
        typeSelect.addEventListener('change', function () {
            if (typeSelect.value === 'button') {
                colorRow.style.display = 'block';
            } else {
                colorRow.style.display = 'none';
            }
        });

        // Target type: external URL or in-page anchor
        var targetTypeSelect = el('select', { class: 'rte-form-input', name: 'targettype' }, [
            el('option', { value: 'external', text: 'Halaman lain (URL external)', selected: !existingIsAnchor }),
            el('option', { value: 'anchor', text: 'Halaman ini (anchor / posisi tertentu)', selected: existingIsAnchor }),
        ]);
        targetTypeSelect.addEventListener('change', function () {
            if (targetTypeSelect.value === 'anchor') {
                urlRow.style.display = 'none';
                anchorRow.style.display = 'block';
            } else {
                urlRow.style.display = 'block';
                anchorRow.style.display = 'none';
            }
        });

        var urlInput = el('input', {
            type: 'url', class: 'rte-form-input', name: 'url',
            placeholder: 'https://example.com',
            value: existingIsAnchor ? 'https://' : rawHref,
        });
        var urlRow = el('div', { class: 'rte-form-group' }, [
            el('label', { class: 'rte-form-label', style: 'margin-top:10px;', text: 'URL' }),
            urlInput,
        ]);

        var anchorInput = el('input', {
            type: 'text', class: 'rte-form-input', name: 'anchorid',
            placeholder: 'Pilih atau ketik anchor id',
            value: existingIsAnchor ? rawHref.replace(/^#/, '') : '',
        });
        var pickAnchorBtn = el('button', {
            type: 'button', class: 'rte-btn rte-btn-secondary rte-pick-anchor-btn',
            text: 'Pilih Target di Halaman Ini',
        });
        pickAnchorBtn.addEventListener('click', function () {
            self._dialogAnchorPicker(function (anchorId) {
                if (anchorId) {
                    anchorInput.value = anchorId;
                    var txt = body.querySelector('[name=text]');
                    if (txt && !txt.value) txt.value = anchorId.replace(/-/g, ' ');
                }
            });
        });
        var anchorRow = el('div', { class: 'rte-form-group', style: 'display: ' + (existingIsAnchor ? 'block' : 'none') + ';' }, [
            el('label', { class: 'rte-form-label', style: 'margin-top:10px;', text: 'Target di Halaman Ini' }),
            anchorInput,
            el('div', { style: 'margin-top:6px;' }, [pickAnchorBtn]),
        ]);

        var body = el('div', { class: 'rte-form' }, [
            el('label', { class: 'rte-form-label', text: 'Tipe Link' }),
            typeSelect,
            colorRow,
            el('label', { class: 'rte-form-label', style: 'margin-top:10px;', text: 'Arahkan Link Ke' }),
            targetTypeSelect,
            urlRow,
            anchorRow,
            el('label', { class: 'rte-form-label', text: 'Text to display' }),
            el('input', { type: 'text', class: 'rte-form-input', name: 'text', value: selectedText }),
            el('label', { class: 'rte-form-row' }, [
                el('input', { type: 'checkbox', name: 'newtab', checked: isNewTab }),
                el('span', { text: ' Open in new tab' }),
            ]),
        ]);
        openModal({
            title: 'Insert link',
            body: body,
            confirmLabel: isEdit ? 'Save' : 'Insert',
            onConfirm: function () {
                var linktype = body.querySelector('[name=linktype]').value;
                var targettype = body.querySelector('[name=targettype]').value;
                var text = body.querySelector('[name=text]').value;
                var newtab = body.querySelector('[name=newtab]').checked;
                var bgcolor = body.querySelector('[name=bgcolor]').value;
                var textcolor = body.querySelector('[name=textcolor]').value;

                var url;
                if (targettype === 'anchor') {
                    var anchorVal = body.querySelector('[name=anchorid]').value.trim();
                    if (!anchorVal) return false;
                    url = '#' + anchorVal.replace(/^#/, '');
                } else {
                    url = body.querySelector('[name=url]').value.trim();
                    if (!url) return false;
                }
                var safeText = text || url;

                if (isEdit) {
                    existingBtn.setAttribute('href', url);
                    existingBtn.textContent = safeText;
                    if (linktype === 'button') {
                        existingBtn.className = 'rte-btn-link';
                        existingBtn.style.display = 'inline-flex';
                        existingBtn.style.alignItems = 'center';
                        existingBtn.style.justifyContent = 'center';
                        existingBtn.style.backgroundColor = bgcolor;
                        existingBtn.style.color = textcolor;
                        existingBtn.style.padding = '10px 24px';
                        existingBtn.style.borderRadius = '8px';
                        existingBtn.style.textDecoration = 'none';
                        existingBtn.style.fontWeight = '600';
                        existingBtn.style.textAlign = 'center';
                        existingBtn.style.margin = '8px 0';
                        existingBtn.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
                        existingBtn.style.boxSizing = 'border-box';
                    } else {
                        existingBtn.className = '';
                        existingBtn.style.cssText = '';
                    }
                    if (newtab) {
                        existingBtn.setAttribute('target', '_blank');
                        existingBtn.setAttribute('rel', 'noopener noreferrer');
                    } else {
                        existingBtn.removeAttribute('target');
                        existingBtn.removeAttribute('rel');
                    }
                    if (targettype === 'anchor') {
                        existingBtn.setAttribute('data-rte-anchor-link', '1');
                    } else {
                        existingBtn.removeAttribute('data-rte-anchor-link');
                    }
                    self._syncSource();
                    setTimeout(function () { self._updatePopupPositions(); }, 10);
                } else {
                    var html;
                    if (linktype === 'button') {
                        html = '<a href="' + escapeHtml(url) + '" class="rte-btn-link"' +
                            (newtab ? ' target="_blank" rel="noopener noreferrer"' : '') +
                            (targettype === 'anchor' ? ' data-rte-anchor-link="1"' : '') +
                            ' style="display: inline-flex; align-items: center; justify-content: center; background-color: ' + escapeHtml(bgcolor) + '; color: ' + escapeHtml(textcolor) + '; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center; margin: 8px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); box-sizing: border-box;">' + escapeHtml(safeText) + '</a>';
                    } else {
                        html = '<a href="' + escapeHtml(url) + '"' +
                            (newtab ? ' target="_blank" rel="noopener noreferrer"' : '') +
                            (targettype === 'anchor' ? ' data-rte-anchor-link="1"' : '') +
                            '>' + escapeHtml(safeText) + '</a>';
                    }
                    self._insertHTML(html);
                }
            },
        });
    };

    // ---------------------------------------------------------------------
    // Table of Contents (Daftar Isi) popup
    // ---------------------------------------------------------------------
    // Two-column popup. Left: TOC list (editable). Right: live preview of
    // the page, click any heading paragraph to insert it as a TOC entry.
    RichTextEditor.prototype._dialogToc = function (existingNode) {
        var self = this;

        function slugify(text) {
            var s = (text || '').toString().toLowerCase();
            s = s.replace(/[^a-z0-9\s\-_]/g, '');
            s = s.replace(/[\s_]+/g, '-');
            s = s.replace(/-+/g, '-').replace(/^-+|-+$/g, '');
            return s || 'section';
        }

        // Collect headings (h1..h6, p that look like titles) from the editor
        function collectHeadings() {
            self._assignAnchorIds();
            var root = self.content;
            if (!root) return [];
            var list = [];
            // Walk top-level children only, so structure is predictable
            var candidates = root.querySelectorAll('h1, h2, h3, h4, h5, h6, p, blockquote, li, div, td, th');
            for (var i = 0; i < candidates.length; i++) {
                var el2 = candidates[i];
                // Skip if inside TOC block
                if (el2.closest && el2.closest('.rte-toc-block')) continue;
                if (el2.querySelector('h1, h2, h3, h4, h5, h6, p, blockquote, li')) continue;
                var text = (el2.textContent || '').trim();
                if (!text) continue;
                // Skip very short / empty headings
                if (text.length < 2) continue;
                // Read the pre-assigned anchor ID or fallback
                var id = el2.getAttribute('data-rte-anchor') || el2.id || (slugify(text) + '-' + (i + 1));
                list.push({ node: el2, text: text, id: id, tag: el2.tagName.toLowerCase() });
            }
            return list;
        }

        // Build initial entry list
        var existingEntries = [];
        if (existingNode) {
            var items = existingNode.querySelectorAll('.rte-toc-item');
            for (var j = 0; j < items.length; j++) {
                var a = items[j].querySelector('a');
                if (a) {
                    existingEntries.push({
                        text: a.textContent || '',
                        target: a.getAttribute('data-rte-target') || a.getAttribute('href') || '',
                        side: a.getAttribute('data-rte-toc-side') || 'left',
                    });
                }
            }
        }

        // ----- Layout: side selector (left/right) -----
        var sideSelect = el('select', { class: 'rte-form-input', name: 'tocside' });
        [
            ['top', 'Penuh di Atas (Full Width)'],
            ['left', 'Daftar isi di kiri (konten di kanan)'],
            ['right', 'Daftar isi di kanan (konten di kiri)']
        ].forEach(function (s) {
            var opt = el('option', { value: s[0], text: s[1] });
            sideSelect.appendChild(opt);
        });
        if (existingEntries.length) sideSelect.value = existingEntries[0].side || 'top';
        else sideSelect.value = 'top';

        // ----- Title field -----
        var titleInput = el('input', {
            type: 'text',
            class: 'rte-form-input',
            name: 'toctitle',
            value: (existingNode ? (existingNode.getAttribute('data-rte-toc-title') || 'Daftar Isi') : 'Daftar Isi'),
            placeholder: 'Judul daftar isi',
        });

        // ----- Left column: editable TOC list -----
        var entriesList = el('div', { class: 'rte-toc-entries' });
        function addEntryRow(data) {
            data = data || { text: '', target: '', side: sideSelect.value };
            var row = el('div', { class: 'rte-toc-entry-row' });
            var numSpan = el('span', { class: 'rte-toc-entry-num', text: (entriesList.children.length + 1) + '.' });
            var textInput = el('input', {
                type: 'text', class: 'rte-form-input rte-toc-entry-text',
                name: 'toctext', value: data.text, placeholder: 'Teks daftar isi',
            });
            var targetInput = el('input', {
                type: 'text', class: 'rte-form-input rte-toc-entry-target',
                name: 'toctarget', value: data.target, placeholder: 'mis. #section-1',
                readonly: 'readonly',
            });
            var pickBtn = el('button', {
                type: 'button', class: 'rte-btn rte-btn-secondary rte-toc-pick-btn',
                text: 'Pilih',
            });
            var upBtn = el('button', { type: 'button', class: 'rte-btn rte-btn-secondary rte-toc-up-btn', html: '&uarr;' });
            var downBtn = el('button', { type: 'button', class: 'rte-btn rte-btn-secondary rte-toc-down-btn', html: '&darr;' });
            var delBtn = el('button', { type: 'button', class: 'rte-btn rte-btn-secondary rte-toc-del-btn', html: '&times;' });

            pickBtn.addEventListener('click', function () {
                self._dialogAnchorPicker(function (anchorId) {
                    if (anchorId) {
                        targetInput.value = '#' + anchorId;
                        if (!textInput.value) textInput.value = anchorId.replace(/-/g, ' ');
                    }
                });
            });
            upBtn.addEventListener('click', function () {
                if (row.previousElementSibling) entriesList.insertBefore(row, row.previousElementSibling);
                renumberEntries();
            });
            downBtn.addEventListener('click', function () {
                if (row.nextElementSibling) entriesList.insertBefore(row.nextElementSibling, row);
                renumberEntries();
            });
            delBtn.addEventListener('click', function () {
                row.parentNode && row.parentNode.removeChild(row);
                renumberEntries();
            });

            var btnWrapper = el('div', { class: 'rte-toc-entry-row-buttons' }, [pickBtn, upBtn, downBtn, delBtn]);
            row.appendChild(numSpan);
            row.appendChild(textInput);
            row.appendChild(targetInput);
            row.appendChild(btnWrapper);
            entriesList.appendChild(row);
        }
        function renumberEntries() {
            for (var k = 0; k < entriesList.children.length; k++) {
                entriesList.children[k].querySelector('.rte-toc-entry-num').textContent = (k + 1) + '.';
            }
        }
        // Seed entries
        if (existingEntries.length) {
            existingEntries.forEach(function (e) { addEntryRow(e); });
        }

        var addBtn = el('button', {
            type: 'button', class: 'rte-btn rte-btn-secondary rte-toc-add-btn', text: '+ Tambah Entry',
        });
        addBtn.addEventListener('click', function () { addEntryRow(); renumberEntries(); });

        var autoBtn = el('button', {
            type: 'button', class: 'rte-btn rte-btn-secondary rte-toc-auto-btn', text: 'Auto dari Heading',
        });
        autoBtn.addEventListener('click', function () {
            var heads = collectHeadings();
            // Clear current
            while (entriesList.firstChild) entriesList.removeChild(entriesList.firstChild);
            heads.forEach(function (h) {
                addEntryRow({ text: h.text, target: '#' + h.id, side: sideSelect.value });
            });
            renumberEntries();
        });

        var leftPane = el('div', { class: 'rte-toc-pane rte-toc-pane-left' }, [
            el('label', { class: 'rte-form-label', text: 'Judul' }),
            titleInput,
            el('label', { class: 'rte-form-label', text: 'Posisi Daftar Isi' }),
            sideSelect,
            el('label', { class: 'rte-form-label', text: 'Entries' }),
            entriesList,
            el('div', { class: 'rte-toc-actions' }, [addBtn, autoBtn]),
        ]);

        // ----- Right column: live preview of headings -----
        var previewList = el('div', { class: 'rte-toc-preview' });
        function rebuildPreview() {
            while (previewList.firstChild) previewList.removeChild(previewList.firstChild);
            var heads = collectHeadings();
            if (!heads.length) {
                previewList.appendChild(el('div', { class: 'rte-toc-preview-empty', text: 'Belum ada heading di konten. Tambahkan entry secara manual di sebelah kiri.' }));
                return;
            }
            heads.forEach(function (h) {
                var row = el('div', { class: 'rte-toc-preview-row' });
                row.appendChild(el('span', { class: 'rte-toc-preview-tag', text: h.tag.toUpperCase() }));
                row.appendChild(el('span', { class: 'rte-toc-preview-text', text: h.text }));
                row.appendChild(el('span', { class: 'rte-toc-preview-id', text: '#' + h.id }));
                row.title = 'Klik untuk menambah ke daftar isi';
                row.addEventListener('click', function () {
                    addEntryRow({ text: h.text, target: '#' + h.id, side: sideSelect.value });
                    renumberEntries();
                });
                previewList.appendChild(row);
            });
        }
        rebuildPreview();

        var refreshPreviewBtn = el('button', {
            type: 'button', class: 'rte-btn rte-btn-secondary',
            text: 'Refresh preview',
        });
        refreshPreviewBtn.addEventListener('click', rebuildPreview);

        var rightPane = el('div', { class: 'rte-toc-pane rte-toc-pane-right' }, [
            el('div', { class: 'rte-toc-preview-header' }, [
                el('label', { class: 'rte-form-label', text: 'Preview Konten (klik heading untuk menambah)' }),
                refreshPreviewBtn,
            ]),
            previewList,
        ]);

        var body = el('div', { class: 'rte-form rte-toc-form' }, [leftPane, rightPane]);

        openModal({
            title: existingNode ? 'Edit Daftar Isi' : 'Buat Daftar Isi',
            body: body,
            wide: true,
            confirmLabel: existingNode ? 'Save' : 'Insert',
            onConfirm: function () {
                var title = titleInput.value.trim() || 'Daftar Isi';
                var side = sideSelect.value || 'left';
                var rows = entriesList.querySelectorAll('.rte-toc-entry-row');
                if (!rows.length) return false;
                var items = [];
                for (var k = 0; k < rows.length; k++) {
                    var t = rows[k].querySelector('.rte-toc-entry-text').value.trim();
                    var tg = rows[k].querySelector('.rte-toc-entry-target').value.trim();
                    if (!t) continue;
                    items.push({ text: t, target: tg });
                }
                if (!items.length) return false;

                // Build HTML
                var sideStyle = (side === 'right' || side === 'left' || side === 'top') ? side : 'top';
                var listHtml = items.map(function (it, i) {
                    var anchor = (it.target || '').replace(/^#/, '');
                    var targetAttr = anchor ? ' data-rte-target="#' + escapeHtml(anchor) + '"' : '';
                    return '<li class="rte-toc-item" data-rte-toc-index="' + (i + 1) + '">' +
                        '<a href="#' + escapeHtml(anchor) + '"' + targetAttr +
                        ' data-rte-toc-side="' + sideStyle + '">' + escapeHtml(it.text) + '</a>' +
                        '</li>';
                }).join('');

                var html = '<div class="rte-toc-block" data-rte-toc="1" data-rte-toc-title="' + escapeHtml(title) + '" data-rte-toc-side="' + sideStyle + '" contenteditable="false">' +
                    '<div class="rte-toc-block-title">' + escapeHtml(title) + '</div>' +
                    '<ul class="rte-toc-list">' + listHtml + '</ul>' +
                    '</div>';

                if (existingNode) {
                    existingNode.outerHTML = html;
                } else {
                    self._insertHTML(html);
                }
                self._assignAnchorIds();
                self._syncSource();
            },
        });
    };

    // ---------------------------------------------------------------------
    // Anchor picker popup — used by TOC entries and the link dialog
    // ---------------------------------------------------------------------
    RichTextEditor.prototype._dialogAnchorPicker = function (cb) {
        var self = this;
        self._assignAnchorIds();
        var anchors = [];
        var root = self.content;
        if (root) {
            var all = root.querySelectorAll('[data-rte-anchor]');
            for (var i = 0; i < all.length; i++) {
                var t = (all[i].textContent || '').trim();
                if (!t) continue;
                if (all[i].closest && all[i].closest('.rte-toc-block')) continue;
                anchors.push({ id: all[i].getAttribute('data-rte-anchor'), text: t });
            }
        }
        var list = el('div', { class: 'rte-anchor-list' });
        if (!anchors.length) {
            list.appendChild(el('div', { class: 'rte-anchor-empty', text: 'Belum ada konten dengan anchor. Tambahkan heading pada konten terlebih dahulu.' }));
        } else {
            anchors.forEach(function (a) {
                var row = el('div', { class: 'rte-anchor-row' });
                row.appendChild(el('span', { class: 'rte-anchor-id', text: '#' + a.id }));
                row.appendChild(el('span', { class: 'rte-anchor-text', text: a.text.length > 80 ? a.text.slice(0, 80) + '…' : a.text }));
                row.addEventListener('click', function () {
                    if (typeof cb === 'function') cb(a.id);
                    close();
                });
                list.appendChild(row);
            });
        }
        var body = el('div', { class: 'rte-form' }, [list]);
        // openModal returns nothing here but we need a way to close from row click — track backdrop manually
        // Reuse openModal pattern: emulate by creating backdrop directly so we can close from inside
        var backdrop = el('div', { class: 'rte-modal-backdrop' });
        var dialog = el('div', { class: 'rte-modal' });
        var header = el('div', { class: 'rte-modal-header' }, [
            el('div', { class: 'rte-modal-drag-handle', html: '<svg width="16" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg"><line x1="1" y1="1" x2="15" y2="1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><line x1="1" y1="5" x2="15" y2="5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><line x1="1" y1="9" x2="15" y2="9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>' }),
            el('div', { class: 'rte-modal-title', text: 'Pilih Target di Halaman Ini' }),
            el('button', { type: 'button', class: 'rte-modal-close', html: '&times;', onclick: close }),
        ]);
        var bodyWrap = el('div', { class: 'rte-modal-body' }, [body]);
        var footer = el('div', { class: 'rte-modal-footer' }, [
            el('button', { type: 'button', class: 'rte-btn rte-btn-secondary', text: 'Batal', onclick: close }),
        ]);
        dialog.appendChild(header);
        dialog.appendChild(bodyWrap);
        dialog.appendChild(footer);
        backdrop.appendChild(dialog);
        backdrop.addEventListener('mousedown', function (e) { if (e.target === backdrop) close(); });
        document.body.appendChild(backdrop);
        // Expose close to row handlers
        function close() { if (backdrop.parentNode) backdrop.parentNode.removeChild(backdrop); }
    };

    // ---------------------------------------------------------------------
    // Assign data-rte-anchor IDs to all candidate top-level content nodes
    // ---------------------------------------------------------------------
    RichTextEditor.prototype._assignAnchorIds = function () {
        var root = this.content;
        if (!root) return;
        function slugify(text) {
            var s = (text || '').toString().toLowerCase();
            s = s.replace(/[^a-z0-9\s\-_]/g, '');
            s = s.replace(/[\s_]+/g, '-');
            s = s.replace(/-+/g, '-').replace(/^-+|-+$/g, '');
            return s || 'section';
        }
        var nodes = root.querySelectorAll('h1, h2, h3, h4, h5, h6, p, blockquote, li, div, td, th');
        var usedIds = {};
        for (var i = 0; i < nodes.length; i++) {
            var n = nodes[i];
            if (n.closest && n.closest('.rte-toc-block')) continue;
            if (n.querySelector('h1, h2, h3, h4, h5, h6, p, blockquote, li')) continue;
            
            var existingAnchor = n.getAttribute('data-rte-anchor');
            var baseId = existingAnchor;
            if (!baseId) {
                var text = (n.textContent || '').trim();
                if (!text) continue;
                baseId = slugify(text);
            }
            
            var finalId = baseId;
            var counter = 1;
            while (usedIds[finalId]) {
                finalId = baseId + '-' + (i + 1) + '-' + counter;
                counter++;
            }
            usedIds[finalId] = true;
            
            n.setAttribute('data-rte-anchor', finalId);
            n.setAttribute('id', finalId);
        }
    };

    RichTextEditor.prototype._dialogImage = function () {
        var self = this;
        var tabs = el('div', { class: 'rte-tabs' });
        var tabUpload = el('button', { type: 'button', class: 'rte-tab rte-tab-active', text: 'Upload' });
        var tabUrl = el('button', { type: 'button', class: 'rte-tab', text: 'URL' });
        tabs.appendChild(tabUpload);
        tabs.appendChild(tabUrl);

        // Alignment selector
        var alignSelect = el('select', { class: 'rte-form-input', name: 'align' });
        [['left', 'Kiri'], ['center', 'Tengah'], ['right', 'Kanan']].forEach(function (a) {
            var opt = el('option', { value: a[0], text: a[1] });
            alignSelect.appendChild(opt);
        });

        var paneUpload = el('div', { class: 'rte-form rte-tab-pane rte-tab-pane-active' }, [
            el('label', { class: 'rte-form-label', text: 'Choose image' }),
            el('input', { type: 'file', class: 'rte-form-input', name: 'file', accept: 'image/*' }),
            el('label', { class: 'rte-form-label', text: 'Alt text' }),
            el('input', { type: 'text', class: 'rte-form-input', name: 'alt' }),
            el('label', { class: 'rte-form-label', text: 'Width (px, optional)' }),
            el('input', { type: 'number', class: 'rte-form-input', name: 'width', min: '0' }),
            el('label', { class: 'rte-form-label', text: 'Posisi' }),
            alignSelect,
        ]);
        var paneUrl = el('div', { class: 'rte-form rte-tab-pane' }, [
            el('label', { class: 'rte-form-label', text: 'Image URL' }),
            el('input', { type: 'url', class: 'rte-form-input', name: 'url', placeholder: 'https://...' }),
            el('label', { class: 'rte-form-label', text: 'Alt text' }),
            el('input', { type: 'text', class: 'rte-form-input', name: 'alt' }),
            el('label', { class: 'rte-form-label', text: 'Width (px, optional)' }),
            el('input', { type: 'number', class: 'rte-form-input', name: 'width', min: '0' }),
            el('label', { class: 'rte-form-label', text: 'Posisi' }),
            alignSelect,
        ]);

        tabUpload.addEventListener('click', function () {
            tabUpload.classList.add('rte-tab-active');
            tabUrl.classList.remove('rte-tab-active');
            paneUpload.classList.add('rte-tab-pane-active');
            paneUrl.classList.remove('rte-tab-pane-active');
        });
        tabUrl.addEventListener('click', function () {
            tabUrl.classList.add('rte-tab-active');
            tabUpload.classList.remove('rte-tab-active');
            paneUrl.classList.add('rte-tab-pane-active');
            paneUpload.classList.remove('rte-tab-pane-active');
        });

        var body = el('div', null, [tabs, paneUpload, paneUrl]);
        openModal({
            title: 'Insert image',
            body: body,
            confirmLabel: 'Insert',
            onConfirm: function () {
                var alignVal = alignSelect.value || 'left';
                var widthAttr = function (input) {
                    var v = parseInt(input.value, 10);
                    return v > 0 ? ' width="' + v + '"' : '';
                };
                var wrapImg = function (img) {
                    var wrapper = document.createElement('div');
                    wrapper.style.textAlign = alignVal;
                    wrapper.style.display = alignVal === 'center' ? 'block' : 'inline-' + alignVal;
                    wrapper.style.margin = alignVal === 'center' ? '0 auto' : (alignVal === 'right' ? '0 0 0 auto' : '0');
                    img.style.maxWidth = '100%';
                    img.style.height = 'auto';
                    wrapper.appendChild(img);
                    return wrapper;
                };
                if (paneUpload.classList.contains('rte-tab-pane-active')) {
                    var fileInput = paneUpload.querySelector('[name=file]');
                    var altInput = paneUpload.querySelector('[name=alt]');
                    var widthInput = paneUpload.querySelector('[name=width]');
                    var f = fileInput.files && fileInput.files[0];
                    if (!f) return false;
                    self._uploadImage(f, function (url) {
                        self._focusContent();
                        var savedRange = self._savedRange;
                        var img = document.createElement('img');
                        img.src = url;
                        if (altInput.value) img.alt = altInput.value;
                        var wv = parseInt(widthInput.value, 10);
                        if (wv > 0) img.width = wv;
                        var wrapper = wrapImg(img);
                        if (savedRange) {
                            var sel = window.getSelection();
                            sel.removeAllRanges();
                            sel.addRange(savedRange);
                            var range = sel.getRangeAt(0);
                            range.deleteContents();
                            range.insertNode(wrapper);
                            range.setStartAfter(wrapper);
                            range.collapse(true);
                            sel.removeAllRanges();
                            sel.addRange(range);
                        } else {
                            self.content.appendChild(wrapper);
                        }
                        self._syncSource();
                        self._updateState();
                    });
                } else {
                    var urlInput = paneUrl.querySelector('[name=url]');
                    var altInput2 = paneUrl.querySelector('[name=alt]');
                    var widthInput2 = paneUrl.querySelector('[name=width]');
                    var url = urlInput.value.trim();
                    if (!url) return false;
                    if (url.indexOf('drive.google.com') !== -1) {
                        url = convertGoogleDriveUrl(url, 'image');
                    }
                    var imgHtml = '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(altInput2.value || '') + '"' + widthAttr(widthInput2) + ' style="max-width:100%;height:auto;">';
                    var wrapperHtml = '<div style="text-align:' + alignVal + ';' + (alignVal === 'center' ? 'display:block;' : 'display:inline-' + alignVal + ';') + (alignVal === 'center' ? 'margin:0 auto;' : (alignVal === 'right' ? 'margin:0 0 0 auto;' : 'margin:0;')) + '">' + imgHtml + '</div>';
                    self._insertHTML(wrapperHtml);
                }
            },
        });
    };

    RichTextEditor.prototype._uploadImage = function (file, cb, errCb) {
        var self = this;
        self.showLoading();
        var handler = this.config.file_upload_handler;
        if (typeof handler === 'function') {
            handler(file, function (url) { self.hideLoading(); try { cb(url); } catch (e) { console.error("Error in upload callback:", e); } }, function (err) { self.hideLoading(); if (errCb) errCb(err); });
        } else {
            // Fallback: embed as base64 data URL.
            var reader = new FileReader();
            reader.onload = function () { self.hideLoading(); try { cb(reader.result); } catch (e) { console.error("Error in upload callback:", e); } };
            reader.onerror = function (err) { self.hideLoading(); if (errCb) errCb(err); };
            reader.readAsDataURL(file);
        }
    };

    RichTextEditor.prototype._dialogVideo = function () {
        var self = this;
        var tabs = el('div', { class: 'rte-tabs' });
        var tabUpload = el('button', { type: 'button', class: 'rte-tab rte-tab-active', text: 'Upload File' });
        var tabUrl = el('button', { type: 'button', class: 'rte-tab', text: 'URL Video' });
        tabs.appendChild(tabUpload);
        tabs.appendChild(tabUrl);

        var paneUpload = el('div', { class: 'rte-form rte-tab-pane rte-tab-pane-active' }, [
            el('label', { class: 'rte-form-label', text: 'Choose video file (MP4, WebM, OGG)' }),
            el('input', { type: 'file', class: 'rte-form-input', name: 'file', accept: 'video/mp4,video/webm,video/ogg,video/*' }),
            el('label', { class: 'rte-form-label', text: 'Width (px)' }),
            el('input', { type: 'number', class: 'rte-form-input', name: 'w', value: '560', min: '0' }),
            el('label', { class: 'rte-form-label', text: 'Height (px)' }),
            el('input', { type: 'number', class: 'rte-form-input', name: 'h', value: '315', min: '0' }),
        ]);
        var paneUrl = el('div', { class: 'rte-form rte-tab-pane' }, [
            el('label', { class: 'rte-form-label', text: 'Video URL (YouTube, Vimeo, atau .mp4)' }),
            el('input', { type: 'url', class: 'rte-form-input', name: 'url', placeholder: 'https://www.youtube.com/watch?v=...' }),
            el('label', { class: 'rte-form-label', text: 'Width (px)' }),
            el('input', { type: 'number', class: 'rte-form-input', name: 'w', value: '560', min: '0' }),
            el('label', { class: 'rte-form-label', text: 'Height (px)' }),
            el('input', { type: 'number', class: 'rte-form-input', name: 'h', value: '315', min: '0' }),
        ]);

        tabUpload.addEventListener('click', function () {
            tabUpload.classList.add('rte-tab-active');
            tabUrl.classList.remove('rte-tab-active');
            paneUpload.classList.add('rte-tab-pane-active');
            paneUrl.classList.remove('rte-tab-pane-active');
        });
        tabUrl.addEventListener('click', function () {
            tabUrl.classList.add('rte-tab-active');
            tabUpload.classList.remove('rte-tab-active');
            paneUrl.classList.add('rte-tab-pane-active');
            paneUpload.classList.remove('rte-tab-pane-active');
        });

        var body = el('div', null, [tabs, paneUpload, paneUrl]);
        var uploadedUrl = null;

        openModal({
            title: 'Insert video',
            body: body,
            confirmLabel: 'Insert',
            onConfirm: function () {
                var w = (paneUpload.classList.contains('rte-tab-pane-active') ?
                    paneUpload.querySelector('[name=w]') : paneUrl.querySelector('[name=w]')).value || '560';
                var h = (paneUpload.classList.contains('rte-tab-pane-active') ?
                    paneUpload.querySelector('[name=h]') : paneUrl.querySelector('[name=h]')).value || '315';

                if (paneUpload.classList.contains('rte-tab-pane-active')) {
                    var fileInput = paneUpload.querySelector('[name=file]');
                    var f = fileInput.files && fileInput.files[0];
                    if (!f) return false;
                    // Upload the file then insert the video tag
                    self._uploadVideo(f, function (url) {
                        var html = '<video controls width="' + w + '" height="' + h + '"><source src="' + escapeHtml(url) + '"></video>';
                        self._insertHTML(html);
                    });
                    return; // close called after async — keep open via return false
                } else {
                    var url = paneUrl.querySelector('[name=url]').value.trim();
                    if (!url) return false;
                    var html = self._buildVideoEmbed(url, w, h);
                    self._insertHTML(html);
                }
            },
        });
    };

    RichTextEditor.prototype._uploadVideo = function (file, cb, errCb) {
        var self = this;
        self.showLoading();
        var handler = this.config.file_upload_handler;
        if (typeof handler === 'function') {
            handler(file, function (url) { self.hideLoading(); try { cb(url); } catch (e) { console.error("Error in upload callback:", e); } }, function (err) { self.hideLoading(); if (errCb) errCb(err); });
        } else {
            // Fallback: embed as base64 data URL.
            var reader = new FileReader();
            reader.onload = function () { self.hideLoading(); try { cb(reader.result); } catch (e) { console.error("Error in upload callback:", e); } };
            reader.onerror = function (err) { self.hideLoading(); if (errCb) errCb(err); };
            reader.readAsDataURL(file);
        }
    };

    RichTextEditor.prototype._buildVideoEmbed = function (url, w, h) {
        // Convert Google Drive share/view links to embeddable preview
        if (url.indexOf('drive.google.com') !== -1) {
            url = convertGoogleDriveUrl(url, 'video');
        }
        var yt = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]+)/);
        if (yt) {
            return '<iframe width="' + w + '" height="' + h + '" src="https://www.youtube.com/embed/' + yt[1] +
                '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        }
        var vm = url.match(/vimeo\.com\/(\d+)/);
        if (vm) {
            return '<iframe width="' + w + '" height="' + h + '" src="https://player.vimeo.com/video/' + vm[1] +
                '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        if (/\.(mp4|webm|ogg)(\?.*)?$/i.test(url)) {
            return '<video controls width="' + w + '" height="' + h + '"><source src="' + escapeHtml(url) + '"></video>';
        }
        // Generic iframe (includes Google Drive /preview)
        return '<iframe width="' + w + '" height="' + h + '" src="' + escapeHtml(url) + '" frameborder="0" allowfullscreen></iframe>';
    };

    RichTextEditor.prototype._dialogCarousel = function (existingCarousel) {
        var self = this;
        var mediaList = [];

        // If editing, extract items from DOM
        if (existingCarousel) {
            var slides = existingCarousel.querySelectorAll('.rte-carousel-slide');
            slides.forEach(function (slide) {
                var img = slide.querySelector('img');
                var vid = slide.querySelector('video');
                var cap = slide.querySelector('.rte-carousel-caption-popup div') || slide.querySelector('.rte-carousel-caption');
                var type = vid ? 'video' : 'image';
                var url = vid ? vid.src : (img ? img.src : '');
                if (url) {
                    mediaList.push({
                        url: url,
                        type: type,
                        caption: cap ? cap.textContent : '',
                        id: 'm' + Math.random().toString(36).substr(2, 9)
                    });
                }
            });
        }

        var body = el('div', { class: 'rte-form' }, [
            el('div', { class: 'rte-carousel-dialog-header', style: 'margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;' }, [
                el('h4', { style: 'margin:0; font-size:14px; color:#555;', text: existingCarousel ? 'Edit Media Carousel' : 'Media Carousel' }),
                el('button', {
                    type: 'button', class: 'rte-btn rte-btn-secondary rte-btn-sm', text: '+ Add Media',
                    onclick: function () {
                        var inp = el('input', { type: 'file', accept: 'image/*,video/*', multiple: true });
                        inp.onchange = function () {
                            if (!inp.files.length) return;
                            Array.from(inp.files).forEach(function (f) {
                                var item = { file: f, caption: '', id: 'm' + Date.now() + Math.random().toString(36).substr(2, 5) };
                                mediaList.push(item);
                                renderItem(item);
                            });
                        };
                        inp.click();
                    }
                })
            ]),
            el('div', { id: 'rte-carousel-items-list', style: 'max-height:300px; overflow-y:auto; border:1px solid #eee; border-radius:8px; padding:10px; background:#fafafa;' }),
            el('div', { style: 'margin-top:15px; border-top:1px solid #eee; padding-top:10px;' }, [
                el('label', { class: 'rte-form-row' }, [
                    el('input', { type: 'checkbox', name: 'autoplay', checked: existingCarousel ? (existingCarousel.getAttribute('data-autoplay') !== 'false') : true }),
                    el('span', { text: ' Autoplay' }),
                ]),
                el('label', { class: 'rte-form-row', style: 'margin-top:8px;' }, [
                    el('span', { text: 'Interval (ms): ', style: 'font-size:12px; color:#666;' }),
                    el('input', { type: 'number', name: 'interval', value: existingCarousel ? (existingCarousel.getAttribute('data-interval') || '3000') : '3000', step: '500', min: '500', style: 'width:80px; padding:2px 5px; border:1px solid #ddd; border-radius:4px;' }),
                ])
            ])
        ]);

        var listEl = body.querySelector('#rte-carousel-items-list');

        function moveItem(item, delta) {
            var idx = mediaList.indexOf(item);
            var newIdx = idx + delta;
            if (newIdx < 0 || newIdx >= mediaList.length) return;

            // Swap in array
            var temp = mediaList[idx];
            mediaList[idx] = mediaList[newIdx];
            mediaList[newIdx] = temp;

            var row = body.querySelector('#' + item.id);
            if (delta === -1) {
                listEl.insertBefore(row, row.previousSibling);
            } else {
                listEl.insertBefore(row.nextSibling, row);
            }
        }

        function renderItem(item) {
            var isVideo = item.type === 'video' || (item.file && item.file.type.startsWith('video/'));
            var preview = el('div', { class: 'rte-carousel-item-preview', style: 'width:60px; height:60px; background:#eee; border-radius:4px; overflow:hidden; flex-shrink:0;' });

            if (item.file) {
                var reader = new FileReader();
                reader.onload = function () {
                    if (isVideo) {
                        var v = el('video', { src: reader.result, style: 'width:100%; height:100%; object-fit:cover;' });
                        preview.appendChild(v);
                    } else {
                        var img = el('img', { src: reader.result, style: 'width:100%; height:100%; object-fit:cover;' });
                        preview.appendChild(img);
                    }
                };
                reader.readAsDataURL(item.file);
            } else if (item.url) {
                if (isVideo) {
                    preview.appendChild(el('video', { src: item.url, style: 'width:100%; height:100%; object-fit:cover;' }));
                } else {
                    preview.appendChild(el('img', { src: item.url, style: 'width:100%; height:100%; object-fit:cover;' }));
                }
            }

            var row = el('div', {
                id: item.id,
                style: 'display:flex; gap:10px; margin-bottom:10px; padding:8px; background:white; border:1px solid #e0e0e0; border-radius:6px; align-items:center;'
            }, [
                // Reorder handles
                el('div', { style: 'display:flex; flex-direction:column; gap:2px;' }, [
                    el('button', {
                        type: 'button', style: 'background:none; border:none; cursor:pointer; padding:2px; font-size:12px; color:#999; line-height:1;',
                        html: '▲', title: 'Move Up',
                        onclick: function () { moveItem(item, -1); }
                    }),
                    el('button', {
                        type: 'button', style: 'background:none; border:none; cursor:pointer; padding:2px; font-size:12px; color:#999; line-height:1;',
                        html: '▼', title: 'Move Down',
                        onclick: function () { moveItem(item, 1); }
                    })
                ]),
                preview,
                el('div', { style: 'flex-grow:1;' }, [
                    el('input', {
                        type: 'text', placeholder: 'Enter caption...', value: item.caption,
                        style: 'width:100%; padding:5px 8px; border:1px solid #ddd; border-radius:4px; font-size:12px;',
                        oninput: function (e) { item.caption = e.target.value; }
                    })
                ]),
                el('button', {
                    type: 'button', style: 'color:#d32f2f; background:none; border:none; cursor:pointer; padding:5px;',
                    html: '&times;',
                    onclick: function () {
                        mediaList = mediaList.filter(function (m) { return m !== item; });
                        row.parentNode.removeChild(row);
                    }
                })
            ]);
            listEl.appendChild(row);
        }

        // Initial render for existing items
        mediaList.forEach(function (m) { renderItem(m); });

        openModal({
            title: existingCarousel ? 'Edit Media Carousel' : 'Insert Media Carousel',
            body: body,
            wide: true,
            confirmLabel: existingCarousel ? 'Save Changes' : 'Insert Carousel',
            onConfirm: function () {
                if (!mediaList.length) return false;
                self.showLoading();
                var autoplay = body.querySelector('[name=autoplay]').checked;
                var interval = body.querySelector('[name=interval]').value || '3000';

                var results = [];
                var itemsProcessed = 0;

                function checkDone() {
                    itemsProcessed++;
                    if (itemsProcessed === mediaList.length) {
                        self.hideLoading();
                        insertFinal(results);
                    }
                }

                function insertFinal(items) {
                    var cid = existingCarousel ? existingCarousel.id : ('carousel_' + Math.random().toString(36).substr(2, 9));
                    var html = '<div class="rte-carousel-container" contenteditable="false" data-autoplay="' + autoplay + '" data-interval="' + interval + '" id="' + cid + '" style="width:100%; aspect-ratio:16/9; margin:1rem auto; display:block;';
                    // Preserve existing style if any (width, alignment, etc)
                    if (existingCarousel) html += existingCarousel.getAttribute('style') || '';
                    html += '">';
                    html += '<div class="rte-carousel-inner">';
                    items.forEach(function (it, idx) {
                        if (!it) return;
                        html += '<div class="rte-carousel-slide' + (idx === 0 ? ' active' : '') + '">';
                        if (it.type === 'video') {
                            html += '<video src="' + escapeHtml(it.url) + '" controls></video>';
                        } else {
                            html += '<img src="' + escapeHtml(it.url) + '" alt="' + escapeHtml(it.caption) + '">';
                        }
                        if (it.caption) {
                            html += '<button class="rte-carousel-caption-btn" type="button" onclick="var p = this.nextElementSibling; p.style.display = \'flex\'; p.style.opacity = \'1\';" title="Lihat Keterangan">?</button>';
                            html += '<div class="rte-carousel-caption-popup" style="display:none; opacity: 0; transition: opacity 0.3s ease; position:absolute; inset:0; background:rgba(0,0,0,0.85); color:#fff; align-items:center; justify-content:center; padding:40px; text-align:center; z-index:15; box-sizing:border-box;">';
                            html += '<button class="rte-carousel-caption-close" type="button" onclick="this.parentElement.style.display = \'none\'; this.parentElement.style.opacity = \'0\'; event.stopPropagation();" style="position:absolute; top:15px; right:15px; background:none; border:none; color:#fff; font-size:28px; cursor:pointer; line-height: 1;">&times;</button>';
                            html += '<div style="font-size:16px; line-height:1.6; max-width:80%; margin: 0 auto; white-space: pre-wrap;">' + escapeHtml(it.caption) + '</div>';
                            html += '</div>';
                        }
                        html += '</div>';
                    });
                    html += '</div>';
                    if (items.length > 1) {
                        html += '<button class="rte-carousel-prev" type="button" onclick="this.closest(\'.rte-carousel-container\')._prev()">&#10094;</button>';
                        html += '<button class="rte-carousel-next" type="button" onclick="this.closest(\'.rte-carousel-container\')._next()">&#10095;</button>';
                        html += '<div class="rte-carousel-dots">';
                        items.forEach(function (_, idx) {
                            html += '<span class="rte-carousel-dot' + (idx === 0 ? ' active' : '') + '" onclick="this.closest(\'.rte-carousel-container\')._goTo(' + idx + ')"></span>';
                        });
                        html += '</div>';
                    }
                    html += '</div><p><br></p>';

                    if (existingCarousel) {
                        // Replace existing
                        var range = document.createRange();
                        range.selectNode(existingCarousel);
                        var frag = range.createContextualFragment(html);
                        existingCarousel.parentNode.replaceChild(frag, existingCarousel);
                        self._syncSource();
                    } else {
                        self._insertHTML(html);
                    }
                }

                mediaList.forEach(function (item, idx) {
                    if (item.file) {
                        var isVideo = item.file.type.startsWith('video/');
                        var uploadFn = isVideo ? self._uploadVideo : self._uploadImage;
                        uploadFn.call(self, item.file, function (url) {
                            results[idx] = { url: url, type: isVideo ? 'video' : 'image', caption: item.caption };
                            checkDone();
                        }, function () { checkDone(); });
                    } else {
                        // Already uploaded
                        results[idx] = { url: item.url, type: item.type, caption: item.caption };
                        checkDone();
                    }
                });
                return true;
            }
        });
    };


    RichTextEditor.prototype._dialogTable = function () {
        var self = this;
        var grid = el('div', { class: 'rte-table-grid' });
        var status = el('div', { class: 'rte-table-status', text: '0 × 0' });
        var rows = 8, cols = 10;
        var cells = [];
        for (var r = 0; r < rows; r++) {
            for (var c = 0; c < cols; c++) {
                (function (r, c) {
                    var cell = el('div', {
                        class: 'rte-table-cell',
                        onmouseenter: function () {
                            cells.forEach(function (cc) {
                                cc.el.classList.toggle('on', cc.r <= r && cc.c <= c);
                            });
                            status.textContent = (r + 1) + ' × ' + (c + 1);
                        },
                        onclick: function () {
                            insertTable(r + 1, c + 1);
                            modal.close();
                        },
                    });
                    cells.push({ el: cell, r: r, c: c });
                    grid.appendChild(cell);
                })(r, c);
            }
        }

        var customRow = el('div', { class: 'rte-form-row' }, [
            el('span', { text: 'Or: ' }),
            el('input', { type: 'number', class: 'rte-form-input rte-form-input-sm', name: 'r', value: '3', min: '1' }),
            el('span', { text: ' × ' }),
            el('input', { type: 'number', class: 'rte-form-input rte-form-input-sm', name: 'c', value: '3', min: '1' }),
            el('button', {
                type: 'button', class: 'rte-btn rte-btn-secondary rte-btn-sm', text: 'Insert',
                onclick: function () {
                    var rr = parseInt(customRow.querySelector('[name=r]').value, 10) || 1;
                    var cc = parseInt(customRow.querySelector('[name=c]').value, 10) || 1;
                    insertTable(rr, cc);
                    modal.close();
                },
            }),
        ]);

        var body = el('div', { class: 'rte-table-builder' }, [grid, status, customRow]);
        var modal = openModal({
            title: 'Insert table',
            body: body,
            confirmLabel: 'Close',
            onConfirm: function () { return; },
        });

        function insertTable(rr, cc) {
            var html = '<table class="rte-table" style="border-collapse:collapse;width:100%"><tbody>';
            for (var i = 0; i < rr; i++) {
                html += '<tr>';
                for (var j = 0; j < cc; j++) {
                    html += '<td style="border:1px solid #ccc;padding:6px;min-width:40px;">&nbsp;</td>';
                }
                html += '</tr>';
            }
            html += '</tbody></table><p><br></p>';
            self._insertHTML(html);
        }
    };

    RichTextEditor.prototype._dialogEmoji = function () {
        var self = this;
        var emojis = ['😀', '😁', '😂', '🤣', '😊', '😍', '😘', '😎', '🤩', '🤔', '🙄', '😴', '😢', '😭', '😡', '🤯', '😱', '🥳', '🤝', '👏', '👍', '👎', '🙏', '💪', '❤️', '💔', '💯', '🔥', '⭐', '✨', '✅', '❌', '⚡', '🎉', '🎁', '🚀', '📌', '📎', '📝', '📷', '🎵', '💡', '⚙️', '🌟', '🌈', '☀️', '🌙', '🌍'];
        var grid = el('div', { class: 'rte-emoji-grid' });
        emojis.forEach(function (em) {
            grid.appendChild(el('button', {
                type: 'button', class: 'rte-emoji-cell', text: em,
                onclick: function () {
                    self._insertHTML(em);
                    modal.close();
                },
            }));
        });
        var modal = openModal({
            title: 'Insert emoji',
            body: grid,
            confirmLabel: 'Close',
            onConfirm: function () { return; },
        });
    };

    // -------- Format Painter --------
    RichTextEditor.prototype._doCopyFormat = function () {
        var sel = window.getSelection();
        var hasActiveSelection = sel && sel.rangeCount > 0 && !sel.isCollapsed && this.content.contains(sel.getRangeAt(0).commonAncestorContainer);
        if (!hasActiveSelection) {
            var rangeToRestore = this._lastNonCollapsedRange || this._savedRange;
            if (rangeToRestore) {
                restoreSelection(rangeToRestore);
            }
            sel = window.getSelection();
        }
        if (!sel || sel.rangeCount === 0 || sel.isCollapsed) {
            Swal.fire({ title: 'Gagal', text: 'Pilih teks yang sudah diformat terlebih dahulu, lalu klik Salin Format.', icon: 'warning', confirmButtonText: 'OK' });
            return;
        }
        
        var range = sel.getRangeAt(0);
        var srcNode = range.startContainer;
        if (srcNode.nodeType === Node.TEXT_NODE) srcNode = srcNode.parentElement;
        if (!srcNode || !this.content.contains(srcNode)) {
            srcNode = this.content;
        }

        var cs = window.getComputedStyle(srcNode);
        if (!cs) {
            Swal.fire({ title: 'Gagal', text: 'Gagal menyalin format.', icon: 'error', confirmButtonText: 'OK' });
            return;
        }

        var blockNode = srcNode;
        var blockTagName = null;
        var foundBlock = null;
        while (blockNode && blockNode !== this.content) {
            var tag = blockNode.tagName.toUpperCase();
            if (['H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'PRE'].indexOf(tag) !== -1) {
                foundBlock = blockNode;
                break;
            }
            if (!foundBlock && ['P', 'DIV'].indexOf(tag) !== -1) {
                foundBlock = blockNode;
            }
            blockNode = blockNode.parentElement;
        }
        if (foundBlock) {
            blockTagName = foundBlock.tagName.toUpperCase();
        }

        this._copiedStyles = {
            fontWeight: cs.getPropertyValue('font-weight'),
            fontStyle: cs.getPropertyValue('font-style'),
            textDecoration: cs.getPropertyValue('text-decoration') || cs.getPropertyValue('text-decoration-line'),
            color: cs.getPropertyValue('color'),
            backgroundColor: cs.getPropertyValue('background-color'),
            fontSize: cs.getPropertyValue('font-size'),
            fontFamily: cs.getPropertyValue('font-family'),
            blockTagName: blockTagName,
            textAlign: cs.getPropertyValue('text-align'),
            lineHeight: cs.getPropertyValue('line-height'),
            isBold: false,
            isItalic: false,
            isUnderline: false,
            isStrike: false,
            isSub: false,
            isSup: false
        };

        if (this._copiedStyles.fontWeight === 'bold' || parseInt(this._copiedStyles.fontWeight, 10) >= 600) this._copiedStyles.isBold = true;
        if (this._copiedStyles.fontStyle === 'italic') this._copiedStyles.isItalic = true;
        if (this._copiedStyles.textDecoration && this._copiedStyles.textDecoration.indexOf('underline') !== -1) this._copiedStyles.isUnderline = true;
        if (this._copiedStyles.textDecoration && this._copiedStyles.textDecoration.indexOf('line-through') !== -1) this._copiedStyles.isStrike = true;

        var curr = srcNode;
        while (curr && curr !== this.content) {
            var tag = curr.tagName;
            if (tag === 'B' || tag === 'STRONG') this._copiedStyles.isBold = true;
            if (tag === 'I' || tag === 'EM') this._copiedStyles.isItalic = true;
            if (tag === 'U') this._copiedStyles.isUnderline = true;
            if (tag === 'S' || tag === 'STRIKE') this._copiedStyles.isStrike = true;
            if (tag === 'SUB') this._copiedStyles.isSub = true;
            if (tag === 'SUP') this._copiedStyles.isSup = true;
            curr = curr.parentElement;
        }

        // Copy link details if source node is inside a link, or if the selection contains a link
        var linkEl = srcNode.closest('a');
        if (!linkEl) {
            var frag = range.cloneContents();
            linkEl = frag.querySelector('a');
        }
        if (linkEl) {
            this._copiedStyles.linkHref = linkEl.getAttribute('href');
            this._copiedStyles.linkTarget = linkEl.getAttribute('target');
        }

        this._formatCopied = true;
        // Highlight the button briefly
        var btn = this._buttons['copyformat'];
        if (btn) { btn.classList.add('rte-active'); setTimeout(function () { btn.classList.remove('rte-active'); }, 1500); }
    };

    // -------- Find & Replace --------
    RichTextEditor.prototype._dialogFindReplace = function () {
        var self = this;
        var found = [], currentIdx = -1;

        var findInput = el('input', { type: 'text', class: 'rte-form-input', name: 'find', placeholder: 'Find text...' });
        var replaceInput = el('input', { type: 'text', class: 'rte-form-input', name: 'replace', placeholder: 'Replace with...' });
        var statusEl = el('div', { class: 'rte-find-status', text: '' });
        var findNextBtn = el('button', { type: 'button', class: 'rte-btn rte-btn-secondary rte-btn-sm', text: 'Find Next' });
        var replaceOneBtn = el('button', { type: 'button', class: 'rte-btn rte-btn-secondary rte-btn-sm', text: 'Replace' });
        var replaceAllBtn = el('button', { type: 'button', class: 'rte-btn rte-btn-secondary rte-btn-sm', text: 'Replace All' });

        function highlight(container, term) {
            if (!term) return [];
            var foundRanges = [];
            var walker = document.createTreeWalker(container, NodeFilter.SHOW_TEXT, null, false);
            var nodes = []; while (walker.nextNode()) nodes.push(walker.currentNode);
            nodes.forEach(function (node) {
                var text = node.textContent;
                var idx = text.toLowerCase().indexOf(term.toLowerCase());
                if (idx !== -1) {
                    var range = document.createRange();
                    range.setStart(node, idx);
                    range.setEnd(node, idx + term.length);
                    foundRanges.push(range);
                }
            });
            return foundRanges;
        }

        function clearHighlights() {
            self.content.querySelectorAll('.rte-find-hl').forEach(function (el) {
                el.replaceWith(document.createTextNode(el.textContent));
            });
        }

        function highlightRange(range) {
            var span = document.createElement('span');
            span.className = 'rte-find-hl';
            try {
                range.surroundContents(span);
            } catch (e) { }
        }

        function scrollRangeIntoView(range) {
            try {
                var rects = range.getClientRects();
                if (rects.length) {
                    self.contentWrap.scrollTop += rects[0].top - self.contentWrap.getBoundingClientRect().top - 80;
                }
            } catch (e) { }
        }

        function updateHighlight() {
            clearHighlights();
            if (currentIdx >= 0 && currentIdx < found.length) {
                highlightRange(found[currentIdx]);
                scrollRangeIntoView(found[currentIdx]);
            }
        }

        // Find Next - advance to the next match without resetting
        function doFindNext() {
            var term = findInput.value.trim();
            if (!term) return;

            if (found.length === 0) {
                // No previous search, do full search
                found = highlight(self.content, term);
                if (found.length === 0) {
                    statusEl.textContent = 'Not found';
                    return;
                }
                currentIdx = 0;
            } else {
                // Advance to next match
                currentIdx = (currentIdx + 1) % found.length;
                if (currentIdx === 0) {
                    statusEl.textContent = 'Reached start, wrapping...';
                }
            }

            statusEl.textContent = (currentIdx + 1) + ' of ' + found.length + ' found';
            updateHighlight();
        }

        // Full search - resets everything
        function doFind() {
            var term = findInput.value.trim();
            if (!term) return;

            clearHighlights();
            found = highlight(self.content, term);
            statusEl.textContent = found.length ? found.length + ' found' : 'Not found';

            if (found.length) {
                currentIdx = 0;
                highlightRange(found[0]);
                scrollRangeIntoView(found[0]);
            } else {
                currentIdx = -1;
            }
        }

        function doReplace(replaceAll) {
            var term = findInput.value.trim();
            var repl = replaceInput.value;
            if (!term) return;
            if (replaceAll) {
                var html = self.content.innerHTML;
                var regex = new RegExp(term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
                self.content.innerHTML = html.replace(regex, repl);
                statusEl.textContent = 'Replaced all';
                self._syncSource();
                found = []; currentIdx = -1;
            } else {
                if (currentIdx >= 0 && currentIdx < found.length) {
                    try {
                        found[currentIdx].deleteContents();
                        found[currentIdx].insertNode(document.createTextNode(repl));
                        self._syncSource();
                        statusEl.textContent = 'Replaced';
                        // Re-find all matches after replacement
                        clearHighlights();
                        found = highlight(self.content, term);
                        if (found.length) {
                            currentIdx = 0;
                            highlightRange(found[0]);
                            statusEl.textContent = (currentIdx + 1) + ' of ' + found.length + ' found';
                        } else {
                            currentIdx = -1;
                            statusEl.textContent = 'Not found';
                        }
                    } catch (e) { }
                }
            }
        }

        var body = el('div', { class: 'rte-form' }, [
            el('label', { class: 'rte-form-label', text: 'Find' }),
            findInput,
            el('button', { type: 'button', class: 'rte-btn rte-btn-secondary rte-btn-sm', text: 'Find Next', onclick: doFindNext, style: { marginTop: '4px' } }),
            el('label', { class: 'rte-form-label', text: 'Replace with' }),
            replaceInput,
            el('div', { style: 'display:flex;gap:6px;margin-top:4px;' }, [replaceOneBtn, replaceAllBtn]),
            statusEl,
        ]);

        findNextBtn.addEventListener('click', doFindNext);
        replaceOneBtn.addEventListener('click', function () { doReplace(false); });
        replaceAllBtn.addEventListener('click', function () { doReplace(true); });

        openModal({
            title: 'Find & Replace',
            body: body,
            confirmLabel: 'Close',
            onConfirm: function () {
                clearHighlights();
            },
        });

        setTimeout(function () { findInput.focus(); }, 50);
    };

    // -------- Insert Search Block inside Content --------
    RichTextEditor.prototype._insertSearchBlock = function () {
        var self = this;
        // Only allow one search block per editor
        if (this.content && this.content.querySelector('.rte-search-block')) {
            alert('Kolom pencarian sudah ada di konten ini.');
            return;
        }

        var alignSelect = el('select', { class: 'rte-form-input', name: 'align' });
        [
            ['left', 'Rata Kiri (Left)'],
            ['center', 'Rata Tengah (Center)'],
            ['right', 'Rata Kanan (Right)'],
            ['full', 'Penuh (Full Width)']
        ].forEach(function (opt) {
            alignSelect.appendChild(el('option', { value: opt[0], text: opt[1] }));
        });
        alignSelect.value = 'left';

        var body = el('div', { class: 'rte-form' }, [
            el('label', { class: 'rte-form-label', text: 'Perataan (Alignment)' }),
            alignSelect
        ]);

        openModal({
            title: 'Insert Kolom Pencarian',
            body: body,
            confirmLabel: 'Insert',
            onConfirm: function () {
                var alignVal = alignSelect.value;
                var html = '<div class="rte-search-block" data-rte-search-block="1" data-rte-align="' + alignVal + '" contenteditable="false">' +
                    '<div class="rte-search-block-title">' +
                        '<svg style="display:inline-block;vertical-align:middle;margin-right:6px;width:16px;height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>' +
                        'Cari di Halaman Ini' +
                    '</div>' +
                    '<div class="rte-search-block-form">' +
                        '<input type="text" class="rte-search-block-input" placeholder="Ketik kata kunci pencarian..." autocomplete="off" />' +
                        '<button type="button" class="rte-search-block-submit">Cari</button>' +
                        '<button type="button" class="rte-search-block-prev" title="Hasil sebelumnya">&#8593;</button>' +
                        '<button type="button" class="rte-search-block-next" title="Hasil berikutnya">&#8595;</button>' +
                        '<button type="button" class="rte-search-block-clear" title="Hapus pencarian">&#10005;</button>' +
                    '</div>' +
                    '<div class="rte-search-block-status"></div>' +
                '</div>';

                self._insertHTML(html);
                self._syncSource();
                self._updateState();
            }
        });
    };

    // -------- Insert Template --------
    RichTextEditor.prototype._dialogTemplate = function () {
        var self = this;
        var TEMPLATES = [
            {
                label: 'Business Letter',
                html: '<h2>Official Letter</h2><p>Date: ________________</p><p>To: ________________</p><p>From: ________________</p><hr><p>Subject: ________________</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>Respectfully,</p><p>&nbsp;</p><p>________________</p>',
            },
            {
                label: 'Memo',
                html: '<h2>Memorandum</h2><p><strong>To:</strong> ____________</p><p><strong>From:</strong> ____________</p><p><strong>Date:</strong> ____________</p><p><strong>Re:</strong> ____________</p><hr><p>&nbsp;</p>',
            },
            {
                label: 'Report Header',
                html: '<h1>Report Title</h1><p><em>Subtitle or description</em></p><hr><h2>1. Introduction</h2><p>&nbsp;</p><h2>2. Discussion</h2><p>&nbsp;</p><h2>3. Conclusion</h2><p>&nbsp;</p>',
            },
            {
                label: 'Announcement',
                html: '<h2 style="text-align:center;">Announcement</h2><p style="text-align:center;"><em>Date: ____________</em></p><hr><p>&nbsp;</p><p style="text-align:center;">&#128226; More details coming soon &#128226;</p>',
            },
            {
                label: 'Table of Contents',
                html: '<h2>Table of Contents</h2><ol><li>Chapter 1 — ____________</li><li>Chapter 2 — ____________</li><li>Chapter 3 — ____________</li></ol>',
            },
            {
                label: 'Agenda',
                html: '<h2>Meeting Agenda</h2><p><strong>Date:</strong> ____________ &nbsp; <strong>Time:</strong> ____________</p><ol><li>Opening &amp; Roll Call</li><li>Previous Minutes</li><li>Old Business</li><li>New Business</li><li>Announcements</li><li>Adjournment</li></ol>',
            },
            {
                label: 'Blank Page',
                html: '<p><br></p>',
            },
        ];

        var grid = el('div', { class: 'rte-template-grid' });
        TEMPLATES.forEach(function (tpl) {
            var card = el('button', { type: 'button', class: 'rte-template-card', text: tpl.label });
            card.addEventListener('click', function () {
                self._insertHTML(tpl.html);
                modal.close();
            });
            grid.appendChild(card);
        });

        var modal = openModal({
            title: 'Insert Template',
            body: grid,
            confirmLabel: 'Close',
            onConfirm: function () { return; },
        });
    };

    // -------- Contextual Popups --------
    // ---- Image floating toolbar (matches richtexteditor.com) ----
    RichTextEditor.prototype._showImageEditorPopup = function (img) {
        var self = this;
        this._closeVideoPopup();
        this._closeImagePopup();
        this._closeCarouselPopup();
        this._closeButtonPopup();
        this._attachMediaResizeHandle(img);

        var toolbar = el('div', { class: 'rte-img-toolbar' });
        var activeMenu = null;
        function closeMenus() { if (activeMenu) { activeMenu.style.display = 'none'; activeMenu = null; } }

        function mkBtn(svgHtml, title, onclick) {
            var b = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            b.innerHTML = svgHtml;
            b.addEventListener('click', function (e) { e.stopPropagation(); closeMenus(); onclick(e); });
            return b;
        }

        function mkDrop(svgHtml, title, items, onOpen) {
            var wrap = el('div', { style: 'position:relative;display:inline-block;' });
            var btn = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            btn.innerHTML = svgHtml + '<svg viewBox="0 0 10 6" style="width:8px;height:8px;margin-left:1px"><polyline points="1,1 5,5 9,1" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
            var menu = el('div', { class: 'rte-img-tb-menu' });
            items.forEach(function (item) {
                if (item === '-') { menu.appendChild(el('div', { style: 'height:1px;background:#eee;margin:3px 0' })); return; }
                var mi = el('button', { type: 'button', class: 'rte-img-tb-menuitem', text: item.label });
                mi._itemData = item;
                mi.addEventListener('click', function (e) { e.stopPropagation(); closeMenus(); menu.style.display = 'none'; item.action(); });
                menu.appendChild(mi);
            });
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = menu.style.display === 'block';
                closeMenus();
                if (!open) { menu.style.display = 'block'; activeMenu = menu; if (onOpen) onOpen(menu); }
            });
            wrap.appendChild(btn); wrap.appendChild(menu);
            return wrap;
        }

        // 1. Set Size
        var ICON_SIZE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M3 9h18"/></svg>';
        toolbar.appendChild(mkDrop(ICON_SIZE, 'Set Size', [
            {
                label: 'Set Size\u2026', action: function () {
                    Swal.fire({
                        title: 'Set Image Size', html:
                            '<div style="display:flex;gap:8px;justify-content:center">' +
                            '<label style="font-size:13px">W: <input id="swal-img-w" type="number" value="' + (img.offsetWidth || img.width || '') + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                            '<label style="font-size:13px">H: <input id="swal-img-h" type="number" value="' + (img.offsetHeight || img.height || '') + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                            '</div>',
                        showCancelButton: true, confirmButtonText: 'Apply', cancelButtonText: 'Cancel'
                    }).then(function (r) {
                        if (r.isConfirmed) {
                            var w = parseInt(document.getElementById('swal-img-w').value, 10);
                            var h = parseInt(document.getElementById('swal-img-h').value, 10);
                            if (w > 0) img.style.width = w + 'px';
                            if (h > 0) img.style.height = h + 'px';
                            self._syncSource();
                            setTimeout(function () { self._updatePopupPositions(); }, 10);
                        }
                    });
                }
            },
            '-',
            { label: 'Auto size', action: function () { img.style.width = ''; img.style.height = ''; img.removeAttribute('width'); img.removeAttribute('height'); self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '100% width', action: function () { img.style.width = '100%'; img.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '75% width', action: function () { img.style.width = '75%'; img.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '50% width', action: function () { img.style.width = '50%'; img.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '25% width', action: function () { img.style.width = '25%'; img.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
        ]));

        // 2. Caption
        var ICON_CAPTION = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="13" rx="2"/><line x1="3" y1="21" x2="21" y2="21"/><line x1="7" y1="18" x2="17" y2="18"/></svg>';
        toolbar.appendChild(mkBtn(ICON_CAPTION, 'Image Caption', function () {
            var fig = img.closest('figure');
            var cap = fig ? fig.querySelector('figcaption') : null;
            if (!fig) {
                var newFig = document.createElement('figure');
                var isCentered = img.style.marginLeft === 'auto' && img.style.marginRight === 'auto';
                newFig.style.cssText = isCentered ? 'display:table; margin:0 auto;' : 'display:inline-table; margin:0; vertical-align:top;';
                if (img.style.float) { newFig.style.float = img.style.float; img.style.float = ''; }
                if (img.style.marginLeft) { newFig.style.marginLeft = img.style.marginLeft; img.style.marginLeft = ''; }
                if (img.style.marginRight) { newFig.style.marginRight = img.style.marginRight; img.style.marginRight = ''; }
                newFig.draggable = true;
                img.parentNode.insertBefore(newFig, img);
                newFig.appendChild(img);
                cap = document.createElement('figcaption');
                cap.contentEditable = 'true';
                cap.style.cssText = 'text-align:center;font-size:0.85em;color:#555;padding:4px 0;display:table-caption;caption-side:bottom;word-break:break-word;';
                cap.textContent = 'Caption';
                newFig.appendChild(cap);
                cap.focus(); var r = document.createRange(); r.selectNodeContents(cap); var s = window.getSelection(); s.removeAllRanges(); s.addRange(r);
            } else if (!cap) {
                cap = document.createElement('figcaption');
                cap.contentEditable = 'true';
                cap.style.cssText = 'text-align:center;font-size:0.85em;color:#555;padding:4px 0;display:table-caption;caption-side:bottom;word-break:break-word;';
                cap.textContent = 'Caption';
                fig.appendChild(cap);
                cap.focus();
            } else {
                cap.parentNode.removeChild(cap);
                if (fig.childNodes.length === 1 && fig.firstChild === img) {
                    if (fig.style.float) img.style.float = fig.style.float;
                    if (fig.style.marginLeft) img.style.marginLeft = fig.style.marginLeft;
                    if (fig.style.marginRight) img.style.marginRight = fig.style.marginRight;
                    if (fig.style.display && fig.style.display !== 'table' && fig.style.display !== 'inline-table') img.style.display = fig.style.display;
                    fig.parentNode.insertBefore(img, fig);
                    fig.parentNode.removeChild(fig);
                }
            }
            self._syncSource();
            setTimeout(function () { self._updatePopupPositions(); }, 10);
        }));

        // 3. Insert link
        var ICON_LINK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10 14a5 5 0 0 0 7.07 0l3-3a5 5 0 0 0-7.07-7.07l-1.5 1.5"/><path d="M14 10a5 5 0 0 0-7.07 0l-3 3a5 5 0 0 0 7.07 7.07l1.5-1.5"/></svg>';
        toolbar.appendChild(mkBtn(ICON_LINK, 'Insert Link', function () {
            var current = img.parentNode && img.parentNode.tagName === 'A' ? img.parentNode.href : '';
            Swal.fire({
                title: 'Link URL', input: 'url', inputValue: current,
                showCancelButton: true, confirmButtonText: 'Apply', inputPlaceholder: 'https://'
            }).then(function (r) {
                if (r.isConfirmed) {
                    var href = r.value.trim();
                    if (img.parentNode && img.parentNode.tagName === 'A') {
                        if (!href) { var a = img.parentNode; a.parentNode.insertBefore(img, a); a.parentNode.removeChild(a); }
                        else img.parentNode.href = href;
                    } else if (href) {
                        var a = document.createElement('a'); a.href = href; a.target = '_blank';
                        img.parentNode.insertBefore(a, img); a.appendChild(img);
                    }
                    self._syncSource();
                }
            });
        }));

        // 4. Justify
        var ICON_JUSTIFY = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>';
        function setJustify(float, mL, mR) {
            var target = img.closest('figure') || img;
            target.style.float = float;
            target.style.marginLeft = mL;
            target.style.marginRight = mR;
            if (target.tagName === 'FIGURE') {
                target.style.display = (float === 'none' && mL === 'auto') ? 'table' : 'inline-table';
                target.style.verticalAlign = 'top';
            } else {
                target.style.display = (float === 'none' && mL === 'auto') ? 'block' : 'inline-block';
            }
            self._syncSource();
            setTimeout(function () { self._updatePopupPositions(); }, 10);
        }
        toolbar.appendChild(mkDrop(ICON_JUSTIFY, 'Justify', [
            { label: 'Justify Left', action: function () { setJustify('none', '0', 'auto'); } },
            { label: 'Justify Center', action: function () { setJustify('none', 'auto', 'auto'); } },
            { label: 'Justify Right', action: function () { setJustify('none', 'auto', '0'); } },
            '-',
            { label: 'Float Left', action: function () { setJustify('left', '0', '10px'); } },
            { label: 'Float Right', action: function () { setJustify('right', '10px', '0'); } },
        ]));

        // 5. Style
        var ICON_STYLE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';
        var getTarget = function () { return img; };
        var getWrap = function () { return img.closest('figure') || img; };
        var styleItems = [
            { label: 'Border', key: 'border', get: function () { return getWrap().style.border ? 'active' : ''; }, action: function () { var t = getWrap(); if (t.style.border) { t.style.border = ''; t.style.padding = ''; t.style.borderRadius = ''; } else { t.style.border = '1px solid #ccc'; t.style.padding = '4px'; t.style.borderRadius = '4px'; t.style.background = '#fff'; } self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: 'Grayscale', key: 'grayscale', get: function () { return img.style.filter === 'grayscale(100%)' ? 'active' : ''; }, action: function () { img.style.filter = img.style.filter === 'grayscale(100%)' ? '' : 'grayscale(100%)'; self._syncSource(); } },
            { label: 'Shadow', key: 'shadow', get: function () { return getWrap().style.boxShadow ? 'active' : ''; }, action: function () { var t = getWrap(); t.style.boxShadow = t.style.boxShadow ? '' : '0 4px 8px rgba(0,0,0,0.1)'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: 'Margin 10px', key: 'margin', get: function () { return getWrap().style.margin ? 'active' : ''; }, action: function () { var t = getWrap(); t.style.margin = t.style.margin ? '' : '10px'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: 'Padding 10px', key: 'padding', get: function () { return getWrap().style.padding ? 'active' : ''; }, action: function () { var t = getWrap(); t.style.padding = t.style.padding ? '' : '10px'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: 'Rounded Corners', key: 'rounded', get: function () { return getWrap().style.borderRadius === '8px' ? 'active' : ''; }, action: function () { var t = getWrap(); t.style.borderRadius = t.style.borderRadius === '8px' ? '' : '8px'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: 'Rounded Image', key: 'circle', get: function () { return img.style.borderRadius === '50%' ? 'active' : ''; }, action: function () { img.style.borderRadius = img.style.borderRadius === '50%' ? '' : '50%'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: 'Thumbnail', key: 'thumb', get: function () { return (getWrap().style.background === 'rgb(255, 255, 255)' || getWrap().style.background === '#fff') ? 'active' : ''; }, action: function () { var t = getWrap(); if (t.style.background === 'rgb(255, 255, 255)' || t.style.background === '#fff') { t.style.border = ''; t.style.padding = ''; t.style.background = ''; t.style.borderRadius = ''; } else { t.style.border = '1px solid #ddd'; t.style.padding = '4px'; t.style.background = '#fff'; t.style.borderRadius = '4px'; } self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: 'Free Canvas Mode', key: 'absolute', get: function () { var w = getWrap(); return w.style.position === 'absolute' ? 'active' : ''; }, action: function () { var w = getWrap(); if (w.style.position === 'absolute') { w.style.position = ''; w.style.left = ''; w.style.top = ''; w.style.zIndex = ''; } else { w.style.position = 'absolute'; w.style.zIndex = '100'; w.style.left = w.offsetLeft + 'px'; w.style.top = w.offsetTop + 'px'; } self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } }
        ];
        toolbar.appendChild(mkDrop(ICON_STYLE, 'Image Style', styleItems, function (menu) {
            // Refresh active states when menu opens
            menu.querySelectorAll('.rte-img-tb-menuitem').forEach(function (mi) {
                var item = mi._itemData;
                mi.classList.toggle('rte-img-tb-menuitem-active', item && item.get && !!item.get());
            });
        }));

        // 6. Delete
        var ICON_DEL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>';
        toolbar.appendChild(mkBtn(ICON_DEL, 'Delete Image', function () {
            self._removeMediaResizeHandle();
            var fig = img.closest('figure');
            var target = fig || img;
            if (target.parentNode) target.parentNode.removeChild(target);
            self._closeImagePopup();
            self._syncSource();
        }));

        // Position toolbar above image
        toolbar.style.position = 'fixed';
        toolbar.style.zIndex = '99995';
        document.body.appendChild(toolbar);
        this._imagePopup = toolbar;

        function positionToolbar() {
            self._updatePopupPositions();
        }
        requestAnimationFrame(positionToolbar);

        setTimeout(function () {
            document.addEventListener('mousedown', self._imagePopupCloseHandler = function (e) {
                if (!toolbar.contains(e.target) && e.target !== img &&
                    !e.target.classList.contains('rte-img-handle') &&
                    !e.target.classList.contains('rte-media-move-handle') &&
                    !(e.target.closest && e.target.closest('.rte-img-overlay')) &&
                    !(e.target.closest && e.target.closest('.swal2-container'))) {
                    self._closeImagePopup();
                    self._removeMediaResizeHandle();
                }
            });
        }, 0);
    };

    RichTextEditor.prototype._attachMediaResizeHandle = function (media) {
        var self = this;
        this._removeMediaResizeHandle();

        // For IFRAME elements, add a transparent click-interceptor overlay inside the content area
        // so that clicks on the iframe are captured by the editor (iframes swallow click events)
        if (media.tagName === 'IFRAME' || media.tagName === 'VIDEO') {
            var clickInterceptor = document.createElement('div');
            clickInterceptor.className = 'rte-iframe-click-interceptor';
            // Use fixed position so it matches the resize overlay exactly and doesn't 
            // overflow the parent container if the parent is larger than the media.
            clickInterceptor.style.cssText = 'position:fixed;z-index:9997;cursor:pointer;background:transparent;';
            clickInterceptor.draggable = true;
            document.body.appendChild(clickInterceptor);

            clickInterceptor.addEventListener('click', function (e) {
                e.stopPropagation();
                self._showVideoEditorPopup(media);
            });
            self._iframeClickInterceptor = clickInterceptor;
        }

        // Create full border overlay with 8 resize handles
        var overlay = document.createElement('div');
        overlay.className = 'rte-img-overlay';
        overlay.style.position = 'fixed';
        overlay.style.pointerEvents = 'none';
        overlay.style.border = '2px solid #3b82f6';
        overlay.style.zIndex = '9998';
        overlay.style.boxSizing = 'border-box';
        document.body.appendChild(overlay);
        self._mediaResizeHandle = overlay;
        self._mediaResizeTarget = media;

        // ---- Move Handle for Media ----
        var moveHandle = document.createElement('div');
        moveHandle.className = 'rte-media-move-handle';
        moveHandle.title = 'Drag to move';
        moveHandle.style.cssText = 'position:absolute;top:0;left:0;width:24px;height:24px;background:#3b82f6;color:#fff;display:flex;align-items:center;justify-content:center;cursor:move;pointer-events:auto;z-index:10;';
        moveHandle.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><polyline points="5 9 2 12 5 15"></polyline><polyline points="9 5 12 2 15 5"></polyline><polyline points="19 9 22 12 19 15"></polyline><polyline points="9 19 12 22 15 19"></polyline><line x1="2" y1="12" x2="22" y2="12"></line><line x1="12" y1="2" x2="12" y2="22"></line></svg>';

        var isMediaDragging = false, dragOffX = 0, dragOffY = 0, mediaOrigLeft = 0, mediaOrigTop = 0;

        function onMediaDragStart(e) {
            e.preventDefault(); e.stopPropagation();
            isMediaDragging = true;
            self._isResizingOrMoving = true;

            var cx = e.touches ? e.touches[0].clientX : e.clientX;
            var cy = e.touches ? e.touches[0].clientY : e.clientY;
            var targetNode = media.closest('figure') || media;

            if (targetNode.style.position === 'absolute') {
                mediaOrigLeft = parseFloat(targetNode.style.left) || targetNode.offsetLeft || 0;
                mediaOrigTop = parseFloat(targetNode.style.top) || targetNode.offsetTop || 0;
            } else {
                mediaOrigLeft = parseFloat(targetNode.style.marginLeft) || 0;
                mediaOrigTop = parseFloat(targetNode.style.marginTop) || 0;
            }
            dragOffX = cx;
            dragOffY = cy;

            document.addEventListener('mousemove', onMediaDragMove);
            document.addEventListener('mouseup', onMediaDragEnd);
            document.addEventListener('touchmove', onMediaDragMove, { passive: false });
            document.addEventListener('touchend', onMediaDragEnd);
        }

        function onMediaDragMove(e) {
            if (!isMediaDragging) return;
            e.preventDefault();
            var cx = e.touches ? e.touches[0].clientX : e.clientX;
            var cy = e.touches ? e.touches[0].clientY : e.clientY;
            var targetNode = media.closest('figure') || media;

            if (targetNode.style.position === 'absolute') {
                targetNode.style.left = (mediaOrigLeft + cx - dragOffX) + 'px';
                targetNode.style.top = (mediaOrigTop + cy - dragOffY) + 'px';
            } else {
                targetNode.style.marginLeft = (mediaOrigLeft + cx - dragOffX) + 'px';
                targetNode.style.marginTop = (mediaOrigTop + cy - dragOffY) + 'px';
            }
            if (self._mediaResizePositioner) self._mediaResizePositioner();
        }

        function onMediaDragEnd() {
            isMediaDragging = false;
            self._isResizingOrMoving = false;
            document.removeEventListener('mousemove', onMediaDragMove);
            document.removeEventListener('mouseup', onMediaDragEnd);
            document.removeEventListener('touchmove', onMediaDragMove);
            document.removeEventListener('touchend', onMediaDragEnd);
            self._syncSource();
        }

        moveHandle.addEventListener('mousedown', onMediaDragStart);
        moveHandle.addEventListener('touchstart', onMediaDragStart, { passive: false });
        overlay.appendChild(moveHandle);

        function positionOverlay() {
            if (!self._mediaResizeTarget || !self._mediaResizeHandle) return;
            var rect = self._mediaResizeTarget.getBoundingClientRect();
            self._mediaResizeHandle.style.left = (rect.left - 2) + 'px';
            self._mediaResizeHandle.style.top = (rect.top - 2) + 'px';
            self._mediaResizeHandle.style.width = (rect.width + 4) + 'px';
            self._mediaResizeHandle.style.height = (rect.height + 4) + 'px';

            // Sync click interceptor if present
            if (self._iframeClickInterceptor) {
                self._iframeClickInterceptor.style.left = rect.left + 'px';
                self._iframeClickInterceptor.style.top = rect.top + 'px';
                self._iframeClickInterceptor.style.width = rect.width + 'px';
                self._iframeClickInterceptor.style.height = rect.height + 'px';
            }

            // Sync popups as well
            if (self._imagePopup || self._videoPopup) self._updatePopupPositions();

            self._mediaResizeRaf = requestAnimationFrame(positionOverlay);
        }
        positionOverlay();
        self._mediaResizePositioner = positionOverlay;

        var dirs = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];
        dirs.forEach(function (dir) {
            var h = document.createElement('div');
            h.className = 'rte-img-handle rte-img-handle-' + dir;
            // Slightly larger and more visible
            h.style.width = '12px';
            h.style.height = '12px';
            h.style.background = '#3b82f6';
            h.style.border = '2px solid #fff';
            h.style.boxShadow = '0 0 4px rgba(0,0,0,0.3)';

            h.addEventListener('mousedown', function (e) {
                e.preventDefault(); e.stopPropagation();
                var startX = e.clientX, startY = e.clientY;
                var startW = media.offsetWidth, startH = media.offsetHeight;
                function onMove(me) {
                    var dx = me.clientX - startX, dy = me.clientY - startY;
                    var nw = startW, nh = startH;
                    if (dir.indexOf('e') > -1) nw = Math.max(20, startW + dx);
                    if (dir.indexOf('w') > -1) nw = Math.max(20, startW - dx);
                    if (dir.indexOf('s') > -1) nh = Math.max(20, startH + dy);
                    if (dir.indexOf('n') > -1) nh = Math.max(20, startH - dy);
                    media.style.width = nw + 'px';
                    if (dir.indexOf('n') > -1 || dir.indexOf('s') > -1) media.style.height = nh + 'px';
                    if (media.classList.contains('rte-carousel-container')) media.style.aspectRatio = 'auto';
                    self._updatePopupPositions();
                }
                function onUp() {
                    document.removeEventListener('mousemove', onMove);
                    document.removeEventListener('mouseup', onUp);
                    self._syncSource();
                }
                document.addEventListener('mousemove', onMove);
                document.addEventListener('mouseup', onUp);
            });
            overlay.appendChild(h);
        });
    };

    RichTextEditor.prototype._removeMediaResizeHandle = function () {
        if (this._mediaResizeRaf) {
            cancelAnimationFrame(this._mediaResizeRaf);
            this._mediaResizeRaf = null;
        }
        if (this._mediaResizeHandle && this._mediaResizeHandle.parentNode) {
            this._mediaResizeHandle.parentNode.removeChild(this._mediaResizeHandle);
        }
        this._mediaResizeHandle = null;
        this._mediaResizeTarget = null;
        this._mediaResizePositioner = null;
        // Remove iframe click interceptor if present
        if (this._iframeClickInterceptor && this._iframeClickInterceptor.parentNode) {
            this._iframeClickInterceptor.parentNode.removeChild(this._iframeClickInterceptor);
        }
        this._iframeClickInterceptor = null;
    };

    RichTextEditor.prototype._closeImagePopup = function () {
        if (this._imagePopup) {
            if (this._imagePopup.parentNode) this._imagePopup.parentNode.removeChild(this._imagePopup);
            this._imagePopup = null;
        }
        if (this._imagePopupCloseHandler) {
            document.removeEventListener('mousedown', this._imagePopupCloseHandler);
            this._imagePopupCloseHandler = null;
        }
    };

    // ---- Video / Iframe floating toolbar ----
    RichTextEditor.prototype._showVideoEditorPopup = function (media) {
        var self = this;
        this._closeImagePopup();
        this._closeVideoPopup();
        this._closeCarouselPopup();
        this._closeButtonPopup();
        this._videoTarget = media;
        this._attachMediaResizeHandle(media);

        var toolbar = el('div', { class: 'rte-img-toolbar' });
        var activeMenu = null;
        function closeMenus() { if (activeMenu) { activeMenu.style.display = 'none'; activeMenu = null; } }

        function mkBtn(svgHtml, title, onclick) {
            var b = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            b.innerHTML = svgHtml;
            b.addEventListener('click', function (e) { e.stopPropagation(); closeMenus(); onclick(e); });
            return b;
        }
        function mkDrop(svgHtml, title, items, onOpen) {
            var wrap = el('div', { style: 'position:relative;display:inline-block;' });
            var btn = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            btn.innerHTML = svgHtml + '<svg viewBox="0 0 10 6" style="width:8px;height:8px;margin-left:1px"><polyline points="1,1 5,5 9,1" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
            var menu = el('div', { class: 'rte-img-tb-menu' });
            items.forEach(function (item) {
                if (item === '-') { menu.appendChild(el('div', { style: 'height:1px;background:#eee;margin:3px 0' })); return; }
                var mi = el('button', { type: 'button', class: 'rte-img-tb-menuitem', text: item.label });
                mi._itemData = item;
                mi.addEventListener('click', function (e) { e.stopPropagation(); closeMenus(); menu.style.display = 'none'; item.action(); });
                menu.appendChild(mi);
            });
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = menu.style.display === 'block';
                closeMenus();
                if (!open) { menu.style.display = 'block'; activeMenu = menu; if (onOpen) onOpen(menu); }
            });
            wrap.appendChild(btn); wrap.appendChild(menu);
            return wrap;
        }

        // 1. Set Size
        var ICON_SIZE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M3 9h18"/></svg>';
        toolbar.appendChild(mkDrop(ICON_SIZE, 'Set Size', [
            {
                label: 'Set Size\u2026', action: function () {
                    Swal.fire({
                        title: 'Set Video Size', html:
                            '<div style="display:flex;gap:8px;justify-content:center">' +
                            '<label style="font-size:13px">W: <input id="swal-vid-w" type="number" value="' + (media.offsetWidth || 560) + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                            '<label style="font-size:13px">H: <input id="swal-vid-h" type="number" value="' + (media.offsetHeight || 315) + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                            '</div>',
                        showCancelButton: true, confirmButtonText: 'Apply'
                    }).then(function (r) {
                        if (r.isConfirmed) {
                            var w = parseInt(document.getElementById('swal-vid-w').value, 10);
                            var h = parseInt(document.getElementById('swal-vid-h').value, 10);
                            if (w > 0) media.style.width = w + 'px';
                            if (h > 0) media.style.height = h + 'px';
                            self._syncSource();
                            setTimeout(function () { self._updatePopupPositions(); }, 10);
                        }
                    });
                }
            },
            '-',
            { label: 'Auto size', action: function () { media.style.width = ''; media.style.height = ''; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '100% width', action: function () { media.style.width = '100%'; media.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '75% width', action: function () { media.style.width = '75%'; media.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '50% width', action: function () { media.style.width = '50%'; media.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '25% width', action: function () { media.style.width = '25%'; media.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
        ]));

        // 2. Caption
        var ICON_CAPTION = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="13" rx="2"/><line x1="3" y1="21" x2="21" y2="21"/><line x1="7" y1="18" x2="17" y2="18"/></svg>';
        toolbar.appendChild(mkBtn(ICON_CAPTION, 'Video Caption', function () {
            var fig = media.closest('figure');
            var cap = fig ? fig.querySelector('figcaption') : null;
            if (!fig) {
                var newFig = document.createElement('figure');
                var isCentered = media.style.marginLeft === 'auto' && media.style.marginRight === 'auto';
                newFig.style.cssText = isCentered ? 'display:table; margin:0 auto;' : 'display:inline-table; margin:0; vertical-align:top;';
                if (media.style.float) { newFig.style.float = media.style.float; media.style.float = ''; }
                if (media.style.marginLeft) { newFig.style.marginLeft = media.style.marginLeft; media.style.marginLeft = ''; }
                if (media.style.marginRight) { newFig.style.marginRight = media.style.marginRight; media.style.marginRight = ''; }
                newFig.draggable = true;
                media.parentNode.insertBefore(newFig, media);
                newFig.appendChild(media);
                cap = document.createElement('figcaption');
                cap.contentEditable = 'true';
                cap.style.cssText = 'text-align:center;font-size:0.85em;color:#555;padding:4px 0;display:table-caption;caption-side:bottom;word-break:break-word;';
                cap.textContent = 'Caption';
                newFig.appendChild(cap);
                cap.focus();
            } else if (!cap) {
                cap = document.createElement('figcaption');
                cap.contentEditable = 'true';
                cap.style.cssText = 'text-align:center;font-size:0.85em;color:#555;padding:4px 0;display:table-caption;caption-side:bottom;word-break:break-word;';
                cap.textContent = 'Caption';
                fig.appendChild(cap);
                cap.focus();
            } else {
                cap.parentNode.removeChild(cap);
                if (fig.childNodes.length === 1 && fig.firstChild === media) {
                    if (fig.style.float) media.style.float = fig.style.float;
                    if (fig.style.marginLeft) media.style.marginLeft = fig.style.marginLeft;
                    if (fig.style.marginRight) media.style.marginRight = media.style.marginRight;
                    if (fig.style.display && fig.style.display !== 'table' && fig.style.display !== 'inline-table') media.style.display = fig.style.display;
                    fig.parentNode.insertBefore(media, fig);
                    fig.parentNode.removeChild(fig);
                }
            }
            self._syncSource();
            setTimeout(function () { self._updatePopupPositions(); }, 10);
        }));

        // 3. Justify
        var ICON_JUSTIFY = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>';
        function setVJustify(float, mL, mR) {
            var wrap = media.closest('figure') || media;
            wrap.style.float = float;
            wrap.style.marginLeft = mL;
            wrap.style.marginRight = mR;
            if (wrap.tagName === 'FIGURE') {
                wrap.style.display = (float === 'none' && mL === 'auto') ? 'table' : 'inline-table';
                wrap.style.verticalAlign = 'top';
            } else {
                wrap.style.display = (float === 'none' && mL === 'auto') ? 'block' : 'inline-block';
            }
            self._syncSource();
            setTimeout(function () { self._updatePopupPositions(); }, 10);
        }
        toolbar.appendChild(mkDrop(ICON_JUSTIFY, 'Justify', [
            { label: 'Justify Left', action: function () { setVJustify('none', '0', 'auto'); } },
            { label: 'Justify Center', action: function () { setVJustify('none', 'auto', 'auto'); } },
            { label: 'Justify Right', action: function () { setVJustify('none', 'auto', '0'); } },
            '-',
            { label: 'Float Left', action: function () { setVJustify('left', '0', '10px'); } },
            { label: 'Float Right', action: function () { setVJustify('right', '10px', '0'); } },
        ]));

        // 4. Style
        var ICON_STYLE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';
        var getTarget = function () { return media; };
        var getWrapV = function () { return media.closest('figure') || media; };
        var vStyleItems = [
            { label: 'Border', get: function () { return !!getTarget().style.border; }, action: function () { var t = getTarget(); if (t.style.border) { t.style.border = ''; t.style.padding = ''; t.style.borderRadius = ''; } else { t.style.border = '1px solid #ccc'; t.style.padding = '4px'; t.style.borderRadius = '4px'; t.style.background = '#fff'; } self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: 'Shadow', get: function () { return !!getTarget().style.boxShadow; }, action: function () { var t = getTarget(); t.style.boxShadow = t.style.boxShadow ? '' : '0 4px 12px rgba(0,0,0,0.3)'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: 'Rounded', get: function () { return !!getTarget().style.borderRadius; }, action: function () { var t = getTarget(); t.style.borderRadius = t.style.borderRadius ? '' : '8px'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            '-',
            { label: 'Free Canvas Mode', get: function () { var w = getWrapV(); return w.style.position === 'absolute' ? 'active' : ''; }, action: function () { var w = getWrapV(); if (w.style.position === 'absolute') { w.style.position = ''; w.style.left = ''; w.style.top = ''; w.style.zIndex = ''; } else { w.style.position = 'absolute'; w.style.zIndex = '100'; w.style.left = w.offsetLeft + 'px'; w.style.top = w.offsetTop + 'px'; } self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
        ];
        toolbar.appendChild(mkDrop(ICON_STYLE, 'Video Style', vStyleItems, function (menu) {
            menu.querySelectorAll('.rte-img-tb-menuitem').forEach(function (mi) {
                var item = mi._itemData;
                if (item && item.get) mi.classList.toggle('rte-img-tb-menuitem-active', !!item.get());
            });
        }));

        // 5. Delete
        var ICON_DEL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>';
        toolbar.appendChild(mkBtn(ICON_DEL, 'Delete Video', function () {
            self._removeMediaResizeHandle();
            var wrap = media.closest('figure') || media;
            if (wrap.parentNode) wrap.parentNode.removeChild(wrap);
            self._closeVideoPopup();
            self._syncSource();
        }));

        toolbar.style.position = 'fixed';
        toolbar.style.zIndex = '99995';
        document.body.appendChild(toolbar);
        this._videoPopup = toolbar;

        function positionToolbar() {
            self._updatePopupPositions();
        }
        requestAnimationFrame(positionToolbar);

        setTimeout(function () {
            document.addEventListener('mousedown', self._videoPopupCloseHandler = function (e) {
                if (!toolbar.contains(e.target) && e.target !== media &&
                    !e.target.classList.contains('rte-img-handle') &&
                    !e.target.classList.contains('rte-media-move-handle') &&
                    !(e.target.closest && e.target.closest('.rte-img-overlay')) &&
                    !(e.target.closest && e.target.closest('.swal2-container'))) {
                    self._closeVideoPopup();
                }
            });
        }, 0);
    };

    RichTextEditor.prototype._closeVideoPopup = function () {
        if (this._videoPopup) {
            if (this._videoPopup.parentNode) this._videoPopup.parentNode.removeChild(this._videoPopup);
            this._videoPopup = null;
        }
        if (this._videoPopupCloseHandler) {
            document.removeEventListener('mousedown', this._videoPopupCloseHandler);
            this._videoPopupCloseHandler = null;
        }
        this._videoTarget = null;
    };

    RichTextEditor.prototype._showTocEditorPopup = function (tocBlock) {
        var self = this;
        this._closeImagePopup();
        this._closeVideoPopup();
        this._closeCarouselPopup();
        this._closeButtonPopup();
        this._closeTocPopup();
        this._attachMediaResizeHandle(tocBlock);

        var toolbar = el('div', { class: 'rte-img-toolbar' });
        var activeMenu = null;
        function closeMenus() { if (activeMenu) { activeMenu.style.display = 'none'; activeMenu = null; } }

        function mkBtn(svgHtml, title, onclick) {
            var b = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            b.innerHTML = svgHtml;
            b.addEventListener('click', function (e) { e.stopPropagation(); closeMenus(); onclick(e); });
            return b;
        }

        // 1. Edit TOC
        var ICON_EDIT = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
        toolbar.appendChild(mkBtn(ICON_EDIT, 'Edit Daftar Isi', function () {
            self._dialogToc(tocBlock);
        }));

        // 2. Delete TOC
        var ICON_DEL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>';
        toolbar.appendChild(mkBtn(ICON_DEL, 'Hapus Daftar Isi', function () {
            self._removeMediaResizeHandle();
            if (tocBlock.parentNode) tocBlock.parentNode.removeChild(tocBlock);
            self._closeTocPopup();
            self._syncSource();
            self._updateState();
        }));

        // Position toolbar above TOC block
        toolbar.style.position = 'fixed';
        toolbar.style.zIndex = '99995';
        document.body.appendChild(toolbar);
        this._tocPopup = toolbar;

        this._updatePopupPositions();
    };

    RichTextEditor.prototype._closeTocPopup = function () {
        if (this._tocPopup) {
            if (this._tocPopup.parentNode) this._tocPopup.parentNode.removeChild(this._tocPopup);
            this._tocPopup = null;
        }
    };

    RichTextEditor.prototype._showSearchBlockEditorPopup = function (searchBlock) {
        var self = this;
        this._closeImagePopup();
        this._closeVideoPopup();
        this._closeCarouselPopup();
        this._closeButtonPopup();
        this._closeTocPopup();
        this._closeSearchBlockPopup();
        this._attachMediaResizeHandle(searchBlock);

        var toolbar = el('div', { class: 'rte-img-toolbar' });
        var activeMenu = null;
        function closeMenus() { if (activeMenu) { activeMenu.style.display = 'none'; activeMenu = null; } }

        function mkBtn(svgHtml, title, onclick) {
            var b = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            b.innerHTML = svgHtml;
            b.addEventListener('click', function (e) { e.stopPropagation(); closeMenus(); onclick(e); });
            return b;
        }

        function mkDrop(svgHtml, title, items, onOpen) {
            var wrap = el('div', { style: 'position:relative;display:inline-block;' });
            var btn = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            btn.innerHTML = svgHtml + '<svg viewBox="0 0 10 6" style="width:8px;height:8px;margin-left:1px"><polyline points="1,1 5,5 9,1" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
            var menu = el('div', { class: 'rte-img-tb-menu' });
            items.forEach(function (item) {
                if (item === '-') { menu.appendChild(el('div', { style: 'height:1px;background:#eee;margin:3px 0' })); return; }
                var mi = el('button', { type: 'button', class: 'rte-img-tb-menuitem', text: item.label });
                mi._itemData = item;
                mi.addEventListener('click', function (e) { e.stopPropagation(); closeMenus(); menu.style.display = 'none'; item.action(); });
                menu.appendChild(mi);
            });
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = menu.style.display === 'block';
                closeMenus();
                if (!open) { menu.style.display = 'block'; activeMenu = menu; if (onOpen) onOpen(menu); }
            });
            wrap.appendChild(btn); wrap.appendChild(menu);
            return wrap;
        }

        // Alignments dropdown
        var alignIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></svg>';
        toolbar.appendChild(mkDrop(alignIcon, 'Perataan', [
            { label: 'Kiri (Left)', action: function () { searchBlock.setAttribute('data-rte-align', 'left'); self._syncSource(); self._updatePopupPositions(); } },
            { label: 'Tengah (Center)', action: function () { searchBlock.setAttribute('data-rte-align', 'center'); self._syncSource(); self._updatePopupPositions(); } },
            { label: 'Kanan (Right)', action: function () { searchBlock.setAttribute('data-rte-align', 'right'); self._syncSource(); self._updatePopupPositions(); } },
            { label: 'Penuh (Full)', action: function () { searchBlock.setAttribute('data-rte-align', 'full'); self._syncSource(); self._updatePopupPositions(); } },
        ]));

        // Delete button
        var ICON_DEL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>';
        toolbar.appendChild(mkBtn(ICON_DEL, 'Hapus Kolom Pencarian', function () {
            self._removeMediaResizeHandle();
            if (searchBlock.parentNode) searchBlock.parentNode.removeChild(searchBlock);
            self._closeSearchBlockPopup();
            self._syncSource();
            self._updateState();
        }));

        toolbar.style.position = 'fixed';
        toolbar.style.zIndex = '99995';
        document.body.appendChild(toolbar);
        this._searchBlockPopup = toolbar;

        this._updatePopupPositions();
    };

    RichTextEditor.prototype._closeSearchBlockPopup = function () {
        if (this._searchBlockPopup) {
            if (this._searchBlockPopup.parentNode) this._searchBlockPopup.parentNode.removeChild(this._searchBlockPopup);
            this._searchBlockPopup = null;
        }
    };

    RichTextEditor.prototype._showCarouselEditorPopup = function (carousel) {
        var self = this;
        this._closeImagePopup();
        this._closeVideoPopup();
        this._closeCarouselPopup();
        this._closeButtonPopup();
        this._attachMediaResizeHandle(carousel);

        var toolbar = el('div', { class: 'rte-img-toolbar' });
        var activeMenu = null;
        function closeMenus() { if (activeMenu) { activeMenu.style.display = 'none'; activeMenu = null; } }

        function mkBtn(svgHtml, title, onclick) {
            var b = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            b.innerHTML = svgHtml;
            b.addEventListener('click', function (e) { e.stopPropagation(); closeMenus(); onclick(e); });
            return b;
        }
        function mkDrop(svgHtml, title, items, onOpen) {
            var wrap = el('div', { style: 'position:relative;display:inline-block;' });
            var btn = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            btn.innerHTML = svgHtml + '<svg viewBox="0 0 10 6" style="width:8px;height:8px;margin-left:1px"><polyline points="1,1 5,5 9,1" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
            var menu = el('div', { class: 'rte-img-tb-menu' });
            items.forEach(function (item) {
                if (item === '-') { menu.appendChild(el('div', { style: 'height:1px;background:#eee;margin:3px 0' })); return; }
                var mi = el('button', { type: 'button', class: 'rte-img-tb-menuitem', text: item.label });
                mi._itemData = item;
                mi.addEventListener('click', function (e) { e.stopPropagation(); closeMenus(); menu.style.display = 'none'; item.action(); });
                menu.appendChild(mi);
            });
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = menu.style.display === 'block';
                closeMenus();
                if (!open) { menu.style.display = 'block'; activeMenu = menu; if (onOpen) onOpen(menu); }
            });
            wrap.appendChild(btn); wrap.appendChild(menu);
            return wrap;
        }

        // 0. Edit Media (List)
        var ICON_LIST = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>';
        toolbar.appendChild(mkBtn(ICON_LIST, 'Edit Media Carousel', function () {
            self._dialogCarousel(carousel);
        }));

        // 1. Set Size
        var ICON_SIZE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M3 9h18"/></svg>';
        toolbar.appendChild(mkDrop(ICON_SIZE, 'Set Size', [
            {
                label: 'Set Size\u2026', action: function () {
                    Swal.fire({
                        title: 'Set Carousel Size', html:
                            '<div style="display:flex;gap:8px;justify-content:center">' +
                            '<label style="font-size:13px">W: <input id="swal-car-w" type="number" value="' + (carousel.offsetWidth || 800) + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                            '<label style="font-size:13px">H: <input id="swal-car-h" type="number" value="' + (carousel.offsetHeight || 450) + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                            '</div>',
                        showCancelButton: true, confirmButtonText: 'Apply'
                    }).then(function (r) {
                        if (r.isConfirmed) {
                            var w = parseInt(document.getElementById('swal-car-w').value, 10);
                            var h = parseInt(document.getElementById('swal-car-h').value, 10);
                            if (w > 0) { carousel.style.width = w + 'px'; carousel.style.aspectRatio = 'auto'; }
                            if (h > 0) { carousel.style.height = h + 'px'; carousel.style.aspectRatio = 'auto'; }
                            self._syncSource();
                            setTimeout(function () { self._updatePopupPositions(); }, 10);
                        }
                    });
                }
            },
            '-',
            { label: 'Auto size (16:9)', action: function () { carousel.style.width = ''; carousel.style.height = ''; carousel.style.aspectRatio = '16/9'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '100% width', action: function () { carousel.style.width = '100%'; carousel.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '75% width', action: function () { carousel.style.width = '75%'; carousel.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '50% width', action: function () { carousel.style.width = '50%'; carousel.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '25% width', action: function () { carousel.style.width = '25%'; carousel.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
        ]));

        // 2. Justify
        var ICON_JUSTIFY = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>';
        function setJustify(float, mL, mR) {
            carousel.style.float = float;
            carousel.style.marginLeft = mL;
            carousel.style.marginRight = mR;
            carousel.style.display = (float === 'none' && mL === 'auto') ? 'block' : 'inline-block';
            self._syncSource();
            setTimeout(function () { self._updatePopupPositions(); }, 10);
        }
        toolbar.appendChild(mkDrop(ICON_JUSTIFY, 'Justify', [
            { label: 'Justify Left', action: function () { setJustify('none', '0', 'auto'); } },
            { label: 'Justify Center', action: function () { setJustify('none', 'auto', 'auto'); } },
            { label: 'Justify Right', action: function () { setJustify('none', 'auto', '0'); } },
            '-',
            { label: 'Float Left', action: function () { setJustify('left', '0', '15px'); } },
            { label: 'Float Right', action: function () { setJustify('right', '15px', '0'); } },
        ]));

        // 3. Delete
        var ICON_DEL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>';
        toolbar.appendChild(mkBtn(ICON_DEL, 'Delete Carousel', function () {
            self._removeMediaResizeHandle();
            if (carousel.parentNode) carousel.parentNode.removeChild(carousel);
            self._closeCarouselPopup();
            self._syncSource();
        }));

        // Position toolbar above carousel
        toolbar.style.position = 'fixed';
        toolbar.style.zIndex = '99995';
        document.body.appendChild(toolbar);
        this._carouselPopup = toolbar;

        this._updatePopupPositions();
    };

    RichTextEditor.prototype._closeCarouselPopup = function () {
        if (this._carouselPopup) {
            if (this._carouselPopup.parentNode) this._carouselPopup.parentNode.removeChild(this._carouselPopup);
            this._carouselPopup = null;
        }
    };

    RichTextEditor.prototype._showButtonEditorPopup = function (btnLink) {
        var self = this;
        this._closeImagePopup();
        this._closeVideoPopup();
        this._closeCarouselPopup();
        this._closeButtonPopup();
        this._attachMediaResizeHandle(btnLink);

        var toolbar = el('div', { class: 'rte-img-toolbar' });
        var activeMenu = null;
        function closeMenus() { if (activeMenu) { activeMenu.style.display = 'none'; activeMenu = null; } }

        function mkBtn(svgHtml, title, onclick) {
            var b = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            b.innerHTML = svgHtml;
            b.addEventListener('click', function (e) { e.stopPropagation(); closeMenus(); onclick(e); });
            return b;
        }
        function mkDrop(svgHtml, title, items, onOpen) {
            var wrap = el('div', { style: 'position:relative;display:inline-block;' });
            var btn = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            btn.innerHTML = svgHtml + '<svg viewBox="0 0 10 6" style="width:8px;height:8px;margin-left:1px"><polyline points="1,1 5,5 9,1" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
            var menu = el('div', { class: 'rte-img-tb-menu' });
            items.forEach(function (item) {
                if (item === '-') { menu.appendChild(el('div', { style: 'height:1px;background:#eee;margin:3px 0' })); return; }
                var mi = el('button', { type: 'button', class: 'rte-img-tb-menuitem', text: item.label });
                mi._itemData = item;
                mi.addEventListener('click', function (e) { e.stopPropagation(); closeMenus(); menu.style.display = 'none'; item.action(); });
                menu.appendChild(mi);
            });
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = menu.style.display === 'block';
                closeMenus();
                if (!open) { menu.style.display = 'block'; activeMenu = menu; if (onOpen) onOpen(menu); }
            });
            wrap.appendChild(btn); wrap.appendChild(menu);
            return wrap;
        }

        // 1. Set Size
        var ICON_SIZE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M3 9h18"/></svg>';
        var bSizeItems = [
            {
                label: 'Set Size\u2026', get: function () { return false; }, action: function () {
                    Swal.fire({
                        title: 'Set Button Size', html:
                            '<div style="display:flex;gap:8px;justify-content:center">' +
                            '<label style="font-size:13px">W: <input id="swal-btn-w" type="number" value="' + (btnLink.offsetWidth || '') + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                            '<label style="font-size:13px">H: <input id="swal-btn-h" type="number" value="' + (btnLink.offsetHeight || '') + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                            '</div>',
                        showCancelButton: true, confirmButtonText: 'Apply'
                    }).then(function (r) {
                        if (r.isConfirmed) {
                            var w = parseInt(document.getElementById('swal-btn-w').value, 10);
                            var h = parseInt(document.getElementById('swal-btn-h').value, 10);
                            if (w > 0) btnLink.style.width = w + 'px';
                            if (h > 0) btnLink.style.height = h + 'px';
                            self._syncSource();
                            setTimeout(function () { self._updatePopupPositions(); }, 10);
                        }
                    });
                }
            },
            '-',
            { label: 'Auto size', get: function () { return !btnLink.style.width && !btnLink.style.height; }, action: function () { btnLink.style.width = ''; btnLink.style.height = ''; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '100% width', get: function () { return btnLink.style.width === '100%'; }, action: function () { btnLink.style.width = '100%'; btnLink.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '75% width', get: function () { return btnLink.style.width === '75%'; }, action: function () { btnLink.style.width = '75%'; btnLink.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '50% width', get: function () { return btnLink.style.width === '50%'; }, action: function () { btnLink.style.width = '50%'; btnLink.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
            { label: '25% width', get: function () { return btnLink.style.width === '25%'; }, action: function () { btnLink.style.width = '25%'; btnLink.style.height = 'auto'; self._syncSource(); setTimeout(function () { self._updatePopupPositions(); }, 10); } },
        ];
        toolbar.appendChild(mkDrop(ICON_SIZE, 'Set Size', bSizeItems, function (menu) {
            menu.querySelectorAll('.rte-img-tb-menuitem').forEach(function (mi) {
                var item = mi._itemData;
                if (item && item.get) mi.classList.toggle('rte-img-tb-menuitem-active', !!item.get());
            });
        }));

        // 2. Justify
        var ICON_JUSTIFY = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>';
        function setBJustify(float, mL, mR, disp) {
            btnLink.style.float = float;
            btnLink.style.marginLeft = mL;
            btnLink.style.marginRight = mR;
            if (disp === 'block') {
                btnLink.style.display = 'flex';
                if (!btnLink.style.width) btnLink.style.width = 'fit-content';
            } else {
                btnLink.style.display = 'inline-flex';
                if (btnLink.style.width === 'fit-content') btnLink.style.width = '';
            }
            self._syncSource();
            setTimeout(function () { self._updatePopupPositions(); }, 10);
        }
        var bJustifyItems = [
            { label: 'Justify Left', get: function () { return btnLink.style.float !== 'left' && btnLink.style.float !== 'right' && btnLink.style.marginLeft !== 'auto'; }, action: function () { setBJustify('none', '0', 'auto', 'inline-flex'); } },
            { label: 'Justify Center', get: function () { return btnLink.style.float !== 'left' && btnLink.style.float !== 'right' && btnLink.style.marginLeft === 'auto' && btnLink.style.marginRight === 'auto'; }, action: function () { setBJustify('none', 'auto', 'auto', 'block'); } },
            { label: 'Justify Right', get: function () { return btnLink.style.float !== 'left' && btnLink.style.float !== 'right' && btnLink.style.marginLeft === 'auto' && (btnLink.style.marginRight === '0px' || btnLink.style.marginRight === '0'); }, action: function () { setBJustify('none', 'auto', '0', 'inline-flex'); } },
            '-',
            { label: 'Float Left', get: function () { return btnLink.style.float === 'left'; }, action: function () { setBJustify('left', '0', '10px', 'inline-flex'); } },
            { label: 'Float Right', get: function () { return btnLink.style.float === 'right'; }, action: function () { setBJustify('right', '10px', '0', 'inline-flex'); } },
        ];
        toolbar.appendChild(mkDrop(ICON_JUSTIFY, 'Justify', bJustifyItems, function (menu) {
            menu.querySelectorAll('.rte-img-tb-menuitem').forEach(function (mi) {
                var item = mi._itemData;
                if (item && item.get) mi.classList.toggle('rte-img-tb-menuitem-active', !!item.get());
            });
        }));

        // 3. Edit Button Details
        var ICON_EDIT = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4z"/></svg>';
        toolbar.appendChild(mkBtn(ICON_EDIT, 'Edit Button Details', function () {
            self._closeButtonPopup();
            self._dialogLink(btnLink);
        }));

        // 4. Delete
        var ICON_DEL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>';
        toolbar.appendChild(mkBtn(ICON_DEL, 'Delete Button', function () {
            self._removeMediaResizeHandle();
            if (btnLink.parentNode) btnLink.parentNode.removeChild(btnLink);
            self._closeButtonPopup();
            self._syncSource();
        }));

        toolbar.style.position = 'fixed';
        toolbar.style.zIndex = '99995';
        document.body.appendChild(toolbar);
        this._buttonPopup = toolbar;

        this._updatePopupPositions();
    };

    RichTextEditor.prototype._closeButtonPopup = function () {
        if (this._buttonPopup) {
            if (this._buttonPopup.parentNode) this._buttonPopup.parentNode.removeChild(this._buttonPopup);
            this._buttonPopup = null;
        }
    };

    // -------- Line Height --------
    RichTextEditor.prototype._setLineHeight = function (value) {
        // Use savedRange when available (called from dropdown after focus left editor)
        var node = null;
        if (this._savedRange && this._savedRange.startContainer) {
            node = this._savedRange.startContainer;
        } else {
            var sel = window.getSelection();
            if (!sel || sel.rangeCount === 0) return;
            node = sel.anchorNode;
        }
        if (!node) return;
        // Walk up to find a block element
        var el = node.nodeType === Node.TEXT_NODE ? node.parentElement : node;
        while (el && el !== this.content) {
            var tag = el.tagName;
            if (tag && /^(P|DIV|H[1-6]|LI|TR|BLOCKQUOTE|PRE)$/.test(tag)) break;
            el = el.parentElement;
        }
        if (el && el !== this.content) {
            el.style.lineHeight = (value / 100);
        }
        this._syncSource();
    };

    RichTextEditor.prototype._doPasteFormat = function () {
        if (!this._formatCopied || !this._copiedStyles) {
            Swal.fire({ title: 'Gagal', text: 'Klik "Salin Format" pada teks yang sudah diformat terlebih dahulu.', icon: 'warning', confirmButtonText: 'OK' });
            return;
        }
        var self = this;
        var sel = window.getSelection();
        var hasActiveSelection = sel && sel.rangeCount > 0 && !sel.isCollapsed && this.content.contains(sel.getRangeAt(0).commonAncestorContainer);
        if (!hasActiveSelection) {
            var rangeToRestore = this._lastNonCollapsedRange || this._savedRange;
            if (rangeToRestore) {
                restoreSelection(rangeToRestore);
            }
            sel = window.getSelection();
        }
        if (!sel || sel.rangeCount === 0 || sel.isCollapsed) {
            Swal.fire({ title: 'Gagal', text: 'Pilih teks untuk menerapkan format.', icon: 'warning', confirmButtonText: 'OK' });
            return;
        }
        var targetRange = sel.getRangeAt(0);
        var markerStart = null, markerEnd = null;
        try {
            var sourceStyles = this._copiedStyles;

            // Insert markers to preserve the selection boundaries during DOM modifications
            markerStart = document.createElement('span');
            markerStart.id = 'rte-paste-marker-start';
            markerStart.style.display = 'none';

            markerEnd = document.createElement('span');
            markerEnd.id = 'rte-paste-marker-end';
            markerEnd.style.display = 'none';

            var rangeClone = targetRange.cloneRange();
            rangeClone.collapse(false);
            rangeClone.insertNode(markerEnd);

            rangeClone.setStart(targetRange.startContainer, targetRange.startOffset);
            rangeClone.collapse(true);
            rangeClone.insertNode(markerStart);

            // 1. Convert containing blocks in the editor document if blockTagName is specified
            if (sourceStyles.blockTagName) {
                var blocksToConvert = [];
                function isBlockEl(el) {
                    return el && ['P', 'DIV', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'PRE'].indexOf(el.tagName.toUpperCase()) !== -1;
                }
                function getContainingBlock(node) {
                    var curr = node;
                    while (curr && curr !== self.content) {
                        if (isBlockEl(curr)) return curr;
                        curr = curr.parentNode;
                    }
                    return null;
                }
                var startBlock = getContainingBlock(markerStart);
                var endBlock = getContainingBlock(markerEnd);

                if (startBlock && endBlock) {
                    var allBlocks = Array.prototype.slice.call(self.content.querySelectorAll('p, div, h1, h2, h3, h4, h5, h6, blockquote, pre'));
                    var inRange = false;
                    allBlocks.forEach(function (block) {
                        if (block === startBlock || block === endBlock) {
                            blocksToConvert.push(block);
                            if (startBlock !== endBlock) {
                                inRange = !inRange;
                            }
                        } else if (inRange) {
                            blocksToConvert.push(block);
                        }
                    });
                } else if (startBlock) {
                    blocksToConvert.push(startBlock);
                } else if (endBlock) {
                    blocksToConvert.push(endBlock);
                }

                blocksToConvert.forEach(function (oldBlock) {
                    var newTag = sourceStyles.blockTagName;
                    var newBlock = oldBlock;
                    if (oldBlock.tagName.toUpperCase() !== newTag) {
                        newBlock = document.createElement(newTag);
                        for (var ai = 0; ai < oldBlock.attributes.length; ai++) {
                            var attr = oldBlock.attributes[ai];
                            newBlock.setAttribute(attr.name, attr.value);
                        }
                        while (oldBlock.firstChild) {
                            newBlock.appendChild(oldBlock.firstChild);
                        }
                        if (oldBlock.parentNode) {
                            oldBlock.parentNode.replaceChild(newBlock, oldBlock);
                        }
                    }
                    if (sourceStyles.textAlign && sourceStyles.textAlign !== 'normal' && sourceStyles.textAlign !== 'start') {
                        newBlock.style.textAlign = sourceStyles.textAlign;
                    }
                    if (sourceStyles.lineHeight && sourceStyles.lineHeight !== 'normal') {
                        newBlock.style.lineHeight = sourceStyles.lineHeight;
                    }
                });
            }

            // Construct format range between the markers
            var formatRange = document.createRange();
            formatRange.setStartAfter(markerStart);
            formatRange.setEndBefore(markerEnd);

            var frag = formatRange.extractContents();

            // 2. Convert blocks inside the extracted fragment if blockTagName is specified
            if (sourceStyles.blockTagName) {
                var fragBlocks = Array.prototype.slice.call(frag.querySelectorAll('p, div, h1, h2, h3, h4, h5, h6, blockquote, pre'));
                fragBlocks.forEach(function (oldBlock) {
                    var newTag = sourceStyles.blockTagName;
                    if (oldBlock.tagName.toUpperCase() !== newTag) {
                        var newBlock = document.createElement(newTag);
                        for (var ai = 0; ai < oldBlock.attributes.length; ai++) {
                            var attr = oldBlock.attributes[ai];
                            newBlock.setAttribute(attr.name, attr.value);
                        }
                        while (oldBlock.firstChild) {
                            newBlock.appendChild(oldBlock.firstChild);
                        }
                        if (oldBlock.parentNode) {
                            oldBlock.parentNode.replaceChild(newBlock, oldBlock);
                        }
                    }
                });
            }

            // Unwrap existing conflicting inline formatting tags in the extracted fragment safely
            var inlineTags = Array.prototype.slice.call(frag.querySelectorAll('b, strong, i, em, u, s, strike, sub, sup, span, font, a'));
            inlineTags.forEach(function (el) {
                var parent = el.parentNode;
                if (!parent) return;
                var isChild = false;
                for (var i = 0; i < parent.childNodes.length; i++) {
                    if (parent.childNodes[i] === el) {
                        isChild = true;
                        break;
                    }
                }
                if (!isChild) return;
                while (el.firstChild) {
                    parent.insertBefore(el.firstChild, el);
                }
                parent.removeChild(el);
            });

            // Clean up any inline styles on links
            var links = frag.querySelectorAll('a');
            links.forEach(function (a) {
                a.removeAttribute('style');
            });

            function createFormatWrapper(styles, contentToWrap) {
                var span = document.createElement('span');
                var css = [];
                if (styles.color && styles.color !== 'inherit') css.push('color: ' + styles.color);
                if (styles.backgroundColor && styles.backgroundColor !== 'rgba(0, 0, 0, 0)' && styles.backgroundColor !== 'transparent' && styles.backgroundColor !== 'inherit') css.push('background-color: ' + styles.backgroundColor);
                if (styles.fontSize && styles.fontSize !== 'inherit') css.push('font-size: ' + styles.fontSize);
                if (styles.fontFamily && styles.fontFamily !== 'inherit') css.push('font-family: ' + styles.fontFamily);
                if (css.length > 0) span.style.cssText = css.join(';');

                span.appendChild(contentToWrap);

                var root = span;
                if (styles.isBold) { var b = document.createElement('b'); b.appendChild(root); root = b; }
                if (styles.isItalic) { var i = document.createElement('i'); i.appendChild(root); root = i; }
                if (styles.isUnderline) { var u = document.createElement('u'); u.appendChild(root); root = u; }
                if (styles.isStrike) { var s = document.createElement('s'); s.appendChild(root); root = s; }
                if (styles.isSub) { var sub = document.createElement('sub'); sub.appendChild(root); root = sub; }
                if (styles.isSup) { var sup = document.createElement('sup'); sup.appendChild(root); root = sup; }

                if (styles.linkHref) {
                    var a = document.createElement('a');
                    a.setAttribute('href', styles.linkHref);
                    if (styles.linkTarget) a.setAttribute('target', styles.linkTarget);
                    a.appendChild(root);
                    root = a;
                }
                return root;
            }

            var blocks = frag.querySelectorAll('p, div, li, td, th, h1, h2, h3, h4, h5, h6, blockquote, pre');
            var firstChild = blocks.length > 0 ? frag.firstChild : null;
            var lastChild = blocks.length > 0 ? frag.lastChild : null;

            if (blocks.length > 0) {
                blocks.forEach(function (block) {
                    var tempFrag = document.createDocumentFragment();
                    while (block.firstChild) {
                        tempFrag.appendChild(block.firstChild);
                    }
                    var wrapped = createFormatWrapper(sourceStyles, tempFrag);
                    block.appendChild(wrapped);
                    if (sourceStyles.textAlign && sourceStyles.textAlign !== 'normal' && sourceStyles.textAlign !== 'start') {
                        block.style.textAlign = sourceStyles.textAlign;
                    }
                    if (sourceStyles.lineHeight && sourceStyles.lineHeight !== 'normal') {
                        block.style.lineHeight = sourceStyles.lineHeight;
                    }
                });
                formatRange.insertNode(frag);
            } else {
                var wrapped = createFormatWrapper(sourceStyles, frag);
                firstChild = wrapped;
                lastChild = wrapped;
                formatRange.insertNode(wrapped);
            }

            // Remove markers
            if (markerStart && markerStart.parentNode) markerStart.parentNode.removeChild(markerStart);
            if (markerEnd && markerEnd.parentNode) markerEnd.parentNode.removeChild(markerEnd);

            if (lastChild) {
                var range = document.createRange();
                range.setStartAfter(lastChild);
                range.collapse(true);
                sel.removeAllRanges();
                sel.addRange(range);
            }

            self._syncSource();
            self._updateState();
        } catch (e) {
            console.warn('[RTE] Paste format error', e);
            if (markerStart && markerStart.parentNode) markerStart.parentNode.removeChild(markerStart);
            if (markerEnd && markerEnd.parentNode) markerEnd.parentNode.removeChild(markerEnd);
        }
    };

    // -------- Insert Code Block --------
    RichTextEditor.prototype._dialogCode = function () {
        var self = this;
        var LANGUAGES = [
            'plain text', 'javascript', 'typescript', 'python', 'php',
            'html', 'css', 'sql', 'bash', 'json', 'xml', 'java', 'c', 'cpp', 'csharp', 'ruby', 'go', 'rust', 'swift', 'kotlin',
        ];
        var langSelect = el('select', { class: 'rte-form-input', style: 'margin-bottom:8px;' });
        LANGUAGES.forEach(function (lang) {
            langSelect.appendChild(el('option', { value: lang, text: lang }));
        });
        var codeArea = el('textarea', {
            class: 'rte-source',
            style: 'width:100%;min-height:180px;background:#1e2230;color:#e6e6e6;padding:12px;border-radius:6px;resize:vertical;font-family:ui-monospace,SFMono-Regular,Consolas,"Liberation Mono",Menlo,monospace;font-size:13px;line-height:1.5;border:1px solid #d6d8dc;',
            placeholder: 'Paste or type your code here...',
        });
        var body = el('div', { class: 'rte-form' }, [
            el('label', { class: 'rte-form-label', text: 'Language' }),
            langSelect,
            el('label', { class: 'rte-form-label', text: 'Code' }),
            codeArea,
        ]);
        openModal({
            title: 'Insert Code Block',
            body: body,
            confirmLabel: 'Insert',
            wide: true,
            onConfirm: function () {
                var lang = langSelect.value;
                var code = codeArea.value;
                if (!code.trim()) return false;
                var langClass = lang !== 'plain text' ? ' class="language-' + lang.replace(/\s+/g, '-') + '"' : '';
                var escaped = escapeHtml(code);

                var containerHtml = '<div class="rte-code-block-container" contenteditable="false">';
                containerHtml += '<div class="rte-code-block-header" contenteditable="false">';
                containerHtml += '<span class="rte-code-block-lang">' + escapeHtml(lang) + '</span>';
                containerHtml += '<button class="rte-code-block-copy-btn" type="button" onclick="var btn=this; var codeEl=btn.closest(\'.rte-code-block-container\').querySelector(\'code\'); if(codeEl){ navigator.clipboard.writeText(codeEl.textContent).then(function(){ btn.textContent=\'Copied!\'; setTimeout(function(){ btn.textContent=\'Copy\'; }, 2000); }); }">Copy</button>';
                containerHtml += '<button class="rte-code-block-delete-btn" type="button">Delete</button>';
                containerHtml += '</div>';
                containerHtml += '<pre' + langClass + ' contenteditable="true"><code>' + escaped + '</code></pre>';
                containerHtml += '</div><p><br></p>';

                self._insertHTML(containerHtml);
                setTimeout(function () { self._loadPrism(); }, 50);
            },
        });
        setTimeout(function () { codeArea.focus(); }, 30);
    };

    // -------- Insert Document --------
    RichTextEditor.prototype._dialogInsertDocument = function () {
        var self = this;
        var tabs = el('div', { class: 'rte-tabs' });
        var tabUpload = el('button', { type: 'button', class: 'rte-tab rte-tab-active', text: 'Upload File' });
        var tabUrl = el('button', { type: 'button', class: 'rte-tab', text: 'URL' });
        tabs.appendChild(tabUpload);
        tabs.appendChild(tabUrl);

        var paneUpload = el('div', { class: 'rte-form rte-tab-pane rte-tab-pane-active' }, [
            el('label', { class: 'rte-form-label', text: 'Choose document (PDF, DOC, DOCX, XLS, PPT, TXT)' }),
            el('input', { type: 'file', class: 'rte-form-input', name: 'file', accept: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf' }),
            el('label', { class: 'rte-form-label', text: 'Display name' }),
            el('input', { type: 'text', class: 'rte-form-input', name: 'label', placeholder: 'e.g. Annual Report 2024' }),
        ]);
        var paneUrl = el('div', { class: 'rte-form rte-tab-pane' }, [
            el('label', { class: 'rte-form-label', text: 'Document URL' }),
            el('input', { type: 'url', class: 'rte-form-input', name: 'url', placeholder: 'https://example.com/document.pdf' }),
            el('label', { class: 'rte-form-label', text: 'Display name' }),
            el('input', { type: 'text', class: 'rte-form-input', name: 'label', placeholder: 'e.g. Annual Report 2024' }),
        ]);

        tabUpload.addEventListener('click', function () {
            tabUpload.classList.add('rte-tab-active'); tabUrl.classList.remove('rte-tab-active');
            paneUpload.classList.add('rte-tab-pane-active'); paneUrl.classList.remove('rte-tab-pane-active');
        });
        tabUrl.addEventListener('click', function () {
            tabUrl.classList.add('rte-tab-active'); tabUpload.classList.remove('rte-tab-active');
            paneUrl.classList.add('rte-tab-pane-active'); paneUpload.classList.remove('rte-tab-pane-active');
        });

        var body = el('div', null, [tabs, paneUpload, paneUrl]);
        openModal({
            title: 'Insert Document',
            body: body,
            confirmLabel: 'Insert',
            onConfirm: function () {
                if (paneUpload.classList.contains('rte-tab-pane-active')) {
                    var fileInput = paneUpload.querySelector('[name=file]');
                    var labelInput = paneUpload.querySelector('[name=label]');
                    var f = fileInput.files && fileInput.files[0];
                    if (!f) return false;
                    self._uploadFile(f, function (url) {
                        var name = labelInput.value.trim() || f.name;
                        var ext = f.name.split('.').pop().toLowerCase();
                        var icon = self._fileIcon(ext);
                        self._insertHTML('<a href="' + escapeHtml(url) + '" target="_blank" style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:#f1f3f6;border:1px solid #d6d8dc;border-radius:6px;text-decoration:none;color:#1a1a1a;font-size:14px;margin:4px 0;"><span style="font-size:22px;">' + icon + '</span><span>' + escapeHtml(name) + '</span></a><p><br></p>');
                    });
                    return; // async — keep open
                } else {
                    var url = paneUrl.querySelector('[name=url]').value.trim();
                    var label = paneUrl.querySelector('[name=label]').value.trim() || url;
                    if (!url) return false;
                    var ext = url.split('.').pop().split('?')[0].toLowerCase();
                    var icon = self._fileIcon(ext);
                    self._insertHTML('<a href="' + escapeHtml(url) + '" target="_blank" style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:#f1f3f6;border:1px solid #d6d8dc;border-radius:6px;text-decoration:none;color:#1a1a1a;font-size:14px;margin:4px 0;"><span style="font-size:22px;">' + icon + '</span><span>' + escapeHtml(label) + '</span></a><p><br></p>');
                }
            },
        });
    };

    RichTextEditor.prototype._fileIcon = function (ext) {
        var icons = {
            pdf: '📄', doc: '📝', docx: '📝', xls: '📊', xlsx: '📊',
            ppt: '📽', pptx: '📽', txt: '📃', rtf: '📃',
            zip: '🗜', rar: '🗜', jpg: '🖼', jpeg: '🖼', png: '🖼', gif: '🖼',
        };
        return icons[ext] || '📎';
    };

    RichTextEditor.prototype._uploadFile = function (file, cb, errCb) {
        var self = this;
        self.showLoading();
        var handler = this.config.file_upload_handler;
        if (typeof handler === 'function') {
            handler(file, function (url) { self.hideLoading(); try { cb(url); } catch (e) { console.error("Error in upload callback:", e); } }, function (err) { self.hideLoading(); if (errCb) errCb(err); });
        } else {
            self.hideLoading();
            alert("File upload not configured.");
            if (errCb) errCb();
        }
    };

    // ===================================================================
    // TABLE SELECTION OVERLAY — 8-point resize handles + drag-to-move
    // ===================================================================
    RichTextEditor.prototype._showTableSelection = function (table) {
        var self = this;

        // Hide table selection if clicking elsewhere
        this._hideTableSelection();

        // Destroy existing overlay + toolbar cleanly
        this._hideTableSelection();

        if (!table || !this.content.contains(table)) return;

        // Create overlay wrapper (positioned relative to editor content)
        var cWrap = this.content.parentElement;
        var wrapRect = cWrap.getBoundingClientRect();
        var tblRect = table.getBoundingClientRect();

        var overlay = el('div', {
            class: 'rte-table-overlay',
            style: [
                'left:' + (tblRect.left - wrapRect.left + cWrap.scrollLeft) + 'px',
                'top:' + (tblRect.top - wrapRect.top + cWrap.scrollTop) + 'px',
                'width:' + tblRect.width + 'px',
                'height:' + tblRect.height + 'px',
                'pointer-events:none',
                'overflow:visible',
            ].join(';')
        });

        // ---- Move handle (top-left) ----
        var moveHandle = el('div', {
            class: 'rte-table-move-handle',
            title: 'Drag to move table',
            style: 'pointer-events:auto;'
        });
        moveHandle.innerHTML = ICON.gripIcon;
        overlay.appendChild(moveHandle);

        // ---- 8-point resize handles ----
        var handles = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];
        handles.forEach(function (h) {
            var handle = el('div', {
                class: 'rte-table-resize-handle rte-rsz-' + h,
                style: 'pointer-events:auto;'
            });
            overlay.appendChild(handle);
        });

        // Append into content area so it scrolls with content
        this.content.parentElement.appendChild(overlay);
        this._tableOverlay = overlay;
        this._selectedTable = table;

        // ---- Column Resizers (vertical handles on borders) ----
        this._buildTableColumnResizers(table, overlay);

        // ---- Move table by dragging the handle ----
        var moveStartX, moveStartY, tblOrigLeft, tblOrigTop;
        var hasDragged = false;

        function onMoveStart(e) {
            e.preventDefault();
            e.stopPropagation();
            self._isResizingOrMoving = true;
            moveStartX = e.touches ? e.touches[0].clientX : e.clientX;
            moveStartY = e.touches ? e.touches[0].clientY : e.clientY;
            tblOrigLeft = parseFloat(table.style.marginLeft) || 0;
            tblOrigTop = parseFloat(table.style.marginTop) || 0;
            hasDragged = false;

            document.addEventListener('mousemove', onMoveMove);
            document.addEventListener('mouseup', onMoveEnd);
            document.addEventListener('touchmove', onMoveMove, { passive: false });
            document.addEventListener('touchend', onMoveEnd);
        }

        function onMoveMove(e) {
            e.preventDefault();
            var dx = (e.touches ? e.touches[0].clientX : e.clientX) - moveStartX;
            var dy = (e.touches ? e.touches[0].clientY : e.clientY) - moveStartY;
            if (Math.abs(dx) > 3 || Math.abs(dy) > 3) {
                hasDragged = true;
            }
            table.style.marginLeft = (tblOrigLeft + dx) + 'px';
            table.style.marginTop = (tblOrigTop + dy) + 'px';
            self._updateTableOverlayPosition();
        }

        function onMoveEnd() {
            self._isResizingOrMoving = false;
            document.removeEventListener('mousemove', onMoveMove);
            document.removeEventListener('mouseup', onMoveEnd);
            document.removeEventListener('touchmove', onMoveMove);
            document.removeEventListener('touchend', onMoveEnd);
            self._syncSource();

            if (!hasDragged) {
                // User just clicked the handle! Select all cells in the table
                self._clearCellSelection(table);
                table.querySelectorAll('td, th').forEach(function (cell) {
                    cell.classList.add('rte-cell-selected');
                });
            }
        }

        moveHandle.addEventListener('mousedown', onMoveStart);
        moveHandle.addEventListener('touchstart', onMoveStart, { passive: false });

        // ---- Resize table by dragging handles ----
        var resizeDir = null;
        var resizeStartX, resizeStartY, resizeStartW, resizeStartH;
        var originalCellWidths = [];

        function onResizeStart(e, dir) {
            e.preventDefault();
            e.stopPropagation();
            self._isResizingOrMoving = true;
            resizeDir = dir;
            resizeStartX = e.touches ? e.touches[0].clientX : e.clientX;
            resizeStartY = e.touches ? e.touches[0].clientY : e.clientY;

            // Force table layout fixed to allow sizing
            table.style.tableLayout = 'fixed';

            resizeStartW = table.offsetWidth;
            resizeStartH = table.offsetHeight;

            originalCellWidths = [];
            if (table.rows.length > 0) {
                Array.from(table.rows[0].cells).forEach(function (cell) {
                    originalCellWidths.push({
                        cell: cell,
                        width: parseFloat(cell.style.width) || cell.offsetWidth
                    });
                });
            }

            document.addEventListener('mousemove', onResizeMove);
            document.addEventListener('mouseup', onResizeEnd);
            document.addEventListener('touchmove', onResizeMove, { passive: false });
            document.addEventListener('touchend', onResizeEnd);
        }

        function onResizeMove(e) {
            if (!resizeDir) return;
            e.preventDefault();
            var dx = (e.touches ? e.touches[0].clientX : e.clientX) - resizeStartX;
            var dy = (e.touches ? e.touches[0].clientY : e.clientY) - resizeStartY;
            var newW = resizeStartW;
            var newH = resizeStartH;
            var h = resizeDir;

            if (h === 'e' || h === 'ne' || h === 'se') newW = Math.max(80, resizeStartW + dx);
            if (h === 'w' || h === 'nw' || h === 'sw') newW = Math.max(80, resizeStartW - dx);
            if (h === 's' || h === 'se' || h === 'sw') newH = Math.max(60, resizeStartH + dy);
            if (h === 'n' || h === 'ne' || h === 'nw') newH = Math.max(60, resizeStartH - dy);

            table.style.width = newW + 'px';
            table.style.height = newH + 'px';

            var overallRatio = newW / resizeStartW;
            originalCellWidths.forEach(function (item) {
                if (item.cell.style.width && item.cell.style.width.indexOf('%') !== -1) {
                    // percentage-based, automatically resizes with table
                } else {
                    item.cell.style.width = Math.max(20, item.width * overallRatio) + 'px';
                }
            });

            self._updateTableOverlayPosition();
        }

        function onResizeEnd() {
            resizeDir = null;
            self._isResizingOrMoving = false;
            document.removeEventListener('mousemove', onResizeMove);
            document.removeEventListener('mouseup', onResizeEnd);
            document.removeEventListener('touchmove', onResizeMove);
            document.removeEventListener('touchend', onResizeEnd);
            self._syncSource();
        }

        handles.forEach(function (h) {
            var handle = overlay.querySelector('.rte-rsz-' + h);
            if (handle) {
                handle.addEventListener('mousedown', function (e) { onResizeStart(e, h); });
                handle.addEventListener('touchstart', function (e) { onResizeStart(e, h); }, { passive: false });
            }
        });

        // ---- Show float table toolbar (only here — not in _refreshTableSelection) ----
        this._showFloatTableToolbar(table);
    };
    RichTextEditor.prototype._buildTableColumnResizers = function (table, overlay) {
        var self = this;
        var rows = table.rows;
        if (!rows.length) return;
        var cells = Array.prototype.slice.call(rows[0].cells);
        var tblRect = table.getBoundingClientRect();

        // Clear existing column resizers from overlay
        Array.prototype.slice.call(overlay.querySelectorAll('.rte-table-col-resizer')).forEach(function (r) { r.parentNode.removeChild(r); });

        for (var i = 0; i < cells.length; i++) {
            (function (idx) {
                var cell = cells[idx];
                var cellRect = cell.getBoundingClientRect();
                var right = cellRect.right - tblRect.left;

                // Create a vertical line at the right border of the cell
                var resizer = el('div', {
                    class: 'rte-table-col-resizer',
                    style: 'left:' + right + 'px; top:0; bottom:0; width:6px; margin-left:-3px; cursor:col-resize; position:absolute; pointer-events:auto; z-index:10;'
                });
                overlay.appendChild(resizer);

                var startX, startW, startWNext;
                var nextCell = cells[idx + 1];

                function onColResizeStart(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self._isResizingOrMoving = true;
                    startX = e.touches ? e.touches[0].clientX : e.clientX;

                    // Clear widths on all other rows to avoid layout conflicts
                    for (var r = 1; r < table.rows.length; r++) {
                        Array.prototype.slice.call(table.rows[r].cells).forEach(function (c) {
                            c.style.width = '';
                        });
                    }

                    // Convert all first-row cells to pixel widths for stable dragging
                    var tblPixelW = table.offsetWidth || 500;
                    var totalOffsetW = 0;
                    cells.forEach(function (c) {
                        totalOffsetW += c.offsetWidth;
                    });
                    cells.forEach(function (c) {
                        var w = totalOffsetW > 0 ? (c.offsetWidth / totalOffsetW) * tblPixelW : c.offsetWidth;
                        c.style.width = w.toFixed(0) + 'px';
                    });

                    startW = cell.offsetWidth;
                    startWNext = nextCell ? nextCell.offsetWidth : null;

                    document.addEventListener('mousemove', onColResizeMove);
                    document.addEventListener('mouseup', onColResizeEnd);
                    document.addEventListener('touchmove', onColResizeMove, { passive: false });
                    document.addEventListener('touchend', onColResizeEnd);
                    document.body.classList.add('rte-resizing-col');
                }

                function onColResizeMove(e) {
                    var cx = e.touches ? e.touches[0].clientX : e.clientX;
                    var dx = cx - startX;

                    table.style.tableLayout = 'fixed';

                    if (nextCell) {
                        var newW = Math.max(10, startW + dx);
                        var diff = newW - startW;
                        var newWNext = Math.max(10, startWNext - diff);
                        var finalDiff = startWNext - newWNext;
                        cell.style.width = (startW + finalDiff) + 'px';
                        nextCell.style.width = newWNext + 'px';
                    } else {
                        var newW = Math.max(10, startW + dx);
                        cell.style.width = newW + 'px';
                        var diff = newW - startW;
                        var tblW = table.offsetWidth;
                        table.style.width = (tblW + diff) + 'px';
                    }

                    self._updateTableOverlayPosition();
                }

                function onColResizeEnd() {
                    self._isResizingOrMoving = false;
                    document.removeEventListener('mousemove', onColResizeMove);
                    document.removeEventListener('mouseup', onColResizeEnd);
                    document.removeEventListener('touchmove', onColResizeMove);
                    document.removeEventListener('touchend', onColResizeEnd);
                    document.body.classList.remove('rte-resizing-col');

                    self._normalizeColumnWidths(table);
                    self._syncSource();
                }

                resizer.addEventListener('mousedown', onColResizeStart);
                resizer.addEventListener('touchstart', onColResizeStart, { passive: false });
            })(i);
        }
    };

    RichTextEditor.prototype._hideTableSelection = function () {
        if (this._selectedTable) {
            Array.from(this._selectedTable.querySelectorAll('.rte-col-resizer')).forEach(function (r) { r.parentNode.removeChild(r); });
        }
        if (this._tableOverlay) {
            if (this._tableOverlay.parentNode) this._tableOverlay.parentNode.removeChild(this._tableOverlay);
            this._tableOverlay = null;
        }
        this._selectedTable = null;
        this._hideFloatTableToolbar();
    };

    RichTextEditor.prototype._updateTableOverlayPosition = function () {
        var overlay = this._tableOverlay;
        var table = this._selectedTable;
        if (!overlay || !table) return;
        var cWrap = this.content.parentElement;
        var wrapRect = cWrap.getBoundingClientRect();
        var tblRect = table.getBoundingClientRect();
        overlay.style.left = (tblRect.left - wrapRect.left + cWrap.scrollLeft) + 'px';
        overlay.style.top = (tblRect.top - wrapRect.top + cWrap.scrollTop) + 'px';
        overlay.style.width = tblRect.width + 'px';
        overlay.style.height = tblRect.height + 'px';

        // Refresh column resizers
        this._buildTableColumnResizers(table, overlay);

        // Also reposition the float toolbar that follows the table
        this._repositionFloatTableToolbar(table);
    };

    RichTextEditor.prototype._updatePopupPositions = function () {
        if (this._imagePopup && this._mediaResizeTarget) {
            var rect = this._mediaResizeTarget.getBoundingClientRect();
            var tbW = this._imagePopup.offsetWidth || 240;
            var left = Math.min(Math.max(rect.left + rect.width / 2 - tbW / 2, 4), window.innerWidth - tbW - 4);
            var top = rect.top - 44;
            if (top < 4) top = rect.bottom + 4;
            this._imagePopup.style.left = left + 'px';
            this._imagePopup.style.top = top + 'px';
        }
        if (this._videoPopup && this._mediaResizeTarget) {
            var rectV = this._mediaResizeTarget.getBoundingClientRect();
            var tbWV = this._videoPopup.offsetWidth || 180;
            var leftV = Math.min(Math.max(rectV.left + rectV.width / 2 - tbWV / 2, 4), window.innerWidth - tbWV - 4);
            var topV = rectV.top - 44;
            if (topV < 4) topV = rectV.bottom + 4;
            this._videoPopup.style.left = leftV + 'px';
            this._videoPopup.style.top = topV + 'px';
        }
        if (this._carouselPopup && this._mediaResizeTarget) {
            var rectC = this._mediaResizeTarget.getBoundingClientRect();
            var tbWC = this._carouselPopup.offsetWidth || 180;
            var leftC = Math.min(Math.max(rectC.left + rectC.width / 2 - tbWC / 2, 4), window.innerWidth - tbWC - 4);
            var topC = rectC.top - 44;
            if (topC < 4) topC = rectC.bottom + 4;
            this._carouselPopup.style.left = leftC + 'px';
            this._carouselPopup.style.top = topC + 'px';
        }
        if (this._buttonPopup && this._mediaResizeTarget) {
            var rectB = this._mediaResizeTarget.getBoundingClientRect();
            var tbWB = this._buttonPopup.offsetWidth || 180;
            var leftB = Math.min(Math.max(rectB.left + rectB.width / 2 - tbWB / 2, 4), window.innerWidth - tbWB - 4);
            var topB = rectB.top - 44;
            if (topB < 4) topB = rectB.bottom + 4;
            this._buttonPopup.style.left = leftB + 'px';
            this._buttonPopup.style.top = topB + 'px';
        }
        if (this._tocPopup && this._mediaResizeTarget) {
            var rectT = this._mediaResizeTarget.getBoundingClientRect();
            var tbWT = this._tocPopup.offsetWidth || 180;
            var leftT = Math.min(Math.max(rectT.left + rectT.width / 2 - tbWT / 2, 4), window.innerWidth - tbWT - 4);
            var topT = rectT.top - 44;
            if (topT < 4) topT = rectT.bottom + 4;
            this._tocPopup.style.left = leftT + 'px';
            this._tocPopup.style.top = topT + 'px';
        }
        if (this._searchBlockPopup && this._mediaResizeTarget) {
            var rectS = this._mediaResizeTarget.getBoundingClientRect();
            var tbWS = this._searchBlockPopup.offsetWidth || 180;
            var leftS = Math.min(Math.max(rectS.left + rectS.width / 2 - tbWS / 2, 4), window.innerWidth - tbWS - 4);
            var topS = rectS.top - 44;
            if (topS < 4) topS = rectS.bottom + 4;
            this._searchBlockPopup.style.left = leftS + 'px';
            this._searchBlockPopup.style.top = topS + 'px';
        }
    };

    // ===================================================================
    // FLOAT TABLE TOOLBAR — ONE unified toolbar with ALL table functions
    // ===================================================================
    RichTextEditor.prototype._showFloatTableToolbar = function (table) {
        var self = this;
        this._hideFloatTableToolbar();

        var toolbar = el('div', { class: 'rte-table-float-toolbar' });

        var tbl = table;

        function getSelTd() {
            var sel = window.getSelection();
            if (!sel || sel.rangeCount === 0) return null;
            var node = sel.anchorNode;
            var td = (node.nodeType === Node.TEXT_NODE) ? node.parentElement : node;
            while (td && td.tagName !== 'TD' && td.tagName !== 'TH') td = td.parentElement;
            return td;
        }

        var activeMenu = null;
        function closeMenus(e) {
            if (activeMenu) {
                if (e && activeMenu.parentElement.contains(e.target)) return;
                activeMenu.style.display = 'none';
                activeMenu = null;
            }
        }
        document.addEventListener('click', closeMenus);

        function createBtn(iconHtml, title, action) {
            var btn = el('button', { type: 'button', class: 'rte-ft-btn', title: title, html: iconHtml });
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                closeMenus();
                action();
            });
            return btn;
        }

        function createDropdown(iconHtml, title, items) {
            var btnWrap = el('div', { style: 'position:relative;display:inline-block;' });
            var btn = el('button', { type: 'button', class: 'rte-ft-btn', title: title, html: iconHtml + '<span style="display:inline-block;width:12px;height:12px;margin-left:2px;pointer-events:none;">' + ICON.chevron + '</span>' });
            btn.style.width = '42px'; // wider to fit caret

            var menu = el('div', { class: 'rte-ft-dropdown', style: 'min-width: 180px; text-align: left; padding: 4px 0;' });

            items.forEach(function (item) {
                if (item === 'sep') {
                    var sep = el('div', { style: 'height:1px; background:#eef0f3; margin:4px 0;' });
                    menu.appendChild(sep);
                    return;
                }

                var mItem = el('button', { type: 'button', class: 'rte-ft-dropitem', style: 'display:flex; align-items:center; gap:8px; text-align:left; padding: 6px 14px;' });
                mItem.appendChild(el('span', { html: item.icon, style: 'width:16px;height:16px;display:inline-block;color:#6b7280;pointer-events:none;' }));
                mItem.appendChild(el('span', { text: item.label, style: 'pointer-events:none;' }));

                if (item.kind === 'color') {
                    var input = el('input', { type: 'color', value: '#000000', style: 'position:absolute;opacity:0;width:1px;height:1px;' });
                    // init color
                    input.value = item.getValue();
                    mItem.appendChild(input);
                    mItem.addEventListener('click', function (e) {
                        e.stopPropagation();
                        input.click();
                    });
                    input.addEventListener('input', function () {
                        item.action(this.value);
                    });
                    input.addEventListener('change', function () {
                        closeMenus();
                    });
                } else {
                    mItem.addEventListener('click', function (e) {
                        e.stopPropagation();
                        closeMenus();
                        item.action();
                    });
                }
                menu.appendChild(mItem);
            });

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = (menu.style.display === 'block');
                closeMenus();
                if (!isOpen) {
                    menu.style.display = 'block';
                    activeMenu = menu;
                }
            });

            btnWrap.appendChild(btn);
            btnWrap.appendChild(menu);
            return btnWrap;
        }

        function applyBorders(type) {
            var selected = Array.from(tbl.querySelectorAll('.rte-cell-selected'));
            if (selected.length === 0) {
                var td = getSelTd();
                if (td) selected = [td];
            }
            if (selected.length === 0) return;

            var defaultBorder = '1px solid #d0d4da';

            var minRow = Infinity, maxRow = -1, minCol = Infinity, maxCol = -1;
            selected.forEach(function (c) {
                var r = c.parentElement.rowIndex;
                var col = c.cellIndex;
                minRow = Math.min(minRow, r); maxRow = Math.max(maxRow, r);
                minCol = Math.min(minCol, col); maxCol = Math.max(maxCol, col);
            });

            selected.forEach(function (c) {
                var r = c.parentElement.rowIndex;
                var col = c.cellIndex;

                if (type === 'bottom') {
                    c.style.borderBottom = defaultBorder;
                } else if (type === 'top') {
                    c.style.borderTop = defaultBorder;
                } else if (type === 'left') {
                    c.style.borderLeft = defaultBorder;
                } else if (type === 'right') {
                    c.style.borderRight = defaultBorder;
                } else if (type === 'none') {
                    c.style.border = 'none';
                    c.style.backgroundImage = 'none';
                } else if (type === 'all') {
                    c.style.border = defaultBorder;
                    c.style.backgroundImage = 'none';
                } else if (type === 'outside') {
                    if (r === minRow) c.style.borderTop = defaultBorder;
                    if (r === maxRow) c.style.borderBottom = defaultBorder;
                    if (col === minCol) c.style.borderLeft = defaultBorder;
                    if (col === maxCol) c.style.borderRight = defaultBorder;
                } else if (type === 'inside') {
                    if (r > minRow) c.style.borderTop = defaultBorder;
                    if (r < maxRow) c.style.borderBottom = defaultBorder;
                    if (col > minCol) c.style.borderLeft = defaultBorder;
                    if (col < maxCol) c.style.borderRight = defaultBorder;
                } else if (type === 'insideH') {
                    if (r > minRow) c.style.borderTop = defaultBorder;
                    if (r < maxRow) c.style.borderBottom = defaultBorder;
                } else if (type === 'insideV') {
                    if (col > minCol) c.style.borderLeft = defaultBorder;
                    if (col < maxCol) c.style.borderRight = defaultBorder;
                } else if (type === 'diagDown') {
                    c.style.backgroundImage = 'linear-gradient(to bottom right, transparent calc(50% - 1px), #d0d4da calc(50% - 1px), #d0d4da calc(50% + 1px), transparent calc(50% + 1px))';
                } else if (type === 'diagUp') {
                    c.style.backgroundImage = 'linear-gradient(to top right, transparent calc(50% - 1px), #d0d4da calc(50% - 1px), #d0d4da calc(50% + 1px), transparent calc(50% + 1px))';
                }
            });

            if (type === 'none') {
                tbl.style.border = 'none';
            } else {
                tbl.style.border = '';
            }

            self._syncSource();
        }

        // 1. Table Header (Single Button)
        toolbar.appendChild(createBtn(ICON.tableHeader, 'Table Header', function () {
            self._toggleTableHeader(tbl);
        }));

        // 2. Table Cell
        toolbar.appendChild(createDropdown(ICON.tableCellHighlight, 'Table Cell', [
            { label: 'Merge Cells', icon: ICON.mergeCells, action: function () { self._mergeTableCells(tbl); } },
            { label: 'Split Cells Vertical', icon: ICON.splitCell, action: function () { self._splitTableCellVertical(tbl); } },
            { label: 'Split Cells Horizontal', icon: ICON.splitCell, action: function () { self._splitTableCellHorizontal(tbl); } },
            'sep',
            { kind: 'color', label: 'Cell Text Color', icon: ICON.textcolor, getValue: function () { var td = getSelTd(); return td ? td.style.color || '#000000' : '#000000'; }, action: function (val) { var selected = Array.from(tbl.querySelectorAll('.rte-cell-selected')); if (selected.length > 0) { selected.forEach(function (c) { c.style.color = val; }); } else { var td = getSelTd(); if (td) td.style.color = val; } self._syncSource(); } },
            { kind: 'color', label: 'Cell Back Color', icon: ICON.paint, getValue: function () { var td = getSelTd(); return td ? td.style.backgroundColor || '#ffffff' : '#ffffff'; }, action: function (val) { var selected = Array.from(tbl.querySelectorAll('.rte-cell-selected')); if (selected.length > 0) { selected.forEach(function (c) { c.style.backgroundColor = val; }); } else { var td = getSelTd(); if (td) td.style.backgroundColor = val; } self._syncSource(); } }
        ]));

        // 3. Table Borders
        toolbar.appendChild(createDropdown(ICON.borders, 'Table Borders', [
            { label: 'Bottom Border', icon: ICON.borderBottom, action: function () { applyBorders('bottom'); } },
            { label: 'Top Border', icon: ICON.borderTop, action: function () { applyBorders('top'); } },
            { label: 'Left Border', icon: ICON.borderLeft, action: function () { applyBorders('left'); } },
            { label: 'Right Border', icon: ICON.borderRight, action: function () { applyBorders('right'); } },
            'sep',
            { label: 'No Border', icon: ICON.borderNone, action: function () { applyBorders('none'); } },
            { label: 'All Borders', icon: ICON.borderAll, action: function () { applyBorders('all'); } },
            { label: 'Outside Borders', icon: ICON.borderOutside, action: function () { applyBorders('outside'); } },
            { label: 'Inside Borders', icon: ICON.borderInside, action: function () { applyBorders('inside'); } },
            'sep',
            { label: 'Inside Horizontal Border', icon: ICON.borderInsideH, action: function () { applyBorders('insideH'); } },
            { label: 'Inside Vertical Border', icon: ICON.borderInsideV, action: function () { applyBorders('insideV'); } },
            'sep',
            { label: 'Diagonal Down Border', icon: ICON.borderDiagonalDown, action: function () { applyBorders('diagDown'); } },
            { label: 'Diagonal Up Border', icon: ICON.borderDiagonalUp, action: function () { applyBorders('diagUp'); } }
        ]));

        // 4. Table Row
        toolbar.appendChild(createDropdown(ICON.tableRowHighlight, 'Table Row', [
            { label: 'Insert Row Above', icon: ICON.insertRowBefore, action: function () { self._insertTableRow(tbl, 0); } },
            { label: 'Insert Row Below', icon: ICON.insertRowAfter, action: function () { self._insertTableRow(tbl, 1); } },
            'sep',
            { label: 'Delete Row', icon: ICON.deleteRow, action: function () { self._deleteTableRow(tbl); } }
        ]));

        // 4. Table Column
        toolbar.appendChild(createDropdown(ICON.tableColHighlight, 'Table Column', [
            { label: 'Insert Column Left', icon: ICON.insertColBefore, action: function () { self._insertTableCol(tbl, 0); } },
            { label: 'Insert Column Right', icon: ICON.insertColAfter, action: function () { self._insertTableCol(tbl, 1); } },
            'sep',
            { label: 'Delete Column', icon: ICON.deleteCol, action: function () { self._deleteTableCol(tbl); } }
        ]));

        // 5. Table Width Selection
        toolbar.appendChild(createDropdown(ICON.tableColWidth, 'Lebar Tabel', [
            { label: 'Lebar 100%', icon: ICON.table, action: function () { self._setTableWidthPercent(tbl, 100); } },
            { label: 'Lebar 75%', icon: ICON.table, action: function () { self._setTableWidthPercent(tbl, 75); } },
            { label: 'Lebar 50%', icon: ICON.table, action: function () { self._setTableWidthPercent(tbl, 50); } },
            { label: 'Lebar 25%', icon: ICON.table, action: function () { self._setTableWidthPercent(tbl, 25); } }
        ]));

        // 5. Table
        toolbar.appendChild(createDropdown(ICON.table, 'Table', [
            {
                label: 'AutoFit Contents', icon: ICON.tableColWidth, action: function () {
                    tbl.style.width = 'auto';
                    tbl.style.tableLayout = 'auto';
                    Array.from(tbl.querySelectorAll('td, th')).forEach(function (c) { c.style.width = ''; });
                    self._updateTableOverlayPosition();
                    self._syncSource();
                }
            },
            {
                label: 'AutoFit Window', icon: ICON.tableColWidth, action: function () {
                    tbl.style.width = '100%';
                    tbl.style.tableLayout = 'auto';
                    Array.from(tbl.querySelectorAll('td, th')).forEach(function (c) { c.style.width = ''; });
                    self._updateTableOverlayPosition();
                    self._syncSource();
                }
            },
            {
                label: 'Fixed Column Width', icon: ICON.tableColWidth, action: function () {
                    tbl.style.tableLayout = 'fixed';
                    // Ensure all cells have a set width based on current size
                    Array.from(tbl.rows[0].cells).forEach(function (c) {
                        if (!c.style.width) c.style.width = c.offsetWidth + 'px';
                    });
                    self._updateTableOverlayPosition();
                    self._syncSource();
                }
            },
            'sep',
            {
                label: 'Align Left', icon: ICON.alignLeft, action: function () {
                    tbl.style.marginLeft = '0';
                    tbl.style.marginRight = 'auto';
                    self._updateTableOverlayPosition();
                    self._repositionFloatTableToolbar(tbl);
                    self._syncSource();
                }
            },
            {
                label: 'Align Center', icon: ICON.alignCenter, action: function () {
                    tbl.style.marginLeft = 'auto';
                    tbl.style.marginRight = 'auto';
                    self._updateTableOverlayPosition();
                    self._repositionFloatTableToolbar(tbl);
                    self._syncSource();
                }
            },
            {
                label: 'Align Right', icon: ICON.alignRight, action: function () {
                    tbl.style.marginLeft = 'auto';
                    tbl.style.marginRight = '0';
                    self._updateTableOverlayPosition();
                    self._repositionFloatTableToolbar(tbl);
                    self._syncSource();
                }
            },
            'sep',
            {
                label: 'Atur Ukuran Tabel', icon: ICON.tableCellProp, action: function () {
                    closeMenus();
                    self._hideFloatTableToolbar();

                    var curW = tbl.style.width || tbl.getAttribute('width') || 'auto';
                    var curH = tbl.style.height || tbl.getAttribute('height') || 'auto';

                    var wInput = el('input', { type: 'text', class: 'rte-form-input', value: curW, placeholder: 'misal: 100%, 500px, auto' });
                    var hInput = el('input', { type: 'text', class: 'rte-form-input', value: curH, placeholder: 'misal: 300px, auto' });

                    var body = el('div', { class: 'rte-form' }, [
                        el('label', { class: 'rte-form-label', text: 'Lebar Tabel (misal: 100%, 500px, auto)' }),
                        wInput,
                        el('label', { class: 'rte-form-label', style: 'margin-top:12px;', text: 'Tinggi Tabel (misal: 300px, auto)' }),
                        hInput
                    ]);

                    openModal({
                        title: 'Atur Ukuran Tabel',
                        body: body,
                        confirmLabel: 'Simpan',
                        cancelLabel: 'Batal',
                        onConfirm: function () {
                            var newW = wInput.value.trim();
                            var newH = hInput.value.trim();

                            if (newW && newW !== 'auto') {
                                tbl.style.width = newW;
                                tbl.removeAttribute('width');
                            } else {
                                tbl.style.width = 'auto';
                                tbl.removeAttribute('width');
                            }

                            if (newH && newH !== 'auto') {
                                tbl.style.height = newH;
                                tbl.removeAttribute('height');
                            } else {
                                tbl.style.height = 'auto';
                                tbl.removeAttribute('height');
                            }

                            tbl.style.tableLayout = 'fixed';
                            self._normalizeColumnWidths(tbl);
                            self._updateTableOverlayPosition();
                            self._syncSource();
                        }
                    });
                }
            },
            'sep',
            {
                label: 'Delete Table', icon: ICON.tableDelete, action: function () {
                    Swal.fire({ title: 'Hapus Tabel?', text: 'Tindakan ini tidak dapat dibatalkan.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal' }).then(function (res) {
                        if (res.isConfirmed) { self._hideTableSelection(); tbl.parentNode.removeChild(tbl); self._syncSource(); }
                    });
                }
            }
        ]));

        toolbar.style.position = 'fixed';
        toolbar.style.zIndex = '99995';
        document.body.appendChild(toolbar);
        this._floatToolbar = toolbar;

        toolbar._closeDropdowns = closeMenus;

        // Position it immediately after insertion (must be in DOM first for offsetWidth)
        // Use rAF to ensure the browser has painted the element and offsetWidth is accurate
        var self2 = this;
        requestAnimationFrame(function () {
            self2._repositionFloatTableToolbar(tbl);
        });
    };

    RichTextEditor.prototype._hideFloatTableToolbar = function () {
        if (this._floatToolbar) {
            document.removeEventListener('click', this._floatToolbar._closeDropdowns);
            if (this._floatToolbar.parentNode) this._floatToolbar.parentNode.removeChild(this._floatToolbar);
            this._floatToolbar = null;
        }
    };

    // ---- Table operation helpers ----
    RichTextEditor.prototype._insertTableRow = function (table, dir) {
        // dir: 0 = above, 1 = below current row
        // Find the currently "active" row from selection
        var sel = window.getSelection();
        var activeTd = null;
        if (sel && sel.rangeCount > 0) {
            var node = sel.anchorNode;
            activeTd = (node.nodeType === Node.TEXT_NODE) ? node.parentElement : node;
            while (activeTd && activeTd.tagName !== 'TD' && activeTd.tagName !== 'TH') activeTd = activeTd.parentElement;
        }
        var rowIdx = activeTd ? activeTd.parentNode.rowIndex : 0;
        var insertAt = (dir === 0) ? rowIdx : rowIdx + 1;

        var newRow = table.insertRow(insertAt);
        var refRow = table.rows[insertAt + 1] || table.rows[insertAt - 1] || null;
        var numCols = refRow ? refRow.cells.length : 1;
        for (var c = 0; c < numCols; c++) {
            var nc = newRow.insertCell();
            nc.innerHTML = '\u00a0';
            nc.style.cssText = (refRow && refRow.cells[c]) ? refRow.cells[c].style.cssText : 'border:1px solid #d0d4da;padding:6px 8px;';
        }
        this._refreshTableSelection(table);
    };

    RichTextEditor.prototype._insertTableCol = function (table, dir) {
        var sel = window.getSelection();
        var activeTd = null;
        if (sel && sel.rangeCount > 0) {
            var node = sel.anchorNode;
            activeTd = (node.nodeType === Node.TEXT_NODE) ? node.parentElement : node;
            while (activeTd && activeTd.tagName !== 'TD' && activeTd.tagName !== 'TH') activeTd = activeTd.parentElement;
        }
        var cIdx = activeTd ? activeTd.cellIndex : 0;
        var insertAt = (dir === 0) ? cIdx : cIdx + 1;

        // Check if table width is pixel-based and cells are pixel-based
        var firstRow = table.rows[0];
        var isPx = false;
        var baseWidth = 100;
        if (firstRow && firstRow.cells.length > 0) {
            var sampleCell = firstRow.cells[0];
            if (sampleCell.style.width && sampleCell.style.width.indexOf('px') !== -1) {
                isPx = true;
                baseWidth = parseFloat(sampleCell.style.width) || 100;
            }
        }

        for (var r = 0; r < table.rows.length; r++) {
            var nc = table.rows[r].insertCell(insertAt);
            nc.innerHTML = '\u00a0';
            var refStyle = table.rows[r].cells[insertAt + 1] || table.rows[r].cells[insertAt - 1];
            nc.style.cssText = refStyle ? refStyle.style.cssText : 'border:1px solid #d0d4da;padding:6px 8px;';
        }

        // Post-insert width normalization
        var newFirstRow = table.rows[0];
        if (newFirstRow) {
            var cells = Array.from(newFirstRow.cells);
            if (isPx) {
                cells[insertAt].style.width = baseWidth + 'px';
                var tblW = table.style.width;
                if (tblW && tblW.indexOf('px') !== -1) {
                    var newTblW = parseFloat(tblW) + baseWidth;
                    table.style.width = newTblW + 'px';
                }
            } else {
                cells[insertAt].style.width = (100 / cells.length).toFixed(1) + '%';
            }
            this._normalizeColumnWidths(table);
        }

        this._refreshTableSelection(table);
    };

    RichTextEditor.prototype._deleteTableRow = function (table) {
        if (table.rows.length <= 1) {
            Swal.fire({ title: 'Tidak Bisa Hapus', text: 'Tabel harus memiliki minimal 1 baris.', icon: 'warning', confirmButtonText: 'OK' });
            return;
        }
        var sel = window.getSelection();
        var activeTd = null;
        if (sel && sel.rangeCount > 0) {
            var node = sel.anchorNode;
            activeTd = (node.nodeType === Node.TEXT_NODE) ? node.parentElement : node;
            while (activeTd && activeTd.tagName !== 'TD' && activeTd.tagName !== 'TH') activeTd = activeTd.parentElement;
        }
        var rowIdx = activeTd ? activeTd.parentNode.rowIndex : table.rows.length - 1;
        table.deleteRow(rowIdx);
        this._refreshTableSelection(table);
    };

    RichTextEditor.prototype._deleteTableCol = function (table) {
        if (table.rows[0] && table.rows[0].cells.length <= 1) {
            Swal.fire({ title: 'Tidak Bisa Hapus', text: 'Tabel harus memiliki minimal 1 kolom.', icon: 'warning', confirmButtonText: 'OK' });
            return;
        }
        var sel = window.getSelection();
        var activeTd = null;
        if (sel && sel.rangeCount > 0) {
            var node = sel.anchorNode;
            activeTd = (node.nodeType === Node.TEXT_NODE) ? node.parentElement : node;
            while (activeTd && activeTd.tagName !== 'TD' && activeTd.tagName !== 'TH') activeTd = activeTd.parentElement;
        }
        var cIdx = activeTd ? activeTd.cellIndex : table.rows[0].cells.length - 1;

        var firstRow = table.rows[0];
        var deletedCell = firstRow ? firstRow.cells[cIdx] : null;
        var deletedWidthVal = deletedCell ? parseFloat(deletedCell.style.width) : NaN;
        var deletedWidthUnit = deletedCell ? (deletedCell.style.width || '').replace(/[0-9.]/g, '') : '';

        for (var r = 0; r < table.rows.length; r++) {
            if (table.rows[r].cells[cIdx]) {
                table.rows[r].deleteCell(cIdx);
            }
        }
        var newFirstRow = table.rows[0];
        if (newFirstRow && newFirstRow.cells.length > 0) {
            if (deletedWidthUnit === 'px' && !isNaN(deletedWidthVal)) {
                var tblW = table.style.width;
                if (tblW && tblW.indexOf('px') !== -1) {
                    var newTblW = Math.max(100, parseFloat(tblW) - deletedWidthVal);
                    table.style.width = newTblW + 'px';
                }
            }
            this._normalizeColumnWidths(table);
        }

        this._refreshTableSelection(table);
    };

    RichTextEditor.prototype._mergeTableCells = function (table) {
        var selected = Array.from(table.querySelectorAll('.rte-cell-selected'));
        if (selected.length <= 1) {
            Swal.fire({ title: 'Gagal', text: 'Pilih (block) beberapa sel terlebih dahulu menggunakan kursor.', icon: 'warning', confirmButtonText: 'OK' });
            return;
        }

        var minRow = Infinity, minCol = Infinity;
        var maxRow = -1, maxCol = -1;
        var distinctRows = new Set();
        var colsInRow = {};

        selected.forEach(function (cell) {
            var r = cell.parentElement.rowIndex;
            var c = cell.cellIndex;
            minRow = Math.min(minRow, r);
            maxRow = Math.max(maxRow, r);
            minCol = Math.min(minCol, c);
            maxCol = Math.max(maxCol, c);

            distinctRows.add(r);
            colsInRow[r] = (colsInRow[r] || 0) + (cell.colSpan || 1);
        });

        var rowSpan = distinctRows.size;
        var colSpan = Math.max.apply(null, Object.values(colsInRow));

        var topCell = null;
        selected.forEach(function (cell) {
            if (cell.parentElement.rowIndex === minRow && cell.cellIndex === minCol) {
                topCell = cell;
            }
        });
        if (!topCell) topCell = selected[0];

        selected.forEach(function (cell) {
            if (cell !== topCell) {
                if (cell.innerHTML.trim() !== '' && cell.innerHTML !== '&nbsp;' && cell.innerHTML !== '<br>') {
                    topCell.innerHTML += '<br>' + cell.innerHTML;
                }
                cell.parentNode.removeChild(cell);
            }
        });

        topCell.rowSpan = rowSpan;
        topCell.colSpan = colSpan;
        topCell.classList.remove('rte-cell-selected');

        this._clearCellSelection(table);
        this._refreshTableSelection(table);
        this._syncSource();
    };

    RichTextEditor.prototype._splitTableCellHorizontal = function (table) {
        var td = table.querySelector('.rte-cell-selected');
        if (!td) {
            var sel = window.getSelection();
            if (sel && sel.rangeCount > 0) {
                var node = sel.anchorNode;
                td = (node.nodeType === Node.TEXT_NODE) ? node.parentElement : node;
                while (td && td.tagName !== 'TD' && td.tagName !== 'TH') td = td.parentElement;
            }
        }
        if (!td) {
            Swal.fire({ title: 'Info', text: 'Pilih satu sel untuk dipisah secara horizontal.', icon: 'info', confirmButtonText: 'OK' });
            return;
        }

        var row = td.parentElement;
        var cIdx = td.cellIndex;
        var colspan = td.colSpan || 1;

        if (colspan > 1) {
            td.colSpan = colspan - 1;
            var nc = row.insertCell(cIdx + 1);
            nc.innerHTML = '\u00a0';
            nc.style.cssText = td.style.cssText;
            nc.colSpan = 1;
            nc.rowSpan = td.rowSpan;
        } else {
            var nc = row.insertCell(cIdx + 1);
            nc.innerHTML = '\u00a0';
            nc.style.cssText = td.style.cssText;
            nc.rowSpan = td.rowSpan;
        }

        this._clearCellSelection(table);
        this._refreshTableSelection(table);
        this._syncSource();
    };

    RichTextEditor.prototype._splitTableCellVertical = function (table) {
        var td = table.querySelector('.rte-cell-selected');
        if (!td) {
            var sel = window.getSelection();
            if (sel && sel.rangeCount > 0) {
                var node = sel.anchorNode;
                td = (node.nodeType === Node.TEXT_NODE) ? node.parentElement : node;
                while (td && td.tagName !== 'TD' && td.tagName !== 'TH') td = td.parentElement;
            }
        }
        if (!td) {
            Swal.fire({ title: 'Info', text: 'Pilih satu sel untuk dipisah secara vertikal.', icon: 'info', confirmButtonText: 'OK' });
            return;
        }

        var rowspan = td.rowSpan || 1;
        if (rowspan > 1) {
            td.rowSpan = rowspan - 1;
            var targetRow = td.parentElement.nextElementSibling;
            if (targetRow) {
                var safeIndex = Math.min(td.cellIndex, targetRow.cells.length);
                var nc = targetRow.insertCell(safeIndex);
                nc.innerHTML = '\u00a0';
                nc.style.cssText = td.style.cssText;
                nc.colSpan = td.colSpan;
            }
        } else {
            // Split a normal cell vertically (create a new row just for this cell)
            var tr = td.parentElement;
            var targetRow = table.insertRow(tr.rowIndex + 1);
            var nc = targetRow.insertCell(0);
            nc.innerHTML = '\u00a0';
            nc.style.cssText = td.style.cssText;
            nc.colSpan = td.colSpan;

            // Adjust rowspan of other cells in the original row to maintain grid
            for (var i = 0; i < tr.cells.length; i++) {
                if (tr.cells[i] !== td) {
                    tr.cells[i].rowSpan = (tr.cells[i].rowSpan || 1) + 1;
                }
            }
        }

        this._clearCellSelection(table);
        this._refreshTableSelection(table);
        this._syncSource();
    };

    RichTextEditor.prototype._toggleTableHeader = function (table) {
        var firstRow = table.rows[0];
        if (!firstRow) return;
        var isHeader = firstRow.cells[0] && firstRow.cells[0].tagName === 'TH';
        for (var c = 0; c < firstRow.cells.length; c++) {
            var cell = firstRow.cells[c];
            var tag = isHeader ? 'TD' : 'TH';
            var nc = document.createElement(tag);
            nc.innerHTML = cell.innerHTML;
            nc.style.cssText = cell.style.cssText;
            firstRow.insertBefore(nc, cell);
            firstRow.removeChild(cell);
        }
        this._refreshTableSelection(table);
    };

    RichTextEditor.prototype._refreshTableSelection = function (table) {
        if (!table) return;
        var self = this;
        // Use double requestAnimationFrame to guarantee browser has reflowed the
        // table DOM (new rows/cols) BEFORE we measure its bounding box.
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                if (!self.content.contains(table)) return; // table was removed
                // Force-destroy old overlay so _showTableSelection won't early-return
                if (self._tableOverlay && self._tableOverlay.parentNode) {
                    self._tableOverlay.parentNode.removeChild(self._tableOverlay);
                }
                self._tableOverlay = null;
                self._selectedTable = null;
                // Recreate overlay + toolbar with fresh, post-reflow dimensions
                self._showTableSelection(table);
            });
        });
    };

    RichTextEditor.prototype._scaleTableCellWidths = function (table, ratio) {
        if (!table.rows.length) return;
        var firstRow = table.rows[0];
        var cells = Array.from(firstRow.cells);
        cells.forEach(function (cell) {
            var currentW = cell.style.width;
            if (currentW) {
                if (currentW.indexOf('%') !== -1) {
                    // Percentage based, scales automatically
                } else {
                    var val = parseFloat(currentW);
                    if (!isNaN(val)) {
                        cell.style.width = Math.max(10, val * ratio) + 'px';
                    }
                }
            } else {
                cell.style.width = Math.max(10, cell.offsetWidth * ratio) + 'px';
            }
        });

        // Also clear widths on other rows
        for (var r = 1; r < table.rows.length; r++) {
            Array.from(table.rows[r].cells).forEach(function (c) {
                c.style.width = '';
            });
        }
    };

    RichTextEditor.prototype._setTableWidthPercent = function (table, percent) {
        table.style.width = percent + '%';
        table.removeAttribute('width');
        table.style.tableLayout = 'fixed';
        this._normalizeColumnWidths(table);
        this._updateTableOverlayPosition();
        this._syncSource();
    };
    RichTextEditor.prototype._normalizeColumnWidths = function (table) {
        if (!table.rows.length) return;

        // Force fixed layout and clear min-width inline to ensure browser respects defined widths
        table.style.tableLayout = 'fixed';
        table.style.minWidth = '0px';
        // Clear inline widths and width attributes on other rows
        for (var r = 1; r < table.rows.length; r++) {
            Array.prototype.slice.call(table.rows[r].cells).forEach(function (c) {
                c.style.width = '';
                c.removeAttribute('width');
            });
        }

        var firstRow = table.rows[0];
        var cells = Array.prototype.slice.call(firstRow.cells);
        if (!cells.length) return;
        var tblW = table.style.width || '';
        var isTblPercent = (tblW.indexOf('%') !== -1 || tblW === 'auto' || tblW === '');

        if (isTblPercent) {
            var totalCellWidth = 0;
            var cellWidths = [];
            cells.forEach(function (cell) {
                cell.removeAttribute('width');
                var w = cell.offsetWidth || 100;
                cellWidths.push(w);
                totalCellWidth += w;
            });
            cells.forEach(function (cell, idx) {
                var pct = totalCellWidth > 0 ? (cellWidths[idx] / totalCellWidth) * 100 : (100 / cells.length);
                cell.style.width = pct.toFixed(1) + '%';
            });
        } else {
            var tblPixelW = parseFloat(tblW) || table.offsetWidth || 500;
            var totalCellWidth = 0;
            var cellWidths = [];
            cells.forEach(function (cell) {
                cell.removeAttribute('width');
                var w = cell.offsetWidth || (tblPixelW / cells.length);
                cellWidths.push(w);
                totalCellWidth += w;
            });
            cells.forEach(function (cell, idx) {
                var w = totalCellWidth > 0 ? (cellWidths[idx] / totalCellWidth) * tblPixelW : (tblPixelW / cells.length);
                cell.style.width = w.toFixed(0) + 'px';
            });
        }
    };
    RichTextEditor.prototype._repositionFloatTableToolbar = function (table) {
        var toolbar = this._floatToolbar;
        if (!toolbar || !table) return;
        var tblRect = table.getBoundingClientRect();
        var spaceAbove = tblRect.top;

        toolbar.style.position = 'fixed';
        toolbar.style.zIndex = '99995';

        var tbW = toolbar.offsetWidth || 300;
        var tbH = toolbar.offsetHeight || 44;

        var left = Math.min(Math.max(tblRect.left + tblRect.width / 2 - tbW / 2, 4), window.innerWidth - tbW - 4);
        toolbar.style.left = left + 'px';

        if (spaceAbove < 60) {
            var bottomTop = tblRect.bottom + 8;
            if (bottomTop + tbH > window.innerHeight) bottomTop = window.innerHeight - tbH - 8;
            toolbar.style.top = bottomTop + 'px';
            toolbar.style.marginTop = '0px';
            toolbar.style.transform = '';
            toolbar.classList.remove('rte-ft-top');
            toolbar.classList.add('rte-ft-bottom');
        } else {
            toolbar.style.top = (tblRect.top - 46) + 'px';
            toolbar.style.marginTop = '0px';
            toolbar.style.transform = '';
            toolbar.classList.remove('rte-ft-bottom');
            toolbar.classList.add('rte-ft-top');
        }
    };

    RichTextEditor.prototype._initTableResize = function (table) {
        var self = this;
        table.style.tableLayout = 'fixed';

        // Observe table changes to update selection
        if (this._tableResizeObserver) this._tableResizeObserver.disconnect();
        this._tableResizeObserver = new MutationObserver(function () {
            self._updateTableOverlayPosition();
        });
        this._tableResizeObserver.observe(table, { attributes: true, attributeFilter: ['style', 'width'] });
    };

    // ===================================================================
    // COMPREHENSIVE TABLE EDITOR POPUP (context menu)
    // ===================================================================
    RichTextEditor.prototype._showTableEditorPopup = function (td) {
        var self = this;
        this._closeTablePopup();
        var popup = el('div', { class: 'rte-context-popup rte-table-popup' });

        // Helper to build a section with a title row
        function section(title, buttons) {
            var sec = el('div', { class: 'rte-popup-section' });
            if (title) {
                sec.appendChild(el('div', { class: 'rte-popup-section-title', text: title }));
            }
            var row = el('div', { class: 'rte-popup-row', style: 'flex-wrap:wrap;' });
            buttons.forEach(function (b) {
                if (!b) return;
                var btn = el('button', {
                    type: 'button', class: 'rte-popup-btn' + (b.wide ? ' rte-popup-btn-wide' : ''),
                    title: b.title || b.label || '',
                    html: (b.icon || '') + (b.label || ''),
                    onclick: b.action,
                });
                row.appendChild(btn);
            });
            sec.appendChild(row);
            return sec;
        }

        // ---- Helpers ----
        function getTable() { return td.closest('table'); }
        function getRow() { return td.parentElement; }
        function getAllRows() { var t = getTable(); return t ? Array.from(t.rows) : []; }
        function getColIdx() { return td.cellIndex; }
        function getRowIdx() { return getRow() ? getRow().rowIndex : 0; }
        function getAllCols(tr) { return Array.from(tr.cells); }

        // ---- Toggle Table Header (make first row <th>) ----
        function toggleHeader() {
            var t = getTable();
            if (!t) return;
            var firstRow = t.rows[0];
            var isHeader = firstRow && firstRow.cells[0] && firstRow.cells[0].tagName === 'TH';
            for (var c = 0; c < firstRow.cells.length; c++) {
                var cell = firstRow.cells[c];
                var tag = isHeader ? 'TD' : 'TH';
                var newCell = document.createElement(tag);
                newCell.innerHTML = cell.innerHTML;
                newCell.style.cssText = cell.style.cssText;
                firstRow.insertBefore(newCell, cell);
                firstRow.removeChild(cell);
            }
            self._syncSource();
        }

        // ---- Row ops ----
        function insertRowAbove() {
            var t = getTable(); var rIdx = getRowIdx();
            if (!t) return;
            var newRow = t.insertRow(rIdx);
            var refRow = t.rows[rIdx + 1];
            for (var c = 0; c < (refRow ? refRow.cells.length : 1); c++) {
                var nc = newRow.insertCell();
                nc.innerHTML = '\u00a0';
                nc.style.cssText = (refRow && refRow.cells[c]) ? refRow.cells[c].style.cssText : 'border:1px solid #ccc;padding:6px;';
            }
            self._syncSource();
        }
        function insertRowBelow() {
            var t = getTable(); var rIdx = getRowIdx();
            if (!t) return;
            var newRow = t.insertRow(rIdx + 1);
            var refRow = t.rows[rIdx];
            for (var c = 0; c < (refRow ? refRow.cells.length : 1); c++) {
                var nc = newRow.insertCell();
                nc.innerHTML = '\u00a0';
                nc.style.cssText = (refRow && refRow.cells[c]) ? refRow.cells[c].style.cssText : 'border:1px solid #ccc;padding:6px;';
            }
            self._syncSource();
        }
        function deleteRow() {
            var t = getTable(); var rIdx = getRowIdx();
            if (!t || t.rows.length <= 1) {
                Swal.fire({ title: 'Tidak Bisa Hapus', text: 'Tabel harus memiliki minimal 1 baris.', icon: 'warning', confirmButtonText: 'OK' });
                return;
            }
            Swal.fire({
                title: 'Hapus Baris?',
                text: 'Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'px-4 py-2 rounded-lg text-sm font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors',
                    cancelButton: 'px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors',
                },
            }).then(function (result) {
                if (result.isConfirmed) {
                    t.deleteRow(rIdx);
                    self._closeTablePopup();
                    self._syncSource();
                }
            });
        }

        // ---- Column ops ----
        function insertColLeft() {
            var t = getTable(); if (!t) return;
            var cIdx = getColIdx();
            for (var r = 0; r < t.rows.length; r++) {
                var nc = t.rows[r].insertCell(cIdx);
                nc.innerHTML = '\u00a0';
                nc.style.cssText = t.rows[r].cells[cIdx + 1] ? t.rows[r].cells[cIdx + 1].style.cssText : 'border:1px solid #ccc;padding:6px;';
            }
            self._syncSource();
        }
        function insertColRight() {
            var t = getTable(); if (!t) return;
            var cIdx = getColIdx();
            for (var r = 0; r < t.rows.length; r++) {
                var nc = t.rows[r].insertCell(cIdx + 1);
                nc.innerHTML = '\u00a0';
                nc.style.cssText = t.rows[r].cells[cIdx] ? t.rows[r].cells[cIdx].style.cssText : 'border:1px solid #ccc;padding:6px;';
            }
            self._syncSource();
        }
        function deleteCol() {
            var t = getTable(); if (!t) return;
            var cIdx = getColIdx();
            var firstRow = t.rows[0];
            if (firstRow && firstRow.cells.length <= 1) {
                Swal.fire({ title: 'Tidak Bisa Hapus', text: 'Tabel harus memiliki minimal 1 kolom.', icon: 'warning', confirmButtonText: 'OK' });
                return;
            }
            Swal.fire({
                title: 'Hapus Kolom?',
                text: 'Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'px-4 py-2 rounded-lg text-sm font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors',
                    cancelButton: 'px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors',
                },
            }).then(function (result) {
                if (result.isConfirmed) {
                    for (var r = 0; r < t.rows.length; r++) {
                        t.rows[r].deleteCell(cIdx);
                    }
                    self._closeTablePopup();
                    self._syncSource();
                }
            });
        }

        // ---- Cell ops ----
        function mergeCells() {
            var sel = window.getSelection();
            if (!sel || sel.rangeCount === 0 || sel.isCollapsed) { Swal.fire({ title: 'Gagal', text: 'Pilih beberapa sel terlebih dahulu (klik dan seret untuk memilih sel).', icon: 'warning', confirmButtonText: 'OK' }); return; }
            try {
                document.execCommand('mergeCells', false, null);
            } catch (e) {
                // Fallback: merge with adjacent cell
                var nextTd = td.nextElementSibling;
                if (nextTd && nextTd.tagName === 'TD') {
                    td.colSpan = td.colSpan + (nextTd.colSpan || 1);
                    td.innerHTML += nextTd.innerHTML;
                    nextTd.parentNode.removeChild(nextTd);
                }
            }
            self._syncSource();
        }
        function splitCell() {
            var colspan = td.colSpan || 1;
            var row = getRow();
            if (!row) return;
            var cIdx = getColIdx();
            for (var i = 0; i < colspan; i++) {
                var nc = row.insertCell(cIdx + 1);
                nc.innerHTML = '\u00a0';
                nc.style.cssText = td.style.cssText;
                nc.style.border = '1px solid #ccc';
                nc.style.padding = '6px';
            }
            td.colSpan = 1;
            self._syncSource();
        }
        function deleteTable() {
            // Use SweetAlert2 modal (same style as the rest of the app)
            Swal.fire({
                title: 'Hapus Tabel?',
                text: 'Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'px-4 py-2 rounded-lg text-sm font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors',
                    cancelButton: 'px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors',
                },
            }).then(function (result) {
                if (result.isConfirmed) {
                    var t = getTable();
                    if (t) { t.parentNode.removeChild(t); }
                    self._closeTablePopup();
                    self._syncSource();
                }
            });
        }

        // ---- Width presets ----
        function setTableWidth(pct) {
            var t = getTable(); if (!t) return;
            t.style.width = pct;
            self._syncSource();
        }
        function autoSizeTable() {
            var t = getTable(); if (!t) return;
            t.style.width = '';
            self._syncSource();
        }

        // ---- Color controls ----
        var ctrlBgColor = el('div', { class: 'rte-popup-row' }, [
            el('label', { text: 'Cell bg:', style: 'min-width:60px;' }),
            el('input', {
                type: 'color', value: td.style.backgroundColor || '#ffffff', class: 'rte-popup-color',
                onchange: function () {
                    td.style.backgroundColor = this.value;
                    self._syncSource();
                }
            }),
        ]);
        var ctrlTextColor = el('div', { class: 'rte-popup-row' }, [
            el('label', { text: 'Text color:', style: 'min-width:60px;' }),
            el('input', {
                type: 'color', value: td.style.color || '#222222', class: 'rte-popup-color',
                onchange: function () {
                    td.style.color = this.value;
                    self._syncSource();
                }
            }),
        ]);

        // ---- Build sections ----
        popup.appendChild(
            section('Cell', [
                { icon: ICON.mergeCells, title: 'Merge cells', action: mergeCells },
                { icon: ICON.splitCell, title: 'Split cell', action: splitCell },
                null,
                { icon: ICON.tableHeader, title: 'Toggle header row', action: toggleHeader },
            ])
        );
        popup.appendChild(ctrlBgColor);
        popup.appendChild(ctrlTextColor);
        popup.appendChild(
            section('Row', [
                { icon: ICON.insertRowBefore, title: 'Insert row above', action: insertRowAbove },
                { icon: ICON.insertRowAfter, title: 'Insert row below', action: insertRowBelow },
                null,
                { icon: ICON.deleteRow, title: 'Delete row', action: deleteRow, wide: false },
            ])
        );
        popup.appendChild(
            section('Column', [
                { icon: ICON.insertColBefore, title: 'Insert column left', action: insertColLeft },
                { icon: ICON.insertColAfter, title: 'Insert column right', action: insertColRight },
                null,
                { icon: ICON.deleteCol, title: 'Delete column', action: deleteCol, wide: false },
            ])
        );
        popup.appendChild(
            section('Table', [
                { label: 'Auto', title: 'Auto size', action: autoSizeTable },
                { label: '100%', title: 'Width 100%', action: function () { setTableWidth('100%'); } },
                { label: '75%', title: 'Width 75%', action: function () { setTableWidth('75%'); } },
                { label: '50%', title: 'Width 50%', action: function () { setTableWidth('50%'); } },
                { label: '25%', title: 'Width 25%', action: function () { setTableWidth('25%'); } },
            ])
        );
        popup.appendChild(
            section(null, [
                { icon: ICON.deleteTable, title: 'Delete table', action: deleteTable, wide: false },
            ])
        );

        var rect = td.getBoundingClientRect();
        popup.style.position = 'fixed';

        // Draggable table popup — build drag handle as FIRST child
        var dragH = el('div', {
            style: 'cursor:move;padding:3px 8px;margin:-8px -12px 6px -12px;background:#f6f7f9;border-bottom:1px solid #eef0f3;color:#9aa0a6;font-size:11px;user-select:none;border-radius:8px 8px 0 0;display:flex;align-items:center;gap:4px;',
            html: '<span style="letter-spacing:2px;font-size:14px;line-height:1;">&#9776;</span><span>Drag to move</span>'
        });
        popup.insertBefore(dragH, popup.firstChild);

        // Position after popup is in DOM so getBoundingClientRect works
        document.body.appendChild(popup);

        var popupW = popup.offsetWidth;
        var popupH = popup.offsetHeight;
        var spaceBelow = window.innerHeight - rect.bottom;
        var spaceAbove = rect.top;
        var useAbove = spaceBelow < popupH + 20 && spaceAbove > popupH + 20;
        var initLeft = Math.min(Math.max(rect.left, 8), window.innerWidth - popupW - 8);
        var initTop = useAbove ? Math.max(8, rect.top - popupH - 8) : Math.min(rect.bottom + 8, window.innerHeight - popupH - 8);
        popup.style.left = initLeft + 'px';
        popup.style.top = initTop + 'px';
        this._tablePopup = popup;

        // ---- Draggable ----
        var isDragging = false, dragOffX = 0, dragOffY = 0;

        function isClickableEl(el) {
            if (!el) return false;
            var tag = el.tagName;
            if (tag === 'BUTTON' || tag === 'INPUT' || tag === 'SELECT' || tag === 'TEXTAREA') return true;
            if (el.classList && (el.classList.contains('rte-popup-btn') || el.classList.contains('rte-popup-color'))) return true;
            return false;
        }

        function onDragStart(e) {
            var target = e.target;
            if (isClickableEl(target)) return; // Don't drag when clicking buttons/inputs
            // Walk up — if we pass through a clickable element, abort
            while (target && target !== popup) {
                if (isClickableEl(target)) return;
                target = target.parentNode;
            }
            if (target !== popup && target !== dragH) return;

            isDragging = true;
            var cx = e.touches ? e.touches[0].clientX : e.clientX;
            var cy = e.touches ? e.touches[0].clientY : e.clientY;
            dragOffX = cx - popup.getBoundingClientRect().left;
            dragOffY = cy - popup.getBoundingClientRect().top;
            document.addEventListener('mousemove', onDragMove);
            document.addEventListener('mouseup', onDragEnd);
            document.addEventListener('touchmove', onDragMove, { passive: false });
            document.addEventListener('touchend', onDragEnd);
            e.preventDefault();
            e.stopPropagation();
        }

        function onDragMove(e) {
            if (!isDragging) return;
            e.preventDefault();
            var cx = e.touches ? e.touches[0].clientX : e.clientX;
            var cy = e.touches ? e.touches[0].clientY : e.clientY;
            var newLeft = Math.max(0, Math.min(cx - dragOffX, window.innerWidth - popup.offsetWidth));
            var newTop = Math.max(0, Math.min(cy - dragOffY, window.innerHeight - popup.offsetHeight));
            popup.style.left = newLeft + 'px';
            popup.style.top = newTop + 'px';
        }

        function onDragEnd() {
            isDragging = false;
            document.removeEventListener('mousemove', onDragMove);
            document.removeEventListener('mouseup', onDragEnd);
            document.removeEventListener('touchmove', onDragMove);
            document.removeEventListener('touchend', onDragEnd);
        }

        dragH.addEventListener('mousedown', onDragStart);
        dragH.addEventListener('touchstart', onDragStart, { passive: false });

        // Close popup when clicking outside
        function closeHandler(e) {
            if (!popup.contains(e.target)) {
                self._closeTablePopup();
            }
        }
        setTimeout(function () {
            document.addEventListener('click', self._tablePopupCloseHandler = closeHandler);
        }, 0);
    };

    RichTextEditor.prototype._closeTablePopup = function () {
        if (this._tablePopup) {
            if (this._tablePopup.parentNode) this._tablePopup.parentNode.removeChild(this._tablePopup);
            this._tablePopup = null;
        }
        if (this._tablePopupCloseHandler) {
            document.removeEventListener('click', this._tablePopupCloseHandler);
            this._tablePopupCloseHandler = null;
        }
        // NOTE: do NOT call _hideTableSelection() here — the overlay/selection
        // is a separate layer from the context popup. Closing one should not
        // automatically close the other.
    };

    RichTextEditor.prototype._setColspan = function (td, count) {
        var table = td.closest('table');
        if (!table) return;
        var rows = table.rows;
        var colIdx = td.cellIndex;
        var rowIdx = td.parentNode.rowIndex;
        for (var r = 0; r < rows.length; r++) {
            if (r === rowIdx) {
                td.colSpan = Math.min(count, rows[r].cells.length - colIdx);
            }
        }
        this._syncSource();
    };

    RichTextEditor.prototype._startCellSelection = function (e, startTd, table) {
        var self = this;
        self._clearCellSelection(table);
        startTd.classList.add('rte-cell-selected');

        var isDragging = true;
        // Optional: prevent default to avoid text selection during drag, 
        // but it might break clicking to place caret.
        // We'll just let native text selection happen alongside, or clear text selection.
        var sel = window.getSelection();
        if (sel) sel.removeAllRanges();

        function onMouseMove(me) {
            if (!isDragging) return;
            var target = document.elementFromPoint(me.clientX, me.clientY);
            if (!target) return;
            var currentTd = target.closest('td, th');
            if (currentTd && table.contains(currentTd)) {
                self._updateCellSelection(table, startTd, currentTd);
            }
        }

        function onMouseUp(ue) {
            isDragging = false;
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    };

    RichTextEditor.prototype._clearCellSelection = function (table) {
        if (!table) table = this.content;
        var selected = table.querySelectorAll('.rte-cell-selected');
        selected.forEach(function (td) { td.classList.remove('rte-cell-selected'); });
    };

    RichTextEditor.prototype._updateCellSelection = function (table, startTd, endTd) {
        this._clearCellSelection(table);
        var startRow = startTd.parentElement.rowIndex;
        var startCol = startTd.cellIndex;
        var endRow = endTd.parentElement.rowIndex;
        var endCol = endTd.cellIndex;

        var minRow = Math.min(startRow, endRow);
        var maxRow = Math.max(startRow, endRow);
        var minCol = Math.min(startCol, endCol);
        var maxCol = Math.max(startCol, endCol);

        for (var r = minRow; r <= maxRow; r++) {
            var row = table.rows[r];
            if (!row) continue;
            for (var c = minCol; c <= maxCol; c++) {
                var cell = row.cells[c];
                if (cell) {
                    cell.classList.add('rte-cell-selected');
                }
            }
        }
    };

    // -------- core operations ---------
    RichTextEditor.prototype._insertHTML = function (html) {
        this._focusContent();
        try {
            document.execCommand('insertHTML', false, html);
        } catch (e) {
            // Fallback: append at end
            this.content.insertAdjacentHTML('beforeend', html);
        }
        this._syncSource();
        this._updateState();
        if (this._selectedTable) {
            this._updateTableOverlayPosition();
        }
        this._updatePopupPositions();
    };

    RichTextEditor.prototype._toggleSource = function () {
        var showingSource = this.sourceArea.style.display !== 'none';
        if (showingSource) {
            // Switch back to WYSIWYG
            this.content.innerHTML = this.sourceArea.value;
            this.sourceArea.style.display = 'none';
            this.contentWrap.style.display = '';
            this.wrapper.classList.remove('rte-source-mode');
        } else {
            // Switch to source
            this._syncSource();
            this.sourceArea.value = this.content.innerHTML;
            this.contentWrap.style.display = 'none';
            this.sourceArea.style.display = 'block';
            this.wrapper.classList.add('rte-source-mode');
        }
    };

    RichTextEditor.prototype.showLoading = function () {
        if (this._loadingOverlay) return;
        this._loadingOverlay = document.createElement('div');
        this._loadingOverlay.style.cssText = 'position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.7);z-index:99999;display:flex;align-items:center;justify-content:center;';
        this._loadingOverlay.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;gap:10px;"><div style="width:30px;height:30px;border:3px solid #f3f3f3;border-top:3px solid #3b82f6;border-radius:50%;animation:rte-spin 1s linear infinite;"></div><span style="font-family:sans-serif;font-size:14px;color:#333;">Uploading...</span></div>';
        if (!document.getElementById('rte-loading-style')) {
            var style = document.createElement('style');
            style.id = 'rte-loading-style';
            style.innerHTML = '@keyframes rte-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
            document.head.appendChild(style);
        }
        this.wrapper.appendChild(this._loadingOverlay);
    };

    RichTextEditor.prototype.hideLoading = function () {
        if (this._loadingOverlay && this._loadingOverlay.parentNode) {
            this._loadingOverlay.parentNode.removeChild(this._loadingOverlay);
        }
        this._loadingOverlay = null;
    };

    RichTextEditor.prototype._toggleFullscreen = function () {
        var wrapper = this.wrapper;
        var isFullscreen = wrapper.classList.contains('rte-fullscreen');

        if (isFullscreen) {
            // Exit fullscreen: restore original inline styles
            wrapper.classList.remove('rte-fullscreen');
            document.body.classList.remove('rte-body-fullscreen');
            if (this._origFullscreenStyle) {
                wrapper.style.position = this._origFullscreenStyle.position;
                wrapper.style.top = this._origFullscreenStyle.top;
                wrapper.style.left = this._origFullscreenStyle.left;
                wrapper.style.width = this._origFullscreenStyle.width;
                wrapper.style.height = this._origFullscreenStyle.height;
                wrapper.style.zIndex = this._origFullscreenStyle.zIndex;
                this._origFullscreenStyle = null;
            }
        } else {
            // Enter fullscreen: save current styles and switch to fixed overlay
            this._origFullscreenStyle = {
                position: wrapper.style.position,
                top: wrapper.style.top,
                left: wrapper.style.left,
                width: wrapper.style.width,
                height: wrapper.style.height,
                zIndex: wrapper.style.zIndex,
            };
            wrapper.classList.add('rte-fullscreen');
            document.body.classList.add('rte-body-fullscreen');
            // The CSS .rte-fullscreen handles viewport dimensions (100vw/vh);
            // we also explicitly set via style to ensure inline override
            wrapper.style.position = 'fixed';
            wrapper.style.top = '0';
            wrapper.style.left = '0';
            wrapper.style.width = '100vw';
            wrapper.style.height = '100vh';
            wrapper.style.zIndex = '99990';
        }
    };

    // ---- Zoom controls ----
    RichTextEditor.prototype._updateZoomSpacer = function () {
        var contentWrap = this.contentWrap;
        var content = this.content;
        if (!contentWrap || !content) return;
        var z = this._zoom;
        
        var spacer = contentWrap.querySelector('.rte-zoom-spacer');
        if (z === 1 || 'zoom' in content.style) {
            if (spacer) spacer.style.height = '0px';
            return;
        }
        
        if (!spacer) {
            spacer = el('div', { class: 'rte-zoom-spacer', style: 'height: 0px; clear: both; pointer-events: none;' });
            contentWrap.appendChild(spacer);
        }
        
        var contentHeight = content.offsetHeight;
        var scaledHeight = contentHeight * z;
        var extraHeight = Math.max(0, scaledHeight - contentHeight);
        spacer.style.height = extraHeight + 'px';
    };

    RichTextEditor.prototype._applyZoom = function () {
        var contentWrap = this.contentWrap;
        var content = this.content;
        if (!contentWrap || !content) return;
        var z = this._zoom;
        var zoomPct = Math.round(z * 100);
        var self = this;

        // To ensure absolute layout consistency during zoom, we use a fixed logical 
        // width for the content area. This prevents text from re-wrapping and 
        // media from shifting positions as the zoom level changes.
        // 1000px provides a stable canvas that mimics common container widths.
        var layoutWidth = 1000;

        content.style.width = layoutWidth + 'px';
        content.style.minWidth = layoutWidth + 'px';
        content.style.margin = '0 auto';
        content.style.display = 'flow-root'; // Clear floats internally

        if ('zoom' in content.style) {
            content.style.zoom = z;
            content.style.transform = '';
            content.style.transformOrigin = '';
        } else {
            // Fallback for Firefox (uses transform:scale)
            content.style.transform = 'scale(' + z + ')';
            content.style.transformOrigin = 'top center';

            if (!this._zoomResizeObserver && window.ResizeObserver) {
                this._zoomResizeObserver = new ResizeObserver(function () {
                    self._updateZoomSpacer();
                });
                this._zoomResizeObserver.observe(content);
            }
        }

        this._updateZoomSpacer();

        // Ensure the parent container allows horizontal scrolling 
        // if the zoomed content exceeds the editor width.
        contentWrap.style.overflowX = 'auto';

        // Also update a zoom label indicator inside the toolbar
        var existing = document.getElementById('rte-zoom-label-' + this.id);
        if (existing) existing.textContent = zoomPct + '%';
    };

    RichTextEditor.prototype._zoomIn = function () {
        if (this._zoom < this._zoomMax) {
            this._zoom = Math.min(this._zoom + this._zoomStep, this._zoomMax);
            this._applyZoom();
        }
    };

    RichTextEditor.prototype._zoomOut = function () {
        if (this._zoom > this._zoomMin) {
            this._zoom = Math.max(this._zoom - this._zoomStep, this._zoomMin);
            this._applyZoom();
        }
    };

    RichTextEditor.prototype._zoomReset = function () {
        this._zoom = 1;
        this._applyZoom();
    };

    RichTextEditor.prototype._snapshotSelection = function () {
        var range = saveSelection(this.content);
        if (range) {
            this._savedRange = range;
            if (!range.collapsed) {
                this._lastNonCollapsedRange = range;
            }
        }
    };

    // ---- Custom undo/redo history (step-by-step, one level at a time) ----
    // Every meaningful content change (typing, table op, formatting) pushes
    // a snapshot to history. Undo pops one step, redo restores it.
    RichTextEditor.prototype._historyPush = function (htmlSnapshot) {
        // Skip during initialization (setHTMLCode from constructor)
        if (this._initializing) return;
        // Deduplicate: skip push if snapshot is identical to the last entry
        var current = this._history[this._historyIndex];
        if (current === htmlSnapshot) return;
        // Discard any "future" entries (redo stack) when a new action is taken
        if (this._historyIndex < this._history.length - 1) {
            this._history = this._history.slice(0, this._historyIndex + 1);
        }
        // Push (append + advance index)
        this._history.push(htmlSnapshot);
        this._historyIndex++;
        // Limit history size by trimming oldest entries
        while (this._history.length > this._historyMax) {
            this._history.shift();
            this._historyIndex--;
        }
    };

    RichTextEditor.prototype._historyUndo = function () {
        if (this._historyIndex <= 0) return;
        // Hide table selection overlay while restoring old state
        this._hideTableSelection();
        this._historyIndex--;
        var html = this._history[this._historyIndex];
        this.content.innerHTML = html;
        // Sync textarea but do NOT push to history (undo is not a new action)
        if (this._target.tagName === 'TEXTAREA') {
            this._target.value = this.content.innerHTML;
        }
        this._updateState();
    };

    RichTextEditor.prototype._historyRedo = function () {
        if (this._historyIndex >= this._history.length - 1) return;
        // Hide table selection overlay while restoring old state
        this._hideTableSelection();
        this._historyIndex++;
        var html = this._history[this._historyIndex];
        this.content.innerHTML = html;
        // Sync textarea but do NOT push to history (redo is not a new action)
        if (this._target.tagName === 'TEXTAREA') {
            this._target.value = this.content.innerHTML;
        }
        this._updateState();
    };

    RichTextEditor.prototype._historyCurrent = function () {
        return this._history[this._historyIndex] || '';
    };

    RichTextEditor.prototype._syncListStyles = function () {
        if (!this.content) return;
        var listItems = this.content.querySelectorAll('li');
        listItems.forEach(function (li) {
            var styledEl = null;
            var queue = [li];
            while (queue.length > 0) {
                var curr = queue.shift();
                if (curr !== li && curr.style && (
                    curr.style.fontSize ||
                    curr.style.fontFamily ||
                    curr.style.color ||
                    curr.style.fontWeight ||
                    curr.style.fontStyle ||
                    curr.style.textDecoration
                )) {
                    styledEl = curr;
                    break;
                }
                for (var i = 0; i < curr.children.length; i++) {
                    queue.push(curr.children[i]);
                }
            }

            if (styledEl) {
                var mapping = {
                    fontSize: '--li-font-size',
                    fontFamily: '--li-font-family',
                    color: '--li-color',
                    fontWeight: '--li-font-weight',
                    fontStyle: '--li-font-style',
                    textDecoration: '--li-text-decoration',
                    lineHeight: '--li-line-height'
                };
                Object.keys(mapping).forEach(function (prop) {
                    var varName = mapping[prop];
                    if (styledEl.style[prop]) {
                        li.style.setProperty(varName, styledEl.style[prop]);
                    } else {
                        li.style.removeProperty(varName);
                    }
                });
                li.style.fontSize = '';
                li.style.fontFamily = '';
                li.style.color = '';
                li.style.fontWeight = '';
                li.style.fontStyle = '';
                li.style.textDecoration = '';
                li.style.lineHeight = '';
            } else {
                var vars = ['--li-font-size', '--li-font-family', '--li-color', '--li-font-weight', '--li-font-style', '--li-text-decoration', '--li-line-height'];
                vars.forEach(function (v) {
                    li.style.removeProperty(v);
                });
                li.style.fontSize = '';
                li.style.fontFamily = '';
                li.style.color = '';
                li.style.fontWeight = '';
                li.style.fontStyle = '';
                li.style.textDecoration = '';
                li.style.lineHeight = '';
            }
        });
    };

    RichTextEditor.prototype._syncSource = function () {
        this._syncListStyles();
        var html = this.content.innerHTML;

        // Mirror to underlying textarea if any
        if (this._target.tagName === 'TEXTAREA') {
            this._target.value = html;
        }
        // Push current content to undo history (step-by-step)
        this._historyPush(html);
    };

    RichTextEditor.prototype._updateState = function () {
        this._syncListStyles();
        var self = this;

        // --- Non-align buttons: use queryCommandState (reliable for bold/italic/etc.) ---
        var map = {
            bold: 'bold', italic: 'italic', underline: 'underline', strike: 'strikeThrough',
            sub: 'subscript', sup: 'superscript',
            ul: 'insertUnorderedList', ol: 'insertOrderedList',
        };
        Object.keys(map).forEach(function (key) {
            var btn = self._buttons[key];
            if (!btn) return;
            try {
                btn.classList.toggle('rte-active', document.queryCommandState(map[key]));
            } catch (e) { }
        });

        // --- Alignment buttons: queryCommandState is UNRELIABLE in Chromium for justify commands.
        // Read text-align directly from the block element at the cursor instead. ---
        var alignMap = {
            alignleft: 'left',
            aligncenter: 'center',
            alignright: 'right',
            alignjustify: 'justify',
        };
        var selA = window.getSelection();
        var nodeA = null;
        var offsetA = 0;
        if (selA && selA.rangeCount > 0) {
            nodeA = selA.anchorNode;
            offsetA = selA.anchorOffset;
        }
        if (!nodeA || !self.content.contains(nodeA)) {
            if (self._savedRange) {
                nodeA = self._savedRange.startContainer;
                offsetA = self._savedRange.startOffset;
            }
        }

        // Resolve parent container-level boundary selections
        if (nodeA === self.content) {
            if (offsetA > 0 && self.content.childNodes[offsetA - 1]) {
                nodeA = self.content.childNodes[offsetA - 1];
            } else if (self.content.childNodes[offsetA]) {
                nodeA = self.content.childNodes[offsetA];
            }
        } else if (nodeA && nodeA.nodeType === Node.ELEMENT_NODE && nodeA.childNodes && nodeA.childNodes[offsetA]) {
            nodeA = nodeA.childNodes[offsetA];
        }
        if (!nodeA || !self.content.contains(nodeA)) {
            nodeA = self.content.firstChild || self.content;
        }

        var blockEl = nodeA ? (nodeA.nodeType === Node.TEXT_NODE ? nodeA.parentNode : nodeA) : null;
        var blockTags = /^(P|DIV|H[1-6]|LI|TD|TH|BLOCKQUOTE|PRE)$/;
        while (blockEl && blockEl !== self.content) {
            if (blockEl.tagName && blockTags.test(blockEl.tagName)) break;
            blockEl = blockEl.parentNode; // parentNode is safer than parentElement
        }
        if (!blockEl || blockEl === self.content) {
            blockEl = self.content.querySelector('p, div, h1, h2, h3, h4, h5, h6') || self.content;
        }
        var curAlign = 'left'; // default
        if (blockEl) {
            // Read computed or inline text-align
            var inlineAlign = blockEl.style && blockEl.style.textAlign ? blockEl.style.textAlign.toLowerCase() : '';
            if (inlineAlign) {
                curAlign = inlineAlign;
            } else {
                // Fall back to computed style
                try {
                    var computed = window.getComputedStyle(blockEl).textAlign;
                    if (computed) curAlign = computed.toLowerCase();
                } catch (e) { }
            }
        }
        // Normalize: 'start' = left, 'end' = right in LTR
        if (curAlign === 'start') curAlign = 'left';
        if (curAlign === 'end') curAlign = 'right';

        Object.keys(alignMap).forEach(function (key) {
            var btn = self._buttons[key];
            if (!btn) return;
            btn.classList.toggle('rte-active', curAlign === alignMap[key]);
        });

        var sel = window.getSelection();
        var node = sel ? sel.anchorNode : null;
        var activeList = node ? (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('ol, ul') : null;
        if (self._buttons['list_alpha']) {
            var isAlpha = activeList && activeList.tagName === 'OL' && activeList.type === 'a' && !activeList.classList.contains('rte-multilevel-list');
            self._buttons['list_alpha'].classList.toggle('rte-active', !!isAlpha);
        }
        if (self._buttons['list_multilevel']) {
            var isMulti = activeList && activeList.tagName === 'OL' && activeList.classList.contains('rte-multilevel-list');
            self._buttons['list_multilevel'].classList.toggle('rte-active', !!isMulti);
        }

        // Stats
        var text = this.content.textContent || '';
        var words = text.trim() ? text.trim().split(/\s+/).length : 0;
        var chars = text.length;
        var counts = this.statusbar.querySelector('.rte-status-counts');
        if (counts) counts.textContent = words + ' words \u2022 ' + chars + ' chars';

        // Update all dropdown button labels AND color swatches to reflect cursor position
        self._dropdowns.forEach(function (d) {
            if (d.updateLabel) d.updateLabel();
            if (d.updateSwatch) d.updateSwatch();
        });
    };

    RichTextEditor.prototype._bind = function () {
        var self = this;
        var c = this.content;

        c.addEventListener('input', function () {
            self._snapshotSelection();
            self._syncSource();
            self._updateState();
            if (self.config.onchange) try { self.config.onchange(self.getHTMLCode()); } catch (e) { }
        });
        c.addEventListener('keyup', function () { self._snapshotSelection(); self._updateState(); });
        c.addEventListener('mouseup', function () { self._snapshotSelection(); self._updateState(); });
        c.addEventListener('click', function () { self._snapshotSelection(); self._updateState(); });
        // selectionchange fires for any caret/selection move including arrow keys held down
        document.addEventListener('selectionchange', function () {
            if (document.activeElement === self.content ||
                (self.content.contains && self.content.contains(document.activeElement))) {
                self._snapshotSelection();
            }
        });

        // ---- INTEGRATED TABLE SELECTION: overlay + ONE unified float toolbar ----
        // mousedown on table cell → show overlay + float toolbar (single source of truth)
        c.addEventListener('mousedown', function (e) {
            var target = e.target;
            // Ignore clicks on overlay handles and float toolbar
            if (target.classList && (
                target.classList.contains('rte-table-move-handle') ||
                target.classList.contains('rte-table-resize-handle') ||
                target.closest('.rte-table-float-toolbar')
            )) return;
            if (self._isResizingOrMoving) return;

            var table = target.closest ? target.closest('table') : null;
            if (table && c.contains(table)) {
                var td = target.closest('td, th');
                if (td && table.contains(td)) {
                    self._startCellSelection(e, td, table);
                }

                self._showTableSelection(table);
                self._initTableResize(table);
                self._closeTablePopup();
            } else {
                self._clearCellSelection(self._selectedTable);
                self._hideTableSelection();
            }
        });

        // Click outside table (anywhere in content) closes selection + popup
        c.addEventListener('click', function (e) {
            var target = e.target;

            // Delete code block handling
            var deleteBtn = target.closest('.rte-code-block-delete-btn');
            if (deleteBtn) {
                e.stopPropagation();
                e.preventDefault();
                var container = deleteBtn.closest('.rte-code-block-container');
                if (container) {
                    Swal.fire({
                        title: 'Hapus Code Block?',
                        text: "Apakah Anda yakin ingin menghapus code block ini?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then(function (result) {
                        if (result.isConfirmed && container.parentNode) {
                            container.parentNode.removeChild(container);
                            self._syncSource();
                            self._updateState();
                        }
                    });
                }
                return;
            }

            if (target.classList && (
                target.classList.contains('rte-table-move-handle') ||
                target.classList.contains('rte-table-resize-handle') ||
                target.closest('.rte-table-float-toolbar')
            )) return;
            if (self._tablePopup && self._tablePopup.contains(target)) return;

            var table = target.closest ? target.closest('table') : null;
            if (!table || !c.contains(table)) {
                self._clearCellSelection(self._selectedTable);
                self._hideTableSelection();
                self._closeTablePopup();
            }
            // Coordinate-based hit test for iframes/videos (pointer-events:none makes them
            // transparent, so click fires on their parent — we detect via bounding rect).
            // IMPORTANT: skip elements inside a non-active carousel slide (opacity:0 slides
            // are stacked at the same coordinates and would otherwise be detected first).
            var clickedMedia = null;
            var allMediaEls = c.querySelectorAll('iframe, video');
            for (var mi = 0; mi < allMediaEls.length; mi++) {
                var mediaEl = allMediaEls[mi];
                // Skip media inside a carousel slide that is NOT active
                var parentSlide = mediaEl.closest ? mediaEl.closest('.rte-carousel-slide') : null;
                if (parentSlide && !parentSlide.classList.contains('active')) continue;
                var mr = mediaEl.getBoundingClientRect();
                if (e.clientX >= mr.left && e.clientX <= mr.right &&
                    e.clientY >= mr.top && e.clientY <= mr.bottom) {
                    clickedMedia = mediaEl;
                    break;
                }
            }

            var carousel = target.closest('.rte-carousel-container');
            var btnLink = target.closest('.rte-btn-link');
            if (!btnLink) {
                var possibleA = target.closest('a');
                if (possibleA && possibleA.style && (possibleA.style.padding || possibleA.style.borderRadius) && possibleA.style.backgroundColor) {
                    possibleA.classList.add('rte-btn-link');
                    btnLink = possibleA;
                }
            }

            if (clickedMedia) {
                clickedMedia.draggable = true;
                self._showVideoEditorPopup(clickedMedia);
            } else if (target.tagName === 'IMG') {
                // Skip if the IMG is inside a non-active carousel slide
                var imgSlide = target.closest ? target.closest('.rte-carousel-slide') : null;
                if (imgSlide && !imgSlide.classList.contains('active')) { /* do nothing */ }
                else {
                    self._closeVideoPopup();
                    self._closeCarouselPopup();
                    self._closeButtonPopup();
                    self._showImageEditorPopup(target);
                }
            } else if (target.tagName === 'VIDEO' || target.tagName === 'IFRAME') {
                self._closeImagePopup();
                self._closeCarouselPopup();
                self._closeButtonPopup();
                target.draggable = true; // ensure it can be dragged
                self._showVideoEditorPopup(target);
            } else if (carousel) {
                self._closeImagePopup();
                self._closeVideoPopup();
                self._closeButtonPopup();
                self._closeTocPopup();
                self._showCarouselEditorPopup(carousel);
            } else if (btnLink) {
                self._closeImagePopup();
                self._closeVideoPopup();
                self._closeCarouselPopup();
                self._closeTocPopup();
                self._showButtonEditorPopup(btnLink);
            } else if (target.closest('.rte-toc-block') && !target.closest('.rte-toc-item a')) {
                var tocBlock = target.closest('.rte-toc-block');
                self._closeImagePopup();
                self._closeVideoPopup();
                self._closeCarouselPopup();
                self._closeButtonPopup();
                self._closeSearchBlockPopup();
                self._showTocEditorPopup(tocBlock);
            } else if (target.closest('.rte-search-block')) {
                var searchBlock = target.closest('.rte-search-block');
                self._closeImagePopup();
                self._closeVideoPopup();
                self._closeCarouselPopup();
                self._closeButtonPopup();
                self._closeTocPopup();
                self._showSearchBlockEditorPopup(searchBlock);
            } else if (target.tagName !== 'TD' && target.tagName !== 'TH' && !target.closest('.rte-img-overlay')) {
                self._closeImagePopup();
                self._closeVideoPopup();
                self._closeCarouselPopup();
                self._closeButtonPopup();
                self._closeTocPopup();
                self._closeSearchBlockPopup();
                self._removeMediaResizeHandle();
            }
        });

        var lastTocScrollTime = 0;
        var handleTocClick = function (e) {
            var a = e.target.closest('.rte-toc-item a');
            if (a) {
                e.preventDefault();
                e.stopPropagation();
                var now = Date.now();
                if (now - lastTocScrollTime < 100) return;
                lastTocScrollTime = now;
                var targetId = a.getAttribute('data-rte-target') || a.getAttribute('href');
                if (targetId) {
                    var cleanId = targetId;
                    var hashIndex = cleanId.indexOf('#');
                    if (hashIndex !== -1) {
                        cleanId = cleanId.substring(hashIndex + 1);
                    } else if (cleanId.indexOf('http') === 0) {
                        cleanId = ''; // Not a hash link
                    } else {
                        cleanId = cleanId.replace(/^#/, '');
                    }
                    if (!cleanId) return;

                    var heading = null;
                    try {
                        heading = document.getElementById(cleanId);
                    } catch (e) {}
                    if (!heading) {
                        try {
                            heading = c.querySelector('[data-rte-anchor="' + cleanId.replace(/"/g, '\\"') + '"]');
                        } catch (err) {}
                    }
                    if (!heading) {
                        var cleanLower = cleanId.toLowerCase().replace(/[^a-z0-9]/g, '');
                        var allNodes = c.querySelectorAll('*');
                        for (var ni = 0; ni < allNodes.length; ni++) {
                            var elNode = allNodes[ni];
                            var nodeAnchor = elNode.getAttribute('data-rte-anchor');
                            var nodeId = elNode.id;
                            if ((nodeAnchor && nodeAnchor.toLowerCase().replace(/[^a-z0-9]/g, '') === cleanLower) || 
                                (nodeId && nodeId.toLowerCase().replace(/[^a-z0-9]/g, '') === cleanLower)) {
                                heading = elNode;
                                break;
                            }
                        }
                    }
                    if (!heading) {
                        var cleanLower = cleanId.toLowerCase().replace(/[^a-z0-9]/g, '');
                        var rootTarget = cleanLower.replace(/\d+$/, '');
                        var allCandidates = c.querySelectorAll('[data-rte-anchor]');
                        for (var ci = 0; ci < allCandidates.length; ci++) {
                            var candId = allCandidates[ci].getAttribute('data-rte-anchor').toLowerCase().replace(/[^a-z0-9]/g, '');
                            var candRoot = candId.replace(/\d+$/, '');
                            if (candRoot === rootTarget || candId === rootTarget || candId.indexOf(rootTarget) === 0 || rootTarget.indexOf(candRoot) === 0) {
                                heading = allCandidates[ci];
                                break;
                            }
                        }
                    }
                    if (!heading) {
                        var linkText = (a.textContent || '').trim();
                        if (linkText) {
                            var normalizedText = linkText.toLowerCase().replace(/[^a-z0-9]/g, '');
                            
                            // 1. Exact match on standard headings
                            var headings = c.querySelectorAll('h1, h2, h3, h4, h5, h6');
                            for (var i = 0; i < headings.length; i++) {
                                if (headings[i].closest && headings[i].closest('.rte-toc-block')) continue;
                                var txt = (headings[i].textContent || '').trim().toLowerCase().replace(/[^a-z0-9]/g, '');
                                if (txt === normalizedText) {
                                    heading = headings[i];
                                    break;
                                }
                            }
                            // 2. Exact match on standard text blocks
                            if (!heading) {
                                var textBlocks = c.querySelectorAll('p, blockquote, li, td, th, div');
                                for (var i = 0; i < textBlocks.length; i++) {
                                    if (textBlocks[i].closest && textBlocks[i].closest('.rte-toc-block')) continue;
                                    var txt = (textBlocks[i].textContent || '').trim().toLowerCase().replace(/[^a-z0-9]/g, '');
                                    if (txt === normalizedText) {
                                        heading = textBlocks[i];
                                        break;
                                    }
                                }
                            }
                            // 3. Partial match on standard headings
                            if (!heading) {
                                for (var i = 0; i < headings.length; i++) {
                                    if (headings[i].closest && headings[i].closest('.rte-toc-block')) continue;
                                    var txt = (headings[i].textContent || '').trim().toLowerCase().replace(/[^a-z0-9]/g, '');
                                    if (txt && (txt.indexOf(normalizedText) !== -1 || normalizedText.indexOf(txt) !== -1)) {
                                        heading = headings[i];
                                        break;
                                    }
                                }
                            }
                            // 4. Partial match on standard text blocks (excluding parent containers)
                            if (!heading) {
                                var textBlocks = c.querySelectorAll('p, blockquote, li, td, th, div');
                                for (var i = 0; i < textBlocks.length; i++) {
                                    var elNode = textBlocks[i];
                                    if (elNode.closest && elNode.closest('.rte-toc-block')) continue;
                                    if (elNode.tagName === 'DIV' && elNode.querySelector('p, h1, h2, h3, h4, h5, h6, li, blockquote')) continue;
                                    var txt = (elNode.textContent || '').trim().toLowerCase().replace(/[^a-z0-9]/g, '');
                                    if (txt && (txt.indexOf(normalizedText) !== -1 || normalizedText.indexOf(txt) !== -1)) {
                                        heading = elNode;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if (!heading) {
                        var tocItem = a.closest('.rte-toc-item');
                        if (tocItem) {
                            var tocIndexAttr = tocItem.getAttribute('data-rte-toc-index');
                            var tocIndex = tocIndexAttr ? parseInt(tocIndexAttr, 10) - 1 : -1;
                            if (tocIndex === -1) {
                                var siblings = Array.prototype.slice.call(tocItem.parentNode.children);
                                tocIndex = siblings.indexOf(tocItem);
                            }
                            if (tocIndex >= 0) {
                                var candidates = Array.prototype.slice.call(c.querySelectorAll('h1, h2, h3, h4, h5, h6, p, blockquote, li, div, td, th'));
                                candidates = candidates.filter(function(el2) {
                                    if (el2.closest && el2.closest('.rte-toc-block')) return false;
                                    if (el2.querySelector('h1, h2, h3, h4, h5, h6, p, blockquote, li')) return false;
                                    var text = (el2.textContent || '').trim();
                                    if (!text || text.length < 2) return false;
                                    return true;
                                });
                                if (tocIndex < candidates.length) {
                                    heading = candidates[tocIndex];
                                }
                            }
                        }
                    }
                    if (heading && cleanId) {
                        if (!heading.id) heading.id = cleanId;
                        if (!heading.getAttribute('data-rte-anchor')) heading.setAttribute('data-rte-anchor', cleanId);
                    }
                    if (heading) {
                        c.querySelectorAll('.rte-toc-highlight-target').forEach(function (el) {
                            el.classList.remove('rte-toc-highlight-target');
                        });
                        heading.style.scrollMarginTop = '130px';
                        heading.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        heading.classList.add('rte-toc-highlight-target');
                        heading.querySelectorAll('*').forEach(function(child) {
                            child.classList.add('rte-toc-highlight-target');
                        });

                        heading.setAttribute('tabindex', '-1');
                        heading.focus();

                        // Directly highlight the exact target heading element via Selection API
                        // Run at multiple intervals to ensure it persists during smooth scrolling & focus shifts.
                        function applyHighlightSelection() {
                            try {
                                var doc = heading.ownerDocument || document;
                                var win = doc.defaultView || window;
                                var range = doc.createRange();
                                var textNode = null;
                                var walk = doc.createTreeWalker(heading, NodeFilter.SHOW_TEXT, null, false);
                                var next = walk.nextNode();
                                while (next) {
                                    if (next.textContent.trim()) {
                                        textNode = next;
                                        break;
                                    }
                                    next = walk.nextNode();
                                }
                                if (textNode) {
                                    var parent = textNode.parentNode;
                                    if (parent && (parent.tagName === 'A' || parent.closest('a'))) {
                                        range.selectNodeContents(parent.closest('a') || parent);
                                    } else {
                                        range.setStart(textNode, 0);
                                        range.setEnd(textNode, textNode.textContent.length);
                                    }
                                } else {
                                    range.selectNodeContents(heading);
                                }
                                var sel = win.getSelection();
                                sel.removeAllRanges();
                                sel.addRange(range);
                            } catch (e) { }
                        }
                        applyHighlightSelection();
                        setTimeout(applyHighlightSelection, 50);
                        setTimeout(applyHighlightSelection, 200);
                        setTimeout(applyHighlightSelection, 500);
                        setTimeout(applyHighlightSelection, 800);

                        setTimeout(function () {
                            heading.classList.remove('rte-toc-highlight-target');
                            heading.querySelectorAll('*').forEach(function(child) {
                                child.classList.remove('rte-toc-highlight-target');
                            });
                            heading.removeAttribute('tabindex');
                            // Clear selection highlight after animation finishes
                            try {
                                window.getSelection().removeAllRanges();
                            } catch (e) { }
                        }, 2500);
                    }
                }
            }
        };
        c.addEventListener('click', handleTocClick);
        c.addEventListener('mousedown', handleTocClick);

        // Hide popups and handle realtime reflow when dragging media natively
        var draggingInternalMedia = false;
        var draggedNode = null;
        var dragEmptyImg = new Image();
        dragEmptyImg.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
        var dragOffsetX = 0;
        var dragOffsetY = 0;

        c.addEventListener('dragstart', function (e) {
            var isInterceptor = e.target.classList && e.target.classList.contains('rte-iframe-click-interceptor');
            if (e.target.tagName === 'IMG' || e.target.tagName === 'VIDEO' || e.target.tagName === 'IFRAME' || isInterceptor) {
                draggingInternalMedia = true;

                var targetMedia = e.target;
                if (isInterceptor) {
                    targetMedia = e.target.parentNode.querySelector('video, iframe') || e.target.parentNode;
                }
                draggedNode = targetMedia.closest('figure') || targetMedia;

                var rect = draggedNode.getBoundingClientRect();
                dragOffsetX = e.clientX - rect.left;
                dragOffsetY = e.clientY - rect.top;

                if (e.dataTransfer) {
                    e.dataTransfer.setData('text/plain', ''); // Required by Firefox and sometimes Chrome to actually start drag
                    if (e.dataTransfer.setDragImage) {
                        e.dataTransfer.setDragImage(dragEmptyImg, 0, 0);
                    }
                }

                draggedNode.dataset.oldOpacity = draggedNode.style.opacity || '';
                draggedNode.style.opacity = '0.5';

                self._closeImagePopup();
                self._closeVideoPopup();

                if (self._mediaResizeHandle) {
                    self._mediaResizeHandle.style.display = 'none';
                }
            }
        });

        c.addEventListener('dragover', function (e) {
            if (draggingInternalMedia && draggedNode) {
                e.preventDefault();

                if (draggedNode.style.position === 'absolute') {
                    var cRect = c.getBoundingClientRect();
                    var newLeft = e.clientX - cRect.left - dragOffsetX + c.scrollLeft;
                    var newTop = e.clientY - cRect.top - dragOffsetY + c.scrollTop;
                    draggedNode.style.left = newLeft + 'px';
                    draggedNode.style.top = newTop + 'px';
                    return;
                }

                var cRect = c.getBoundingClientRect();
                if (e.clientY > cRect.bottom - 40) {
                    var p = document.createElement('p');
                    p.innerHTML = '<br>';
                    c.appendChild(p);
                }

                var oldDisplay = draggedNode.style.display;
                draggedNode.style.display = 'none';

                var range = (document.caretRangeFromPoint || document.caretPositionFromPoint)
                    ? (document.caretRangeFromPoint
                        ? document.caretRangeFromPoint(e.clientX, e.clientY)
                        : null)
                    : null;

                draggedNode.style.display = oldDisplay;

                if (range && c.contains(range.startContainer)) {
                    var container = range.startContainer;
                    var elNode = container.nodeType === 3 ? container.parentNode : container;
                    var targetFigure = elNode.closest ? elNode.closest('figure, img:not([data-rte-ui]), video:not([data-rte-ui])') : null;

                    if (targetFigure && targetFigure !== draggedNode) {
                        var rect = targetFigure.getBoundingClientRect();
                        var insertAfter = e.clientX > (rect.left + rect.width / 2);
                        var r2 = document.createRange();
                        if (insertAfter) {
                            r2.setStartAfter(targetFigure);
                        } else {
                            r2.setStartBefore(targetFigure);
                        }
                        r2.collapse(true);
                        r2.insertNode(draggedNode);
                    } else if (!targetFigure) {
                        range.insertNode(draggedNode);
                    }
                }
            }
        });

        c.addEventListener('dragend', function (e) {
            if (draggedNode) {
                draggedNode.style.opacity = draggedNode.dataset.oldOpacity || '';
                delete draggedNode.dataset.oldOpacity;
            }
            draggingInternalMedia = false;
            draggedNode = null;

            if (self._mediaResizeHandle) {
                self._mediaResizeHandle.style.display = '';
            }
            self._removeMediaResizeHandle();

            self._syncSource();
        });

        // Update overlay position on scroll
        c.addEventListener('scroll', function () {
            if (self._selectedTable) self._updateTableOverlayPosition();
            self._updatePopupPositions();
        });
        // rte-content-wrap is the real scrollable container
        c.parentElement.addEventListener('scroll', function () {
            if (self._selectedTable) self._updateTableOverlayPosition();
            self._updatePopupPositions();
        });
        window.addEventListener('scroll', function () {
            if (self._selectedTable) self._updateTableOverlayPosition();
            self._updatePopupPositions();
        });
        window.addEventListener('resize', function () {
            if (self._selectedTable) self._updateTableOverlayPosition();
            self._updatePopupPositions();
        });
        c.addEventListener('blur', function () { self._snapshotSelection(); });
        c.addEventListener('focus', function () { self._updateState(); });

        // Hide table selection + cell selection when clicking completely outside the editor wrapper
        document.addEventListener('mousedown', function (e) {
            // self.wrapper is the outermost RTE container element
            if (!self.wrapper.contains(e.target)) {
                // Ignore sweetalert dialogs, image popup, table popup
                if (e.target.closest && (
                    e.target.closest('.swal2-container') ||
                    e.target.closest('.rte-context-popup') ||
                    e.target.closest('.rte-table-float-toolbar') ||
                    e.target.closest('.rte-img-toolbar') ||
                    e.target.closest('.rte-img-overlay') ||
                    e.target.closest('.rte-iframe-click-interceptor')
                )) return;
                self._clearCellSelection(self._selectedTable);
                self._hideTableSelection();
                self._closeTablePopup();
                self._closeImagePopup();
                self._closeVideoPopup();
                self._closeCarouselPopup();
                self._closeButtonPopup();
                self._closeTocPopup();
                self._removeMediaResizeHandle();
            }
        });

        // Source area syncs back to content on blur
        this.sourceArea.addEventListener('input', function () {
            if (self._target.tagName === 'TEXTAREA') self._target.value = self.sourceArea.value;
        });

        // Paste cleanup: prefer plain text-ish; allow HTML but strip dangerous
        c.addEventListener('paste', function (e) {
            if (!e.clipboardData) return;
            var html = e.clipboardData.getData('text/html');
            var text = e.clipboardData.getData('text/plain');
            if (html) {
                e.preventDefault();
                var clean = self._sanitizePastedHTML(html);
                document.execCommand('insertHTML', false, clean);
            } else if (text) {
                // Let native handle plain text — fine
            }
        });

        // Drag-and-drop image upload
        c.addEventListener('drop', function (e) {
            if (draggingInternalMedia) {
                e.preventDefault(); // Node already moved in dragover, handled in dragend
                return;
            }
            if (!e.dataTransfer || !e.dataTransfer.files || !e.dataTransfer.files.length) return;
            var f = e.dataTransfer.files[0];
            if (!/^image\//.test(f.type)) return;
            e.preventDefault();
            // Move caret to drop point
            var range = (document.caretRangeFromPoint || document.caretPositionFromPoint)
                ? (document.caretRangeFromPoint
                    ? document.caretRangeFromPoint(e.clientX, e.clientY)
                    : null)
                : null;
            if (range) { var sel = window.getSelection(); sel.removeAllRanges(); sel.addRange(range); self._snapshotSelection(); }
            self._uploadImage(f, function (url) {
                self._insertHTML('<img src="' + escapeHtml(url) + '" alt="">');
            });
        });

        // Keyboard shortcuts
        c.addEventListener('keydown', function (e) {
            if (e.ctrlKey || e.metaKey) {
                var k = e.key.toLowerCase();
                if (k === 'b') { e.preventDefault(); self.exec('bold'); }
                else if (k === 'i') { e.preventDefault(); self.exec('italic'); }
                else if (k === 'u') { e.preventDefault(); self.exec('underline'); }
                else if (k === 'k') { e.preventDefault(); self._dialogLink(); }
                else if (k === 'z') { e.preventDefault(); self._historyUndo(); }
                else if (k === 'y') { e.preventDefault(); self._historyRedo(); }
                self._snapshotSelection();
                self._updateState();
            }

            // Enter key custom handling for code blocks and custom styled containers
            if (e.key === 'Enter') {
                var sel = window.getSelection();
                if (sel && sel.rangeCount > 0) {
                    var range = sel.getRangeAt(0);
                    if (range.collapsed && range.startContainer) {
                        var node = range.startContainer;
                        var block = (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('p, div, h1, h2, h3, h4, h5, h6, blockquote, pre');

                        if (block && block !== self.content) {
                            function isAtStartOf(el) {
                                var testRange = document.createRange();
                                testRange.setStart(el, 0);
                                testRange.setEnd(range.startContainer, range.startOffset);
                                return testRange.toString().trim().length === 0;
                            }

                            function isAtEndOf(el) {
                                var testRange = document.createRange();
                                testRange.setStart(range.startContainer, range.startOffset);
                                testRange.setEnd(el, el.childNodes.length);
                                return testRange.toString().trim().length === 0;
                            }

                            if (block.tagName === 'PRE') {
                                e.preventDefault();
                                var newline = document.createTextNode('\n');
                                range.insertNode(newline);
                                range.setStartAfter(newline);
                                range.collapse(true);
                                sel.removeAllRanges();
                                sel.addRange(range);
                                self._syncSource();
                                self._updateState();
                                return;
                            }

                            var isCustomDiv = (block.tagName === 'DIV' && (block.className || block.getAttribute('style')));
                            if (block.tagName === 'BLOCKQUOTE' || isCustomDiv) {
                                if (isAtStartOf(block)) {
                                    e.preventDefault();
                                    var p = document.createElement('p');
                                    p.appendChild(document.createElement('br'));
                                    block.parentNode.insertBefore(p, block);
                                    self._syncSource();
                                    self._updateState();
                                    return;
                                } else if (isAtEndOf(block)) {
                                    e.preventDefault();
                                    var p = document.createElement('p');
                                    p.appendChild(document.createElement('br'));
                                    block.parentNode.insertBefore(p, block.nextSibling);
                                    var newRange = document.createRange();
                                    newRange.selectNodeContents(p);
                                    newRange.collapse(true);
                                    sel.removeAllRanges();
                                    sel.addRange(newRange);
                                    self._syncSource();
                                    self._updateState();
                                    return;
                                } else {
                                    e.preventDefault();
                                    var br = document.createElement('br');
                                    range.insertNode(br);
                                    range.setStartAfter(br);
                                    range.collapse(true);
                                    sel.removeAllRanges();
                                    sel.addRange(range);
                                    self._syncSource();
                                    self._updateState();
                                    return;
                                }
                            }
                        }
                    }
                }
            }

            // Backspace custom handling for Word-like list & indent behavior
            if (e.key === 'Backspace') {
                var sel = window.getSelection();
                if (sel && sel.rangeCount > 0) {
                    var range = sel.getRangeAt(0);
                    if (range.collapsed && range.startContainer) {
                        var node = range.startContainer;

                        function isAtStartOf(el) {
                            var testRange = document.createRange();
                            testRange.setStart(el, 0);
                            testRange.setEnd(range.startContainer, range.startOffset);
                            var beforeText = testRange.toString().trim();
                            var beforeFrag = testRange.cloneContents();
                            var hasMediaBefore = beforeFrag.querySelector('img, table, iframe, video, audio');
                            return beforeText.length === 0 && !hasMediaBefore;
                        }

                        var block = (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('p, div, h1, h2, h3, h4, h5, h6, blockquote, pre');
                        if (block && block !== self.content) {
                            var specialAncestor = null;
                            var parentWalk = block;
                            while (parentWalk && parentWalk !== self.content) {
                                var isParentCustomDiv = (parentWalk.tagName === 'DIV' && (parentWalk.className || parentWalk.getAttribute('style')));
                                if (parentWalk.tagName === 'BLOCKQUOTE' || parentWalk.tagName === 'PRE' || isParentCustomDiv) {
                                    specialAncestor = parentWalk;
                                }
                                parentWalk = parentWalk.parentNode;
                            }

                            if (specialAncestor && isAtStartOf(specialAncestor)) {
                                var cleanText = specialAncestor.textContent.replace(/[\u200B\u200C\u200D\uFEFF]/g, '').trim();
                                var hasMedia = specialAncestor.querySelector('img, table, iframe, video, audio');
                                if (cleanText.length === 0 && !hasMedia) {
                                    e.preventDefault();
                                    var prev = specialAncestor.previousElementSibling;
                                    if (prev) {
                                        specialAncestor.parentNode.removeChild(specialAncestor);
                                        var newRange = document.createRange();
                                        newRange.selectNodeContents(prev);
                                        newRange.collapse(false);
                                        sel.removeAllRanges();
                                        sel.addRange(newRange);
                                    } else {
                                        var p = document.createElement('p');
                                        p.appendChild(document.createElement('br'));
                                        specialAncestor.parentNode.replaceChild(p, specialAncestor);
                                        var newRange = document.createRange();
                                        newRange.selectNodeContents(p);
                                        newRange.collapse(true);
                                        sel.removeAllRanges();
                                        sel.addRange(newRange);
                                    }
                                    self._syncSource();
                                    self._updateState();
                                    return;
                                }
                            }
                        }

                        var li = (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('li');
                        if (li) {
                            if (isAtStartOf(li)) {
                                e.preventDefault();
                                var parentList = li.closest('ol, ul');
                                var isNested = parentList && parentList.parentNode && parentList.parentNode.closest('ol, ul');

                                if (isNested) {
                                    var marker = document.createElement('span');
                                    marker.id = 'rte-temp-outdent-marker'; marker.textContent = '\u200B';
                                    range.insertNode(marker);
                                    range.selectNode(marker);
                                    range.collapse(false);
                                    sel.removeAllRanges(); sel.addRange(range);

                                    self.exec('outdent');

                                    var restoredMarker = document.getElementById('rte-temp-outdent-marker');
                                    if (restoredMarker) {
                                        var newRange = document.createRange();
                                        newRange.setStartAfter(restoredMarker);
                                        newRange.collapse(true);
                                        sel.removeAllRanges(); sel.addRange(newRange);
                                        restoredMarker.parentNode.removeChild(restoredMarker);
                                    }
                                } else {
                                    // Manual, 100% clean top-level outdent (avoids browser execCommand layout bugs)
                                    var p = document.createElement('p');
                                    p.style.marginLeft = '1.5rem';

                                    while (li.firstChild) {
                                        var child = li.firstChild;
                                        if (child.tagName === 'P' || child.tagName === 'DIV') {
                                            while (child.firstChild) {
                                                p.appendChild(child.firstChild);
                                            }
                                            li.removeChild(child);
                                        } else {
                                            p.appendChild(child);
                                        }
                                    }
                                    if (p.childNodes.length === 0) {
                                        p.appendChild(document.createElement('br'));
                                    }

                                    var nextLis = [];
                                    var nextSib = li.nextElementSibling;
                                    while (nextSib) {
                                        nextLis.push(nextSib);
                                        nextSib = nextSib.nextElementSibling;
                                    }

                                    parentList.parentNode.insertBefore(p, parentList.nextSibling);

                                    if (nextLis.length > 0) {
                                        var newList = document.createElement(parentList.tagName);
                                        newList.className = parentList.className;
                                        if (parentList.getAttribute('type')) newList.setAttribute('type', parentList.getAttribute('type'));
                                        if (parentList.getAttribute('style')) newList.setAttribute('style', parentList.getAttribute('style'));

                                        nextLis.forEach(function (nLi) {
                                            newList.appendChild(nLi);
                                        });
                                        p.parentNode.insertBefore(newList, p.nextSibling);
                                    }

                                    li.parentNode.removeChild(li);
                                    if (parentList.children.length === 0) {
                                        parentList.parentNode.removeChild(parentList);
                                    }

                                    var newRange = document.createRange();
                                    newRange.selectNodeContents(p);
                                    newRange.collapse(true);
                                    sel.removeAllRanges(); sel.addRange(newRange);
                                }

                                self._syncSource();
                                self._updateState();
                                return;
                            }
                        } else {
                            var block = (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('p, div, h1, h2, h3, h4, h5, h6, blockquote, pre');
                            if (block && isAtStartOf(block)) {
                                var prevBlock = null;
                                var sibling = block.previousElementSibling;
                                while (sibling) {
                                    if (/^(P|DIV|H[1-6]|BLOCKQUOTE|PRE|OL|UL|TABLE)$/.test(sibling.tagName)) {
                                        prevBlock = sibling;
                                        break;
                                    }
                                    sibling = sibling.previousElementSibling;
                                }
                                if (!prevBlock) {
                                    // First block in the editor: clear center/right alignment first
                                    if (block.style.textAlign && block.style.textAlign !== 'left') {
                                        e.preventDefault();
                                        block.style.textAlign = '';
                                        self._syncSource();
                                        self._updateState();
                                        return;
                                    }
                                }
                                if (block.style.marginLeft || block.style.paddingLeft) {
                                    e.preventDefault();
                                    var currentMargin = parseFloat(block.style.marginLeft) || 0;
                                    if (currentMargin > 40) {
                                        block.style.marginLeft = (currentMargin - 40) + 'px';
                                    } else {
                                        block.style.marginLeft = '';
                                    }
                                    block.style.paddingLeft = '';
                                    self._syncSource();
                                    self._updateState();
                                    return;
                                }
                            }
                        }
                    }
                }
            }

            // Tab inside table -> next cell, otherwise -> indent
            if (e.key === 'Tab') {
                var sel = window.getSelection();
                var node = (sel && sel.rangeCount > 0) ? sel.anchorNode : null;
                var td = node ? (node.nodeType === Node.TEXT_NODE ? node.parentNode : node).closest('td, th') : null;

                if (td) {
                    e.preventDefault();
                    var cells = Array.from(td.closest('table').querySelectorAll('td, th'));
                    var idx = cells.indexOf(td);
                    var nextCell = e.shiftKey ? cells[idx - 1] : cells[idx + 1];
                    if (nextCell) {
                        var range = document.createRange();
                        range.selectNodeContents(nextCell);
                        range.collapse(false);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                } else {
                    e.preventDefault();
                    self.exec(e.shiftKey ? 'outdent' : 'indent');
                }
            }
        });

        // Click outside fullscreen / Esc to leave
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && self.wrapper.classList.contains('rte-fullscreen')) {
                self._toggleFullscreen();
            }
        });
    };
    RichTextEditor.prototype._sanitizePastedHTML = function (html) {
        // Strip <script>, <style>, on* attributes, MS Office cruft, colgroups, and cols
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var disallowed = ['script', 'style', 'meta', 'link', 'object', 'embed', 'colgroup', 'col'];
        disallowed.forEach(function (tag) {
            var nodes = doc.querySelectorAll(tag);
            nodes.forEach(function (n) { n.parentNode.removeChild(n); });
        });
        this._stripEditorStyles(doc.body);

        var JUNK_STYLE = /(^|;)\s*(mso-[a-z-]+|widows|orphans|page-break-(?:before|after|inside)|break-(?:before|after|inside)|text-indent|letter-spacing|word-spacing)\s*:[^;]*/gi;

        var all = doc.body.querySelectorAll('*');
        all.forEach(function (n) {
            var tagName = n.tagName.toLowerCase();
            if (tagName === 'table') {
                n.classList.add('rte-table');
                n.style.width = '';
                n.style.height = '';
                n.style.minWidth = '';
                n.style.maxWidth = '';
                n.style.tableLayout = 'fixed';
                n.removeAttribute('width');
                n.removeAttribute('height');
            } else if (tagName === 'td' || tagName === 'th') {
                n.style.width = '';
                n.style.height = '';
                n.style.minWidth = '';
                n.style.maxWidth = '';
                n.style.whiteSpace = '';
                n.removeAttribute('width');
                n.removeAttribute('height');
            } else if (tagName === 'tr') {
                n.style.height = '';
                n.removeAttribute('height');
            } else {
                var parentCell = n.closest ? n.closest('td, th') : null;
                if (parentCell) {
                    n.style.width = '';
                    n.style.height = '';
                    n.style.minWidth = '';
                    n.style.maxWidth = '';
                    n.style.whiteSpace = '';
                    n.style.wordBreak = '';
                    n.removeAttribute('width');
                    n.removeAttribute('height');
                }
            }

            for (var i = n.attributes.length - 1; i >= 0; i--) {
                var attr = n.attributes[i];
                // Drop on* event handlers
                if (/^on/i.test(attr.name)) { n.removeAttribute(attr.name); continue; }
                // Drop MS Office xmlns / metadata attrs
                if (/^xmlns/.test(attr.name)) { n.removeAttribute(attr.name); continue; }

                // Drop non-RTE classes to prevent external stylesheet clashing
                if (attr.name === 'class' && tagName !== 'table') {
                    var classes = attr.value.split(/\s+/);
                    var hasRteClass = classes.some(function (c) {
                        return c.indexOf('rte-') === 0 || c.indexOf('language-') === 0;
                    });
                    if (!hasRteClass) {
                        n.removeAttribute('class');
                        continue;
                    }
                }

                if (attr.name === 'style') {
                    // Strip MS-Office / pagination junk first.
                    var cleaned = attr.value.replace(JUNK_STYLE, '').trim();
                    cleaned = cleaned.replace(/^;+|;+$/g, '').replace(/;\s*;/g, ';');
                    if (cleaned) {
                        n.setAttribute('style', cleaned);

                        // We intentionally KEEP color, font-family, font-size, background-color,
                        // and other text styles so pasted HTML matches the source formatting.
                        var keys = [];
                        for (var k = 0; k < n.style.length; k++) {
                            keys.push(n.style[k]);
                        }

                        keys.forEach(function (prop) {
                            var isJunk = /^(widows|orphans|text-indent|letter-spacing|word-spacing|page-break|break-inside)$/i.test(prop);
                            if (isJunk) {
                                n.style.removeProperty(prop);
                            }
                        });

                        if (n.style.cssText === '' || !n.getAttribute('style')) {
                            n.removeAttribute('style');
                        }
                    } else {
                        n.removeAttribute('style');
                    }
                }
            }
        });

        return doc.body.innerHTML;
    };
    // ------- Public API -------
    RichTextEditor.prototype.getHTMLCode = function () {
        if (this.sourceArea.style.display !== 'none') {
            return this.sourceArea.value;
        }
        this._stripEditorStyles(this.content);
        return this.content.innerHTML;
    };
    RichTextEditor.prototype.getHTML = RichTextEditor.prototype.getHTMLCode;

    RichTextEditor.prototype.setHTMLCode = function (html) {
        var safe = (html == null) ? '' : html;
        if (this.sourceArea.style.display !== 'none') {
            this.sourceArea.value = safe;
        }
        this.content.innerHTML = safe;
        this._stripEditorStyles(this.content);
        this._upgradeCarouselCaptions();
        this._assignAnchorIds();
        this._syncSource();
        this._updateState();
    };
    RichTextEditor.prototype.setHTML = RichTextEditor.prototype.setHTMLCode;

    RichTextEditor.prototype.setHeight = function (height) {
        if (this.wrapper) {
            this.wrapper.style.height = typeof height === 'number' ? height + 'px' : height;
        }
    };

    RichTextEditor.prototype.insertHTML = function (html) { this._insertHTML(html); };

    RichTextEditor.prototype.insertImageByUrl = function (url, alt) {
        this._insertHTML('<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(alt || '') + '">');
    };

    RichTextEditor.prototype.focus = function () {
        this.content.focus();
        placeCursorAtEnd(this.content);
    };

    RichTextEditor.prototype.destroy = function () {
        if (this._zoomResizeObserver) {
            this._zoomResizeObserver.disconnect();
            this._zoomResizeObserver = null;
        }
        if (this.wrapper && this.wrapper.parentNode) this.wrapper.parentNode.removeChild(this.wrapper);
        if (this._target && this._target.tagName === 'TEXTAREA') this._target.style.display = '';
    };

    // Static plugin registry (compat shim)
    RichTextEditor.plugins = RichTextEditor.plugins || {};
    RichTextEditor.registerPlugin = function (name, pluginFn) {
        RichTextEditor.plugins[name] = pluginFn;
    };

    RichTextEditor.prototype._loadPrism = function () {
        if (window.Prism) {
            try {
                window.Prism.highlightAllUnder(this.content);
            } catch (e) { }
            return;
        }
        var self = this;
        // Load CSS
        if (!document.querySelector('link[href*="prism.min.css"]')) {
            var link = el('link', {
                rel: 'stylesheet',
                href: 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css'
            });
            document.head.appendChild(link);
        }
        // Load JS
        if (!window.PrismLoading) {
            window.PrismLoading = true;
            var script = el('script', {
                src: 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js'
            });
            script.onload = function () {
                var autoloader = el('script', {
                    src: 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js'
                });
                autoloader.onload = function () {
                    if (window.Prism) {
                        try {
                            window.Prism.highlightAllUnder(self.content);
                        } catch (e) { }
                    }
                };
                document.head.appendChild(autoloader);
            };
            document.head.appendChild(script);
        } else {
            var interval = setInterval(function () {
                if (window.Prism && window.Prism.plugins && window.Prism.plugins.autoloader) {
                    clearInterval(interval);
                    try {
                        window.Prism.highlightAllUnder(self.content);
                    } catch (e) { }
                }
            }, 100);
        }
    };

    RichTextEditor.prototype._upgradeCarouselCaptions = function () {
        var self = this;
        var oldCaptions = this.content.querySelectorAll('.rte-carousel-caption');
        oldCaptions.forEach(function (cap) {
            var slide = cap.parentElement;
            if (!slide || !slide.classList.contains('rte-carousel-slide')) return;
            if (slide.querySelector('.rte-carousel-caption-btn')) {
                cap.parentNode.removeChild(cap);
                return;
            }
            var text = cap.textContent || '';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'rte-carousel-caption-btn';
            btn.title = 'Lihat Keterangan';
            btn.textContent = '?';
            btn.onclick = function (e) {
                e.stopPropagation();
                var p = btn.nextElementSibling;
                if (p) {
                    p.style.display = 'flex';
                    p.style.opacity = '1';
                }
            };

            var popup = document.createElement('div');
            popup.className = 'rte-carousel-caption-popup';
            popup.style.display = 'none';
            popup.style.opacity = '0';
            popup.style.transition = 'opacity 0.3s ease';

            var closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'rte-carousel-caption-close';
            closeBtn.innerHTML = '&times;';
            closeBtn.onclick = function (e) {
                e.stopPropagation();
                popup.style.display = 'none';
                popup.style.opacity = '0';
            };

            var content = document.createElement('div');
            content.style.fontSize = '16px';
            content.style.lineHeight = '1.6';
            content.style.maxWidth = '80%';
            content.style.margin = '0 auto';
            content.style.whiteSpace = 'pre-wrap';
            content.textContent = text;

            popup.appendChild(closeBtn);
            popup.appendChild(content);

            slide.replaceChild(popup, cap);
            slide.insertBefore(btn, popup);
        });

        // Upgrade code blocks to premium layout dynamically inside the editor
        var pres = this.content.querySelectorAll('pre');
        pres.forEach(function (pre) {
            if (pre.closest('.rte-code-block-container')) return;

            var lang = 'Plain Text';
            var classes = pre.className.split(/\s+/);
            classes.forEach(function (c) {
                if (c.indexOf('language-') === 0) {
                    lang = c.replace('language-', '').replace('-', ' ');
                }
            });

            var container = document.createElement('div');
            container.className = 'rte-code-block-container';
            container.setAttribute('contenteditable', 'false');

            var header = document.createElement('div');
            header.className = 'rte-code-block-header';
            header.setAttribute('contenteditable', 'false');

            var langLabel = document.createElement('span');
            langLabel.className = 'rte-code-block-lang';
            langLabel.textContent = lang;

            var copyBtn = document.createElement('button');
            copyBtn.type = 'button';
            copyBtn.className = 'rte-code-block-copy-btn';
            copyBtn.textContent = 'Copy';
            copyBtn.onclick = function (e) {
                e.stopPropagation();
                var codeEl = container.querySelector('code') || pre;
                if (codeEl) {
                    navigator.clipboard.writeText(codeEl.textContent).then(function () {
                        copyBtn.textContent = 'Copied!';
                        setTimeout(function () { copyBtn.textContent = 'Copy'; }, 2000);
                    });
                }
            };

            var deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'rte-code-block-delete-btn';
            deleteBtn.textContent = 'Delete';

            header.appendChild(langLabel);
            header.appendChild(copyBtn);
            header.appendChild(deleteBtn);
            container.appendChild(header);

            var newPre = pre.cloneNode(true);
            newPre.removeAttribute('style');
            newPre.setAttribute('contenteditable', 'true');

            var nested = newPre.querySelectorAll('*');
            nested.forEach(function (el) {
                el.style.backgroundColor = 'transparent';
                el.style.background = 'transparent';
            });

            container.appendChild(newPre);
            if (pre.parentNode) {
                pre.parentNode.replaceChild(container, pre);
            }
        });

        // Upgrade existing code block containers to have the delete button if they don't have it
        var containers = this.content.querySelectorAll('.rte-code-block-container');
        containers.forEach(function (container) {
            var header = container.querySelector('.rte-code-block-header');
            if (header && !header.querySelector('.rte-code-block-delete-btn')) {
                var deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.className = 'rte-code-block-delete-btn';
                deleteBtn.textContent = 'Delete';
                header.appendChild(deleteBtn);
            }
        });

        // Strip any leftover editor selection classes from tables when loaded
        var selectedCells = this.content.querySelectorAll('.rte-cell-selected');
        selectedCells.forEach(function (cell) {
            cell.classList.remove('rte-cell-selected');
        });

        // Re-trigger Prism syntax highlighting inside the editor
        setTimeout(function () { self._loadPrism(); }, 50);
    };

    global.RichTextEditor = RichTextEditor;
})(typeof window !== 'undefined' ? window : this);
