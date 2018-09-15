<template>
    <k-button-group>
        <k-button
            icon="cancel"
            theme="negative"
            @click="$emit('cancel')"
        >
            Cancel
        </k-button>
        <k-dropdown>
            <k-button
                icon="check"
                theme="positive" 
                @click="$emit('submit')"
            >
                Save as draft
            </k-button>
        </k-dropdown>
    </k-button-group>
</template>

<script>
    export default {

        props: {
            status: {},
        },

    }
</script>