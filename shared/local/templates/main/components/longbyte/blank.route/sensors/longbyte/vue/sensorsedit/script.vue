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
                    Игнорировать данные вне диапазона
                </div>
                <div class="sensors-edit__col col-2">
                    Диапазон значений на графике
                </div>
                <div class="sensors-edit__col col-1">
                    Использовать шаблон
                </div>
                <!--                <div class="sensors-edit__col col-1">
                                    Не уведомлять до
                                </div>-->
            </div>
            <form :id="getFormName(sensor)" :name="getFormName(sensor)" class="sensors-edit__item row" v-for="sensor in sensors" v-if="isRowVisible(sensor)">
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
                           name="ignore_less"
                           :value="sensor.ignore_less"
                           @change="saveForm(sensor)"
                           />
                    - 
                    <input class="form-control sensors-edit__input-text" 
                           type="text" 
                           name="ignore_more"
                           :value="sensor.ignore_more" 
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
                <div class="sensors-edit__col col-1">
                    <select class="sensors-edit__template-select form-control" @change="changeTemplate(sensor)">
                        <option value="">Выбрать</option>
                        <option value="manual">Пользовательский</option>
                        <option value="percent">Проценты</option>
                        <option value="temp_cpu">Температура CPU</option>
                        <option value="temp_gpu">Температура GPU</option>
                        <option value="temp_hdd">Температура HDD</option>
                        <option value="bool_yes">Да/Нет. Корректное "да"</option>
                        <option value="bool_no">Да/Нет. Корректное "нет"</option>
                        <option value="volt33">Напряжение 3.3V</option>
                        <option value="volt5">Напряжение 5V</option>
                        <option value="volt12">Напряжение 12V</option>
                    </select>
                </div>
                <!--                <div class="sensors-edit__col col-1">
                                    <input class="form-control sensors-edit__input-date" 
                                           type="text"
                                           name="off_alert"
                                           :value="sensor.off_alert" 
                                           @change="saveForm(sensor)"
                                           />
                                </div>-->
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
                showActive: true,
                allowSave: true,
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
                if (this.allowSave) {
                    let formData = new FormData(document.forms[this.getFormName(sensor)]);
                    axios
                        .post('/api/sensors/edit/?token=' + window.vueData.system_token, formData)
                        .then(response => (this.sensors = response.data.data));
                }
            },
            changeTemplate(sensor) {
                this.allowSave = false;

                let obTemplateData = {
                    manual: {},
                    percent: {
                        visual_min: 0,
                        visual_max: 100,
                    },
                    temp_cpu: {
                        alert_value_max: 75,
                        visual_min: 20,
                        visual_max: 100,
                    },
                    temp_gpu: {
                        alert_value_max: 85,
                        visual_min: 20,
                        visual_max: 100,
                    },
                    temp_hdd: {
                        alert_value_max: 60,
                        visual_min: 20,
                        visual_max: 100,
                    },
                    bool_yes: {
                        alert_value_min: 0.99,
                    },
                    bool_no: {
                        alert_value_max: 0.01,
                    },
                    volt33: {
                        alert_value_min: 3.19,
                        alert_value_max: 3.47,
                        visual_min: 3,
                        visual_max: 3.6,
                    },
                    volt5: {
                        alert_value_min: 4.83,
                        alert_value_max: 5.25,
                        visual_min: 4.5,
                        visual_max: 5.5,
                    },
                    volt12: {
                        alert_value_min: 11.6,
                        alert_value_max: 12.6,
                        visual_min: 11,
                        visual_max: 13,
                    },
                };

                let form = document.forms[this.getFormName(sensor)];
                let obSelect = form.querySelector('select');
                console.log(obSelect);
                if (!!obTemplateData[obSelect.value]) {
                    let obSelectedTemplate = obTemplateData[obSelect.value];
                    if (!isNaN(obSelectedTemplate.alert_value_min)) {
                        form.alert_value_min.value = obSelectedTemplate.alert_value_min;
                    } else {
                        form.alert_value_min.value = '';
                    }
                    if (!isNaN(obSelectedTemplate.alert_value_max)) {
                        form.alert_value_max.value = obSelectedTemplate.alert_value_max;
                    } else {
                        form.alert_value_max.value = '';
                    }
                    
                     if (!isNaN(obSelectedTemplate.ignore_less)) {
                        form.ignore_less.value = obSelectedTemplate.ignore_less;
                    } else {
                        form.ignore_less.value = '';
                    }
                    if (!isNaN(obSelectedTemplate.ignore_more)) {
                        form.ignore_more.value = obSelectedTemplate.ignore_more;
                    } else {
                        form.ignore_more.value = '';
                    }                    
                    
                    if (!isNaN(obSelectedTemplate.visual_min)) {
                        form.visual_min.value = obSelectedTemplate.visual_min;
                    } else {
                        form.visual_min.value = '';
                    }
                    if (!isNaN(obSelectedTemplate.visual_max)) {
                        form.visual_max.value = obSelectedTemplate.visual_max;
                    } else {
                        form.visual_max.value = '';
                    }
                }
                this.allowSave = true;
                this.saveForm(sensor);
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
