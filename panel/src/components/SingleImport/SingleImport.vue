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
            @cancel="reset"
            @review="review"
        />

        <ReviewDialog
            ref="dialog"
            v-bind="{ url }"
            @success="success"
        />
    </k-field>
</template>


<style>
    .result {
        margin-top: 1rem;
    }
</style>

<script>
    import Preview from '../Preview/Preview.vue';
    import ReviewDialog from '../ReviewDialog/ReviewDialog.vue';

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

            reset() {
                // reset input field and preview
                this.url = null;
                this.preview = null;
                this.$refs.input.focus();
            },

            success(data) {
                console.log(data)
                // redirect to the newly created page
                let route = this.$api.pages.link(data.pageId);
                this.$router.push(route);
            },

            review() {
                this.$refs.dialog.open();
            },

        },

    }
</script>