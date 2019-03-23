<template id="portfolio-item">
    <div class="col-xs-12 col-md-6 portfolio-item">
        <div class="inner" v-bind:id="result.ID">
            <div class="img">
                <img v-if="result.PREVIEW_SRC" v-bind:src="result.PREVIEW_SRC" />
            </div>
            <div class="desc">
                <div class="name">{{result.NAME}}</div>
                <div class="detail-text">{{result.DETAIL_TEXT}}</div>
                <div class="preview-text">{{result.PREVIEW_TEXT}}</div>
                <div class="years">
                    {{result.PRINT_YEAR}}
                </div>
                <div class="links">
                    <a v-if="result.DETAIL_SRC" v-bind:href="result.DETAIL_SRC" target="_blank">Посмотреть макет</a>
                    <a v-if="result.URL" v-bind:href="result.URL" rel="nofollow" target="_blank">Перейти на сайт</a>
                </div>
                <div class="tags">
                    <portfolio-item-tag
                        v-for="tag in result.TAGS"
                        v-bind:result="tag"
                        v-bind:key="tag.ID"
                        ></portfolio-item-tag>
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
