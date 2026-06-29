<script setup lang="ts">
import { computed } from "vue";
import { Pencil, Trash2 } from "@lucide/vue";
import type { TableColumn } from "@nuxt/ui";

import type { CourseDto } from "../types/course.types";

const props = defineProps<{
  courses: readonly CourseDto[];
  isLoading: boolean;
  page: number;
  pageSize: number;
  total: number;
}>();

const tableData = computed(() => [...props.courses]);

const emit = defineEmits<{
  "update:page": [value: number];
  edit: [course: CourseDto];
  delete: [course: CourseDto];
}>();

const columns: TableColumn<CourseDto>[] = [
  { accessorKey: "name", header: "Nama Course" },
  { accessorKey: "description", header: "Deskripsi" },
  { accessorKey: "minimum_score", header: "Nilai Kelulusan Min." },
  { accessorKey: "status", header: "Status" },
  { id: "actions", header: "Aksi" },
];

function statusLabel(status: string): string {
  const labels: Record<string, string> = {
    active: "Aktif",
    draft: "Draft",
    inactive: "Nonaktif",
  };

  return labels[status] ?? status;
}

function statusColor(status: string): "success" | "warning" | "neutral" {
  if (status === "active") return "success";
  if (status === "draft") return "warning";
  return "neutral";
}

function visibleRange(page: number, pageSize: number, total: number): string {
  if (total === 0) return "0 course";

  const from = (page - 1) * pageSize + 1;
  const to = Math.min(page * pageSize, total);
  return `${from}-${to} dari ${total} course`;
}
</script>

<template>
  <div
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
  >
    <div class="overflow-x-auto">
      <UTable
        :data="tableData"
        :columns="columns"
        :loading="isLoading"
        loading-color="primary"
        empty="Belum ada course. Tambahkan course pertama Anda."
        :ui="{
          th: 'bg-emerald-50/60 px-5 py-4 text-xs font-bold uppercase tracking-wide text-slate-600',
          td: 'px-5 py-4 align-middle text-sm text-slate-600',
          tr: 'border-b border-slate-100 last:border-0',
        }"
        class="min-w-205"
      >
        <template #name-cell="{ row }">
          <div class="max-w-60">
            <p class="font-semibold text-slate-900">{{ row.original.name }}</p>
          </div>
        </template>

        <template #description-cell="{ row }">
          <p class="max-w-80 line-clamp-2 leading-5 text-slate-500">
            {{ row.original.description }}
          </p>
        </template>

        <template #minimum_score-cell="{ row }">
          <span class="font-semibold text-slate-800">{{
            row.original.minimum_score
          }}</span>
        </template>

        <template #status-cell="{ row }">
          <UBadge :color="statusColor(row.original.status)" variant="subtle">
            {{ statusLabel(row.original.status) }}
          </UBadge>
        </template>

        <template #actions-cell="{ row }">
          <div class="flex items-center gap-1">
            <UTooltip text="Edit course">
              <UButton
                :icon="Pencil"
                color="neutral"
                variant="ghost"
                size="sm"
                :aria-label="`Edit ${row.original.name}`"
                @click="emit('edit', row.original)"
              />
            </UTooltip>
            <UTooltip text="Hapus course">
              <UButton
                :icon="Trash2"
                color="error"
                variant="ghost"
                size="sm"
                :aria-label="`Hapus ${row.original.name}`"
                @click="emit('delete', row.original)"
              />
            </UTooltip>
          </div>
        </template>
      </UTable>
    </div>

    <div
      class="flex flex-col gap-4 border-t border-slate-200 bg-emerald-50/30 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
    >
      <p class="text-sm text-slate-500">
        {{ visibleRange(page, pageSize, total) }}
      </p>
      <UPagination
        :page="page"
        :total="total"
        :items-per-page="pageSize"
        show-edges
        :sibling-count="1"
        :disabled="isLoading"
        @update:page="emit('update:page', $event)"
      />
    </div>
  </div>
</template>
