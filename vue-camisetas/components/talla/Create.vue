<template>
  <div>
    <h1>New Talla</h1>

    <div v-if="loading" class="alert alert-info" role="status">Loading...</div>
    <div v-if="error" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ error }}</div>

    <TallaForm :handle-submit="create" :values="item" :errors="violations"></TallaForm>
    <router-link :to="{ name: 'TallaList' }" class="btn btn-default">Back to list</router-link>
  </div>
</template>

<script>
  import TallaForm from './Form.vue';
  import { createNamespacedHelpers } from 'vuex';

  const { mapActions, mapGetters } = createNamespacedHelpers('talla/create');

  export default {
    components: {
      TallaForm
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
        this.$store.dispatch('talla/create/create', item);
      }
    },
    watch: {
      created: function (created) {
        if (created) {
          this.$router.push({ name: 'TallaUpdate', params: { id: created['@id']} });
        }
      }
    }
  }
</script>
