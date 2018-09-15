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
            @submit="submit()"
        />
        <footer class="k-dialog-footer" slot="footer">
            <ReviewDialogActions
                :status="data ? data.form.status : {}"
                @cancel="cancel()"
                @submit="submit()"
            />
        </footer>
    </k-dialog>
</template>

<script>
    import Api from '../../lib/Api.js';
    import slug from '../../helpers/slug.js';

    import ReviewDialogActions from './ReviewDialogActions.vue';

    export default {

        components: {
            ReviewDialogActions,
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
                        label: this.$t('page.title'),
                        type: 'text',
                        required: true,
                        icon: 'title',
                    },
                    slug: {
                        label: this.$t('page.slug'),
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

                if(this.data && this.url === this.data.url) {
                    return;
                }

                this.fetchFormData();
            },

            async submit() {
                try {
                    let response = await Api.createPage(this.url, this.data.form.data);
                } catch(error) {
                    this.$refs.dialog.error(error.message);
                    return;
                }

                this.$store.dispatch('notification/success', 'Page created!');
                this.$emit('success');
                this.$refs.dialog.close();
            },

            cancel() {
                this.$emit('cancel');
                this.$refs.dialog.close();
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