<template>
    <div class="loading-indicator">
      <div style="--index: 0"></div>
      <div style="--index: 1"></div>
      <div style="--index: 2"></div>
    </div>
</template>

<style>
    .loading-indicator {
        text-align: center;
        padding: 1.5rem;
    }

    .loading-indicator > div {
        width: .75rem;
        height: .75rem;
        background-color: #777;

        border-radius: 50%;
        display: inline-block;
        animation-name: bounce;
        animation-duration: 1.5s;
        animation-delay: calc(var(--index) * .15s);
        animation-timing-function: ease-in-out;
        animation-iteration-count: infinite;
    }

    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
</style>