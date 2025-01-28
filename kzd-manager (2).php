<?php
/**
 * Plugin Name: KZD Manager
 * Description: Plugin voor autodemontagebedrijven om de KZD-norm te raadplegen en documenten te beheren.
 * Version: 1.5
 * Author: Julian Groenendijk
 */

// Begin Deel 1: Plugin Header, Class Definitie en Constructor
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class KZD_Manager
 *
 * Hoofdklasse voor de KZD Manager plugin.
 */
class KZD_Manager {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        // add_action( 'init', array( $this, 'register_taxonomy' ) ); // Verwijderd: registratie van de categorie taxonomie
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        // add_action( 'wp_ajax_kzd_manager_add_category', array( $this, 'ajax_add_category' ) ); // Verwijderd: AJAX call voor toevoegen categorie
        // add_action( 'wp_ajax_kzd_manager_search_categories', array( $this, 'ajax_search_categories' ) ); // Verwijderd: AJAX call voor zoeken categorieën
        add_action( 'wp_ajax_kzd_manager_delete_kaart', array( $this, 'ajax_delete_kaart' ) );
        add_action( 'wp_ajax_kzd_manager_get_kaarten', array( $this, 'ajax_get_kaarten_callback' ) );
        add_action( 'wp_ajax_kzd_manager_get_documenten', array( $this, 'ajax_get_documenten' ) );
        add_action( 'wp_ajax_kzd_manager_upload_files', array( $this, 'ajax_upload_files' ) );
        add_action( 'wp_ajax_nopriv_kzd_manager_upload_files', array( $this, 'ajax_upload_files' ) );
        add_action( 'wp_ajax_kzd_manager_delete_attachment', array( $this, 'ajax_delete_attachment' ) );
        add_action( 'wp_ajax_kzd_manager_archiveer_attachment', array( $this, 'ajax_archiveer_attachment' ) );
        add_action( 'wp_ajax_kzd_manager_dearchiveer_attachment', array( $this, 'ajax_dearchiveer_attachment' ) );
        add_action( 'wp_ajax_kzd_manager_download_file', array( $this, 'ajax_download_file' ) );
        // add_action( 'admin_post_kzd_manager_add_category', array( $this, 'process_add_category_form' ) ); // Verwijderd: Verwerking toevoegen categorie formulier
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    }
// Einde Deel 1: Plugin Header, Class Definitie en Constructor
// Begin Deel 2: register_post_type()
    /**
     * Registreer custom post type 'kzd_kaart'.
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x( 'Kaarten', 'Post Type General Name', 'kzd-manager' ),
            'singular_name'         => _x( 'Kaart', 'Post Type Singular Name', 'kzd-manager' ),
            'menu_name'             => __( 'KZD Manager', 'kzd-manager' ),
            'name_admin_bar'        => __( 'Kaart', 'kzd-manager' ),
            'archives'              => __( 'Kaart Archief', 'kzd-manager' ),
            'attributes'            => __( 'Kaart Attributen', 'kzd-manager' ),
            'parent_item_colon'     => __( 'Parent Kaart:', 'kzd-manager' ),
            'all_items'             => __( 'Alle Kaarten', 'kzd-manager' ),
            'add_new_item'          => __( 'Nieuwe Kaart Toevoegen', 'kzd-manager' ),
            'add_new'               => __( 'Nieuwe Toevoegen', 'kzd-manager' ),
            'new_item'              => __( 'Nieuwe Kaart', 'kzd-manager' ),
            'edit_item'             => __( 'Bewerk Kaart', 'kzd-manager' ),
            'update_item'           => __( 'Update Kaart', 'kzd-manager' ),
            'view_item'             => __( 'Bekijk Kaart', 'kzd-manager' ),
            'view_items'            => __( 'Bekijk Kaarten', 'kzd-manager' ),
            'search_items'          => __( 'Zoek Kaarten', 'kzd-manager' ),
            'not_found'             => __( 'Niet gevonden', 'kzd-manager' ),
            'not_found_in_trash'    => __( 'Niet gevonden in prullenbak', 'kzd-manager' ),
            'featured_image'        => __( 'Featured Afbeelding', 'kzd-manager' ),
            'set_featured_image'    => __( 'Stel Featured Afbeelding in', 'kzd-manager' ),
            'remove_featured_image' => __( 'Verwijder Featured Afbeelding', 'kzd-manager' ),
            'use_featured_image'    => __( 'Gebruik als Featured Afbeelding', 'kzd-manager' ),
            'insert_into_item'      => __( 'Voeg in in kaart', 'kzd-manager' ),
            'uploaded_to_this_item' => __( 'Geüpload naar deze kaart', 'kzd-manager' ),
            'items_list'            => __( 'Kaarten Lijst', 'kzd-manager' ),
            'items_list_navigation' => __( 'Kaarten Lijst Navigatie', 'kzd-manager' ),
            'filter_items_list'     => __( 'Filter Kaarten Lijst', 'kzd-manager' ),
        );
        $args = array(
            'label'               => __( 'Kaart', 'kzd-manager' ),
            'description'         => __( 'Kaarten voor KZD Manager', 'kzd-manager' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'custom-fields' ),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => 'kzd-manager', // Show under the main KZD Manager menu
            'menu_position'       => 5,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
        );
        register_post_type( 'kzd_kaart', $args );
    }
// Einde Deel 2: register_post_type()
// Begin Deel 3: Menu-items en registratie van de instellingen
    /**
     * Voegt admin menu items toe.
     */
    public function admin_menu() {
        add_menu_page(
            __( 'KZD Manager', 'kzd-manager' ),
            __( 'KZD Manager', 'kzd-manager' ),
            'manage_options',
            'kzd-manager',
            array( $this, 'render_pdf_viewer_page' ),
            'dashicons-book-alt',
            20
        );
        add_submenu_page(
            'kzd-manager',
            __( 'KZD Web Norm', 'kzd-manager' ),
            __( 'KZD Web Norm', 'kzd-manager' ),
            'manage_options',
            'kzd-manager-web-norm',
            array( $this, 'render_web_norm_page' )
        );
        add_submenu_page(
            'kzd-manager',
            __( 'Kaarten', 'kzd-manager' ),
            __( 'Kaarten', 'kzd-manager' ),
            'manage_options',
            'kzd-manager-show-kaarten',
            array( $this, 'render_show_kaarten_page' )
        );
        add_submenu_page(
            'kzd-manager',
            __( 'Nieuwe kaart', 'kzd-manager' ),
            __( 'Nieuwe kaart', 'kzd-manager' ),
            'manage_options',
            'kzd-manager-create-kaart',
            array( $this, 'render_create_kaart_page' )
        );
        add_submenu_page(
            'kzd-manager',
            __( 'Instellingen', 'kzd-manager' ),
            __( 'Instellingen', 'kzd-manager' ),
            'manage_options',
            'kzd-manager-settings',
            array( $this, 'render_settings_page' )
        );
        add_submenu_page(
            null, // Hidden submenu item
            __( 'Documenten Popup', 'kzd-manager' ),
            __( 'Documenten Popup', 'kzd-manager' ),
            'manage_options',
            'kzd-manager-documenten-popup',
            array( $this, 'render_documenten_popup_page' )
        );
        add_submenu_page(
            null, // Hidden submenu
            __( 'Kaart bewerken', 'kzd-manager' ),
            __( 'Kaart bewerken', 'kzd-manager' ),
            'manage_options',
            'kzd-manager-edit-kaart',
            array( $this, 'render_edit_kaart_page' )
        );
    }

    /**
     * Registreer plugin instellingen.
     */
    public function register_settings() {
        register_setting( 'kzd_manager_settings_group', 'kzd_manager_kaarten_per_rij', array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 3,
        ) );
    }
