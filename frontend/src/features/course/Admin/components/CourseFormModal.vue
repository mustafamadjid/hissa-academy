<script setup lang="ts">
import { reactive, ref, watch } from 'vue'

import type {
  CourseDto,
  CourseFormErrors,
  CourseFormValues,
  CourseStatus,
} from '../types/course.types'
import { validateCourseForm } from '../validation/course-form.validation'

const props = defineProps<{
  open: boolean
  course: CourseDto | null
  isSubmitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  'update:open': [value: boolean]
  submit: [values: CourseFormValues]
}>()

const statusOptions: Array<{ label: string; value: CourseStatus }> = [
  { label: 'Draft', value: 'draft' },
  { label: 'Aktif', value: 'active' },
  { label: 'Nonaktif', value: 'inactive' },
]

const values = reactive<CourseFormValues>({
  courseName: '',
  description: '',
  minimumScore: 75,
  status: 'draft',
})
const errors = ref<CourseFormErrors>({})

function resetForm(): void {
  values.courseName = props.course?.name ?? ''
  values.description = props.course?.description ?? ''
  values.minimumScore = props.course?.minimum_score ?? 75
  values.status = isCourseStatus(props.course?.status) ? props.course.status : 'draft'
  errors.value = {}
}

function isCourseStatus(status: string | undefined): status is CourseStatus {
  return status === 'active' || status === 'draft' || status === 'inactive'
}

function submitForm(): void {
  errors.value = validateCourseForm(values)

  if (Object.keys(errors.value).length > 0) return

  emit('submit', { ...values })
}

watch(
  () => [props.open, props.course] as const,
  ([open]) => {
    if (open) resetForm()
  },
)
</script>

<template>
  <UModal
    :open="open"
    :title="course ? 'Edit course' : 'Tambah course'"
    :description="
      course
        ? 'Perbarui informasi course yang dipilih.'
        : 'Lengkapi informasi dasar untuk membuat course baru.'
    "
    :dismissible="!isSubmitting"
    :ui="{ content: 'sm:max-w-2xl', footer: 'justify-end' }"
    @update:open="emit('update:open', $event)"
  >
    <template #body>
      <form id="course-form" class="space-y-5" @submit.prevent="submitForm">
        <div
          v-if="errorMessage"
          role="alert"
          class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
        >
          {{ errorMessage }}
        </div>

        <UFormField
          label="Nama course"
          name="course_name"
          required
          :error="errors.courseName"
        >
          <UInput
            v-model="values.courseName"
            placeholder="Contoh: Dasar Perbankan Syariah"
            maxlength="255"
            class="w-full"
            autofocus
          />
        </UFormField>

        <UFormField
          label="Deskripsi"
          name="description"
          required
          :error="errors.description"
          :hint="`${values.description.length}/255`"
        >
          <UTextarea
            v-model="values.description"
            placeholder="Jelaskan ringkasan materi course"
            :rows="4"
            maxlength="255"
            autoresize
            class="w-full"
          />
        </UFormField>

        <div class="grid gap-5 sm:grid-cols-2">
          <UFormField
            label="Nilai kelulusan minimum"
            name="minimum_score"
            required
            :error="errors.minimumScore"
          >
            <UInput
              v-model.number="values.minimumScore"
              type="number"
              min="0"
              max="100"
              step="0.01"
              class="w-full"
            />
          </UFormField>

          <UFormField label="Status" name="status" required :error="errors.status">
            <USelect
              v-model="values.status"
              :items="statusOptions"
              class="w-full"
            />
          </UFormField>
        </div>
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
        form="course-form"
        type="submit"
        :label="course ? 'Simpan perubahan' : 'Tambah course'"
        :loading="isSubmitting"
      />
    </template>
  </UModal>
</template>
