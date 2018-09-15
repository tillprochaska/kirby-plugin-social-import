<template>
    <div class="card" :class="{ error: theme }">
        <k-icon v-if="icon" class="icon" :type="icon" />
        <div class="content"><slot/></div>
    </div>
</template>

<style scoped>
    .card {
        display: flex;
        width: 100%;
        padding: 1rem 1.2rem;

        background-color: #fff;
        box-shadow: 0 2px 5px rgba(22,23,26,.05);
        border-radius: 1px;
    }

    .icon {
        margin-right: 1rem;
        flex-grow: 0;
        flex-shrink: 0;
    }

    .content {
        overflow: hidden;
        flex-grow: 1;
        flex-shrink: 1;
    }

    .error {
        border-left: 3px solid #c82829;
    }

    .error .icon {
        color: #c82829;
    }
</style>

<script>

    export default {

        props: {
            theme: null,
            icon: null,
        },

    }
</script>