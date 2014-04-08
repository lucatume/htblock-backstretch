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
    protected $butler;
    protected $imageSources;

    public function __construct()
    {
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
        // the setting to allow a user to set the body bg
        // is '0' -> reverse casting to bool
        $showColorPicker = !(bool)($dbValue or '0');
        if ($showColorPicker) {
            // if the setting has not been set yet or the setting is
            // true then add the color picker to theme customizer controls
            // the set color will be stored in the 'backstretch[bg-color]' option
            $this->themeSection->addSetting('bg-color', 'Select a background color', '#FFF', 'color');
        }
        // register this block theme-wide settings
        VEPanel::on(__NAMESPACE__ . '\VisualEditorPanel');
        // along with the visual editor panel load a litte style to
        // fix the slider width
        add_action('headway_visual_editor_styles', function (){
            echo sprintf('<style>%s {width:%dpx;}</style>', 'input[id*="input-general-htbackstretch-"]', 30);
        });
        // depending on the theme user and the theme developer settings
        // delegate the butler with the administration of theme user options and theme developer settings
        $this->butler = new Butler($showColorPicker);
        $this->butler->serve();
        // $this->useOptions();
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
