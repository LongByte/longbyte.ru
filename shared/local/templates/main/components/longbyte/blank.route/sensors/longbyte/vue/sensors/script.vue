<template id="sensors-template">
    <div class="sensors">
        <div class="sensors__date">
            <div class="sensors__date-label">
                Дата: 
            </div>
            <div class="sensors__date-component">
                <vuejs-datepicker @selected="changeDate" />
            </div>
            <div class="sensors__date-refresh">
                <button @click="refresh">Обновить</button>
            </div>
        </div>
        <div class="sensors__last-update">Последнее обновление: {{store.system.last_update}}</div>
        <div class="sensors__last-update" v-if="store.system.last_update != store.system.last_receive">Последнее получение данных {{store.system.last_receive}}</div>
        <div class="sensors__links">
            <a href="edit/">Настроить датчики</a>
            <a href="stat/">Статистика за все время</a>
        </div>
        <div class="sensors__list">
            <template v-for="sensorData in store.sensors">
                <template v-if="sensorData.view=='bool'">
                    <div class="sensorbool">
                        <sensorbool :sensor="sensorData" />
                    </div>
                </template>
                <template v-if="sensorData.view=='line'">
                    <div class="sensorline">
                        <sensorline :sensor="sensorData" />
                    </div>
                </template>
                <template v-if="sensorData.view=='bar'">
                    <div class="sensorbar">
                        <sensorbar :sensor="sensorData" />
                    </div>
                </template>
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
        template: `#sensors-template`,
        components: {
            vuejsDatepicker
        },
        mounted() {
            this.loadData();
            this.startCountdown();
        },
        methods: {
            changeDate(date) {
                this.loadData(date);
            },
            refresh() {
                this.loadData();
            },
            startCountdown() {
                this.stopCountdown();
                this.interval = setInterval(() => this.refresh(), 1000 * 60);
            },
            stopCountdown() {
                if (!!this.interval) {
                    clearInterval(this.interval);
                }
            },
            loadData(date) {
                let url = '/api/sensors/get/?name=' + window.vueData.system_name + '&token=' + window.vueData.system_token;
                if (!!date) {
                    this.stopCountdown();
                    let day = date.getDate();
                    if (day < 10)
                        day = '0' + day;
                    let month = date.getMonth() + 1;
                    if (month < 10)
                        month = '0' + month;
                    let strDate = day + '.' + month + '.' + date.getFullYear();
                    url += '&date=' + strDate;
                } else {
                    this.startCountdown();
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