// Einde Deel 3: Menu-items en registratie van de instellingen
// Begin Deel 4: render_pdf_viewer_page() en render_web_norm_page()
    /**
     * Render de PDF viewer pagina.
     */
public function render_pdf_viewer_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'KZD Norm - PDF Viewer', 'kzd-manager' ); ?></h1>
        <div id="kzd-pdf-viewer">
            <iframe src="<?php echo esc_url( plugins_url( 'pdfjs/web/viewer.html?file=' . urlencode( plugins_url( '221123-Norm-KwaliteitsZorg-Demontage.pdf', __FILE__ ) ) ) ); ?>" width="100%" height="800px"></iframe>
        </div>
    </div>
    <?php
}

    /**
     * Render de web norm pagina.
     */
public function render_web_norm_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'KZD Norm - Webversie', 'kzd-manager' ); ?></h1>
        <div id="kzd-web-norm">
            <input type="text" id="search-box" placeholder="<?php esc_attr_e( 'Zoeken in de KZD-norm...', 'kzd-manager' ); ?>">
            <button id="search-button"><?php _e( 'Zoeken', 'kzd-manager' ); ?></button>

            <div id="toc">
                <h2><?php _e( 'Inhoudsopgave', 'kzd-manager' ); ?></h2>
                <ul>
                    </ul>
            </div>

            <div id="kzd-content">
                <?php
                // Gebruik plugin_dir_path() met de juiste bestandsnaam
                $kzd_norm_content = file_get_contents( plugin_dir_path( __FILE__ ) . '221123-Norm-KwaliteitsZorg-Demontage.html' );

                // Controleer of het bestand is ingeladen
                if ( empty( $kzd_norm_content ) ) {
                    echo '<p>' . esc_html__( 'Fout bij het laden van de KZD-norm. Controleer of het bestand 221123-Norm-KwaliteitsZorg-Demontage.html bestaat in de plugin map.', 'kzd-manager' ) . '</p>';
                } else {
                    // Laad de inhoud van het HTML-bestand in de div
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            document.getElementById("kzd-content").innerHTML = `' . addslashes( $kzd_norm_content ) . '`;
                        });
                    </script>';
                }
                ?>
            </div>
        </div>
    <?php
}
// Einde Deel 4: render_pdf_viewer_page() en render_web_norm_page()
// Begin Deel 5: render_create_kaart_page()
/**
 * Render de pagina om een nieuwe kaart aan te maken.
 */
