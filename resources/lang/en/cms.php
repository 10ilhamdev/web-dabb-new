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
        'hide' => 'Hide',
        'show_label' => 'Show',

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
            'move_title' => 'Move to Another Menu',
            'move_help' => 'Select another menu to move this feature into a sub-menu',
            'move_keep' => '— Keep in Main Menu —',
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
                'move_title' => 'Move to Another Menu',
                'move_help' => 'Leave blank to keep in the current menu (:name)',
                'move_keep' => '— Keep in current menu —',
                'move_top' => 'Make Main Menu (Top Level)',
                'badge_sub' => '(sub-menu)',
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
            'visibility_toggled' => 'Feature visibility toggled successfully.',
        ],

        'errors' => [
            'profile_requires_parent' => 'Profile feature requires a valid parent_id.',
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
            'add_new_image' => 'Add New Image',
        ],

        'delete_section' => [
            'title' => 'Delete Section',
            'confirm' => 'Are you sure you want to delete the section :name?',
            'yes' => 'Yes, Delete',
        ],
        'sections_empty' => 'No sections yet. Click "+ Add Section" to add one.',

        'flash' => [
            'page_added' => 'Page added successfully.',
            'page_updated' => 'Page updated successfully.',
            'page_deleted' => 'Page deleted successfully.',
            'section_added' => 'Section added successfully.',
            'section_updated' => 'Section updated successfully.',
            'section_deleted' => 'Section deleted successfully.',
            'visibility_toggled' => 'Page visibility toggled successfully.',
        ],

        // Public page
        'welcome' => 'Welcome to the :name portal,',
        'search_placeholder' => 'Search',
        'list_title' => ':name List',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Profile Pages (profile_pages)
    |--------------------------------------------------------------------------
    */

    'profile_pages' => [
        'title' => 'Page List: :name',
        'desc' => 'Manage profile pages for this menu',
        'preview_title' => 'Guest Page Preview',
        'preview_desc' => 'Page navigation that will be displayed on the public page',
        'page_label' => 'Page:',
        'nav_help' => 'Navigation buttons will appear on the public page to navigate between pages',
        'card_title' => 'Profile Page',
        'card_desc' => 'Manage profile pages. Sections are managed in the Edit page.',
        'add_button' => 'Add Page',
        'col_no' => 'No',
        'col_title' => 'Title',
        'col_type' => 'Type',
        'col_sections' => 'Section',
        'col_order' => 'Order',
        'col_action' => 'Action',
        'empty' => 'No pages yet. Click "Add Page" to create one.',
        'type_default' => 'Default',
        'type_sdm_chart' => 'HR (Chart)',
        'type_struktur_image' => 'Organizational Structure',
        'type_tugas_fungsi' => 'Tasks and Functions',
        'delete' => [
            'title' => 'Delete Page?',
            'confirm' => 'Are you sure you want to delete :name?',
            'cancel' => 'Cancel',
            'yes' => 'Delete',
        ],
        'form' => [
            'add_title' => 'Add Page',
            'edit_title' => 'Edit Page',
            'type' => 'Page Type',
            'type_help' => 'Select type according to the content to be displayed',
            'title' => 'Page Title',
            'title_placeholder' => 'Example: Tasks and Functions',
            'description' => 'Description / Content',
            'description_help' => 'Format text using the Rich Text Editor.',
            'link_settings' => 'Link Settings',
            'link_text' => 'Link Text',
            'link_text_placeholder' => 'Example: Learn More',
            'link_url' => 'Link URL',
            'subtitle_section' => 'Subtitle',
            'subtitle' => 'Additional Title',
            'subtitle_placeholder' => 'Example: Employee Count Chart by Age',
            'subtitle_help' => 'Subtitle to be displayed below the main title',
            'chart_title' => 'HR Chart',
            'chart_desc' => 'Choose data and chart type to display',
            'chart_roles' => 'Select User Roles to Count:',
            'chart_roles_help' => '* Leave empty to include all roles',
            'chart_field' => 'Select Data to Display:',
            'chart_field_placeholder' => '-- Select Data Field --',
            'chart_field_add' => 'Add',
            'chart_field_help' => 'Select data field to add chart. You can add multiple fields.',
            'chart_config' => 'Chart Configuration:',
            'chart_config_empty' => 'Select data field above to add chart',
            'chart_generate' => 'Generate Chart',
            'chart_preview_empty' => 'Select data field and chart type, then click "Generate Chart"',
            'images_title' => 'Supporting Images',
            'images_help' => 'Drag to change focal point position or click Position for preset. Max 10MB per file.',
            'images_upload_placeholder' => 'Click or drag to upload images',
            'order' => 'Order',
            'order_help' => 'Pages with lower order will be displayed first',
            'cancel' => 'Cancel',
            'save' => 'Save',
            'preview_header' => 'Page Preview',
            'preview_help' => 'You can drag images to change their position or change focal point',
            'preview_auto_update' => 'Preview automatically updates when you edit',
            'section_info_title' => 'Manage Section After Saving',
            'section_info_desc' => 'After the page is saved, you can manage sections (sub-content) through the Edit page.',
        ],
        'sections' => [
            'title' => 'Page Sections',
            'desc' => 'Manage sub-contents or sections for this page',
            'add_button' => 'Add Section',
            'empty' => 'No sections yet. Click "Add Section" to create one.',
            'add_title' => 'Add Section',
            'edit_title' => 'Edit Section',
            'form_title' => 'Section Title',
            'form_title_placeholder' => 'Example: Main Task',
            'form_desc' => 'Description',
            'form_desc_placeholder' => 'Section description...',
            'form_order' => 'Order',
            'delete' => [
                'title' => 'Delete Section?',
                'confirm' => 'Are you sure you want to delete :name?',
                'cancel' => 'Cancel',
                'yes' => 'Delete',
            ]
        ]
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
            'background_label' => 'Hero Background (Image or Video)',
            'unknown_type' => 'Unknown type',
            'current' => 'Current:',
            'remove_background' => 'Remove background (revert to default video)',
            'background_help' => 'Upload an <span class="font-semibold">image</span> (JPG/PNG/WebP/GIF/AVIF) or <span class="font-semibold">video</span> (MP4/WebM/OGG/MOV) to replace the hero background. If left blank, the website will use the default video (library-books.mp4).',
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
            'related_desc' => 'Links with clickable photos',
            'related_title' => 'Title',
            'related_photo' => 'Photo',
            'related_link' => 'Link',
            'add_related' => 'Add Link',
        ],

        'info' => [
            'title' => 'DABB Information Section',
            'desc' => 'Title and two paragraphs of information about DABB',
            'section' => 'Section Title',
            'image1' => 'Paragraph 1 Image',
            'image2' => 'Paragraph 2 Image',
            'image_help' => 'JPG, PNG, or WebP. Leave blank if you do not want to change.',
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
            'gallery_desc' => 'Archive exhibition section title on the homepage',
            'gallery_help' => 'Archive exhibition gallery content is automatically retrieved from virtual exhibition data.',
            'stats' => 'Section Title',
            'youtube' => 'Section Title',
            'instagram' => 'Section Title',
        ],

        'stats' => [
            'title' => 'Statistics Labels',
            'desc' => 'Text labels for visitor statistics counters',
            'total' => 'Total Visitors Label',
            'today' => 'Today\'s Visitors Label',
            'image_label' => 'Statistics Image',
            'help' => 'Visitor statistics numbers are automatically calculated based on the number of page accesses by visitors.',
        ],

        'youtube' => [
            'title' => 'YouTube Videos',
            'desc' => 'YouTube video IDs displayed in the carousel (format: ID only, example: F2NhNTiNxoY)',
            'video_label' => 'Video :number',
            'placeholder' => 'YouTube ID',
            'help' => 'Copy the ID from the YouTube URL: youtube.com/watch?v=<strong>ID_HERE</strong>',
            'add_video' => 'Add Video',
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
        'hotspot_label_index' => 'Hotspot :number',
        'label_tooltip' => 'Tooltip Text',
        'label_target_room' => 'Target Room',
        'label_delete_confirm' => 'Delete this hotspot?',
        'label_hotspot_type' => 'Hotspot Type',
        'type_floor' => 'Floor (Flat 3D)',
        'type_door' => 'Door (Vertical)',

        'preview_title' => '360° Panorama Preview',
        'preview_desc' => 'Click a target point on the panorama to get Yaw/Pitch, or drag to look around',
        'preview_placeholder' => 'Preview not available',
        'preview_placeholder_sub' => 'Select a 360° image first',

        'btn_cancel' => 'Cancel',
        'btn_save' => 'Save Changes',

        // Flash messages
        'flash' => [
            'created' => 'Virtual room created successfully.',
            'updated' => 'Virtual room updated successfully.',
            'deleted' => 'Virtual room deleted.',
        ],
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

        // Flash messages
        'flash' => [
            'created' => 'Virtual 3D room created successfully. You can now add media to the walls.',
            'updated' => 'Virtual 3D room updated successfully.',
            'deleted' => 'Virtual 3D room deleted.',
        ],
        'form_desc_edit' => 'Set up room information, colors, wall media, and navigation hotspots',

        // Shared form
        'info_title' => 'Room Information',
        'label_name' => 'Room Name',
        'label_desc' => 'Description',
        'label_thumbnail' => 'Room Thumbnail',
        'thumbnail_help' => 'Preview image for room list (JPG, PNG, WEBP)',
        'thumbnail_keep' => 'Leave empty if you don\'t want to change it',
        'label_diameter_front' => 'Front Wall Diameter (cm)',
        'label_diameter_back' => 'Back Wall Diameter (cm)',
        'label_diameter_left' => 'Left Wall Diameter (cm)',
        'label_diameter_right' => 'Right Wall Diameter (cm)',
        'diameter_help' => 'Wall diameter size in centimeters (default: 1000)',

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
        'media_upload_failed' => 'Upload failed.',
        'media_save_success' => 'Position & size saved!',
        'media_save_failed' => 'Failed to save position.',
        'media_delete_confirm' => 'Delete this media from the wall?',
        'media_delete_success' => 'Media deleted.',
        'media_delete_failed' => 'Failed to delete media.',
        'media_count' => 'items',

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
        'editor_wall_title_left'  => 'LEFT WALL',
        'editor_wall_title_right' => 'RIGHT WALL',
        'editor_wall_title_back'  => 'BACK WALL',
        'editor_door_settings_for' => 'Door settings for',
        'editor_props_title' => 'Selected Media Properties',
        'editor_props_delete' => 'Delete',
        'editor_props_save' => 'Save Position',
        'media_desc_label' => 'Caption / Description',
        'media_desc_placeholder' => 'Enter detailed caption (HTML supported)...',
        'caption_single' => 'Single Text',
        'caption_multi_qa' => 'Questions & Answers (Q&A)',
        'question' => 'Question',
        'answer' => 'Answer',
        'add_qa' => 'Add Q&A',

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
            'pdf_section' => 'PDF File (Optional)',
            'upload_pdf' => 'Upload PDF',
            'pdf_desc' => 'If uploaded, the book will use this PDF as the flipbook content.',
            'book_info' => 'Book Information',
            'author' => 'Author',
            'dimensions' => 'Dimensions',
            'total_pages' => 'Total Pages',
            'weight' => 'Weight',
            'language' => 'Language',
            'publisher' => 'Publisher',
            'publication_year' => 'Publication Year',
            'isbn' => 'ISBN',
            'synopsis' => 'Synopsis',
            'description' => 'Description (Displayed on Book Detail Page)',
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

        // Flash messages
        'flash' => [
            'created' => 'Book created successfully.',
            'updated' => 'Book updated successfully.',
            'deleted' => 'Book deleted.',
        ],

        // Buttons
        'btn_cancel' => 'Cancel',
        'btn_save' => 'Save Book',
        'btn_save_changes' => 'Save Changes',

        // JS messages
        'pdf_loading' => 'Calculating PDF pages...',
        'pdf_success' => 'Successfully detected :count pages',
        'pdf_failed' => 'Failed to read PDF pages',
        'upload_failed' => 'Failed to upload file.',
        'pdf_info' => 'This book uses a PDF file. Manual pages below will be ignored in the exhibition view.',
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

        // Flash messages
        'flash' => [
            'created' => 'Book page created successfully.',
            'updated' => 'Book page updated successfully.',
            'deleted' => 'Book page deleted.',
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
        'publication' => 'Publication',
        'layanan_publik' => 'Public Service',
        'pengelolaan' => 'Management',
        'kontak_kami' => 'Contact Us',
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
        'download' => 'Download',
        'zoom' => 'Zoom In',
        'hide' => 'Hide',
        'show' => 'Show',
        'delete' => 'Delete',
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
        'form_username_placeholder' => 'Required',
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

        // MySQL type rules — attribute visibility (shown as help text in form)
        'rule_no_length' => "Type ':type' does not support length/character attribute.",
        'rule_no_unique' => "Type ':type' does not support UNIQUE (BLOB/TEXT/JSON).",
        'rule_no_unsigned' => "Type ':type' does not support UNSIGNED.",
        'rule_no_auto_increment' => "Type ':type' does not support AUTO_INCREMENT (integer types only).",
        'rule_no_foreign' => "Type ':type' does not support FOREIGN KEY (integer/varchar/char only).",
        'rule_no_primary' => "Type ':type' does not support PRIMARY KEY (integer types only).",

        // MySQL type constraints (explained for users)
        'type_constraint_year' => "YEAR: 2-digit values (00-69→2000-2069, 70-99→1970-1999) or 4-digit (1901-2155).",
        'type_constraint_json' => "JSON: stores validated JSON data. Does not support character length.",
        'type_constraint_spatial' => "Spatial types (GEOMETRY/POINT/POLYGON, etc.): no length/unsigned/unique/auto_increment. Requires SRID (default 0).",
        'type_constraint_bit' => "BIT: format BIT(M) with M=1-64. Example: BIT(8) for 1 byte.",
        'type_constraint_boolean' => "BOOLEAN: stored as TINYINT(1). Value 0=FALSE, 1=TRUE.",
        'type_constraint_binary' => "BINARY/VARBINARY: stores binary data (files, images, etc.). Length optional for BINARY, required for VARBINARY.",
        'type_constraint_real' => "REAL: synonym for DOUBLE(53) in MySQL. Precision ~15 decimal digits.",
        'type_constraint_enum' => "ENUM: max 65,535 values. Values cannot be empty or duplicate. Order matters (ordinal).",
        'type_constraint_set' => "SET: similar to ENUM but can store SEVERAL values at once (max 64 members).",

        // Column pre-validation errors (store / update)
        'column_enum_space_empty' => 'Column #:index: ENUM/SET values must be comma-separated WITHOUT spaces. Use format: IV,IB,VIP (no spaces).',
        'column_enum_space_in_value' => "Column #:index: ENUM/SET values must not contain spaces. Change ':part' to ':clean'. Example: IV,IB,VIP",
        'column_enum_invalid_char' => "Column #:index: ENUM/SET value ':value' contains invalid characters. Single-quote ('), double-quote (\"), backslash (\\), and comma (,) are not allowed inside a value.",
        'column_name_empty' => 'Column #:index: Column name cannot be empty.',
        'column_name_has_space' => "Column #:index: Column name ':name' must not contain spaces. Use underscores instead: nomor_kartu.",
        'column_name_invalid_pattern' => "Column #:index: Column name ':name' can only contain letters, numbers, and underscores. Must not start with a number.",
        'column_enum_required' => "Column #:index: ENUM requires at least one option in the 'Options' field. Example: IV,IB,VIP",
        'column_set_required' => "Column #:index: SET requires at least one option in the 'Options' field. Example: option_1,option_2",
        'column_enum_duplicate' => "Column #:index: ENUM/SET values must not contain duplicates. Duplicate values: ':values'",
        'column_name_prefix' => "Column ':column':",

        // ENUM/SET editor
        'enum_editor_btn' => 'Open Editor',
        'enum_editor_title' => 'ENUM/SET Value Editor',
        'enum_editor_subtitle' => 'Manage option values easily, no more space issues.',
        'enum_editor_help' => 'Press Enter in an input to add a new row. Use arrow buttons ↑↓ to reorder.',
        'enum_editor_add' => 'Add Value',
        'enum_editor_value_placeholder' => 'Type a value here...',
        'enum_editor_move_up' => 'Move up',
        'enum_editor_move_down' => 'Move down',
        'enum_editor_remove' => 'Remove',

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

        // DataTables (i18n) — shared, accessed as cms.roles.datatable_info
        'datatable_info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
        'datatable_info_empty' => 'No entries',
        'datatable_info_filtered' => '(filtered from _MAX_ total entries)',
        'datatable_zero_records' => 'No matching records found',
        'datatable_search_placeholder' => 'Search...',

        // Role labels (i18n)
        'labels' => [
            'administrator' => 'Administrator',
            'pegawai' => 'Staff',
            'umum' => 'General Public',
            'pelajar_mahasiswa' => 'Student',
            'instansi_swasta' => 'Private Sector',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Publications
    |--------------------------------------------------------------------------
    */
    'publication' => [
        'title' => 'Publication Management: :name',
        'desc' => 'Manage Announcements, News, and Gallery for this menu',
        'list_title' => 'Publication Content List',
        'list_desc' => 'Order determines the display position on the public page',
        'add_button' => 'Add Content',
        'back' => 'Back',
        
        // Table columns
        'col_no' => 'No',
        'col_title' => 'Title',
        'col_type' => 'Type',
        'col_date' => 'Date',
        'col_order' => 'Order',
        'col_action' => 'Action',

        // Types
        'type_announcement' => 'Announcement',
        'type_news' => 'News/Article',
        'type_gallery' => 'Gallery',

        // Form
        'create_title' => 'Add Publication Content',
        'edit_title' => 'Edit Publication Content',
        'label_type' => 'Type',
        'label_title' => 'Title',
        'placeholder_title' => 'Enter content title...',
        'label_description' => 'Description / Content',
        'label_gallery' => 'Gallery Media (Photo/Video)',
        'hint_gallery' => 'Click or drag media here',
        'hint_gallery_sub' => 'Supports image and video formats',
        'gallery_info_create' => 'The Gallery page will automatically collect all image and video media from the system. Media you add below will appear as a priority in the initial order.',
        'gallery_info_edit' => 'The Gallery page will automatically collect all image and video media from the system. Media you add/save below will appear as a priority in the initial order.',
        'delete_media' => 'Delete Media',
        'label_pdf' => 'Document File (PDF)',
        'hint_pdf' => 'PDF format max 5MB',
        'hint_pdf_edit' => 'Upload to replace existing file',
        'current_doc' => 'Current Document',
        'view_pdf' => 'View PDF File',
        
        'sidebar_title' => 'Page Settings',
        'label_order' => 'Order',
        'label_date' => 'Date',
        'label_subtitle' => 'Sub-title / Summary',
        'placeholder_subtitle' => 'Brief summary of news...',
        
        'btn_save' => 'Save Content',
        'btn_update' => 'Update Content',
        'btn_cancel' => 'Cancel',

        // Delete modal
        'delete_title' => 'Delete Content?',
        'delete_confirm' => 'Are you sure you want to delete :name?',
        'delete_yes' => 'Delete',
        'delete_no' => 'Cancel',

        // Empty state
        'empty' => 'No publication content yet. Click "Add Content" to add one.',

        // Flash
        'flash' => [
            'added' => 'Publication content added successfully.',
            'updated' => 'Publication content updated successfully.',
            'deleted' => 'Publication content deleted successfully.',
        ],
    ],

    'layanan_publik' => [
        'title' => 'Public Service Management: :name',
        'desc' => 'Manage Public Service Pages (Visits, LARASKA, Static Archives, etc.)',
        'list_title' => 'Public Service Content List',
        'list_desc' => 'Order determines the display position on the public page',
        'add_button' => 'Add Content',
        'back' => 'Back',
        
        // Table columns
        'col_no' => 'No',
        'col_title' => 'Title',
        'col_type' => 'Service Type',
        'col_date' => 'Date',
        'col_order' => 'Order',
        'col_action' => 'Action',

        // Types
        'type_kunjungan' => 'Visit Registration',
        'type_laraska' => 'LARASKA',
        'type_statis' => 'Static Archive Service',
        'type_konsultasi' => 'Archive Consultation',
        'type_perpustakaan' => 'Library',
        'type_umum' => 'General Service',

        // Form
        'create_title' => 'Add Public Service Content',
        'edit_title' => 'Edit Public Service Content',
        'label_type' => 'Service Type',
        'label_title' => 'Title',
        'placeholder_title' => 'Enter content title...',
        'label_description' => 'Description / Procedures / Guidelines',
        'label_gallery' => 'Gallery Media (Photo/Video)',
        'hint_gallery' => 'Click or drag media here',
        'hint_gallery_sub' => 'Supports image and video formats',
        'label_pdf' => 'Document / Form File (PDF)',
        'hint_pdf' => 'PDF format max 5MB',
        'hint_pdf_edit' => 'Upload to replace existing file',
        'current_doc' => 'Current Document',
        'view_pdf' => 'View PDF File',
        
        'sidebar_title' => 'Page Settings',
        'label_order' => 'Order',
        'label_date' => 'Date',
        'label_subtitle' => 'Sub-title / Summary',
        'placeholder_subtitle' => 'Brief summary of service...',
        
        'btn_save' => 'Save Content',
        'btn_update' => 'Update Content',
        'btn_cancel' => 'Cancel',

        // Delete modal
        'delete_title' => 'Delete Content?',
        'delete_confirm' => 'Are you sure you want to delete :name?',
        'delete_yes' => 'Delete',
        'delete_no' => 'Cancel',

        // Empty state
        'empty' => 'No public service content yet. Click "Add Content" to add one.',

        // Flash
        'flash' => [
            'added' => 'Public service content added successfully.',
            'updated' => 'Public service content updated successfully.',
            'deleted' => 'Public service content deleted successfully.',
        ],

        // Kunjungan settings
        'kunjungan_settings_title' => 'Visit Registration Service Settings',
        'kunjungan_settings_desc' => 'Configure schedules, capacity rules, daily quotas, holidays, closed slots, and form fields.',
        'section1_title' => '1. Visit Schedule',
        'btn_hide_guest' => 'Hide this section on guest page',
        'btn_show_guest' => 'Show this section on guest page',
        'status_show' => 'Shown',
        'status_hide' => 'Hidden',
        'section1_label_title' => 'Section 1 Title (Optional, default: Visit Schedule)',
        'section1_placeholder_title' => 'Visit Schedule',
        'section1_label_desc' => 'Schedule Description Text',
        
        'section2_title' => '2. Visit Application',
        'section2_label_title' => 'Section 2 Title (Optional, default: Visit Application)',
        'section2_placeholder_title' => 'Visit Application',
        'section2_label_desc' => 'Application Rules / Capacity',
        
        'section3_title' => '3. Visit Calendar',
        'section3a_title' => '3a. Holiday / Special Closed Date Settings',
        'section3a_desc' => 'Dates configured below are additional holidays/closed dates (optional).',
        'info_auto_title' => 'Automation Information:',
        'info_auto_desc' => 'National Holidays, Collective Leave, as well as Saturday & Sunday are automatically detected from the official calendar. You do not need to add them manually here, just add internal institutional closing dates if any.',
        'btn_add_holiday' => 'Add Holiday',
        'label_holiday_date' => 'Holiday Date',
        'label_holiday_reason' => 'Description / Reason',
        'placeholder_holiday_reason' => 'e.g. National Holiday / Closed',
        'btn_delete' => 'Delete',
        'empty_holidays' => 'No special holidays added yet.',
        
        'section3b_title' => '3b. Daily Maximum Slot Settings',
        'section3b_desc' => 'Set the maximum registration quota limit for each day (applies automatically every day if no special setting in 3c).',
        'label_daily_quota' => 'Maximum Visit Slots / Day',
        
        'section3c_title' => '3c. Special Quota / Specific Time Slot Closing (Morning / Afternoon)',
        'section3c_desc' => 'Set the maximum number of slots on specific dates & times (enter 0 to close the slot completely).',
        'btn_add_close_slot' => 'Add Slot Closing',
        'label_date' => 'Date',
        'label_slot_time' => 'Time / Slot',
        'slot_pagi' => 'Morning (07:30 - 12:00)',
        'slot_siang' => 'Afternoon (13:00 - 16:00)',
        'label_max_slot' => 'Number of Slots (Max)',
        'placeholder_close_slot' => '0 = Closed',
        'label_max_hint' => 'Max slots : :max',
        'label_close_reason' => 'Description / Reason',
        'placeholder_close_reason' => 'e.g. Morning Quota Full',
        'empty_close_slots' => 'No special slot closings added yet.',
        
        'section4_title' => '4. Visit Form Column List Settings',
        'btn_add_form_field' => 'Add Form Column',
        'label_field_id' => 'Field ID (Unique)',
        'label_field_label' => 'Form Label',
        'label_field_type' => 'Input Type',
        'type_text' => 'Short Text (text)',
        'type_email' => 'Email (email)',
        'type_number' => 'Number (number)',
        'type_date' => 'Date (date)',
        'type_select' => 'Selection (select)',
        'type_file' => 'Upload File (file)',
        'type_textarea' => 'Long Text (textarea)',
        'label_required' => 'Required',
        'label_options_select' => 'Selection Options (Comma separated)',
        'label_options_file' => 'Notes / File Format',
        'placeholder_options_select' => 'e.g. Education, Research, Work Visit',
        'placeholder_options_file' => 'e.g. Format pdf/doc, max 2MB',
        
        'label_auto_today' => 'Auto update to today\'s date',
        'btn_edit' => 'Edit',

        'laraska_settings_title' => 'LARASKA Service Settings',
        'laraska_settings_desc' => 'Configure service hours, service announcement, and LARASKA mechanism steps.',
        'label_laraska_hours' => 'Service Hours Text',
        'maklumat_box_title' => 'Service Announcement',
        'label_maklumat_title' => 'Service Announcement Title',
        'label_maklumat_content' => 'Service Announcement Content',
        'label_maklumat_date' => 'Announcement Place & Date',
        'label_maklumat_director' => 'Announcement Official',
        'label_laraska_mech_title' => 'Service Mechanism Title',
        'label_laraska_step1_title' => 'Step 1 Title',
        'label_laraska_step1_desc' => 'Step 1 Description',
        'label_laraska_step2_title' => 'Step 2 Title',
        'label_laraska_step2_desc' => 'Step 2 Description',
        'label_laraska_step3_title' => 'Step 3 Title',
        'label_laraska_step3_desc' => 'Step 3 Description',
        'label_laraska_step4_title' => 'Step 4 Title',
        'label_laraska_step4_desc' => 'Step 4 Description',
        'btn_add_laraska_step' => 'Add Mechanism Step',

        // Statis
        'statis_settings_title' => 'Static Archive Service Settings',
        'statis_settings_desc' => 'Configure service hours, search stages, mechanisms, and guide files for static archive services.',
        'label_statis_hours' => 'Service Hours',
        'label_statis_order_hours' => 'Archive Order Hours',
        'statis_stages_title' => 'Archive Search Stages (Flow Circles)',
        'btn_add_statis_stage' => 'Add Flow Stage',
        'label_stage_name' => 'Stage Name',
        'statis_mech1_title' => 'Direct Service Mechanism (On-site)',
        'label_statis_mech1_title' => 'Direct Mechanism Title',
        'label_statis_direct_pdf' => 'Guide PDF File (Direct Mechanism)',
        'btn_add_statis_mech1_step' => 'Add Direct Mechanism Step',
        'label_statis_step_title_direct' => 'Step Title',
        'label_statis_step_desc' => 'Step Description',
        'statis_mech2_title' => 'Indirect Service Mechanism (Online)',
        'label_statis_mech2_title' => 'Indirect Mechanism Title',
        'label_statis_indirect_pdf' => 'Guide PDF File (Indirect Mechanism)',
        'btn_add_statis_mech2_step' => 'Add Indirect Mechanism Step',
        'label_statis_step_title_indirect' => 'Step Title',
        'btn_delete_step' => 'Delete Step',
        'btn_delete_stage' => 'Delete Stage',
        'placeholder_auto_file' => 'File name (auto-filled on upload)',
        'btn_delete_doc' => 'Delete Document',
        'btn_delete_photo' => 'Delete Photo',

        // Konsultasi
        'konsultasi_settings_title' => 'Archive Consultation Settings',
        'konsultasi_settings_desc' => 'Configure service descriptions and archive consultation form settings displayed on the guest page.',
        'label_consultation_desc' => 'Consultation Service Description',
        'consultation_form_general_title' => 'General Consultation Form Settings',
        'label_consultation_form_title' => 'Form Title',
        'label_consultation_form_send' => 'Submit Button Label',
        'label_consultation_success' => 'Success Message (after submit)',
        'consultation_form_fields_title' => 'Consultation Form Column List Settings',
        'label_placeholder' => 'Placeholder',
        'placeholder_placeholder' => 'e.g. Enter text...',

        // Perpustakaan
        'perpustakaan_settings_title' => 'Library Service Settings',
        'perpustakaan_settings_desc' => 'Configure library objectives, facilities, service hours, rules, procedures, and guide files.',
        'lib_objs_title' => 'Library Objectives',
        'btn_add_lib_obj' => 'Add Objective Point',
        'placeholder_lib_obj' => 'Write objective point...',
        'lib_visit_btn_title' => 'Visit Library Website Button',
        'label_lib_visit_btn' => 'Button Label',
        'label_lib_redirect_url' => 'Library Website URL (optional)',
        'hint_lib_redirect_url' => 'If empty, a default info popup will appear.',
        'lib_cards_title' => 'Library Facilities & Services (Cards)',
        'btn_add_lib_card' => 'Add Facility',
        'label_lib_card' => 'Facility',
        'label_lib_card_title' => 'Facility Title',
        'label_lib_card_desc' => 'Facility Description',
        'placeholder_title_general' => 'Title...',
        'placeholder_desc_general' => 'Description...',
        'label_lib_hours' => 'Service Hours',
        'lib_rules_title' => 'Rules & Regulations (Points)',
        'btn_add_lib_rule' => 'Add Rule',
        'placeholder_lib_rule' => 'Rule...',
        'lib_photos_title' => 'Library Facility Photos',
        'label_lib_photos' => 'Select Multiple Photos (More than 1 allowed)',
        'label_lib_photos_edit' => 'Add Multiple New Photos (More than 1 allowed)',
        'btn_choose_images' => 'Choose Images',
        'placeholder_lib_photos_names' => 'File names (auto)',
        'hint_lib_photos' => 'You can select multiple photos at once when the file selection window opens.',
        'hint_lib_photos_edit' => 'New photos selected here will be added to the existing photo list.',
        'lib_procs_title' => 'Procedure Flow & Stages Title',
        'btn_add_lib_proc' => 'Add Procedure',
        'placeholder_lib_proc_title' => 'Main procedure title...',
        'label_lib_proc' => 'Procedure',
        'label_lib_proc_title' => 'Procedure Title',
        'label_lib_proc_desc' => 'Procedure Description',
        'label_lib_pdf' => 'Library Guide PDF File',
        'btn_choose_file' => 'Choose File',
        'current_photos_title' => 'Current Uploaded Photos (Remove to replace/delete):',
    ],


    'pengelolaan' => [
        'title' => 'Pengelolaan Management: :name',
        'desc' => 'Manage Pengelolaan Pages (Penyusutan, Penyimpanan, Preservasi, Pengolahan, Pemanfaatan, Penjangkauan, Akuisisi)',
        'list_title' => 'Pengelolaan Content List',
        'list_desc' => 'Order determines the display position on the public page',
        'add_button' => 'Add Content',
        'edit_button' => 'Edit Content',
        'back' => 'Back',
        
        // Table columns
        'col_no' => 'No',
        'col_title' => 'Title',
        'col_type' => 'Page Type',
        'col_date' => 'Date',
        'col_order' => 'Order',
        'col_action' => 'Action',

        // Types
        'type_penyusutan' => 'Penyusutan Arsip',
        'type_penyimpanan' => 'Penyimpanan Arsip',
        'type_preservasi' => 'Preservasi Arsip',
        'type_pengolahan' => 'Pengolahan Arsip Statis',
        'type_pemanfaatan' => 'Pemanfaatan Arsip',
        'type_penjangkauan' => 'Penjangkauan Arsip',
        'type_akuisisi' => 'Akuisisi Arsip',

        // Form
        'create_title' => 'Add Pengelolaan Content',
        'edit_title' => 'Edit Pengelolaan Content',
        'label_type' => 'Page Type',
        'label_title' => 'Title',
        'placeholder_title' => 'Enter content title...',
        'label_description' => 'Description / Content',
        'label_gallery' => 'Media Images',
        'hint_gallery' => 'Click or drag images here',
        'hint_gallery_sub' => 'Supports jpg, png, webp formats',
        'label_pdf' => 'Document / Guide / Form File (PDF)',
        'hint_pdf' => 'PDF format max 5MB',
        'hint_pdf_edit' => 'Upload to replace existing file',
        'current_doc' => 'Current Document',
        'view_pdf' => 'View PDF File',
        
        'sidebar_title' => 'Page Settings',
        'label_order' => 'Order',
        'label_date' => 'Date',
        'label_subtitle' => 'Sub-title / Summary',
        'placeholder_subtitle' => 'Brief summary...',
        
        'btn_save' => 'Save Content',
        'btn_update' => 'Update Content',
        'btn_cancel' => 'Cancel',
        'btn_edit' => 'Edit',

        // Delete modal
        'delete_title' => 'Delete Content?',
        'delete_confirm' => 'Are you sure you want to delete :name?',
        'delete_yes' => 'Delete',
        'delete_no' => 'Cancel',

        // Empty state
        'empty' => 'No pengelolaan content yet. Click "Add Content" to add one.',

        // Flash
        'flash' => [
            'added' => 'Pengelolaan content added successfully.',
            'updated' => 'Pengelolaan content updated successfully.',
            'deleted' => 'Pengelolaan content deleted successfully.',
        ],

        // Sub-type specific labels
        'penyusutan_settings_title' => 'Penyusutan Arsip Settings',
        'penyusutan_settings_desc' => 'Configure images and description for penyusutan arsip.',
        'penyimpanan_settings_title' => 'Penyimpanan Arsip Settings',
        'penyimpanan_settings_desc' => 'Configure storage principles, storage systems (cards), facilities, and room types.',
        'preservasi_settings_title' => 'Preservasi Arsip Settings',
        'preservasi_settings_desc' => 'Configure preservation list, restoration text, and restoration steps.',
        'pengolahan_settings_title' => 'Pengolahan Arsip Statis Settings',
        'pengolahan_settings_desc' => 'Configure processing points, infographics, and service mechanism file.',
        'pemanfaatan_settings_title' => 'Pemanfaatan Arsip Settings',
        'pemanfaatan_settings_desc' => 'Configure legal quote, archive access list, and mechanism file.',
        'penjangkauan_settings_title' => 'Penjangkauan Arsip Settings',
        'penjangkauan_settings_desc' => 'Configure outreach activities list and guide/catalog file.',
        'akuisisi_settings_title' => 'Akuisisi Arsip Settings',
        'akuisisi_settings_desc' => 'Configure acquisition stages and acquisition form/guideline file.',

        // Custom fields
        'label_prinsip' => 'Storage Principle (Text/Box)',
        'label_sistem_title' => 'Storage Systems List (Cards)',
        'btn_add_sistem' => 'Add Storage System',
        'label_fasilitas_title' => 'Facilities & Equipment List',
        'btn_add_fasilitas' => 'Add Facility',
        'label_ruang_title' => 'Storage Room Types List',
        'btn_add_ruang' => 'Add Room Type',
        'label_preservasi_list' => 'Preservation Activities List',
        'btn_add_preservasi' => 'Add Preservation Activity',
        'label_restorasi_desc' => 'Archive Restoration Description',
        'label_restorasi_list' => 'Restoration Stages List',
        'btn_add_restorasi' => 'Add Restoration Stage',
        'label_pengolahan_list' => 'Processing Points List',
        'btn_add_pengolahan' => 'Add Processing Point',
        'label_mekanisme_title' => 'Service Mechanism Title',
        'label_mekanisme_desc' => 'Service Mechanism Description',
        'label_pemanfaatan_quote' => 'Legal Basis Quote (Box)',
        'label_akses_list' => 'Archive Access / Types List',
        'btn_add_akses' => 'Add Access Type',
        'label_kegiatan_list' => 'Outreach Activities List',
        'btn_add_kegiatan' => 'Add Activity',
        'label_tahapan_list' => 'Acquisition Stages List',
        'btn_add_tahapan' => 'Add Stage',

        // Additional dynamic form & section labels
        'label_prinsip_title' => 'Storage Principles Section Title',
        'label_prinsip_desc' => 'Storage Principles Section Description',
        'label_prinsip_list' => 'Storage Principles Points List',
        'btn_add_poin' => 'Add Point',
        'item_prinsip' => 'Principle',
        'btn_delete_poin' => 'Delete Point',
        'label_poin_title' => 'Point Title',
        'placeholder_prinsip_title' => 'Example: Integrity',
        'label_poin_desc' => 'Point Description',
        'placeholder_prinsip_desc' => 'Example: Archives are stored without changing original arrangement...',

        'label_sistem_section_title' => 'Storage Systems Section Title',
        'placeholder_sistem_title' => 'Example: Storage Systems',
        'item_sistem' => 'System',
        'label_sistem_name' => 'System Title',
        'label_choose_icon' => 'Choose Icon',
        'label_desc_general' => 'Description',

        'label_fasilitas_section_title' => 'Facilities Section Title',
        'placeholder_fasilitas_title' => 'Example: Storage Facilities',
        'label_upload_fasilitas' => 'Upload Facility Images (Multiple allowed)',
        'hint_upload_multiple' => 'Format: JPG, PNG, WebP. You can select multiple files at once.',

        'label_ruang_section_title' => 'Room Section Title',
        'placeholder_ruang_title' => 'Example: Storage Room',
        'label_upload_ruang' => 'Upload Room Images (Multiple allowed)',

        'label_preservasi_section_title' => 'Preservation Activities Section Title',
        'placeholder_preservasi_title' => 'Example: Preservation Activities',

        'label_restorasi_section_title' => 'Archive Restoration Section Title',
        'placeholder_restorasi_title' => 'Example: ARCHIVE RESTORATION',

        'label_pengolahan_section_title' => 'Processing Section Title',
        'placeholder_pengolahan_title' => 'Example: Processing Stages',

        'label_akses_section_title' => 'Access List Section Title',
        'placeholder_akses_title' => 'Example: Access & Utilization Services',
        'item_akses' => 'Access',
        'label_akses_name' => 'Access Title',

        'label_kegiatan_section_title' => 'Outreach Activities List Section Title',
        'placeholder_kegiatan_title' => 'Example: Outreach Programs & Activities',
        'item_kegiatan' => 'Activity',
        'label_kegiatan_name' => 'Activity Title',
        'label_button_text' => 'Button Text (Top Right)',
        'placeholder_button_text' => 'Example: Visit',
        'label_button_url' => 'Button URL Link',
        'placeholder_button_url' => 'https://...',

        'label_tahapan_section_title' => 'Acquisition Stages List Section Title',
        'placeholder_tahapan_title' => 'Example: Acquisition Stages & Procedures',
        'item_tahapan' => 'Stage',
        'label_tahapan_name' => 'Stage Title',

        'label_current_images_del' => 'Current Images (Check to remove):',
        'btn_delete_check' => 'Remove',

        'icons' => [
            'sistem_clipboard' => '📋 Clipboard (Classification / Number)',
            'sistem_archive' => '🗃️ Archive Box (Subject / File)',
            'sistem_book' => '📖 Book (Alphabetical / Guide)',
            'sistem_calendar' => '📅 Calendar (Date / Chronological)',
            'sistem_map' => '🗺️ Map (Territory / Geographical)',
            'sistem_document' => '📄 Document (File / Notes)',
            'sistem_lock' => '🔒 Lock (Security / Confidential)',
            'sistem_database' => '🗄️ Database (Server / Storage)',
            'sistem_tag' => '🏷️ Tag (Label / Category)',
            'sistem_folder' => '📁 Folder (Directory / File)',
            'sistem_check' => '✔️ Checkmark (Verification / Completed)',
            'sistem_star' => '⭐ Star (Featured / Important)',

            'akses_clipboard' => '📋 Clipboard (Classification / List)',
            'akses_archive' => '🗃️ Archive Box (Subject / File)',
            'akses_book' => '📖 Book (Alphabetical / Guide)',
            'akses_calendar' => '📅 Calendar (Date / Chronological)',
            'akses_map' => '🗺️ Map (Territory / Geographical)',
            'akses_document' => '📄 Document (File / Notes)',
            'akses_lock' => '🔒 Lock (Security / Confidential)',
            'akses_database' => '🗄️ Database (Server / Storage)',

            'kegiatan_clipboard' => '📋 Clipboard (List / Exhibition)',
            'kegiatan_archive' => '🗃️ Archive Box (Collection / File)',
            'kegiatan_book' => '📖 Book (Publication / Script)',
            'kegiatan_calendar' => '📅 Calendar (Activity / Partnership)',
            'kegiatan_map' => '🗺️ Map (Territory / Geographical)',
            'kegiatan_document' => '📄 Document (File / Notes)',
            'kegiatan_lock' => '🔒 Lock (Security / Confidential)',
            'kegiatan_database' => '🗄️ Database (Server / Storage)',
            'kegiatan_users' => '👥 Users (Socialization / Education)',
            'kegiatan_globe' => '🌐 Globe (International / Website)',
            'kegiatan_tag' => '🏷️ Tag (Label / Category)',
            'kegiatan_folder' => '📁 Folder (Directory / File)',
            'kegiatan_check' => '✔️ Checkmark (Verification / Completed)',
            'kegiatan_star' => '⭐ Star (Featured / Important)',
        ],
    ],


    'kontak_kami' => [
        'title' => 'Contact Us Management: :name',
        'desc' => 'Manage Contact Us Page (Main Contact, Branch, Headquarters, or Other)',
        'list_title' => 'Contact List',
        'list_desc' => 'Order will determine display position on public page',
        'add_button' => 'Add Contact',
        'edit_button' => 'Edit Contact',
        'back' => 'Back',
        
        // Table columns
        'col_no' => 'No',
        'col_title' => 'Title / Contact Name',
        'col_type' => 'Contact Type',
        'col_date' => 'Date',
        'col_order' => 'Order',
        'col_action' => 'Action',

        // Types
        'type_kontak' => 'Main Contact',
        'type_cabang' => 'Branch Office',
        'type_pusat' => 'Headquarters',
        'type_lainnya' => 'Other',

        // Form
        'create_title' => 'Add Contact Us',
        'edit_title' => 'Edit Contact Us',
        'label_type' => 'Contact Type',
        'label_title' => 'Title / Office Name',
        'placeholder_title' => 'Example: ANRI Headquarters / Information Service...',
        'label_description' => 'Description / Additional Info',
        'label_gallery' => 'Media Image / Office Photo',
        'hint_gallery' => 'Click or drag image here',
        'hint_gallery_sub' => 'Supports jpg, png, webp formats',
        
        'sidebar_title' => 'Page Settings',
        'label_order' => 'Order',
        'label_date' => 'Date',
        'label_subtitle' => 'Sub-title / Summary',
        'placeholder_subtitle' => 'Short summary...',
        
        'btn_save' => 'Save Contact',
        'btn_update' => 'Update Contact',
        'btn_cancel' => 'Cancel',
        'btn_edit' => 'Edit',

        // Delete modal
        'delete_title' => 'Delete Contact?',
        'delete_confirm' => 'Are you sure you want to delete :name?',
        'delete_yes' => 'Delete',
        'delete_no' => 'Cancel',

        // Empty state
        'empty' => 'No contact us data yet. Click "Add Contact" to add.',

        // Flash
        'flash' => [
            'added' => 'Contact us content added successfully.',
            'updated' => 'Contact us content updated successfully.',
            'deleted' => 'Contact us content deleted successfully.',
        ],

        // Custom fields
        'label_alamat' => 'Full Address',
        'placeholder_alamat' => 'Example: Jl. Ampera Raya No.7, Cilandak, Jakarta Selatan...',
        'label_jam_desc' => 'Operating Hours Description',
        'label_jam_list' => 'Operating Hours List',
        'btn_add_jam' => 'Add Schedule',
        'label_telepon' => 'Phone Number',
        'placeholder_telepon' => 'Example: (021) 7805851',
        'label_whatsapp' => 'WhatsApp Number',
        'placeholder_whatsapp' => 'Example: 6281234567890',
        'label_email' => 'Email Address',
        'placeholder_email' => 'Example: info@anri.go.id',
        'label_instagram' => 'Instagram (URL / Username)',
        'label_twitter' => 'Twitter / X (URL / Username)',
        'label_facebook' => 'Facebook (URL / Username)',
        'label_youtube' => 'YouTube (URL / Username)',
        'label_cards_title' => 'Service / Contact Cards List',
        'btn_add_card' => 'Add Card',

        // Dynamic sections & extra data
        'top_cards_title' => 'Main Information Cards / Highlight (Top Cards)',
        'top_cards_desc' => 'Add highlight cards (such as Location, Depot, Service) that appear at the top of the page',
        'btn_add_top_card' => 'Add Card',
        'top_card_item_prefix' => 'Highlight ',
        'btn_delete_top_card' => 'Delete Card',
        'label_top_card_title' => 'Main Title / Number (Example: Bandung, 5 Days)',
        'placeholder_top_card_title' => 'Example: Bandung',
        'label_top_card_subtitle' => 'Label / Sub-title (Example: Strategic Location)',
        'placeholder_top_card_subtitle' => 'Example: Strategic Location',
        'label_choose_icon' => 'Choose Icon',
        'icon_map' => '📍 Location (Map Pin)',
        'icon_building' => '🏢 Building / Facility (Depot/Office)',
        'icon_clock' => '⏰ Clock / Time (Service/Duration)',
        'icon_phone' => '📞 Phone (Call Center)',
        'icon_message' => '💬 Message / Chat',
        'icon_mail' => '📧 Email / Mail',
        'icon_globe' => '🌐 Website / Portal',

        'alamat_section_header' => 'Contact & Address Information',
        'alamat_section_desc' => 'Complete the office address, email, phone, and social media data',
        'label_section_title_guest' => 'Section Title (Displayed on Guest Page)',
        'placeholder_alamat_section_title' => 'Contact & Address Information',
        'placeholder_instagram' => 'https://instagram.com/...',
        'placeholder_twitter' => 'https://twitter.com/...',
        'placeholder_facebook' => 'https://facebook.com/...',
        'placeholder_youtube' => 'https://youtube.com/...',

        'jam_section_header' => 'Schedule / Operating Hours',
        'jam_section_desc' => 'Set service working days and hours',
        'placeholder_jam_section_title' => 'Schedule & Operating Hours',
        'label_jam_hari' => 'Working Days',
        'placeholder_jam_hari' => 'Monday - Thursday',
        'label_jam_waktu' => 'Operating Hours',
        'placeholder_jam_waktu' => '08:00 - 15:00 WIB',
        'btn_delete_jam' => 'Delete Schedule',

        'cards_section_desc' => 'Add service information cards, complaints, or special contact channels',
        'placeholder_cards_section_title' => 'Service & Complaint Channels',
        'card_item_prefix' => 'Card ',
        'btn_delete_card' => 'Delete Card',
        'label_card_title' => 'Service / Contact Title',
        'placeholder_card_title' => 'Example: Information Service',
        'label_card_subtitle' => 'Sub-title / Short Description',
        'placeholder_card_subtitle' => 'Example: For archive-related inquiries',
        'icon_phone_card' => '📞 Phone (Service / Call Center)',
        'icon_message_card' => '💬 Message / Chat (Complaint / Consultation)',
        'icon_mail_card' => '📧 Email (Mail / Support)',
        'icon_map_card' => '📍 Location (Address / Branch)',
        'icon_clock_card' => '⏰ Clock (Operating Hours)',
        'icon_globe_card' => '🌐 Website (Online Portal)',
    ],


    'reports' => [
        // Kunjungan
        'kunjungan_title' => 'Visit Registration Report',
        'kunjungan_subtitle' => 'Monitoring Visit Registration Service data and comparative charts of visit purposes and trends.',
        'filter_start' => 'Start:',
        'filter_end' => 'End:',
        'btn_filter' => 'Filter',
        'btn_reset' => 'Reset',
        'btn_cancel' => 'Cancel',
        'total_visitor' => 'Total Visitors',
        'total_visitor_sub' => 'All registered visit participants',
        'purpose_edukasi' => 'Educational Purpose',
        'purpose_edukasi_sub' => 'School & campus visits',
        'purpose_penelitian' => 'Research Purpose',
        'purpose_penelitian_sub' => 'Research & literature study',
        'purpose_kunker' => 'Work Visit',
        'purpose_kunker_sub' => 'Comparative study & institutions',
        'chart_purpose_title' => 'Visit Purposes',
        'chart_purpose_sub' => 'Participant distribution by purpose',
        'chart_trend_title' => 'Daily Visit Trend',
        'chart_trend_sub' => 'Number of visit participants over the last 30 days',
        'chart_trend_approved_title' => 'Approved Visit Trend',
        'chart_trend_approved_sub' => 'Chart of approved visit participants',
        'chart_trend_rejected_title' => 'Rejected Visit Trend',
        'chart_trend_rejected_sub' => 'Chart of rejected visit participants',
        'table_kunjungan_title' => 'Visit Registration History',
        'table_kunjungan_sub' => 'Complete list of visit service applicants',
        'col_no' => 'No',
        'col_name_inst' => 'Name & Institution',
        'col_contact' => 'Contact',
        'col_date_time' => 'Date & Time',
        'col_count' => 'Count',
        'col_purpose' => 'Purpose',
        'col_action' => 'Action',
        'time_pagi' => 'MORNING',
        'time_siang' => 'AFTERNOON',
        'label_org' => 'pax',
        'label_org_full' => 'People',
        'btn_detail' => 'View Details',
        'btn_download' => 'Download Letter',
        'empty_kunjungan' => 'No visit registration data yet.',
        'modal_kunjungan_title' => 'Visit Registration Details',
        'modal_name' => 'Full Name',
        'modal_email' => 'Email',
        'modal_phone' => 'Phone Number',
        'modal_inst' => 'Institution',
        'modal_position' => 'Position',
        'modal_visit_date' => 'Visit Date',
        'modal_visit_time' => 'Time Session',
        'modal_visitor_count' => 'Number of Visitors',
        'modal_purpose' => 'Purpose',
        'modal_form_data' => 'Additional Form Data',
        'status' => 'Status',
        'status_approved' => 'Approved',
        'status_rejected' => 'Rejected',
        'status_pending' => 'Pending',
        'remarks' => 'Remarks',
        'file_attachment' => 'File Attachment',
        'view_file' => 'View File',
        'btn_close' => 'Close',

        // Pengunjung
        'pengunjung_title' => 'Website Visitor Monitoring',
        'pengunjung_subtitle' => 'Monitoring website visitor statistics based on accessed pages.',
        'pengunjung_index_subtitle' => 'Select the type of visitor metrics you want to analyze.',
        'page_views_title' => 'Total Page Views',
        'page_views_desc' => 'Total pages accessed by visitors. 1 visitor can generate multiple page views.',
        'unique_visitors_title' => 'Total Unique Visitors',
        'unique_visitors_desc' => 'Calculated based on unique visitor IPs per day. Indicates the actual number of individuals.',
        'btn_view_detail' => 'View Details',
        'btn_back' => 'Back',
        'page_views_header_title' => 'Visitor Monitoring (Page Views)',
        'page_views_header_desc' => 'Monitoring total website page visit statistics.',
        'unique_visitors_header_title' => 'Visitor Monitoring (Unique Visitors)',
        'unique_visitors_header_desc' => 'Monitoring unique visitor statistics (by IP) per day.',
        'chart_unique_title' => 'Unique Visitors Report',
        'chart_unique_series' => 'Visitor Count',
        'chart_unique_unit' => 'Visitors',
        'preset_today' => 'Today',
        'preset_week' => '7 Days',
        'preset_month' => '30 Days',
        'preset_year' => '1 Year',
        'preset_custom' => 'Custom',
        'total_views' => 'Total Page Views',
        'page_breakdown' => 'Page Breakdown',
        'chart_views_title' => 'Page Views Chart',
        'chart_views_sub' => 'Access distribution based on website menu categories',
        'table_pengunjung_title' => 'Page Access History (Visitor Log)',
        'table_pengunjung_sub' => 'Real-time record of website page access activity',
        'col_ip' => 'IP Address',
        'col_path' => 'Page (Path)',
        'col_device' => 'Device / Browser',
        'col_access_time' => 'Access Time',
        'empty_pengunjung' => 'No visitor log data yet.',

        // Konsultasi
        'konsultasi_title' => 'Archival Consultation Report',
        'konsultasi_subtitle' => 'Monitoring Archival Consultation Service applications from the public and institutions.',
        'total_consultation' => 'Total Consultation Applications',
        'total_consultation_sub' => 'All registered consultation history',
        'table_konsultasi_title' => 'Archival Consultation Application History',
        'table_konsultasi_sub' => 'Complete list of archival consultation and guidance applicants',
        'col_topic' => 'Consultation Topic',
        'col_submit_date' => 'Submission Date',
        'empty_konsultasi' => 'No archival consultation application data yet.',
        'modal_konsultasi_title' => 'Consultation Application Details',
        'modal_topic' => 'Consultation Topic',
        'modal_submit_date' => 'Submission Date',
        'btn_download_attachment' => 'Download Attachment',
        'status_replied' => 'Replied',
        'status_waiting' => 'Waiting',
        'btn_reply' => 'Reply Message',
        'btn_delete' => 'Delete Data',
        'msg_replied_already' => 'Reply message has already been sent.',
        'msg_reply_success' => 'Reply message sent successfully to the applicant\'s email.',
        'msg_reply_fail' => 'Failed to send reply message. Make sure the email server settings are correct.',
        'msg_del_no_access' => 'You do not have access rights to delete consultation data.',
        'msg_del_success' => 'Consultation data deleted successfully.',
        'swal_success' => 'Success!',
        'swal_fail' => 'Failed!',
        'swal_error' => 'Error!',
        'swal_error_sys' => 'A system error occurred.',
        'swal_del_title' => 'Delete Data?',
        'swal_del_text' => 'This consultation data will be deleted permanently.',
        'swal_del_confirm' => 'Yes, Delete!',
        'swal_cancel' => 'Cancel',
        'swal_deleted' => 'Deleted!',
        'reply_modal_title' => 'Reply to Consultation',
        'reply_modal_to' => 'To',
        'reply_modal_msg' => 'Reply Message',
        'reply_modal_placeholder' => 'Type your reply message here...',
        'btn_send_reply' => 'Send Reply',
        'btn_sending' => 'Sending...',

        // Online
        'online_title' => 'Online User Report',
        'online_subtitle' => 'Monitoring online user history, active time, and website page access activity.',
        'online_stat_realtime' => 'Currently Online Users',
        'online_stat_realtime_sub' => 'Active in last 5 minutes',
        'online_stat_active' => 'Total Active Users',
        'online_stat_active_sub' => 'Throughout selected date',
        'online_stat_peak' => 'Peak Online Hour',
        'online_stat_peak_sub' => 'Busiest time',
        'online_stat_avg' => 'Average Online / Hour',
        'online_stat_avg_sub' => 'Users per active hour',
        'chart_online_title' => 'Hourly Online Users History Chart',
        'chart_online_sub' => 'Distribution of active users in each hour on the selected date',
        'table_realtime_title' => 'Online Users List (Real-time)',
        'table_realtime_sub' => 'Last activity status of all system users',
        'table_activity_title' => 'User Activity History',
        'table_activity_sub' => 'Summary of total page access and user interaction throughout the selected date',
        'my_profile_title' => 'Personal Data',
        'my_profile_sub' => 'Your account information and current online activity status',
        'my_activity_title' => 'My Activity History',
        'my_activity_sub' => 'Summary of your total page access and interactions throughout the selected date',
        'col_user' => 'User',
        'col_role' => 'Role',
        'col_last_activity' => 'Last Activity',
        'col_page_views' => 'Page Views Count',
        'col_last_access' => 'Last Access',
        'empty_online' => 'No online users currently.',
        'empty_activity' => 'No user activity on this date.',
        'preset_yesterday' => 'Yesterday',
        'filter_date' => 'Date:',
        'online_stat_peak_desc' => 'Busiest time with the highest number of active online users in an hour',
        'peak_active_users' => 'Peak<br>Active Users',
        'peak_hours_list' => 'Recorded Peak Hours List (:count Hours)',
        'empty_peak_hours' => 'No peak hour data recorded for this date yet.',
        'tooltip_active_users' => 'Active Users',
        'user_label' => 'User',
    ],

    'datatable' => [
        'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
        'info_empty' => 'No entries',
        'info_filtered' => '(filtered from _MAX_ total entries)',
        'zero_records' => 'No matching records found',
        'search_placeholder' => 'Search...',
        'paginate' => [
            'first' => 'First',
            'last' => 'Last',
            'next' => 'Next',
            'previous' => 'Previous',
        ],
    ],
];
