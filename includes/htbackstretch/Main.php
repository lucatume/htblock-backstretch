<?php
namespace htbackstretch;

use \tad\wrappers\ThemeCustomizeSection;

class Main
{
    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'blockRegister'));
        add_action('init', array($this, 'extend_updater'));
        // will add the 'background_images' section
        $this->themeSection = new ThemeCustomizeSection('Background images', 'backstretch', 'Set one or more images to be used as the site background.', __NAMESPACE__);
        // add the setting and the control with it
        // will be stored in 'background_images[image_sources]' as an option
        // defaults to an empty string
        $this->themeSection->addSetting('image_sources', 'Upload or select one or more images.', '', 'multi-image');
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
