<template id="sensorbar">
    <div class="">

    </div>
</template>
<script>
Vue.component('sensorbar', {
    extends: VueChartJs.HorizontalBar,
    props: ['sensor'],
    template: `#sensorbar`,
    mounted() {
        this.render();
    },
    watch: {
        sensor() {
            this.render();
        }
    },
    computed: {
        fullName() {
            if (!!this.sensor.label && this.sensor.label.length > 0) {
                return this.sensor.label + ' (' + this.sensor.sensor_unit + ')';
            } else {
                return this.sensor.sensor_device + ' ' + this.sensor.sensor_name + ' (' + this.sensor.sensor_unit + ')';
            }
        }
    },
    methods: {
        render() {
            let datasets = [];
            let minValue = 0;
            let maxValue = 1;

            let minColor = 'rgba(0, 0, 255, 0.5)';
            let avgColor = 'rgba(0, 255, 0, 0.5)';
            let maxColor = 'rgba(255, 0, 0, 0.5)';
            let borderColor = '#000000';
            if (this.sensor.alert.alert) {
                borderColor = 'red';
                if (this.sensor.alert.direction == 1) {
                    maxColor = 'red';
                } else if (this.sensor.alert.direction == -1) {
                    minColor = 'blue';
                }
            }

            for (let key in this.sensor.values) {
                let value = this.sensor.values[key];
                datasets.push({
                    label: 'Min',
                    backgroundColor: minColor,
                    borderWidth: 1,
                    borderColor: borderColor,
                    data: [
                        value.value_min || value.value,
                    ]
                });
                datasets.push({
                    label: 'Avg',
                    backgroundColor: avgColor,
                    borderWidth: 1,
                    borderColor: borderColor,
                    data: [
                        value.value_avg || value.value,
                    ]
                });
                datasets.push({
                    label: 'Max',
                    backgroundColor: maxColor,
                    borderWidth: 1,
                    borderColor: borderColor,
                    data: [
                        value.value_max || value.value,
                    ]
                });
                if (value.value_max > maxValue) {
                    maxValue = value.value_max;
                }
                if (value.value_min < 0) {
                    minValue = value.value_min;
                }
            }

            let visibleDiff = this.sensor.visual_max - this.sensor.visual_min;

            if (+this.sensor.visual_min != 0) {
                if (minValue < this.sensor.visual_min && minValue != 0) {
                    minValue = minValue - visibleDiff * 0.1;
                } else {
                    minValue = this.sensor.visual_min;
                }
            }

            if (+this.sensor.visual_max != 0) {
                if (maxValue > this.sensor.visual_max) {
                    maxValue = maxValue + visibleDiff * 0.1;
                } else {
                    maxValue = this.sensor.visual_max;
                }
            } else {

                if (maxValue <= 1) {
                    maxValue = 1;
                } else if (maxValue <= 10) {
                    maxValue = 10;
                } else if (maxValue <= 20) {
                    maxValue = 20;
                } else if (maxValue <= 100) {
                    maxValue = 100;
                } else if (maxValue <= 250) {
                    maxValue = 250;
                } else {
                    let valueLen = 0;
                    let tmpMax = maxValue;
                    while (tmpMax > 10) {
                        tmpMax /= 10;
                        valueLen++;
                    }
                    if (tmpMax >= 5) {
                        maxValue = Math.pow(10, valueLen + 1);
                    } else if (tmpMax >= 2) {
                        maxValue = Math.pow(10, valueLen + 1) / 2;
                    } else {
                        maxValue = Math.pow(10, valueLen + 1) / 5;
                    }
                }
            }

            this.renderChart(
                {
                    labels: [''],
                    datasets: datasets,

                }, {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            ticks: {
                                min: minValue,
                                max: maxValue
                            },
                        }],
                    },
                    title: {
                        display: true,
                        text: this.fullName
                    },
                    legend: {
                        display: false,
                    },
                }
            );
        }
    }
})
</script>
