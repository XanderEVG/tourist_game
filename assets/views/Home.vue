<template>
    <div class="posRel">
        <Transition name="fade">
            <Modal v-if="modal.show" @close="modal.show = !modal.show">
                <Transition name="fade" mode="out-in">
                    <component :is="modalIs" @clickRegBtn="ShowRegModal" />
                </Transition>
            </Modal>
        </Transition>
        <Headline :isLogin="isLogin" :userName="userName" @clickOnLogin="showAuthModal" />
        <Map />
    </div>
</template>

<script>
import Headline from "../components/Headline.vue";
import Modal from "../components/Modal.vue";
import ModalLogin from "./modalWindow/ModalLogin.vue";
import ModalReistration from "./modalWindow/ModalRegistration.vue";
import Map from "../components/Map.vue";
export default {
    components: { Headline, Modal, ModalLogin, ModalReistration, Map },
    name: "Home",

    data() {
        return {
            isLogin: false,
            userName: "Василий П.",
            modal: {
                show: false,
                auth: false,
                registration: false,
            },
            settings: {
                apiKey: "",
                lang: "ru_RU",
                coordorder: "latlong",
                enterprise: false,
                version: "2.1",
            },
        };
    },

    computed: {
        modalIs() {
            if (this.modal.auth) {
                return "ModalLogin";
            } else if (this.modal.registration) {
                return "ModalReistration";
            }
        },
    },

    methods: {
        showAuthModal() {
            this.modal.show = true;
            this.modal.auth = true;
            this.modal.registration = false;
        },

        ShowRegModal() {
            this.modal.show = true;
            this.modal.auth = false;
            this.modal.registration = true;
        },
    },
};
</script>

<style lang="scss" scoped>
.fade-enter-active,
.fade-leave-active {
    transition: all 0.5s ease;
}
.fade-from,
.fade-to {
    opacity: 0;
}
</style>
