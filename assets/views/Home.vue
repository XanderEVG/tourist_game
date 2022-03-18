<template>
    <div class="posRel">
        <Transition name="fade">
            <Modal v-if="showAuth" @close="showAuth = !showAuth"><ModalLogin /></Modal>
        </Transition>
        <Headline :isLogin="isLogin" :userName="userName" @clickOnLogin="showAuth = !showAuth" />
        <Map />
    </div>
</template>

<script>
import Headline from "../components/Headline.vue";
import Modal from "../components/Modal.vue";
import ModalLogin from "./modalWindow/ModalLogin.vue";
import Map from "../components/Map.vue";
export default {
    components: { Headline, Modal, ModalLogin, Map },
    name: "Home",

    data() {
        return {
            isLogin: false,
            userName: "Василий П.",
            showAuth: false,
            settings: {
                apiKey: "",
                lang: "ru_RU",
                coordorder: "latlong",
                enterprise: false,
                version: "2.1",
            },
        };
    },
};
</script>

<style lang="scss" scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-from,
.fade-to {
    opacity: 0;
}
</style>
