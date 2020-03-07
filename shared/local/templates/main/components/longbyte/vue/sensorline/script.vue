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
                let maxValue = 1;
                let arData = [];
                let arLabels = [];
                
                for (let key in this.sensor.values) {
                    let value = this.sensor.values[key];
                    arData.push(value.sensor_value);
                    arLabels.push(value.date);
                    if (value.sensor_value > maxValue) {
                        maxValue = value.sensor_value;
                    }
                }
                
                let color = 'rgba(127, 255, 127, 0.5)';
                if (this.sensor.alert) {
                    if (this.sensor.alert_direction == 1) {
                        color = 'rgba(255, 127, 127, 0.5)';
                    } else if (this.sensor.alert_direction == -1) {
                        color = 'rgba(127, 127, 255, 0.5)';
                    }
                }
                
                datasets.push({
                    label: this.sensor.sensor_name + ' (' + this.sensor.sensor_unit + ')',
                    backgroundColor: color,
                    data: arData
                });


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
                    } else {
                        maxValue = Math.pow(10, valueLen + 1) / 2;
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
                                    min: 0,
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
