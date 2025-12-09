<?php

$GLOBALS['loubluma_help_used'] = false;

function custom_loubluma_help( $atts, $content = null ) {
  $GLOBALS['loubluma_help_used'] = true;

  $atts = shortcode_atts(
    [
      'hide' => 15
    ],
    $atts);

  $hide = (int) $atts['hide'] * 1000;

  ob_start();
  ?>
  <div id="temp-bleymard-help-banner" data-hide="<?php echo $hide; ?>">
    <?php echo wp_kses_post($content); ?>
  </div>
  <?php
  return ob_get_clean();
}

add_shortcode( 'custom_loubluma_help', 'custom_loubluma_help' );

add_action('wp_footer', function () {
  if ( empty($GLOBALS['loubluma_help_used']) ) return;

  ?>
  <script>
    (function () {

      function initHelpBanner( firstTime = false ) {

        const banner = document.getElementById('temp-bleymard-help-banner');
        const mobileHeader = document.getElementById('ast-mobile-header');
        const button = mobileHeader.querySelector('.main-header-menu-toggle');

        console.log(banner);
        console.log(mobileHeader);
        console.log(button);

        if (!banner || !mobileHeader || !button) {
          if ( firstTime ) {
            setTimeout( () => {
              initHelpBanner();
            }, 1000 );
          }
          return;
        }

        mobileHeader.appendChild(banner);
        banner.id = 'bleymard-help-banner';

        const hideDelay = parseInt(banner.dataset.hide || '10000', 10);

        banner.addEventListener('click', () => {
          banner.style.display = 'none';
        });

        button.addEventListener('click', () => {
          banner.style.display = 'none';
        });

        setTimeout(() => {
          banner.style.display = 'none';
        }, hideDelay);
      }

      initHelpBanner( true );

    })();
  </script>
  <?php
});

