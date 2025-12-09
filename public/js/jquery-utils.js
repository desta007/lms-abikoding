/**
 * jQuery Utilities for LMS Application
 * Provides reusable functions for button loading states and AJAX search
 */

(function($) {
    'use strict';

    // Loading spinner HTML
    const loadingSpinner = '<svg class="animate-spin h-5 w-5 text-white inline-block ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    /**
     * Show loading state on button
     * @param {jQuery} $button - The button element
     * @param {string} originalText - Original button text
     */
    function showButtonLoading($button, originalText) {
        // Get original text from button-text span or button itself
        const $buttonText = $button.find('.button-text');
        if ($buttonText.length) {
            $button.data('original-text', originalText || $buttonText.text());
            $buttonText.html(originalText || $buttonText.text());
        } else {
            $button.data('original-text', originalText || $button.text());
        }
        
        $button.prop('disabled', true);
        $button.css('opacity', '0.7');
        $button.css('cursor', 'not-allowed');
        
        // Add loading spinner
        if (!$button.find('.loading-spinner').length) {
            const spinner = $(loadingSpinner).addClass('loading-spinner');
            if ($buttonText.length) {
                $buttonText.append(spinner);
            } else {
                $button.append(spinner);
            }
        }
    }

    /**
     * Hide loading state on button
     * @param {jQuery} $button - The button element
     */
    function hideButtonLoading($button) {
        $button.prop('disabled', false);
        $button.css('opacity', '1');
        $button.css('cursor', 'pointer');
        
        // Remove loading spinner
        $button.find('.loading-spinner').remove();
        
        // Restore original text
        const originalText = $button.data('original-text');
        if (originalText) {
            const $buttonText = $button.find('.button-text');
            if ($buttonText.length) {
                $buttonText.text(originalText);
            } else {
                $button.html(originalText);
            }
        }
    }

    /**
     * Initialize button loading states for all forms
     */
    function initFormButtonLoading() {
        // Handle all form submissions
        $('form').on('submit', function(e) {
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');
            
            if ($submitButton.length && !$form.data('no-loading')) {
                showButtonLoading($submitButton);
            }
        });

        // Handle button clicks (for buttons that trigger actions)
        $('button[data-loading="true"]').on('click', function() {
            const $button = $(this);
            if (!$button.prop('disabled')) {
                showButtonLoading($button);
            }
        });
    }

    /**
     * Initialize AJAX search functionality
     */
    function initAjaxSearch() {
        // Search forms with data-ajax-search attribute
        $('form[data-ajax-search="true"]').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');
            const $searchInput = $form.find('input[name="search"]');
            const $resultsContainer = $($form.data('results-container') || '#courses-container');
            const $loadingIndicator = $($form.data('loading-indicator') || '.search-loading');
            const formAction = $form.attr('action') || window.location.href;
            const formMethod = $form.attr('method') || 'GET';

            // Show loading state
            if ($submitButton.length) {
                showButtonLoading($submitButton);
            }
            if ($loadingIndicator.length) {
                $loadingIndicator.show();
            }

            // Serialize form data
            const formData = $form.serialize();

            // Make AJAX request
            $.ajax({
                url: formAction,
                type: formMethod,
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    // Handle different response types
                    let htmlContent = '';
                    
                    if (typeof response === 'string') {
                        // Response is HTML string - extract only the courses container content
                        const $response = $('<div>').html(response);
                        
                        // Try to find the courses container wrapper in the response
                        const containerSelector = $form.data('results-container') || '#courses-container-wrapper';
                        const foundContainer = $response.find(containerSelector);
                        
                        if (foundContainer.length) {
                            // Get only the inner HTML of the container (not the container itself)
                            htmlContent = foundContainer.html();
                        } else {
                            // If no wrapper found, check if response is already the courses list content
                            // (This happens when controller returns partial view directly)
                            const coursesContainer = $response.find('#courses-container');
                            if (coursesContainer.length) {
                                // Response contains courses container, get its parent wrapper content
                                htmlContent = coursesContainer.parent().html();
                            } else {
                                // Response might be the courses list directly
                                htmlContent = $response.html();
                            }
                        }
                    } else if (response && response.html) {
                        // Response is JSON with html property
                        // The html should be the courses list content directly
                        htmlContent = response.html;
                    } else if (response && response.data && response.data.html) {
                        // Nested JSON response
                        htmlContent = response.data.html;
                    }

                    if (htmlContent) {
                        // Replace only the courses container wrapper content
                        $resultsContainer.html(htmlContent);
                        
                        // Re-initialize view toggle buttons after update
                        updateViewToggleButtons();
                        
                        // Update courses count if element exists
                        const $response = typeof response === 'string' ? $('<div>').html(response) : $('<div>').html(response.html || response.data?.html || '');
                        const newCount = $response.find('#courses-count').text();
                        if (newCount && $('#courses-count').length) {
                            $('#courses-count').text(newCount);
                        } else {
                            // Try to extract count from pagination
                            const paginationText = $response.find('.pagination').text() || '';
                            const match = paginationText.match(/(\d+)\s+kursus/);
                            if (match && $('#courses-count').length) {
                                $('#courses-count').text(match[1] + ' kursus tersedia');
                            }
                        }
                    }

                    // Update URL without reload
                    const newUrl = formAction + (formData ? '?' + formData : '');
                    window.history.pushState({path: newUrl}, '', newUrl);
                },
                error: function(xhr) {
                    console.error('Search error:', xhr);
                    // Show error message
                    if ($resultsContainer.length) {
                        $resultsContainer.html(
                            '<div class="text-center py-8 text-red-600">' +
                            'Terjadi kesalahan saat melakukan pencarian. Silakan coba lagi.' +
                            '</div>'
                        );
                    }
                },
                complete: function() {
                    // Hide loading state
                    if ($submitButton.length) {
                        hideButtonLoading($submitButton);
                    }
                    if ($loadingIndicator.length) {
                        $loadingIndicator.hide();
                    }
                }
            });
        });

        // Real-time search (debounced) for search inputs
        let searchTimeout;
        $('input[name="search"][data-live-search="true"]').on('input', function() {
            const $input = $(this);
            const $form = $input.closest('form[data-ajax-search="true"]');
            
            if (!$form.length) return;

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                $form.submit();
            }, 500); // Wait 500ms after user stops typing
        });
    }

    /**
     * Initialize filter forms with AJAX
     */
    function initAjaxFilters() {
        // Filter forms with data-ajax-filter attribute
        $('form[data-ajax-filter="true"]').on('change', 'select, input[type="checkbox"], input[type="radio"]', function() {
            const $form = $(this).closest('form');
            $form.submit();
        });
    }

    /**
     * Initialize delete button confirmations with loading
     */
    function initDeleteButtons() {
        $('form[data-confirm-delete="true"]').on('submit', function(e) {
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');
            
            if (!confirm($form.data('confirm-message') || 'Apakah Anda yakin?')) {
                e.preventDefault();
                return false;
            }

            if ($submitButton.length) {
                showButtonLoading($submitButton);
            }
        });
    }

    /**
     * Update view toggle buttons state
     */
    function updateViewToggleButtons() {
        const urlParams = new URLSearchParams(window.location.search);
        const currentView = urlParams.get('view') || 'grid';
        const gridBtn = $('#grid-view-btn');
        const listBtn = $('#list-view-btn');
        
        if (gridBtn.length && listBtn.length) {
            if (currentView === 'grid') {
                gridBtn.addClass('bg-white shadow-sm');
                listBtn.removeClass('bg-white shadow-sm');
            } else {
                listBtn.addClass('bg-white shadow-sm');
                gridBtn.removeClass('bg-white shadow-sm');
            }
        }
    }

    /**
     * Initialize all utilities when DOM is ready
     */
    $(document).ready(function() {
        initFormButtonLoading();
        initAjaxSearch();
        initAjaxFilters();
        initDeleteButtons();
        updateViewToggleButtons();

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.path) {
                window.location.href = e.state.path;
            }
        });
    });

    // Expose utility functions globally
    window.ButtonLoading = {
        show: showButtonLoading,
        hide: hideButtonLoading
    };

})(jQuery);

