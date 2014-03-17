<?php
namespace htbackstretch;

class Main
{
    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'blockRegister'));
        add_action('init', array($this, 'extend_updater'));
        // add custom backgroun theme support
        $themeSupport = new \tad\wrappers\ThemeSupport();
        $themeSupport->add('custom-background');
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
