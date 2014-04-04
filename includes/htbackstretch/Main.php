<?php
namespace htbackstretch;

use \tad\wrappers\ThemeCustomizeSection as Section;
use \tad\wrappers\headway\BlockSettings as Settings;
use \tad\wrappers\headway\VEPanel;

class Main
{
    protected $section;
    protected $blockSetting;
    protected $panel;

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
        // load site-wide settings from the database
        // using namespace as it's the same as the block id
        $blockSettings = new Settings(__NAMESPACE__);
        // conservative default
        $showColorPicker = $blockSettings->settings['bg-color-allow'];
        if (is_null($showColorPicker) or (bool)$showColorPicker) {
            $this->themeSection->addSetting('bg-color', 'Select a background color', '#FFF', 'color');
        }
        // register this block theme-wide options
        $this->panel = new VEPanel(__NAMESPACE__ . '\VisualEditorPanel');
        // add_action('after_setup_theme', array($this, 'addVisualEditorPanels'));
    }

    // public function addVisualEditorPanels()
    // {
    //     if (!class_exists('Headway')) {
    //         return;
    //     }
    //     // include the class defining those options
    //     include_once dirname(__FILE__) . '/VisualEditorPanel.php';
    //     // register the visual editor panel
    //     $class = '\htbackstretch\VisualEditorPanel';
    //     $tag = 'headway_visual_editor_display_init';
    //     // hook in with a priority higher than the one Headway registers
    //     // its own setup block to have the Header Image options panel show
    //     // on the right side of it
    //     $priority = 1000;
    //     add_action($tag, create_function('', 'return headway_register_visual_editor_panel_callback(\'' . $class . '\');'), $priority);
    // }

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
