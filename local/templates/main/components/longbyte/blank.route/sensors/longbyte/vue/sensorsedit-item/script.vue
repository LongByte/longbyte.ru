<template id="sensors-edit-item-template">

    <div class="col-12 sensors-edit-item" v-if="isRowVisible">
        <form :id="formName" :name="formName" class="row">
            <input type="hidden" :value="sensor.id" name="id" />
            <div class="col-12">
                <div class="sensors-edit-item__first-line">
                    <select class="short form-control" @change="saveForm()" name="log_mode">
                        <option value="0" :selected="sensor.log_mode == 0">Среднее за сутки</option>
                        <option value="1" :selected="sensor.log_mode == 1">Каждое значение</option>
                        <option value="2" :selected="sensor.log_mode == 2">Каждое значение сегодня и среднее за прошлые дни</option>
                    </select>
                    <span class="pretty-checkbox">
                        <input type="hidden"
                               value="0" 
                               name="active" 
                               />
                        <input type="checkbox"
                               value="1" 
                               name="active" 
                               :id="'sensor-active-'+sensor.id"
                               :checked="isChecked(sensor.active)" 
                               @change="saveForm()"
                               />
                        <label :for="'sensor-active-'+sensor.id"></label>
                    </span>
                    <div class="sensors-edit-item__name">
                        <template v-if="isLabelEdit">
                            <input class="form-control form-control--inline" name="label" :value="sensor.label" :placeholder="fullName" @blur="blurLabelEdit" />
                        </template>
                        <template v-else>
                            <span class="sensors-edit-item__name-label" v-html="fullName" @click="toggleLabelEdit"></span>
                        </template>
                    </div>
                </div>
            </div>
            <div class="sensors-edit-item__col col-3">
                <div class="sensors-edit-item__sort">
                    Порядок: 
                    <button type="button" class="btn btn-info" @click.prevent="sortUp()">↑</button>
                    <input class="form-control sensors-edit-item__input-text" 
                           type="text"
                           name="sort"
                           :value="sensor.sort"
                           @change="saveForm(sensor)"
                           />
                    <button type="button" class="btn btn-info" @click.prevent="sortDown()">↓</button>
                </div>
                <div class="sensors-edit-item__sort">
                    Единицы:
                    <input class="form-control sensors-edit-item__input-text" 
                           type="text"
                           name="sensor_unit"
                           :value="sensor.sensor_unit"
                           @change="saveForm()"
                           />
                    Точность:
                    <input class="form-control sensors-edit-item__input-text" 
                           type="text"
                           name="precision"
                           :value="sensor.precision"
                           @change="saveForm()"
                           />
                </div>

            </div>
            <div class="sensors-edit-item__col col-4">
                <div class="row">
                    <div class="col-4">
                        Оповещать при выходе за пределы:
                    </div> 
                    <div class="col-8">
                        <input class="form-control sensors-edit-item__input-text" 
                               type="text"
                               name="alert_value_min"
                               :value="sensor.alert_value_min"
                               @change="saveForm()"
                               />
                        - 
                        <input class="form-control sensors-edit-item__input-text" 
                               type="text" 
                               name="alert_value_max"
                               :value="sensor.alert_value_max" 
                               @change="saveForm()"
                               />
                        <select class="short form-control form-control--inline" @change="changeTemplate()" name="template">
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
                <div class="row">
                    <div class="col-4">
                        Не принимать вне диапазона:
                    </div> 
                    <div class="col-8">
                        <input class="form-control sensors-edit-item__input-text" 
                               type="text"
                               name="ignore_less"
                               :value="sensor.ignore_less"
                               @change="saveForm()"
                               />
                        - 
                        <input class="form-control sensors-edit-item__input-text" 
                               type="text" 
                               name="ignore_more"
                               :value="sensor.ignore_more" 
                               @change="saveForm()"
                               />
                    </div> 
                </div> 
                <div class="row">
                    <div class="col-4">
                        График:
                    </div> 
                    <div class="col-8">
                        <input class="form-control sensors-edit-item__input-text"
                               type="text"
                               name="visual_min"
                               :value="sensor.visual_min" 
                               @change="saveForm()"
                               /> 
                        - 
                        <input class="form-control sensors-edit-item__input-text" 
                               type="text" 
                               name="visual_max"
                               :value="sensor.visual_max" 
                               @change="saveForm()"
                               />
                    </div> 
                </div> 
                <div class="row" v-if="!!sensor.statistic">
                    <div class="col-4">
                        Принятые значения:
                    </div> 
                    <div class="col-8">
                        <input class="form-control sensors-edit-item__input-text"
                               type="text"
                               disabled
                               :value="sensor.statistic.value_min" 
                               /> 
                        - 
                        <input class="form-control sensors-edit-item__input-text" 
                               type="text" 
                               disabled
                               :value="sensor.statistic.value_max" 
                               />
                    </div> 
                </div> 
            </div>
            <div class="sensors-edit-item__col col-2">
                <div class="pretty-checkbox">
                    <input type="hidden"
                           value="0" 
                           name="alert_enable" 
                           />
                    <input type="checkbox"
                           value="1" 
                           name="alert_enable" 
                           :id="'sensor-alert-enable-'+sensor.id"
                           :checked="isChecked(sensor.alert_enable)" 
                           @change="saveForm()"
                           />
                    <label :for="'sensor-alert-enable-'+sensor.id">включить оповещения</label>
                </div>
                <div class="">
                    Приостановить оповещения до:
                    <input class="form-control sensors-edit-item__input-date" 
                           type="text"
                           name="alert_mute_till"
                           :value="sensor.alert_mute_till" 
                           @change="saveForm()"
                           />
                </div>
            </div>
            <div class="sensors-edit-item__col col-2">
                <div>
                    Модицифировать значение:
                </div>
                <div>
                    <input class="form-control" 
                           type="text"
                           name="modifier"
                           :value="sensor.modifier" 
                           @change="saveForm()"
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
            <div class="sensors-edit-item__col col-1">
                <div class="sensors-edit-item__action">
                    <select name="merge" class="short form-control form-control--inline">
                        <template v-for="onesensor in sensors">
                            <option
                                v-if="onesensor.id != sensor.id"
                                :value="onesensor.id"
                                v-html="onesensor.sensor_app + ' ' + onesensor.sensor_device + ' ' + onesensor.sensor_name"
                                ></option>
                        </template>
                    </select>
                    <button type="button" class="btn btn-warning" @click.prevent="mergeSensors()">Объединить</button>
                </div>
                <div class="sensors-edit-item__action" v-if="sensor.active == false">
                    <button type="button" class="btn btn-warning" @click.prevent="deleteData(sensor)">Удалить данные<template v-if="!!sensor.statistic"> ({{sensor.statistic.values_count}})</template></button>
                </div>
                <div class="sensors-edit-item__action" v-if="sensor.active == false">
                    <button type="button" class="btn btn-danger" @click.prevent="deleteSensor(sensor)">Удалить датчик</button>
                </div>
            </div>
        </form>
    </div>
