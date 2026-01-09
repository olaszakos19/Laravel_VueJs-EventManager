<template>
  <div class="backdrop">
    <div class="modal">
      <h3 class="text-lg font-semibold mb-4">
        Biztosan törölni szeretnéd?
      </h3>

      <p class="text-sm text-gray-600 mb-6">
        A művelet nem visszavonható.
      </p>

      <div class="flex justify-end gap-3">
        <button
          class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
          @click="emit('close')"
        >
          Mégse
        </button>

        <button
          class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
          @click="emit('confirm')"
        >
          Igen, törlöm
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const emit = defineEmits<{
  (e: "close"): void;
  (e: "confirm"): void;
}>();
</script>

