<?php
if( class_exists('Bricks\Element')){

class Single_Listing_Bricks extends \Bricks\Element
{
    public $category = 'custom';
    public $name = 'single-listing';
    public $nestable = false;
    public $icon = 'fas fa-anchor';
    public $scripts = ['jetEngineBricks'];
    public function get_label()
    {
        return esc_html__('Single Listing', 'bricks-basic-element');
    }
    public function set_controls()
    {
        $this->controls['all_list_id'] = [
            'tab' => 'content',
            'label' => esc_html__('All Listing ID', 'bricks-basic-element'),
            'type' => 'text',
        ];
        $this->controls['single_list_id'] = [
            'tab' => 'content',
            'label' => esc_html__('Single Listing ID', 'bricks-basic-element'),
            'type' => 'text',
        ];
        $this->controls['no_active'] = [
            'tab' => 'content',
            'label' => esc_html__('No Single Active ID', 'bricks-basic-element'),
            'type' => 'text',
        ];
        $this->controls['closeBtn'] = [
            'tab' => 'content',
            'label' => esc_html__('Close Button ID', 'bricks-basic-element'),
            'type' => 'text',
        ];
        $this->controls['activeItemClass'] = [
            'tab' => 'content',
            'label' => esc_html__('Active Button Class', 'bricks-basic-element'),
            'type' => 'text',
        ];
    }
    // Enqueue element styles and scripts
    public function enqueue_scripts()
    {
        wp_enqueue_style('jet-engine-frontend');
    }
    public function render()
    {
        $settings = $this->settings;
        $this->enqueue_widget_scripts($settings);
    }
    public function enqueue_widget_scripts($settings) {
        // Register the JavaScript file
        wp_register_script('SingleListing', plugin_dir_url(__FILE__) . '/script.js', array(), '1.0', true);
        
        // Localize script with widget settings (PHP to JS)
        $localized_data = array(
            'single_list_id' => $settings['single_list_id'],
            'all_list_id'    => $settings['all_list_id'],
            'no_active'      => $settings['no_active'],
            'closeBtn'       => $settings['closeBtn'],
            'activeItemClass'    => $settings['activeItemClass'],
        );
        
        wp_localize_script('SingleListing', 'SingleQuerySettings', $localized_data);
        
        // Enqueue the script
        wp_enqueue_script('SingleListing');
    }
}

}
