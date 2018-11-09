<template>
  <div>
    <h1>New User</h1>

    <div v-if="loading" class="alert alert-info" role="status">Loading...</div>
    <div v-if="error" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ error }}</div>

    <UserForm :handle-submit="create" :values="item" :errors="violations"></UserForm>
    <router-link :to="{ name: 'UserList' }" class="btn btn-default">Back to list</router-link>
  </div>
</template>

<script>
  import UserForm from './Form.vue';
  import { createNamespacedHelpers } from 'vuex';

  const { mapActions, mapGetters } = createNamespacedHelpers('user/create');

  export default {
    components: {
      UserForm
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
        this.$store.dispatch('user/create/create', item);
      }
    },
    watch: {
      created: function (created) {
        if (created) {
          this.$router.push({ name: 'UserUpdate', params: { id: created['@id']} });
        }
      }
    }
  }
</script>
