@php
	$primary_color = '#'.site_settings("primary_color");
	$secondary_color = '#'.site_settings("secondary_color");
	$font_color = '#'.site_settings("font_color");
	$primary_light = hexa_to_rgba($primary_color);
	$secondary_light = hexa_to_rgba($secondary_color);
@endphp

<style type="text/css">
	:root {
	  --primary: <?php echo $primary_color ?>;
	  --secondary: <?php echo $secondary_color ?>;
	  --text-primary: <?php echo $font_color ?> !important;
	  --primary-light: <?php echo "rgba(".$primary_light.",0.05)" ?> !important;
	  --secondary-light: <?php echo "rgba(".$secondary_light.",0.2)" ?> !important;
	}
</style>
