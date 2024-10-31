<?php

$apiData = null;

// Hook the 'admin_menu' action hook, run the function named 'mfp_Add_My_Admin_Link()'
add_action( 'admin_menu', 'rdv360_add_options_page' );

// Add a new top level menu link to the ACP
function rdv360_add_options_page()
{
    add_menu_page(
        'Rdv360', // Title of the page
        'Rdv360', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        'rdv360', // The 'slug' - file to display when clicking the link
        'rdv360_options_page'
    );
}

function rdv360_options_page(){
    rdv360_api_get_data();
    global $apiData
    ?>
    <div class="wrap rdv360-settings">
        <h1><?php echo __("Rdv360 configuration", "rdv360_widget") ?></h1>
        <p><?php echo __("Ce widget fonctionne avec la solution de prise de rendez-vous RDV360.", "rdv360_widget") ?></p>
        <p>Vous n'avez pas encore de compte rdv360 ? Créez en un gratuitement en <a href="https://www.rdv360.com/?utm_source=plugin-wordpress&utm_medium=plugin-Description">cliquant ici</a>.</p>

        <form method="post" action="options.php">
            <?php settings_fields('rdv360_options'); ?>
            <section id="apiParameters">
                <?php do_settings_sections('rdv360'); ?>
                <?php if ($apiData) { ?>
                    <div class="notice notice-success">Vous êtes connecté à votre compte Rdv360</div>
                <?php } else { ?>
                    <div class="notice notice-warning">Connectez vous à votre compte Rdv360 pour utiliser le widget</div>
                <?php } ?>
                <?php submit_button() ?>
            </section>
            <section id="options">
                <?php do_settings_sections('rdv360_params'); ?>
                <?php submit_button("Generate shortcode") ?>
            </section>
        </form>

        <section>
            <h3>3. Copiez ce code court et collez-le dans votre article, page ou widget :</h3>
            <pre style="white-space: break-spaces; background-color: white; color: black; padding: 5px; border-radius: 5px"><?php echo rdv360_generate_shortcode() ?></pre>
        </section>

        <section id="help">
            <h2>Description</h2>
            <p>Ce plugin vous permet d’ajouter une page de réservation en ligne à partir de votre compte <a href="https://pro.rdv360.com/">rdv360</a>.</p>
            <p>Si vous n’avez pas encore de compte, visitez <a href="https://www.rdv360.com/?utm_source=plugin-wordpress&utm_medium=plugin-Description">notre site</a> pour avoir plus d’informations sur notre service.</p>
            <p>Rdv360 est un logiciel de gestion pour les petites entreprises qui propose les fonctionnalités suivantes :</p>
            <ul>
                <li>Fichier client complet</li>
                <li>Agendas en ligne</li>
                <li>Réservation des prestations en ligne (dont via ce plugin)</li>
                <li>Caisse et comptabilité</li>
                <li>Rappels de rendez-vous par SMS et emails</li>
                <li>Création de votre site web personnalisé</li>
                <li>E-commerce</li>
                <li>Et bien plus</li>
            </ul>
            <p><strong>Découvrez la solution de gestion complète pour les professionnels : <a href="https://www.rdv360.com/?utm_source=plugin-wordpress&utm_medium=plugin-Description">Rdv360.com</a></strong></p>

            <h2>Comment faire pour l’intégrer ?</h2>
            <div class="howto">
                <div style="position: relative; padding-bottom: 56.25%; height: 0;">
                    <iframe src="https://www.loom.com/embed/ae3cb911bce54cc5bb4b01a84d1e28f3"
                            allowfullscreen
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0">
                    </iframe>
                </div>

                <ul>
                    <li>Avoir un compte RDV360 : si ce n’est pas encore le cas, vous pouvez en créer un rapidement en <a href="https://www.rdv360.com/?utm_source=plugin-wordpress&utm_medium=plugin-Description">cliquant ici</a></li>
                    <li>Rentrez vos clés API : Vous trouverez ces données sur votre page <a href="https://pro.rdv360.com/configuration/api/">https://pro.rdv360.com/configuration/api/</a></li>
                    <li>Choisissez vos options : Vous décidez des menus que vous souhaitez voir apparaître sur votre widget (réservation en ligne, boutique, chèque cadeau…)</li>
                    <li>Un code est automatiquement généré : il suffit de le copier et le coller dans votre article sur votre site Wordpress, à l’endroit où vous souhaitez le voir apparaître.</li>
                </ul>
            </div>
<!--            <p>Besoin d’aide ? Vous pouvez regarder notre vidéo d’explication en cliquant ici</p>-->
        </section>
    </div>
    <?php
}

