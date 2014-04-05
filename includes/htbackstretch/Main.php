<?php
namespace htbackstretch;

use \tad\wrappers\ThemeCustomizeSection as Section;
use \tad\wrappers\headway\GlobalSettings as Settings;
use \tad\wrappers\headway\VEPanel;
use \tad\wrappers\Option;

class Main
{
    protected $section;
    protected $blockSetting;
    protected $panel;
    protected $showColorPicker;

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
        $dbValue = Settings::on('htbackstretch-')->noImageSelected;
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
        $this->panel = new VEPanel(__NAMESPACE__ . '\VisualEditorPanel');
        // depending on the setting then print a style to the page
        $this->maybePrintBodyStyle();
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
