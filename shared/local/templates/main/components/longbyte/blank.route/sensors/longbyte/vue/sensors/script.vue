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
        <div class="sensors__links">
            <a href="edit/">Настроить датчики</a>
            <a href="stat/">Статистика за все время</a>
        </div>
        <div class="sensors__list">
            <div class="sensors__item" v-for="sensorData in store.sensors">
                <template v-if="store.system.mode==0 || sensorData.values.length == 1" >
                    <template v-if="sensorData.sensor_unit=='Yes/No'">
                        <sensorbool :sensor="sensorData" />
                    </template>
                    <template v-else>
                        <sensorbar :sensor="sensorData" />
                    </template>
                </template>
                <template v-if="store.system.mode==1 && sensorData.values.length > 1">
                    <sensorline :sensor="sensorData" />
                </template>
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
                this.interval = setInterval(() => this.refresh(), 1000 * 60 * 5);
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
