<template>
    <div>
        <heading class="mb-6">Content Manager</heading>
        <draggable v-model="panels" @sort="changeSort()">
            <template v-for="panel in panels" >
                <ContentPanelComponent :panel="panel" :contents="panel.contents"></ContentPanelComponent>
            </template>
        </draggable>


        <div class="panel-create-button" @click="createPanel()">
            <font-awesome-icon :icon="['fas', 'plus']" />
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import ContentPanelComponent from './ContentPanelComponent.vue';
import plus from './plus.vue';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faPlus } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
library.add(faPlus);

export default {
    components: {
        draggable,
        ContentPanelComponent,
        plus,
        FontAwesomeIcon
    },
    data(){
        return {
            panels: [],
            status: false,
        }
    },
    methods: {
        getPanels: function () {
            var ref = this;
            window.axios.post('/nova-vendor/content-manager/panel/all')
                .then(function (response) {
                    if(response.data.status){
                        ref.panels = response.data.data;
                    }
                    ref.$toasted.show(ref.__(response.data.message), { type: 'success' });
                }).catch(function (error) {
                ref.$toasted.show(ref.__('Error Occurred!'), { type: 'error' });
            });
        },
        createPanel: async function(){
            var ref = this;

            var order = 1;
            if(ref.panels.length !== 0) {
                order = ref.panels.length + 1;
            }
            await window.axios.post('/nova-vendor/content-manager/panel', {
                name: 'Panel ' + order,
                status: true,
                order: order,
            }).then(function (response) {
                if(response.data.status){
                    ref.getPanels();
                    ref.$toasted.show(ref.__(response.data.message), { type: 'success' });
                } else {
                    ref.$toasted.show(ref.__(response.data.message), { type: 'error' });
                }
            }).catch(function (error) {
                ref.$toasted.show(ref.__('Error Occurred!'), { type: 'error' });
            });
        },
        changeSort: function () {
            var ref = this;
            window.axios.put('/nova-vendor/content-manager/panel/update-sort', {
                panels: ref.panels,
            }).then(function (response) {
                if(response.data.status){
                    ref.$toasted.show(ref.__(response.data.message), { type: 'success' });
                } else {
                    ref.$toasted.show(ref.__(response.data.message), { type: 'error' });
                }
            }).catch(function (error) {
                ref.$toasted.show(ref.__('Error Occurred!'), { type: 'error' });
            });
        },
    },
    mounted() {
        var ref = this;
        ref.getPanels();
    }
}
</script>

<style lang="scss">
    .panel-create-button {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        border: 9px dashed var(--primary-30);
        border-radius: 9px;
        height: 100px;
        &:hover {
            border-color: var(--primary);
            .fa-plus{
                color: var(--primary) !important;
            }
        }
        .fa-plus{
            font-size: 50px;
            color: var(--primary-30) !important;
        }
    }
</style>
