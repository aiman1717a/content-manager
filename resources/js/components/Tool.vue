<template>
    <div>
        <heading class="mb-6">Content Manager</heading>
        <ContentComponent heading="Main content" type="main-content"></ContentComponent>
        <ContentComponent heading="Second content" type="second-content"></ContentComponent>
    </div>
</template>

<script>
import ContentComponent from './ContentComponent.vue';

export default {
    components: {
        ContentComponent,
    },
}
</script>

<style lang="scss">

</style>
