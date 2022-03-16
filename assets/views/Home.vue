<template>
    <div class="posRel">
        <Modal v-if="showAuth" @close="showAuth = !showAuth"><ModalLogin /></Modal>
        <Headline :isLogin="isLogin" :userName="userName" @clickOnLogin="showAuth = !showAuth" />
    </div>
</template>

<script>
import Headline from "../components/Headline.vue";
import Modal from "../components/Modal.vue";
import ModalLogin from "./modalWindow/ModalLogin.vue";
export default {
    components: { Headline, Modal, ModalLogin },
    name: "Home",

    data() {
        return {
            isLogin: false,
            userName: "Василий П.",
            showAuth: false,
        };
    },
};
</script>
