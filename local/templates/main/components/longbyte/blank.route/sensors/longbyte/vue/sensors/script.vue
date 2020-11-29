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
        <div class="sensors__options">
            <div class="sensors__options-item">
                В 3 колонки:
                <input type="checkbox"
                       @change="toggleThreeCol()"
                       />
            </div>
            <div class="sensors__options-item">
                За последний час:
                <input type="checkbox"
                       @change="toggleLastHour()"
                       />
            </div>
        </div>
        <div class="sensors__links">
            <template v-for="link in store.links">
                <a :href="link.href" v-html="link.title"></a>
            </template>
        </div>
        <div :class="getSensorsListClass()">
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
                    sensors: [],
                    links: [],
                },
                showThreeCol: false,
                showLastHour: false,
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
                    if (this.isLastHour()) {
                        url += '&since=-1hour';
                    }
                }
                axios
                    .get(url)
                    .then(function (response) {
                        this.store = response.data.data;
                    }.bind(this))
                    ;
            },
            getSensorsListClass() {
                return 'sensors__list' + (this.isThreeCol() ? ' sensors__list--three-col' : '');
            },
            isThreeCol() {
                return this.showThreeCol;
            },
            toggleThreeCol() {
                this.showThreeCol = !this.showThreeCol;
            },
            isLastHour() {
                return this.showLastHour;
            },
            toggleLastHour() {
                this.showLastHour = !this.showLastHour;
                this.refresh();
            },
        }
    })
</script>
