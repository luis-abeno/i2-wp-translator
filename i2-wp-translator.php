<?php
/*
 * Plugin Name:         I2 WP Translator
 * Plugin URI:          https://i2web.com.br/i2-wp-translator
 * Description:         Developed by I2 Web, this plugin translate a wordpress site into another languages
 * Version:             1.0
 * Requires at least:   6+
 * Requires PHP:        8.1
 * Author:              Luís Henrique Abeno
 * Author URI:          https://i2web.com.br
 * License:             GNU GENERAL PUBLIC LICENSE
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.html
 */
function i2_wp_translator_init()
{
    wp_enqueue_script('i2-wp-translator-js', plugins_url('i2-wp-translator.js', __FILE__), array('jquery'));
    wp_enqueue_style('i2-wp-translator-front', plugins_url('i2-wp-translator-front.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'i2_wp_translator_init');

function admin_enqueue_scripts()
{
    wp_enqueue_style('i2-wp-translator', plugins_url('i2-wp-translator.css', __FILE__));
    wp_enqueue_style('fa', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css');
}
add_action('admin_enqueue_scripts', 'admin_enqueue_scripts');


function i2_wp_translator_lang_selector()
{
    $html = '';

    $languages = array(
        'pt-br' => array('flag' => 'br-flag.png', 'code' => 'PT', 'desc' => 'Português'),
        'en' => array('flag' => 'en-flag.png', 'code' => 'EN', 'desc' => 'English')
    );

    $current_language = isset($_GET['lang']) ? htmlspecialchars($_GET['lang'], ENT_QUOTES, 'UTF-8') : get_option('my_translator_current_language', 'pt-br');
    $html .= '<div class="my-translator-language-selector" style="position:relative">';

    $html .= "<div class='current-lang'>" . $languages[$current_language]['code'] . '<img style="margin-left:10px" src="' . plugin_dir_url(__FILE__) . 'arrow.png" />' . "</div>";

    $html .= '<ul class="language-selector" style="list-style-type:none;position: absolute;z-index: 99;top: 20px;right:-60px">';
    foreach ($languages as $code => $label) {
        $image_url = '<img src="' . plugin_dir_url(__FILE__) . $label['flag'] . '" />';
        $html .= '<li data-lang="' . $code . '">' . $image_url . '&nbsp;&nbsp;' . $label['desc'] . '</li>';
    }
    $html .= '</ul>';

    $html .= '</div>';

    return $html;
}
add_shortcode('i2-wp-translator-lang-selector', 'i2_wp_translator_lang_selector');


function i2_wp_translator_translations_page()
{
    add_menu_page(
        'I2 Translator: Translations',
        'I2 Translator: Translations',
        'manage_options',
        'i2-translation-settings',
        'render_i2_wp_translator_translations_page'
    );
}
add_action('admin_menu', 'i2_wp_translator_translations_page');


function render_i2_wp_translator_translations_page()
{
    // Define the prefix you want to filter options by
    $prefix = 'i2_translation_';

    // Get all options
    $all_options = wp_load_alloptions();

    // Initialize an array to store options that match the prefix
    $filtered_options = array();

    // Loop through all options and filter the ones with the specified prefix
    foreach ($all_options as $option_name => $option_value) {
        if (strpos($option_name, $prefix) === 0) {
            // Option name starts with the specified prefix
            // Add it to the filtered options array
            $filtered_options[$option_name] = $option_value;
        }
    } ?>
    <div class="wrap">
        <h2>I2 Translator: Translations</h2>

        <form method="post" action="options.php" style="margin-top:20px">
            <div style="display:flex;flex-direction:column">
                <label for="translation_slug">Translation Slug:</label>
                <input type="text" id="translation_slug" name="translation_slug" />
            </div>

            <div style="display:flex;flex-direction:column;margin:15px 0">
                <label for="translation_en">EN Translation:</label>
                <textarea id="translation_en" name="translation_en"></textarea>
            </div>

            <div style="display:flex;flex-direction:column;margin:15px 0">
                <label for="translation_pt">PT Translation:</label>
                <textarea id="translation_pt" name="translation_pt"></textarea>
            </div>

            <input type='hidden' name='op' value='add_translation' />

            <?php submit_button('Adicionar tradução'); ?>
        </form>

        <table class="wp-list-table">
            <thead>
                <tr>
                    <th>Slug</th>
                    <th>EN</th>
                    <th>PT</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($filtered_options as $option_name => $option_value) {
                    $unserialized_data = unserialize($option_value);
                ?>
                    <tr>

                        <td><?php echo $unserialized_data['slug']; ?></td>
                        <td><?php echo $unserialized_data['translation_en']; ?></td>
                        <td><?php echo $unserialized_data['translation_pt']; ?></td>
                        <td class="delete-icon">
                            <form method="post" action="your-post-endpoint-url">
                                <input type="hidden" name="slug" value="<?php echo $unserialized_data['slug']; ?>">
                                <input type='hidden' name='op' value='delete_translation' />
                                <button type="submit"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php
                } ?>
            </tbody>
        </table>

    </div>
<?php
}

function i2_wp_translator_register_settings()
{
    register_setting('i2-wp-translator-settings-group', 'i2_wp_translator_settings');
}
add_action('admin_init', 'i2_wp_translator_register_settings');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['op'])) {

    if ($_POST['op'] == 'add_translation') {
        $slug = str_replace(" ", "_", strtolower($_POST['translation_slug']));
        $save_translation = array(
            'slug' => $slug,
            'translation_en' => $_POST['translation_en'],
            'translation_pt' => $_POST['translation_pt']
        );
        add_option('i2_translation_' . $slug, $save_translation);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }


    if ($_POST['op'] == 'delete_translation') {
        delete_option('i2_translation_' . $_POST['slug']);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

function i2_wp_translator_get_translation($atts)
{
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'slug' => '',
        'lang' => 'pt-br', // Default language
    ), $atts);

    // Get the current language from the URL query string
    if (isset($_GET['lang'])) {
        $atts['lang'] = sanitize_text_field($_GET['lang']);
    }

    // Use $atts['slug'] and $atts['lang'] to retrieve the translation
    $slug = $atts['slug'];
    $lang = $atts['lang'];
    $option_value = get_option($slug);
    if ($option_value !== false) {
        if ($lang == "en") {
            return $option_value["translation_en"];
        } else if ($lang == "pt-br") {
            return $option_value["translation_pt"];
        }
    } else {
        // The option with the specified name does not exist
        return "Failed to retrieve translation";
    }

    // Return the translation content
    return;
}
add_shortcode('i2_wp_translator_get_translation', 'i2_wp_translator_get_translation');
