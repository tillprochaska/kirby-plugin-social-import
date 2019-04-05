<template>
    <k-field
        :label="label"
        :help="!url ? help : null"
    >
        <k-input
            type="url"
            theme="field"
            ref="input"
            v-bind="{ placeholder }"
            v-model="url"
        />

        <Preview
            class="result"
            v-bind="{ url }"
            @cancel="clear()"
            @review="review()"
        />

        <ReviewDialog
            ref="dialog"
            v-bind="{ url }"
            @success="clear()"
        />
    </k-field>
</template>


<style>
    .result {
        margin-top: 1rem;
    }
</style>

<script>
    import Preview from './Preview/Preview.vue';
    import ReviewDialog from './ReviewDialog/ReviewDialog.vue';

    export default {

        components: {
            Preview,
            ReviewDialog,
        },

        props: {
            label: null,
            help: null,
            placeholder: null,
        },

        data() {
            return {
                url: '',
            };
        },

        methods: {

            clear() {
                this.url = null;
                this.preview = null;
                this.$refs.input.focus();
            },

            review() {
                this.$refs.dialog.open();
            },

        },

    }
</script>