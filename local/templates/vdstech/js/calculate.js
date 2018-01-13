var calculator = {
    config: {
        servers: [
            {},
            {
                basePrice: 100,
                cpu: {
                    min: 2,
                    max: 8,
                    step: false,
                    steps: [2, 4, 6, 8],
                    pricePerOne: 50,
                    coef: 1.05
                },
                ram: {
                    min: 128,
                    max: 16384,
                    step: false,
                    steps: [128, 256, 512, 1024, 1.5 * 1024, 2 * 1024, 3 * 1024, 4 * 1024, 6 * 1024, 8 * 1024, 12 * 1024, 16 * 1024],
                    pricePerOne: 0.1,
                    coef: 1.05
                },
                ssd: {
                    min: 4,
                    max: 30,
                    step: 1,
                    steps: [],
                    pricePerOne: 20,
                    coef: 1.05
                },
                hdd: {
                    min: 4,
                    max: 100,
                    step: 1,
                    steps: [],
                    pricePerOne: 5,
                    coef: 1.05
                }
            },
            {
                basePrice: 200,
                cpu: {
                    min: 2,
                    max: 16,
                    step: false,
                    steps: [2, 4, 6, 8, 10, 12, 16],
                    pricePerOne: 50,
                    coef: 1.05
                },
                ram: {
                    min: 128,
                    max: 16384,
                    step: false,
                    steps: [128, 256, 512, 1024, 1.5 * 1024, 2 * 1024, 3 * 1024, 4 * 1024, 6 * 1024, 8 * 1024, 12 * 1024, 16 * 1024],
                    pricePerOne: 0.1,
                    coef: 1.05
                },
                ssd: {
                    min: 4,
                    max: 30,
                    step: 1,
                    steps: [],
                    pricePerOne: 20,
                    coef: 1.05
                },
                hdd: {
                    min: 4,
                    max: 100,
                    step: 1,
                    steps: [],
                    pricePerOne: 5,
                    coef: 1.05
                }
            },
            {
                basePrice: 300,
                cpu: {
                    min: 2,
                    max: 24,
                    step: false,
                    steps: [2, 4, 6, 8, 10, 12, 16, 20, 24],
                    pricePerOne: 50,
                    coef: 1.05
                },
                ram: {
                    min: 128,
                    max: 16384,
                    step: false,
                    steps: [128, 256, 512, 1024, 1.5 * 1024, 2 * 1024, 3 * 1024, 4 * 1024, 6 * 1024, 8 * 1024, 12 * 1024, 16 * 1024],
                    pricePerOne: 0.1,
                    coef: 1.05
                },
                ssd: {
                    min: 4,
                    max: 30,
                    step: 1,
                    steps: [],
                    pricePerOne: 20,
                    coef: 1.05
                },
                hdd: {
                    min: 4,
                    max: 100,
                    step: 1,
                    steps: [],
                    pricePerOne: 5,
                    coef: 1.05
                }
            }
        ]
    },
    texts: {
        cpu: ' потока(ов)',
        ram: ' МБ',
        disk: ' ГБ',
        summary: ' руб./мес.',
    },
    data: {
        serverID: 0,
        cpu: 2,
        ram: 128,
        ssdCheck: false,
        ssd: 4,
        hddCheck: false,
        hdd: 4
    },
    objects: {
        cpu: false,
        cpuText: false,
        ram: false,
        ramText: false,
        ssdCB: false,
        ssd: false,
        ssdText: false,
        hddCB: false,
        hdd: false,
        hddText: false,
        summary: false
    },
    init: function () {
        this.objects.cpu = $('#cpu');
        this.objects.cpuText = $('#cpu-text');
        this.objects.ram = $('#ram');
        this.objects.ramText = $('#ram-text');
        this.objects.ssdCB = $('#ssd');
        this.objects.ssd = $('#ssd-space');
        this.objects.ssdText = $('#ssd-space-text');
        this.objects.hddCB = $('#hdd');
        this.objects.hdd = $('#hdd-space');
        this.objects.hddText = $('#hdd-space-text');
        this.objects.summary = $('#summary');

        $('[name=server]').on('change', $.proxy(function () {
            this.selectServer();
        }, this));
    },

    selectServer: function () {
        this.data.serverID = Number($('[name=server]:checked').val());

        var thisServer = this.config.servers[this.data.serverID];

        if (this.data.cpu < thisServer.cpu.min)
            this.data.cpu = thisServer.cpu.min;
        if (this.data.cpu > thisServer.cpu.max)
            this.data.cpu = thisServer.cpu.max;

        if (this.data.ram < thisServer.ram.min)
            this.data.ram = thisServer.ram.min;
        if (this.data.ram > thisServer.ram.max)
            this.data.ram = thisServer.ram.max;

        if (this.data.ssd < thisServer.ssd.min)
            this.data.ssd = thisServer.ssd.min;
        if (this.data.ssd > thisServer.ssd.max)
            this.data.ssd = thisServer.ssd.max;

        if (this.data.hdd < thisServer.hdd.min)
            this.data.hdd = thisServer.hdd.min;
        if (this.data.hdd > thisServer.hdd.max)
            this.data.hdd = thisServer.hdd.max;


        this.objects.cpu.slider({
            min: 0,
            max: thisServer.cpu.steps.length - 1,
            step: 1,
            value: $.inArray(this.data.cpu, thisServer.cpu.steps)
        });
        this.objects.cpu.off('slide').on('slide', $.proxy(function (event, ui) {
            this.data.cpu = this.config.servers[this.data.serverID].cpu.steps[ui.value];
            this.objects.cpuText.text(this.data.cpu + this.texts.cpu);
            this.doCalc();
        }, this));
        this.objects.cpuText.text(this.data.cpu + this.texts.cpu);

        this.objects.ram.slider({
            min: 0,
            max: thisServer.ram.steps.length - 1,
            step: 1,
            value: $.inArray(this.data.ram, thisServer.ram.steps)
        });
        this.objects.ram.off('slide').on('slide', $.proxy(function (event, ui) {
            this.data.ram = this.config.servers[this.data.serverID].ram.steps[ui.value];
            this.objects.ramText.text(this.data.ram + this.texts.ram);
            this.doCalc();
        }, this));
        this.objects.ramText.text(this.data.ram + this.texts.ram);

        this.objects.ssd.slider({
            min: thisServer.ssd.min,
            max: thisServer.ssd.max,
            step: 1,
            value: this.data.ssd,
            disabled: !this.data.ssdCheck
        });
        this.objects.ssd.off('slide').on('slide', $.proxy(function (event, ui) {
            this.data.ssd = ui.value;
            this.objects.ssdText.text(this.data.ssd + this.texts.disk);
            this.doCalc();
        }, this));
        this.objects.ssdText.text(this.data.ssd + this.texts.disk);

        this.objects.hdd.slider({
            min: thisServer.hdd.min,
            max: thisServer.hdd.max,
            step: 1,
            value: this.data.hdd,
            disabled: !this.data.hddCheck
        });
        this.objects.hdd.off('slide').on('slide', $.proxy(function (event, ui) {
            this.data.hdd = ui.value;
            this.objects.hddText.text(this.data.hdd + this.texts.disk);
            this.doCalc();
        }, this));
        this.objects.hddText.text(this.data.hdd + this.texts.disk);

        this.objects.ssdCB.off('change').on('change', $.proxy(function () {
            this.data.ssdCheck = this.objects.ssdCB.is(':checked');
            this.objects.ssd.slider('option', 'disabled', !this.data.ssdCheck);
            this.doCalc();
        }, this));

        this.objects.hddCB.off('change').on('change', $.proxy(function () {
            this.data.hddCheck = this.objects.hddCB.is(':checked');
            this.objects.hdd.slider('option', 'disabled', !this.data.hddCheck);
            this.doCalc();
        }, this));

        this.doCalc();
    },
    doCalc: function () {
        var thisServer = this.config.servers[this.data.serverID];

        var sum = thisServer.basePrice;

        sum += Math.pow(this.data.cpu - thisServer.cpu.min, thisServer.cpu.coef) * thisServer.cpu.pricePerOne;

        sum += Math.pow(this.data.ram - thisServer.ram.min, thisServer.ram.coef) * thisServer.ram.pricePerOne;

        if (this.data.ssdCheck) {
            sum += Math.pow(this.data.ssd - thisServer.ssd.min, thisServer.ssd.coef) * thisServer.ssd.pricePerOne;
        }

        if (this.data.hddCheck) {
            sum += Math.pow(this.data.hdd - (!this.data.ssdCheck ? thisServer.hdd.min : 0), thisServer.hdd.coef) * thisServer.hdd.pricePerOne;
        }

        sum = Math.round(sum);

        this.objects.summary.text(sum + this.texts.summary);
    }
};
$(function () {
    calculator.init();

    $('button').click(function () {
        var config = '';
        config = 'Сервер: ' + $('input[name=server]').filter(':checked').next().text() + ", " +
            $('#cpu-text').text() + " ," +
            'ОЗУ: ' + $('#ram-text').text() +
            (calculator.data.ssdCheck ? (', SSD: ' + $('#ssd-space-text').text()) : '') +
            (calculator.data.hddCheck ? (', HDD: ' + $('#hdd-space-text').text()) : '');
        window.history.pushState(false, false, 'http://hosting.vdstech.ru/calculate.php?config=' + config);
    });
});