public function render_create_kaart_page() {
    // Controleer of de gebruiker de juiste rechten heeft
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Je hebt geen toestemming om deze pagina te bekijken.', 'kzd-manager' ) );
    }

    $message = '';

    if ( isset( $_POST['submit_kaart'] ) && check_admin_referer( 'kzd_manager_create_kaart' ) ) {
        // Verwerk formulierdata
        $titel       = isset( $_POST['titel'] ) ? sanitize_text_field( $_POST['titel'] ) : '';
        $notities    = isset( $_POST['notities'] ) ? sanitize_textarea_field( $_POST['notities'] ) : '';
        $vervaldatum = isset( $_POST['vervaldatum'] ) ? sanitize_text_field( $_POST['vervaldatum'] ) : '';
        $categorie   = isset( $_POST['categorie_id'] ) ? (int) $_POST['categorie_id'] : 0;

        // Valideer de ingevoerde data
        $errors = array();
        if ( empty( $titel ) ) {
            $errors[] = __( 'Titel mag niet leeg zijn.', 'kzd-manager' );
        }

        if ( empty( $vervaldatum ) ) {
            $errors[] = __( 'Vervaldatum is verplicht.', 'kzd-manager' );
        }

        // Voeg hier meer validatie toe indien nodig

        if ( empty( $errors ) ) {
            // Sla de kaart op in de database
            $kaart_id = wp_insert_post( array(
                'post_type'    => 'kzd_kaart',
                'post_title'   => $titel,
                'post_content' => $notities,
                'post_status'  => 'publish',
                'meta_input'   => array(
                    'vervaldatum' => $vervaldatum,
                ),
            ) );

            // Koppel de kaart aan de categorie (verwijderd in deze context)

            if ( $kaart_id && ! is_wp_error( $kaart_id ) ) {

                // Verwerk de geüploade bestanden
                if ( ! empty( $_FILES['kzd_upload']['name'][0] ) ) {
                    $this->handle_file_upload( $kaart_id, 'kzd_upload' );
                }

                // Redirect naar de pagina met kaarten
                wp_safe_redirect( admin_url( 'admin.php?page=kzd-manager-show-kaarten&message=created' ) );
                exit;
            } else {
                $message = '<div class="notice notice-error"><p>' . __( 'Er is een fout opgetreden bij het opslaan van de kaart.', 'kzd-manager' ) . '</p></div>';
            }
        } else {
            $message = '<div class="notice notice-error"><ul>';
            foreach ( $errors as $error ) {
                $message .= '<li>' . $error . '</li>';
            }
            $message .= '</ul></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1><?php _e( 'Nieuwe kaart aanmaken', 'kzd-manager' ); ?></h1>

        <?php echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <form method="post" action="" enctype="multipart/form-data">
            <?php wp_nonce_field( 'kzd_manager_create_kaart' ); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="titel"><?php _e( 'Titel:', 'kzd-manager' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="titel" name="titel" value="<?php echo isset( $_POST['titel'] ) ? esc_attr( $_POST['titel'] ) : ''; ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="notities"><?php _e( 'Notities:', 'kzd-manager' ); ?></label>
                        </th>
                        <td>
                            <textarea id="notities" name="notities" rows="5" class="regular-text"><?php echo isset( $_POST['notities'] ) ? esc_textarea( $_POST['notities'] ) : ''; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="vervaldatum"><?php _e( 'Vervaldatum:', 'kzd-manager' ); ?></label>
                        </th>
                        <td>
                            <input type="date" id="vervaldatum" name="vervaldatum" value="<?php echo isset( $_POST['vervaldatum'] ) ? esc_attr( $_POST['vervaldatum'] ) : ''; ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="kzd_upload"><?php _e( 'Bijlage:', 'kzd-manager' ); ?></label>
                        </th>
                        <td>
                            <input type="file" id="kzd_upload" name="kzd_upload[]" multiple="multiple">
                            <p class="description"><?php _e( 'Selecteer één of meerdere bestanden (PDF, afbeeldingen, etc.).', 'kzd-manager' ); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <input type="submit" name="submit_kaart" value="<?php esc_attr_e( 'Kaart aanmaken', 'kzd-manager' ); ?>" class="button button-primary">
        </form>
    </div>
    <?php
}
// Einde Deel 5: render_create_kaart_page()
// Begin Deel 6: render_settings_page()
    /**
     * Render de instellingen pagina.
     */
    public function render_settings_page() {
        // Controleer of de gebruiker de juiste rechten heeft
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Je hebt geen toestemming om deze pagina te bekijken.', 'kzd-manager' ) );
        }
        ?>
        <div class="wrap">
            <h1><?php _e( 'KZD Manager Instellingen', 'kzd-manager' ); ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields( 'kzd_manager_settings_group' ); ?>
                <?php do_settings_sections( 'kzd_manager_settings_page' ); ?>

                <h2><?php _e( 'Kaartenweergave', 'kzd-manager' ); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Aantal kaarten per rij:', 'kzd-manager' ); ?></th>
                        <td>
                            <select name="kzd_manager_kaarten_per_rij">
                                <?php
                                $selected = get_option( 'kzd_manager_kaarten_per_rij', 3 );
                                for ( $i = 1; $i <= 6; $i++ ) {
                                    echo '<option value="' . esc_attr( $i ) . '" ' . selected( $selected, $i, false ) . '>' . esc_html( $i ) . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <?php
                // Verwijderd: de sectie met de instellingen voor de categorieën
                ?>

                <?php submit_button(); ?>
            </form>
        </div>

        <?php
    }
// Einde Deel 6: render_settings_page()
// Begin Deel 7: Verwijderde code (categorieën)
    // Verwijderd: Geen categorieën meer nodig.
// Einde Deel 7: Verwijderde code (categorieën)
// Begin Deel 8: render_show_kaarten_page()
    /**
     * Render de pagina met de kaarten.
     */
    public function render_show_kaarten_page() {
        // Controleer of de gebruiker de juiste rechten heeft
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Je hebt geen toestemming om deze pagina te bekijken.', 'kzd-manager' ) );
        }

        $message = '';

        // Controleer of er een kaart succesvol is aangemaakt
        if ( isset( $_GET['message'] ) && $_GET['message'] == 'created' ) {
            $message = '<div class="notice notice-success is-dismissible"><p>' . __( 'Kaart succesvol aangemaakt.', 'kzd-manager' ) . '</p></div>';
        }

        // Controleer of er een kaart succesvol is bijgewerkt
        if ( isset( $_GET['message'] ) && $_GET['message'] == 'updated' ) {
            $message = '<div class="notice notice-success is-dismissible"><p>' . __( 'Kaart succesvol bijgewerkt.', 'kzd-manager' ) . '</p></div>';
        }

        // Controleer of er een kaart succesvol is verwijderd
        if ( isset( $_GET['message'] ) && $_GET['message'] == 'deleted' ) {
            $message = '<div class="notice notice-success is-dismissible"><p>' . __( 'Kaart succesvol verwijderd.', 'kzd-manager' ) . '</p></div>';
        }

        // Haal de filtercategorie op
        $selected_category = isset( $_GET['categorie'] ) ? (int) $_GET['categorie'] : '';
        ?>
        <div class="wrap">
            <h1><?php _e( 'Kaarten', 'kzd-manager' ); ?></h1>

            <div id="message-container">
                <?php echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>

            <div id="kaarten_container" class="kaarten-grid">
                <?php
        $args = array(
        'post_type'      => 'kzd_kaart',
         'posts_per_page' => - 1, // Alle kaarten tonen
            );

                $kaarten = new WP_Query( $args );

                if ( $kaarten->have_posts() ) {
                    while ( $kaarten->have_posts() ) {
                        $kaarten->the_post();
                        $vervaldatum           = get_post_meta( get_the_ID(), 'vervaldatum', true );
                        $dagen_tot_vervaldatum = floor( ( strtotime( $vervaldatum ) - time() ) / ( 60 * 60 * 24 ) );
                        $rand_kleur            = '';

                        if ( $dagen_tot_vervaldatum <= 0 ) {
                            $rand_kleur = 'red';
                        } elseif ( $dagen_tot_vervaldatum <= 7 ) {
                            $rand_kleur = 'orange';
                        } else {
                            $rand_kleur = 'green';
                        }
                        ?>
                        <div class="kzd-card" data-kaart-id="<?php echo get_the_ID(); ?>" data-vervaldatum="<?php echo esc_attr( $vervaldatum ); ?>" style="border-color: <?php echo esc_attr( $rand_kleur ); ?>">
                            <h3><?php the_title(); ?></h3>
                            <div class="kzd-card-content"><?php the_content(); ?></div>
                            <p class="vervaldatum">
                                <?php
                                $vervaldatum_timestamp = strtotime( $vervaldatum );
                                if ( $vervaldatum_timestamp !== false ) {
                                    $formatted_vervaldatum = date( 'd-m-Y', $vervaldatum_timestamp );
                                    printf( esc_html__( 'Vervaldatum: %s', 'kzd-manager' ), esc_html( $formatted_vervaldatum ) );
                                } else {
                                    echo esc_html__( 'Vervaldatum: Ongeldig formaat', 'kzd-manager' );
                                }
                                ?>
                            </p>
                            <div class="kzd-card-actions">
                                <button class="button documenten-knop" data-kaart-id="<?php echo get_the_ID(); ?>"><?php _e( 'Documenten', 'kzd-manager' ); ?></button>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=kzd-manager-edit-kaart&kaart_id=' . get_the_ID() ) ); ?>" class="button bewerk-knop"><?php _e( 'Bewerken', 'kzd-manager' ); ?></a>
                                <button class="button button-delete wis-knop" data-kaart-id="<?php echo get_the_ID(); ?>"><?php _e( 'Wissen', 'kzd-manager' ); ?></button>
                                <button class="button upload-knop verborgen" data-kaart-id="<?php echo get_the_ID(); ?>"><?php _e( 'Uploaden', 'kzd-manager' ); ?></button>
                            </div>
                        </div>
                        <?php
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p>' . esc_html__( 'Geen kaarten gevonden.', 'kzd-manager' ) . '</p>';
                }
                ?>
            </div>
        </div>
        <div id="upload-popup" class="hidden">
            <div class="upload-popup-content">
                <h2 id="upload-popup-title"></h2>
                <input type="hidden" id="upload-kaart-id" value="">
                <input type="file" id="upload-file" name="upload_file[]" multiple="multiple">
                <div id="upload-file-list">
                    </div>
                <button class="button button-primary" id="upload-button"><?php _e( 'Uploaden', 'kzd-manager' ); ?></button>
                <button class="button" id="upload-cancel"><?php _e( 'Annuleren', 'kzd-manager' ); ?></button>
            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                // Filter functionaliteit
                $('#categorie_filter').change(function() {
                    const selectedCategory = $(this).val();
                    const url = new URL(window.location.href);

                    if (selectedCategory) {
                        url.searchParams.set('categorie', selectedCategory);
                    } else {
                        url.searchParams.delete('categorie');
                    }

                    $.ajax({
                        url: url.toString(),
                        beforeSend: function() {
                            // Voeg hier eventueel een laad animatie toe
                            $('#kaarten_container').html('<p>Kaarten laden...</p>');
                        },
                        success: function(response) {
                            $('#kaarten_container').html($(response).find('#kaarten_container').html());

                            // Update de URL in de adresbalk
                            window.history.pushState({path:url.toString()},'',url.toString());
                        },
                        error: function() {
                            $('#kaarten_container').html('<p>Er is een fout opgetreden bij het laden van de kaarten.</p>');
                        }
                    });
                });

                // Functie om de kaarten te herladen
                function reloadKaarten() {
                    $.ajax({
                        url: ajaxurl,
                        data: {
                            action: 'kzd_manager_get_kaarten',
                        },
                        success: function(response) {
                            // Vervang de inhoud van de kaarten-container
                            $('#kaarten_container').html(response);

                            // Herstel de "Documenten" knoppen
                            $('.kzd-card').each(function() {
                                const kaartId = $(this).data('kaart-id');
                                $(this).find('.kzd-card-actions .upload-knop').replaceWith('<button class="button documenten-knop" data-kaart-id="' + kaartId + '"><?php _e( 'Documenten', 'kzd-manager' ); ?></button>');
                            });
                        },
                        error: function() {
                            $('#kaarten_container').html('<p><?php echo esc_js( __( 'Er is een fout opgetreden bij het laden van de kaarten.', 'kzd-manager' ) ); ?></p>');
                        }
                    });
                }

                // Upload popup functionaliteit
                let clickedCardId = 0;
                $(document).on('click', '.upload-knop', function(e) {
                    e.preventDefault();
                    clickedCardId = $(this).closest('.kzd-card').data('kaart-id');
                    $('#upload-kaart-id').val(clickedCardId);
                    $('#upload-popup').removeClass('hidden');
                    const cardTitle = $(this).closest('.kzd-card').find('h3').text();
                    $('#upload-popup-title').text('Upload bijlages voor: ' + cardTitle);
                    
                    // Wis de bestaande bestandslijst
                    $('#upload-file-list').empty();
                });

                $('#upload-cancel').click(function(e) {
                    e.preventDefault();
                    $('#upload-popup').addClass('hidden');
                });

                $('#upload-button').click(function(e) {
                    e.preventDefault();
                    const formData = new FormData();
                    const files = $('#upload-file')[0].files;
                    const kaartId = $('#upload-kaart-id').val();

                    for (let i = 0; i < files.length; i++) {
                        formData.append('upload_file[]', files[i]);
                    }

                    formData.append('action', 'kzd_manager_upload_files');
                    formData.append('kaart_id', kaartId);
                    formData.append('_ajax_nonce', '<?php echo wp_create_nonce( 'upload_files_nonce' ); ?>');

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                alert('<?php echo esc_js( __( 'Bestanden succesvol geüpload.', 'kzd-manager' ) ); ?>');
                                $('#upload-popup').addClass('hidden');
                                reloadKaarten(); // Herlaad de kaarten na succesvolle upload
                            } else {
                                alert(response.data.message);
                            }
                        },
                        error: function() {
                            alert('<?php echo esc_js( __( 'Er is een fout opgetreden bij het uploaden van de bestanden.', 'kzd-manager' ) ); ?>');
                        }
                    });
                });

                // Herlaad kaarten na aanmaken/bewerken kaart d.m.v. custom event
                $(document).on('kaartBewerkt', function() {
                    reloadKaarten();
                });

                // Verwijder functionaliteit
                $(document).on('click', '.wis-knop', function(e) {
                    e.preventDefault();
                    const kaartId = $(this).data('kaart-id');
                    const cardElement = $(this).closest('.kzd-card');

                    if (confirm('<?php echo esc_js( __( 'Weet je zeker dat je deze kaart wilt verwijderen?', 'kzd-manager' ) ); ?>')) {
                        // Voeg de class 'verwijderen' toe om de transitie te starten (optioneel, voor visueel effect)
                        cardElement.addClass('verwijderen');
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'kzd_manager_delete_kaart',
                                kaart_id: kaartId,
                                _ajax_nonce: '<?php echo wp_create_nonce( 'delete_kaart_nonce' ); ?>'
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Verwijder de kaart na een korte vertraging (bijv. 0.2 seconden)
                                    setTimeout(function() {
                                        cardElement.remove();
                                        // Toon de succesmelding
                                        alert('<?php echo esc_js( __( 'Kaart succesvol verwijderd.', 'kzd-manager' ) ); ?>');
                                        // Herlaad de kaarten na succesvolle verwijdering
                                        reloadKaarten();
                                    }, 200);
                                } else {
                                    // Herstel de kaart bij een fout
                                    cardElement.removeClass('verwijderen');
                                    alert(response.data.message);
                                }
                            },
                            error: function() {
                                // Herstel de kaart bij een fout
                                cardElement.removeClass('verwijderen');
                                alert('<?php echo esc_js( __( 'Er is een fout opgetreden bij het verwijderen van de kaart.', 'kzd-manager' ) ); ?>');
                            }
                        });
                    } else {
                        // Herstel de kaart als de gebruiker annuleert
                        cardElement.removeClass('verwijderen');
                    }
                });

                // Documenten popup functionaliteit
                $(document).on('click', '.documenten-knop', function(e) {
                    e.preventDefault();
                    const kaartId = $(this).data('kaart-id');
                    const cardTitle = $(this).closest('.kzd-card').find('h3').text();
                    const popupUrl = `<?php echo admin_url( 'admin.php?page=kzd-manager-documenten-popup&kaart_id=' ); ?>${kaartId}`;

                    // Open de popup
                    window.open(popupUrl, 'documentenPopup', 'width=800,height=600,resizable=yes,scrollbars=yes');
                });
            });
        </script>
        <?php
    }
