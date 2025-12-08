<?php

add_action('wp_footer', function () {
  if (!is_front_page()) return;
  ?>
  <div id="bleymard-help-banner">
    Pour ouvrir le menu, appuyez sur le bouton ☰en haut de l’écran.
    <span id="bleymard-help-close">✖</span>
  </div>
  <?php
});

add_action('wp_footer', function () {
  ?>
  <script>
    (function () {
      if (window.innerWidth > 768) return;

      const banner = document.getElementById('bleymard-help-banner');
      const closeBtn = document.getElementById('bleymard-help-close');

      if (!banner || !closeBtn) return;

      setTimeout(() => {
        banner.style.display = 'block';
      }, 3000);

      closeBtn.addEventListener('click', () => {
        banner.style.display = 'none';
      });

      setTimeout(() => {
        banner.style.display = 'none';
      }, 15000);
    })();
  </script>
  <?php
});

function custom_loubluma_help( $atts, $content = null ) {
  $atts = shortcode_atts(
    [
      'show' => 3,
      'hide' => 15
    ],
    $atts);

  $show = (int) $atts['show'] * 1000;
  $hide = (int) $atts['hide'] * 1000;

  ob_start();
  ?>
  <div id="bleymard-help-banner" data-show="<?php echo $show; ?>" data-hide="<?php echo $hide; ?>">
    <?php echo wp_kses_post($content); ?>
    <span id="bleymard-help-close">✖</span>
  </div>
  <?php
  return ob_get_clean();
}

add_shortcode( 'custom_loubluma_help', 'custom_loubluma_help' );

add_action('wp_footer', function () {
  ?>
  <script>
    (function () {
      const banner = document.getElementById('bleymard-help-banner');
      const closeBtn = document.getElementById('bleymard-help-close');

      if (!banner || !closeBtn) return;

      const showDelay = parseInt(banner.dataset.show || '3000', 10);
      const hideDelay = parseInt(banner.dataset.hide || '15000', 10);

      setTimeout(() => {
        banner.style.display = 'block';
      }, showDelay);

      closeBtn.addEventListener('click', () => {
        banner.style.display = 'none';
      });

      setTimeout(() => {
        banner.style.display = 'none';
      }, showDelay + hideDelay);
    })();
  </script>
  <?php
});

