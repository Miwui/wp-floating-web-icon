/* global wp, jQuery */
jQuery( function( $ ) {

    // ── Media uploader ───────────────────────────────────────────────────────
    var mediaFrame;

    $( '#au-upload-btn' ).on( 'click', function( e ) {
        e.preventDefault();

        if ( mediaFrame ) { mediaFrame.open(); return; }

        mediaFrame = wp.media({
            title:    'Seleccionar imagen para el aviso',
            button:   { text: 'Usar esta imagen' },
            multiple: false,
            library:  { type: 'image' }
        });

        mediaFrame.on( 'select', function() {
            var attachment = mediaFrame.state().get( 'selection' ).first().toJSON();
            $( '#au-img-url' ).val( attachment.url );
            $( '#au-preview' ).attr( 'src', attachment.url ).show();
            $( '#au-remove-btn' ).show();
        });

        mediaFrame.open();
    });

    $( '#au-remove-btn' ).on( 'click', function() {
        $( '#au-img-url' ).val( '' );
        $( '#au-preview' ).attr( 'src', '' ).hide();
        $( this ).hide();
    });

    // ── Toggle – actualizar texto label en tiempo real ───────────────────────
    $( 'input[name="aviso_umag_options[enabled]"]' ).on( 'change', function() {
        var $label = $( this ).siblings( '.au-toggle-label' );
        $label.text( this.checked ? 'Activo' : 'Inactivo' );
    });

    // ── Corner picker – resaltar la opción activa ────────────────────────────
    $( '.au-corner input[type="radio"]' ).on( 'change', function() {
        $( '.au-corner' ).removeClass( 'active' );
        $( this ).closest( '.au-corner' ).addClass( 'active' );
    });

});