// =========== EINDE DEEL 8 ===========
// Begin Deel 9: ajax_get_kaarten_callback()
    /**
     * AJAX callback om de kaarten op te halen
     */
    public function ajax_get_kaarten_callback()
    {
        $args = array(
            'post_type'      => 'kzd_kaart',
            'posts_per_page' => - 1, // Alle kaarten tonen
        );

        $kaarten = new WP_Query( $args );

        if ( $kaarten->have_posts() ) {
            while ( $kaarten->have_posts() ) {
                $kaarten->the_post();
                $vervaldatum           = get_post_meta( get_the_ID(), 'vervaldatum', true );
                $dagen_tot_vervaldatum = floor( ( strtotime( $vervaldatum ) - time() ) / ( 60 * 60 * 24 ) );
                $rand_kleur            = '';

                if ( $dagen_tot_vervaldatum <= 0 ) {
                    $rand_kleur = 'red';
                } elseif ( $dagen_tot_vervaldatum <= 7 ) {
                    $rand_kleur = 'orange';
                } else {
                    $rand_kleur = 'green';
                }
                ?>
                <div class="kzd-card" data-kaart-id="<?php echo get_the_ID(); ?>" data-vervaldatum="<?php echo esc_attr( $vervaldatum ); ?>" style="border-color: <?php echo esc_attr( $rand_kleur ); ?>">
                    <h3><?php the_title(); ?></h3>
                    <div class="kzd-card-content"><?php the_content(); ?></div>
                    <p class="vervaldatum">
                        <?php
                        $vervaldatum_timestamp = strtotime( $vervaldatum );
                        if ( $vervaldatum_timestamp !== false ) {
                            $formatted_vervaldatum = date( 'd-m-Y', $vervaldatum_timestamp );
                            printf( esc_html__( 'Vervaldatum: %s', 'kzd-manager' ), esc_html( $formatted_vervaldatum ) );
                        } else {
                            echo esc_html__( 'Vervaldatum: Ongeldig formaat', 'kzd-manager' );
                        }
                        ?>
                    </p>
                    <div class="kzd-card-actions">
                        <button class="button documenten-knop" data-kaart-id="<?php echo get_the_ID(); ?>"><?php _e( 'Documenten', 'kzd-manager' ); ?></button>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=kzd-manager-edit-kaart&kaart_id=' . get_the_ID() ) ); ?>" class="button bewerk-knop"><?php _e( 'Bewerken', 'kzd-manager' ); ?></a>
                        <button class="button button-delete wis-knop" data-kaart-id="<?php echo get_the_ID(); ?>"><?php _e( 'Wissen', 'kzd-manager' ); ?></button>
                    </div>
                </div>
                <?php
            }
            wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__( 'Geen kaarten gevonden.', 'kzd-manager' ) . '</p>';
        }

        wp_die();
    }
