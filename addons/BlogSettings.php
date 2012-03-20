<?php

class BlogSettings
{
    /**
     * Keep track of the default values for each setting
     *
     * @var array
     */
    private $_settingDefaults = array();

    private $_settingIds = array();

    public function __construct()
    {
        $configurationClass = new ReflectionClass('BlogConfiguration');

        $properties = $configurationClass->getProperties(
            ReflectionProperty::IS_STATIC
        );

        foreach ($properties as $property) {
            $propertyName  = $property->name;
            $propertyValue = $configurationClass->getStaticPropertyValue(
                $propertyName
            );

            $settingId = strtolower(
                preg_replace('/([^A-Z])([A-Z])/', '\1_\2', $propertyName)
            );

            $this->_settingIds[] = $settingId;

            $this->_settingDefaults[] = $propertyValue;
            $configurationClass->setStaticPropertyValue(
                $propertyName,
                get_option($settingId) ?: $propertyValue
            );
        }

        add_action('admin_menu', array(&$this, 'adminAddPage'), 1);
        add_action('admin_init', array(&$this, 'adminInit'));
        //add_action('admin_print_styles', array(&$this, 'adminStyles'));
    }

    /**
     * Add the settings page to the Network Admin menu
     *
     * @return void
     */
    public function adminAddPage()
    {
        add_menu_page(
            'Blog Settings',
            'Blog Settings',
            'manage_options',
            'blog-settings',
            array(&$this, 'displayPage'),
            null,
            null
        );
        add_submenu_page(
            'blog-settings',
            'Blog Settings',
            'Blog Settings',
            'manage_options',
            'blog-settings',
            array(&$this, 'displayPage')
        );
    }

    /**
     * Initialize the settings page
     *
     * @return void
     */
    public function adminInit()
    {
        foreach ($this->_getSettings() as $setting) {
            $prettyTitle   = ucwords(str_replace('_', ' ', $setting['id']));

            $defaults      = array(
                'title'   => $prettyTitle,
                'type'    => 'text',
                'class'   => '',
                'default' => '',
                'section' => 'blog_main',
            );
            $setting       = wp_parse_args($setting, $defaults);
            register_setting(
                'blog_settings',
                $setting['id'],
                array(&$this, 'validateSetting')
            );
            add_settings_field(
                $setting['id'],
                $setting['title'],
                array(&$this, 'display' . ucfirst($setting['type']) . 'Field'),
                'blog-settings',
                'blog_main',
                $setting
            );
        }
        add_settings_section(
            'blog_main',
            'Main Settings',
            array(&$this, 'displayPage'),
            'blog-settings'
        );
    }

    /**
     * Display the settings view
     *
     * @return void
     */
    public function displayPage()
    {
        require_once TEMPLATEPATH . '/views/admin/blogSettings.php';
    }

    /**
     * Display a text field for the specified setting
     *
     * @param $setting Setting object to display
     */
    public function displayTextField($setting)
    {
        $option = get_option($setting['id']);
        if (isset($option)) {
            $value = $option;
        } else {
            $value = $setting['default'];
        }
        ?>
    <input type="text"
           class="regular-text"
           style="width: 50em"
           value="<?php echo esc_attr($value); ?>"
           name="<?php echo $setting['id']; ?>"
           placeholder="<?php esc_attr_e($setting['default']); ?>" />
    <?php

    }

    /**
     * Validate a setting's value.
     *
     * In this case, validation is not necessary. WordPress handles sanitization
     * so we don't need to do any further validation.
     *
     * @param $input Input string to validate
     * @return string
     */
    public function validateSetting($input)
    {
        return $input;
    }

    /**
     * Retrieve an array of settings objects to pass to the initialization
     * routine
     *
     * @return array
     */
    private function _getSettings()
    {
        $settings = array();
        for($i=0;$i<sizeof($this->_settingIds);$i++) {
            $settings[] = array(
                'id'      => $this->_settingIds[$i],
                'default' => $this->_settingDefaults[$i]
            );

        }

        return $settings;
    }
}
