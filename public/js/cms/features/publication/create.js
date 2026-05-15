function publicationForm(initialType = 'pengumuman') {
    return {
        type: initialType,
        files: [],

        handleFiles(event) {
            const newFiles = Array.from(event.target.files);
            this.files = [...this.files, ...newFiles];
            this.renderPreviews();
        },

        renderPreviews() {
            const container = document.getElementById('file-previews');
            container.innerHTML = '';
            
            this.files.forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'relative group aspect-square rounded-lg overflow-hidden bg-gray-100 border border-gray-200';
                
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'w-full h-full object-cover';
                    div.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = URL.createObjectURL(file);
                    video.className = 'w-full h-full object-cover';
                    div.appendChild(video);
                }

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity';
                removeBtn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                removeBtn.onclick = () => {
                    this.files.splice(index, 1);
                    this.renderPreviews();
                };
                div.appendChild(removeBtn);
                
                container.appendChild(div);
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize RTE
    if (typeof RichTextEditor !== 'undefined') {
        const editor = new RichTextEditor("#div_editor1", {
            file_upload_handler: function(file, callback, optionalIndex, optionalFiles) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', window.rteUploadUrl ? document.querySelector('meta[name="csrf-token"]').content : '');

                fetch(window.rteUploadUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.url) {
                        callback(data.url);
                    }
                })
                .catch(error => console.error('Error uploading to RTE:', error));
            }
        });

        // Sync editor content to hidden input before submit
        const form = document.querySelector('form');
        form.onsubmit = function() {
            document.getElementById('description_input').value = editor.getHTMLCode();
        };
    }
});
