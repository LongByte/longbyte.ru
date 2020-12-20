<template id="sensors-edit-template">
    <div class="sensors-edit">

        <div class="sensors-edit__links">
            <template v-for="link in links">
                <a :href="link.href" v-html="link.title"></a>
            </template>
        </div>

        <div class="sensors-edit__list container">
            <div class="sensors-edit__item row">
                <div class="sensors-edit__col col-3">
                    Покаывать только включенные датчики:
                    <input type="checkbox"
                           checked
                           @change="toggleShowActive()"
                           />
                </div>
            </div>
            <div class="row">
                <template v-for="sensor in sensors">
                    <sensorsedit-item
                        :sensor="sensor"
                        :show-active="showActive"
                        :system-token="system.token"
                        @refreshdata="refreshData"
                        ></sensorsedit-item>
                </template>
            </div>
        </div>
    </div>
</template>
<script>
    var sensorseditApp = new Vue({
        el: '#sensorseditApp',
        data() {
            return {
                system: {},
                sensors: [],
                links: [],
                showActive: true,
            };
        },
        template: `#sensors-edit-template`,
        components: {

        },
        mounted() {
            this.loadData();
        },
        methods: {
            refreshData(response) {
                this.system = response.data.data.system;
                this.sensors = response.data.data.sensors;
                this.links = response.data.data.links;
            },
            loadData() {
                let url = '/api/sensors/edit/?token=' + window.vueData.system_token;
                axios
                    .get(url)
                    .then(response => {
                        this.refreshData(response);
                    });
            },
            toggleShowActive() {
                this.showActive = !this.showActive;
            }
        }
    })
</script>
