<template>
  <div>
    <h1>Show {{ item && item['@id'] }}</h1>

    <div v-if="loading" class="alert alert-info" role="status">Loading...</div>
    <div v-if="error" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ error }}</div>
    <div v-if="deleteError" class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{ deleteError }}</div>

    <div v-if="item" class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Field</th>
            <th>Value</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>apellidos</td>
            <td>{{ item['apellidos'] }}</td>
          </tr>
          <tr>
            <td>username</td>
            <td>{{ item['username'] }}</td>
          </tr>
          <tr>
            <td>password</td>
            <td>{{ item['password'] }}</td>
          </tr>
          <tr>
            <td>roles</td>
            <td>{{ item['roles'] }}</td>
          </tr>
          <tr>
            <td>salt</td>
            <td>{{ item['salt'] }}</td>
          </tr>
          <tr>
            <td>nombre</td>
            <td>{{ item['nombre'] }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <router-link v-if="item" :to="{ name: 'UserList' }" class="btn btn-default">Back to list</router-link>
    <button @click="deleteItem(item)" class="btn btn-danger">Delete</button>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    computed: mapGetters({
      deleteError: 'user/del/error',
      error: 'user/show/error',
      loading: 'user/show/loading',
      item: 'user/show/item',
    }),
    methods: {
      deleteItem (item) {
        if (window.confirm('¿Seguro quieres eliminar?'))
          this.$store.dispatch('user/del/delete', item).then(response => this.$router.push({ name: 'UserList' }));
      }
    },
    created () {
      this.$store.dispatch('user/show/retrieve', decodeURIComponent(this.$route.params.id));
    },
    beforeDestroy() {
      this.$store.dispatch('user/show/reset');
    }
  }
</script>
