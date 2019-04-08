<template>
    <k-dialog
        ref="dialog"
        size="medium"
    >
        <k-form
            v-if="data"
            ref="form"

            :fields="fields"
            v-model="data.form.data"
            @submit="submit"
        />
        <div
            v-else
            class="loading"
        >
            <LoadingIndicator />
        </div>
        <ReviewDialogActions
            slot="footer"
            :status="data ? data.form.status : {}"
            @cancel="cancel"
            @submit="submit"
        />
    </k-dialog>
</template>

<style>
    .loading {
        padding: 5rem 0;
    }
</style>

<script>
    import Api from '../../lib/Api.js';
    import slug from '../../helpers/slug.js';

    import ReviewDialogActions from './ReviewDialogActions.vue';
    import LoadingIndicator from '../LoadingIndicator/LoadingIndicator.vue';


    export default {

        components: {
            ReviewDialogActions,
            LoadingIndicator,
        },

        props: {
            url: null,
        },

        data() {
            return {
                data: null,
                isLoading: false,
            };
        },

        watch: {
            'data.form.data.title'(title) {
                this.data.form.data.slug = slug(title);
            },
        },

        computed: {
            fields() {
                if(!this.data) return;

                let mandatoryFields = {
                    title: {
                        label: this.$t('title'),
                        type: 'text',
                        required: true,
                        icon: 'title',
                    },
                    slug: {
                        label: this.$t('slug'),
                        type: 'text',
                        required: true,
                        counter: false,
                        icon: 'url',
                    },
                };

                return Object.assign(mandatoryFields, this.data.form.fields);
            },
        },

        methods: {

            async open() {
                this.$refs.dialog.open();

                // if the url hasn’t changed since the last time
                // the modal has been opened, do not fetch the
                // data again and keep the current data.
                if(this.data && this.url === this.data.url) {
                    return;
                }

                this.fetchFormData();
            },

            async submit() {
                let response;
                let route;

                try {
                    response = await Api.createPage(this.url, this.data.form.data);
                } catch(error) {
                    this.$refs.dialog.error(error.message);
                    return;
                }

                this.$refs.dialog.close();
                this.$store.dispatch('notification/success', this.$t('import.success'));
                this.$emit('success', {
                    pageId: response.pageId,
                });
            },

            cancel() {
                this.$refs.dialog.close();
                this.$emit('cancel');
            },

            async fetchFormData() {
                this.isLoading = true;
                this.data = null;

                try {
                    this.data = await Api.getForm(this.url);
                } catch(error) {
                    this.$refs.dialog.error(error.message);
                }

                this.isLoading = false;
            },

        },

    }
</script>