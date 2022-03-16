<template>
    <label for="" class="input" :class="{ input_focusOrNot: isFocus }"
        ><span v-if="preicon" class="input__preicon">{{ preicon }}</span
        ><input
            :type="inputType"
            :value="value"
            @input="$emit('update:value', $event.target.value)"
            @focus="focusHandler"
            @blur="blurHandler"
            class="input__input"
            :class="{ input__input_preiconEnable: preicon }" />
        <span class="input__text" :class="{ input__text_focusOrNotEmpty: focusOrNotEmpty, input__text_preiconEnable: preicon }"> <slot></slot></span
    ></label>
</template>

<script>
export default {
    name: "MainInput",
    components: {},
    props: {
        inputType: { type: String, default: "text" },
        value: {
            type: String,
            required: true,
        },
        preicon: String,
    },
    data() {
        return {
            focused: false,
        };
    },
    computed: {
        focusOrNotEmpty() {
            if (this.value != "" || this.isFocus) {
                return true;
            } else {
                return false;
            }
        },
        isFocus() {
            if (this.focused) {
                return true;
            } else {
                return false;
            }
        },
    },
    methods: {
        focusHandler() {
            this.focused = true;
        },
        blurHandler() {
            this.focused = false;
        },
    },
};
</script>

<style lang="scss" scoped>
.input {
    position: relative;
    display: flex;

    &::after {
        content: "";
        display: block;
        position: absolute;
        left: 1px;
        right: 1px;
        bottom: 0;
        width: 100%;
        height: 2px;
        background-color: rgba(0, 0, 0, 0.08);
        z-index: 2;
    }

    &_focusOrNot::after {
        background-color: #1071ff;
        transition: 0.5s;
    }

    &__text {
        position: absolute;
        top: 8px;
        left: 10px;
        font-size: 18px;
        background-color: #0000;
        font-family: "roboto main";
        transition-duration: 0.25s;
        z-index: 2;

        &_focusOrNotEmpty {
            top: -12px !important;
            left: 10px !important;
            font-size: 14px !important;
        }

        &_preiconEnable {
            top: 8px;
            left: 24px;
        }
    }

    &__input {
        font-size: 18px;
        padding: 8px 10px;
        flex-grow: 1;
        border: none;
        display: flex;

        &:focus {
            outline: none;
        }

        &_preiconEnable {
            padding-left: 16px;
        }
    }

    &__preicon {
        position: absolute;
        top: 4px;
        left: -9px;
        font-family: "Material Icons";
        font-size: 22px;
        color: rgb(80, 78, 78);
    }
}
</style>
