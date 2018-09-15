<template>

    <div v-if="url">

        <PreviewCard
            v-if="data"
            :title="data.preview.title"
            :description="data.preview.description"
            :meta="data.preview.meta"
        />

        <PreviewSkeleton v-if="isLoading" />

        <k-button-group v-if="isLoading || data">
            <k-button
                icon="cancel"
                theme="negative"
                :disabled="isLoading"
                @click="$emit('cancel')"
            >
                Cancel
            </k-button>
            <k-button
                icon="check"
                theme="positive" 
                :disabled="isLoading"
                @click="$emit('review')"
            >
                Review data
            </k-button>
        </k-button-group>

        <Card v-if="error" theme="error" icon="alert">
            {{ error.message }}
        </Card>

    </div>

</template>

<script>
    import Api from '../../lib/Api.js';

    import Card from '../Card/Card.vue';
    import PreviewSkeleton from './PreviewSkeleton.vue';
    import PreviewCard from './PreviewCard.vue';

    export default {

        components: {
            Card,
            PreviewSkeleton,
            PreviewCard,
        },

        props: {
            url: null,
        },

        data() {
            return {
                data: null,
                error: null,
                isLoading: false,
            };
        },

        watch: {
            async url(url) {
                this.data = null;
                this.error = null;

                if(!url) {
                    this.isLoading = false;
                    return;
                }

                this.isLoading = true;

                try {
                    this.data = await Api.getPreview(url);
                } catch(error) {
                    this.error = error;
                }

                this.isLoading = false;
            },
        }

    }
</script>