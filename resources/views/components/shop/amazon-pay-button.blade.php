@props(['data' => []])
<div id="AmazonPayButton"></div>
<script type="text/javascript" charset="utf-8">
    const amazonPayButton = amazon.Pay.renderButton('#AmazonPayButton', <?php echo json_encode($data, JSON_UNESCAPED_SLASHES); ?>);
</script>
