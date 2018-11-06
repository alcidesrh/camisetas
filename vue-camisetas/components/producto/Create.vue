<template>
  <div>
    <h1>New Producto</h1>

    <div v-if="loading" class="alert alert-info" role="status">Loading...</div>
    <div v-if="error" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ error }}</div>

    <ProductoForm :handle-submit="create" :values="item" :errors="violations"></ProductoForm>
    <router-link :to="{ name: 'ProductoList' }" class="btn btn-default">Back to list</router-link>
  </div>
</template>

<script>
  import ProductoForm from './Form.vue';
  import { createNamespacedHelpers } from 'vuex';

  const { mapActions, mapGetters } = createNamespacedHelpers('producto/create');

  export default {
    components: {
      ProductoForm
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
        this.$store.dispatch('producto/create/create', item);
      }
    },
    watch: {
      created: function (created) {
        if (created) {
          this.$router.push({ name: 'ProductoUpdate', params: { id: created['@id']} });
        }
      }
    }
  }
</script>
