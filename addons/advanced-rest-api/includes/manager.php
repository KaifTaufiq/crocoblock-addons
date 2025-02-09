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
            foreach($this->settings[$id]['query_parameters'] as $param) {
                if (!isset($param['key']) || !isset($param['from'])) {
                    continue;
                }
                $key = $param['key'];
                $fixedKey = '{' . $key . '}';
                if($param['from'] == 'query_var'){
                    $query_var = $param['query_var'] ?? false;
                    if($query_var) {
                        $value = $_GET[$query_var] ?? false;
                        if( $value && str_contains($new_url,$fixedKey) ){
                            $new_url = str_replace($fixedKey, $value, $new_url);
                        }
                    }
                    continue;
                } elseif ($param['from'] == 'shortcode') {
                    $shortcode = $param['shortcode'] ?? false;
                    if($shortcode) {
                        $value = do_shortcode($shortcode);
                        $debug = $param['debugShortcode'] ?? false;
                        if($debug == "true") {
                            $old_url = $new_url;
                        }
                        if(!empty($value) && str_contains($new_url,$fixedKey)){
                            $new_url = str_replace($fixedKey,$value, $new_url);
                        }
                        if($debug == "true") {
                            ?>
                            <script>
                                console.log('From URL: <?php echo $old_url; ?>');
                                console.log('Shortcode: <?php echo $shortcode; ?>');
                                console.log('Value: <?php echo $value; ?>');
                                console.log('Updated URL: <?php echo $new_url; ?>');
                            </script>
                            <?php
                        }
                    }
                    continue;
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