add_action('admin_init', 'rdv360_admin_init');
function rdv360_admin_init(){
    register_setting( 'rdv360_options', 'rdv360_options', 'rdv360_options_validate' );

    add_settings_section('rdv360_api', __("1. Paramètres API", "rdv360_widget"), 'rdv360_section_api', 'rdv360');
    add_settings_field('rdv360_api_key', __("Clé API", "rdv360_widget"), 'rdv360_setting_api_key', 'rdv360', 'rdv360_api');
    add_settings_field('rdv360_api_secret', __("Secret API", "rdv360_widget"), 'rdv360_setting_api_secret', 'rdv360', 'rdv360_api');

    add_settings_section('rdv360_parameters', __("2. Options du widget", "rdv360_widget"), 'rdv360_section_parameters', 'rdv360_params');
    add_settings_field('rdv360_menu', __("Masquer le menu ?", "rdv360_widget"), 'rdv360_parameter_menu', 'rdv360_params', 'rdv360_parameters');
    add_settings_field('rdv360_account', __("Masquer le compte utilisateur ?", "rdv360_widget"), 'rdv360_parameter_account', 'rdv360_params', 'rdv360_parameters');
    add_settings_field('rdv360_gift', __("Masquer l'espace cadeau ?", "rdv360_widget"), 'rdv360_parameter_gift', 'rdv360_params', 'rdv360_parameters');
    add_settings_field('rdv360_category', __("Page d'arrivée", "rdv360_widget"), 'rdv360_parameter_category', 'rdv360_params', 'rdv360_parameters');
}

function rdv360_section_api()
{
    echo '<p>' . __("Renseignez les paramètres de connexion à l'API", "rdv360_widget") . '</p>';
    echo '<p>' . __("Vous trouverez vos paramètres API sur votre compte RDV360 (\"Config\" > \"API RDV360\")", "rdv360_widget") . '</p>';
}

function rdv360_setting_api_key()
{
    $options = get_option('rdv360_options');
    $value = $options['api_key'] ?? '';
    echo "<input id='rdv360_api_key' name='rdv360_options[api_key]' size='40' type='text' value='{$value}' />";
}

function rdv360_setting_api_secret()
{
    $options = get_option('rdv360_options');
    $value = $options['api_secret'] ?? '';
    echo "<input id='rdv360_api_secret' name='rdv360_options[api_secret]' size='40' type='text' value='{$value}' />";
}

function rdv360_section_parameters()
{
    echo '<p>' . __("Personnalisez le widget puis générer le shortcode associé.", "rdv360_widget") . '</p>';
}

function rdv360_parameter_menu()
{
    $options = get_option('rdv360_options');
    ?>
    <input id='rdv360_option_menu' name='rdv360_options[menu]' type='checkbox' <?php if ($options['menu']) echo "checked" ?> />
    <?php
}

function rdv360_parameter_account()
{
    $options = get_option('rdv360_options');
    ?>
    <input id='rdv360_option_account' name='rdv360_options[account]' type='checkbox' <?php if ($options['account']) echo "checked" ?> />
    <?php
}

function rdv360_parameter_gift()
{
    $options = get_option('rdv360_options');
    ?>
    <input id='rdv360_option_gift' name='rdv360_options[gift]' type='checkbox' <?php if ($options['gift']) echo "checked" ?> />
    <?php
}

function rdv360_parameter_category()
{
    global $apiData;
    $options = get_option('rdv360_options');
    $categories = $apiData['menu'];
    ?>
    <select id="rdv360_option_category" name='rdv360_options[category]'>
        <?php foreach ($categories as $key => $category) { ?>
            <option value="<?php echo $key ?>" <?php if ((isset($options['category']) && $options['category'] === $key) || (!isset($options['category']) && $key === 'services')) echo "selected" ?> ><?php echo $category['label'] ?></option>
        <?php } ?>
    </select>
    <?php
}

