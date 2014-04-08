<?php
namespace htbackstretch;

use \tad\wrappers\ThemeCustomizeSection as Section;
use \tad\wrappers\headway\GlobalSettings as Settings;
use \tad\wrappers\headway\VEPanel;

/**
 * The entry point of the block plugin; hooks into Headway and WordPress.
 */
class Main
{
    protected $themeSection;
    protected $settings;
    protected $panel;
    protected $showColorPicker;
    protected $imageSources;

    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'blockRegister'));
        add_action('init', array($this, 'extend_updater'));
        // add the 'Background images' section
        // options will be stored in the 'backstretch' option in an array format
        // the namespace is used as the text domain
        $this->themeSection = new Section('Background images', 'backstretch', 'What to use as site background?', __NAMESPACE__);
        // add the multi-image control to allow the user to select one or more images
        // defaults to no images
        // will be stored in 'backstretch[imageSources]'
        $this->themeSection->addSetting('imageSources', 'Upload or select one or more images.', '', 'multi-image');
        // load site-wide settings from the database passing a prefix to get this block
        // theme-wide settings only
        $this->settings = new Settings('htbackstretch-');
        $dbValue = $this->settings->noImageSelected;
        is_null($dbValue) ? $this->showColorPicker = '0' : $this->showColorPicker = $dbValue;
        // please note: the first option in the select the theme developer
        // uses has the index 0 and that's the one reading
        // 'user can set a background color'
        if ($this->showColorPicker == '0') {
            // if the setting has not been set yet or the setting is
            // true then add the color picker to theme customizer controls
            // the set color will be stored in the 'backstretch[bg-color]' option
            $this->themeSection->addSetting('bg-color', 'Select a background color', '#FFF', 'color');
        }
        // register this block theme-wide settings
        VEPanel::on(__NAMESPACE__ . '\VisualEditorPanel');
        // depending on the theme user and the theme developer settings
        // do something related to the body background image or color
        $this->useOptions();
    }
    protected function useOptions()
    {
        // get the images sources from the database if the theme user did
        // upload/selected at least one
        $imageSources = Option::on('backstretch')->imageSources;
        // if the user did not select at least one image to use as the body
        // background then maybe use the color
        if (is_null($imageSources) or $imageSources == '') {
            $this->maybePrintBodyStyle();
            return;
        }
        // there is at least one image, use that
        $this->useImages($imageSources);
    }
    protected function maybePrintBodyStyle()
    {
        // if the theme user is not allowed to set a body background color return
        if ($this->showColorPicker != '0') {
            return;
        }
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
    protected function useImages($imageSources)
    {
        // the multiple images control will store the image sources in a
        // comma separated list
        $this->imageSources = explode(',', $imageSources);
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
        // localize the image sources to the page
        wp_localize_script('backstretch', 'backstretchImages', $this->imageSources); 
        // enqueue a script to start backstretch
        // using a debug friendly suffix
        $src = Script::suffix(HTBACKSTRETCH_BLOCK_URL . 'assets/js/backstretchStart.js');
        wp_enqueue_script('backstretchStart', $src, array('jquery', 'backstretch'), false, true);
    }
    public function blockRegister()
    {
        if (!class_exists('Headway')) {
            return;
        }
        return headway_register_block('\htbackstretch\Block', plugins_url(false, __FILE__));
    }


    public function extend_updater()
    {
        if (!class_exists('HeadwayUpdaterAPI')) {
            return;
        }
        $updater = new \HeadwayUpdaterAPI(array(
            'slug' => 'htbackstretch',
            'path' => plugin_basename(__FILE__),
            'name' => 'Backstretch block',
            'type' => 'block',
            'current_version' => HTBACKSTRETCH_BLOCK_VERSION
            ));
    }
}
