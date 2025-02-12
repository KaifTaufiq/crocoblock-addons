<?php 

namespace CrocoblockAddons\Addons\AdvancedRestApi;

class Manager
{
    public $nonce_key = 'advanced_rest_api_nonce';

    public $settings;

    public function __construct()
    {
        $this->settings = Addon::instance()->get_setting();
        add_filter('jet-engine/rest-api-listings/response/body', [$this,'FormatResponseBody'], 10, 4);
        add_filter('jet-engine/rest-api-listings/request/url',  [$this,'FormatRequestURL'], 10, 2);
        add_filter( 'jet-engine/rest-api-listings/request/type', [$this,'ChangeType'], 10,2);
    }

    public function FormatResponseBody($body, $request_instance, $query_args, $response) {
        $id = $request_instance->get_endpoint()['id'];
        if( isset($id) && isset($this->settings[$id]) && isset($this->settings[$id]['isSingle']) && $this->settings[$id]['isSingle'] == true ) {
            // Return the body as an array if isSingle is true for the endpoint
            return [$body];
        }
        return $body;
    }

    public function FormatRequestURL($url, $instance) {
        $id = $instance->get_endpoint()['id'];
        if( 
            isset($this->settings[$id]) && 
            isset($this->settings[$id]['query_parameters']) && 
            !empty( $this->settings[$id]['query_parameters'] )
        ) {
            $new_url = $url;
            foreach( $this->settings[$id]['query_parameters'] as $param ) {
                if (empty($param['key']) || empty($param['from'])) {
                    continue;
                }
                $key = $param['key'];
                $fixedKey = '{' . $key . '}';

                $replaceKey = function ($value) use (&$new_url, $fixedKey, $param) {
                    if ( !empty($value) && str_contains($new_url, $fixedKey)) {
                        return str_replace($fixedKey, $value, $new_url);
                    }
                    if ( !empty($param['fallback']) && str_contains($new_url, $fixedKey)) {
                        return str_replace($fixedKey, $param['fallback'], $new_url);
                    }
                    return str_replace($fixedKey, '', $new_url);
                };

                switch ($param['from']) {
                    case 'query_var':
                        $query_var = $param['query_var'] ?? false;
                        if( $query_var ) {
                            $value = $_GET[$query_var] ?? false;
                            $new_url = $replaceKey($value);
                        }
                        break;
                    case 'shortcode':
                        $shortcode = $param['shortcode'] ?? false;
                        $fallbackValue = $param['fallback'] ?? '';
                        if( $shortcode ) {
                            $value = do_shortcode($shortcode) ?: false;
                            $new_url = $replaceKey($value);
                        }
                        if( !empty($param['debugShortcode']) && $param['debugShortcode'] == 'true' ) {
                            ?>
                            <script>
                                console.log('From URL: <?php echo esc_js($url); ?>');
                                console.log('Shortcode: <?php echo esc_js($shortcode); ?>');
                                console.log('Shortcode Result: <?php echo esc_js($value); ?>');
                                <?php if (empty($value) && !empty($fallbackValue)): ?>
                                    console.log('Shortcode Result Empty, Using Fallback: <?php echo esc_js($fallbackValue); ?>');
                                <?php elseif (empty($value) && empty($fallbackValue)): ?>
                                    console.log('Shortcode Result & Fallback are Empty');
                                <?php endif; ?>
                                console.log('Updated URL: <?php echo $new_url; ?>');
                            </script>
                            <?php
                        }
                        break;
                }
            }
            return $new_url;
        }
        return $url;
    }

    public function ChangeType($type, $instance) {
        $id = $instance->get_endpoint()['id'];
        if( isset($this->settings[$id]) && isset($this->settings[$id]['isPOST']) && $this->settings[$id]['isPOST'] === true) {
            $type = 'post';
        }
        return $type;
    }
}