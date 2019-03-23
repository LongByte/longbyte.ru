<template id="portfolio-item-tag">
    <span class="portfolio-item-tag">{{result.TAG}}</span>
</template>
<script>
    Vue.component('portfolio-item-tag', {
        props: ['result'],
        template: `#portfolio-item-tag`
    });
</script>
