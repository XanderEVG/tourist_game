<template>
    <button
        :type="type"
        class="mainBtn posRel"
        :class="{
            mainBtn_primary: primary,
            mainBtn_secondary: secondary,
            mainBtn_texted: texted,
            mainBtn_empty: !primary & !secondary & !texted,
        }"
        @click="$emit('onClick')"
    >
        <span v-if="preIcon" class="mainBtn__preIcon">{{ preIcon }}</span
        ><slot></slot>
    </button>
</template>

<script>
export default {
    name: "MainBtn",
    props: {
        type: { type: String, default: "button" },
        preIcon: String,
        primary: Boolean,
        secondary: Boolean,
        texted: Boolean,
    },
};
</script>

<style lang="scss" scoped>
@import "../sassVariable/_variable.scss";

.mainBtn {
    padding: 15px 20px;
    box-sizing: border-box;
    border: none;
    border-radius: 24px;
    display: flex;
    justify-content: space-around;
    align-items: center;
    font-size: 14px;
    line-height: 18px;
    font-family: "roboto main";
    transition-duration: 0.25s;

    &_primary {
        background: $primaryColor;
        color: #fff;

        &:hover {
            background: $primaryColorHover;
        }

        &:focus {
            background: $primaryColorFocus;
        }

        &:active {
            background: $primaryColorActive;
        }

        &:disabled {
            background: $primaryColorDisabled;
            color: $primaryColorDisabledText;
        }
    }

    &_secondary {
        background: $secondaryColor;
        color: $primaryColor;
        border: 2px solid $primaryColor;

        &:hover {
            background: $secondaryColorHover;
        }

        &:focus {
            background: $secondaryColorFocus;
        }

        &:active {
            background: $secondaryColorActive;
        }

        &:disabled {
            border: 2px solid $secondaryColorDisabledBorder;
            background: $secondaryColorDisabled;
            color: $secondaryColorDisabledText;
        }
    }

    &_texted {
        background: $secondaryColor;
        color: $primaryColor;

        &:hover {
            background: $secondaryColorHover;
        }

        &:focus {
            background: $secondaryColorFocus;
        }

        &:active {
            background: $secondaryColorActive;
        }

        &:disabled {
            background: $secondaryColorDisabled;
            color: $secondaryColorDisabledText;
        }
    }

    &_empty {
        background-color: $secondaryColor;
        color: $subTextColor;

        &:hover {
            background: $secondaryColorHover;
        }

        &:focus {
            background: $secondaryColorFocus;
        }

        &:active {
            background: $secondaryColorActive;
        }

        &:disabled {
            background: $secondaryColorDisabled;
            color: $secondaryColorDisabledText;
        }
    }

    &__preIcon {
        margin-right: 12px;
        font-family: "Material Icons";
        font-size: 14px;
        line-height: 18px;
    }
}
</style>
