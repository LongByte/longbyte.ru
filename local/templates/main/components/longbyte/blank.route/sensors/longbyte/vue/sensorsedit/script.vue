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
            <form :id="getFormName(sensor)" :name="getFormName(sensor)" class="sensors-edit__item row" v-for="sensor in sensors" v-if="isRowVisible(sensor)">
                <input type="hidden" :value="sensor.id" name="id" />
                <div class="sensors-edit__col col-2">
                    <div>
                        Включен:
                        <input type="checkbox"
                               value="1" 
                               name="active" 
                               :checked="isChecked(sensor.active)" 
                               @change="saveForm(sensor)"
                               />
                    </div>
                    <div class="" v-html="sensor.sensor_app"></div>
                    <div class="" v-html="sensor.sensor_device"></div>
                    <div class="" v-html="sensor.sensor_name"></div>
                    <div class="">
                        Режим логирования:<br>
                        <select class="sensors-edit__template-select form-control" @change="saveForm(sensor)" name="log_mode">
                            <option value="0" :selected="sensor.log_mode == 0">Среднее за сутки</option>
                            <option value="1" :selected="sensor.log_mode == 1">Каждое значение</option>
                            <option value="2" :selected="sensor.log_mode == 2">Каждое значение сегодня и среднее за прошлые дни</option>
                        </select>
                    </div>
                    <div class="sensors-edit__sort">
                        Порядок: 
                        <button type="button" class="btn btn-info" @click.prevent="sortUp(sensor)">↑</button>
                        <input class="form-control sensors-edit__input-text" 
                               type="text"
                               name="sort"
                               :value="sensor.sort"
                               @change="saveForm(sensor)"
                               />
                        <button type="button" class="btn btn-info" @click.prevent="sortDown(sensor)">↓</button>
                    </div>
                </div>
                <div class="sensors-edit__col col-1">
                    <div class="">
                        Единицы измерения:<br>
                        <input class="form-control sensors-edit__input-text" 
                               type="text"
                               name="sensor_unit"
                               :value="sensor.sensor_unit"
                               @change="saveForm(sensor)"
                               />
                    </div>
                    <div class="">
                        Количество знаков после запятой:<br>
                        <input class="form-control sensors-edit__input-text" 
                               type="text"
                               name="precision"
                               :value="sensor.precision"
                               @change="saveForm(sensor)"
                               />
                    </div>
                </div>
                <div class="sensors-edit__col col-4">
                    <div class="row">
                        <div class="col-6">
                            Оповещать при выходе за пределы:
                        </div> 
                        <div class="col-6">
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
                    </div> 
                    <div class="row">
                        <div class="col-6">
                            Игнорировать получаемые значения вне диапазона:
                        </div> 
                        <div class="col-6">
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
                    </div> 
                    <div class="row">
                        <div class="col-6">
                            Минимальное и максимальное значение на графиках:
                        </div> 
                        <div class="col-6">
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
                    </div> 
                    <div class="row">
                        <div class="col-6 offset-6">
                            <select class="sensors-edit__template-select form-control" @change="changeTemplate(sensor)" name="template">
                                <option value="">Выбрать шаблон</option>
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
                    </div>
                </div>
                <div class="sensors-edit__col col-2">
                    <div>
                        Включить оповещения:
                        <input type="checkbox"
                               value="1" 
                               name="alert_enable" 
                               :checked="isChecked(sensor.alert_enable)" 
                               @change="saveForm(sensor)"
                               />
                    </div>
                    <div class="">
                        Приостановить оповещения до:
                        <input class="form-control sensors-edit__input-date" 
                               type="text"
                               name="alert_mute_till"
                               :value="sensor.alert_mute_till" 
                               @change="saveForm(sensor)"
                               />
                    </div>
                </div>
                <div class="sensors-edit__col col-2">
                    <div>
                        Модицифировать значение:
                    </div>
                    <div>
                        <input class="form-control" 
                               type="text"
                               name="modifier"
                               :value="sensor.modifier" 
                               @change="saveForm(sensor)"
                               />
                    </div>
                    <div>
                        <small>
                            Допускается указать до двух арифметических действий. Например:<br>
                            *1024, *0.98+6, +2*1.4 (первое действие всегда будет приоритетно)<br>
                            Допустимы знаки +-*/. цифры и пробелы.<br>
                            Учтите, что правила будут ориентироваться на уже измененное значение.
                        </small>
                    </div>
                </div>
                <div class="sensors-edit__col col-1">
                    <div class="" v-if="sensor.active == false">
                        <button type="button" class="btn btn-warning" @click.prevent="deleteData(sensor)">Удалить данные</button>
                    </div>
                    <div class="" v-if="sensor.active == false">
                        <button type="button" class="btn btn-danger" @click.prevent="deleteSensor(sensor)">Удалить датчик</button>
                    </div>
                </div>
            </form>
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
                links: [],
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
                    .then(response => {
                        this.sensors = response.data.data.sensors;
                        this.links = response.data.data.links;
                    });
            },
            saveForm(sensor) {
                if (this.allowSave) {
                    let formData = new FormData(document.forms[this.getFormName(sensor)]);
                    axios
                        .post('/api/sensors/edit/?token=' + window.vueData.system_token, formData)
                        .then(response => (this.sensors = response.data.data));
                }
            },
            sortUp(sensor) {
                if (this.allowSave) {
                    let form = document.forms[this.getFormName(sensor)];
                    form.sort.value = +form.sort.value - 1;
                    this.saveForm(sensor);
                }
            },
            sortDown(sensor) {
                if (this.allowSave) {
                    let form = document.forms[this.getFormName(sensor)];
                    form.sort.value = +form.sort.value + 1;
                    this.saveForm(sensor);
                }
            },
            deleteData(sensor) {
                if (this.allowSave) {
                    if (window.confirm('Вы собираетесь удалить все данные по этому датчику. Вы уверены?')) {
                        axios
                            .delete('/api/sensors/edit/?token=' + window.vueData.system_token + '&id=' + sensor.id + '&mode=data')
                            .then(response => (this.sensors = response.data.data));
                    }
                }
            },
            deleteSensor(sensor) {
                if (this.allowSave) {
                    if (window.confirm('Вы собираетесь удалить датчик и все его данные. Вы уверены? Если данные датчика поступают с клиента то он будет вновь создан.')) {
                        axios
                            .delete('/api/sensors/edit/?token=' + window.vueData.system_token + '&id=' + sensor.id + '&mode=sensor')
                            .then(response => (this.sensors = response.data.data));
                    }
                }
            },
            changeTemplate(sensor) {
                this.allowSave = false;

                let obTemplateData = {
                    manual: {},
                    percent: {
                        visual_min: 0,
                        visual_max: 100,
                        precision: 0,
                    },
                    temp_cpu: {
                        alert_value_max: 75,
                        visual_min: 20,
                        visual_max: 100,
                        precision: 0,
                    },
                    temp_gpu: {
                        alert_value_max: 85,
                        visual_min: 20,
                        visual_max: 100,
                        precision: 0,
                    },
                    temp_hdd: {
                        alert_value_max: 60,
                        visual_min: 20,
                        visual_max: 100,
                        precision: 0,
                    },
                    bool_yes: {
                        alert_value_min: 0.99,
                        precision: 0,
                    },
                    bool_no: {
                        alert_value_max: 0.01,
                        precision: 0,
                    },
                    volt33: {
                        alert_value_min: 3.19,
                        alert_value_max: 3.47,
                        visual_min: 3,
                        visual_max: 3.6,
                        precision: 2,
                    },
                    volt5: {
                        alert_value_min: 4.83,
                        alert_value_max: 5.25,
                        visual_min: 4.5,
                        visual_max: 5.5,
                        precision: 2,
                    },
                    volt12: {
                        alert_value_min: 11.6,
                        alert_value_max: 12.6,
                        visual_min: 11,
                        visual_max: 13,
                        precision: 2,
                    },
                };

                let form = document.forms[this.getFormName(sensor)];
                let obSelect = form.querySelector('select[name=template]');
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
                    if (!isNaN(obSelectedTemplate.precision)) {
                        form.precision.value = obSelectedTemplate.precision;
                    } else {
                        form.precision.value = 0;
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
