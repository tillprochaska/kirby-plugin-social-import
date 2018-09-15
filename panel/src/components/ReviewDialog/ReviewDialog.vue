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
                let response;
                let route;

                try {
                    response = await Api.createPage(this.url, this.data.form.data);
                    route = this.$api.pages.link(response.pageData.id);
                } catch(error) {
                    this.$refs.dialog.error(error.message);
                    return;
                }

                this.$emit('success');
                this.$refs.dialog.close();
                this.$store.dispatch("notification/success", this.$t('page.created'));
                this.$router.push(route);
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