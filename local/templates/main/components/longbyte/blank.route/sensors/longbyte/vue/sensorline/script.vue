<template id="sensorline">
    <div class="">

    </div>
</template>
<script>
Vue.component('sensorline', {
    extends: VueChartJs.Line,
    props: ['sensor'],
    template: `#sensorline`,
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
            let maxValue = null;
            let arData = {
                min: [],
                avg: [],
                max: []
            };
            let arLabels = [];

            for (let key in this.sensor.values) {
                let value = this.sensor.values[key];
                arData.min.push(value.value_min);
                arData.avg.push(value.value_avg);
                arData.max.push(value.value_max);
                if (maxValue == null || value.value_max > maxValue) {
                    maxValue = value.value_max;
                }
                if (minValue == null || value.value_min < minValue) {
                    minValue = value.value_min;
                }
                arLabels.push(value.date);
            }

            let bgColor = 'rgba(127, 255, 127, 0.5)';
            if (this.sensor.alert.alert) {
                if (this.sensor.alert.direction == 1) {
                    bgColor = 'rgba(255, 127, 127, 0.5)';
                }
                if (this.sensor.alert.direction == -1) {
                    bgColor = 'rgba(127, 127, 255, 0.5)';
                }
            }

            datasets.push({
                label: 'Min',
                backgroundColor: 'transparent',
                borderColor: 'rgba(127, 127, 255, 0.5)',
                data: arData.min
            });
            datasets.push({
                label: 'Avg',
                backgroundColor: bgColor,
                data: arData.avg
            });
            datasets.push({
                label: 'Max',
                backgroundColor: 'transparent',
                borderColor: 'rgba(255, 127, 127, 0.5)',
                data: arData.max
            });


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
            }

            this.renderChart(
                {
                    labels: arLabels,
                    datasets: datasets,
                }, {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
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
                    animation: {
                        duration: 0
                    },
                    hover: {
                        animationDuration: 0
                    },
                    responsiveAnimationDuration: 0
                }
            );
        }
    }
})
</script>
