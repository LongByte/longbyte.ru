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
                            @setpreloader="setpreloader"
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
                            @setpreloader="setpreloader"
                            ></sensorsedit-item>
                    </template>
                </template>
            </div>
        </div>
        <template v-if="preloader">
            <div class="preloader">
                <div class="windows8">
                    <div class="wBall" id="wBall_1">
                        <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_2">
                        <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_3">
                        <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_4">
                        <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_5">
                        <div class="wInnerBall"></div>
                    </div>
                </div>
            </div>
        </template>
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
                preloader: false,
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
                this.setpreloader(false);
            },
            loadData() {
                this.setpreloader(true);
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
            setpreloader(visible) {
                this.preloader = !!visible;
            },
        }
    })
</script>