// Einde Deel 9: ajax_get_kaarten_callback()
// Begin Deel 10: render_edit_kaart_page()
   /**
    * Render de pagina om een kaart te bewerken.
    */
   public function render_edit_kaart_page() {
       // Controleer of de gebruiker de juiste rechten heeft
       if ( ! current_user_can( 'manage_options' ) ) {
           wp_die( esc_html__( 'Je hebt geen toestemming om deze pagina te bekijken.', 'kzd-manager' ) );
       }

       $kaart_id = isset( $_GET['kaart_id'] ) ? (int) $_GET['kaart_id'] : 0;
       $kaart    = get_post( $kaart_id );

       // Controleer of de kaart bestaat
       if ( ! $kaart || $kaart->post_type !== 'kzd_kaart' ) {
           wp_die( esc_html__( 'Ongeldige kaart ID.', 'kzd-manager' ) );
       }

       $message = '';
       $errors = array();

       if ( isset( $_POST['submit_kaart'] ) && check_admin_referer( 'kzd_manager_edit_kaart_' . $kaart_id ) ) {
           // Verwerk formulierdata
           $titel       = isset( $_POST['titel'] ) ? sanitize_text_field( $_POST['titel'] ) : '';
           $notities    = isset( $_POST['notities'] ) ? sanitize_textarea_field( $_POST['notities'] ) : '';
           $vervaldatum = isset( $_POST['vervaldatum'] ) ? sanitize_text_field( $_POST['vervaldatum'] ) : '';

           // Valideer de ingevoerde data
           if ( empty( $titel ) ) {
               $errors[] = __( 'Titel mag niet leeg zijn.', 'kzd-manager' );
           }

           if ( empty( $vervaldatum ) ) {
               $errors[] = __( 'Vervaldatum is verplicht.', 'kzd-manager' );
           }

           // Voeg hier meer validatie toe indien nodig

           if ( empty( $errors ) ) {
               // Update de kaart in de database
               $update_result = wp_update_post( array(
                   'ID'           => $kaart_id,
                   'post_title'   => $titel,
                   'post_content' => $notities,
                   'meta_input'   => array(
                       'vervaldatum' => $vervaldatum,
                   ),
               ) );

               if ( $update_result && ! is_wp_error( $update_result ) ) {
                   // Verwerk de geüploade bestanden
                   if ( ! empty( $_FILES['kzd_upload']['name'][0] ) ) {
                       $this->handle_file_upload( $kaart_id, 'kzd_upload' );
                   }

                   // Toon de succesmelding en redirect
                   $message = '<div class="notice notice-success is-dismissible"><p>' . __( 'Kaart succesvol bijgewerkt.', 'kzd-manager' ) . '</p></div>';
                   echo '<script type="text/javascript">
                       jQuery(document).ready(function($) {
                           setTimeout(function() {
                               window.location.href = "' . admin_url( 'admin.php?page=kzd-manager-show-kaarten&message=updated' ) . '";
                           }, 0); // Direct doorsturen naar de overzichtspagina
                       });
                   </script>';
               } else {
                   $message = '<div class="notice notice-error"><p>' . __( 'Er is een fout opgetreden bij het bijwerken van de kaart.', 'kzd-manager' ) . '</p></div>';
               }
           } else {
               $message = '<div class="notice notice-error"><ul>';
               foreach ( $errors as $error ) {
                   $message .= '<li>' . $error . '</li>';
               }
               $message .= '</ul></div>';
           }
       }

       ?>
       <div class="wrap">
           <h1><?php _e( 'Kaart bewerken', 'kzd-manager' ); ?></h1>

           <?php echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

           <form method="post" action="" enctype="multipart/form-data">
               <?php wp_nonce_field( 'kzd_manager_edit_kaart_' . $kaart_id ); ?>
               <table class="form-table">
                   <tbody>
                       <tr>
                           <th scope="row">
                               <label for="titel"><?php _e( 'Titel:', 'kzd-manager' ); ?></label>
                           </th>
                           <td>
                               <input type="text" id="titel" name="titel" value="<?php echo esc_attr( $kaart->post_title ); ?>" class="regular-text">
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">
                               <label for="notities"><?php _e( 'Notities:', 'kzd-manager' ); ?></label>
                           </th>
                           <td>
                               <textarea id="notities" name="notities" rows="5" class="regular-text"><?php echo esc_textarea( $kaart->post_content ); ?></textarea>
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">
                               <label for="vervaldatum"><?php _e( 'Vervaldatum:', 'kzd-manager' ); ?></label>
                           </th>
                           <td>
                               <input type="date" id="vervaldatum" name="vervaldatum" value="<?php echo esc_attr( get_post_meta( $kaart_id, 'vervaldatum', true ) ); ?>" class="regular-text">
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">
                               <label for="kzd_upload"><?php _e( 'Bijlage:', 'kzd-manager' ); ?></label>
                           </th>
                           <td>
                               <input type="file" id="kzd_upload" name="kzd_upload[]" multiple="multiple">
                               <p class="description"><?php _e( 'Selecteer één of meerdere bestanden (PDF, afbeeldingen, etc.).', 'kzd-manager' ); ?></p>
                           </td>
                       </tr>
                   </tbody>
               </table>

               <input type="submit" name="submit_kaart" value="<?php esc_attr_e( 'Kaart bijwerken', 'kzd-manager' ); ?>" class="button button-primary">
           </form>
       </div>
       <?php
   }
// Einde Deel 10: render_edit_kaart_page()
// Begin Deel 11: ajax_delete_kaart()
    /**
    * AJAX callback om een kaart te verwijderen.
    */
    public function ajax_delete_kaart() {
        check_ajax_referer( 'delete_kaart_nonce', '_ajax_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Je hebt niet de juiste rechten om deze actie uit te voeren.', 'kzd-manager' ) ) );
        }

        $kaart_id = isset( $_POST['kaart_id'] ) ? (int) $_POST['kaart_id'] : 0;

        if ( ! $kaart_id ) {
            wp_send_json_error( array( 'message' => __( 'Ongeldige kaart ID.', 'kzd-manager' ) ) );
        }

        $result = wp_delete_post( $kaart_id, true ); // true = force delete

        if ( $result ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( array( 'message' => __( 'Er is een fout opgetreden bij het verwijderen van de kaart.', 'kzd-manager' ) ) );
        }
    }
    // Einde Deel 11: ajax_delete_kaart()
    // Begin Deel 12: handle_file_upload()
    /**
     * Verwerkt de geüploade bestanden en koppelt ze aan de kaart.
     *
     * @param int $kaart_id De ID van de kaart waaraan de bestanden gekoppeld moeten worden.
     * @param string $field_name De naam van het uploadveld in het formulier.
     */
    private function handle_file_upload($kaart_id, $field_name)
    {
        // Controleer of er bestanden zijn geüpload
        if (empty($_FILES[$field_name]['name'][0])) {
            return;
        }

        // Inclusief de benodigde WordPress-bestanden voor bestandsuploads
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $files = $_FILES[$field_name];

        // Array om de attachment IDs op te slaan
        $attachment_ids = array();

        // Nieuwe, beveiligde map
        $upload_dir = '/home/adm01digi/kzd-documenten/';

        // Maak de map aan als deze nog niet bestaat
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // Maak de map aan met rechten 0755 (aanpassen indien nodig)
        }

        // Loop door alle geüploade bestanden
        foreach ($files['name'] as $key => $value) {
            if ($files['name'][$key]) {
                $file = array(
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                );

                // Upload het bestand naar de beveiligde map
                $file_upload = wp_handle_upload($file, array('test_form' => false, 'upload_dir' => $upload_dir));

                if (isset($file_upload['error'])) {
                    // Handel eventuele uploadfouten af
                    $message = '<div class="notice notice-error"><p>' . sprintf( __( 'Fout bij het uploaden van bestand %s: %s', 'kzd-manager' ), esc_html( $file['name'] ), esc_html( $file_upload['error'] ) ) . '</p></div>';
                    echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    continue;
                }

                // Maak een attachment aan voor het geüploade bestand
                $attachment = array(
                    'guid' => $file_upload['url'],
                    'post_mime_type' => $file_upload['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_upload['file'])),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                // Voeg de attachment toe aan de WordPress media bibliotheek
                $attachment_id = wp_insert_attachment($attachment, $file_upload['file'], $kaart_id);

                if (!is_wp_error($attachment_id)) {
                    // Genereer attachment metadata en update de database
                    $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_upload['file']);
                    wp_update_attachment_metadata($attachment_id, $attachment_data);

                    // Voeg de attachment ID toe aan de array
                    $attachment_ids[] = $attachment_id;
                }
            }
        }

// Koppel de bestanden aan de kaart
if ( ! empty( $attachment_ids ) ) {
    // Haal bestaande bijlages op
    $existing_attachments = get_post_meta( $kaart_id, 'kzd_attachments', true );

    // Voeg de nieuwe bijlages toe aan de bestaande
    if ( is_array( $existing_attachments ) ) {
        $attachment_ids = array_merge( $existing_attachments, $attachment_ids );
    }

    // Verwijder duplicaten
    $attachment_ids = array_unique( $attachment_ids );

    // Sla de bijgewerkte lijst op
    update_post_meta( $kaart_id, 'kzd_attachments', $attachment_ids );
}
    }
