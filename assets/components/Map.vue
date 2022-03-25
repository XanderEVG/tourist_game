<template>
    <div class="map" id="map" style="width: 100%; height: 100vh"></div>
    <!-- <Transition name="fade">
        <component :is="ballonIs" :marker="currentMarker" @closeBallon="closeBallonHandler" />
    </Transition> -->
</template>

<script>
//import MarkerBallon from "./MapBallon/MarkerBallon.vue";
//import NewMarkerBallonForm from "./MapBallon/NewMarkerBallonForm.vue";
export default {
    name: "Map",

    components: {
        /*MarkerBallon, NewMarkerBallonForm*/
    },

    data() {
        return {
            map: {},
            markers: [],
            currentMarker: {
                id: 0,
                coords: [],
                name: "",
                difficulty: {
                    id: null,
                    name: "",
                },
                description: "",
                imgSrc: "",
                visited: false,
            },
            // showBallon: false,
            // showBallonNewMarker: false,
        };
    },

    //computed: {
    //     ballonIs() {
    //         if (this.showBallon) {
    //             return "MarkerBallon";
    //         } else if (this.showBallonNewMarker) {
    //             return "NewMarkerBallonForm";
    //         }
    //     },
    // },

    watch: {
        showBallonNewMarker(newVal) {
            if (newVal === false) {
                let markers = this.map.geoObjects;
                let length = markers.getLength();
                //console.log(markers.get(length - 1));
                markers.remove(markers.get(length - 1));
                // markers.add([57.203, 65.54]);
            }
        },
    },

    async created() {
        await this.getMarkers();
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
            createMarkBtn.events.add("select", () => {
                map.cursors.push("crosshair");
                //that.closeBallonHandler();
                map.events.once("click", function (e) {
                    // Получение координат щелчка
                    let coords = e.get("coords");
                    map.cursors.push("grab");
                    createMarkBtn.deselect();
                    console.log(coords);
                    let newDot = new ymaps.Placemark(coords, { hintContent: "Новая метка" }, { preset: "islands#Icon", iconColor: "#d02828" });
                    that.currentMarker = {
                        id: 0,
                        coords: [],
                        name: "",
                        difficulty: {
                            id: null,
                            name: "",
                        },
                        description: "",
                        imgSrc: "",
                        visited: false,
                    };
                    newDot.marker = that.currentMarker;

                    map.geoObjects.add(newDot);
                    map.setCenter(coords);
                });
            });

            map.controls.add(createMarkBtn, { float: "left", maxWidth: 150 });

            //создаю кластер
            let clusterVisited = new ymaps.Clusterer();
            let clusterUnVisited = new ymaps.Clusterer();
            let markersForVisitedCluster = [];
            let markersForUnVisitedCluster = [];

            //создаю метки

            that.markers.forEach((marker) => {
                let dot;
                if (marker.visited) {
                    //если посещено
                    dot = new ymaps.Placemark(
                        marker.coords,
                        {
                            hintContent: marker.name,
                            openBalloonOnClick: false,
                            balloonContent: `
                                <div class="ballon">
                                    <div class="ballon__head">
                                        <p class="ballon__title"> ${marker.name} <span>${marker.visited ? "(посещено)" : ""}</span></p>
                                    </div>
                                    <div class="ballon__Box">
                                        <p class="ballon__subtitle">Сложность :</p>
                                        <p class="ballon__text">${marker.difficulty.name}</p>
                                    </div>
                                    <div class="ballon__description">
                                        ${marker.description}
                                    </div>
                                    <div class="ballon__fotoBox">
                                        <img class="ballon__foto" src="${marker.imgSrc}" />
                                    </div>
                                    <div class="ballon__btnBox">
                                        <button class="ballon__btn">Построить маршрут</button>
                                        <button class="ballon__btn">В избранное</button>
                                    </div>
                                </div>`,
                        },
                        {
                            preset: "islands#dotIcon",
                            openBalloonOnClick: false,
                            hideIconOnBalloonOpen: false,
                        }
                    );
                    dot.marker = marker;
                    markersForVisitedCluster.push(dot);
                } else {
                    //иначе
                    dot = new ymaps.Placemark(
                        marker.coords,
                        {
                            hintContent: marker.name,
                            balloonContent: `
                                <div class="ballon">
                                    <div class="ballon__head">
                                        <p class="ballon__title"> ${marker.name} <span>${marker.visited ? "(посещено)" : ""}</span></p>
                                    </div>
                                    <div class="ballon__Box">
                                        <p class="ballon__subtitle">Сложность :</p>
                                        <p class="ballon__text">${marker.difficulty.name}</p>
                                    </div>
                                    <div class="ballon__description">
                                        ${marker.description}
                                    </div>
                                    <div class="ballon__fotoBox">
                                        <img class="ballon__foto" src="${marker.imgSrc}" />
                                    </div>
                                    <div class="ballon__btnBox">
                                        <button class="ballon__btn">Построить маршрут</button>
                                        <button class="ballon__btn">В избранное</button>
                                    </div>
                                </div>`,
                        },
                        {
                            preset: "islands#Icon",
                            openBalloonOnClick: false,
                            hideIconOnBalloonOpen: false,
                        }
                    );
                    dot.marker = marker;
                    markersForUnVisitedCluster.push(dot);
                }
                //событие клика на метку

                dot.events.add("click", function (e) {
                    // that.showBallon = true;
                    // that.showBallonNewMarker = false;
                    let target = e.get("target");
                    openSideBallon(target.properties.get("balloonContent"));
                    that.currentMarker = dot.marker;
                    map.setCenter(dot.geometry.getCoordinates());
                });
            });
            clusterVisited.add(markersForVisitedCluster);
            clusterUnVisited.add(markersForUnVisitedCluster);
            map.geoObjects.add(clusterVisited);
            map.geoObjects.add(clusterUnVisited);
            that.map = map;

            function openSideBallon(html) {
                console.log(html);
            }
        });
    },

    methods: {
        getMarkers() {
            fetch("/api/marker")
                .then((response) => response.json())
                .then((res) => {
                    res.data.forEach((el) => {
                        el.coords = [];
                        el.coords.push(el.latitude);
                        el.coords.push(el.longitude);
                        this.markers.push(el);
                    });
                });
        },

        // closeBallonHandler() {
        //     this.showBallon = false;
        //     this.showBallonNewMarker = false;
        // },
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
