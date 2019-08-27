<template id="portfolio-item">
    <div class="col-xs-12 col-md-6 portfolio-item">
        <div class="inner" v-bind:id="result.ID">
            <div class="img">
                <img v-if="result.PREVIEW_SRC" :src="result.PREVIEW_SRC" />
            </div>
            <div class="desc">
                <div class="name" v-html="result.NAME"></div>
                <div class="detail-text" v-html="result.DETAIL_TEXT"></div>
                <div class="preview-text" v-html="result.PREVIEW_TEXT"></div>
                <div class="years" v-html="result.PRINT_YEAR"></div>
                <div class="links">
                    <a v-if="result.DETAIL_SRC" :href="result.DETAIL_SRC" target="_blank">Посмотреть макет</a>
                    <a v-if="result.URL" :href="result.URL" rel="nofollow" target="_blank">Перейти на сайт</a>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    Vue.component('portfolio-item', {
        props: ['result'],
        template: `#portfolio-item`
    });
</script>