// Einde Deel 12: handle_file_upload()
// Begin Deel 13: AJAX-functies voor bijlage beheer
    /**
     * AJAX callback om bestanden te uploaden en aan een kaart te koppelen.
     */
    public function ajax_upload_files() {
        check_ajax_referer( 'upload_files_nonce', '_ajax_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Je hebt niet de juiste rechten om deze actie uit te voeren.', 'kzd-manager' ) ) );
        }

        $kaart_id = isset( $_POST['kaart_id'] ) ? (int) $_POST['kaart_id'] : 0;

        if ( ! $kaart_id ) {
            wp_send_json_error( array( 'message' => __( 'Ongeldige kaart ID.', 'kzd-manager' ) ) );
        }

        if ( empty( $_FILES['upload_file'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Geen bestanden geüpload.', 'kzd-manager' ) ) );
        }

        // Inclusief de benodigde WordPress-bestanden voor bestandsuploads
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $files = $_FILES['upload_file'];

        // Array om de attachment IDs op te slaan
        $attachment_ids = array();

        // Nieuwe, beveiligde map
        $upload_dir = '/home/adm01digi/kzd-documenten/';

        // Loop door alle geüploade bestanden
        foreach ( $files['name'] as $key => $value ) {
            if ( $files['name'][ $key ] ) {
                $file = array(
                    'name'     => $files['name'][ $key ],
                    'type'     => $files['type'][ $key ],
                    'tmp_name' => $files['tmp_name'][ $key ],
                    'error'    => $files['error'][ $key ],
                    'size'     => $files['size'][ $key ]
                );

                // Upload het bestand naar de beveiligde map
                $file_upload = wp_handle_upload( $file, array( 'test_form' => false, 'upload_dir' => $upload_dir ) );

                if ( isset( $file_upload['error'] ) ) {
                    wp_send_json_error( array( 'message' => sprintf( __( 'Fout bij het uploaden van bestand %s: %s', 'kzd-manager' ), esc_html( $file['name'] ), esc_html( $file_upload['error'] ) ) ) );
                }

                // Gebruik de bestandsnaam als titel
                $attachment_title = $file['name'];

                // Maak een attachment aan voor het geüploade bestand
                $attachment = array(
                    'guid'           => $file_upload['url'],
                    'post_mime_type' => $file_upload['type'],
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $attachment_title ) ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                // Voeg de attachment toe aan de WordPress media bibliotheek
                $attachment_id = wp_insert_attachment( $attachment, $file_upload['file'], $kaart_id );

                if ( ! is_wp_error( $attachment_id ) ) {
                    // Genereer attachment metadata en update de database
                    $attachment_data = wp_generate_attachment_metadata( $attachment_id, $file_upload['file'] );
                    wp_update_attachment_metadata( $attachment_id, $attachment_data );

                    // Voeg de attachment ID toe aan de array
                    $attachment_ids[] = $attachment_id;
                }
            }
        }

// Koppel de bestanden aan de kaart
if ( ! empty( $attachment_ids ) ) {
    // Haal bestaande bijlages op
    $existing_attachments = get_post_meta( $kaart_id, 'kzd_attachments', true );

    // Voeg de nieuwe bijlages toe aan de bestaande
    if ( is_array( $existing_attachments ) ) {
        $attachment_ids = array_merge( $existing_attachments, $attachment_ids );
    }

    // Verwijder duplicaten
    $attachment_ids = array_unique( $attachment_ids );

    // Sla de bijgewerkte lijst op
    update_post_meta( $kaart_id, 'kzd_attachments', $attachment_ids );
}

        wp_send_json_success();
    }

    /**
     * AJAX callback om een bijlage te verwijderen.
     */
    public function ajax_delete_attachment() {
        check_ajax_referer( 'delete_attachment_nonce', '_ajax_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Je hebt niet de juiste rechten om deze actie uit te voeren.', 'kzd-manager' ) ) );
        }

        $attachment_id = isset( $_POST['attachment_id'] ) ? (int) $_POST['attachment_id'] : 0;

        if ( ! $attachment_id ) {
            wp_send_json_error( array( 'message' => __( 'Ongeldige bijlage ID.', 'kzd-manager' ) ) );
        }

        // Verwijder de attachment
        $result = wp_delete_attachment( $attachment_id, true ); // true = force delete

        if ( $result ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( array( 'message' => __( 'Er is een fout opgetreden bij het verwijderen van de bijlage.', 'kzd-manager' ) ) );
        }
    }

    /**
     * AJAX callback om een bijlage te archiveren.
     */
    public function ajax_archiveer_attachment() {
        check_ajax_referer( 'archiveer_attachment_nonce', '_ajax_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Je hebt niet de juiste rechten om deze actie uit te voeren.', 'kzd-manager' ) ) );
        }

        $attachment_id = isset( $_POST['attachment_id'] ) ? (int) $_POST['attachment_id'] : 0;

        if ( ! $attachment_id ) {
            wp_send_json_error( array( 'message' => __( 'Ongeldige bijlage ID.', 'kzd-manager' ) ) );
        }

        // Archiveer de attachment
        update_post_meta( $attachment_id, 'kzd_gearchiveerd', true );

        wp_send_json_success();
    }

    /**
     * AJAX callback om een bijlage te de-archiveren.
     */
    public function ajax_dearchiveer_attachment() {
        check_ajax_referer( 'dearchiveer_attachment_nonce', '_ajax_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Je hebt niet de juiste rechten om deze actie uit te voeren.', 'kzd-manager' ) ) );
        }

        $attachment_id = isset( $_POST['attachment_id'] ) ? (int) $_POST['attachment_id'] : 0;

        if ( ! $attachment_id ) {
            wp_send_json_error( array( 'message' => __( 'Ongeldige bijlage ID.', 'kzd-manager' ) ) );
        }

        // De-archiveer de attachment
        delete_post_meta( $attachment_id, 'kzd_gearchiveerd' );

        wp_send_json_success();
    }
