<template>
    <div class="map" id="map" style="width: 100%; height: 100vh"></div>
    <Transition name="fade">
        <div class="ballon" v-if="showBallon">
            <div class="ballon__head">
                <p class="ballon__title">{{ currentMarker.title }}</p>
                <button class="ballon__close" @mousedown="showBallon = !showBallon">close</button>
            </div>
            <div class="ballon__Box">
                <p class="ballon__subtitle">Сложность :</p>
                <p class="ballon__text">{{ currentMarker.difficult }}</p>
            </div>
            <div class="ballon__description" v-if="currentMarker.desc">
                {{ currentMarker.desc }}
            </div>
            <div class="ballon__fotoBox" v-if="currentMarker.imgSrc">
                <img class="ballon__foto" :src="currentMarker.imgSrc" />
            </div>
            <div class="ballon__btnBox">
                <button class="ballon__btn">Построить маршрут</button>
                <button class="ballon__btn">В избранное</button>
            </div>
        </div>
    </Transition>
    <!-- название сложность описание фото кнопки("построить маршрут"-если получится и "в избранное")-->
</template>

<script>
export default {
    name: "Map",

    data() {
        return {
            markers: [],
            currentMarker: {
                id: null,
                coords: [],
                title: "",
                difficult: "",
                desc: "",
                imgSrc: "",
            },
            showBallon: false,
        };
    },

    created() {
        this.getMarkers();
        let that = this;
        ymaps.ready(() => {
            let map = new ymaps.Map("map", {
                center: [57.153055, 65.534328],
                zoom: 10,
                type: "yandex#hybrid",
            });
            map.controls.remove("searchControl"); //удаляю лишние элементы управления

            // var location = ymaps.geolocation.get();

            // location.then(
            //     function (result) {
            //         // Добавление местоположения на карту.
            //         this.map.setCenter(result.geoObjects);
            //     },
            //     function (err) {
            //         console.log("Ошибка: " + err);
            //     }
            // );
            map.controls.remove("geolocationControl");
            map.controls.remove("routeButtonControl");
            map.controls.remove("trafficControl");
            map.controls.remove("fullscreenControl");

            //создаю кнопку добавления метки
            let createMarkBtn = new ymaps.control.Button("<b>Добавить метку<b>");
            createMarkBtn.events.add("select", (event) => {
                map.events.once("click", function (e) {
                    // Получение координат щелчка
                    let coords = e.get("coords");
                    createMarkBtn.deselect();
                    console.log(coords);
                });
            });

            map.controls.add(createMarkBtn, { float: "left", maxWidth: 150 });

            //создаю метки
            that.markers.forEach((marker) => {
                let dot = new ymaps.Placemark(marker.coords);
                dot.marker = marker;
                dot.events.add("click", function () {
                    that.showBallon = true;
                    that.currentMarker = dot.marker;
                    map.panTo(marker.coords);
                });

                map.geoObjects.add(dot);
            });
        });
    },

    methods: {
        getMarkers() {
            this.markers.push(
                {
                    id: 123,
                    coords: [57.153055, 65.534328],
                    title: "Город",
                    difficult: "Легко",
                    desc: "12312313",
                    imgSrc: "https://via.placeholder.com/1820x980",
                },
                {
                    id: 124,
                    coords: [57.123, 65.534958],
                    title: "Город",
                    difficult: "Сложно",
                    desc: "12312313",
                    imgSrc: "https://via.placeholder.com/1820x980",
                }
            );
        },
    },
};
</script>

<style lang="scss">
@import "../sassVariable/_variable.scss";

.ymaps-2-1-79-controls__control_toolbar {
    margin-top: 50px;
}
.ymaps-2-1-79-map-copyrights-promo {
    display: none;
}
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
