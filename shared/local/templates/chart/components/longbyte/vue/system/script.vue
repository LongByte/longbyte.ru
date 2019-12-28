<template id="system">
    <form class="" method="post" action="">
        <button type="submit" class="btn btn-save">Сохранить</button>
        <div class="system">
            <table>
                <template v-for="field in system">
                    <template v-if="field.type === 'hidden'">
                        <input type="hidden" :name="field.code" :value="field.value" />
                    </template>
                    <template v-else>
                        <tr>
                            <td v-html="field.name"></td>
                            <td>
                                <template v-if="field.type === 'select'">
                                    <select :name="field.code" v-model="field.value" :multiple="field.multiple" class="system__form-field">
                                        <template v-for="option in field.values">
                                            <option v-html="option.value" :value="option.id"></option>
                                        </template>
                                    </select>
                                </template>
                                <template v-else-if="field.type === 'checkbox'">
                                    <input type="hidden" :name="field.code" value="0" />
                                    <input type="checkbox" :name="field.code" value="1" :checked="field.value" />
                                </template>
                                <template v-else-if="field.type === 'text'">
                                    <input type="text" :name="field.code" :value="field.value" class="system__form-field" />
                                </template>
                                <span v-html="field.hint"></span>
                            </td>
                        </tr>
                    </template>
                </template>
            </table>
        </div>

        <div class="result">
            <table>
                <template v-for="test_type in test_types">
                    <tr>
                        <td class="result__test-type-name" v-html="test_type.name"></td>
                        <td>Результат 1</td>
                        <td>Результат 2</td>
                        <td>Результат 3</td>
                        <td>Информация</td>
                    </tr>
                    <template v-for="test in test_type.tests">
                        <tr>
                            <td class="result__test-name" v-html="test.name"></td>
                            <td>
                                <input :value="getValue(test,'result')" :name="getName(test,'result')" />
                                <span v-html="test.units"></span>
                            </td>
                            <td>
                                <input :value="getValue(test,'result2')" :name="getName(test,'result2')" />
                                <span v-html="test.units"></span>
                            </td>
                            <td>
                                <input :value="getValue(test,'result3')" :name="getName(test,'result3')" />
                                <span v-html="test.units"></span>
                            </td>
                            <td>
                                <textarea rows="5" cols="70" v-html="getValue(test,'info')" :name="getName(test,'info')"></textarea>
                            </td>
                        </tr>
                    </template>
                </template>
            </table>
        </div>
        <button type="submit" class="btn btn-save">Сохранить</button>
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
            }
        }
    })
</script>
