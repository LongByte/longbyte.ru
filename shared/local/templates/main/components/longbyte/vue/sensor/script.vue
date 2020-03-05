<template id="sensor">
    <div class="">

    </div>
</template>
<script>
    Vue.component('sensor', {
        extends: VueChartJs.Bar,
        props: ['sensor'],
        template: `#sensor`,
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
                for (let key in this.sensor.values) {
                    let value = this.sensor.values[key];
                    datasets.push({
                        label: 'Min',
                        backgroundColor: 'blue',
                        borderWidth: 1,
                        borderColor: '#000000',
                        data: [
                            value.sensor_value_min,
                        ]
                    });
                   
                    datasets.push({
                        label: 'Avg',
                        backgroundColor: 'green',
                        borderWidth: 1,
                        borderColor: '#000000',
                        data: [
                            value.sensor_value,
                        ]
                    });
                     datasets.push({
                        label: 'Max',
                        backgroundColor: 'red',
                        borderWidth: 1,
                        borderColor: '#000000',
                        data: [
                            value.sensor_value_max,
                        ]
                    });
                }

                this.renderChart(
                    {
                        labels: [this.sensor.sensor_app + ' ' + this.sensor.sensor_device + ' ' + this.sensor.sensor_name + ' (' + this.sensor.sensor_unit + ')' ],
                        datasets: datasets
                    }, {
                        responsive: true, 
                        maintainAspectRatio: false,
                    }
                );
            }
        }
    })
</script>
