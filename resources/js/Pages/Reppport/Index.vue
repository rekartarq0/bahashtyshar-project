<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  data: Array,
});

const reports = [
  {
    title: 'ڕاپۆرتی پڕۆژەکان',
    subtitle: 'بینینی پڕۆژەکان',
    icon: 'fas fa-box',
    color: '#3B82F6', // blue-500
    route: 'repport.projects',
  },
  
];

</script>
<template>
  <AppLayout title="Repport">
    <template #header>
      <h4 dir="rtl" class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black dark:text-black pl-1 sm:pl-2">
        ڕاپۆرتەکان
      </h4>
    </template>

    <div class="py-5 font-droidkufi">
      <div class="w-full mx-auto sm:px-4 lg:px-6">
        <div dir="rtl" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-2">
          <div v-for="report in reports" :key="report.title"
            class="rounded-2xl h-52 shadow-md hover:shadow-2xl transform transition-all duration-300 cursor-pointer relative group"
            @click="router.visit(route(report.route))" :style="{ backgroundColor: report.color }">
            <div class="flex flex-col justify-center items-center text-center h-full text-white p-5">
              <div class="bg-white bg-opacity-20 rounded-full p-4 mb-3">
                <i :class="['text-2xl', report.icon]"></i>
              </div>
              <h3 class="text-lg font-bold mb-1">{{ report.title }}</h3>
              <p class="text-sm opacity-90">{{ report.subtitle }}</p>
            </div>
            <div
              class="absolute inset-0 rounded-2xl border-2 border-white opacity-0 group-hover:opacity-100 transition duration-300">
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
