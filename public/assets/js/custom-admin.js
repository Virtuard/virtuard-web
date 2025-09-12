/**
 * Custom Admin JavaScript
 * Provides drag-to-reorder functionality for gallery images
 */

(function($) {
    'use strict';

    // Gallery Drag and Drop Functionality
    class GalleryDragDrop {
        constructor() {
            this.init();
        }

        init() {
            this.initializeSortable();
            this.bindEvents();
        }

        // Initialize sortable functionality for gallery items
        initializeSortable() {
            $('.dungdt-upload-multiple .attach-demo').each(function() {
                const container = $(this);
                
                // Make container sortable using HTML5 drag and drop
                container.sortable({
                    items: '.image-item',
                    cursor: 'move',
                    opacity: 0.8,
                    tolerance: 'pointer',
                    placeholder: 'image-item-placeholder',
                    start: function(event, ui) {
                        ui.placeholder.height(ui.item.height());
                        ui.placeholder.addClass('sortable-placeholder');
                    },
                    update: function(event, ui) {
                        // Update the hidden input with new order
                        const galleryContainer = container.closest('.dungdt-upload-multiple');
                        updateGalleryOrder(galleryContainer);
                    }
                });

                // Add drag handle styling
                container.find('.image-item').each(function() {
                    $(this).attr('draggable', true);
                    $(this).addClass('sortable-item');
                });
            });
        }

        // Bind additional events
        bindEvents() {
            const self = this;

            // Reinitialize sortable when new images are added
            $(document).on('DOMNodeInserted', '.dungdt-upload-multiple .attach-demo', function() {
                self.initializeSortable();
            });

            // Add drag handle visual indicator
            $(document).on('mouseenter', '.dungdt-upload-multiple .image-item', function() {
                $(this).addClass('drag-hover');
            });

            $(document).on('mouseleave', '.dungdt-upload-multiple .image-item', function() {
                $(this).removeClass('drag-hover');
            });
        }
    }

    // Update gallery order in hidden input
    function updateGalleryOrder(galleryContainer) {
        const imageItems = galleryContainer.find('.attach-demo .image-item');
        const ids = [];

        imageItems.each(function() {
            const editBtn = $(this).find('.edit-multiple');
            const id = editBtn.attr('data-id');
            if (id) {
                ids.push(id);
            }
        });

        // Update the hidden input value
        const hiddenInput = galleryContainer.find('input[type="hidden"]');
        hiddenInput.val(ids.join(','));

        // Trigger change event for any listeners
        hiddenInput.trigger('change');
    }

    // HTML5 Drag and Drop Implementation (fallback for jQuery UI)
    class HTML5DragDrop {
        constructor() {
            this.draggedElement = null;
            this.init();
        }

        init() {
            this.bindDragEvents();
        }

        bindDragEvents() {
            const self = this;

            // Make image items draggable
            $(document).on('dragstart', '.dungdt-upload-multiple .image-item', function(e) {
                self.draggedElement = this;
                $(this).addClass('dragging');
                
                // Set drag effect
                e.originalEvent.dataTransfer.effectAllowed = 'move';
                e.originalEvent.dataTransfer.setData('text/html', this.outerHTML);
            });

            $(document).on('dragend', '.dungdt-upload-multiple .image-item', function(e) {
                $(this).removeClass('dragging');
                $('.image-item').removeClass('drag-over');
                self.draggedElement = null;
            });

            $(document).on('dragover', '.dungdt-upload-multiple .image-item', function(e) {
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'move';
                
                if (this !== self.draggedElement) {
                    $(this).addClass('drag-over');
                }
            });

            $(document).on('dragleave', '.dungdt-upload-multiple .image-item', function(e) {
                $(this).removeClass('drag-over');
            });

            $(document).on('drop', '.dungdt-upload-multiple .image-item', function(e) {
                e.preventDefault();
                
                if (this !== self.draggedElement) {
                    const container = $(this).closest('.attach-demo');
                    const draggedEl = $(self.draggedElement);
                    const targetEl = $(this);
                    
                    // Determine drop position
                    const rect = this.getBoundingClientRect();
                    const midpoint = rect.left + rect.width / 2;
                    const dropX = e.originalEvent.clientX;
                    
                    if (dropX < midpoint) {
                        // Insert before
                        targetEl.before(draggedEl);
                    } else {
                        // Insert after
                        targetEl.after(draggedEl);
                    }
                    
                    // Update gallery order
                    const galleryContainer = container.closest('.dungdt-upload-multiple');
                    updateGalleryOrder(galleryContainer);
                }
                
                $(this).removeClass('drag-over');
            });
        }
    }

    // Enhanced gallery upload functionality
    function enhanceGalleryUpload() {
        // Override the existing gallery upload click handler
        $('.dungdt-upload-multiple').off('click', '.btn-field-upload');
        
        $('.dungdt-upload-multiple').on('click', '.btn-field-upload', function() {
            let p = $(this).closest('.dungdt-upload-multiple');
            uploaderModal.show({
                multiple: true,
                file_type: 'image',
                onSelect: function (files) {
                    if (typeof files != 'undefined' && files.length) {
                        var ids = [];
                        var html = '';
                        p.addClass('active');
                        
                        for (var i = 0; i < files.length; i++) {
                            let path = (files[i].edit_path !== undefined) ? files[i].edit_path : files[i].max_large_size;
                            ids.push(files[i].id);
                            html += '<div class="image-item sortable-item" draggable="true">' +
                                '<div class="inner">' +
                                '<a class="edit-img btn btn-sm btn-primary edit-multiple" data-id="'+files[i].id+'" data-file="'+path+'"><i class="fa fa-edit"></i></a>' +
                                '<span class="delete btn btn-sm btn-danger"><i class="fa fa-trash"></i></span>' +
                                '<div class="drag-handle"><i class="fa fa-arrows"></i></div>' +
                                '<img loading="lazy" class="image-responsive image-preview w-100" src="' + files[i].thumb_size + '"/>' +
                                '</div>' +
                                '</div>';
                        }
                        
                        p.find('.attach-demo').append(html);
                        
                        // Get existing IDs and append new ones
                        var existingIds = p.find('input').val();
                        var allIds = existingIds ? existingIds.split(',').concat(ids) : ids;
                        p.find('input').val(allIds.join(','));
                        
                        // Reinitialize drag and drop for new items
                        initializeDragAndDrop();
                    }
                },
            });
        });
    }

    // Initialize drag and drop functionality
    function initializeDragAndDrop() {
        // Check if jQuery UI sortable is available
        if ($.fn.sortable) {
            new GalleryDragDrop();
        } else {
            // Use HTML5 drag and drop as fallback
            new HTML5DragDrop();
        }
    }

    // Add CSS styles for drag and drop
    function addDragDropStyles() {
        const styles = `
            <style id="gallery-drag-drop-styles">
                .dungdt-upload-multiple .image-item {
                    cursor: move;
                    transition: all 0.2s ease;
                    position: relative;
                }
                
                .dungdt-upload-multiple .image-item:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                }
                
                .dungdt-upload-multiple .image-item.dragging {
                    opacity: 0.5;
                    transform: rotate(5deg);
                }
                
                .dungdt-upload-multiple .image-item.drag-over {
                    border: 2px dashed #007bff;
                    background-color: rgba(0,123,255,0.1);
                }
                
                .dungdt-upload-multiple .image-item.drag-hover {
                    border: 1px solid #007bff;
                }
                
                .image-item-placeholder {
                    border: 2px dashed #ccc;
                    background-color: #f8f9fa;
                    height: 160px;
                    margin-bottom: 10px;
                }
                
                .sortable-placeholder {
                    border: 2px dashed #007bff !important;
                    background-color: rgba(0,123,255,0.1) !important;
                }
                
                .drag-handle {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: rgba(0,0,0,0.7);
                    color: white;
                    padding: 5px;
                    border-radius: 3px;
                    opacity: 0;
                    transition: opacity 0.2s ease;
                    pointer-events: none;
                    z-index: 10;
                }
                
                .dungdt-upload-multiple .image-item:hover .drag-handle {
                    opacity: 1;
                }
                
                .dungdt-upload-multiple .attach-demo {
                    min-height: 50px;
                }
            </style>
        `;
        
        if (!$('#gallery-drag-drop-styles').length) {
            $('head').append(styles);
        }
    }

    // Initialize everything when document is ready
    $(document).ready(function() {
        // Add CSS styles
        addDragDropStyles();
        
        // Initialize drag and drop
        initializeDragAndDrop();
        
        // Enhance gallery upload functionality
        enhanceGalleryUpload();
        
        // Add drag handles to existing gallery items
        $('.dungdt-upload-multiple .image-item').each(function() {
            if (!$(this).find('.drag-handle').length) {
                $(this).find('.inner').append('<div class="drag-handle"><i class="fa fa-arrows"></i></div>');
            }
            $(this).attr('draggable', true);
            $(this).addClass('sortable-item');
        });

        console.log('Custom Admin JS: Gallery drag-to-reorder functionality initialized');
    });

    // Export functions for global access
    window.GalleryDragDrop = {
        reinitialize: initializeDragAndDrop,
        updateOrder: updateGalleryOrder
    };

})(jQuery);