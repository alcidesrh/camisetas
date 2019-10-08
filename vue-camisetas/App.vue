<template>
    <v-app>
        <v-toolbar dark color="primary" fixed clipped-right app tabs>
            <img v-show="host" v-bind:src="host+'LocallySourcedCubalogo-111x40.png'"/>
            <v-spacer></v-spacer>
            <v-tabs
                    color="primary"
                    right>
                <v-tabs-slider color="white" style="height: 2px"></v-tabs-slider>
                <v-tab id="usuarios" flat @click="$router.push({name: 'UserList'})">
                    <v-icon class="mr-1">group</v-icon>
                    Usuarios
                </v-tab>
                <v-tab id="stocks" flat @click="$router.push({name: 'StockList'})">
                    <v-icon class="mr-1">account_box</v-icon>
                    Stocks
                </v-tab>
                <v-tab id="ventas" flat @click="$router.push({name: 'VentaList'})">
                    <v-icon class="mr-1">shopping_basket</v-icon>
                    Ventas
                </v-tab>
                <v-tab id="gestion_producto">
                    <v-menu bottom>
                        <v-btn flat slot="activator">
                            <v-icon class="mr-1">settings</v-icon>
                            Gestión de producto
                        </v-btn>
                        <v-list>
                            <v-list-tile id="productos" @click="$router.push({name: 'ProductoList'});">
                                <v-list-tile-title>
                                    <v-icon>format_list_numbered</v-icon>
                                    Camisetas
                                </v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile id="sudaderas" @click="$router.push({name: 'SudaderaList'});">
                                <v-list-tile-title>
                                    <v-icon>format_list_numbered</v-icon>
                                    Sudaderas
                                </v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile id="tallas" @click="$router.push({name: 'TallaList'})">
                                <v-list-tile-title>
                                    <v-icon>format_list_numbered</v-icon>
                                    Tallas
                                </v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </v-tab>

                <v-tab id="close" flat>
                    <a v-bind:href="logoutRoute()" class="v-tabs__item">
                        <v-icon class="mr-1">highlight_off</v-icon>
                        Cerrar Sesión
                    </a>
                </v-tab>
            </v-tabs>
        </v-toolbar>
        <v-content>
            <v-layout>
                <v-flex>
                    <transition name="fade" class="pa-5">
                        <router-view></router-view>
                    </transition>
                </v-flex>
            </v-layout>
        </v-content>
    </v-app>
</template>
<script>
    import {API_HOST} from './config/_entrypoint';

    export default {

        data: () => ({
            loading: false,
            host: false,
        }),
        methods: {
            logoutRoute() {
                return API_HOST + '/logout'
            }
        },
        watch: {},
        created() {
            this.$nextTick(function () {
                if (typeof route_reload != typeof undefined) {
                    if (route_reload.length == 1) {
                        if (route_reload[0] != 'stocks' && route_reload[0] != 'ventas') {
                            if (route_reload[0] == 'stocks-user')
                                document.getElementById('usuarios').firstChild.click();
                            else
                                document.getElementById('gestion_producto').firstChild.click();
                        }
                        else
                         document.getElementById(route_reload[0]).firstChild.click();
                    } else {
                        if (route_reload[0] == 'stocks')
                            document.getElementById(route_reload[0]).firstChild.click();
                        else if (route_reload[0] == 'ventas')
                            document.getElementById(route_reload[0]).firstChild.click();
                        else
                            document.getElementById('gestion_producto').firstChild.click();
                        let path = "";
                        route_reload.forEach(function (item) {
                            path = path + '/' + item;
                        });
                        this.$router.push({path: path});
                    }
                }
                else{
                    document.getElementById('usuarios').firstChild.click();
                }
//----------------------------------------------------------

//---------------------------------------------------------------------------
            })
        }
    }
</script>