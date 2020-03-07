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
        methods: {
            render() {
                let datasets = [];
                let maxValue = 1;
                
                let minColor = 'rgba(0, 0, 255, 0.5)';
                let avgColor = 'rgba(0, 255, 0, 0.5)';
                let maxColor = 'rgba(255, 0, 0, 0.5)';
                let borderColor = '#000000';
                if (this.sensor.alert) {
                    borderColor = 'red';
                    if (this.sensor.alert_direction == 1) {
                        maxColor = 'red';
                    } else if (this.sensor.alert_direction == -1) {
                        minColor = blue;
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
                            value.sensor_value_min,
                        ]
                    });
                    datasets.push({
                        label: 'Avg',
                        backgroundColor: avgColor,
                        borderWidth: 1,
                        borderColor: borderColor,
                        data: [
                            value.sensor_value,
                        ]
                    });
                    datasets.push({
                        label: 'Max',
                        backgroundColor: maxColor,
                        borderWidth: 1,
                        borderColor: borderColor,
                        data: [
                            value.sensor_value_max,
                        ]
                    });
                    if (value.sensor_value_max > maxValue) {
                        maxValue = value.sensor_value_max;
                    }
                }

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
                        labels: [''],
                        datasets: datasets,
                        
                    }, {
                    responsive: true,
                    maintainAspectRatio: false, 
                    scales: {
                        xAxes: [{
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