// Einde Deel 13: AJAX-functies voor bijlage beheer
// Begin Deel 14: ajax_get_documenten()
    /**
/**
 * AJAX callback om de documenten voor een kaart op te halen.
 */
public function ajax_get_documenten() {
    check_ajax_referer( 'get_documenten_nonce', '_ajax_nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => __( 'Je hebt niet de juiste rechten om deze actie uit te voeren.', 'kzd-manager' ) ) );
    }

    $kaart_id = isset( $_GET['kaart_id'] ) ? (int) $_GET['kaart_id'] : 0;

    if ( ! $kaart_id ) {
        wp_send_json_error( array( 'message' => __( 'Ongeldige kaart ID.', 'kzd-manager' ) ) );
    }

    $attachment_ids = get_post_meta( $kaart_id, 'kzd_attachments', true );
    $output = '';

    if ( $attachment_ids ) {
        foreach ( $attachment_ids as $attachment_id ) {
            $attachment_url = wp_get_attachment_url( $attachment_id );
            $attachment_title = get_the_title( $attachment_id );

            if ( $attachment_url ) {
                $output .= '<li><a href="' . esc_url( $attachment_url ) . '" target="_blank">' . esc_html( $attachment_title ) . '</a></li>';
            }
        }
    } else {
        $output = '<li>' . esc_html__( 'Geen documenten gevonden.', 'kzd-manager' ) . '</li>';
    }

    wp_send_json_success( $output );
}
// Einde Deel 14: ajax_get_documenten()
// Begin Deel 15: render_documenten_popup_page()
    /**
     * Render de pagina voor de documenten popup.
     */
    public function render_documenten_popup_page() {
        // Controleer of de gebruiker de juiste rechten heeft
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Je hebt geen toestemming om deze pagina te bekijken.', 'kzd-manager' ) );
        }

        $kaart_id = isset( $_GET['kaart_id'] ) ? (int) $_GET['kaart_id'] : 0;

        if ( ! $kaart_id ) {
            wp_die( esc_html__( 'Ongeldige kaart ID.', 'kzd-manager' ) );
        }

        $kaart = get_post( $kaart_id );

        // Controleer of de kaart bestaat
        if ( ! $kaart || $kaart->post_type !== 'kzd_kaart' ) {
            wp_die( esc_html__( 'Ongeldige kaart.', 'kzd-manager' ) );
        }
        $vervaldatum           = get_post_meta( $kaart_id, 'vervaldatum', true );
        $vervaldatum_timestamp = strtotime( $vervaldatum );

        // Formatteer de vervaldatum
        if ( $vervaldatum_timestamp !== false ) {
            $formatted_vervaldatum = date( 'd-m-Y', $vervaldatum_timestamp );
        } else {
            $formatted_vervaldatum = __( 'Ongeldig formaat', 'kzd-manager' );
        }

        // Verwijder admin-bar en andere onnodige elementen
        remove_action('wp_head', '_admin_bar_bump_cb');
        add_filter( 'show_admin_bar', '__return_false' ); // Verberg de admin bar

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title><?php printf( esc_html__( 'Documenten voor: %s', 'kzd-manager' ), esc_html( $kaart->post_title ) ); ?></title>
            <meta charset="UTF-8">
            <?php
            // Verwijder scripts en styles die conflicten kunnen veroorzaken
            remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
            remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
            wp_head(); // Houd wp_head() aan, maar zonder global styles
            ?>
            <link rel="stylesheet" href="<?php echo plugins_url( 'css/kzd-manager-admin.css', __FILE__ ); ?>" type="text/css" media="all" />
            <style>
                body{
                    margin: 0;
                }

                .verborgen {
                    display: none;
                }
            </style>
        </head>
        <body class="documenten-popup">
            <div class="popup-container">
            <h1><?php printf( esc_html__( 'Documenten voor: %s', 'kzd-manager' ), esc_html( $kaart->post_title ) ); ?></h1>
            <p class="vervaldatum">
                <?php printf( esc_html__( 'Vervaldatum: %s', 'kzd-manager' ), esc_html( $formatted_vervaldatum ) ); ?>
            </p>
 <ul class="bestanden-lijst">
        <?php
        $attachment_ids = get_post_meta( $kaart_id, 'kzd_attachments', true );
        if ( $attachment_ids ) {
            foreach ( $attachment_ids as $attachment_id ) {
                // Gebruik de AJAX actie om het bestand te downloaden
                $download_link = admin_url( 'admin-ajax.php?action=kzd_manager_download_file&kaart_id=' . $kaart_id . '&attachment_id=' . $attachment_id . '&_wpnonce=' . wp_create_nonce( 'download_file_nonce' ) );

                $attachment_title = get_the_title( $attachment_id );
                $gearchiveerd = get_post_meta( $attachment_id, 'kzd_gearchiveerd', true );
                if ( $attachment_url ) { // <---- BELANGRIJK: Deze regel aangepast
                    echo '<li data-attachment-id="' . esc_attr( $attachment_id ) . '"' . ($gearchiveerd ? ' class="gearchiveerd"' : '') .'>';
                    echo '<a href="' . esc_url( $download_link ) . '" target="_blank">' . esc_html( $attachment_title ) . '</a> ';
                    echo '<span class="bijlage-acties">';
                    echo '<a href="#" class="delete-bijlage" data-attachment-id="' . esc_attr( $attachment_id ) . '"><span class="dashicons dashicons-trash"></span></a>';
                    if ($gearchiveerd) {
                        echo '<a href="#" class="de-archiveer-bijlage" data-attachment-id="' . esc_attr($attachment_id) . '"><span class="dashicons dashicons-download"></span></a>';
                    } else {
                        echo '<a href="#" class="archiveer-bijlage" data-attachment-id="' . esc_attr($attachment_id) . '"><span class="dashicons dashicons-upload"></span></a>';
                    }
                    echo '</span>';
                    echo '</li>';
                }
            }
        } else {
            echo '<li>' . esc_html__( 'Geen bijlages gevonden.', 'kzd-manager' ) . '</li>';
        }
        ?>
    </ul>

            <button class="button" id="toon-upload-formulier"><?php _e('Nieuw bestand uploaden', 'kzd-manager'); ?></button>
            <div id="upload-popup" class="verborgen">
                <div class="upload-popup-content">
                    <h2 id="upload-popup-title"></h2>
                    <input type="hidden" id="upload-kaart-id" value="">
                    <input type="file" id="upload-file" name="upload_file[]" multiple="multiple">
                    <div id="upload-file-list">
                        </div>
                    <button class="button button-primary" id="upload-button"><?php _e( 'Uploaden', 'kzd-manager' ); ?></button>
                    <button class="button" id="upload-cancel"><?php _e( 'Annuleren', 'kzd-manager' ); ?></button>
                </div>
            </div>
            </div>

            <?php wp_footer(); // Voegt de nodige WordPress footer elementen toe, zoals scripts ?>

            <script>
