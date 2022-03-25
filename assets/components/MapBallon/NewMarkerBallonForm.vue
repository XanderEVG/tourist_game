<template>
    <div class="ballon">
        <div class="ballon__head">
            <p class="ballon__title">{{ marker.title }}</p>
            <button class="ballon__close" @mousedown="$emit('closeBallon')">close</button>
        </div>
        <div class="ballon__Box">
            <p class="ballon__subtitle">Сложность :</p>
            <p class="ballon__text">{{ marker.difficult }}</p>
        </div>
        <div class="ballon__description" v-if="marker.description">
            {{ marker.description }}
        </div>
        <div class="ballon__fotoBox" v-if="marker.imgSrc">
            <img class="ballon__foto" :src="marker.imgSrc" />
        </div>
        <div class="ballon__btnBox">
            <button class="ballon__btn">Построить маршрут</button>
            <button class="ballon__btn">В избранное</button>
        </div>
    </div>
</template>

<script>
export default {
    name: "NewMarkerBallonForm",

    props: {
        marker: Object,
    },
};
</script>

<style lang="scss" scoped>
@import "../../sassVariable/_variable.scss";
.ballon {
    position: absolute;
    top: 20%;
    left: 5%;
    z-index: 10;
    width: clamp(200px, 35%, 450px);
    padding: 10px 16px;
    background-color: #eaebffde;
    box-shadow: 0 0 15px 5px #000;
    border-radius: 4px;

    &__head {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    &__title {
        font-family: "roboto head";
        font-size: 16px;
        color: $textColor;
    }

    &__close {
        font-family: "Material Icons";
        font-size: 16px;
        background-color: #0000;
        border: none;
        color: $textColor;
        transition-duration: 0.3s;

        &:hover {
            color: #000;
        }
    }

    &__Box {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
    }

    &__subtitle {
        font-family: "roboto head";
        font-size: 14px;
        color: $textColor;
    }

    &__text {
        flex-grow: 1;
        font-size: 14px;
        text-align: end;
    }

    &__fotoBox {
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    &__foto {
        max-width: 100%;
    }

    &__btnBox {
        display: flex;
        justify-content: space-around;
        margin-top: 10px;
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
}
</style>
