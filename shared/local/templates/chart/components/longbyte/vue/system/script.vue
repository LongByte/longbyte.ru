<template id="system">
    <form name="system" class="" method="post" action="" @submit="save">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <br>
        <br>
        <h5>Система</h5>
        <div class="system">
            <template v-for="field in system">
                <template v-if="field.type === 'hidden'">
                    <input type="hidden" :name="field.code" :value="field.value" />
                </template>
                <template v-else>
                    <div class="form-row">
                        <div class="col-2" v-html="field.name"></div>
                        <div class="col-4">
                            <template v-if="field.type === 'select'">
                                <select class="form-control" :name="field.code" :multiple="field.multiple">
                                    <template v-for="option in field.values">
                                        <option v-html="option.value" :value="option.id" :selected="field.value == option.id"></option>
                                    </template>
                                </select>
                            </template>
                            <template v-else-if="field.type === 'checkbox'">
                                <div class="form-check">
                                    <input type="hidden" :name="field.code" value="0" />
                                    <input class="form-check-input position-static" type="checkbox" :name="field.code" value="1" :checked="field.value" />
                                </div>
                            </template>
                            <template v-else-if="field.type === 'text'">
                                <input class="form-control" type="text" :name="field.code" :value="field.value" />
                            </template>
                        </div>
                        <span class="col-4" v-html="field.hint"></span>
                    </div>
                </template>
            </template>
        </div>

        <div class="result">
            <template v-for="test_type in test_types">
                <div class="result__group">
                    <div class="form-row">
                        <h5 class="result__test-type-name col-2" v-html="test_type.name"></h5>
                    </div>
                    <template v-for="test in test_type.tests">
                        <div class="form-row">
                            <div class="result__test-name col-2" v-html="test.name"></div>
                            <div class="col-2">
                                <template v-if="isVisiable(test,'result')">
                                    <input class="form-control" :value="getValue(test,'result')" :name="getName(test,'result')" :placeholder="test.placeholder_result" />
                                    <span v-html="test.units"></span>
                                </template>
                            </div>
                            <div class="col-2">
                                <template v-if="isVisiable(test,'result2')">
                                    <input class="form-control" :value="getValue(test,'result2')" :name="getName(test,'result2')" :placeholder="getPlaceholder(test,'result2')" />
                                    <span v-html="test.units"></span>
                                </template>
                            </div>
                            <div class="col-2">
                                <template v-if="isVisiable(test,'result3')">
                                    <input class="form-control" :value="getValue(test,'result3')" :name="getName(test,'result3')" :placeholder="getPlaceholder(test,'result3')" />
                                    <span v-html="test.units"></span>
                                </template>
                            </div>
                            <div class="col-4">
                                <template v-if="isVisiable(test,'info')">
                                    <textarea class="form-control" rows="5" v-html="getValue(test,'info')" :name="getName(test,'info')" :placeholder="getPlaceholder(test,'info')"></textarea>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>

</template>
<script>
    var chartApp = new Vue({
        el: '#chartApp',
        data: () => window.vueData.system,
        template: `#system`,
        methods: {
            getName(test, resultKey) {
                let name = 'unknow';
                if (!!test)
                    name = 'result[' + test.id + '][' + resultKey + ']';
                return name;
            },
            getValue(test, resultKey) {
                let html = '';
                if (!!test.result && !!test.result[resultKey])
                    html = test.result[resultKey];
                return html;
            },
            getPlaceholder(test, resultKey) {
                let html = '';
                if (!!test && !!test['placeholder_' + resultKey])
                    html = test['placeholder_' + resultKey];
                return html;
            },
            isVisiable(test, resultKey) {
                return (!!test && !!test['placeholder_' + resultKey]);
            },
            save(event) {
                event.preventDefault();
                let form = new FormData(document.forms.system);
                axios
                    .post('', form)
                    .then(response => (this.data = response));
            }
        }
    })
</script>
