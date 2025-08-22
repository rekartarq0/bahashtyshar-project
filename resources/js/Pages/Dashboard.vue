<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref, onMounted, watch } from "vue";
import { Check, Clock, Minus } from "@element-plus/icons-vue"; 
import dayjs from 'dayjs';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  data: Object, // contains { stages: [...], projectsByStage: {...} }
});

const stages = props.data.stages;
const projectsByStage = props.data.projectsByStage;

// Stage header colors
function getStageColor() {
  return "bg-gradient-to-r from-fuchsia-800 to-fuchsia-600 to-fuchsia-500";
}

// ---- Helpers ----
function getTotalDays(start, end = null) {
  if (!start) return 0;
  const startDate = new Date(start);
  const endDate = end ? new Date(end) : new Date();
  const diffMs = endDate - startDate;
  return diffMs / (1000 * 60 * 60 * 24); 
}

function getDurationColor(start, end) {
  if (!start) return "bg-gradient-to-r from-gray-300 to-gray-500";

  const diffDays = getTotalDays(start, end);

  if (diffDays <= 2) {
    return "bg-gradient-to-br from-green-400 to-green-600";
  } else if (diffDays <= 4) {
    return "bg-gradient-to-br from-yellow-400 to-orange-500";
  } else {
    return "bg-gradient-to-br from-red-500 to-red-700";
  }
}

function formatDate(dateStr) {
  if (!dateStr) return "N/A";
  const date = new Date(dateStr);
  return date.toLocaleString("ckb-IQ", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

function getTimeBetween(start, end = null) {
  if (!start) return "0 چرکە";
  const startDate = new Date(start);
  const endDate = end ? new Date(end) : new Date();
  let diffMs = endDate - startDate;

  const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diffMs / (1000 * 60 * 60)) % 24);
  const minutes = Math.floor((diffMs / (1000 * 60)) % 60);

  return `${days} ڕۆژ - ${hours} س - ${minutes} د`;
}

function getProjectStatus(item) {
  if (!item.start_time) return "pending"; 
  return item.end_time ? "success" : "inProcess";
}


const searchQueryitem = ref({
  datefilter: props.data.datefilter || [dayjs().startOf('month').format('YYYY-MM-DD'), dayjs().format('YYYY-MM-DD')]
});


const loading = ref(false);

// --- Fetching (like your first dashboard) ---
const fetchPage = debounce(() => {
  const datefilter = Array.isArray(searchQueryitem.value.datefilter)
    ? searchQueryitem.value.datefilter
    : [];

  const query = new URLSearchParams(window.location.search);
  const current0 = query.get('datefilter[0]');
  const current1 = query.get('datefilter[1]');

  if (current0 === datefilter[0] && current1 === datefilter[1]) {
    return; // nothing changed
  }

  loading.value = true;

  router.get('/dashboard', {
    datefilter
  }, {
    preserveScroll: true,
    preserveState: false, 
    replace: true, 
    onFinish: () => loading.value = false,
    onSuccess: () => {},
    onError: () => loading.value = false
  });
}, 300);

let hasMounted = false;
watch(
  () => searchQueryitem.value.datefilter,
  () => {
    if (hasMounted) {
      fetchPage();
    }
  },
  { deep: true }
);

onMounted(() => {
  hasMounted = true;
  loading.value = false;
});
</script>

