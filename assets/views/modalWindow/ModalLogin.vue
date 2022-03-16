<template>
    <div class="modalLogin">
        <p class="modalLogin__title">Авторизация</p>
        <div class="modalLogin__inputBox">
            <MainInput v-model:value="login" :preicon="'person'" class="modalLogin__input">Логин</MainInput>
            <MainInput v-model:value="password" :inputType="'password'" :preicon="'lock'" class="modalLogin__input">Пароль</MainInput>
        </div>
        <div class="modalLogin__btnBox">
            <MainBtn primary>Войти</MainBtn>
            <MainBtn secondary>Регистрация</MainBtn>
            <MainBtn texted>Забыли пароль?</MainBtn>
        </div>

        <p class="modalLogin__otherMethodTitle">Войти через соцсети</p>
        <div class="modalLogin__otherMethodBox">
            <button class="modalLogin__otherMethod"><img class="modalLogin__icon" src="../../img/VkLogo.png" alt="" /><span class="modalLogin__otherMethodText">Войти через ВКонтакте</span></button>
            <button class="modalLogin__otherMethod"><img class="modalLogin__icon" src="../../img/GoogleLogo.png" alt="" /><span class="modalLogin__otherMethodText">Войти через Google</span></button>
        </div>
    </div>
</template>

<script>
import MainInput from "../../components/MainInput.vue";
import MainBtn from "../../components/MainBtn.vue";
export default {
    name: "ModalLogin",

    components: { MainInput, MainBtn },

    data() {
        return {
            login: "",
            password: "",
        };
    },
};
</script>

<style lang="scss" scoped>
@import "../../sassVariable/_variable.scss";

.modalLogin {
    display: flex;
    flex-direction: column;
    &__title {
        margin-right: 20px;
        font-size: 24px;
    }

    &__inputBox {
        display: flex;
        flex-direction: column;
        margin-top: 20px;
    }

    &__input {
        margin: 10px 0;
        width: 100%;
    }

    &__btnBox {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }

    &__otherMethodBox {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    &__otherMethodTitle {
        margin-top: 20px;
    }

    &__otherMethod {
        display: flex;
        align-items: center;
        border: 2px solid $primaryColor;
        border-radius: 24px;
        box-sizing: content-box;
        padding: 8px 16px;
        background-color: #0000;
        width: 100%;
        height: 32px;
        margin-right: 20px;
        transition-duration: 0.3s;
        filter: grayscale(1);

        &:hover {
            filter: grayscale(0);
            background-color: $secondaryColorHover;
        }

        &:active {
            background-color: $secondaryColorActive;
        }

        &:last-child {
            margin-right: 0px;
        }
    }
    &__icon {
        width: 40px;
        flex: 0 1 32px;
        margin-right: 4px;
    }

    &__otherMethodText {
        flex: 1 1 40%;
    }
}
</style>
