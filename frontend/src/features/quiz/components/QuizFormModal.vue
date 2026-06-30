<script setup lang="ts">
import { reactive, ref, watch } from "vue";

import type {
  AdminQuizDto,
  QuizFormErrors,
  QuizFormValues,
} from "../types/admin-quiz.types";
import { validateQuizForm } from "../validation/quiz-form.validation";

const props = defineProps<{
  open: boolean;
  quiz: AdminQuizDto | null;
  isSubmitting: boolean;
  errorMessage?: string;
}>();

const emit = defineEmits<{
  "update:open": [value: boolean];
  submit: [values: QuizFormValues];
}>();

const values = reactive<QuizFormValues>({ quizName: "" });
const errors = ref<QuizFormErrors>({});

function resetForm(): void {
  values.quizName = props.quiz?.quiz_name ?? "";
  errors.value = {};
}

function submitForm(): void {
  errors.value = validateQuizForm(values);
  if (Object.keys(errors.value).length > 0) return;

  emit("submit", { ...values });
}

watch(
  () => [props.open, props.quiz] as const,
  ([open]) => {
    if (open) resetForm();
  },
);
</script>

<template>
  <UModal
    :open="open"
    :title="quiz ? 'Edit quiz' : 'Tambah quiz'"
    :description="
      quiz
        ? 'Perbarui nama quiz untuk course ini.'
        : 'Setiap course hanya dapat memiliki satu quiz.'
    "
    :dismissible="!isSubmitting"
    :ui="{ content: 'sm:max-w-lg', footer: 'justify-end' }"
    @update:open="emit('update:open', $event)"
  >
    <template #body>
      <form id="quiz-form" class="space-y-5" @submit.prevent="submitForm">
        <div
          v-if="errorMessage"
          role="alert"
          class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
        >
          {{ errorMessage }}
        </div>

        <UFormField
          label="Nama quiz"
          name="quiz_name"
          required
          :error="errors.quizName"
        >
          <UInput
            v-model="values.quizName"
            maxlength="255"
            placeholder="Contoh: Quiz Akhir Course"
            class="w-full"
            autofocus
          />
        </UFormField>
      </form>
    </template>

    <template #footer>
      <UButton
        class="p-3 cursor-pointer"
        label="Batal"
        color="neutral"
        variant="outline"
        :disabled="isSubmitting"
        @click="emit('update:open', false)"
      />
      <UButton
        class="p-3 cursor-pointer"
        form="quiz-form"
        type="submit"
        :label="quiz ? 'Simpan perubahan' : 'Tambah quiz'"
        :loading="isSubmitting"
      />
    </template>
  </UModal>
</template>