<template>
  <AppLayout title="داشبۆرد">
    <template #header>
      <h4
        dir="rtl"
        class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black pl-1 sm:pl-2"
      >
        داشبۆرد
      </h4>
    </template>

    <div class="py-5 font-droidkufi">
      <!-- Loading overlay -->
      <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-70 flex justify-center items-center z-50">
        <svg class="animate-spin h-10 w-10 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
          viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
      </div>

      <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
          <!-- Date Filter -->
          <div class="md:w-1/2 px-4">
            <el-form-item style="width: 100%;" label-position="left" label-width="120px">
              <el-date-picker 
                v-model="searchQueryitem.datefilter" 
                type="daterange"
                range-separator="تا" 
                start-placeholder="لە ڕۆژ" 
                end-placeholder="بۆ ڕۆژ"
                format="YYYY-MM-DD" 
                value-format="YYYY-MM-DD" />
            </el-form-item>
          </div>

          <!-- Stages Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 px-4">
            <div
              v-for="stage in stages"
              :key="stage"
              class="flex flex-col items-center"
            >
              <!-- Stage header -->
              <div
                :class="[ 'w-full text-center py-2 rounded-sm text-white font-bold text-lg mb-6', getStageColor() ]"
              >
                {{ stage === 'Planning' ? 'پلاندان' :
                   stage === 'Creative' ? 'دروستکردن' :
                   stage === 'Design' ? 'دیزاین' :
                   stage === 'Sale' ? 'فروشتن' : stage }}
              </div>

              <!-- Projects -->
              <div
                v-if="projectsByStage[stage] && projectsByStage[stage].length"
                class="grid grid-cols-3 xl:grid-cols-4 gap-2"
              >
                <div
                  v-for="item in projectsByStage[stage]"
                  :key="item.id"
                  class="relative group"
                >
                  <!-- Project Square -->
                  <div
                    :class="[ 'relative w-20 h-20 flex items-center justify-center rounded-md text-white text-[10px] font-semibold text-center leading-tight shadow-md cursor-pointer transition-transform duration-200 hover:scale-105', getDurationColor(item.start_time, item.end_time) ]"
                  >
                    {{ item.project.name }}

                    <!-- Status icon -->
                    <div class="absolute top-1 right-1">
                      <Check
                        v-if="getProjectStatus(item) === 'success'"
                        class="w-4 h-4 text-green-300"
                      />
                      <Clock
                        v-else-if="getProjectStatus(item) === 'inProcess'"
                        class="w-4 h-4 text-yellow-300"
                      />
                      <Minus
                        v-else
                        class="w-4 h-4 text-gray-300"
                      />
                    </div>
                  </div>

                  <!-- Tooltip -->
                  <div
                    class="absolute z-10 hidden group-hover:block -top-32 left-1/2 -translate-x-1/2 w-56 bg-white text-black text-xs rounded-lg shadow-lg p-2"
                  >
                    <p class="font-bold">{{ item.project.name }}</p>
                    <p>دەستپێك: {{ formatDate(item.start_time) }}</p>
                    <p v-if="item.end_time">کۆتایی: {{ formatDate(item.end_time) }}</p>
                    <p>ماوە: {{ getTimeBetween(item.start_time, item.end_time) }}</p>
                    <p class="mt-1">
                      <strong>حاڵەت:</strong>
                      <span v-if="getProjectStatus(item) === 'success'" class="text-green-600">بەسەرکەوتوویی تەواو بوو ✅</span>
                      <span v-else-if="getProjectStatus(item) === 'inProcess'" class="text-yellow-600">هێشتا بەردەوامە ⏳</span>
                      <span v-else class="text-gray-500">دەستپێ نەکراوە</span>
                    </p>
                  </div>
                </div>
              </div>

              <!-- No projects -->
              <div v-else class="text-gray-400 text-xs text-center italic mt-6">
                هیچ پرۆژەیەک نییە
              </div>
            </div>
          </div>

          <!-- Legend -->
          <div class="mt-6 p-4 border-t border-gray-200 text-sm text-gray-600 font-droidkufi">
            <p><strong>تێبینی:</strong> ڕەنگەکان بنەمای ماوەی پڕۆژە هەیە</p>
            <ul class="list-disc ml-5 mt-2">
              <li><span class="text-green-600">سەوز</span> → ≤ 2 ڕۆژ</li>
              <li><span class="text-yellow-500">نارنجی</span> → > 2 ڕۆژ و ≤ 4 ڕۆژ</li>
              <li><span class="text-red-600">سور</span> → > 4 ڕۆژ</li>
            </ul>
            <p class="mt-3"><strong>ئایکۆنەکان:</strong></p>
            <ul class="list-disc ml-5 mt-2">
              <li><Check class="inline w-4 h-4 text-green-500" /> → بەسەرکەوتوویی تەواو بوو</li>
              <li><Clock class="inline w-4 h-4 text-yellow-500" /> → هێشتا بەردەوامە</li>
              <li><Minus class="inline w-4 h-4 text-gray-400" /> → دەستپێ نەکراوە</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
