<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CMS — Feature Management (features/index)
    |--------------------------------------------------------------------------
    */

    'features' => [
        'title' => 'Feature Management',
        'card_title' => 'CMS Feature Management',
        'card_desc' => 'Manage all features displayed on the website',
        'add_button' => 'Add Feature',

        // Table headers
        'col_name' => 'Feature Name',
        'col_type' => 'Menu Type',
        'col_sub_count' => 'Sub Features',
        'col_order' => 'Order',
        'col_action' => 'Action',

        // Badges
        'type_dropdown' => 'Dropdown',
        'type_link' => 'Link',

        // Buttons
        'detail' => 'Detail',

        // Empty state
        'empty' => 'No features yet. Click "+ Add Feature" to create one.',

        // Edit modal
        'edit_title' => 'Edit Feature',

        // Add modal
        'add_title' => 'Add New Feature',

        // Delete modal
        'delete' => [
            'title' => 'Delete Feature',
            'confirm' => 'Are you sure you want to delete the feature :name? This action cannot be undone.',
            'yes' => 'Yes, Delete',
        ],

        // Form labels (shared between add/edit)
        'form' => [
            'name' => 'Feature Name',
            'type' => 'Menu Type',
            'path' => 'Path / URL',
            'path_placeholder' => 'Example: /home',
            'order' => 'Order',
            'name_placeholder' => 'Example: Home',
        ],

        // Detail page (features/show)
        'detail_title' => 'Feature Detail: :name',
        'type_label' => 'Type',

        // Sub-menu section (dropdown type)
        'sub' => [
            'list_title' => 'Sub Menu List — :name',
            'list_desc' => 'Manage sub menus within the :name menu',
            'add_button' => 'Add Sub Menu',
            'col_name' => 'Sub Menu Name',
            'col_path' => 'Path / URL',
            'col_order' => 'Order',
            'col_action' => 'Action',
            'empty' => 'No sub menus yet. Click "+ Add Sub Menu" to create one.',

            // Add sub modal
            'add_title' => 'Add Sub Menu',

            // Edit sub modal
            'edit_title' => 'Edit Sub Menu',

            // Delete sub modal
            'delete' => [
                'title' => 'Delete Sub Menu',
                'confirm' => 'Are you sure you want to delete the sub menu :name?',
                'yes' => 'Yes, Delete',
            ],

            // Sub form labels
            'form' => [
                'name' => 'Sub Menu Name',
                'path' => 'Path / URL',
                'path_placeholder' => 'Example: /profile/history',
                'name_placeholder' => 'Example: History',
                'order' => 'Order',
            ],
        ],

        // Content editor (link type)
        'content' => [
            'title' => 'Page Content Editor — :name',
            'desc' => 'Edit the content displayed on the :name page',
            'label' => 'Page Content',
            'placeholder' => 'Enter HTML or text content for this page...',
            'help' => 'You can use HTML to format the content.',
        ],

        // Flash messages
        'flash' => [
            'sub_added' => 'Sub menu added successfully.',
            'feature_added' => 'Feature added successfully.',
            'feature_updated' => 'Feature updated successfully.',
            'content_saved' => 'Page content saved successfully.',
            'feature_deleted' => 'Feature deleted successfully.',
            'sub_updated' => 'Sub feature updated successfully.',
            'sub_deleted' => 'Sub feature deleted successfully.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Feature Pages
    |--------------------------------------------------------------------------
    */

    'feature_pages' => [
        'title' => 'Page Management — :name',
        'desc' => 'Manage pages displayed for the :name feature',
        'add_button' => 'Add Page',
        'back_to_feature' => 'Back to Feature',

        'col_title' => 'Page Title',
        'col_sections' => 'Sections',
        'col_order' => 'Order',
        'col_action' => 'Action',

        'empty' => 'No pages yet. Click "+ Add Page" to create one.',

        'add_title' => 'Add New Page',
        'edit_title' => 'Edit Page',

        'delete' => [
            'title' => 'Delete Page',
            'confirm' => 'Are you sure you want to delete the page :name?',
            'yes' => 'Yes, Delete',
        ],

        'form' => [
            'title' => 'Page Title',
            'title_placeholder' => 'Example: Contemporary Exhibition',
            'description' => 'Page Description',
            'description_placeholder' => 'Brief description of this page...',
            'order' => 'Order',
        ],

        // Sections
        'sections_title' => 'Page Sections — :name',
        'sections_desc' => 'Manage content sections on the :name page',
        'add_section' => 'Add Section',
        'add_section_title' => 'Add New Section',
        'edit_section_title' => 'Edit Section',

        'section_form' => [
            'title' => 'Section Title',
            'title_placeholder' => 'Example: Mini Diorama Facility',
            'description' => 'Description',
            'description_placeholder' => 'Section description...',
            'images' => 'Images',
            'images_help' => 'Upload JPG/PNG/WebP images, max 2MB per file',
            'existing_images' => 'Current Images',
            'order' => 'Order',
        ],

        'delete_section' => [
            'title' => 'Delete Section',
            'confirm' => 'Are you sure you want to delete the section :name?',
            'yes' => 'Yes, Delete',
        ],

        'flash' => [
            'page_added' => 'Page added successfully.',
            'page_updated' => 'Page updated successfully.',
            'page_deleted' => 'Page deleted successfully.',
            'section_added' => 'Section added successfully.',
            'section_updated' => 'Section updated successfully.',
            'section_deleted' => 'Section deleted successfully.',
        ],

        // Public page
        'welcome' => 'Welcome to the :name portal,',
        'search_placeholder' => 'Search',
        'list_title' => ':name List',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Home Editor (home/edit)
    |--------------------------------------------------------------------------
    */

    'home' => [
        'title' => 'Home Page Content Editor',
        'desc' => 'Manage all content displayed on the Home page of the website',
        'view_page' => 'View Page',

        'hero' => [
            'title' => 'Hero Section (Main Banner)',
            'desc' => 'Main text and CTA button at the top of the page',
            'hero_title' => 'Hero Title',
            'hero_cta' => 'CTA Button Text',
        ],

        'feature_strip' => [
            'title' => 'Feature Strip (Below Hero Banner)',
            'desc' => 'Two information boxes below the hero',
            'left' => 'Left Text',
            'middle' => 'Middle Button',
            'middle_link' => 'Middle Button Link',
            'right_button' => 'Right Button',
            'right_button_link' => 'Right Button Link',
            'right_text' => 'Right Text',
            'related_links' => 'Related Links',
            'related_title' => 'Title',
            'related_photo' => 'Photo',
            'related_link' => 'Link',
            'add_related' => 'Add Link',
        ],

        'info' => [
            'title' => 'DABB Information Section',
            'desc' => 'Title and two paragraphs of information about DABB',
            'section' => 'Section Title',
            'paragraph1' => 'Paragraph 1',
            'paragraph2' => 'Paragraph 2',
        ],

        'activities' => [
            'title' => 'Archival Activities Section',
            'desc' => '6 activity items displayed in colored cards',
            'section' => 'Section Title',
        ],

        'section_titles' => [
            'title' => 'Other Section Titles',
            'desc' => 'Titles for Gallery, Statistics, YouTube, Instagram sections, etc.',
            'related' => 'Section Title',
            'gallery' => 'Archive Exhibition (Gallery)',
            'stats' => 'Section Title',
            'youtube' => 'Section Title',
            'instagram' => 'Section Title',
        ],

        'stats' => [
            'title' => 'Statistics Labels',
            'desc' => 'Text labels for visitor statistics counters',
            'total' => 'Total Visitors Label',
            'today' => 'Today\'s Visitors Label',
        ],

        'youtube' => [
            'title' => 'YouTube Videos',
            'desc' => 'YouTube video IDs displayed in the carousel (format: ID only, example: F2NhNTiNxoY)',
            'video_label' => 'Video :number',
            'placeholder' => 'YouTube ID',
            'help' => 'Copy the ID from the YouTube URL: youtube.com/watch?v=<strong>ID_HERE</strong>',
        ],

        'instagram' => [
            'title' => 'Instagram Feed',
            'desc' => 'Instagram post codes displayed on the home page',
            'username_label' => 'Instagram Username',
            'username_help' => 'Enter Instagram username without @',
            'post_label' => 'Post :number',
            'placeholder' => 'Instagram Post Code',
            'add_post' => 'Add Post',
            'help' => 'Copy the code from Instagram URL: instagram.com/p/<strong>CODE_HERE</strong>/',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Virtual Rooms 360° (virtual_rooms)
    |--------------------------------------------------------------------------
    */

    'virtual_rooms' => [
        'breadcrumb_parent' => 'Virtual Exhibition Real',
        'breadcrumb_active' => 'Dashboard',
        'breadcrumb_form_parent' => 'Virtual Exhibition Real / Room List',
        'breadcrumb_edit' => 'Edit Room',
        'breadcrumb_create' => 'Add Room',

        'page_title' => 'Page Management — :name',
        'page_desc' => 'Manage virtual rooms and navigation hotspots for :name 360 degrees',
        'view_exhibition' => 'View Virtual Exhibition',
        'add_room' => 'Add Virtual Room',

        'stat_total_rooms' => 'Total Rooms',
        'stat_total_rooms_sub' => 'Active virtual rooms',
        'stat_total_hotspots' => 'Total Hotspots',
        'stat_total_hotspots_sub' => 'Active navigation points',
        'stat_avg_hotspots' => 'Average Hotspots',
        'stat_avg_hotspots_sub' => 'Per room',

        'table_title' => 'Virtual Room List',
        'col_no' => 'No',
        'col_thumbnail' => 'Thumbnail',
        'col_name' => 'Room Name',
        'col_desc' => 'Description',
        'col_hotspot' => 'Hotspot',
        'col_action' => 'Action',
        'empty' => 'No virtual rooms have been added yet.',
        'delete_confirm' => 'Are you sure you want to delete this room?',

        // Form (create/edit)
        'form_title_create' => 'Add Virtual Room',
        'form_title_edit' => 'Edit Virtual Room',
        'form_desc' => 'Update room information and configure navigation hotspots',
        'back_to_list' => 'Back to Room List',
        'info_title' => 'Room Information',
        'label_name' => 'Room Name',
        'label_desc' => 'Description',
        'label_thumbnail' => 'Room Thumbnail',
        'thumbnail_help' => 'Preview image for room list (JPG, PNG, WEBP)',
        'label_image_360' => '360° Image',
        'image_360_help' => 'Equirectangular 360 degree image (JPG, PNG)',

        'hotspot_title' => 'Navigation Hotspots',
        'hotspot_add' => 'Add',
        'hotspot_rooms_available' => 'Available rooms: :count',
        'hotspot_empty' => "Empty. Click 'Add'",

        'preview_title' => '360° Panorama Preview',
        'preview_desc' => 'Click a target point on the panorama to get Yaw/Pitch, or drag to look around',
        'preview_placeholder' => 'Preview not available',
        'preview_placeholder_sub' => 'Select a 360° image first',

        'btn_cancel' => 'Cancel',
        'btn_save' => 'Save Changes',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Virtual 3D Rooms (virtual_3d_rooms)
    |--------------------------------------------------------------------------
    */

    'virtual_3d_rooms' => [
        'breadcrumb_parent' => 'Virtual 3D Rooms',
        'breadcrumb_edit' => 'Edit: :name',
        'breadcrumb_create' => 'Add Room',

        'page_title' => 'Virtual 3D Rooms — :name',
        'page_desc' => 'Manage virtual rooms with 4 walls and interactive doors',
        'view_exhibition' => 'View Virtual Exhibition',
        'add_room' => 'Add 3D Room',

        'stat_total_rooms' => 'Total Rooms',
        'stat_total_rooms_sub' => 'Active virtual 3D rooms',
        'stat_total_media' => 'Total Media',
        'stat_total_media_sub' => 'Images &amp; videos on walls',
        'stat_avg_media' => 'Average Media',
        'stat_avg_media_sub' => 'Per room',

        'table_title' => 'Virtual 3D Room List',
        'col_no' => 'No',
        'col_thumbnail' => 'Thumbnail',
        'col_name' => 'Room Name',
        'col_desc' => 'Description',
        'col_media' => 'Media',
        'col_action' => 'Action',
        'empty' => 'No virtual 3D rooms have been added yet.',
        'delete_confirm' => 'Are you sure you want to delete this room? All wall media will also be deleted.',

        // Create form
        'form_title_create' => 'Add Virtual 3D Room',
        'form_desc_create' => 'Set up room information, wall/floor/ceiling colors, and navigation hotspots',
        'back_to_list' => 'Back to Room List',

        // Edit form
        'form_title_edit' => 'Edit Room: :name',
        'form_desc_edit' => 'Set up room information, colors, wall media, and navigation hotspots',

        // Shared form
        'info_title' => 'Room Information',
        'label_name' => 'Room Name',
        'label_desc' => 'Description',
        'label_thumbnail' => 'Room Thumbnail',
        'thumbnail_help' => 'Preview image for room list (JPG, PNG, WEBP)',
        'thumbnail_keep' => 'Leave empty if you don\'t want to change it',

        'colors_title' => 'Room Colors',
        'label_wall_color' => 'Wall Color',
        'label_floor_color' => 'Floor Color',
        'label_ceiling_color' => 'Ceiling Color',

        'door_title' => 'Door / Hotspot Settings',
        'door_desc' => 'The door is on the back wall of the 3D room and can direct visitors to another page or room.',
        'door_desc_edit' => 'Back wall door for navigation to other pages/rooms',
        'label_door_type' => 'Door Link Type',
        'door_type_none' => 'Inactive (Visual Only)',
        'door_type_room' => 'Navigate to Another Room',
        'door_type_url' => 'Free Link (URL)',
        'label_target_room' => 'Target Room',
        'target_room_placeholder' => '— Select Room —',
        'rooms_available' => 'Available rooms: :count',
        'label_target_url' => 'Target URL',
        'label_door_label' => 'Door Label (Optional)',
        'door_label_placeholder' => 'Example: EXIT',

        'media_title' => 'Wall Media (Photo / Video)',
        'media_save_first' => 'Save the room first',
        'media_save_first_sub' => 'After saving, you will be redirected to the edit page to add photos/videos to the room walls.',
        'media_items' => ':count items',
        'media_selected_wall' => 'Selected Wall',
        'media_wall_front' => 'Front Wall',
        'media_wall_hint' => 'Select a wall in the <strong>Media Position Editor</strong> panel on the right',
        'media_type_label' => 'Media Type',
        'media_type_image' => 'Image (JPG/PNG)',
        'media_type_video' => 'Video (MP4)',
        'media_file_label' => 'File Upload',
        'media_upload_btn' => 'Upload &amp; Add to Wall',
        'media_wall_label' => 'Wall: :wall',
        'media_delete' => 'Delete',
        'media_empty' => 'No media yet. Upload a file above.',
        'media_upload_success' => 'Media uploaded successfully!',
        'media_upload_choose' => 'Select a file to upload!',

        'preview_title' => '3D Room Preview',
        'preview_desc' => 'Live 3D room preview based on your color settings',
        'preview_desc_edit' => 'Live room preview based on your color settings',
        'preview_front' => 'FRONT',
        'preview_back' => 'BACK',
        'preview_left' => 'LEFT',
        'preview_right' => 'RIGHT',
        'preview_floor' => 'FLOOR',
        'preview_ceiling' => 'CEILING',
        'preview_door' => 'DOOR',
        'preview_btn_default' => 'Default',
        'preview_btn_front' => 'Front',
        'preview_btn_left' => 'Left',
        'preview_btn_right' => 'Right',
        'preview_btn_back' => 'Back',
        'preview_btn_top' => 'Top',

        'editor_title' => 'Wall Media Position Editor',
        'editor_desc' => 'Drag media to adjust position on the wall. Click media to show properties.',
        'editor_wall_front' => 'Front Wall',
        'editor_wall_left' => 'Left Wall',
        'editor_wall_right' => 'Right Wall',
        'editor_wall_back' => 'Back Wall',
        'editor_wall_title_front' => 'FRONT WALL',
        'editor_props_title' => 'Selected Media Properties',
        'editor_props_delete' => 'Delete',
        'editor_props_save' => 'Save Position',

        'btn_cancel' => 'Cancel',
        'btn_save_create' => 'Save Room',
        'btn_save_edit' => 'Save Changes',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Virtual Books
    |--------------------------------------------------------------------------
    */

    'virtual_books' => [
        'breadcrumb_parent' => 'CMS',
        'breadcrumb_list' => 'Book List',
        'breadcrumb_create' => 'Add Book',
        'breadcrumb_edit' => 'Edit Book',

        'page_title' => 'Book List: :name',
        'page_desc' => 'Manage books in this feature',
        'add_button' => 'Add Book',
        'table_title' => 'Book List',

        'col_cover' => 'Cover',
        'col_title' => 'Book Title',
        'col_pages' => 'Pages',
        'col_order' => 'Order',
        'col_action' => 'Action',

        'no_cover' => 'No Cover',
        'page_count' => ':count pages',
        'detail_title' => 'Detail - Manage Pages',
        'edit_cover' => 'Edit Book Cover',
        'empty' => 'No books yet. Click "Add Book" to create the first one.',

        'delete' => [
            'title' => 'Delete Book',
            'confirm' => 'Are you sure you want to delete the book',
            'confirm_warn' => '? All pages will also be deleted.',
            'yes' => 'Yes, Delete',
        ],

        // Create form
        'create_title' => 'Add New Book',
        'create_desc' => 'Create a new book in the :name feature',
        'back_to_list' => 'Back to Book List',

        // Edit form
        'edit_title' => 'Edit Book: :name',
        'edit_desc' => 'Update book cover settings',
        'book_settings' => 'Book Settings',

        // Form fields
        'form' => [
            'title' => 'Book Title',
            'title_placeholder' => 'Enter book title',
            'cover' => 'Book Cover',
            'cover_help' => 'JPG, PNG, or WebP.',
            'cover_help_optional' => 'JPG, PNG, or WebP. Optional.',
            'remove_cover' => 'Remove cover',
            'remove_back_cover' => 'Remove back cover',
            'additional_text' => 'Additional Text (Optional)',
            'additional_text_help' => 'Add text such as subtitle or cover description',
            'additional_text_placeholder' => 'Additional text :number',
            'add_text' => 'Add Text',
            'back_cover' => 'Back Cover',
            'back_title' => 'Book Title (Back)',
            'back_title_placeholder' => 'Title for back cover (optional)',
            'back_cover_label' => 'Book Cover (Back)',
            'back_text' => 'Additional Text (Back)',
            'back_text_help' => 'Add text for back cover',
            'thumbnail' => 'List Thumbnail',
            'thumbnail_will_save' => 'Thumbnail to be saved:',
            'thumbnail_new_will_save' => 'New thumbnail to be saved:',
            'remove_thumbnail' => 'Remove thumbnail',
            'remove' => 'Remove',
            'cancel_remove' => 'Cancel',
            'generate_thumbnail' => 'Generate from Preview',
            'generate_help' => 'Or upload manually. Generate will create a thumbnail from the book preview.',
            'order' => 'Order',
            'order_help' => 'Display order of the book in the feature',
        ],

        // Preview
        'preview_title' => 'Book Cover Preview',
        'preview_placeholder' => 'Upload cover for preview',
        'preview_default_title' => 'Book Title',
        'preview_back_title' => 'Back Cover Preview',
        'preview_back_placeholder' => 'Upload back cover',
        'zoom_out' => 'Zoom Out',
        'zoom_in' => 'Zoom In',
        'reset_position' => 'Reset Position',
        'drag_hint' => 'Drag elements to adjust position | Scroll on image to resize',

        // Buttons
        'btn_cancel' => 'Cancel',
        'btn_save' => 'Save Book',
        'btn_save_changes' => 'Save Changes',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Virtual Book Pages
    |--------------------------------------------------------------------------
    */

    'virtual_book_pages' => [
        'breadcrumb_parent' => 'Virtual Books',
        'breadcrumb_list' => 'Book Pages',
        'breadcrumb_create' => 'Add Page',
        'breadcrumb_edit' => 'Edit Page',

        'page_title' => 'Pages: :name',
        'page_desc' => 'Manage pages in this book',
        'edit_cover' => 'Edit Cover',
        'add_button' => 'Add Page',
        'no_cover' => 'No Cover',
        'page_count' => ':count pages',
        'table_title' => 'Book Page List',

        'col_thumbnail' => 'Thumbnail',
        'col_title' => 'Title',
        'col_type' => 'Type',
        'col_order' => 'Order',
        'col_action' => 'Action',

        'no_thumb' => 'No Thumb',
        'type_cover' => 'Front Cover',
        'type_back_cover' => 'Back Cover',
        'type_content' => 'Content Page',
        'empty' => 'No pages yet. Click "Add Page" to start.',

        'delete' => [
            'title' => 'Delete Page',
            'confirm' => 'Are you sure you want to delete the page',
            'yes' => 'Yes, Delete',
        ],

        // Create form
        'create_title' => 'Add Book Page',
        'create_desc' => 'Add a new page for the virtual book',
        'back_to_list' => 'Back to List',

        // Edit form
        'edit_title' => 'Edit Page: :name',
        'edit_desc' => 'Update virtual book page information',

        // Form fields
        'form' => [
            'images_title' => 'Page Images',
            'upload_images' => 'Upload Images (Multiple)',
            'upload_images_help' => 'JPG, PNG, or WebP. Max 2MB per image. You can upload multiple images at once.',
            'current_images' => 'Current Images',
            'existing_label' => 'Exists',
            'remove_all_images' => 'Remove all images',
            'upload_new_images' => 'Upload New Images',
            'upload_new_images_help' => 'JPG, PNG, or WebP. Max 2MB per image.',
            'page_info' => 'Page Information',
            'title' => 'Page Title',
            'title_placeholder' => 'Enter page title',
            'content' => 'Text Content',
            'content_placeholder' => 'Enter page text content',
            'image_size' => 'Image Size (%)',
            'image_size_help' => 'Set image height in the page',
            'image_fit_mode' => 'Image Display Mode',
            'image_fit_contained' => 'Within Content Bounds',
            'image_fit_fullbleed' => 'Full Bleed',
            'image_fit_mode_help' => 'Choose "Within Content Bounds" to keep the image inside title & footer lines. Choose "Full Bleed" to cover the entire page.',
            'order' => 'Order',
            'order_help' => 'Page display order in the book',
            'thumbnail_title' => 'Page Thumbnail',
            'current_thumbnail' => 'Current Thumbnail',
            'remove_thumbnail' => 'Remove thumbnail',
            'upload_thumbnail' => 'Upload Thumbnail',
            'upload_new_thumbnail' => 'Upload New Thumbnail',
            'thumbnail_will_save' => 'Thumbnail to be saved:',
            'thumbnail_new_will_save' => 'New thumbnail to be saved:',
            'remove' => 'Remove',
            'cancel_remove' => 'Cancel',
            'generate_thumbnail' => 'Generate from Preview',
            'generate_help' => 'Or upload manually. Generate will create a thumbnail from the page preview.',
        ],

        // Preview
        'preview_title' => 'Page Preview',
        'preview_hint' => 'Drag elements in preview with cursor',
        'default_title' => 'Page Title',
        'new_label' => 'New :number',

        // Buttons
        'btn_cancel' => 'Cancel',
        'btn_save' => 'Save Page',
        'btn_save_changes' => 'Save Changes',

        // JS messages
        'js' => [
            'generating' => 'Generating...',
            'generate_failed' => 'Failed to generate thumbnail: ',
            'generate_btn' => 'Generate from Preview',
            'preview_not_found' => 'Book preview not found',
            'upload_cover_first' => 'Please upload a book cover first',
        ],
    ],

    // Page type options (shared: show.blade.php sub menu modals)
    'page_types' => [
        'label' => 'Page Type',
        'none' => 'None',
        'beranda' => 'Homepage',
        'onsite' => 'Onsite Archive Exhibition',
        'real' => 'Virtual Archive Exhibition Real (360°)',
        '3d' => 'Virtual Archive Exhibition 3D',
        'book' => 'Virtual Archive Exhibition Book',
        'slideshow' => 'Virtual Archive Exhibition SlideShow',
        'profile' => 'Profile',
    ],

    /*
    |--------------------------------------------------------------------------
    | Common (shared across CMS pages)
    |--------------------------------------------------------------------------
    */

    'common' => [
        'cancel' => 'Cancel',
        'save_changes' => 'Save Changes',
        'save_content' => 'Save Content',
        'back' => 'Back',
        'required' => '*',
        'saved_successfully' => 'Settings saved successfully.',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Virtual Slideshow
    |--------------------------------------------------------------------------
    */

    'virtual_slideshow' => [
        'title' => 'Virtual Archive Slideshow',

        // Table columns
        'col_order' => 'Order',
        'col_thumbnail' => 'Thumbnail',
        'col_title' => 'Title',
        'col_type' => 'Type',
        'col_slides' => 'Slides Count',
        'col_content' => 'Content',
        'col_action' => 'Action',

        // Index page
        'pages_list_title' => 'Page List / Exhibition',
        'pages_list_desc' => 'Manage virtual archive exhibition pages and slide content.',
        'add_page' => 'Add Page',
        'empty_pages' => 'No pages yet. Create a page first in the "Manage Pages" menu.',
        'slides_count' => ':count slides',
        'manage_slides' => 'Manage Slides',
        'edit_page' => 'Edit Page',
        'view_public' => 'View Public Page',

        // Delete modals
        'delete_page_title' => 'Delete Page',
        'delete_page_confirm' => 'Are you sure you want to delete page',
        'delete_slide_title' => 'Delete Slide',
        'delete_slide_confirm' => 'Are you sure you want to delete slide',
        'delete_video_upload_title' => 'Delete Video Upload',
        'delete_video_upload_confirm' => 'Are you sure you want to delete this uploaded video?',
        'delete_video_url_title' => 'Delete Video URL',
        'delete_video_url_confirm' => 'Are you sure you want to delete this video URL?',

        // Create/Edit page form
        'create_page_title' => 'Add Exhibition Page',
        'edit_page_title' => 'Edit Exhibition Page',
        'page_info' => 'Page Information',
        'page_title_label' => 'Page Title',
        'page_title_placeholder' => 'Exhibition page title...',
        'page_desc_label' => 'Description',
        'page_desc_placeholder' => 'Short description...',
        'page_order_label' => 'Order',
        'page_order_help' => 'Display order on the public page',
        'page_thumbnail_label' => 'Thumbnail',
        'upload_image_hint' => 'Click to upload image',
        'thumbnail_optional' => 'Optional. If empty, thumbnail will be taken from first slide.',
        'thumbnail_edit_help' => 'Optional. If empty, thumbnail stays as before.',
        'current_thumbnail' => 'Current thumbnail',
        'save_page' => 'Save Page',
        'update_page' => 'Update Page',

        // Slides index
        'manage_slides_title' => 'Manage Slides: :title',
        'slides_list_title' => 'Slide List',
        'slides_list_desc' => 'Arrange slide order and manage interactive content.',
        'add_slide' => 'Add Slide',
        'add_first_slide' => 'Add First Slide',
        'empty_slides' => 'No slides yet. Click "Add Slide" to start.',
        'untitled' => '(untitled)',
        'images_count' => ':count images',
        'has_video' => 'Video',
        'info_popup_count' => ':count info popup',
        'view_exhibition' => 'View Public Page (Exhibition #:order)',

        // Slide types
        'type_hero' => 'Hero',
        'type_text' => 'Text',
        'type_carousel' => 'Carousel',
        'type_video' => 'Video',
        'type_text_carousel' => 'Text + Carousel',
        'type_hero_desc' => 'Opening banner',
        'type_text_desc' => 'Text content only',
        'type_carousel_desc' => 'Image slideshow',
        'type_video_desc' => 'Embed video',
        'type_text_carousel_desc' => 'Split layout',

        // Create/Edit slide form
        'create_slide_title' => 'Add New Slide',
        'edit_slide_title' => 'Edit Slide',
        'page_label' => 'Page: :title',
        'errors_found' => 'There are errors:',
        'step1_type' => '1. Select Slide Type',
        'step2_content' => '2. Content',
        'step3_media' => '3. Media',
        'step4_video' => '4. Video',
        'slide_title_label' => 'Title',
        'optional' => 'optional',
        'slide_subtitle_label' => 'Sub-title',
        'slide_desc_label' => 'Description / Content Text',
        'desc_toolbar_hint' => 'optional - use toolbar for formatting',
        'layout_label' => 'Layout',
        'layout_left' => 'Text Left, Image Right',
        'layout_center' => 'Center',
        'layout_right' => 'Image Left, Text Right',
        'bg_color_label' => 'Background Color',
        'order_label' => 'Order',
        'media_type_images' => 'Images',
        'media_type_videos' => 'Videos',
        'method_upload' => 'Upload File',
        'method_url' => 'URL',
        'image_upload_hint' => 'Click to select images (multiple allowed)',
        'image_url_placeholder' => 'https://example.com/image.jpg or Google Drive link',
        'add_image_url' => 'Add Image URL',
        'open_link' => 'Open link',
        'popup_caption_images' => 'Info Popup Caption per Image',
        'popup_caption_hint' => 'clicking the ? button will show this text',
        'upload_images_first' => 'Upload or enter image URL first to fill popup caption.',
        'hero_single_image' => 'Hero can only have 1 image.',
        'hero_image_upload_hint' => 'Click to select image (only 1)',
        'hero_exists_title' => 'Cannot Select Hero',
        'hero_exists_error' => 'This page already has a Hero slide. Only 1 Hero allowed per page.',
        'hero_url_restriction' => 'Hero can only have 1 image. Remove uploaded image first.',
        'hero_upload_restriction' => 'Hero can only have 1 image. Remove URL image first.',
        'hero_limit_warning' => 'Only 1 image allowed for Hero. Remove existing image first.',
        'carousel_video_url_placeholder' => 'https://youtube.com/watch?v=... or Google Drive link',
        'add_video_url' => 'Add Video URL',
        'carousel_video_upload_hint' => 'Click to select video (multiple, .mp4, .webm)',
        'popup_caption_videos' => 'Info Popup Caption per Video',
        'add_videos_first' => 'Add video first to fill popup caption.',
        'single_video_url_placeholder' => 'https://youtube.com/watch?v=..., Google Drive, or other video URL',
        'preview' => 'Preview',
        'popup_video_url' => 'Info Popup Caption Video (URL)',
        'video_upload_hint' => 'Click to select video (.mp4, .webm)',
        'popup_video_upload' => 'Info Popup Caption Video (Upload)',
        'save_slide' => 'Save Slide',
        'update_slide' => 'Update Slide',
        'caption_single' => 'Single Caption',
        'caption_multi_qa' => 'Multi Q&A',
        'question' => 'Question',
        'answer' => 'Answer',
        'add_qa' => '+ Add Q&A',
        'existing_images' => 'Existing uploaded images',
        'existing_video_url' => 'Existing video URL',
        'existing_video_upload' => 'Existing uploaded video',
        'add_new_images' => 'Add new images (upload)',
        'popup_existing_images' => 'Info Popup Caption (uploaded images)',
        'popup_url_images' => 'Info Popup Caption (URL images)',
        'image_number' => 'Image :number',
        'view' => 'View',
        'open' => 'Open',

        // Common
        'cancel' => 'Cancel',
        'delete' => 'Delete',

        // Flash messages
        'flash' => [
            'page_created' => 'Exhibition page created successfully.',
            'page_updated' => 'Exhibition page updated successfully.',
            'page_deleted' => 'Exhibition page deleted successfully.',
            'slide_created' => 'Slide created successfully.',
            'slide_updated' => 'Slide updated successfully.',
            'slide_deleted' => 'Slide deleted successfully.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Profile Page
    |--------------------------------------------------------------------------
    */

    'profile' => [
        'breadcrumb_parent' => 'CMS',
        'breadcrumb_active' => 'Profile',

        'page_title' => 'Profile Page — :name',
        'page_desc' => 'Manage profile pages for the :name feature',
        'view_page' => 'View Public Page',

        // Profile page types
        'type_default' => 'Default',
        'type_sdm_chart' => 'SDM (Chart)',
        'type_struktur_image' => 'Struktur Organisasi',
        'type_tugas_fungsi' => 'Tugas dan Fungsi',

        // Pages list
        'col_title' => 'Page Title',
        'col_type' => 'Page Type',
        'col_sections' => 'Sections',
        'col_order' => 'Order',
        'col_action' => 'Action',
        'empty' => 'No profile pages yet. Click "+ Add Page" to create one.',
        'add_button' => 'Add Page',

        // Add/Edit modal
        'add_title' => 'Add Profile Page',
        'edit_title' => 'Edit Profile Page',
        'create_title' => 'Add Page',
        'form_title_label' => 'Page Title',
        'form_title_placeholder' => 'Enter page title',
        'form_type_label' => 'Page Type',
        'form_type_help' => 'Select the type of this page. Each type has different fields.',
        'form_description_label' => 'Content',
        'form_description_placeholder' => 'Enter page content...',
        'form_subtitle_label' => 'Subtitle',
        'form_subtitle_placeholder' => 'Enter subtitle',
        'form_link_text_label' => 'Link Text',
        'form_link_text_placeholder' => 'e.g. Learn More',
        'form_link_url_label' => 'Link URL',
        'form_link_url_placeholder' => 'https://example.com',
        'form_logo_label' => 'Logo',
        'form_logo_help' => 'PNG or WebP with transparent background. Max 2MB.',
        'form_order_label' => 'Order',
        'form_chart_section' => 'Charts (SDM)',
        'form_generate_chart' => 'Generate Chart',
        'form_generate_chart_desc' => 'Generate charts automatically from internal user data (Admin & Pegawai only).',
        'form_chart_pie' => 'Pie Chart (Gender)',
        'form_chart_bar' => 'Bar Chart (Age Group)',
        'form_chart_preview' => 'Chart Preview',
        'form_chart_no_data' => 'No chart data. Click "Generate Chart" to create charts.',
        'form_chart_no_users' => 'No internal user data found. Please add Admin and Pegawai users first.',
        'form_gambar_section' => 'Images',
        'form_gambar_help' => 'Upload images for this section. Max 2MB per image.',
        'btn_save_return' => 'Save & Return',

        // Delete
        'delete_title' => 'Delete Profile Page',
        'delete_confirm' => 'Are you sure you want to delete the page',
        'delete_yes' => 'Yes, Delete',

        // Flash
        'flash' => [
            'page_added' => 'Profile page added successfully.',
            'page_updated' => 'Profile page updated successfully.',
            'page_deleted' => 'Profile page deleted successfully.',
        ],

        // Buttons
        'btn_cancel' => 'Cancel',
        'btn_save' => 'Save Page',
        'btn_save_changes' => 'Save Changes',

        // Sections (for page section management)
        'sections_title' => 'Sections — :name',
        'sections_desc' => 'Manage sections for this profile page. Sections can contain titles, descriptions, and images.',
        'sections_list' => 'Sections',
        'add_section' => 'Add Section',
        'add_section_title' => 'Add Section',
        'edit_section_title' => 'Edit Section',
        'section_order' => 'Order: :order',
        'empty_sections' => 'No sections yet. Click "+ Add Section" to create one.',
        'section_form_title' => 'Section Title',
        'section_form_title_placeholder' => 'Enter section title',
        'section_form_description' => 'Description',
        'section_form_description_placeholder' => 'Enter description (optional)',
        'section_form_images' => 'Images',
        'section_form_add_images' => 'Upload Images',
        'section_form_add_more_images' => 'Add More Images',
        'section_form_images_help' => 'Select one or more images (JPEG, PNG, WebP). Max 2MB each.',
        'section_form_order' => 'Order',

        // Delete section
        'delete_section_title' => 'Delete Section',
        'delete_section_confirm' => 'Are you sure you want to delete the section',
        'delete_section_yes' => 'Yes, Delete',

        // Public
        'chart_pie' => 'Pie Chart (Gender)',
        'chart_bar' => 'Bar Chart (Age Group)',
        'public_empty' => 'No profile pages available yet.',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — User Management (pengguna/index)
    |--------------------------------------------------------------------------
    */
    'pengguna' => [
        'title' => 'User Management',
        'subtitle' => 'User List',
        'breadcrumb' => 'Users',
        'list' => 'User List',

        // Stats
        'stats_total' => 'Total Users',
        'stats_admin' => 'Admins',
        'stats_pegawai' => 'Staff',
        'stats_eksternal' => 'External Users',
        'stats_verified' => 'Verified',
        'stats_total_sub' => 'All user accounts',
        'stats_admin_sub' => 'Administrator accounts',
        'stats_pegawai_sub' => 'ANRI staff accounts',
        'stats_eksternal_sub' => 'Non-admin & non-staff accounts',
        'stats_verified_sub' => 'Emails have been verified',

        // Filters
        'filter_role' => 'Select Role',
        'filter_status' => 'Select Status',
        'filter_verified_all' => 'All Status',
        'filter_verified_yes' => 'Verified',
        'filter_verified_no' => 'Pending',

        // Table
        'col_user' => 'User',
        'col_email' => 'Email',
        'col_username' => 'Username',
        'col_role' => 'Role',
        'col_status' => 'Status',
        'col_joined' => 'Joined',
        'col_action' => 'Actions',

        // Buttons
        'add_button' => 'Add User',
        'edit_button' => 'Edit',
        'delete_button' => 'Delete',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'update' => 'Update',
        'back' => 'Back',

        // Forms
        'create_title' => 'Add New User',
        'create_subtitle' => 'Create a new user account for the system',
        'edit_title' => 'Edit User',
        'edit_subtitle' => 'Update user information',
        'form_name' => 'Full Name',
        'form_name_placeholder' => 'Enter full name',
        'form_username' => 'Username',
        'form_username_placeholder' => 'Optional',
        'form_email' => 'Email',
        'form_email_placeholder' => 'example@email.com',
        'form_role' => 'Role',
        'form_role_placeholder' => '-- Select Role --',
        'form_password' => 'Password',
        'form_password_placeholder' => 'Minimum 8 characters',
        'form_password_confirmation' => 'Confirm Password',
        'form_password_optional' => 'Leave blank if you do not want to change the password',
        'form_photo' => 'Profile Photo',
        'form_photo_help' => 'JPG/PNG, max 2MB. Optional.',
        'form_photo_current' => 'Current photo',

        // Role profile data
        'form_profile_title' => 'User Profile Data',
        'form_profile_desc' => 'Additional data per user role. All fields are optional.',
        'form_nip' => 'NIP',
        'form_nip_placeholder' => 'Enter NIP (18 digits)',
        'form_jenis_kelamin' => 'Gender',
        'form_tempat_lahir' => 'Birth Place',
        'form_tempat_lahir_placeholder' => 'e.g. Jakarta',
        'form_tanggal_lahir' => 'Birth Date',
        'form_kartu_identitas' => 'Identity Card (Upload)',
        'form_kartu_identitas_help' => 'JPG/PNG/PDF, max 2MB. Optional.',
        'form_kartu_identitas_current' => 'Current file',
        'form_kartu_identitas_view' => 'View file',
        'form_nomor_kartu_identitas' => 'Identity Card Number',
        'form_nomor_kartu_identitas_placeholder' => 'Enter ID/Student card number',
        'form_alamat' => 'Address',
        'form_alamat_placeholder' => 'Full address',
        'form_nomor_whatsapp' => 'WhatsApp Number',
        'form_nomor_whatsapp_placeholder' => 'e.g. 0831xxxxxxxx',
        'form_agama' => 'Religion',
        'form_agama_placeholder' => '— Select Religion —',
        'form_jabatan' => 'Position',
        'form_jabatan_placeholder' => '— Select Position —',
        'form_pangkat_golongan' => 'Rank / Class',
        'form_pangkat_golongan_placeholder' => '— Select Rank —',
        'form_jenis_keperluan' => 'Purpose Type',
        'form_jenis_keperluan_placeholder' => '— Select Purpose —',
        'form_judul_keperluan' => 'Purpose Title',
        'form_judul_keperluan_placeholder' => 'e.g. Thesis Research',
        'keperluan_register_only' => 'Register Only',
        'keperluan_research' => 'Research',
        'keperluan_visit' => 'Visit',

        // Status badges
        'status_verified' => 'Verified',
        'status_pending' => 'Pending',

        // Delete
        'delete_title' => 'Delete User',
        'delete_confirm' => 'Are you sure you want to delete user :name? This action cannot be undone.',
        'delete_yes' => 'Yes, Delete',

        // Flash
        'created_successfully' => 'User created successfully.',
        'updated_successfully' => 'User updated successfully.',
        'deleted_successfully' => 'User deleted successfully.',
        'cannot_delete_self' => 'You cannot delete your own account.',
        'already_verified' => 'This user\'s email is already verified.',
        'verification_sent' => 'Verification link sent successfully.',
        'marked_verified' => ':name\'s email has been marked as verified.',

        // Empty
        'empty' => 'No users yet.',

        // Export / DataTables buttons
        'btn_copy' => 'Copy',
        'btn_csv' => 'CSV',
        'btn_excel' => 'Excel',
        'btn_word' => 'Word',
        'btn_pdf' => 'PDF',
        'btn_print' => 'Print',
        'btn_export' => 'Export',
        'filter_section_title' => 'Filter',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Role Management (roles/index)
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'title' => 'Role Management',
        'subtitle' => 'User Roles List',
        'breadcrumb' => 'Roles',

        // Stats
        'stats_total' => 'Total Roles',
        'stats_system' => 'System',
        'stats_custom' => 'Custom',

        // Table
        'col_no' => 'No',
        'col_name' => 'Role Name',
        'col_label' => 'Label',
        'col_table' => 'Profile Table',
        'col_type' => 'Type',
        'col_users' => 'User Count',

        // Badges
        'type_system' => 'System',
        'type_custom' => 'Custom',

        // Buttons
        'add_button' => 'Add Role',

        // Forms
        'create_title' => 'Add New Role',
        'create_subtitle' => 'Create a new user role',
        'edit_title' => 'Edit Role',

        'form_name' => 'Role Name',
        'form_name_placeholder' => 'e.g. partner',
        'form_name_help' => 'Unique name (lowercase, no spaces). Used as database key.',
        'form_name_warning' => 'Warning: lowercase, numbers, and underscores only (no spaces & no capital letters).',
        'form_label' => 'Display Label',
        'form_label_placeholder' => 'e.g. Partner / Mitra',
        'form_type' => 'Role Type',
        'form_type_help' => 'System roles cannot be deleted. Custom roles can be deleted if they have no users.',
        'form_table_name' => 'Profile Table Name',
        'form_table_name_placeholder' => 'e.g. user_partners',
        'form_table_name_help' => 'Database table name for storing this role\'s profile data.',
        'form_relation_name' => 'Model Relation Name',
        'form_relation_name_placeholder' => 'e.g. userPartner',
        'form_relation_name_help' => 'Relation method name on User model. e.g. userPartner.',
        'form_description' => 'Description',
        'form_description_placeholder' => 'Short description of this role...',

        'name_system_locked' => 'System role name cannot be changed.',

        // Validation errors
        'validation_name_unique' => 'Role name already taken. Please choose another name.',
        'validation_name_regex' => 'Role name may only contain lowercase letters, numbers, and underscores (no spaces & no capital letters).',
        'validation_table_name_unique' => 'Profile Table Name already used by another role.',
        'validation_table_name_regex' => 'Table name may only contain lowercase letters, numbers, and underscores.',
        'validation_relation_name_unique' => 'Model Relation Name already used by another role.',
        'validation_relation_name_regex' => 'Relation name must be camelCase: lowercase first, then letters/numbers.',
        'validation_table_name_required' => 'Profile Table Name is required.',
        'validation_relation_name_required' => 'Model Relation Name is required.',

        // Delete
        'delete_confirm' => 'Are you sure you want to delete the role ":name"? Roles with users cannot be deleted.',

        // Flash
        'created_successfully' => 'Role created successfully.',
        'updated_successfully' => 'Role updated successfully.',
        'deleted_successfully' => 'Role deleted successfully.',
        'cannot_delete_with_users' => 'This role cannot be deleted because it still has users.',
        'cannot_delete_has_users' => 'This role cannot be deleted because it still has :count user(s).',
        'cannot_delete_system' => 'System roles cannot be deleted.',

        // Permissions
        'permissions_title' => 'Menu Permissions',
        'permissions_desc' => 'Manage sidebar menu access for this role.',
        'permissions_access' => 'Access Menu',

        // Columns management
        'col_columns' => 'Columns',
        'columns_count' => 'columns',
        'columns_title' => 'Table Column Structure',
        'columns_desc' => 'Define columns for this role\'s profile table. Columns will be automatically created in the database.',
        'add_column' => 'Add Column',
        'select_template' => 'Select Template',
        'empty_template' => 'Empty',
        'column' => 'Column',
        'table_structure' => 'Table Structure',
        'no_columns' => 'No columns added yet.',
        'col_column_name' => 'Column Name (DB)',
        'col_column_type' => 'Data Type',
        'col_column_label' => 'Display Label',
        'col_nullable' => 'Nullable',
        'col_unique' => 'Unique',
        'col_column_length' => 'Length',
        'col_options' => 'Options',
        'sync_columns' => 'Sync Columns',
        'sync_confirm' => 'Sync columns from database table to this form? Existing columns will be updated.',
        'columns_synced' => 'Columns successfully synced from database table.',
        'col_references_table' => 'References Table',
        'col_references_column' => 'References Column',
        'col_on_delete' => 'On Delete',
        'col_on_update' => 'On Update',
        'col_primary' => 'Primary',
        'col_unsigned' => 'Unsigned',
        'col_auto_increment' => 'Auto Increment',
        'col_foreign' => 'Foreign Key',
        'col_default' => 'Default',

        // Validation/error messages
        'error_unsigned_not_supported' => "Column ':column': UNSIGNED not supported for type ':type'.",
        'error_auto_increment_integer_only' => "Column ':column': AUTO_INCREMENT is only supported for MySQL integer types.",
        'error_auto_increment_not_null' => "Column ':column': AUTO_INCREMENT cannot be NULL.",
        'error_column_prefix' => "Column ':column' (MySQL :code): :message",
        'error_mysql_prefix' => "MySQL Error :code: :message",

        // Column pre-validation errors (store / update)
        'column_enum_space_empty' => 'Column #:index: ENUM/SET values must be comma-separated WITHOUT spaces. Use format: IV,IB,VIP (no spaces).',
        'column_enum_space_in_value' => "Column #:index: ENUM/SET values must not contain spaces. Change ':part' to ':clean'. Example: IV,IB,VIP",
        'column_enum_invalid_char' => "Column #:index: ENUM/SET value ':value' contains invalid characters. Only letters, numbers, and underscores are allowed.",
        'column_name_empty' => 'Column #:index: Column name cannot be empty.',
        'column_name_has_space' => "Column #:index: Column name ':name' must not contain spaces. Use underscores instead: nomor_kartu.",
        'column_name_invalid_pattern' => "Column #:index: Column name ':name' can only contain letters, numbers, and underscores. Must not start with a number.",
        'column_enum_required' => "Column #:index: ENUM requires at least one option in the 'Options' field. Example: IV,IB,VIP",
        'column_set_required' => "Column #:index: SET requires at least one option in the 'Options' field. Example: option_1,option_2",
        'column_enum_duplicate' => "Column #:index: ENUM/SET values must not contain duplicates. Duplicate values: ':values'",
        'column_name_prefix' => "Column ':column':",

        // MySQL error code mapped messages
        'mysql_1064' => 'Invalid SQL syntax. Check the combination of type, length, unsigned, nullability, and default value.',
        'mysql_1264' => 'Existing data is out of range for the new column type. Update or remove existing data first.',
        'mysql_1366' => 'Existing data cannot be converted to the new column type (incorrect value).',
        'mysql_1364' => 'NOT NULL column has no valid default value.',
        'mysql_1048' => 'NOT NULL column cannot contain NULL.',
        'mysql_1062' => 'UNIQUE/PRIMARY constraint failed because there are duplicate values in existing data.',
        'mysql_1452' => 'Foreign key constraint failed: a child record references a non-existent parent.',
        'mysql_1451' => 'Cannot modify/delete because the column is still referenced by another foreign key.',
        'mysql_1075' => 'Invalid AUTO_INCREMENT. Ensure only one column is auto-increment, it is an integer type, and has a key.',
        'mysql_1171' => 'PRIMARY KEY must be NOT NULL. Change the column to NOT NULL first.',

        // DataTables / Filters
        'search_placeholder' => 'Search...',
        'filter_type' => 'Select filter Type',
        'filter_columns' => 'Select filter Columns',
        'filter_columns_none' => 'No columns',
        'filter_columns_has' => 'Has columns',

        // Empty
        'empty' => 'No roles yet.',
    ],

];
