<template id="sensorsonline-template">
    <div class="sensors">
        <div class="sensors__date">
            <div class="sensors__date-refresh">
                <button @click="refresh">Обновить</button>
            </div>
        </div>
        <div class="sensors__last-update">Последнее обновление: {{ store.system.last_update }}</div>
        <div class="sensors__last-update" v-if="store.system.last_update != store.system.last_receive">Последнее получение данных {{ store.system.last_receive }}</div>
        <div class="sensors__links">
            <a href="edit/">Настроить датчики</a>
            <a href="stat/">Статистика за все время</a>
        </div>
        <div class="container">
            <template v-for="sensor in store.sensors">
                <div class="row sensors__line">
                    <div class="col-sm-4">
                        {{ sensor.sensor_device }}
                    </div>
                    <div class="col-sm-4">
                        {{ sensor.sensor_name }}
                    </div>
                    <div class="col-sm-2">
                        <template v-for="value in sensor.values">
                            {{ value.value }}
                        </template>
                    </div>
                    <div class="col-sm-1">
                        {{ sensor.sensor_unit }}
                    </div>
                    <div class="col-sm-1">
                        <template v-if="sensor.alert.alert">
                            !
                        </template>
                    </div>
                </div>
            </template>
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
                sensors: []
            },
        };
    },
    template: `#sensorsonline-template`,
    components: {},
    mounted() {
        this.loadData();
    },
    methods: {
        refresh() {
            this.loadData();
        },
        loadData(date) {
            let url = '/api/sensors/online/?name=' + window.vueData.system_name + '&token=' + window.vueData.system_token;
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
