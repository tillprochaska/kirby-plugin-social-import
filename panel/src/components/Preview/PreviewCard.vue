<template>
    <Card>
        <k-headline class="title">{{ title }}</k-headline>
        <k-text class="description">{{ description }}</k-text>
        <k-text class="meta" theme="help">{{ meta }}</k-text>
    </Card>
</template>

<script>
    import Card from '../Card/Card.vue';

    export default {

        components: {
            Card,
        },

        props: {
            title: null,
            description: null,
            meta: null,
        },

    }
</script>

<style>
    .description {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
        margin: .5rem 0;
    }

    .meta, .title {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }
</style>