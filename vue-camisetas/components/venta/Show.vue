<template>
  <div class="pa-5">
    <v-snackbar
            :color="snackbarColor"
            :timeout="3000"
            :top="true"
            :multi-line="true"
            v-model="snackbar"
    >
      {{ snackbarText }}
    </v-snackbar>
    <v-card>
      <v-container style="position: fixed; z-index: 2001" fill-height justify-center
                   v-show="loading || retrieveLoadingVenta || retrieveLoadingTalla">
        <v-progress-circular indeterminate :size="70" :width="3" color="success"></v-progress-circular>
      </v-container>
    </v-card>

    <v-card>
      <v-card-title>
        <span class="headline">Venta</span>
        <v-btn icon flat @click.native="closeVenta" class="modal-btn-close">
          <v-icon>close</v-icon>
        </v-btn>
      </v-card-title>
      <div v-if="retrieved">
        <v-card
                max-width="500"
                class="ml-3"
        >
          <v-layout
                  tag="v-card-text"
                  text-xs-left
                  wrap
          >
            <v-flex xs12 xs5 mr-3 mb-2><strong>Usuario:</strong> {{retrieved.user.fullName}}</v-flex>
            <v-flex xs12 xs5 mr-3 mb-2><strong>Creado:</strong> {{formatDate(retrieved.createAt )}}</v-flex>
            <v-flex xs12 mr-3 mb-2 v-if="retrieved.lastUpdate"><strong>Última actualización:</strong> {{formatDate(retrieved.lastUpdate )}}</v-flex>
            <v-flex xs12 mr-3 mb-2><strong>Stock total:</strong> {{stockTotal}}</v-flex>
            <v-flex xs12 mr-3 mb-2><strong>Venta total:</strong> {{ventaTotal}}</v-flex>
            <v-flex xs12 mr-3 mb-2><strong>En stock:</strong> {{stockTotal - ventaTotal}}</v-flex>
          </v-layout>
        </v-card>
        <v-data-table
                :headers="headers"
                :items="retrieved.productos"
                hide-actions
        >
          <template slot="items" slot-scope="props">
            <td>
              <v-chip>
                <img height="35" v-bind:src="getImageUrl(props.item.producto.imagen.path)"
                     class="py-1"/>
                <label class="pl-2 d-inline">{{ props.item.producto.nombre }}</label>
              </v-chip>
            </td>
            <td v-for="talla in props.item.tallas_table">
              <v-tooltip top v-if="talla">
                <label slot="activator" v-bind:style="{color: talla.vendidas == talla.stock ? 'teal' : 'orange'}">
                  {{talla.stock != 0 ? talla.vendidas+' de '+talla.stock : '-----'}}
                </label>
                <span>{{ talla.lastUpdate ? 'Actualizado: ' + formatDate(talla.lastUpdate) : '0 venta' }}</span>
              </v-tooltip>
              <label v-else>-----</label>
            </td>
          </template>
          <v-alert slot="no-results" :value="true" color="error" icon="warning">
            No se encontraron resultado para "{{ search }}".
          </v-alert>
        </v-data-table>
      </div>
    </v-card>
  </div>
</template>
<script>
    import {mapGetters} from 'vuex';
    import {API_HOST} from '../../config/_entrypoint';
    import moment from 'moment';

    export default {
        data() {
            return {
                stockTotal: 0,
                ventaTotal: 0,
                fromUser: false,
                loading: false,
                search: '',
                headers: [],
                snackbar: false,
                snackbarText: '',
                snackbarColor: 'success',
                flag: false,
            }
        },
        computed: {
            ...mapGetters({
                retrieved: 'venta/update/retrieved',
                productos: 'producto/list/items',
                tallas: 'talla/list/items',
                retrieveLoadingVenta: 'venta/update/retrieveLoading',
                retrieveLoadingTalla: 'talla/list/loading',
            })
        },
        watch: {
            dialog(val) {
                val || this.close()
            },
            snackbar(val) {
                val || (this.snackbarColor = 'success')
            }
        },
        methods: {
            formatDate(date){
                return date?moment(date).format('DD/MM/YYYY'):"";
            },
            closeVenta() {
                if (this.fromUser)
                    this.$router.push({name: 'VentaList', params: {user: this.fromUser}})
                else
                    this.$router.push({name: 'VentaList'})
            },
            getImageUrl(path) {
                return API_HOST + '/' + path;
            },
            error(message) {
                this.flag = true;
                this.snackbarColor = 'error';
                this.snackbarText = message;
                this.snackbar = true;
            },
            getItem() {
                this.$store.dispatch('venta/update/retrieve', '/ventas/' + decodeURIComponent(this.$route.params.id)).then(() => {
                    this.setTable();
                });
            },
            setTable() {
                let $this = this, tallas = [];
                this.headers.push({text: '', value: ''});
                this.tallas.forEach(item => {
                    $this.headers.push({text: item.nombre, value: 'nombre'});
                    $this.retrieved.productos.forEach(item2 => {
                        if (typeof item2.tallas_table == typeof undefined)
                            item2.tallas_table = []
                        let filter = item2.tallas.filter(item3 => item3.talla.id == item.id);
                        if (filter.length){
                            item2.tallas_table.push(filter[0]);
                            $this.stockTotal += filter[0].cantidad;
                            $this.ventaTotal += filter[0].vendidas;
                        }
                        else
                            item2.tallas_table.push(false);
                    });
                    tallas = [];
                });
            }
        },
        created() {
            this.$store.dispatch('venta/update/reset');
            if (typeof this.$route.params.user != typeof undefined)
                this.fromUser = decodeURIComponent(this.$route.params.user);

            if (!this.tallas.length)
                this.$store.dispatch('talla/list/getItems').then(() => {
                    this.getItem();
                });
            else
                this.getItem();
        }
    }
</script>
