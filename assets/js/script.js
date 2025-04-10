jQuery(document).ready(function($) {
  
    
    const $hub = $('.fintech-resource-hub');
    const $typeFilter = $('#type-filter');
    const $topicFilter = $('#topic-filter');
    const $searchFilter = $('#search-filter');
    const $resourcesGrid = $('.resources-grid');
    const $loadMoreContainer = $('.load-more-container');
    const $loadMoreButton = $('.load-more-button');
    const $noResults = $('.no-results-message');


   
    const config = {
        perPage: 6
    };



    // Function to create resource card HTML
    function createResourceCard(resource) {
        return `
            <div class="resource-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
                 data-type="${resource.type || ''}"
                 data-topic="${resource.topic || ''}"
                 data-title="${resource.title || ''}"
                 data-content="${resource.excerpt || ''}">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-3 py-1 text-sm font-semibold text-blue-700 bg-blue-100 rounded-full">
                            ${resource.type || 'Unknown Type'}
                        </span>
                        <span class="px-3 py-1 text-sm font-semibold text-green-700 bg-green-100 rounded-full">
                            ${resource.topic || 'Unknown Topic'}
                        </span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3 line-clamp-2">
                        ${resource.title || 'Untitled'}
                    </h3>
                    <div class="text-gray-600 mb-4 line-clamp-3 text-sm leading-relaxed">
                        ${resource.excerpt || 'No description available'}
                    </div>
                    <div class="flex items-center justify-between mt-auto">
                        ${resource.reading_time ? `
                            <span class="text-sm text-gray-500">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                ${resource.reading_time} min read
                            </span>
                        ` : ''}
                        ${resource.external_link ? `
                            <a href="${resource.external_link}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="text-blue-600 hover:text-blue-800 font-medium inline-flex items-center">
                                View Resource
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    // Function to load resources
    function loadResources(reset = true) {
        const currentPage = parseInt($hub.data('page')) || 1;
        const type = $typeFilter.val() || '';
        const topic = $topicFilter.val() || '';
        const search = $searchFilter.val() || '';

        $loadMoreContainer.addClass('hidden');

        if (reset) {
            $resourcesGrid.empty();
            $hub.data('page', 1);
        }

        // Build the REST API URL - ensure it uses the same protocol as the current page
        const protocol = window.location.protocol;
        const apiUrl = `${protocol}${fintechResourceHub.apiUrl}/resources`;
        const params = new URLSearchParams({
            page: reset ? 1 : currentPage,
            per_page: config.perPage,
            type,
            topic,
            search
        });

        // Make the API call
        fetch(`${apiUrl}?${params.toString()}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-WP-Nonce': fintechResourceHub.nonce,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Cache-Control': 'no-cache'
            },
            mode: 'cors'
        })
        .then(response => {
           if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const resources = data.resources || [];
            const totalPages = data.total_pages || 1;
            const currentPage = data.current_page || 1;

            $hub.data('total-pages', totalPages);
            $hub.data('page', currentPage);

            if (resources.length > 0) {
                resources.forEach(function(resource) {
                    $resourcesGrid.append(createResourceCard(resource));
                });

                // Only show load more if there are more pages and we have resources
                if (currentPage < totalPages) {
                    $loadMoreContainer.removeClass('hidden');
                }

                $noResults.addClass('hidden');
            } else {
                if (reset) {
                    $resourcesGrid.empty();
                    $noResults.removeClass('hidden');
                }
                $loadMoreContainer.addClass('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading resources:', error);
            $noResults.removeClass('hidden')
                     .find('p')
                     .text('Error loading resources. Please try again later.');
            $loadMoreContainer.addClass('hidden');
        });
    }

    // Initialize event listeners
    function initializeEventListeners() {
       $typeFilter.on('change', function() {
            loadResources();
            updateURL();
        });

        $topicFilter.on('change', function() {
            loadResources();
            updateURL();
        });

        let searchTimer;
        $searchFilter.on('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                loadResources();
                updateURL();
            }, 500);
        });

        $loadMoreButton.on('click', function() {
            const currentPage = parseInt($hub.data('page')) || 1;
            $hub.data('page', currentPage + 1);
            loadResources(false);
        });
    }

    // Initialize the resource hub
    function initialize() {
        
        if (!$hub.length) {
            console.error('Resource hub container not found');
            return;
        }

        if (typeof fintechResourceHub === 'undefined' || !fintechResourceHub.apiUrl) {
            console.error('WordPress data not properly initialized:', {
                fintechResourceHub: typeof fintechResourceHub,
                apiUrl: fintechResourceHub?.apiUrl
            });
            return;
        }

        // Initialize filters from URL parameters first
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('type')) $typeFilter.val(urlParams.get('type'));
        if (urlParams.has('topic')) $topicFilter.val(urlParams.get('topic'));
        if (urlParams.has('search')) $searchFilter.val(urlParams.get('search'));

        // Then initialize event listeners
        initializeEventListeners();

        // Finally load resources
        loadResources();
    }

    // Start initialization
    initialize();

    // Update URL when filters change
    function updateURL() {
        const params = new URLSearchParams();
        
        if ($typeFilter.val()) {
            params.set('type', $typeFilter.val());
        }
        if ($topicFilter.val()) {
            params.set('topic', $topicFilter.val());
        }
        if ($searchFilter.val()) {
            params.set('search', $searchFilter.val());
        }

        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);
        
    }
}); 