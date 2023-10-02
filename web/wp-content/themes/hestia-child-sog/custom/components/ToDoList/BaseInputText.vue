<template>
  <div>
    <input 
      type="text" 
      class="input" 
      v-model="inputValue"
    />
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  props: {
    modelValue: String
  },
  setup (props, context) {
    const inputValue = computed({
      get: () => props.modelValue || '',
      set: val => {
        context.emit('update:modelValue', val)
      }
    })
    return {
      inputValue,
    }
  },
};
</script>

<style lang="scss" scoped>
@import "../variables.scss";

.input {
  width: 100%;
  padding: 8px 10px;
  border: 1px solid $vue-blue;
}
</style>
