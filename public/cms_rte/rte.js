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
        gripIcon: '<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="5" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="15" cy="19" r="1.5"/></svg>',
        cellMerge: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/><line x1="7" y1="11" x2="17" y2="11"/></svg>',
        cellSplit: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="12" y1="3" x2="12" y2="21"/><line x1="3" y1="12" x2="21" y2="12"/></svg>',
        tableCellHighlight: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><rect x="9" y="9" width="6" height="6" fill="currentColor" stroke="none"/></svg>',
        tableRowHighlight: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><rect x="3" y="9" width="18" height="6" fill="currentColor" stroke="none"/></svg>',
        tableColHighlight: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><rect x="9" y="3" width="6" height="18" fill="currentColor" stroke="none"/></svg>',
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
        { label: 'Arial', value: 'Arial, sans-serif' },
        { label: 'Calibri', value: 'Calibri, sans-serif' },
        { label: 'Comic Sans MS', value: '"Comic Sans MS", cursive' },
        { label: 'Courier New', value: '"Courier New", monospace' },
        { label: 'Georgia', value: 'Georgia, serif' },
        { label: 'Helvetica', value: 'Helvetica, Arial, sans-serif' },
        { label: 'Impact', value: 'Impact, sans-serif' },
        { label: 'Lucida Console', value: '"Lucida Console", monospace' },
        { label: 'Tahoma', value: 'Tahoma, sans-serif' },
        { label: 'Times New Roman', value: '"Times New Roman", Times, serif' },
        { label: 'Trebuchet MS', value: '"Trebuchet MS", sans-serif' },
        { label: 'Verdana', value: 'Verdana, Geneva, sans-serif' },
    ];

    var FONT_SIZES = [
        { label: '8',  value: '1' },
        { label: '10', value: '2' },
        { label: '12', value: '3' },
        { label: '14', value: '4' },
        { label: '18', value: '5' },
        { label: '24', value: '6' },
        { label: '36', value: '7' },
    ];

    var BLOCK_FORMATS = [
        { label: 'Paragraph',  value: 'p' },
        { label: 'Heading 1',  value: 'h1' },
        { label: 'Heading 2',  value: 'h2' },
        { label: 'Heading 3',  value: 'h3' },
        { label: 'Heading 4',  value: 'h4' },
        { label: 'Heading 5',  value: 'h5' },
        { label: 'Heading 6',  value: 'h6' },
        { label: 'Quote',      value: 'blockquote' },
        { label: 'Code',       value: 'pre' },
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
        ],
        [
            { kind: 'btn', name: 'ul', icon: ICON.ul, title: 'Bullet list', cmd: 'insertUnorderedList' },
            { kind: 'btn', name: 'ol', icon: ICON.ol, title: 'Numbered list', cmd: 'insertOrderedList' },
            { kind: 'btn', name: 'outdent', icon: ICON.outdent, title: 'Decrease indent', cmd: 'outdent' },
            { kind: 'btn', name: 'indent', icon: ICON.indent, title: 'Increase indent', cmd: 'indent' },
            { kind: 'btn', name: 'quote', icon: ICON.quote, title: 'Quote', custom: 'blockquote' },
            { kind: 'btn', name: 'code', icon: ICON.code, title: 'Insert Code Block', custom: 'codeblock' },
        ],
        [
            { kind: 'btn', name: 'link', icon: ICON.link, title: 'Insert link', custom: 'link' },
            { kind: 'btn', name: 'unlink', icon: ICON.unlink, title: 'Remove link', cmd: 'unlink' },
            { kind: 'btn', name: 'image', icon: ICON.image, title: 'Insert image', custom: 'image' },
            { kind: 'btn', name: 'video', icon: ICON.video, title: 'Insert video', custom: 'video' },
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
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
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

        // --- Draggable modal ---
        var isDragging = false;
        var dragOffX = 0, dragOffY = 0;

        // Calculate initial centered position AFTER dialog is in DOM
        var initLeft = (backdrop.clientWidth - dialog.offsetWidth) / 2;
        var initTop = (backdrop.clientHeight - dialog.offsetHeight) / 2;
        dialog.style.position = 'absolute';
        dialog.style.left = initLeft + 'px';
        dialog.style.top = initTop + 'px';

        function onDragStart(e) {
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
            height: '320px',
        }, config || {});

        this.id = nextId();
        this._target = target;
        this._initialHTML = target.innerHTML || '';
        target.innerHTML = '';

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
    }

    RichTextEditor.prototype._build = function (target) {
        var self = this;
        var cfg = this.config;

        // Wrapper assumes the role of `target`'s container; we place wrapper after
        // target and hide target. This lets the original element remain in DOM
        // (handy for textarea form binding).
        var wrapper = el('div', { class: 'richtexteditor rte-modern', id: this.id });
        if (target.tagName === 'TEXTAREA') {
            target.style.display = 'none';
            target.parentNode.insertBefore(wrapper, target.nextSibling);
        } else {
            // Replace contents inside target (target is just a placeholder div).
            target.appendChild(wrapper);
        }

        var toolbar = el('div', { class: 'rte-toolbar', role: 'toolbar' });
        wrapper.appendChild(toolbar);

        var contentWrap = el('div', { class: 'rte-content-wrap' });
        var content = el('div', {
            class: 'rte-content ' + (cfg.editorBodyCssClass || 'rte-content-body'),
            contenteditable: cfg.readOnly ? 'false' : 'true',
            spellcheck: 'true',
            'data-placeholder': cfg.placeholder || '',
        });
        contentWrap.appendChild(content);
        wrapper.appendChild(contentWrap);

        var sourceArea = el('textarea', { class: 'rte-source', spellcheck: 'false' });
        sourceArea.style.display = 'none';
        sourceArea.style.minHeight = cfg.height || '320px';
        wrapper.appendChild(sourceArea);

        var statusbar = el('div', { class: 'rte-statusbar' }, [
            el('span', { class: 'rte-status-path', text: 'body' }),
            el('span', { class: 'rte-status-counts', text: '0 words • 0 chars' }),
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
        item.items.forEach(function (opt) {
            var optEl = el('div', {
                class: 'rte-dropdown-item',
                text: opt.label,
                style: item.action === 'fontName' && opt.value ? { fontFamily: opt.value } : null,
                onclick: function (e) {
                    e.preventDefault();
                    self._focusContent();
                    if (item.action === 'block') {
                        self.exec('formatBlock', opt.value);
                    } else if (item.action === 'fontName') {
                        if (opt.value) self.exec('fontName', opt.value);
                    } else if (item.action === 'fontSize') {
                        self.exec('fontSize', opt.value);
                    } else if (item.action === 'lineHeight') {
                        self._setLineHeight(opt.value);
                    }
                    if (!item.icon) label.textContent = opt.label;
                    closePanel();
                    self._syncSource();
                    self._updateState();
                },
            });
            panel.appendChild(optEl);
        });

        var open = false;
        function openPanel() {
            // Close other panels
            self._dropdowns.forEach(function (d) { d.close(); });
            var rect = btn.getBoundingClientRect();
            panel.style.position = 'fixed';
            panel.style.left = rect.left + 'px';
            panel.style.top = (rect.bottom + 2) + 'px';
            panel.style.minWidth = rect.width + 'px';
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
        this._dropdowns.push({ close: closePanel });
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
        var swatch = el('span', { class: 'rte-tb-color-swatch', style: { background: item.cmd === 'foreColor' ? '#000' : '#ffeb3b' } });
        btn.appendChild(swatch);
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
                    self.exec(item.cmd, c);
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
                self.exec(item.cmd, input.value);
                swatch.style.background = input.value;
                closePanel();
                self._syncSource();
            },
        });
        var clear = el('button', {
            type: 'button', class: 'rte-btn rte-btn-secondary rte-btn-sm', text: 'Remove',
            onclick: function (e) {
                e.preventDefault();
                self._focusContent();
                self.exec(item.cmd === 'foreColor' ? 'foreColor' : 'hiliteColor', 'inherit');
                closePanel();
                self._syncSource();
            },
        });
        customRow.appendChild(input);
        customRow.appendChild(apply);
        customRow.appendChild(clear);
        panel.appendChild(customRow);

        var open = false;
        function openPanel() {
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
        this._dropdowns.push({ close: closePanel });
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

    RichTextEditor.prototype._focusContent = function () {
        if (this._savedRange) {
            this.content.focus();
            try { restoreSelection(this._savedRange); } catch (e) {}
        } else {
            this.content.focus();
        }
    };

    RichTextEditor.prototype.exec = function (cmd, value) {
        try {
            // hiliteColor needs styleWithCSS = true on some browsers
            document.execCommand('styleWithCSS', false, true);
            document.execCommand(cmd, false, value);
        } catch (e) {
            console.warn('[RTE] exec failed', cmd, value, e);
        }
        this._snapshotSelection();
    };

    RichTextEditor.prototype._customAction = function (action) {
        switch (action) {
            case 'link': return this._dialogLink();
            case 'image': return this._dialogImage();
            case 'video': return this._dialogVideo();
            case 'table': return this._dialogTable();
            case 'hr': return this._insertHTML('<hr>');
            case 'blockquote': return this.exec('formatBlock', 'blockquote');
            case 'codeblock': return this._dialogCode();
            case 'source': return this._toggleSource();
            case 'fullscreen': return this._toggleFullscreen();
            case 'emoji': return this._dialogEmoji();
            case 'copyformat': return this._doCopyFormat();
            case 'pasteformat': return this._doPasteFormat();
            case 'find': return this._dialogFindReplace();
            case 'template': return this._dialogTemplate();
            case 'document': return this._dialogInsertDocument();
            case 'undo': this._historyUndo(); return;
            case 'redo': this._historyRedo(); return;
        }
    };

    // -------- dialogs ---------
    RichTextEditor.prototype._dialogLink = function () {
        var self = this;
        var sel = window.getSelection();
        var selectedText = sel && sel.toString() ? sel.toString() : '';
        var body = el('div', { class: 'rte-form' }, [
            el('label', { class: 'rte-form-label', text: 'URL' }),
            el('input', { type: 'url', class: 'rte-form-input', name: 'url', placeholder: 'https://example.com', value: 'https://' }),
            el('label', { class: 'rte-form-label', text: 'Text to display' }),
            el('input', { type: 'text', class: 'rte-form-input', name: 'text', value: selectedText }),
            el('label', { class: 'rte-form-row' }, [
                el('input', { type: 'checkbox', name: 'newtab', checked: true }),
                el('span', { text: ' Open in new tab' }),
            ]),
        ]);
        openModal({
            title: 'Insert link',
            body: body,
            confirmLabel: 'Insert',
            onConfirm: function () {
                var url = body.querySelector('[name=url]').value.trim();
                var text = body.querySelector('[name=text]').value;
                var newtab = body.querySelector('[name=newtab]').checked;
                if (!url) return false;
                var safeText = text || url;
                var html = '<a href="' + escapeHtml(url) + '"' +
                    (newtab ? ' target="_blank" rel="noopener noreferrer"' : '') +
                    '>' + escapeHtml(safeText) + '</a>';
                self._insertHTML(html);
            },
        });
    };

    RichTextEditor.prototype._dialogImage = function () {
        var self = this;
        var tabs = el('div', { class: 'rte-tabs' });
        var tabUpload = el('button', { type: 'button', class: 'rte-tab rte-tab-active', text: 'Upload' });
        var tabUrl = el('button', { type: 'button', class: 'rte-tab', text: 'URL' });
        tabs.appendChild(tabUpload);
        tabs.appendChild(tabUrl);

        var paneUpload = el('div', { class: 'rte-form rte-tab-pane rte-tab-pane-active' }, [
            el('label', { class: 'rte-form-label', text: 'Choose image' }),
            el('input', { type: 'file', class: 'rte-form-input', name: 'file', accept: 'image/*' }),
            el('label', { class: 'rte-form-label', text: 'Alt text' }),
            el('input', { type: 'text', class: 'rte-form-input', name: 'alt' }),
            el('label', { class: 'rte-form-label', text: 'Width (px, optional)' }),
            el('input', { type: 'number', class: 'rte-form-input', name: 'width', min: '0' }),
        ]);
        var paneUrl = el('div', { class: 'rte-form rte-tab-pane' }, [
            el('label', { class: 'rte-form-label', text: 'Image URL' }),
            el('input', { type: 'url', class: 'rte-form-input', name: 'url', placeholder: 'https://...' }),
            el('label', { class: 'rte-form-label', text: 'Alt text' }),
            el('input', { type: 'text', class: 'rte-form-input', name: 'alt' }),
            el('label', { class: 'rte-form-label', text: 'Width (px, optional)' }),
            el('input', { type: 'number', class: 'rte-form-input', name: 'width', min: '0' }),
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
                var widthAttr = function (input) {
                    var v = parseInt(input.value, 10);
                    return v > 0 ? ' width="' + v + '"' : '';
                };
                if (paneUpload.classList.contains('rte-tab-pane-active')) {
                    var fileInput = paneUpload.querySelector('[name=file]');
                    var altInput = paneUpload.querySelector('[name=alt]');
                    var widthInput = paneUpload.querySelector('[name=width]');
                    var f = fileInput.files && fileInput.files[0];
                    if (!f) return false;
                    self._uploadImage(f, function (url) {
                        // Use direct DOM insertion to avoid execCommand breaking editor with large base64
                        self._focusContent();
                        var savedRange = self._savedRange;
                        var img = document.createElement('img');
                        img.src = url;
                        if (altInput.value) img.alt = altInput.value;
                        var wv = parseInt(widthInput.value, 10);
                        if (wv > 0) img.width = wv;
                        if (savedRange) {
                            var sel = window.getSelection();
                            sel.removeAllRanges();
                            sel.addRange(savedRange);
                            var range = sel.getRangeAt(0);
                            range.deleteContents();
                            range.insertNode(img);
                            range.setStartAfter(img);
                            range.collapse(true);
                            sel.removeAllRanges();
                            sel.addRange(range);
                        } else {
                            self.content.appendChild(img);
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
                    var html = '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(altInput2.value || '') + '"' + widthAttr(widthInput2) + '>';
                    self._insertHTML(html);
                }
            },
        });
    };

    RichTextEditor.prototype._uploadImage = function (file, cb, errCb) {
        var self = this;
        self.showLoading();
        var handler = this.config.file_upload_handler;
        if (typeof handler === 'function') {
            handler(file, function (url) { self.hideLoading(); try { cb(url); } catch(e) { console.error("Error in upload callback:", e); } }, function (err) { self.hideLoading(); if (errCb) errCb(err); });
        } else {
            // Fallback: embed as base64 data URL.
            var reader = new FileReader();
            reader.onload = function () { self.hideLoading(); try { cb(reader.result); } catch(e) { console.error("Error in upload callback:", e); } };
            reader.onerror = function(err) { self.hideLoading(); if(errCb) errCb(err); };
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
            handler(file, function (url) { self.hideLoading(); try { cb(url); } catch(e) { console.error("Error in upload callback:", e); } }, function (err) { self.hideLoading(); if (errCb) errCb(err); });
        } else {
            self.hideLoading();
            alert("Video upload not configured.");
            if (errCb) errCb();
        }
    };

    RichTextEditor.prototype._buildVideoEmbed = function (url, w, h) {
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
        return '<iframe width="' + w + '" height="' + h + '" src="' + escapeHtml(url) + '" frameborder="0" allowfullscreen></iframe>';
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
        var emojis = ['😀','😁','😂','🤣','😊','😍','😘','😎','🤩','🤔','🙄','😴','😢','😭','😡','🤯','😱','🥳','🤝','👏','👍','👎','🙏','💪','❤️','💔','💯','🔥','⭐','✨','✅','❌','⚡','🎉','🎁','🚀','📌','📎','📝','📷','🎵','💡','⚙️','🌟','🌈','☀️','🌙','🌍'];
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
        if (!sel || sel.rangeCount === 0 || sel.isCollapsed) {
            Swal.fire({ title: 'Gagal', text: 'Pilih teks yang sudah diformat terlebih dahulu, lalu klik Salin Format.', icon: 'warning', confirmButtonText: 'OK' });
            return;
        }
        // Store the selected HTML range
        this._copiedFormat = sel.getRangeAt(0).cloneRange();
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
            } catch (e) {}
        }

        function scrollRangeIntoView(range) {
            try {
                var rects = range.getClientRects();
                if (rects.length) {
                    self.contentWrap.scrollTop += rects[0].top - self.contentWrap.getBoundingClientRect().top - 80;
                }
            } catch (e) {}
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
                    } catch (e) {}
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
        this._closeImagePopup();
        this._attachMediaResizeHandle(img);

        var toolbar = el('div', { class: 'rte-img-toolbar' });
        var activeMenu = null;
        function closeMenus() { if (activeMenu) { activeMenu.style.display = 'none'; activeMenu = null; } }

        function mkBtn(svgHtml, title, onclick) {
            var b = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            b.innerHTML = svgHtml;
            b.addEventListener('click', function(e) { e.stopPropagation(); closeMenus(); onclick(e); });
            return b;
        }

        function mkDrop(svgHtml, title, items, onOpen) {
            var wrap = el('div', { style: 'position:relative;display:inline-block;' });
            var btn = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            btn.innerHTML = svgHtml + '<svg viewBox="0 0 10 6" style="width:8px;height:8px;margin-left:1px"><polyline points="1,1 5,5 9,1" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
            var menu = el('div', { class: 'rte-img-tb-menu' });
            items.forEach(function(item) {
                if (item === '-') { menu.appendChild(el('div', { style: 'height:1px;background:#eee;margin:3px 0' })); return; }
                var mi = el('button', { type: 'button', class: 'rte-img-tb-menuitem', text: item.label });
                mi.addEventListener('click', function(e) { e.stopPropagation(); closeMenus(); menu.style.display = 'none'; item.action(); });
                menu.appendChild(mi);
            });
            btn.addEventListener('click', function(e) {
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
            { label: 'Set Size\u2026', action: function() {
                Swal.fire({ title: 'Set Image Size', html:
                    '<div style="display:flex;gap:8px;justify-content:center">' +
                    '<label style="font-size:13px">W: <input id="swal-img-w" type="number" value="' + (img.offsetWidth||img.width||'') + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                    '<label style="font-size:13px">H: <input id="swal-img-h" type="number" value="' + (img.offsetHeight||img.height||'') + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                    '</div>',
                    showCancelButton: true, confirmButtonText: 'Apply', cancelButtonText: 'Cancel'
                }).then(function(r) { if (r.isConfirmed) {
                    var w = parseInt(document.getElementById('swal-img-w').value, 10);
                    var h = parseInt(document.getElementById('swal-img-h').value, 10);
                    if (w > 0) img.style.width = w + 'px';
                    if (h > 0) img.style.height = h + 'px';
                    self._syncSource();
                    setTimeout(function(){self._updatePopupPositions();}, 10);
                }});
            }},
            '-',
            { label: 'Auto size', action: function() { img.style.width=''; img.style.height=''; img.removeAttribute('width'); img.removeAttribute('height'); self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: '100% width', action: function() { img.style.width='100%'; img.style.height='auto'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: '75% width', action: function() { img.style.width='75%'; img.style.height='auto'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: '50% width', action: function() { img.style.width='50%'; img.style.height='auto'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: '25% width', action: function() { img.style.width='25%'; img.style.height='auto'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
        ]));

        // 2. Caption
        var ICON_CAPTION = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="13" rx="2"/><line x1="3" y1="21" x2="21" y2="21"/><line x1="7" y1="18" x2="17" y2="18"/></svg>';
        toolbar.appendChild(mkBtn(ICON_CAPTION, 'Image Caption', function() {
            var fig = img.closest('figure');
            var cap = fig ? fig.querySelector('figcaption') : null;
            if (!fig) {
                var newFig = document.createElement('figure');
                newFig.style.cssText = 'display:table; margin:0 auto;';
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
                    if (fig.style.display && fig.style.display !== 'table') img.style.display = fig.style.display;
                    fig.parentNode.insertBefore(img, fig);
                    fig.parentNode.removeChild(fig);
                }
            }
            self._syncSource();
            setTimeout(function(){self._updatePopupPositions();}, 10);
        }));

        // 3. Insert link
        var ICON_LINK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10 14a5 5 0 0 0 7.07 0l3-3a5 5 0 0 0-7.07-7.07l-1.5 1.5"/><path d="M14 10a5 5 0 0 0-7.07 0l-3 3a5 5 0 0 0 7.07 7.07l1.5-1.5"/></svg>';
        toolbar.appendChild(mkBtn(ICON_LINK, 'Insert Link', function() {
            var current = img.parentNode && img.parentNode.tagName === 'A' ? img.parentNode.href : '';
            Swal.fire({ title: 'Link URL', input: 'url', inputValue: current,
                showCancelButton: true, confirmButtonText: 'Apply', inputPlaceholder: 'https://'
            }).then(function(r) { if (r.isConfirmed) {
                var href = r.value.trim();
                if (img.parentNode && img.parentNode.tagName === 'A') {
                    if (!href) { var a = img.parentNode; a.parentNode.insertBefore(img, a); a.parentNode.removeChild(a); }
                    else img.parentNode.href = href;
                } else if (href) {
                    var a = document.createElement('a'); a.href = href; a.target = '_blank';
                    img.parentNode.insertBefore(a, img); a.appendChild(img);
                }
                self._syncSource();
            }});
        }));

        // 4. Justify
        var ICON_JUSTIFY = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>';
        function setJustify(float, mL, mR) {
            var target = img.closest('figure') || img;
            target.style.float = float;
            target.style.marginLeft = mL;
            target.style.marginRight = mR;
            if (target.tagName === 'FIGURE') {
                target.style.display = 'table';
            } else {
                target.style.display = (float === 'none' && mL === 'auto') ? 'block' : 'inline-block';
            }
            self._syncSource();
            setTimeout(function(){self._updatePopupPositions();}, 10);
        }
        toolbar.appendChild(mkDrop(ICON_JUSTIFY, 'Justify', [
            { label: 'Justify Left',   action: function() { setJustify('none', '0', 'auto'); }},
            { label: 'Justify Center', action: function() { setJustify('none', 'auto', 'auto'); }},
            { label: 'Justify Right',  action: function() { setJustify('none', 'auto', '0'); }},
            '-',
            { label: 'Float Left',  action: function() { setJustify('left', '0', '10px'); }},
            { label: 'Float Right', action: function() { setJustify('right', '10px', '0'); }},
        ]));

        // 5. Style
        var ICON_STYLE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';
        var getTarget = function() { return img; };
        var styleItems = [
            { label: 'Border',          key: 'border',       get: function() { return getTarget().style.border ? 'active' : ''; },     action: function() { var t = getTarget(); if(t.style.border){ t.style.border=''; t.style.padding=''; t.style.borderRadius=''; }else{ t.style.border='1px solid #ccc'; t.style.padding='4px'; t.style.borderRadius='4px'; t.style.background='#fff'; } self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: 'Grayscale',       key: 'grayscale',    get: function() { return img.style.filter === 'grayscale(100%)' ? 'active' : ''; }, action: function() { img.style.filter = img.style.filter === 'grayscale(100%)' ? '' : 'grayscale(100%)'; self._syncSource(); }},
            { label: 'Shadow',          key: 'shadow',       get: function() { return getTarget().style.boxShadow ? 'active' : ''; },  action: function() { var t = getTarget(); t.style.boxShadow = t.style.boxShadow ? '' : '0 4px 8px rgba(0,0,0,0.1)'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: 'Margin 10px',     key: 'margin',       get: function() { return getTarget().style.margin ? 'active' : ''; },    action: function() { var t = getTarget(); t.style.margin = t.style.margin ? '' : '10px'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: 'Padding 10px',    key: 'padding',      get: function() { return getTarget().style.padding ? 'active' : ''; },   action: function() { var t = getTarget(); t.style.padding = t.style.padding ? '' : '10px'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: 'Rounded Corners', key: 'rounded',      get: function() { return getTarget().style.borderRadius === '8px' ? 'active' : ''; }, action: function() { var t = getTarget(); t.style.borderRadius = t.style.borderRadius === '8px' ? '' : '8px'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: 'Rounded Image',   key: 'circle',       get: function() { return img.style.borderRadius === '50%' ? 'active' : ''; }, action: function() { img.style.borderRadius = img.style.borderRadius === '50%' ? '' : '50%'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: 'Thumbnail',       key: 'thumb',        get: function() { return (getTarget().style.background === 'rgb(255, 255, 255)' || getTarget().style.background === '#fff') ? 'active' : ''; }, action: function() { var t=getTarget(); if(t.style.background === 'rgb(255, 255, 255)' || t.style.background === '#fff') { t.style.border=''; t.style.padding=''; t.style.background=''; t.style.borderRadius=''; } else { t.style.border='1px solid #ddd'; t.style.padding='4px'; t.style.background='#fff'; t.style.borderRadius='4px'; } self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
        ];
        toolbar.appendChild(mkDrop(ICON_STYLE, 'Image Style', styleItems, function(menu) {
            // Refresh active states when menu opens
            menu.querySelectorAll('.rte-img-tb-menuitem').forEach(function(mi, i) {
                mi.classList.toggle('rte-img-tb-menuitem-active', styleItems[i] && styleItems[i].get && !!styleItems[i].get());
            });
        }));

        // 6. Delete
        var ICON_DEL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>';
        toolbar.appendChild(mkBtn(ICON_DEL, 'Delete Image', function() {
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

        setTimeout(function() {
            document.addEventListener('mousedown', self._imagePopupCloseHandler = function(e) {
                if (!toolbar.contains(e.target) && e.target !== img &&
                    !e.target.classList.contains('rte-img-resize-handle') &&
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

        function positionOverlay() {
            if (!self._mediaResizeTarget || !self._mediaResizeHandle) return;
            var rect = self._mediaResizeTarget.getBoundingClientRect();
            self._mediaResizeHandle.style.left   = (rect.left - 2) + 'px';
            self._mediaResizeHandle.style.top    = (rect.top - 2) + 'px';
            self._mediaResizeHandle.style.width  = (rect.width + 4) + 'px';
            self._mediaResizeHandle.style.height = (rect.height + 4) + 'px';
            
            // Sync popups as well
            if (self._imagePopup || self._videoPopup) self._updatePopupPositions();
            
            self._mediaResizeRaf = requestAnimationFrame(positionOverlay);
        }
        positionOverlay();
        self._mediaResizePositioner = positionOverlay;

        var dirs = ['nw','n','ne','e','se','s','sw','w'];
        dirs.forEach(function(dir) {
            var h = document.createElement('div');
            h.className = 'rte-img-handle rte-img-handle-' + dir;
            // Slightly larger and more visible
            h.style.width = '12px';
            h.style.height = '12px';
            h.style.background = '#3b82f6';
            h.style.border = '2px solid #fff';
            h.style.boxShadow = '0 0 4px rgba(0,0,0,0.3)';
            
            h.addEventListener('mousedown', function(e) {
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
        this._closeVideoPopup();
        this._videoTarget = media;
        this._attachMediaResizeHandle(media);

        var toolbar = el('div', { class: 'rte-img-toolbar' });
        var activeMenu = null;
        function closeMenus() { if (activeMenu) { activeMenu.style.display = 'none'; activeMenu = null; } }

        function mkBtn(svgHtml, title, onclick) {
            var b = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            b.innerHTML = svgHtml;
            b.addEventListener('click', function(e) { e.stopPropagation(); closeMenus(); onclick(e); });
            return b;
        }
        function mkDrop(svgHtml, title, items, onOpen) {
            var wrap = el('div', { style: 'position:relative;display:inline-block;' });
            var btn = el('button', { type: 'button', class: 'rte-img-tb-btn', title: title });
            btn.innerHTML = svgHtml + '<svg viewBox="0 0 10 6" style="width:8px;height:8px;margin-left:1px"><polyline points="1,1 5,5 9,1" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
            var menu = el('div', { class: 'rte-img-tb-menu' });
            items.forEach(function(item) {
                if (item === '-') { menu.appendChild(el('div', { style: 'height:1px;background:#eee;margin:3px 0' })); return; }
                var mi = el('button', { type: 'button', class: 'rte-img-tb-menuitem', text: item.label });
                mi.addEventListener('click', function(e) { e.stopPropagation(); closeMenus(); menu.style.display = 'none'; item.action(); });
                menu.appendChild(mi);
            });
            btn.addEventListener('click', function(e) {
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
            { label: 'Set Size\u2026', action: function() {
                Swal.fire({ title: 'Set Video Size', html:
                    '<div style="display:flex;gap:8px;justify-content:center">' +
                    '<label style="font-size:13px">W: <input id="swal-vid-w" type="number" value="' + (media.offsetWidth||560) + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                    '<label style="font-size:13px">H: <input id="swal-vid-h" type="number" value="' + (media.offsetHeight||315) + '" style="width:80px;padding:4px;border:1px solid #ccc;border-radius:4px"></label>' +
                    '</div>',
                    showCancelButton: true, confirmButtonText: 'Apply'
                }).then(function(r) { if (r.isConfirmed) {
                    var w = parseInt(document.getElementById('swal-vid-w').value,10);
                    var h = parseInt(document.getElementById('swal-vid-h').value,10);
                    if (w>0) media.style.width = w+'px';
                    if (h>0) media.style.height = h+'px';
                    self._syncSource();
                    setTimeout(function(){self._updatePopupPositions();}, 10);
                }});
            }},
            '-',
            { label: 'Auto size',  action: function() { media.style.width=''; media.style.height=''; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: '100% width', action: function() { media.style.width='100%'; media.style.height='auto'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: '75% width',  action: function() { media.style.width='75%';  media.style.height='auto'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: '50% width',  action: function() { media.style.width='50%';  media.style.height='auto'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: '25% width',  action: function() { media.style.width='25%';  media.style.height='auto'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
        ]));

        // 2. Caption
        var ICON_CAPTION = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="13" rx="2"/><line x1="3" y1="21" x2="21" y2="21"/><line x1="7" y1="18" x2="17" y2="18"/></svg>';
        toolbar.appendChild(mkBtn(ICON_CAPTION, 'Video Caption', function() {
            var fig = media.closest('figure');
            var cap = fig ? fig.querySelector('figcaption') : null;
            if (!fig) {
                var newFig = document.createElement('figure');
                newFig.style.cssText = 'display:table; margin:0 auto;';
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
                    if (fig.style.marginRight) media.style.marginRight = fig.style.marginRight;
                    if (fig.style.display && fig.style.display !== 'table') media.style.display = fig.style.display;
                    fig.parentNode.insertBefore(media, fig);
                    fig.parentNode.removeChild(fig);
                }
            }
            self._syncSource();
            setTimeout(function(){self._updatePopupPositions();}, 10);
        }));

        // 3. Justify
        var ICON_JUSTIFY = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>';
        function setVJustify(float, mL, mR) {
            var wrap = media.closest('figure') || media;
            wrap.style.float = float;
            wrap.style.marginLeft = mL;
            wrap.style.marginRight = mR;
            if (wrap.tagName === 'FIGURE') {
                wrap.style.display = 'table';
            } else {
                wrap.style.display = (float === 'none' && mL === 'auto') ? 'block' : 'inline-block';
            }
            self._syncSource();
            setTimeout(function(){self._updatePopupPositions();}, 10);
        }
        toolbar.appendChild(mkDrop(ICON_JUSTIFY, 'Justify', [
            { label: 'Justify Left',   action: function() { setVJustify('none', '0', 'auto'); }},
            { label: 'Justify Center', action: function() { setVJustify('none', 'auto', 'auto'); }},
            { label: 'Justify Right',  action: function() { setVJustify('none', 'auto', '0'); }},
            '-',
            { label: 'Float Left',  action: function() { setVJustify('left', '0', '10px'); }},
            { label: 'Float Right', action: function() { setVJustify('right', '10px', '0'); }},
        ]));

        // 4. Style
        var ICON_STYLE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';
        var getTarget = function() { return media; };
        var vStyleItems = [
            { label: 'Border', action: function() { var t = getTarget(); if(t.style.border){ t.style.border=''; t.style.padding=''; t.style.borderRadius=''; }else{ t.style.border='1px solid #ccc'; t.style.padding='4px'; t.style.borderRadius='4px'; t.style.background='#fff'; } self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: 'Shadow', action: function() { var t = getTarget(); t.style.boxShadow = t.style.boxShadow ? '' : '0 4px 12px rgba(0,0,0,0.3)'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
            { label: 'Rounded', action: function() { var t = getTarget(); t.style.borderRadius = t.style.borderRadius ? '' : '8px'; self._syncSource(); setTimeout(function(){self._updatePopupPositions();}, 10); }},
        ];
        toolbar.appendChild(mkDrop(ICON_STYLE, 'Video Style', vStyleItems, function(menu) {
            menu.querySelectorAll('.rte-img-tb-menuitem').forEach(function(mi, i) {
                var item = vStyleItems[i];
                var t = getTarget();
                if (item && item.label === 'Border') mi.classList.toggle('rte-img-tb-menuitem-active', !!t.style.border);
                if (item && item.label === 'Shadow') mi.classList.toggle('rte-img-tb-menuitem-active', !!t.style.boxShadow);
                if (item && item.label === 'Rounded') mi.classList.toggle('rte-img-tb-menuitem-active', !!t.style.borderRadius);
            });
        }));

        // 5. Delete
        var ICON_DEL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>';
        toolbar.appendChild(mkBtn(ICON_DEL, 'Delete Video', function() {
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

        setTimeout(function() {
            document.addEventListener('mousedown', self._videoPopupCloseHandler = function(e) {
                if (!toolbar.contains(e.target) && e.target !== media &&
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

    // -------- Line Height --------
    RichTextEditor.prototype._setLineHeight = function (value) {
        var sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return;
        var node = sel.anchorNode;
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

    // -------- Paste Format --------
    RichTextEditor.prototype._doPasteFormat = function () {
        if (!this._formatCopied || !this._copiedFormat) {
            Swal.fire({ title: 'Gagal', text: 'Klik "Salin Format" pada teks yang sudah diformat terlebih dahulu.', icon: 'warning', confirmButtonText: 'OK' });
            return;
        }
        var self = this;
        var savedSel = window.getSelection();
        if (!savedSel || savedSel.rangeCount === 0) {
            Swal.fire({ title: 'Gagal', text: 'Pilih teks untuk menerapkan format.', icon: 'warning', confirmButtonText: 'OK' });
            return;
        }
        var targetRange = savedSel.getRangeAt(0);
        try {
            var container = this._copiedFormat.commonAncestorContainer.cloneNode(true);
            // Extract the formatting wrapper (bold, italic, span, etc.)
            var formatRoot = this._getFormatWrapper(this._copiedRange || this._copiedFormat);
            if (!formatRoot) {
                // Fallback: just wrap selected text in a <span> with inline styles from the copied node
                var frag = targetRange.extractContents();
                var wrapper = document.createElement('span');
                // Copy computed styles from the source node
                var srcNode = this._copiedFormat.commonAncestorContainer;
                if (srcNode.nodeType === Node.TEXT_NODE) srcNode = srcNode.parentElement;
                if (srcNode && srcNode !== this.content) {
                    var cs = window.getComputedStyle(srcNode);
                    var inlineStyle = [
                        'fontWeight', 'fontStyle', 'textDecoration', 'color',
                        'backgroundColor', 'fontFamily', 'fontSize',
                    ].map(function (prop) {
                        var v = cs.getPropertyValue(
                            prop === 'textDecoration' ? 'text-decoration' :
                            prop === 'fontFamily' ? 'font-family' :
                            prop === 'fontSize' ? 'font-size' :
                            prop === 'fontWeight' ? 'font-weight' :
                            prop === 'fontStyle' ? 'font-style' :
                            prop === 'backgroundColor' ? 'background-color' : prop
                        );
                        return v && v !== 'normal' && v !== 'none' && v !== 'inherit' ? prop.replace(/([A-Z])/g, '-$1').toLowerCase() + ':' + v : null;
                    }).filter(Boolean).join(';');
                    if (inlineStyle) wrapper.style.cssText = inlineStyle;
                }
                wrapper.appendChild(frag);
                targetRange.insertNode(wrapper);
            } else {
                var newWrapper = formatRoot.cloneNode(false);
                newWrapper.appendChild(targetRange.extractContents());
                targetRange.insertNode(newWrapper);
            }
            // Restore cursor after inserted element
            var range = document.createRange();
            range.setStartAfter(newWrapper || wrapper || targetRange.startContainer);
            range.collapse(true);
            savedSel.removeAllRanges();
            savedSel.addRange(range);
            this._syncSource();
            this._updateState();
        } catch (e) {
            console.warn('[RTE] Paste format error', e);
        }
    };

    RichTextEditor.prototype._getFormatWrapper = function (range) {
        // Walk from the start of the copied range up to find an inline formatting element
        var node = range.startContainer;
        if (node.nodeType === Node.TEXT_NODE) node = node.parentElement;
        while (node && node !== this.content) {
            var tag = node.tagName;
            if (tag && /^(B|I|U|S|STRONG|EM|SUP|SUB|SPAN|A)$/.test(tag)) return node;
            node = node.parentElement;
        }
        return null;
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
                self._insertHTML('<pre' + langClass + ' style="background:#1f2330;color:#e6e6e6;padding:12px 14px;border-radius:6px;overflow-x:auto;font-family:ui-monospace,SFMono-Regular,Consolas,"Liberation Mono",Menlo,monospace;font-size:0.92em;line-height:1.5;margin:0.5em 0;"><code>' + escaped + '</code></pre><p><br></p>');
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
            handler(file, function (url) { self.hideLoading(); try { cb(url); } catch(e) { console.error("Error in upload callback:", e); } }, function (err) { self.hideLoading(); if (errCb) errCb(err); });
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

        // Guard: if same table already selected AND we're not refreshing, skip
        // NOTE: _refreshTableSelection will null out _selectedTable before calling this
        if (this._selectedTable === table && this._tableOverlay) return;

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
                'pointer-events:none',  // let clicks pass through to content below
            ].join(';')
        });

        // ---- Move handle (top-left) ----
        var moveHandle = el('div', {
            class: 'rte-table-move-handle',
            title: 'Drag to move table'
        });
        moveHandle.innerHTML = ICON.gripIcon;
        overlay.appendChild(moveHandle);

        // ---- 8-point resize handles ----
        var handles = ['nw','n','ne','e','se','s','sw','w'];
        handles.forEach(function (h) {
            var handle = el('div', { class: 'rte-table-resize-handle rte-rsz-' + h });
            overlay.appendChild(handle);
        });

        // Append into content area so it scrolls with content
        this.content.parentElement.appendChild(overlay);
        this._tableOverlay = overlay;
        this._selectedTable = table;

        // ---- Move table by dragging the handle ----
        var moveStartX, moveStartY, tblOrigLeft, tblOrigTop;

        function onMoveStart(e) {
            e.preventDefault();
            e.stopPropagation();
            self._isResizingOrMoving = true;
            moveStartX = e.touches ? e.touches[0].clientX : e.clientX;
            moveStartY = e.touches ? e.touches[0].clientY : e.clientY;
            tblOrigLeft = parseFloat(table.style.marginLeft) || 0;
            tblOrigTop = parseFloat(table.style.marginTop) || 0;

            document.addEventListener('mousemove', onMoveMove);
            document.addEventListener('mouseup', onMoveEnd);
            document.addEventListener('touchmove', onMoveMove, { passive: false });
            document.addEventListener('touchend', onMoveEnd);
        }

        function onMoveMove(e) {
            e.preventDefault();
            var dx = (e.touches ? e.touches[0].clientX : e.clientX) - moveStartX;
            var dy = (e.touches ? e.touches[0].clientY : e.clientY) - moveStartY;
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
        }

        moveHandle.addEventListener('mousedown', onMoveStart);
        moveHandle.addEventListener('touchstart', onMoveStart, { passive: false });

        // ---- Resize table by dragging handles ----
        var resizeDir = null;
        var resizeStartX, resizeStartY, resizeStartW, resizeStartH;

        function onResizeStart(e, dir) {
            e.preventDefault();
            e.stopPropagation();
            self._isResizingOrMoving = true;
            resizeDir = dir;
            resizeStartX = e.touches ? e.touches[0].clientX : e.clientX;
            resizeStartY = e.touches ? e.touches[0].clientY : e.clientY;
            resizeStartW = table.offsetWidth;
            resizeStartH = table.offsetHeight;

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

    RichTextEditor.prototype._hideTableSelection = function () {
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
    };

    RichTextEditor.prototype._updatePopupPositions = function() {
        if (this._imagePopup && this._mediaResizeTarget) {
            var rect = this._mediaResizeTarget.getBoundingClientRect();
            var tbW = this._imagePopup.offsetWidth || 240;
            var left = Math.min(Math.max(rect.left + rect.width/2 - tbW/2, 4), window.innerWidth - tbW - 4);
            var top = rect.top - 44;
            if (top < 4) top = rect.bottom + 4;
            this._imagePopup.style.left = left + 'px';
            this._imagePopup.style.top = top + 'px';
        }
        if (this._videoPopup && this._mediaResizeTarget) {
            var rectV = this._mediaResizeTarget.getBoundingClientRect();
            var tbWV = this._videoPopup.offsetWidth || 180;
            var leftV = Math.min(Math.max(rectV.left + rectV.width/2 - tbWV/2, 4), window.innerWidth - tbWV - 4);
            var topV = rectV.top - 44;
            if (topV < 4) topV = rectV.bottom + 4;
            this._videoPopup.style.left = leftV + 'px';
            this._videoPopup.style.top = topV + 'px';
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
            btn.addEventListener('click', function(e) {
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
            
            items.forEach(function(item) {
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
                    mItem.addEventListener('click', function(e) {
                        e.stopPropagation();
                        input.click();
                    });
                    input.addEventListener('input', function() {
                        item.action(this.value);
                    });
                    input.addEventListener('change', function() {
                        closeMenus();
                    });
                } else {
                    mItem.addEventListener('click', function(e) {
                        e.stopPropagation();
                        closeMenus();
                        item.action();
                    });
                }
                menu.appendChild(mItem);
            });

            btn.addEventListener('click', function(e) {
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

        // 1. Table Header (Single Button)
        toolbar.appendChild(createBtn(ICON.tableHeader, 'Table Header', function() {
            self._toggleTableHeader(tbl);
        }));

        // 2. Table Cell
        toolbar.appendChild(createDropdown(ICON.tableCellHighlight, 'Table Cell', [
            { label: 'Merge Cells', icon: ICON.mergeCells, action: function() { self._mergeTableCells(tbl); } },
            { label: 'Split Cells Vertical', icon: ICON.splitCell, action: function() { self._splitTableCellVertical(tbl); } },
            { label: 'Split Cells Horizontal', icon: ICON.splitCell, action: function() { self._splitTableCellHorizontal(tbl); } },
            'sep',
            { kind: 'color', label: 'Cell Text Color', icon: ICON.textcolor, getValue: function() { var td = getSelTd(); return td ? td.style.color || '#000000' : '#000000'; }, action: function(val) { var selected = Array.from(tbl.querySelectorAll('.rte-cell-selected')); if (selected.length > 0) { selected.forEach(function(c) { c.style.color = val; }); } else { var td = getSelTd(); if(td) td.style.color = val; } self._syncSource(); } },
            { kind: 'color', label: 'Cell Back Color', icon: ICON.paint, getValue: function() { var td = getSelTd(); return td ? td.style.backgroundColor || '#ffffff' : '#ffffff'; }, action: function(val) { var selected = Array.from(tbl.querySelectorAll('.rte-cell-selected')); if (selected.length > 0) { selected.forEach(function(c) { c.style.backgroundColor = val; }); } else { var td = getSelTd(); if(td) td.style.backgroundColor = val; } self._syncSource(); } }
        ]));

        // 3. Table Row
        toolbar.appendChild(createDropdown(ICON.tableRowHighlight, 'Table Row', [
            { label: 'Insert Row Above', icon: ICON.insertRowBefore, action: function() { self._insertTableRow(tbl, 0); } },
            { label: 'Insert Row Below', icon: ICON.insertRowAfter, action: function() { self._insertTableRow(tbl, 1); } },
            'sep',
            { label: 'Delete Row', icon: ICON.deleteRow, action: function() { self._deleteTableRow(tbl); } }
        ]));

        // 4. Table Column
        toolbar.appendChild(createDropdown(ICON.tableColHighlight, 'Table Column', [
            { label: 'Insert Column Left', icon: ICON.insertColBefore, action: function() { self._insertTableCol(tbl, 0); } },
            { label: 'Insert Column Right', icon: ICON.insertColAfter, action: function() { self._insertTableCol(tbl, 1); } },
            'sep',
            { label: 'Delete Column', icon: ICON.deleteCol, action: function() { self._deleteTableCol(tbl); } }
        ]));

        // 5. Table
        toolbar.appendChild(createDropdown(ICON.table, 'Table', [
            { label: 'Auto size', icon: ICON.tableColWidth, action: function() { tbl.style.width = ''; self._updateTableOverlayPosition(); self._syncSource(); } },
            { label: '100% width', icon: ICON.tableColWidth, action: function() { tbl.style.width = '100%'; self._updateTableOverlayPosition(); self._syncSource(); } },
            { label: '75% width', icon: ICON.tableColWidth, action: function() { tbl.style.width = '75%'; self._updateTableOverlayPosition(); self._syncSource(); } },
            { label: '50% width', icon: ICON.tableColWidth, action: function() { tbl.style.width = '50%'; self._updateTableOverlayPosition(); self._syncSource(); } },
            'sep',
            { label: 'Delete Table', icon: ICON.tableDelete, action: function() { 
                Swal.fire({ title: 'Hapus Tabel?', text: 'Tindakan ini tidak dapat dibatalkan.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal' }).then(function (res) {
                    if (res.isConfirmed) { self._hideTableSelection(); tbl.parentNode.removeChild(tbl); self._syncSource(); }
                });
            } }
        ]));

        // ---- Position inside overlay to scroll with table ----
        toolbar.style.position = 'absolute';
        toolbar.style.left = '50%';
        toolbar.style.transform = 'translateX(-50%)';
        toolbar.style.zIndex = '9999';
        
        var tblRect = tbl.getBoundingClientRect();
        var spaceAbove = tblRect.top;
        if (spaceAbove < 60) {
            toolbar.style.top = '100%';
            toolbar.style.marginTop = '8px';
            toolbar.classList.add('rte-ft-bottom');
        } else {
            toolbar.style.top = '-46px';
            toolbar.classList.add('rte-ft-top');
        }

        if (self._tableOverlay) {
            self._tableOverlay.appendChild(toolbar);
        } else {
            document.body.appendChild(toolbar);
        }
        this._floatToolbar = toolbar;

        toolbar._closeDropdowns = closeMenus;
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

        for (var r = 0; r < table.rows.length; r++) {
            var nc = table.rows[r].insertCell(insertAt);
            nc.innerHTML = '\u00a0';
            var refStyle = table.rows[r].cells[insertAt + 1] || table.rows[r].cells[insertAt - 1];
            nc.style.cssText = refStyle ? refStyle.style.cssText : 'border:1px solid #d0d4da;padding:6px 8px;';
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
        for (var r = 0; r < table.rows.length; r++) {
            table.rows[r].deleteCell(cIdx);
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

        selected.forEach(function(cell) {
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
        selected.forEach(function(cell) {
            if (cell.parentElement.rowIndex === minRow && cell.cellIndex === minCol) {
                topCell = cell;
            }
        });
        if (!topCell) topCell = selected[0];

        selected.forEach(function(cell) {
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

    RichTextEditor.prototype._repositionFloatTableToolbar = function (table) {
        var toolbar = this._floatToolbar;
        if (!toolbar || !table) return;
        var tblRect = table.getBoundingClientRect();
        var spaceAbove = tblRect.top;
        
        toolbar.style.position = 'absolute';
        toolbar.style.left = '50%';
        toolbar.style.transform = 'translateX(-50%)';

        if (spaceAbove < 60) {
            toolbar.style.top = '100%';
            toolbar.style.marginTop = '8px';
            toolbar.classList.remove('rte-ft-top');
            toolbar.classList.add('rte-ft-bottom');
        } else {
            toolbar.style.top = '-46px';
            toolbar.style.marginTop = '0px';
            toolbar.classList.remove('rte-ft-bottom');
            toolbar.classList.add('rte-ft-top');
        }
    };

    // ===================================================================
    // TABLE COLUMN/RESIZE (per-cell border drag)
    // ===================================================================
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
            el('input', { type: 'color', value: td.style.backgroundColor || '#ffffff', class: 'rte-popup-color',
                onchange: function () {
                    td.style.backgroundColor = this.value;
                    self._syncSource();
                }
            }),
        ]);
        var ctrlTextColor = el('div', { class: 'rte-popup-row' }, [
            el('label', { text: 'Text color:', style: 'min-width:60px;' }),
            el('input', { type: 'color', value: td.style.color || '#222222', class: 'rte-popup-color',
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
        selected.forEach(function(td) { td.classList.remove('rte-cell-selected'); });
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
    };

    RichTextEditor.prototype._toggleSource = function () {
        var showingSource = this.sourceArea.style.display !== 'none';
        if (showingSource) {
            // Switch back to WYSIWYG
            this.content.innerHTML = this.sourceArea.value;
            this.sourceArea.style.display = 'none';
            this.content.style.display = '';
            this.wrapper.classList.remove('rte-source-mode');
        } else {
            // Switch to source
            this._syncSource();
            this.content.style.display = 'none';
            this.sourceArea.style.display = '';
            this.wrapper.classList.add('rte-source-mode');
        }
    };

    RichTextEditor.prototype.showLoading = function() {
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

    RichTextEditor.prototype.hideLoading = function() {
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

    RichTextEditor.prototype._snapshotSelection = function () {
        this._savedRange = saveSelection(this.content);
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

    RichTextEditor.prototype._syncSource = function () {
        // Mirror to underlying textarea if any
        if (this._target.tagName === 'TEXTAREA') {
            this._target.value = this.content.innerHTML;
        }
        // Push current content to undo history (step-by-step)
        this._historyPush(this.content.innerHTML);
    };

    RichTextEditor.prototype._updateState = function () {
        var self = this;
        // Toggle "active" class on toolbar buttons matching queryCommandState
        var map = {
            bold: 'bold', italic: 'italic', underline: 'underline', strike: 'strikeThrough',
            sub: 'subscript', sup: 'superscript',
            ul: 'insertUnorderedList', ol: 'insertOrderedList',
            alignleft: 'justifyLeft', aligncenter: 'justifyCenter',
            alignright: 'justifyRight', alignjustify: 'justifyFull',
        };
        Object.keys(map).forEach(function (key) {
            var btn = self._buttons[key];
            if (!btn) return;
            try {
                btn.classList.toggle('rte-active', document.queryCommandState(map[key]));
            } catch (e) {}
        });
        // Stats
        var text = this.content.textContent || '';
        var words = text.trim() ? text.trim().split(/\s+/).length : 0;
        var chars = text.length;
        var counts = this.statusbar.querySelector('.rte-status-counts');
        if (counts) counts.textContent = words + ' words • ' + chars + ' chars';
    };

    RichTextEditor.prototype._bind = function () {
        var self = this;
        var c = this.content;

        c.addEventListener('input', function () {
            self._snapshotSelection();
            self._syncSource();
            self._updateState();
            if (self.config.onchange) try { self.config.onchange(self.getHTMLCode()); } catch (e) {}
        });
        c.addEventListener('keyup', function () { self._snapshotSelection(); self._updateState(); });
        c.addEventListener('mouseup', function () { self._snapshotSelection(); self._updateState(); });

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
            if (target.tagName === 'IMG') {
                self._showImageEditorPopup(target);
            } else if (target.tagName === 'VIDEO' || target.tagName === 'IFRAME') {
                target.draggable = true; // ensure it can be dragged
                self._showVideoEditorPopup(target);
            } else if (target.tagName !== 'TD' && target.tagName !== 'TH') {
                self._closeImagePopup();
                self._closeVideoPopup();
                self._removeMediaResizeHandle();
            }
        });

        // Hide popups and handle realtime reflow when dragging media natively
        var draggingInternalMedia = false;
        var draggedNode = null;
        var dragEmptyImg = new Image();
        dragEmptyImg.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

        c.addEventListener('dragstart', function (e) {
            if (e.target.tagName === 'IMG' || e.target.tagName === 'VIDEO' || e.target.tagName === 'IFRAME') {
                draggingInternalMedia = true;
                draggedNode = e.target.closest('figure') || e.target;
                if (e.dataTransfer && e.dataTransfer.setDragImage) {
                    e.dataTransfer.setDragImage(dragEmptyImg, 0, 0);
                }
                self._closeImagePopup();
                self._closeVideoPopup();
                self._removeMediaResizeHandle();
            }
        });
        c.addEventListener('dragover', function (e) {
            if (draggingInternalMedia && draggedNode) {
                e.preventDefault(); // Allow drop
                var range = (document.caretRangeFromPoint || document.caretPositionFromPoint)
                    ? (document.caretRangeFromPoint
                        ? document.caretRangeFromPoint(e.clientX, e.clientY)
                        : null)
                    : null;
                if (range && c.contains(range.startContainer) && !draggedNode.contains(range.startContainer)) {
                    range.insertNode(draggedNode);
                }
            }
        });
        c.addEventListener('dragend', function (e) {
            draggingInternalMedia = false;
            draggedNode = null;
        });

        // Update overlay position on scroll
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
                    e.target.closest('.rte-table-float-toolbar')
                )) return;
                self._clearCellSelection(self._selectedTable);
                self._hideTableSelection();
                self._closeTablePopup();
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
                draggingInternalMedia = false;
                draggedNode = null;
                e.preventDefault(); // Node already moved in dragover
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

            // Tab inside lists -> indent
            if (e.key === 'Tab') {
                e.preventDefault();
                self.exec(e.shiftKey ? 'outdent' : 'indent');
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
        // Strip <script>, <style>, on* attributes, MS Office cruft
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var disallowed = ['script', 'style', 'meta', 'link', 'object', 'embed'];
        disallowed.forEach(function (tag) {
            var nodes = doc.querySelectorAll(tag);
            nodes.forEach(function (n) { n.parentNode.removeChild(n); });
        });
        var all = doc.body.querySelectorAll('*');
        all.forEach(function (n) {
            // Drop on* event handlers
            for (var i = n.attributes.length - 1; i >= 0; i--) {
                var attr = n.attributes[i];
                if (/^on/i.test(attr.name)) n.removeAttribute(attr.name);
                if (attr.name === 'class' && /\bMso/.test(attr.value)) n.removeAttribute('class');
            }
        });
        return doc.body.innerHTML;
    };

    // ------- Public API -------
    RichTextEditor.prototype.getHTMLCode = function () {
        if (this.sourceArea.style.display !== 'none') {
            // Return whatever is in source view
            return this.sourceArea.value;
        }
        return this.content.innerHTML;
    };
    RichTextEditor.prototype.getHTML = RichTextEditor.prototype.getHTMLCode;

    RichTextEditor.prototype.setHTMLCode = function (html) {
        var safe = (html == null) ? '' : html;
        if (this.sourceArea.style.display !== 'none') {
            this.sourceArea.value = safe;
        }
        this.content.innerHTML = safe;
        this._syncSource();
        this._updateState();
    };
    RichTextEditor.prototype.setHTML = RichTextEditor.prototype.setHTMLCode;

    RichTextEditor.prototype.insertHTML = function (html) { this._insertHTML(html); };

    RichTextEditor.prototype.insertImageByUrl = function (url, alt) {
        this._insertHTML('<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(alt || '') + '">');
    };

    RichTextEditor.prototype.focus = function () {
        this.content.focus();
        placeCursorAtEnd(this.content);
    };

    RichTextEditor.prototype.destroy = function () {
        if (this.wrapper && this.wrapper.parentNode) this.wrapper.parentNode.removeChild(this.wrapper);
        if (this._target && this._target.tagName === 'TEXTAREA') this._target.style.display = '';
    };

    // Static plugin registry (compat shim)
    RichTextEditor.plugins = RichTextEditor.plugins || {};
    RichTextEditor.registerPlugin = function (name, pluginFn) {
        RichTextEditor.plugins[name] = pluginFn;
    };

    global.RichTextEditor = RichTextEditor;
})(typeof window !== 'undefined' ? window : this);
