<?php
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

interface iTrailStorySettings {
    public function __construct();
    public function add_trail_story_menu_page();
    public function create_trail_story_menu_page();
    public function create_trail_story_settings_page();
    public function page_init();
    public function sanitize( $input );
    public function print_option_info();
    public function print_section_info();
    public function trail_story_option_callback();
    public function trail_story_setting_callback();
}

/**
* PLUGIN SETTINGS PAGE
*/
class TrailStorySettings implements iTrailStorySettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_trail_story_menu_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_trail_story_menu_page()
    {
        // This page will be under "Settings"add_submenu_page( 'tools.php', 'SEO Image Tags', 'SEO Image Tags', 'manage_options', 'seo_image_tags', 'seo_image_tags_options_page' );
        add_menu_page(
            'Trail Story',
            'Trail Story',
            'manage_options',
            'trail-story',
            array( $this, 'create_trail_story_menu_page' ),
            plugins_url('geo-mashup-trail-story-add-on/assets/icon-20x20.png'), 100
        );

        add_submenu_page(
            'trail-story',
            'Itineraries',
            'Itineraries',
            'manage_options',
            'edit.php?post_type=itinerary'
        );

        add_submenu_page(
            'trail-story',
            'Trail Stories',
            'Trail Stories',
            'manage_options',
            'edit.php?post_type=trail-story'
        );

        add_submenu_page(
            'trail-story',
            'Trail Conditions',
            'Trail Conditions',
            'manage_options',
            'edit.php?post_type=trail-condition'
        );

        add_submenu_page(
            'trail-story',
            'Settings',
            'Settings',
            'manage_options',
            'trail-story-settings',
            array( $this, 'create_trail_story_settings_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_trail_story_menu_page()
    {
        // Set class property
        $this->options = get_option( 'trail_story_option' );
        ?>
        <div class="wrap">
            <h2>Options</h2>
            <form method="post" action="options.php">

            <?php
                // This prints out all hidden setting fields
                settings_fields( 'trail_story_options_group' );
                do_settings_sections( 'trail-story-options-admin' );
                submit_button('Save Options');
            ?>
            </form>
        </div>
        <?php
    }

     /**
     * Options page callback
     */
    public function create_trail_story_settings_page()
    {
        // Set class property
        $this->options = get_option( 'trail_story_settings' );
        ?>
        <div class="wrap">
            <h2>Settings</h2>
            <form method="post" action="options.php">

            <?php
                // This prints out all hidden setting fields
                settings_fields( 'trail_story_settings_group' );
                do_settings_sections( 'trail-story-setting-admin' );
                submit_button('Save Settings');
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'trail_story_options_group', // Option group
            'trail_story_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'trail_story_settings_group', // Option group
            'trail_story_settings', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'trail_story_options_section', // ID
            'Options Section', // Title
            array( $this, 'print_option_info' ), // Callback
            'trail-story-options-admin' // Page
        );

        add_settings_section(
            'trail_story_settings_section', // ID
            'Settings Section', // Title
            array( $this, 'print_section_info' ), // Callback
            'trail-story-setting-admin' // Page
        );

        add_settings_field(
            'trail_story_option', // ID
            'Trail Story Option', // Title
            array( $this, 'trail_story_option_callback' ), // Callback
            'trail-story-options-admin', // Page
            'trail_story_options_section' // Section
        );

        add_settings_field(
            'trail_story_setting', // ID
            'Trail Story Setting', // Title
            array( $this, 'trail_story_setting_callback' ), // Callback
            'trail-story-setting-admin', // Page
            'trail_story_settings_section' // Section
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['trail_story_option'] ) )
            $new_input['trail_story_option'] = absint( $input['trail_story_option'] );

        if( isset( $input['trail_story_setting'] ) )
            $new_input['trail_story_setting'] = absint( $input['trail_story_setting'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_option_info()
    {
        print '<br/><p style="font-size:14px; margin:0 25% 0 0;"><strong>Developed at <a href="http://www.orionweb.net" target="_blank">Orion Group</a> LLC by '.
         '<a href="http://andrewmgunn.com" target="_blank">Andrew Gunn</a>, Ryan Van Ess, Jon Valcq, and Josh Selk</strong>';
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        //print '<br/><p style="font-size:14px; margin:0 25% 0 0;"><strong>Options coming soon!</strong>';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function trail_story_option_callback()
    {
        //Get plugin options
        $options = get_option( 'trail_story_options' );

        if (isset($options['trail_story_option'])) {
            $html .= '<input type="checkbox" id="trail_story_option"
             name="trail_story_options[trail_story_option]" value="1"' . checked( 1, $options['trail_story_option'], false ) . '/>';
        } else {
            $html .= '<input type="checkbox" id="trail_story_option"
             name="trail_story_options[trail_story_option]" value="1"' . checked( 1, $options['trail_story_option'], false ) . '/>';
        }

        echo $html;

        global $geo_mashup_options;

        // Create marker and color arrays
        $colorNames = Array(
            'aqua' => '#00ffff',
            'black' => '#000000',
            'blue' => '#0000ff',
            'fuchsia' => '#ff00ff',
            'gray' => '#808080',
            'green' => '#008000',
            'lime' => '#00ff00',
            'maroon' => '#800000',
            'navy' => '#000080',
            'olive' => '#808000',
            'orange' => '#ffa500',
            'purple' => '#800080',
            'red' => '#ff0000',
            'silver' => '#c0c0c0',
            'teal' => '#008080',
            'white' => '#ffffff',
            'yellow' => '#ffff00'
        );
         $include_taxonomies = $geo_mashup_options->get( 'overall', 'include_taxonomies' ); ?>
                <table>
                    <tr>
                        <th><?php _e( 'Post Types', 'geo-mashup-trail-story-add-on' ); ?></th>
                        <th><?php _e('Color', 'geo-mashup-trail-story-add-on'); ?></th>
                        <th><?php _e('Show Connecting Line Until Zoom Level (0-20 or none)','geo-mashup-trail-story-add-on'); ?></th>
                    </tr>
                    <?php foreach( get_post_types( array( 'show_ui' => true ), 'objects' ) as $post_type ) : ?>
                        <?php if ( in_array( $post_type->name, $geo_mashup_options->get( 'overall', 'located_post_types' ) ) ) { ?>
                            <tr>
                                <td><?php echo esc_html( $post_type->labels->name ); ?></td>
                                <td>
                                    <select id="<?php echo esc_attr( $post_type->rewrite['slug'] ); ?>_color" 
                                        name="global_map[term_options][<?php echo $include_taxonomy; ?>][color][<?php echo esc_attr( $term->slug ); ?>]">
                                    <?php foreach($colorNames as $name => $rgb) : ?>
                                        <option value="<?php echo esc_attr( $name ); ?>"<?php
                                            if ( isset( $taxonomy_options['color'][$term->slug] ) and $taxonomy_options['color'][$term->slug] == $name ) {
                                                echo ' selected="selected"';
                                            }
                                        ?> style="background-color:<?php echo esc_attr( $rgb ); ?>;"><?php echo esc_html( $name ); ?></option>
                                    <?php endforeach; // color name ?>  
                                    </select>
                        </td><td>
                        <input id="<?php echo $include_taxonomy; ?>_line_zoom_<?php 
                            echo esc_attr( $term->slug ); ?>" name="global_map[term_options][<?php 
                            echo $include_taxonomy; ?>][line_zoom][<?php
                            echo esc_attr( $term->slug ); ?>]" value="<?php 
                            if ( isset( $taxonomy_options['line_zoom'][$term->slug] ) )
                                echo esc_attr( $taxonomy_options['line_zoom'][$term->slug] );
                            ?>" type="text" size="2" maxlength="2" /></td></tr>
                        <?php } ?>
                    <?php endforeach; // taxonomy term ?>
                </table><?php
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function trail_story_setting_callback()
    {
        //Get plugin options
        $options = get_option( 'trail_story_settings' );

        if (isset($options['trail_story_option'])) {
            $html .= '<input type="checkbox" id="trail_story_settings"
             name="trail_story_settings[trail_story_setting]" value="1"' . checked( 1, $options['trail_story_setting'], false ) . '/>';
        } else {
            $html .= '<input type="checkbox" id="trail_story_settings"
             name="trail_story_settings[trail_story_setting]" value="1"' . checked( 1, $options['trail_story_setting'], false ) . '/>';
        }

        echo $html;
    }
}

if( is_admin() )
    $trail_story = new TrailStorySettings();
