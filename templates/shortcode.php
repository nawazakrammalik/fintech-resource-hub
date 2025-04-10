<?php
/**
 * Shortcode template for displaying fintech resources
 */


$initial_type = isset($atts['type']) ? sanitize_text_field($atts['type']) : '';
$initial_topic = isset($atts['topic']) ? sanitize_text_field($atts['topic']) : '';


$resource_types = array('Video', 'Guide', 'Tool', 'Article');
$resource_topics = array('Tax', 'Audit', 'FP&A', 'Other');
?>

<div class="fintech-resource-hub" data-page="1">
   
    <div class="filters-section mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
         
            <div class="filter-group">
             <div class="relative">
                    <select id="type-filter" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">All Types</option>
                        <?php foreach ($resource_types as $type): ?>
                            <option value="<?php echo esc_attr($type); ?>" <?php selected($initial_type, $type); ?>>
                                <?php echo esc_html($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

      
            <div class="filter-group">
               <div class="relative">
                    <select id="topic-filter" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">All Topics</option>
                        <?php foreach ($resource_topics as $topic): ?>
                            <option value="<?php echo esc_attr($topic); ?>" <?php selected($initial_topic, $topic); ?>>
                                <?php echo esc_html($topic); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

         
            <div class="filter-group">
                <input type="text" 
                       id="search-filter" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white" 
                       placeholder="Search by title or description...">
            </div>
        </div>
    </div>


    <div class="resources-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>

 
    <div class="load-more-container hidden text-center mt-8 mb-4">
        <button class="load-more-button bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 border-0">
            Load More
        </button>
    </div>

    
    <div class="no-results-message hidden text-center py-8">
        <p class="text-gray-600">No resources found matching your criteria. Please try different filters.</p>
    </div>
</div> 