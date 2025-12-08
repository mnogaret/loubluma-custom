<?php

function custom_loubluma_help( $atts, $content = null ) {
  $atts = shortcode_atts(
    [
      'hide' => 15
    ],
    $atts);

  $hide = (int) $atts['hide'] * 1000;

  ob_start();
  ?>
  <div id="bleymard-help-banner" data-hide="<?php echo $hide; ?>">
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

      const hideDelay = parseInt(banner.dataset.hide || '15000', 10);

      closeBtn.addEventListener('click', () => {
        banner.style.display = 'none';
      });

      setTimeout(() => {
        banner.style.display = 'none';
      }, hideDelay);
    })();
  </script>
  <?php
});

