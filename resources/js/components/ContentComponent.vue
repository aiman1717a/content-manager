<template>
    <card
        class="bg-white flex flex-col flex-wrap items-center justify-center"
        style="min-height: 300px;margin-bottom: 50px;"
    >
        <heading class="mr-auto my-4 ml-4">{{heading}}</heading>
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
                    <a :href="content.article.topic.slug + '/' + content.article.id">
                        <h3 class="text-90 font-normal text-2xl rtl mb-3"> {{content.article.headline}}</h3>
                    </a>
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
                        <button class="btn btn-default btn-primary float-right" @click="startEdit(content.id, type, content.article.id)">
                            Edit
                        </button>
                    </template>
                    <template v-else>
                        <button class="btn btn-default btn-primary float-right" @click="startEdit(content.id, type)">
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
            <div class="new-article m-4 w-1/5" @click="addContent('main-content')">
                <plus></plus>
            </div>
        </draggable>
    </card>
</template>

<script>
    import plus from './plus.vue';
    import deleteBtn from './delete-btn.vue';
    import draggable from 'vuedraggable';
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { faTrashAlt } from '@fortawesome/free-solid-svg-icons'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    library.add(faTrashAlt);

    export default {
        props: ['heading', 'type'],
        components: {
            plus,
            deleteBtn,
            draggable,
            FontAwesomeIcon
        },
        data() {
            return {
                articles: [],
                contentState: {
                    id: null,
                    status: false,
                    type: null,
                    article_id: null,
                },
                contents: []
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
        methods: {
            addContent: function (type) {
                var ref = this;

                window.axios.post('/nova-vendor/content-manager/content', {
                    model: Nova.config.content_manager.content_model,
                    order: ref.contents.length + 1,
                    type: ref.type,
                }).then(function (response) {
                    console.log(response);
                    ref.contents = response.data;
                    if(response.data.status){
                        ref.getContents();
                        ref.$toasted.show(ref.__(response.data.messagge), { type: 'success' });
                    } else {
                        ref.$toasted.show(ref.__(response.data.messagge), { type: 'error' });
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
                        ref.$toasted.show(ref.__(response.data.messagge), { type: 'success' });
                    } else {
                        ref.$toasted.show(ref.__(response.data.messagge), { type: 'error' });
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
            startEdit: function (id, type, article_id = null) {
                this.contentState.id = id;
                this.contentState.type = type;
                this.contentState.status = true;
                this.contentState.article_id = article_id;
            },

            cancelEdit: function () {
                this.contentState.id = null;
                this.contentState.type = null;
                this.contentState.status = false;
                this.contentState.article_id = null;
            },

            getContents: function () {
                var ref = this;
                window.axios.post('/nova-vendor/content-manager/content/all', {
                    model: Nova.config.content_manager.content_model,
                    type: ref.type,
                }).then(function (response) {
                    if(response.data.status){
                        ref.contents = response.data.data;
                    }
                    ref.$toasted.show(ref.__(response.data.message), { type: 'success' });
                }).catch(function (error) {
                    ref.$toasted.show(ref.__('Error Occurred!'), { type: 'error' });
                });
            },
            getArticles: function () {
                var ref = this;
                window.axios.post('/nova-vendor/content-manager/article/all', {
                    model: Nova.config.content_manager.article_model,
                }).then(function (response) {
                    if(response.data.status){
                        ref.articles = response.data.data;
                        ref.$toasted.show(ref.__(response.data.messagge), { type: 'success' });
                    } else {
                        ref.$toasted.show(ref.__(response.data.messagge), { type: 'error' });
                    }
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
                        ref.$toasted.show(ref.__(response.data.messagge), { type: 'success' });
                    } else {
                        ref.$toasted.show(ref.__(response.data.messagge), { type: 'error' });
                    }
                }).catch(function (error) {
                    ref.$toasted.show(ref.__('Error Occurred!'), { type: 'error' });
                });
            }
        },
        mounted() {
            var ref = this;
            ref.getContents();
            ref.getArticles();
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
