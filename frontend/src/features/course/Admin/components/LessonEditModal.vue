<script setup lang="ts">
import { reactive, ref, watch } from 'vue'

import type {
  AdminLessonDto,
  LessonFormErrors,
  LessonFormValues,
} from '../types/course.types'
import { validateLessonForm } from '../validation/lesson-form.validation'

const props = defineProps<{
  open: boolean
  lesson: AdminLessonDto | null
  isSubmitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  'update:open': [value: boolean]
  submit: [values: LessonFormValues]
}>()

const values = reactive<LessonFormValues>({
  title: '',
  videoUrl: '',
})
const errors = ref<LessonFormErrors>({})

function resetForm(): void {
  values.title = props.lesson?.title ?? ''
  values.videoUrl = props.lesson?.video?.video_url ?? ''
  errors.value = {}
}

function submitForm(): void {
  errors.value = validateLessonForm(values)

  if (Object.keys(errors.value).length > 0) return

  emit('submit', { ...values })
}

watch(
  () => [props.open, props.lesson] as const,
  ([open]) => {
    if (open) resetForm()
  },
)
</script>

<template>
  <UModal
    :open="open"
    title="Edit lesson"
    description="Perbarui judul dan video YouTube untuk lesson ini."
    :dismissible="!isSubmitting"
    :ui="{ content: 'sm:max-w-xl', footer: 'justify-end' }"
    @update:open="emit('update:open', $event)"
  >
    <template #body>
      <form id="lesson-edit-form" class="space-y-5" @submit.prevent="submitForm">
        <div
          v-if="errorMessage"
          role="alert"
          class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
        >
          {{ errorMessage }}
        </div>

        <UFormField
          label="Judul lesson"
          name="title"
          required
          :error="errors.title"
        >
          <UInput
            v-model="values.title"
            maxlength="255"
            placeholder="Contoh: Pengenalan Akad Syariah"
            class="w-full"
            autofocus
          />
        </UFormField>

        <UFormField
          label="Link video YouTube"
          name="video_url"
          :error="errors.videoUrl"
          help="Opsional. Kosongkan field untuk menghapus video yang sudah tersimpan."
        >
          <UInput
            v-model="values.videoUrl"
            type="url"
            inputmode="url"
            placeholder="https://www.youtube.com/watch?v=..."
            class="w-full"
          />
        </UFormField>
      </form>
    </template>

    <template #footer>
      <UButton
        label="Batal"
        color="neutral"
        variant="outline"
        :disabled="isSubmitting"
        @click="emit('update:open', false)"
      />
      <UButton
        form="lesson-edit-form"
        type="submit"
        label="Simpan perubahan"
        :loading="isSubmitting"
      />
    </template>
  </UModal>
</template>
