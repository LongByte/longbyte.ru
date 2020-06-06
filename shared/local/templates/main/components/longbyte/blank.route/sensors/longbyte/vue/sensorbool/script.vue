<template id="sensorbool">
    <div :class="getSensorboolClass()">
        <div class="sensorbool__title" v-html="getTitle()"></div>
        <div class="sensorbool__value">
            <div class="sensorbool__value-avg">Current: <span class="sensorbool__value-current-value" v-html="getLastValue()"></span></div>
            <div class="sensorbool__value-avg">Avg: <span class="sensorbool__value-avg-value" v-html="getAvgValue()"></span></div>
            <div class="sensorbool__value-min">Min: <span class="sensorbool__value-min-value" v-html="getMinValue()"></span></div>
            <div class="sensorbool__value-max">Max: <span class="sensorbool__value-max-value" v-html="getMaxValue()"></span></div>
        </div>
    </div>
</template>
<script>
    Vue.component('sensorbool', {
        props: ['sensor'],
        template: `#sensorbool`,
        mounted() {

        },
        watch: {

        },
        methods: {
            getTitle() {
                return this.sensor.sensor_device + ' ' + this.sensor.sensor_name;
            },
            getMinValue() {
                return this.getTextValue(this.sensor.values[0].value_min);
            },
            getMaxValue() {
                return this.getTextValue(this.sensor.values[0].value_max);
            },
            getAvgValue() {
                return this.getTextValue(this.sensor.values[0].value_avg);
            },
            getLastValue() {
                return this.getTextValue(this.sensor.values[0].value);
            },
            getTextValue(value) {

                let textValue = '-';
                if (value == 1) {
                    textValue = 'Yes';
                } else if (value == 0) {
                    textValue = 'No';
                } else if (value > 0 && value < 1) {
                    let yesValue = value * 100;
                    let noValue = 100 - yesValue;
                    textValue = 'No: ' + noValue.toFixed(2) + '% / Yes: ' + yesValue.toFixed(2) + '%';
                }

                return textValue;
            },
            getSensorboolClass() {
                let classAlert = '';
                if (this.sensor.alert.alert) {
                    if (this.sensor.alert.direction == 1) {
                        classAlert = ' sensorbool--alert sensorbool--alert-red';
                    } else if (this.sensor.alert.direction == -1) {
                        classAlert = ' sensorbool--alert sensorbool--alert-blue';
                    }
                }

                return 'sensorbool' + classAlert;
            }
        }
    })
</script>
