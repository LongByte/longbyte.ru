<template id="portfolio-list">
    <div class="portfolio-list row">
        <template v-for="item in items">
            <portfolio-item :result="item"></portfolio-item>
        </template>
    </div>
</template>
<script>
var portfolioApp = new Vue({
    el: '#portfolioApp',
    data: () => window.vueData.portfolio,
    template: `#portfolio-list`
})
</script>