// Verwijder functionaliteit bijlages
$(document).on('click', '.delete-bijlage', function(e) {
    e.preventDefault();
    const attachmentId = $(this).data('attachment-id');
    const listItem = $(this).closest('li');

    if (confirm('<?php echo esc_js( __( 'Weet je zeker dat je deze bijlage wilt verwijderen?', 'kzd-manager' ) ); ?>')) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kzd_manager_delete_attachment',
                attachment_id: attachmentId,
                _ajax_nonce: '<?php echo wp_create_nonce( 'delete_attachment_nonce' ); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php echo esc_js( __( 'Bijlage succesvol verwijderd.', 'kzd-manager' ) ); ?>');
                    listItem.remove();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('<?php echo esc_js( __( 'Er is een fout opgetreden bij het verwijderen van de bijlage.', 'kzd-manager' ) ); ?>');
            }
        });
    }
});
                // Archiveer functionaliteit bijlages
                $(document).on('click', '.archiveer-bijlage', function(e) {
                    e.preventDefault();
                    const attachmentId = $(this).data('attachment-id');
                    const listItem = $(this).closest('li');

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'kzd_manager_archiveer_attachment',
                            attachment_id: attachmentId,
                            _ajax_nonce: '<?php echo wp_create_nonce( 'archiveer_attachment_nonce' ); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                listItem.addClass('gearchiveerd');
                                alert('<?php echo esc_js( __( 'Bijlage succesvol gearchiveerd.', 'kzd-manager' ) ); ?>');
                                location.reload();
                            } else {
                                alert(response.data.message);
                            }
                        },
                        error: function() {
                            alert('<?php echo esc_js( __( 'Er is een fout opgetreden bij het archiveren van de bijlage.', 'kzd-manager' ) ); ?>');
                        }
                    });
                });

                // De-archiveer functionaliteit bijlages
                $(document).on('click', '.de-archiveer-bijlage', function(e) {
                    e.preventDefault();
                    const attachmentId = $(this).data('attachment-id');
                    const listItem = $(this).closest('li');

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'kzd_manager_dearchiveer_attachment',
                            attachment_id: attachmentId,
                            _ajax_nonce: '<?php echo wp_create_nonce( 'dearchiveer_attachment_nonce' ); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                listItem.removeClass('gearchiveerd');
                                alert('<?php echo esc_js( __( 'Bijlage succesvol gedearchiveerd.', 'kzd-manager' ) ); ?>');
                                location.reload();
                            } else {
                                alert(response.data.message);
                            }
                        },
                        error: function() {
                            alert('<?php echo esc_js( __( 'Er is een fout opgetreden bij het de-archiveren van de bijlage.', 'kzd-manager' ) ); ?>');
                        }
                    });
                });

                $('#toon-upload-formulier').on('click', function() {
                    $('#upload-popup').removeClass('verborgen');
                });

                $('#upload-file').change(function(e) {
                    //Bestanden toevoegen aan de bestandslijst
                    const files = e.target.files;
                    
                    $('#upload-file-list').empty();

                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        $('#upload-file-list').append(`
                            <div class="upload-file-item">
                                ${file.name}
                            </div>
                        `);
                    }
                });

$('#upload-button').click(function(e) {
    e.preventDefault();
    const formData = new FormData();
    const files = $('#upload-file')[0].files;
    const kaartId = <?php echo $kaart_id; ?>;

    for (let i = 0; i < files.length; i++) {
        formData.append('upload_file[]', files[i]);
    }

    formData.append('action', 'kzd_manager_upload_files');
    formData.append('kaart_id', kaartId);
    formData.append('_ajax_nonce', '<?php echo wp_create_nonce( 'upload_files_nonce' ); ?>');

    $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                alert('<?php echo esc_js( __( 'Bestanden succesvol geüpload.', 'kzd-manager' ) ); ?>');
                location.reload(); // Herlaad de popup na succesvolle upload
            } else {
                alert(response.data.message);
            }
        },
        error: function() {
            alert('<?php echo esc_js( __( 'Er is een fout opgetreden bij het uploaden van de bestanden.', 'kzd-manager' ) ); ?>');
        }
    });
});
                $('#upload-cancel').click(function(e) {
                    e.preventDefault();
                    $('#upload-popup').addClass('verborgen');
                });
            });
            </script>
        </body>
        </html>
        <?php
        exit;
    }
// =========== EINDE DEEL 15 ===========
// Begin Deel 16: enqueue_styles() en enqueue_scripts()

    /**
     * Voegt de benodigde CSS-bestanden toe aan de admin.
     */
    public function enqueue_styles() {
        wp_enqueue_style( 'kzd-manager-admin-styles', plugin_dir_url( __FILE__ ) . 'css/kzd-manager-admin.css', array(), '1.0.2' );
    }

    /**
     * Voegt de benodigde scripts toe aan de admin.
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'kzd-manager-admin-scripts', plugin_dir_url( __FILE__ ) . 'js/kzd-manager-admin.js', array( 'jquery', 'jquery-ui-autocomplete' ), '1.0.2', true );

        $script_vars = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'upload_files_nonce' => wp_create_nonce('upload_files_nonce'),
            'delete_attachment_nonce' => wp_create_nonce('delete_attachment_nonce'),
            'archiveer_attachment_nonce' => wp_create_nonce('archiveer_attachment_nonce'),
            'dearchiveer_attachment_nonce' => wp_create_nonce('dearchiveer_attachment_nonce'),
            'get_documenten_nonce' => wp_create_nonce('get_documenten_nonce'),
            'delete_kaart_nonce' => wp_create_nonce('delete_kaart_nonce')
        );

        // Localize script om admin-ajax url en nonces door te geven aan javascript
        wp_localize_script('kzd-manager-admin-scripts', 'kzd_manager_ajax', $script_vars);
    }
// Einde Deel 16: enqueue_styles() en enqueue_scripts()
// =========== DEEL 17: ajax_download_file() ===========
   /**
     * AJAX callback om een bestand te downloaden
     */
    public function ajax_download_file() {
        // Beveiligd download script

        // Controleer of de gebruiker ingelogd is en de juiste rechten heeft
        if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Geen toegang.' );
        }

        // Controleer nonce
        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'download_file_nonce' ) ) {
            wp_die( 'Ongeldige aanvraag.' );
        }

        // Haal de kaart ID en attachment ID op
        $kaart_id = isset( $_GET['kaart_id'] ) ? (int) $_GET['kaart_id'] : 0;
        $attachment_id = isset( $_GET['attachment_id'] ) ? (int) $_GET['attachment_id'] : 0;

        // Controleer of de kaart en attachment bestaan en gekoppeld zijn
        if ( ! $kaart_id || ! $attachment_id || ! metadata_exists( 'post', $kaart_id, 'kzd_attachments') || ! in_array( $attachment_id, get_post_meta( $kaart_id, 'kzd_attachments', true ) ) ) {
            wp_die( 'Ongeldige kaart of bijlage ID.' );
        }

        // Haal het attachment object op
        $attachment = get_post( $attachment_id );

        // Controleer of het attachment object bestaat en van het juiste type is
        if ( ! $attachment || $attachment->post_type !== 'attachment' ) {
            wp_die( 'Bijlage niet gevonden.' );
        }

        // Haal het bestandspad op
        $file_path = get_attached_file( $attachment_id );

        // Controleer of het bestand bestaat
        if ( ! file_exists( $file_path ) ) {
            wp_die( 'Bestand niet gevonden.' );
        }

        // Forceer download
        header( 'Content-Description: File Transfer' );
        header( 'Content-Type: application/octet-stream' );
        header( 'Content-Disposition: attachment; filename="' . wp_basename( $file_path ) . '"' );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate' );
        header( 'Pragma: public' );
        header( 'Content-Length: ' . filesize( $file_path ) );
        readfile( $file_path );
        exit;
    }
// =========== EINDE DEEL 17 ===========
} // <--- Afsluitende accolade van de KZD_Manager class

// Start de plugin
new KZD_Manager();

?>