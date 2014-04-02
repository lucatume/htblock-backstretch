<?php
namespace htbackstretch;

class VisualEditorPanel extends \HeadwayVisualEditorPanelAPI
{
    public $id = 'htbackstretch';
    public $name = 'Backstretch';
    public $mode = 'grid';
    
    public $tabs = array(
        'user-options' => 'User Options',
        );
    
    public $inputs = array(
        'user-options' => array(
            'heading-no-image' => array(
                'type' => 'heading',
                'name' => 'heading-no-image',
                'label' => 'No image selected'
                ),
            'htbackstretch-no-image-selected' => array(
                'type' => 'select',
                'name' => 'htbackstretch-no-image-selected',
                'label' => 'If the user did not select any image:',
                'options' => array(
                    'user can set a background color',
                    'use a default color',
                    'use a default image'
                    ),
                'toggle' => array(
                    0 => array(
                        'hide' => array(
                            '#input-htbackstretch-default-bg-color',
                            '#input-htbackstretch-default-bg-image'
                            )
                        ),
                    1 => array(
                        'show' => array(
                            '#input-htbackstretch-default-bg-color'
                            ),
                        'hide' => array(
                            '#input-htbackstretch-default-bg-image'
                            )
                        ),
                    2 => array(
                        'hide' => array(
                            '#input-htbackstretch-default-bg-color'
                            ),
                        'show' => array(
                            '#input-htbackstretch-default-bg-image'
                            )
                        )
                    )
                ),
            'htbackstretch-default-bg-color' => array(
                'type' => 'colorpicker',
                'name' => 'htbackstretch-default-bg-color',
                'label' => 'Default background color',
                'default' => '#FFF',
                ),
            'htbackstretch-default-bg-image' => array(
                'type' => 'image',
                'name' => 'htbackstretch-default-bg-image',
                'label' => 'Default background image',
                'default' => ''
                ),
            'heading-one-image' => array(
                'type' => 'heading',
                'name' => 'heading-one-image',
                'label' => 'One image selected'
                ),
            'htbackstretch-one-image-selected' => array(
                'type' => 'select',
                'name' => 'htbackstretch-one-image-selected',
                'label' => 'If the user did select one image:',
                'options' => array(
                    'use the image as is',
                    'apply an effect to the image'
                    ),
                'toggle' => array(
                    0 => array(
                        'hide' => array(
                            '#input-htbackstretch-one-image-effect',
                            )
                        ),
                    1 => array(
                        'show' => array(
                            '#input-htbackstretch-one-image-effect'
                            )
                        )
                    )
                ),
                'htbackstretch-one-image-effect' => array(
                    'type' => 'select',
                    'name' => 'htbackstretch-one-image-effect',
                    'label' => 'Select an effect',
                    'options' => array(
                        'grayscale',
                        'sepia',
                        'negative'
                        )
                    ),
                'heading-more-images' => array(
                    'type' => 'heading',
                    'name' => 'heading-more-images',
                    'label' => 'Two or more images selected'
                    ),
                'htbackstretch-more-images-use' => array(
                    'type' => 'select',
                    'name' => 'htbackstretch-more-images-use',
                    'label' => 'If the user did select two or more images:',
                    'options' => array(
                        'show the images in a slider-like effect',
                        'show one random image per page load'
                        )
                    ),
                'htbackstretch-more-images-effect-use' => array(
                    'type' => 'select',
                    'name' => 'htbackstretch-more-images-effect-use',
                    'label' => 'Apply an effect to the images?',
                    'options' => array(
                        'No, leave the images as they are.',
                        'Yes, apply an effect to all the images.'
                        ),
                    'toggle' => array(
                        0 => array (
                            'hide' => array('#input-htbackstretch-more-images-effect')
                            ),
                        1 => array(
                            'show' => array('#input-htbackstretch-more-images-effect')
                            )
                        )
                    ),
                'htbackstretch-more-images-effect' => array(
                    'type' => 'select',
                    'name' => 'htbackstretch-more-images-effect',
                    'label' => 'Select an effect',
                    'options' => array(
                        'grayscale',
                        'sepia',
                        'negative'
                        )
                    )
                )
            );

    protected function inputName($inputSlug)
    {
        return sprintf('#input-%s-%s', $this->id, $inputSlug);
    }
    protected function optionName($inputSlug)
    {
        return sprintf('%s-%s', $this->id, $inputSlug);
    }
}
