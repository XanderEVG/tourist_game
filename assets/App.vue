<template>
    <router-view />
</template>

<style lang="scss">
* {
    margin: 0;
    padding: 0;
    font-family: "roboto main";
}
.container {
    margin: 0 auto;
    max-width: clamp(370px, 75%, 1040px);
}
.posRel {
    position: relative;
}
@font-face {
    font-family: "roboto main";
    font-weight: 400;
    src: url("./fonts/Roboto-Regular.ttf") format("truetype");
}

@font-face {
    font-family: "roboto head";
    font-weight: 500;
    src: url("./fonts/Roboto-MediumItalic.ttf") format("truetype");
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
