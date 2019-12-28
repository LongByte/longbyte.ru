<template id="systems">
    <div class="systems">
        <template v-for="group in groups">
            <div class="systems__group">
                <div class="systems__group-name" v-html="group.name"></div>
                <div class="systems__list">
                    <template v-for="system in group.systems">
                        <div class="systems__list-item">
                            <a :href="system.url" v-html="system.name" class="systems__list-liml"></a>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</template>
<script>
    var chartApp = new Vue({
        el: '#chartApp',
        data: () => window.vueData.systems,
        template: `#systems`
    })
</script>
