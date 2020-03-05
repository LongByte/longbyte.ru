<template id="sensors-template">
    <div class="sensors">
        <div class="sensors__date">
            <vuejs-datepicker @selected="changeDate" />
        </div>
        <div class="sensors__list">
            <div class="sensors__item" v-for="sensorData in store.sensors">
                <sensor :sensor="sensorData" />
            </div>
        </div>
    </div>
</template>
<script>
    var portfolioApp = new Vue({
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
        },
        methods: {
            changeDate(date) {
                this.loadData(date);
            },
            loadData(date) {
                let url = '/api/sensors/get/?token=' + window.vueData.system_token;
                if (!!date) {
                    let day = date.getDate();
                    if (day < 10)
                        day = '0' + day;
                    let month = date.getMonth() + 1;
                    if (month < 10)
                        month = '0' + month;
                    let strDate = day + '.' + month + '.' + date.getFullYear();
                    url += '&date=' + strDate;
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
