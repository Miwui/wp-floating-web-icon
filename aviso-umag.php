<?php
/**
 * Plugin Name: Aviso UMAG
 * Plugin URI:  https://umag.cl
 * Description: Muestra un ícono o imagen fijo en cualquiera de las 4 esquinas de la pantalla.
 * Version:     1.2.1
 * Author:      Antonio Bravo Saavedra
 * License:     GPL-2.0+
 * Text Domain: aviso-umag
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Constantes ──────────────────────────────────────────────────────────────
define( 'AVISO_UMAG_VERSION', '1.2.1' );
define( 'AVISO_UMAG_DIR', plugin_dir_path( __FILE__ ) );
define( 'AVISO_UMAG_URL', plugin_dir_url( __FILE__ ) );

// ── Ajustes por defecto ──────────────────────────────────────────────────────
function aviso_umag_defaults() {
    return [
        'enabled'   => 1,
        'position'  => 'bottom-right',   // top-left | top-right | bottom-left | bottom-right
        'img_url'   => '',               // URL de imagen subida
        'img_width' => 80,              // px
        'link_url'  => '',              // URL de destino al hacer clic (opcional)
        'link_target' => '_blank',
        'offset_x'  => 20,             // px desde el borde horizontal
        'offset_y'  => 255,            // px desde el borde vertical
    ];
}

function aviso_umag_options() {
    $defaults = aviso_umag_defaults();
    $saved    = get_option( 'aviso_umag_options', [] );
    return wp_parse_args( $saved, $defaults );
}

// ── Menú de administración ───────────────────────────────────────────────────
add_action( 'admin_menu', function() {
    add_options_page(
        'Aviso UMAG',
        'Aviso UMAG',
        'manage_options',
        'aviso-umag',
        'aviso_umag_settings_page'
    );
});

// ── Registrar ajustes ────────────────────────────────────────────────────────
add_action( 'admin_init', function() {
    register_setting( 'aviso_umag_group', 'aviso_umag_options', 'aviso_umag_sanitize' );
});

function aviso_umag_sanitize( $input ) {
    $defaults = aviso_umag_defaults();
    $out = [];

    $out['enabled']      = ! empty( $input['enabled'] ) ? 1 : 0;
    $out['position']     = in_array( $input['position'] ?? '', ['top-left','top-right','bottom-left','bottom-right'] )
                           ? $input['position'] : $defaults['position'];
    $out['img_url']      = esc_url_raw( $input['img_url'] ?? '' );
    $out['img_width']    = absint( $input['img_width'] ?? $defaults['img_width'] );
    $out['link_url']     = esc_url_raw( $input['link_url'] ?? '' );
    $out['link_target']  = ( $input['link_target'] ?? '' ) === '_self' ? '_self' : '_blank';
    $out['offset_x']     = absint( $input['offset_x'] ?? $defaults['offset_x'] );
    $out['offset_y']     = absint( $input['offset_y'] ?? $defaults['offset_y'] );

    return $out;
}

// ── Encolar media uploader en la página de ajustes ───────────────────────────
add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( $hook !== 'settings_page_aviso-umag' ) return;
    wp_enqueue_media();
    wp_enqueue_style(  'aviso-umag-admin', AVISO_UMAG_URL . 'admin/admin.css', [], AVISO_UMAG_VERSION );
    wp_enqueue_script( 'aviso-umag-admin', AVISO_UMAG_URL . 'admin/admin.js',  ['jquery'], AVISO_UMAG_VERSION, true );
});

// ── Página de ajustes ────────────────────────────────────────────────────────
function aviso_umag_settings_page() {
    $opts = aviso_umag_options();
    ?>
    <div class="wrap aviso-umag-wrap">
        <h1>⚑ Aviso UMAG</h1>
        <p class="description">Muestra un ícono o imagen fijo en una esquina de la pantalla.</p>

        <form method="post" action="options.php">
            <?php settings_fields( 'aviso_umag_group' ); ?>

            <table class="form-table" role="presentation">

                <!-- Activar -->
                <tr>
                    <th scope="row">Estado</th>
                    <td>
                        <label class="au-toggle">
                            <input type="checkbox" name="aviso_umag_options[enabled]" value="1"
                                <?php checked( $opts['enabled'], 1 ); ?> />
                            <span class="au-toggle-track">
                                <span class="au-toggle-thumb"></span>
                            </span>
                            <span class="au-toggle-label"><?php echo $opts['enabled'] ? 'Activo' : 'Inactivo'; ?></span>
                        </label>
                    </td>
                </tr>

                <!-- Posición -->
                <tr>
                    <th scope="row"><label for="au-position">Posición</label></th>
                    <td>
                        <div class="au-corner-picker">
                            <?php
                            $positions = [
                                'top-left'     => 'Superior izquierda',
                                'top-right'    => 'Superior derecha',
                                'bottom-left'  => 'Inferior izquierda',
                                'bottom-right' => 'Inferior derecha',
                            ];
                            foreach ( $positions as $val => $label ) : ?>
                                <label class="au-corner <?php echo esc_attr( $val ); ?> <?php echo $opts['position'] === $val ? 'active' : ''; ?>">
                                    <input type="radio" name="aviso_umag_options[position]"
                                           value="<?php echo esc_attr( $val ); ?>"
                                           <?php checked( $opts['position'], $val ); ?> />
                                    <?php echo esc_html( $label ); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>

                <!-- Imagen -->
                <tr>
                    <th scope="row"><label>Imagen / Ícono</label></th>
                    <td>
                        <div class="au-image-picker">
                            <?php if ( $opts['img_url'] ) : ?>
                                <img id="au-preview" src="<?php echo esc_url( $opts['img_url'] ); ?>"
                                     style="max-width:120px;display:block;margin-bottom:8px;" />
                            <?php else : ?>
                                <img id="au-preview" src="" style="max-width:120px;display:none;margin-bottom:8px;" />
                            <?php endif; ?>
                            <input type="hidden" id="au-img-url" name="aviso_umag_options[img_url]"
                                   value="<?php echo esc_url( $opts['img_url'] ); ?>" />
                            <button type="button" class="button" id="au-upload-btn">Seleccionar imagen</button>
                            <button type="button" class="button" id="au-remove-btn"
                                <?php echo $opts['img_url'] ? '' : 'style="display:none"'; ?>>Quitar imagen</button>
                        </div>
                    </td>
                </tr>

                <!-- Ancho -->
                <tr>
                    <th scope="row"><label for="au-width">Ancho (px)</label></th>
                    <td>
                        <input type="number" id="au-width" name="aviso_umag_options[img_width]"
                               value="<?php echo esc_attr( $opts['img_width'] ); ?>"
                               min="20" max="400" step="1" class="small-text" /> px
                        <p class="description">La altura se ajustará proporcionalmente.</p>
                    </td>
                </tr>

                <!-- Enlace -->
                <tr>
                    <th scope="row"><label for="au-link">Enlace al hacer clic</label></th>
                    <td>
                        <input type="url" id="au-link" name="aviso_umag_options[link_url]"
                               value="<?php echo esc_url( $opts['link_url'] ); ?>"
                               class="regular-text" placeholder="https://..." />
                        <p class="description">Déjalo vacío si no quieres que sea clickeable.</p>
                        <label style="margin-top:6px;display:inline-block;">
                            <input type="checkbox" name="aviso_umag_options[link_target]"
                                   value="_blank" <?php checked( $opts['link_target'], '_blank' ); ?> />
                            Abrir en nueva pestaña
                        </label>
                    </td>
                </tr>

                <!-- Offset X -->
                <tr>
                    <th scope="row"><label for="au-offset-x">Separación horizontal</label></th>
                    <td>
                        <input type="number" id="au-offset-x" name="aviso_umag_options[offset_x]"
                               value="<?php echo esc_attr( $opts['offset_x'] ); ?>"
                               min="0" max="200" step="1" class="small-text" /> px
                    </td>
                </tr>

                <!-- Offset Y -->
                <tr>
                    <th scope="row"><label for="au-offset-y">Separación vertical</label></th>
                    <td>
                        <input type="number" id="au-offset-y" name="aviso_umag_options[offset_y]"
                               value="<?php echo esc_attr( $opts['offset_y'] ); ?>"
                               min="0" max="600" step="1" class="small-text" /> px
                    </td>
                </tr>

            </table>

            <?php submit_button( 'Guardar cambios' ); ?>
        </form>
    </div>
    <?php
}

// ── Renderizar el aviso en el frontend ───────────────────────────────────────
add_action( 'wp_footer', function() {
    $opts = aviso_umag_options();

    if ( ! $opts['enabled'] || ! $opts['img_url'] ) return;

    // Calcular propiedades CSS de posición
    $pos   = $opts['position'];
    $ox    = absint( $opts['offset_x'] ) . 'px';
    $oy    = absint( $opts['offset_y'] ) . 'px';
    $width = absint( $opts['img_width'] ) . 'px';

    $css_pos = '';
    if ( strpos( $pos, 'top' )    !== false ) $css_pos .= "top:{$oy};";
    if ( strpos( $pos, 'bottom' ) !== false ) $css_pos .= "bottom:{$oy};";
    if ( strpos( $pos, 'left' )   !== false ) $css_pos .= "left:{$ox};";
    if ( strpos( $pos, 'right' )  !== false ) $css_pos .= "right:{$ox};";

    $img_tag = sprintf(
        '<img src="%s" alt="Aviso UMAG" style="width:%s;height:auto;display:block;filter:drop-shadow(0 4px 10px rgba(0,0,0,.30)) drop-shadow(0 1px 3px rgba(0,0,0,.18));transition:filter .22s ease, transform .22s cubic-bezier(.34,1.56,.64,1);" id="aviso-umag-img" />',
        esc_url( $opts['img_url'] ),
        esc_attr( $width )
    );

    $inner = $opts['link_url']
        ? sprintf( '<a href="%s" target="%s" rel="noopener noreferrer" style="display:block;line-height:0;">%s</a>',
                   esc_url( $opts['link_url'] ),
                   esc_attr( $opts['link_target'] ),
                   $img_tag )
        : $img_tag;

    $style = implode( '', [
        "position:fixed;",
        $css_pos,
        "z-index:99999;",
        "transition:transform .22s cubic-bezier(.34,1.56,.64,1);",
        "cursor:default;",
    ]);

    printf(
        '<div id="aviso-umag-widget" style="%s">%s</div>' .
        '<style>
            #aviso-umag-widget:hover {
                transform: scale(1.07);
            }
            #aviso-umag-widget:hover #aviso-umag-img {
                filter: drop-shadow(0 8px 18px rgba(0,0,0,.36)) drop-shadow(0 2px 5px rgba(0,0,0,.20));
            }
            @media (prefers-reduced-motion: reduce) {
                #aviso-umag-widget,
                #aviso-umag-img { transition: none !important; }
                #aviso-umag-widget:hover { transform: none; }
            }
        </style>',
        $style,
        $inner
    );
});
