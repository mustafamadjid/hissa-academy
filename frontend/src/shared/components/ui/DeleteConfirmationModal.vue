<script setup lang="ts">
defineProps<{
  open: boolean
  title: string
  description: string
  isDeleting?: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  'update:open': [value: boolean]
  confirm: []
}>()
</script>

<template>
  <UModal
    :open="open"
    :title="title"
    :description="description"
    :dismissible="!isDeleting"
    :ui="{ footer: 'justify-end' }"
    @update:open="emit('update:open', $event)"
  >
    <template #body>
      <p class="text-sm leading-6 text-slate-600">
        Tindakan ini tidak dapat dibatalkan. Pastikan data yang dipilih sudah benar.
      </p>
      <p
        v-if="errorMessage"
        role="alert"
        class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
      >
        {{ errorMessage }}
      </p>
    </template>

    <template #footer>
      <UButton
        label="Batal"
        color="neutral"
        variant="outline"
        :disabled="isDeleting"
        @click="emit('update:open', false)"
      />
      <UButton
        label="Hapus"
        color="error"
        :loading="isDeleting"
        @click="emit('confirm')"
      />
    </template>
  </UModal>
</template>
