<template>
  <form @submit.prevent="handleSubmit(item)">
    <div :class="{ 'form-group': true, 'has-error': (errors && errors.imagen) }">
      <label for="_imagen" class="control-label">imagen</label>
      <input v-model="item.imagen" type="text"  placeholder=""  id="_imagen" class="form-control" />
      <span v-if="errors && errors.imagen" class="help-block" id="_imagen_helpBlock">{{ errors.imagen }}</span>
    </div>
    <div :class="{ 'form-group': true, 'has-error': (errors && errors.tallas) }">
      <label for="_tallas" class="control-label">tallas</label>
      <input v-model="item.tallas" type="text"  placeholder=""  id="_tallas" class="form-control" />
      <span v-if="errors && errors.tallas" class="help-block" id="_tallas_helpBlock">{{ errors.tallas }}</span>
    </div>
    <div :class="{ 'form-group': true, 'has-error': (errors && errors.nombre) }">
      <label for="_nombre" class="control-label">nombre</label>
      <input v-model="item.nombre" type="text"  placeholder=""  id="_nombre" class="form-control" />
      <span v-if="errors && errors.nombre" class="help-block" id="_nombre_helpBlock">{{ errors.nombre }}</span>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</template>

<script>
  export default {
    props: {
      handleSubmit: {
        type: Function,
        required: true,
      },
      values: {
        type: Object,
        required: true
      },
      errors: {
        type: Object
      },
      initialValues: {
        type: Object
      }
    },
    computed: {
      item: function () {
        return this.initialValues ? this.initialValues : this.values;
      }
    }
  }
</script>