function rdv360_options_validate($input) {
    $options = get_option('rdv360_options');

    $options['api_key'] = trim($input['api_key']);
    $options['api_secret'] = trim($input['api_secret']);

    $options['menu'] = ($input['menu'] === 'on');
    $options['account'] = ($input['account'] === 'on');
    $options['gift'] = ($input['gift'] === 'on');
    $options['category'] = $input['category'];

    return $options;
}


function rdv360_shortcode( $atts = [], $content = null, $tag = '' ) {
    wp_register_script('rdv360_widget_js', 'https://www.rdv360.com/widget/dist/app_vuidget_v3.js', null, null, true);
    wp_register_style('rdv360_widget_css', 'https://www.rdv360.com/widget/dist/app_v3.css');
    wp_enqueue_script('rdv360_widget_js');
    wp_enqueue_style('rdv360_widget_css');

    // normalize attribute keys, lowercase
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );

    $options = get_option('rdv360_options');

    if ($options['api_key'] && $options['api_secret']) {
        // start box
        $o = '<div class="rdv360-widget" data-apikey="' . $options['api_key'] . '" data-apisecret="' . $options['api_secret'] . '"';

        foreach ($atts as $att) {
            switch($att) {
                case 'hide_menu':
                    $o .= ' data-hide_menu';
                    break;
                case 'hide_account':
                    $o .= ' data-hide_account';
                    break;
                case 'hide_gift':
                    $o .= ' data-hide_gift';
                    break;
            }
        }

        if (isset($atts['category'])) $o .= ' data-category="' . $atts['category'] . '"';
        if (isset($atts['category']) && isset($atts['item'])) $o .= ' data-item_id="' . $atts['item'] . '"';
        if (isset($atts['category']) && isset($atts['item']) && isset($atts['auto_cart'])) $o .= ' data-auto_cart';
        if (isset($atts['ga'])) $o .= ' data-ga="' . $atts['ga'] . '"';

        $o .= '></vue-widget>';
    } else {
        $o = '';
    }

    // return output
    return $o;
}

function rdv360_api_get_data() {
    global $apiData;

    $options = get_option('rdv360_options');

    $curl = curl_init();

    $url = "https://api.rdv360.com/account/widget";

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "api-key: " . $options['api_key'],
            "api-secret: " . $options['api_secret']
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $data = json_decode($response, true);
    if (isset($data["result"]) && $data["result"] === "error") {
        $apiData = null;
    } else {
        $apiData = $data;
    }
}

function rdv360_generate_shortcode() {
    $options = get_option('rdv360_options');

    if (isset($options['api_key']) && isset($options['api_secret'])) {
        // start box
        $o = '[rdv360';

        if (isset($options['menu']) && $options['menu']) $o .= ' hide_menu';
        if (isset($options['account']) && $options['account']) $o .= ' hide_account';
        if (isset($options['gift']) && $options['gift']) $o .= ' hide_gift';
        if (isset($options['category']) && $options['category']) $o .= ' category="' . $options['category'] . '"';
        if (isset($options['category']) && $options['category'] && isset($options['item']) && $options['item']) $o .= ' item_id="' . $options['item'] . '"';
        if (isset($options['category']) && $options['category'] && isset($options['item']) && $options['item'] && $options['auto_cart']) $o .= ' auto_cart';
        if (isset($options['ga']) && $options['ga']) $o .= ' ga="' . $options['ga'] . '"';

        $o .= ']';
    } else {
        $o = 'Please fill in api key and secret key.';
    }

    return $o;
}
/**
 * Central location to create all shortcodes.
 */
function rdv360_shortcodes_init() {
    add_shortcode( 'rdv360', 'rdv360_shortcode' );
}

add_action( 'init', 'rdv360_shortcodes_init' );

function rdv360_register_assets () {
    wp_register_script('rdv360_widget_js', 'https://www.rdv360.com/widget/dist/app_vuidget.js', null, null, true);
    wp_register_style('rdv360_widget_css', 'https://www.rdv360.com/widget/dist/app.css');
    wp_enqueue_script('rdv360_widget_js');
    wp_enqueue_style('rdv360_widget_css');
}

// Load styles css for admin plugin
function rdv360_admin_style() {
    wp_register_style( 'rdv360_admin_css', plugins_url('/css/admin.css', __FILE__), false, '1.0.0', 'all');
    wp_enqueue_style( 'rdv360_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'rdv360_admin_style' );
