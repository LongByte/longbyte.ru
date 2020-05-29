<template id="sensors-edit-template">
    <div class="sensors-edit">


        <div class="sensors-edit__list container">
            <div class="row">
                <div class="sensors-edit__col col-0">
                    <input type="checkbox"
                           checked
                           @change="toggleShowActive()"
                           />
                </div>
                <div class="sensors-edit__col col-1">Приложение</div>
                <div class="sensors-edit__col col-1">Устройство</div>
                <div class="sensors-edit__col col-1">Датчик</div>
                <div class="sensors-edit__col col-1">Ед. изм.</div>
                <div class="sensors-edit__col col-2">
                    Пределы допустимых значений
                </div>
                <div class="sensors-edit__col col-2">
                    Диапазон значений на графике
                </div>
                <div class="sensors-edit__col col-2">
                    Использовать шаблон
                </div>
                <div class="sensors-edit__col col-1">
                    Не уведомлять до
                </div>
            </div>
            <form :id="getFormName(sensor)" class="sensors-edit__item row" v-for="sensor in sensors" v-if="isRowVisible(sensor)">
                <div class="sensors-edit__col col-0">
                    <input type="hidden" :value="sensor.id" name="id" />
                    <input type="checkbox"
                           value="1" 
                           name="active" 
                           :checked="isChecked(sensor.active)" 
                           @change="saveForm(sensor)"
                           />
                </div>
                <div class="sensors-edit__col col-1" v-html="sensor.sensor_app"></div>
                <div class="sensors-edit__col col-1" v-html="sensor.sensor_device"></div>
                <div class="sensors-edit__col col-1" v-html="sensor.sensor_name"></div>
                <div class="sensors-edit__col col-1" v-html="sensor.sensor_unit"></div>
                <div class="sensors-edit__col col-2">
                    <input class="form-control sensors-edit__input-text" 
                           type="text"
                           name="alert_value_min"
                           :value="sensor.alert_value_min"
                           @change="saveForm(sensor)"
                           />
                    - 
                    <input class="form-control sensors-edit__input-text" 
                           type="text" 
                           name="alert_value_max"
                           :value="sensor.alert_value_max" 
                           @change="saveForm(sensor)"
                           />
                </div>
                <div class="sensors-edit__col col-2">
                    <input class="form-control sensors-edit__input-text"
                           type="text"
                           name="visual_min"
                           :value="sensor.visual_min" 
                           @change="saveForm(sensor)"
                           /> 
                    - 
                    <input class="form-control sensors-edit__input-text" 
                           type="text" 
                           name="visual_max"
                           :value="sensor.visual_max" 
                           @change="saveForm(sensor)"
                           />
                </div>
                <div class="sensors-edit__col col-2">
                    <select class="sensors-edit__template-select">
                        <option value="manual">Пользовательский</option>
                        <option value="percent">Проценты</option>
                        <option value="temp">Температура</option>
                        <option value="bool-yes">Да/Нет. Корректное "да"</option>
                        <option value="bool-no">Да/Нет. Корректное "нет"</option>
                        <option value="volt33">Напряжение 3.3V</option>
                        <option value="volt5">Напряжение 5V</option>
                        <option value="volt12">Напряжение 12V</option>
                    </select>
                </div>
                <div class="sensors-edit__col col-1">
                    <input class="form-control sensors-edit__input-date" 
                           type="text"
                           name="off_alert"
                           :value="sensor.off_alert" 
                           @change="saveForm(sensor)"
                           />
                </div>
            </form>

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
                showActive: true
            };
        },
        template: `#sensors-edit-template`,
        components: {

        },
        mounted() {
            this.loadData();
        },
        methods: {
            refresh() {
                this.loadData();
            },
            loadData() {
                let url = '/api/sensors/edit/?token=' + window.vueData.system_token;
                axios
                    .get(url)
                    .then(response => (this.sensors = response.data.data));
            },
            saveForm(sensor) {
                let form = new FormData(document.forms[this.getFormName(sensor)]);
                axios
                    .post('/api/sensors/edit/?token=' + window.vueData.system_token, form)
                    .then(response => (this.sensors = response.data.data));
            },
            getFormName(sensor) {
                return 'form_' + sensor.id;
            },
            isChecked(active) {
                return active == 1;
            },
            isRowVisible(sensor) {
                return this.isChecked(sensor.active) || !this.showActive;
            },
            toggleShowActive() {
                this.showActive = !this.showActive;
            }
        }
    })
</script>
