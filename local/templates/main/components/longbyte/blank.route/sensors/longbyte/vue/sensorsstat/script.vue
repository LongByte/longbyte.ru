<template id="sensors-template">
    <div class="sensors">
        <div class="sensors__links">
            <template v-for="link in store.links">
                <a :href="link.href" v-html="link.title"></a>
            </template>
        </div>
        <div class="sensors__list">
            <div class="sensors__item" v-for="sensorData in store.sensors">
                <sensorline v-if="sensorData.values.length > 0" :sensor="sensorData" />
            </div>
        </div>
    </div>
</template>
<script>
var sensorsApp = new Vue({
    el: '#sensorsApp',
    data() {
        return {
            store: {
                system: {},
                sensors: [],
                links: [],
            },
        };
    },
    template: `#sensors-template`,
    components: {},
    mounted() {
        this.loadData();
    },
    methods: {
        loadData() {
            let url = '/api/sensors/stat/?token=' + window.vueData.system_token;
            if (window.vueData.since) {
                url += '&since=' + window.vueData.since;
            }
            axios
                .get(url)
                .then(function (response) {
                    this.store = response.data.data;
                }.bind(this))
            ;
        },
    }
})
</script>
