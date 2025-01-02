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
    }

    public function FormatResponseBody($body, $request_instance, $query_args, $response) {
        $id = $request_instance->get_endpoint()['id'];
        if( isset($id) && isset($this->settings[$id]) && $this->settings[$id]['isSingle'] === 'true') {
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
                    $query_var = isset($param['query_var']) ? $param['query_var'] : false;
                    if($query_var) {
                        $value = isset($_GET[$query_var]) ? $_GET[$query_var] : '';
                        if(!empty($value) && str_contains($new_url,$fixedKey)){
                            $new_url = str_replace($fixedKey,$value, $new_url);
                        }
                    }
                    continue;
                } elseif ($param['from'] == 'shortcode') {
                    $shortcode = isset($param['shortcode']) ? $param['shortcode'] : false;
                    $stripped = stripslashes($shortcode);
                    if($stripped) {
                        $value = do_shortcode($stripped);
                        $debug = isset($param['debugShortcode']) ? $param['debugShortcode'] : false;
                        if($debug == "true") {
                            ?>
                            <script>
                                console.log('Shortcode: <?php echo $stripped; ?>');
                                console.log('Value: <?php echo $value; ?>');
                            </script>
                            <?php
                        }
                        if(!empty($value) && str_contains($new_url,$fixedKey)){
                            $new_url = str_replace($fixedKey,$value, $new_url);
                        }
                        if($debug == "true") {
                            ?>
                            <script>
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
}