<template>
  <div>
    <h1>Edit {{ item && item['@id'] }}</h1>

    <div v-if="created" class="alert alert-success" role="status">{{ created['@id'] }} created.</div>
    <div v-if="updated" class="alert alert-success" role="status">{{ updated['@id'] }} updated.</div>
    <div v-if="retrieveLoading || updateLoading || deleteLoading"class="alert alert-info" role="status">Loading...</div>
    <div v-if="retrieveError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ retrieveError }}</div>
    <div v-if="updateError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ updateError }}</div>
    <div v-if="deleteError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ deleteError }}</div>

    <ProductoForm v-if="item" :handle-submit="update" :values="item" :errors="violations" :initialValues="retrieved"></ProductoForm>
    <router-link v-if="item" :to="{ name: 'ProductoList' }" class="btn btn-default">Back to list</router-link>
    <button @click="del" class="btn btn-danger">Delete</button>
  </div>
</template>

<script>
  import ProductoForm from './Form.vue';
  import { mapGetters } from 'vuex';

  export default {
    created () {
      this.$store.dispatch('producto/update/retrieve', decodeURIComponent(this.$route.params.id));
    },
    components: {
      ProductoForm
    },
    computed: {
      ...mapGetters({
        retrieveError: 'producto/update/retrieveError',
        retrieveLoading: 'producto/update/retrieveLoading',
        updateError: 'producto/update/updateError',
        updateLoading: 'producto/update/updateLoading',
        deleteError: 'producto/del/error',
        deleteLoading: 'producto/del/loading',
        created: 'producto/create/created',
        deleted: 'producto/del/deleted',
        retrieved: 'producto/update/retrieved',
        updated: 'producto/update/updated',
        violations: 'producto/update/violations'
      })
    },
    data: function() {
      return {
        item: {}
      }
    },
    methods: {
      update (values) {
        this.$store.dispatch('producto/update/update', {item: this.retrieved, values: values });
      },
      del () {
        if (window.confirm('Are you sure you want to delete this item?'))
          this.$store.dispatch('producto/del/delete', this.retrieved);
      },
      reset () {
        this.$store.dispatch('producto/update/reset');
        this.$store.dispatch('producto/del/reset');
        this.$store.dispatch('producto/create/reset');

      }
    },
    watch: {
      deleted: function (deleted) {
        if (deleted) {
          this.$router.push({ name: 'ProductoList' });
        }
      }
    },
    beforeDestroy() {
      this.reset();
    }
  }
</script>
