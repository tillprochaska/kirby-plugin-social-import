<template>
    <Card>
        <svg viewBox="0 0 500 108" version="1.1" width="100%" height="6.625rem" preserveAspectRatio="none">
            <rect
                clip-path="url(#elements)"
                x="0" y="0"
                width="500" height="108"
                style="fill: url('#gradient');"
            ></rect>
            <defs data-v-51fa412d="">
                <clipPath id="elements">
                    <rect x="0" y="8" width="420" height="8"></rect>
                    <rect x="0" y="40" width="480" height="8"></rect>
                    <rect x="0" y="64" width="320" height="8"></rect>
                    <rect x="0" y="96" width="420" height="8"></rect>
                </clipPath>
                <linearGradient id="gradient">
                    <stop offset="-0.25" stop-color="#efefef">
                        <animate
                            attributeName="offset"
                            values="-2; 1"
                            dur="1s"
                            repeatCount="indefinite"
                        ></animate>
                    </stop>
                    <stop offset="0.25" stop-color="#f8f8f8">
                        <animate
                            attributeName="offset"
                            values="-1.5; 1.5"
                            dur="1s"
                            repeatCount="indefinite"
                        ></animate>
                    </stop>
                    <stop offset="0.75" stop-color="#efefef">
                        <animate
                            attributeName="offset"
                            values="-1; 2"
                            dur="1s"
                            repeatCount="indefinite"
                        ></animate>
                    </stop>
                </linearGradient>
            </defs>
        </svg>
    </Card>
</template>

<script>
    import Card from '../Card/Card.vue';

    export default {

        components: {
            Card,
        },

    }
</script>