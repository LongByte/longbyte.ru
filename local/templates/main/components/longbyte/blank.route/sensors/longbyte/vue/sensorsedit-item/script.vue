<template id="sensors-edit-item-template">

    <div class="col-12 col-sm-6 col-md-4" v-if="isRowVisible()">
        <div class="sensors-edit-item">
            <form :id="getFormName()" :name="getFormName()" class="row">
                <input type="hidden" :value="sensor.id" name="id" />
                <div class="col-12">
                    <input type="checkbox"
                           value="1" 
                           name="active" 
                           :checked="isChecked(sensor.active)" 
                           @change="saveForm()"
                           />
                    <span class="sensors-edit-item__name">
                        <template v-if="isAliasEdit">
                            <input name="alias" value="" :placeholder="fullName" @blur="toggleAliasEdit" />
                        </template>
                        <template v-else>
                            <span v-html="fullName" @click="toggleAliasEdit"></span>
                        </template>
                    </span>
                    <button>...</button>
                    <template v-if="false">
                        <div class="" v-if="sensor.active == false">
                            <button type="button" class="btn btn-warning" @click.prevent="deleteData()">Удалить данные</button>
                        </div>
                        <div class="" v-if="sensor.active == false">
                            <button type="button" class="btn btn-danger" @click.prevent="deleteSensor()">Удалить датчик</button>
                        </div>
                    </template>
                </div>
                <div class="col-12">

                    <span class="">
                        Единицы:
                        <input class="form-control form-control--size2 form-control--inline sensors-edit__input-text" 
                               type="text"
                               name="sensor_unit"
                               :value="sensor.sensor_unit"
                               @change="saveForm()"
                               />
                    </span>
                    <span class="">
                        Точность:
                        <input class="form-control form-control--size1 form-control--inline sensors-edit__input-text" 
                               type="text"
                               name="precision"
                               :value="sensor.precision"
                               @change="saveForm()"
                               />
                        ?
                    </span>

                    <span>
                        Формула:
                        <input class="form-control form-control--size5 form-control--inline" 
                               type="text"
                               name="modifier"
                               :value="sensor.modifier" 
                               @change="saveForm()"
                               />
                        ?
                        <template v-if="false">
                            Допускается указать до двух арифметических действий. Например:<br>
                            *1024, *0.98+6, +2*1.4 (первое действие всегда будет приоритетно)<br>
                            Допустимы знаки +-*/. цифры и пробелы.<br>
                            Учтите, что правила будут ориентироваться на уже измененное значение.
                        </template>

                    </span>
                </div>
                <div class="col-6">
                    <div class="">
                        <span>
                            Порядок: 
                        </span>
                        <input class="form-control form-control--size4 form-control--inline sensors-edit__input-text" 
                               type="text"
                               name="sort"
                               :value="sensor.sort"
                               @change="saveForm()"
                               />
                        <span>
                            <button type="button" class="btn btn-info" @click.prevent="sortUp(sensor)">↑</button>
                            <button type="button" class="btn btn-info" @click.prevent="sortDown(sensor)">↓</button>
                        </span>
                    </div>
                    <div class="">
                        Режим логирования:
                        <select class="sensors-edit__template-select form-control form-control--inline" @change="saveForm()" name="log_mode">
                            <option value="0" :selected="sensor.log_mode == 0">Среднее за сутки</option>
                            <option value="1" :selected="sensor.log_mode == 1">Каждое значение</option>
                            <option value="2" :selected="sensor.log_mode == 2">Каждое значение сегодня и среднее за прошлые дни</option>
                        </select>
                    </div>
                    <div class="">
                        Группы:
                        todo
                    </div>
                </div>
                <div class="col-6">


                    <div class="">
                        График: 
                        <input class="form-control form-control--size5 form-control--inline sensors-edit__input-text"
                               type="text"
                               name="visual_min"
                               :value="sensor.visual_min" 
                               @change="saveForm()"
                               /> 
                        - 
                        <input class="form-control form-control--size5 form-control--inline sensors-edit__input-text" 
                               type="text" 
                               name="visual_max"
                               :value="sensor.visual_max" 
                               @change="saveForm()"
                               />

                        <select class="sensors-edit__template-select form-control form-control--size1 form-control--inline" @change="changeTemplate()" name="template">
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
                    <div class="">
                        Значения:
                        <input class="form-control form-control--size5 form-control--inline sensors-edit__input-text" 
                               type="text"
                               name="ignore_less"
                               :value="sensor.ignore_less"
                               @change="saveForm()"
                               />
                        - 
                        <input class="form-control form-control--size5 form-control--inline sensors-edit__input-text" 
                               type="text" 
                               name="ignore_more"
                               :value="sensor.ignore_more" 
                               @change="saveForm()"
                               />
                    </div>
                    <div class="">
                        Нормальные:
                        <input class="form-control form-control--size5 form-control--inline sensors-edit__input-text" 
                               type="text"
                               name="alert_value_min"
                               :value="sensor.alert_value_min"
                               @change="saveForm()"
                               />
                        - 
                        <input class="form-control form-control--size5 form-control--inline sensors-edit__input-text" 
                               type="text" 
                               name="alert_value_max"
                               :value="sensor.alert_value_max" 
                               @change="saveForm()"
                               />
                    </div>
                    <div class="">
                        Оповещать:
                        <input type="checkbox"
                               value="1" 
                               name="alert_enable" 
                               :checked="isChecked(sensor.alert_enable)" 
                               @change="saveForm()"
                               />
                    </div>
                    <div class="" v-if="isChecked(sensor.alert_enable)">
                        Приостановить до:
                        <input class="form-control sensors-edit__input-date" 
                               type="text"
                               name="alert_mute_till"
                               :value="sensor.alert_mute_till" 
                               @change="saveForm()"
                               />
                    </div>

                </div>

            </form>

        </div>
    </div>
</template>
<script>
    Vue.component('sensorsedit-item', {
        props: {
            sensor: [Array, Object],
            showActive: [Boolean],
            systemToken: [String]
        },
        template: `#sensors-edit-item-template`,
        data() {
            return {
                allowSave: true,
                isAliasEdit: false,
            };
        },
        components: {

        },
        mounted() {

        },
        computed: {
            fullName() {
                return this.sensor.sensor_app + ' ' + this.sensor.sensor_device + ' ' + this.sensor.sensor_name;
            }
        },
        methods: {

            saveForm() {
                if (this.allowSave) {
                    let formData = new FormData(document.forms[this.getFormName()]);
                    axios
                        .post('/api/sensors/edit/?token=' + this.systemToken, formData)
                        .then(response => {
                            this.$emit('refreshdata', response);
                        });
                }
            },
            sortUp() {
                if (this.allowSave) {
                    let form = document.forms[this.getFormName()];
                    form.sort.value = +form.sort.value - 1;
                    this.saveForm();
                }
            },
            sortDown() {
                if (this.allowSave) {
                    let form = document.forms[this.getFormName()];
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

                let form = document.forms[this.getFormName()];
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
            getFormName() {
                return 'form_' + this.sensor.id;
            },
            isChecked(active) {
                return active == 1;
            },
            isRowVisible() {
                return this.isChecked(this.sensor.active) || !this.showActive;
            },
            toggleAliasEdit() {
                this.isAliasEdit = !this.isAliasEdit;
                if (this.isAliasEdit) {
                    setTimeout(param => {
                        let form = document.forms[this.getFormName()];
                        let obInput = form.querySelector('[name=alias]');
                        obInput.focus();
                    }, 10);
                }
            }
        }
    })
</script>
