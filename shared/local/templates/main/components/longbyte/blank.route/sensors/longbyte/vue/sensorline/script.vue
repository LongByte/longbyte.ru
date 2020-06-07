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
        methods: {
            render() {
                let datasets = [];
                let minValue = 0;
                let maxValue = null;
                let arData = [];
                let arLabels = [];

                for (let key in this.sensor.values) {
                    let value = this.sensor.values[key];
                    arData.push({
                        x: new Date(value.date),
                        y: value.value,
                    });
                    arLabels.push(value.date);
                    if (maxValue == null || value.value > maxValue) {
                        maxValue = value.value;
                    }
                    if (minValue == null || value.value < minValue) {
                        minValue = value.value;
                    }
                }

                let color = 'rgba(127, 255, 127, 0.5)';
                if (this.sensor.alert.alert) {
                    if (this.sensor.alert.direction == 1) {
                        color = 'rgba(255, 127, 127, 0.5)';
                    } else if (this.sensor.alert.direction == -1) {
                        color = 'rgba(127, 127, 255, 0.5)';
                    }
                }

                datasets.push({
                    label: this.sensor.sensor_name + ' (' + this.sensor.sensor_unit + ')',
                    backgroundColor: color,
                    data: arData,
                    lineTension: 0,
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
                        xAxes: [{
                                type: 'time',
                                distribution: 'linear',
                                time: {
                                    unit: 'second',
                                    displayFormats: {
                                        second: 'HH:mm:ss'
                                    }
                                }
                            }],
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
