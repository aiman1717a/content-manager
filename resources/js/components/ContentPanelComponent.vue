<template>
    <card
        class="bg-white "
        style="min-height: 300px;margin-bottom: 50px;"
        v-if="panel !== null"
    >
        <div class="flex flex-row flex-wrap items-center justify-center">
<!--            <heading class="mr-auto my-4 ml-4">{{panel.name}}</heading>-->
            <input class="panel-name appearance-none rounded py-2 px-3 focus:outline-none text-gray-700 mr-auto my-4 ml-4 text-2xl" id="username" type="text" placeholder="Username" v-model="panel.name">
            <toggle-button
                class="panel-status-btn mr-4"
                v-model="panel.status"
                :width="90"
                :labels="{checked: 'Active', unchecked: 'Disabled'}"
                @change="updatePanelStatus()"
            />
            <button class="panel-delete-btn py-2 px-4 rounded focus:outline-none text-2xl" @click="deletePanel()">
                <font-awesome-icon :icon="['fas', 'trash-alt']" />
            </button>
        </div>

        <div class="flex flex-col flex-wrap items-center justify-center">
            <draggable class="panel-1 flex flex-wrap" v-model="contents" @sort="changeSort()">
                    <div class="article m-4 w-1/5 cursor-move" v-for="content in contents" :key="content.id">
                        <div class="left-tool" v-if="content.article !== null">
                            <div class="article-id">
                                {{content.article.id}}
                            </div>
                        </div>
                        <div class="toolbox cursor-pointer">
                            <div class="tool" @click="deleteContent(content.id)">
                                <font-awesome-icon :icon="['fas', 'trash-alt']" />
                            </div>
                        </div>
                        <template v-if="content.article !== null">
                            <img :src="content.article[article_thumbnail]" alt="Image Not Found">
                            <tempalte v-if="content.article.topic != null">
                                <a :href="content.article.topic.slug + '/' + content.article.id">
                                    <h3 class="text-90 font-normal text-2xl rtl mb-3"> {{content.article.headline}}</h3>
                                </a>
                            </tempalte>
                        </template>
                        <template v-else>
                            <img :src="storageUrl + '/image_holder.png'" alt="Image Not Found">
                        </template>

                        <template v-if="contentState.status">
                            <template v-if="contentState.id === content.id">
                                <select class="block appearance-none w-full bg-white border border-70 text-80 py-3 px-4 pr-8 rounded mb-4 rtl" id="grid-state" v-model="contentState.article_id">
                                    <option v-for="article in articles" v-bind:value="article.id">{{article[article_display_name]}}</option>
                                </select>
                                <button class="btn btn-default btn-primary float-right ml-4" @click="updateContent(contentState)">
                                    Save
                                </button>
                            </template>

                        </template>

                        <template v-if="!contentState.status">
                            <template v-if="content.article !== null">
                                <button class="btn btn-default btn-primary float-right" @click="startEdit(content.id, panel.id, content.article.id)">
                                    Edit
                                </button>
                            </template>
                            <template v-else>
                                <button class="btn btn-default btn-primary float-right" @click="startEdit(content.id, panel.id)">
                                    Edit
                                </button>
                            </template>
                        </template>
                        <template v-if="contentState.id === content.id">
                            <button class="btn btn-default btn-primary float-right" @click="cancelEdit()">
                                Cancel
                            </button>
                        </template>


                    </div>
                    <div class="new-article m-4 w-1/5" @click="createContent()">
                        <plus></plus>
                    </div>
            </draggable>
        </div>

    </card>
</template>

