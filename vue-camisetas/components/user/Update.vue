<template>
  <div>
    <h1>Edit {{ item && item['@id'] }}</h1>

    <div v-if="created" class="alert alert-success" role="status">{{ created['@id'] }} created.</div>
    <div v-if="updated" class="alert alert-success" role="status">{{ updated['@id'] }} updated.</div>
    <div v-if="retrieveLoading || updateLoading || deleteLoading"class="alert alert-info" role="status">Loading...</div>
    <div v-if="retrieveError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ retrieveError }}</div>
    <div v-if="updateError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ updateError }}</div>
    <div v-if="deleteError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ deleteError }}</div>

    <UserForm v-if="item" :handle-submit="update" :values="item" :errors="violations" :initialValues="retrieved"></UserForm>
    <router-link v-if="item" :to="{ name: 'UserList' }" class="btn btn-default">Back to list</router-link>
    <button @click="del" class="btn btn-danger">Delete</button>
  </div>
</template>

<script>
  import UserForm from './Form.vue';
  import { mapGetters } from 'vuex';

  export default {
    created () {
      this.$store.dispatch('user/update/retrieve', decodeURIComponent(this.$route.params.id));
    },
    components: {
      UserForm
    },
    computed: {
      ...mapGetters({
        retrieveError: 'user/update/retrieveError',
        retrieveLoading: 'user/update/retrieveLoading',
        updateError: 'user/update/updateError',
        updateLoading: 'user/update/updateLoading',
        deleteError: 'user/del/error',
        deleteLoading: 'user/del/loading',
        created: 'user/create/created',
        deleted: 'user/del/deleted',
        retrieved: 'user/update/retrieved',
        updated: 'user/update/updated',
        violations: 'user/update/violations'
      })
    },
    data: function() {
      return {
        item: {}
      }
    },
    methods: {
      update (values) {
        this.$store.dispatch('user/update/update', {item: this.retrieved, values: values });
      },
      del () {
        if (window.confirm('¿Seguro quieres eliminar?'))
          this.$store.dispatch('user/del/delete', this.retrieved);
      },
      reset () {
        this.$store.dispatch('user/update/reset');
        this.$store.dispatch('user/del/reset');
        this.$store.dispatch('user/create/reset');

      }
    },
    watch: {
      deleted: function (deleted) {
        if (deleted) {
          this.$router.push({ name: 'UserList' });
        }
      }
    },
    beforeDestroy() {
      this.reset();
    }
  }
</script>
