<template id="portfolio-item">
    <div class="col-xs-12 col-md-6 portfolio-item">
        <div class="inner" v-bind:id="result.id">
            <div class="img">
                <img v-if="result.preview_src" :src="result.preview_src" />
            </div>
            <div class="desc">
                <div class="name" v-html="result.name"></div>
                <div class="detail-text" v-html="result.detail_text"></div>
                <div class="preview-text" v-html="result.preview_text"></div>
                <div class="years" v-html="result.print_year"></div>
                <div class="links">
                    <a v-if="result.detail_src" :href="result.detail_src" target="_blank">Посмотреть макет</a>
                    <a v-if="result.url" :href="result.url" rel="nofollow" target="_blank">Перейти на сайт</a>
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