<script>
    import plus from './plus.vue';
    import deleteBtn from './delete-btn.vue';
    import draggable from 'vuedraggable';
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { faTrashAlt } from '@fortawesome/free-solid-svg-icons'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { ToggleButton } from 'vue-js-toggle-button'
    library.add(faTrashAlt);

    export default {
        props: ['panel', 'contents'],
        components: {
            plus,
            deleteBtn,
            draggable,
            FontAwesomeIcon,
            ToggleButton
        },
        data() {
            return {
                articles: [],
                contentState: {
                    id: null,
                    panel_id: null,
                    status: false,
                    article_id: null,
                },
                // contents: []
            }
        },
        computed: {
            storageUrl: function () {
                return Nova.config.content_manager.storage_url;
            },
            article_display_name: function () {
                return Nova.config.content_manager.article.display_name;
            },
            article_thumbnail: function () {
                return Nova.config.content_manager.article.thumbnail;
            }
        },
        watch: {
            'panel.name': function () {
                var ref = this;
                ref.updatePanelName()
            }
        },
        methods: {
            getContents: function () {
                var ref = this;
                window.axios.post('/nova-vendor/content-manager/content/all', {
                    model: Nova.config.content_manager.content_model,
                    panel_id: ref.panel.id,
                }).then(function (response) {
                    if(response.data.status){
                        ref.contents = response.data.data;
                    }
                    ref.$toasted.show(ref.__(response.data.message), { type: 'success' });
                }).catch(function (error) {
                    ref.$toasted.show(ref.__('Error Occurred!'), { type: 'error' });
                });
            },
            createContent: function () {
                var ref = this;

                var order = 1;
                if(ref.contents.length !==  0){
                    order = ref.contents.length + 1;
                }
                window.axios.post('/nova-vendor/content-manager/content', {
                    model: Nova.config.content_manager.content_model,
                    order: order,
                    panel_id: ref.panel.id,
                }).then(function (response) {
                    ref.contents = response.data;
                    if(response.data.status){
                        ref.getContents();
                        ref.$toasted.show(ref.__(response.data.message), { type: 'success' });
                    } else {
                        ref.$toasted.show(ref.__(response.data.message), { type: 'error' });
                    }
                }).catch(function (error) {
                    ref.$toasted.show(ref.__('Error Occurred!'), { type: 'error' });
                });
            },
            updateContent: function (contentState) {
                var ref = this;
                window.axios.put('/nova-vendor/content-manager/content', {
                    model: Nova.config.content_manager.content_model,
                    content: contentState,
                }).then(function (response) {
                    if(response.data.status){
                        ref.getContents();
                        ref.$toasted.show(ref.__(response.data.message), { type: 'success' });
                    } else {
                        ref.$toasted.show(ref.__(response.data.message), { type: 'error' });
                    }
                    ref.cancelEdit();
                }).catch(function (error) {
                    ref.$toasted.show(ref.__('Error Occurred!'), { type: 'error' });
                });
            },
            deleteContent: function (id) {
                var ref = this;
                window.axios.post('/nova-vendor/content-manager/content/delete', {
                    model: Nova.config.content_manager.content_model,
                    id: id,
                }).then(function (response) {
                    if(response.data.status){
                        ref.getContents();
                        ref.$toasted.show(ref.__(response.data.messagge), { type: 'success' });
                    } else {
                        ref.$toasted.show(ref.__(response.data.messagge), { type: 'error' });
                    }
                    ref.cancelEdit();
                }).catch(function (error) {
                    ref.$toasted.show(ref.__('Error Occurred!'), { type: 'error' });
                });
            },
            changeSort: function () {
                var ref = this;
                window.axios.put('/nova-vendor/content-manager/content/update-sort', {
                    model: Nova.config.content_manager.content_model,
                    contents: ref.contents,
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
            startEdit: function (id, panel_id, article_id = null) {
                this.contentState.id = id;
                this.contentState.panel_id = panel_id;
                this.contentState.status = true;
                this.contentState.article_id = article_id;
            },

            cancelEdit: function () {
                this.contentState.id = null;
                this.contentState.panel_id = null;
                this.contentState.status = false;
                this.contentState.article_id = null;
            },


            getArticles: function () {
                var ref = this;
                window.axios.post('/nova-vendor/content-manager/article/all', {
                    model: Nova.config.content_manager.article_model,
                }).then(function (response) {
                    if(response.data.status){
                        ref.articles = response.data.data;
                        // ref.$toasted.show(ref.__(response.data.message), { type: 'success' });
                    } else {
                        // ref.$toasted.show(ref.__(response.data.message), { type: 'error' });
                    }
                }).catch(function (error) {
                    ref.$toasted.show(ref.__('Error Occurred!'), { type: 'error' });
                });
            },
            updatePanelName: function () {
                var ref = this;
                window.axios.put('/nova-vendor/content-manager/panel/name', {
                    panel_id: ref.panel.id,
                    name: ref.panel.name,
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
            updatePanelStatus: function () {
                var ref = this;
                window.axios.put('/nova-vendor/content-manager/panel/status', {
                    panel_id: ref.panel.id,
                    status: ref.panel.status
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
            deletePanel: function () {
                var ref = this;
                window.axios.delete('/nova-vendor/content-manager/panel/' + ref.panel.id)
                .then(function (response) {
                    if(response.data.status){
                        ref.panel = null;
                        ref.$toasted.show(ref.__(response.data.message), { type: 'success' });
                    } else {
                        ref.$toasted.show(ref.__(response.data.message), { type: 'error' });
                    }
                }).catch(function (error) {
                    ref.$toasted.show(ref.__('Error Occurred!'), { type: 'error' });
                });
            }
        },
        mounted() {
            var ref = this;
            ref.getArticles();
            this.panelStatus = this.panel.status;
        },
    }
</script>

<style lang="scss">
    .rtl{
        direction: rtl;
    }
    .panel-1 {
        width: 100%;
    }
    .panel-name{
        &:focus{
            box-shadow: 0px 0px 0px 3px  var(--primary);
        }
    }
    .panel-delete-btn{
        color: var(--primary-30);
        margin-right: 20px;
        &:hover{
            color: var(--primary);
        }
        &:active{
            box-shadow: 0px 0px 0px 3px  var(--primary);
        }
    }
    .new-article {
        border: 9px dashed var(--primary-30);
        border-radius: 9px;
        &:hover {
            border-color: var(--primary);
        }
    }
    .plus{
        color: var(--primary-30);
        font-size: 10px;
        padding: 80px;
        &:hover {
            color: var(--primary);
            cursor: pointer;
        }
    }
    .article{
        position: relative;
        background: white;
        img{
            width: 100%;
        }
        a{
            text-decoration: none;
            color: var(--90);
        }
        .left-tool{
            position: absolute;
            top: 0;
            left: 0;
            background: white;
            width: 50px;
            height: 50px;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
        }
        .article-id{
            text-align: center;
        }

        .toolbox{
            position: absolute;
            top: 0;
            right: 0;
            background: white;
            .tool{
                width: 50px;
                height: 50px;
                text-align: center;
                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: center;
                &:hover{
                    .fa-trash-alt{
                        color: red !important;
                    }
                }
                svg{
                    font-size: 20px;
                }
            }
        }
    }
</style>
