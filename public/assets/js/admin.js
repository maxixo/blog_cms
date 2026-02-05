document.addEventListener('DOMContentLoaded', function () {
    console.log('Admin JS loaded');
    
    // Initialize all admin functionality
    initFormHandling();
    initDeleteConfirmation();
    initImageUpload();
    initCharacterCounters();
    initTableSearch();
    initTableFilters();
});

/**
 * Form handling with loading states
 */
function initFormHandling() {
    const forms = document.querySelectorAll('form[data-loading]');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            const loadingText = submitBtn?.getAttribute('data-loading-text') || 'Loading...';
            
            if (submitBtn) {
                submitBtn.classList.add('is-loading');
                submitBtn.disabled = true;
                submitBtn.textContent = loadingText;
            }
        });
    });
}

/**
 * Delete confirmation dialogs
 */
function initDeleteConfirmation() {
    const deleteForms = document.querySelectorAll('.delete-form');
    
    deleteForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const message = this.getAttribute('data-confirm') || 
                          'Are you sure you want to delete this? This action cannot be undone.';
            const confirmed = confirm(message);
            
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Image upload with preview
 */
function buildImagePlaceholderHtml() {
    return `
        <div class="image-upload-placeholder">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            <p>Click to upload or drag and drop</p>
            <small>JPG, PNG, GIF, WebP (max 5MB)</small>
        </div>
    `;
}

function buildImagePreviewHtml(imageUrl, inputId) {
    const targetAttr = inputId ? ` data-target="${inputId}"` : '';
    return `
        <img src="${imageUrl}" alt="Image Preview">
        <button type="button" class="btn-remove-image"${targetAttr}>
            <span aria-hidden="true">&times;</span>
        </button>
    `;
}

function bindRemoveButton(previewContainer, input) {
    const removeBtn = previewContainer?.querySelector('.btn-remove-image');
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            removeImage(input, previewContainer);
        });
    }
}

function setImagePreview(previewContainer, input, html) {
    if (!previewContainer) {
        return;
    }
    previewContainer.innerHTML = html;
    bindRemoveButton(previewContainer, input);
}

function initImageUpload() {
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(function(input) {
        const uploadArea = input.closest('.image-upload-area');
        const previewContainer = uploadArea?.querySelector('.image-preview');
        const existingUrl = uploadArea?.getAttribute('data-existing-url') || '';
        if (previewContainer && existingUrl) {
            previewContainer.dataset.existingUrl = existingUrl;
        }
        if (uploadArea) {
            uploadArea.addEventListener('click', function(event) {
                if (event.target === input) {
                    return;
                }
                if (event.target.closest('.btn-remove-image')) {
                    return;
                }
                if (event.target.closest('.image-upload-trigger')) {
                    return;
                }
                if (input.disabled) {
                    return;
                }
                input.click();
            });
        }

        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const removeInput = uploadArea?.querySelector('input[name="remove_featured_image"]');
            if (removeInput) {
                removeInput.value = '0';
            }
            
            // Validate file size (5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('File size must be less than 5MB.');
                input.value = '';
                return;
            }
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
                input.value = '';
                return;
            }
            
            // Show preview
            if (previewContainer) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    setImagePreview(previewContainer, input, buildImagePreviewHtml(event.target.result, input.id));
                };
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Bind existing remove buttons
    const existingRemoveBtns = document.querySelectorAll('.btn-remove-image');
    existingRemoveBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = targetId ? document.getElementById(targetId) : 
                         this.closest('.image-upload-area')?.querySelector('input[type="file"]');
            const previewContainer = this.closest('.image-preview');
            
            if (input && previewContainer) {
                removeImage(input, previewContainer);
            }
        });
    });
}

/**
 * Remove image
 */
function removeImage(input, previewContainer) {
    const confirmed = confirm('Are you sure you want to remove this image?');
    
    if (!confirmed) {
        return;
    }

    const uploadArea = input.closest('.image-upload-area');
    const removeInput = uploadArea?.querySelector('input[name="remove_featured_image"]');
    const existingUrl = previewContainer?.dataset?.existingUrl ||
        uploadArea?.getAttribute('data-existing-url') || '';
    const hasNewFile = input.files && input.files.length > 0;

    if (hasNewFile) {
        input.value = '';
        if (existingUrl) {
            setImagePreview(previewContainer, input, buildImagePreviewHtml(existingUrl, input.id));
        } else {
            setImagePreview(previewContainer, input, buildImagePlaceholderHtml());
        }
        if (removeInput) {
            removeInput.value = '0';
        }
        return;
    }

    if (removeInput && existingUrl) {
        removeInput.value = '1';
    }
    if (previewContainer) {
        previewContainer.dataset.existingUrl = '';
    }
    if (uploadArea && existingUrl) {
        uploadArea.setAttribute('data-existing-url', '');
    }

    input.value = '';
    setImagePreview(previewContainer, input, buildImagePlaceholderHtml());
}

/**
 * Character counters for textareas
 */
