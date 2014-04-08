<?php
namespace htbackstretch;

use \tad\wrappers\headway\GlobalSettings as Settings;
use \tad\wrappers\Option;
use \tad\utils\Script;

/**
 * Handles theme user scenarios and theme developer settings to deliver the result.
 */
class Butler
{
    protected $allowSetColor;
    protected $data;
    protected $settings;

    public function __construct($allowSetColor = false)
    {
        $this->allowSetColor = $allowSetColor;
        $this->data = array();
        $this->settings = new Settings('htbackstretch-');
    }
    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
    }
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }
    public function serve()
    {
        // get the images sources from the database if the theme user did
        // upload/selected at least one
        $this->imageSources = Option::on('backstretch')->imageSources;
        // if the user did not select at least one image to use as the body
        // background then maybe use the color
        if (is_null($this->imageSources) or $this->imageSources == '') {
            $this->maybePrintBodyStyle();
            return;
        }
        // there is at least one image, use that
        $this->useImages();
    }

    protected function maybePrintBodyStyle()
    {
        // if the theme user is not allowed to set a body background color return
        if (!$this->allowSetColor) {
            return; }
        // hook into the 'wp_enqueue_scripts' hook to print the style
        $tag = 'wp_enqueue_scripts';
        $function = function () {
            $class = 'htbackstretch-color';
            $color = \tad\wrappers\Option::on('backstretch')->bgColor;
            echo sprintf('<style>body.%s{background-color:%s;}</style>', $class, $color);
        };
        add_action($tag, $function);
        // hook into th body_class filter to add a class to the body
        $tag = 'body_class';
        $function = function ($classes) {
            $classes[] = 'htbackstretch-color';
            return $classes;
        };
        add_filter($tag, $function);
    }

    protected function useImages()
    {
        // the multiple images control will store the image sources in a
        // comma separated list
        $this->imageSources = explode(',', $this->imageSources);
        // will be 1 to many
        $count = count($this->imageSources);
        // did the theme developer chose to show one random image per page?
        // default to false -> show in a slider-like effect
        $useRandom = (bool)($this->settings->moreImagesSelected or '0');
        if ($useRandom) {
            $randomIndex = mt_rand(0, $count - 1);
            $this->imageSources = array($this->imageSources[$randomIndex]);
        }
        $useEffect = false;
        // effects: grayscale, sepia, negative in this order
        $effect = '0';
        if ($count == 1) {
            // the setting for 'do not use an effect' is '0'
            // will default to not using an effect
            $useEffect = (bool)($this->settings->oneImageSelected or '0');
            $effect = $this->settings->oneImageEffect or '0';
        } else {
            // the setting for 'do not use an effect' is '0'
            // will default to not using an effect
            $useEffect = (bool)($this->settings->moreImagesEffectUse or '0');
            $effect = $this->settings->moreImagesEffect or '0';
        }
        if ($useEffect) {
            // require the BFI_Thumb file
            \tadlibs_include('bfi/BFI_Thumb');
            $buffer = array();
            foreach ($this->imageSources as $src) {
                // obtain the url to the modified image generated
                // by bfi_thumb
                $params = array();
                switch ($effect) {
                    case '1':
                        // sepia
                    $params = array('grayscale' => true, 'color' => '#643200');
                    break;
                    case '2':
                        // negative
                    $params = array('negate' => true);
                    break;
                    default:
                        // grayscale
                    $params = array('grayscale' => true);
                    break;
                }
                // add the source of the modified image to the buffer
                $buffer[] = bfi_thumb($src, $params);
            }
            $this->imageSources = $buffer;
        }
        // hook into wp_enqueue_scripts
        add_action( 'wp_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    public function enqueueScripts()
    {
        // enqueue the backstretch plugin from the CDN, requires jQuery
        wp_enqueue_script('backstretch', '//cdnjs.cloudflare.com/ajax/libs/jquery-backstretch/2.0.4/jquery.backstretch.min.js', 'jquery'); 
        // get the duration and fade settings with some defaults
        $this->duration = intval($this->settings->moreImagesDuration);
        $this->fade = intval($this->settings->moreImagesFade);
        wp_localize_script('backstretch', 'backstretchData', $this->data); 
        // enqueue a script to start backstretch
        // using a debug friendly suffix
        $src = Script::suffix(HTBACKSTRETCH_BLOCK_URL . 'assets/js/backstretchStart.js');
        wp_enqueue_script('backstretchStart', $src, array('jquery', 'backstretch'), false, true);
    }
}