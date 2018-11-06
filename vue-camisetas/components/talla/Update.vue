<template>
  <div>
    <h1>Edit {{ item && item['@id'] }}</h1>

    <div v-if="created" class="alert alert-success" role="status">{{ created['@id'] }} created.</div>
    <div v-if="updated" class="alert alert-success" role="status">{{ updated['@id'] }} updated.</div>
    <div v-if="retrieveLoading || updateLoading || deleteLoading"class="alert alert-info" role="status">Loading...</div>
    <div v-if="retrieveError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ retrieveError }}</div>
    <div v-if="updateError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ updateError }}</div>
    <div v-if="deleteError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ deleteError }}</div>

    <TallaForm v-if="item" :handle-submit="update" :values="item" :errors="violations" :initialValues="retrieved"></TallaForm>
    <router-link v-if="item" :to="{ name: 'TallaList' }" class="btn btn-default">Back to list</router-link>
    <button @click="del" class="btn btn-danger">Delete</button>
  </div>
</template>

<script>
  import TallaForm from './Form.vue';
  import { mapGetters } from 'vuex';

  export default {
    created () {
      this.$store.dispatch('talla/update/retrieve', decodeURIComponent(this.$route.params.id));
    },
    components: {
      TallaForm
    },
    computed: {
      ...mapGetters({
        retrieveError: 'talla/update/retrieveError',
        retrieveLoading: 'talla/update/retrieveLoading',
        updateError: 'talla/update/updateError',
        updateLoading: 'talla/update/updateLoading',
        deleteError: 'talla/del/error',
        deleteLoading: 'talla/del/loading',
        created: 'talla/create/created',
        deleted: 'talla/del/deleted',
        retrieved: 'talla/update/retrieved',
        updated: 'talla/update/updated',
        violations: 'talla/update/violations'
      })
    },
    data: function() {
      return {
        item: {}
      }
    },
    methods: {
      update (values) {
        this.$store.dispatch('talla/update/update', {item: this.retrieved, values: values });
      },
      del () {
        if (window.confirm('Are you sure you want to delete this item?'))
          this.$store.dispatch('talla/del/delete', this.retrieved);
      },
      reset () {
        this.$store.dispatch('talla/update/reset');
        this.$store.dispatch('talla/del/reset');
        this.$store.dispatch('talla/create/reset');

      }
    },
    watch: {
      deleted: function (deleted) {
        if (deleted) {
          this.$router.push({ name: 'TallaList' });
        }
      }
    },
    beforeDestroy() {
      this.reset();
    }
  }
</script>
