<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
?>
<script>
    window.vueData.systems = <?= \LongByte\Vue::toVueJson($arResult['VUE']) ?>;
</script>
