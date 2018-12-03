<template>
  <form @submit.prevent="handleSubmit(item)">
    <div :class="{ 'form-group': true, 'has-error': (errors && errors.feria) }">
      <label for="_feria" class="control-label">feria</label>
      <input v-model="item.feria" type="text"  placeholder=""  id="_feria" class="form-control" />
      <span v-if="errors && errors.feria" class="help-block" id="_feria_helpBlock">{{ errors.feria }}</span>
    </div>
    <div :class="{ 'form-group': true, 'has-error': (errors && errors.createAt) }">
      <label for="_createAt" class="control-label">createAt</label>
      <input v-model="item.createAt" type="text"  placeholder=""  id="_createAt" class="form-control" />
      <span v-if="errors && errors.createAt" class="help-block" id="_createAt_helpBlock">{{ errors.createAt }}</span>
    </div>
    <div :class="{ 'form-group': true, 'has-error': (errors && errors.lastUpdate) }">
      <label for="_lastUpdate" class="control-label">lastUpdate</label>
      <input v-model="item.lastUpdate" type="text"  placeholder=""  id="_lastUpdate" class="form-control" />
      <span v-if="errors && errors.lastUpdate" class="help-block" id="_lastUpdate_helpBlock">{{ errors.lastUpdate }}</span>
    </div>
    <div :class="{ 'form-group': true, 'has-error': (errors && errors.closeAt) }">
      <label for="_closeAt" class="control-label">closeAt</label>
      <input v-model="item.closeAt" type="text"  placeholder=""  id="_closeAt" class="form-control" />
      <span v-if="errors && errors.closeAt" class="help-block" id="_closeAt_helpBlock">{{ errors.closeAt }}</span>
    </div>
    <div :class="{ 'form-group': true, 'has-error': (errors && errors.open) }">
      <label for="_open" class="control-label">open</label>
      <input v-model="item.open" type="checkbox"  placeholder=""  id="_open" class="form-control" />
      <span v-if="errors && errors.open" class="help-block" id="_open_helpBlock">{{ errors.open }}</span>
    </div>
    <div :class="{ 'form-group': true, 'has-error': (errors && errors.user) }">
      <label for="_user" class="control-label">user</label>
      <input v-model="item.user" type="text"  placeholder=""  id="_user" class="form-control" />
      <span v-if="errors && errors.user" class="help-block" id="_user_helpBlock">{{ errors.user }}</span>
    </div>
    <div :class="{ 'form-group': true, 'has-error': (errors && errors.venta) }">
      <label for="_venta" class="control-label">venta</label>
      <input v-model="item.venta" type="text"  placeholder=""  id="_venta" class="form-control" />
      <span v-if="errors && errors.venta" class="help-block" id="_venta_helpBlock">{{ errors.venta }}</span>
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
