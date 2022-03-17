<template>
    <div class="headline">
        <div class="container headline__box">
            <div class="headline__title">Навигатор целей</div>
            <button class="headline__btn" v-if="!isLogin" @click="$emit('clickOnLogin')">Войти</button>
            <div v-else class="headline__loginBox">
                <div class="headline__userName">{{ userName }}</div>
                <button class="headline__btn">Выйти</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Headline",

    props: {
        isLogin: Boolean,
        userName: String,
    },
};
</script>

<style lang="scss" scoped>
@import "../sassVariable/_variable.scss";
.headline {
    background-color: #eaebffde;
    padding: 8px 0;
    position: absolute;
    top: 0px;
    left: 0px;
    z-index: 100;
    width: 100%;

    &__box {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    &__title {
        font-family: "roboto head";
        font-size: 24px;
        color: $textColor;
        flex: 1 1 50%;
    }

    &__btn {
        background-color: #0000;
        padding: 8px 16px;
        border: 2px solid $textColor;
        border-radius: 8px;

        color: $textColor;
        transition-duration: 0.3s;

        &:hover {
            color: #000;
            border: 2px solid #000;
            background-color: rgba(0, 0, 0, 0.1);
        }
        &:active {
            background-color: rgba(0, 0, 0, 0.2);
        }
    }

    &__loginBox {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex: 1 1 clamp(100px, 20%, 520px);
    }

    &__userName {
        margin-right: clamp(5px, 5%, 20px);
    }
}
</style>
