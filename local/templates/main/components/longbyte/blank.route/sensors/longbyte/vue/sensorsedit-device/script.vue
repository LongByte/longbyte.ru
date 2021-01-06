<template id="sensors-edit-device-template">

    <div class="col-12 sensors-edit-device">
        <div class="row">
            <div class="col-7 sensors-edit-device__collapse" v-html="device.name" @click.prevent="toggleCollapse()"></div>
            <div class="col-3">
                <button type="button" class="btn btn-warning" @click.prevent="deleteData()">Удалить данные неактивных датчиков</button>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-danger" @click.prevent="deleteSensors()">Удалить датчики</button>
            </div>
        </div>
        <div class="row">
            <template v-if="!collapse">
                <template v-for="sensor in device.sensors">
                    <sensorsedit-item
                        :sensors="sensors"
                        :sensor="sensor"
                        :show-active="showActive"
                        :system-token="systemToken"
                        @refreshdata="refreshdata"
                        ></sensorsedit-item>
                </template>
            </template>
        </div>
    </div>
</template>
<script>
    Vue.component('sensorsedit-device', {
        props: {
            sensors: [Array, Object],
            device: [Array, Object],
            sensor: [Array, Object],
            showActive: [Boolean],
            systemToken: [String]
        },
        template: `#sensors-edit-device-template`,
        data() {
            return {
                collapse: true
            };
        },
        components: {

        },
        mounted() {

        },
        computed: {

        },
        methods: {
            refreshdata(response) {
                this.$emit('refreshdata', response);
            },
            deleteData() {
                if (window.confirm('Вы собираетесь удалить все данные неактивных датчиков этого устройства. Вы уверены?')) {
                    axios
                        .delete('/api/sensors/device/?token=' + this.systemToken + '&id=' + encodeURIComponent(this.device.name) + '&mode=data')
                        .then(response => {
                            this.$emit('refreshdata', response);
                        });
                }
            },
            deleteSensors() {
                if (window.confirm('Вы собираетесь удалить все неактивные датчики устройства и все их данные. Вы уверены? Если данные датчика поступают с клиента то он будет вновь создан.')) {
                    axios
                        .delete('/api/sensors/device/?token=' + this.systemToken + '&id=' + encodeURIComponent(this.device.name) + '&mode=sensor')
                        .then(response => {
                            this.$emit('refreshdata', response);
                        });
                }
            },

            toggleCollapse() {
                this.collapse = !this.collapse;
            },

        }
    })
</script>
