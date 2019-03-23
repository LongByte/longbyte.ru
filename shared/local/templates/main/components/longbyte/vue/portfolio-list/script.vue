<template id="portfolio-list">
    <div class="portfolio-list row">
        <portfolio-item
            v-for="item in items"
            v-bind:result="item"
            v-bind:key="item.ID"
            ></portfolio-item>
    </div>
</template>
<script>
    var portfolioApp = new Vue({
        el: '#portfolioApp',
        data: () => window.vueData.portfolio,
        template: `#portfolio-list`
    })
</script>
