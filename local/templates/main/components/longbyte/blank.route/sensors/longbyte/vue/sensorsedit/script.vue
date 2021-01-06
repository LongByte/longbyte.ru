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
                    <div class="pretty-checkbox">
                        <input type="checkbox"
                               checked
                               @change="toggleShowActive()"
                               id="toggleShowActive"
                               />
                        <label for="toggleShowActive">Покаывать только включенные датчики</label>
                    </div>
                </div>
                <div class="sensors-edit__col col-3">
                    <div class="pretty-checkbox">
                        <input type="checkbox"
                               @change="toggleGroupDevice()"
                               id="toggleGroupDevice"
                               />
                        <label for="toggleGroupDevice">Группировать по устройству</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <template v-if="groupDevice">
                    <template v-for="device in devices">
                        <sensorsedit-device
                            :sensors="sensors"
                            :device="device"
                            :show-active="showActive"
                            :system-token="system.token"
                            @refreshdata="refreshdata"
                            ></sensorsedit-device>
                    </template>
                </template>
                <template v-else>
                    <template v-for="sensor in sensors">
                        <sensorsedit-item
                            :sensors="sensors"
                            :sensor="sensor"
                            :show-active="showActive"
                            :system-token="system.token"
                            @refreshdata="refreshdata"
                            ></sensorsedit-item>
                    </template>
                </template>
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
                devices: [],
                links: [],
                showActive: true,
                groupDevice: false,
            };
        },
        template: `#sensors-edit-template`,
        components: {

        },
        mounted() {
            this.loadData();
        },
        methods: {
            refreshdata(response) {
                this.system = response.data.data.system;
                this.sensors = response.data.data.sensors;
                this.devices = response.data.data.devices;
                this.links = response.data.data.links;
            },
            loadData() {
                let url = '/api/sensors/edit/?token=' + window.vueData.system_token;
                axios
                    .get(url)
                    .then(response => {
                        this.refreshdata(response);
                    });
            },
            toggleShowActive() {
                this.showActive = !this.showActive;
            },
            toggleGroupDevice() {
                this.groupDevice = !this.groupDevice;
            },
        }
    })
</script>