function initCharacterCounters() {
    const textareas = document.querySelectorAll('textarea[maxlength]');
    
    textareas.forEach(function(textarea) {
        const maxLength = parseInt(textarea.getAttribute('maxlength'));
        const counter = textarea.parentElement.querySelector('.form-text');
        
        if (counter && maxLength) {
            updateCharacterCounter(textarea, maxLength, counter);
            
            textarea.addEventListener('input', function() {
                updateCharacterCounter(this, maxLength, counter);
            });
        }
    });
}

/**
 * Update character counter
 */
function updateCharacterCounter(textarea, maxLength, counter) {
    const currentLength = textarea.value.length;
    counter.textContent = `${currentLength}/${maxLength} characters`;
    
    // Change color if near limit
    const percentage = (currentLength / maxLength) * 100;
    if (percentage >= 90) {
        counter.style.color = '#ef4444';
    } else if (percentage >= 75) {
        counter.style.color = '#f59e0b';
    } else {
        counter.style.color = '';
    }
}

/**
 * Table search functionality
 */
function initTableSearch() {
    const searchInput = document.getElementById('searchPosts');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        const table = document.getElementById('postsTable');
        
        if (!table) return;
        
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(function(row) {
            const title = row.querySelector('.post-title-cell strong')?.textContent.toLowerCase() || '';
            const author = row.querySelectorAll('td')[1]?.textContent.toLowerCase() || '';
            const category = row.querySelectorAll('td')[2]?.textContent.toLowerCase() || '';
            
            const matches = title.includes(searchTerm) || 
                           author.includes(searchTerm) || 
                           category.includes(searchTerm);
            
            row.style.display = matches ? '' : 'none';
        });
        
        // Show "no results" message if needed
        showNoResultsMessage(table, searchTerm);
    });
}

/**
 * Table filter functionality
 */
function initTableFilters() {
    const statusFilter = document.getElementById('statusFilter');
    if (!statusFilter) return;
    
    statusFilter.addEventListener('change', function(e) {
        const status = e.target.value;
        const table = document.getElementById('postsTable');
        
        if (!table) return;
        
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(function(row) {
            const badge = row.querySelector('.badge');
            const postStatus = badge?.textContent.toLowerCase() || '';
            
            if (status === 'all' || postStatus === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

/**
 * Show no results message
 */
function showNoResultsMessage(table, searchTerm) {
    const tbody = table.querySelector('tbody');
    const visibleRows = Array.from(tbody.querySelectorAll('tr')).filter(row => 
        row.style.display !== 'none'
    );
    
    let noResultsMsg = tbody.querySelector('.no-results-message');
    
    if (searchTerm && visibleRows.length === 0) {
        if (!noResultsMsg) {
            noResultsMsg = document.createElement('tr');
            noResultsMsg.className = 'no-results-message';
            noResultsMsg.innerHTML = `
                <td colspan="7" style="text-align: center; padding: 2rem; color: #6b7280;">
                    <p style="margin: 0;">No posts found matching "${escapeHtml(searchTerm)}"</p>
                </td>
            `;
            tbody.appendChild(noResultsMsg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Auto-save draft functionality (optional enhancement)
 */
let autoSaveTimeout;
function initAutoSave() {
    const form = document.getElementById('postForm');
    if (!form) return;
    
    const titleInput = document.getElementById('title');
    const contentTextarea = document.getElementById('content');
    
    if (!titleInput || !contentTextarea) return;
    
    // Save draft every 30 seconds after user stops typing
    const saveDraft = function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            const title = titleInput.value.trim();
            const content = tinymce.get('content')?.getContent() || contentTextarea.value;
            
            if (title || content) {
                // Auto-save logic would go here
                // This could be implemented with AJAX
                console.log('Auto-saving draft...');
            }
        }, 30000);
    };
    
    titleInput.addEventListener('input', saveDraft);
    
    // For TinyMCE
    if (typeof tinymce !== 'undefined') {
        tinymce.get('content')?.on('keyup', saveDraft);
        tinymce.get('content')?.on('change', saveDraft);
    }
}

/**
 * Initialize slug generation from title
 */
function initSlugGeneration() {
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    if (titleInput && slugInput && !slugInput.value) {
        titleInput.addEventListener('input', function() {
            const title = this.value.trim();
            if (title && !slugInput.getAttribute('data-manual-edit')) {
                slugInput.value = generateSlug(title);
            }
        });
        
        slugInput.addEventListener('input', function() {
            this.setAttribute('data-manual-edit', 'true');
        });
    }
}

/**
 * Generate URL-friendly slug
 */
function generateSlug(text) {
    return text
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

/**
 * Initialize tooltips
 */
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[title]');
    
    tooltipElements.forEach(function(element) {
        element.addEventListener('mouseenter', function() {
            // Could implement custom tooltips here
            // For now, relying on browser default tooltips
        });
    });
}

/**
 * Initialize all helpers
 */
document.addEventListener('DOMContentLoaded', function() {
    initTooltips();
    initSlugGeneration();
    initAutoSave();
});
