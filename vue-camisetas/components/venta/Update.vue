<template>
  <div>
    <h1>Edit {{ item && item['@id'] }}</h1>

    <div v-if="created" class="alert alert-success" role="status">{{ created['@id'] }} created.</div>
    <div v-if="updated" class="alert alert-success" role="status">{{ updated['@id'] }} updated.</div>
    <div v-if="retrieveLoading || updateLoading || deleteLoading"class="alert alert-info" role="status">Loading...</div>
    <div v-if="retrieveError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ retrieveError }}</div>
    <div v-if="updateError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ updateError }}</div>
    <div v-if="deleteError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ deleteError }}</div>

    <VentaForm v-if="item" :handle-submit="update" :values="item" :errors="violations" :initialValues="retrieved"></VentaForm>
    <router-link v-if="item" :to="{ name: 'VentaList' }" class="btn btn-default">Back to list</router-link>
    <button @click="del" class="btn btn-danger">Delete</button>
  </div>
</template>

<script>
  import VentaForm from './Form.vue';
  import { mapGetters } from 'vuex';

  export default {
    created () {
      this.$store.dispatch('venta/update/retrieve', decodeURIComponent(this.$route.params.id));
    },
    components: {
      VentaForm
    },
    computed: {
      ...mapGetters({
        retrieveError: 'venta/update/retrieveError',
        retrieveLoading: 'venta/update/retrieveLoading',
        updateError: 'venta/update/updateError',
        updateLoading: 'venta/update/updateLoading',
        deleteError: 'venta/del/error',
        deleteLoading: 'venta/del/loading',
        created: 'venta/create/created',
        deleted: 'venta/del/deleted',
        retrieved: 'venta/update/retrieved',
        updated: 'venta/update/updated',
        violations: 'venta/update/violations'
      })
    },
    data: function() {
      return {
        item: {}
      }
    },
    methods: {
      update (values) {
        this.$store.dispatch('venta/update/update', {item: this.retrieved, values: values });
      },
      del () {
        if (window.confirm('Are you sure you want to delete this item?'))
          this.$store.dispatch('venta/del/delete', this.retrieved);
      },
      reset () {
        this.$store.dispatch('venta/update/reset');
        this.$store.dispatch('venta/del/reset');
        this.$store.dispatch('venta/create/reset');

      }
    },
    watch: {
      deleted: function (deleted) {
        if (deleted) {
          this.$router.push({ name: 'VentaList' });
        }
      }
    },
    beforeDestroy() {
      this.reset();
    }
  }
</script>
