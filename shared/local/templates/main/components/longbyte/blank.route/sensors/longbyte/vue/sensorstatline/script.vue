<template id="sensorstatline">
    <div class="">

    </div>
</template>
<script>
    Vue.component('sensorstatline', {
        extends: VueChartJs.Line,
        props: ['sensor'],
        template: `#sensorstatline`,
        mounted() {
            this.render();
        },
        watch: {
            sensor() {
                this.render();
            }
        },
        methods: {
            render() {
                let datasets = [];
                let minValue = null;
                let maxValue = null;
                let arData = {
                    min: [],
                    avg: [],
                    max: []
                };
                let arLabels = [];

                for (let key in this.sensor.values) {
                    let value = this.sensor.values[key];
                    arData.min.push(value.sensor_value_min);
                    arData.avg.push(value.sensor_value);
                    arData.max.push(value.sensor_value_max);
                    arLabels.push(value.date);
                    if (maxValue == null || value.sensor_value_max > maxValue) {
                        maxValue = value.sensor_value_max;
                    }
                    if (minValue == null || value.sensor_value_min < minValue) {
                        minValue = value.sensor_value_min;
                    }
                }

                datasets.push({
                    label: 'Min',
                    backgroundColor: 'rgba(127, 127, 255, 0.5)',
                    data: arData.min
                });
                datasets.push({
                    label: 'Avg',
                    backgroundColor: 'rgba(127, 255, 127, 0.5)',
                    data: arData.avg
                });
                datasets.push({
                    label: 'Max',
                    backgroundColor: 'rgba(255, 127, 127, 0.5)',
                    data: arData.max
                });

                let diff = maxValue - minValue;
                minValue -= diff * 0.1;
                if (minValue < 0) {
                    minValue = 0;
                }
                maxValue += diff * 0.1;

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
                        text: this.sensor.sensor_device + ' ' + this.sensor.sensor_name + ' (' + this.sensor.sensor_unit + ')'
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
