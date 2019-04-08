<template>
    <section class="k-section">

        <header class="k-section-header">
          <k-headline>
            {{ label }}
          </k-headline>

          <k-button-group>
            <k-button
                icon="import"
                link="/plugins/socialImportBatch"
            >
                Batch Import
            </k-button>
          </k-button-group>
        </header>

        <div class="k-field">
            <k-input
                type="url"
                theme="field"
                ref="input"
                v-bind="{ placeholder }"
                v-model="url"
            />

            <k-text
                theme="help"
                class="k-field-help"
                v-if="!url"
            >
                {{ help }}
            </k-text>
        </div>

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

    </section>
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