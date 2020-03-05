<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
?>
<script>
    window.vueData.system_token = '<?= $arResult['SYSTEM_TOKEN'] ?>';
    window.vueData.sensors = {};
</script>

