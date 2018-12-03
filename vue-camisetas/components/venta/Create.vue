<template>
  <div>
    <h1>New Venta</h1>

    <div v-if="loading" class="alert alert-info" role="status">Loading...</div>
    <div v-if="error" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ error }}</div>

    <VentaForm :handle-submit="create" :values="item" :errors="violations"></VentaForm>
    <router-link :to="{ name: 'VentaList' }" class="btn btn-default">Back to list</router-link>
  </div>
</template>

<script>
  import VentaForm from './Form.vue';
  import { createNamespacedHelpers } from 'vuex';

  const { mapActions, mapGetters } = createNamespacedHelpers('venta/create');

  export default {
    components: {
      VentaForm
    },
    data: function() {
      return {
        item: {}
      }
    },
    computed: mapGetters([
      'error',
      'loading',
      'created',
      'violations'
    ]),
    methods: {
      create: function(item) {
        this.$store.dispatch('venta/create/create', item);
      }
    },
    watch: {
      created: function (created) {
        if (created) {
          this.$router.push({ name: 'VentaUpdate', params: { id: created['@id']} });
        }
      }
    }
  }
</script>
