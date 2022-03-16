<template>
    <div class="modal">
        <div class="modal__window posRel"><button class="modal__exit" @click="$emit('close')">close</button><slot></slot></div>
    </div>
</template>

<script>
export default {
    name: "Modal",
};
</script>

<style lang="scss" scoped>
.modal {
    position: absolute;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.4);

    display: flex;
    justify-content: center;
    align-items: center;

    &__window {
        background-color: white;
        width: clamp(320px, 40%, 640px);
        box-sizing: border-box;
        padding: 16px 32px;
        box-shadow: 0px 0px 10px 5px rgba(0, 0, 0, 0.6);
        border-radius: 10px;
    }

    &__exit {
        position: absolute;
        right: 16px;
        top: 16px;
        background-color: #0000;
        border: none;
        font-family: "Material Icons";
        font-size: 20px;
        color: black;
        transition-duration: 0.3s;

        &:hover {
            color: rgb(63, 63, 235);
        }
    }
}
</style>