</template>
<script>
    Vue.component('sensorsedit-item', {
        props: {
            sensors: [Array, Object],
            sensor: [Array, Object],
            showActive: [Boolean],
            systemToken: [String]
        },
        template: `#sensors-edit-item-template`,
        data() {
            return {
                allowSave: true,
                isLabelEdit: false,
            };
        },
        components: {
            
        },
        mounted() {

        },
        computed: {
            fullName() {
                if (!!this.sensor.label && this.sensor.label.length > 0) {
                    return this.sensor.label;
                } else {
                    return this.sensor.sensor_app + ' ' + this.sensor.sensor_device + ' ' + this.sensor.sensor_name;
                }
            },
            formName() {
                return 'form_' + this.sensor.id;
            },
            isRowVisible() {
                return this.isChecked(this.sensor.active) || !this.showActive;
            },
        },
        methods: {
            saveForm() {
                if (this.allowSave) {
                    let formData = new FormData(document.forms[this.formName]);
                    axios
                        .post('/api/sensors/edit/?token=' + this.systemToken, formData)
                        .then(response => {
                            this.$emit('refreshdata', response);
                            this.isLabelEdit = false;
                            this.isContextMenu = false;
                        });
                }
            },
            sortUp() {
                if (this.allowSave) {
                    let form = document.forms[this.formName];
                    form.sort.value = +form.sort.value - 1;
                    this.saveForm();
                }
            },
            sortDown() {
                if (this.allowSave) {
                    let form = document.forms[this.formName];
                    form.sort.value = +form.sort.value + 1;
                    this.saveForm();
                }
            },
            deleteData() {
                if (this.allowSave) {
                    if (window.confirm('Вы собираетесь удалить все данные по этому датчику. Вы уверены?')) {
                        axios
                            .delete('/api/sensors/edit/?token=' + this.systemToken + '&id=' + this.sensor.id + '&mode=data')
                            .then(response => {
                                this.$emit('refreshData');
                            });
                    }
                }
            },
            deleteSensor() {
                if (this.allowSave) {
                    if (window.confirm('Вы собираетесь удалить датчик и все его данные. Вы уверены? Если данные датчика поступают с клиента то он будет вновь создан.')) {
                        axios
                            .delete('/api/sensors/edit/?token=' + this.systemToken + '&id=' + this.sensor.id + '&mode=sensor')
                            .then(response => {
                                this.$emit('refreshData');
                            });
                    }
                }
            },
            mergeSensors() {
                let obForm = document.forms[this.formName];
                if (window.confirm('Вы собираетесь передать все данные датчика ' + obForm.querySelector('select[name=merge] option[value="' + obForm.querySelector('select[name=merge]').value + '"]').innerHTML + ' и удалить его. Вы уверены?')) {
                    let obFormData = new FormData();
                    obFormData.append('from_id', obForm.querySelector('select[name=merge]').value);
                    obFormData.append('to_id', this.sensor.id);
                    axios
                        .post('/api/sensors/merge/?token=' + this.systemToken, obFormData)
                        .then(response => {
                            this.$emit('refreshdata', response);
                        });
                }
            },
            changeTemplate() {
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

                let form = document.forms[this.formName];
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
                this.saveForm();
            },
            isChecked(active) {
                return active == 1;
            },
            toggleLabelEdit() {
                this.isLabelEdit = !this.isLabelEdit;
                if (this.isLabelEdit) {
                    setTimeout(param => {
                        let form = document.forms[this.formName];
                        let obInput = form.querySelector('[name=label]');
                        obInput.focus();
                    }, 10);
                }
            },
            blurLabelEdit() {
                this.saveForm();
                this.toggleLabelEdit();
            },
        }
    })
</script>
