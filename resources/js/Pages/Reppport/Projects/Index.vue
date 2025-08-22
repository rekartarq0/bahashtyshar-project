<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import { ref } from 'vue';
import { nextTick } from 'vue'
import * as XLSX from 'xlsx';
import { saveAs } from 'file-saver';
import { usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs'

import InvoicePrintHeader from '@/Components/InvoicePrintHeader.vue'
const props = defineProps({
  filter: Array,
});

const today = dayjs().format('YYYY-MM-DD');
const StartOfMonth = dayjs().startOf('month').format('YYYY-MM-DD');


const filters = ref({
  projects_id: [],
  datefilter: [StartOfMonth, today]
});
const page = usePage();

const settings=ref(page.props.settings);

const options = ref({
  name: true,
  stage: true,
  start_time: true,
  end_time: true,
  user_id: true,
});

const data = ref([]);

const handlePrintSections = () => {
  const printContent = document.getElementById('print-sections');
  const originalContent = document.body.innerHTML;
  document.body.innerHTML = printContent.outerHTML;
  window.print();
  document.body.innerHTML = originalContent;
  window.location.reload();
}

// Handle Print / Send Filtered Request
const handlePrintRepport = async () => {
  try {
    const response = await axios.post('/api/report/projects', filters.value)
    console.log(response.data);
    
    data.value = response.data
    await nextTick(); // wait for DOM update before printing
    handlePrintSections();

    // Optionally trigger a print or export with the returned data
  } catch (error) {
    console.error('Error fetching filtered report:', error)
  }
}
const handleExportExcleRepport = async () => {
  try {
    const response = await axios.post('/api/report/projects', filters.value)
    data.value = response.data
    await nextTick(); // wait for DOM update before printing
    // Optionally trigger a print or export with the returned data
  } catch (error) {
    console.error('Error fetching filtered report:', error)
  }
}

const downloadExcel = async () => {
  await handleExportExcleRepport();
  downloadExcels();
};
const downloadExcels = () => {
  if (!data.value?.data?.length) {
    alert('No data to export');
    return;
  }

  const headers = [];
  const rowMappers = [];

  // Index
  headers.push('#');
  rowMappers.push((_, i) => i + 1);

  // Project Name
  if (options.value.name) {
    headers.push('ناوی پڕۆژە');
    rowMappers.push(item => item?.project?.name || '');
  }

  // Stage
  if (options.value.stage) {
    headers.push('حاڵەکاتی پڕۆژە');
    rowMappers.push(item => item?.stage || '');
  }

  // Start Time
  if (options.value.start_time) {
    headers.push('بەرواری دەستپێکردن');
    rowMappers.push(item => item?.start_time || '');
  }

  // End Time
  if (options.value.end_time) {
    headers.push('بەرواری کۆتایی');
    rowMappers.push(item => item?.end_time || '');
  }

  // Created At + User
  if (options.value.user_id) {
    headers.push('دروستکراوە لە');
    rowMappers.push(item => {
      const dateStr = item.created_at
        ? new Date(item.created_at).toLocaleString('en-GB')
        : '';
      const userName = item.user?.name || '';
      return `${dateStr} by ${userName}`;
    });
  }

  // Build rows
  const rows = data.value.data.map((item, index) =>
    rowMappers.map(fn => {
      try {
        return fn(item, index);
      } catch {
        return '';
      }
    })
  );

  const worksheetData = [headers, ...rows];

  // Create sheet & workbook
  const worksheet = XLSX.utils.aoa_to_sheet(worksheetData);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Projects Report');

  // Export file
  const wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
  saveAs(
    new Blob([wbout], { type: 'application/octet-stream' }),
    'projects_report.xlsx'
  );
};
// getTotalDays
function getTotalDays(start, end = null) {
  if (!start) return 0;
  const startDate = new Date(start);
  const endDate = end ? new Date(end) : new Date();
  const diffMs = endDate - startDate;
  return diffMs / (1000 * 60 * 60 * 24); // total days as float
}

function getDurationColor(start, end) {
  if (!start) return "bg-gray-100 dark:bg-gray-800";

  const diffDays = getTotalDays(start, end);

  if (diffDays <= 2) return "bg-green-100 dark:bg-green-900";
  else if (diffDays <= 4) return "bg-orange-100 dark:bg-orange-900";
  else return "bg-red-100 dark:bg-red-900";
}


</script>

<template>
  <AppLayout title="invoice projects">
    <template #header>
      <h4 dir="rtl" class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black dark:text-black pl-1 sm:pl-2">
        ڕاپۆرتی پڕۆژەکان
      </h4>
    </template>

    <div dir="rtl" id="print-sections">

      <!-- pages invoice header Desghin -->
      <InvoicePrintHeader :settings="settings" />
      <div class="hidden mt-2 p-4 print:block w-full font-droidkufi projects-center justify-center text-center">
        <div class=" p-3 bg-gray-800">
          <p class="text-white text-lg">ڕاپۆرتی جەردی پڕۆژەکان</p>
        </div>
        <div v-if="data.filters?.projects?.length || data.filters?.projects?.length" class="p-3">
          <p>فلتەرەکان</p>
          <div class="projects-start justify-start">
            <p v-if="data.filters.projects?.length">
              <strong>پۆلەکان:</strong>
              <span v-for="(category, index) in data.filters.projects" :key="category.id">
                {{ category.name }}<span v-if="index < data.filters.projects.length - 1">، </span>
              </span>
            </p>

            <p v-if="data.filters.projects?.length" class="mt-2">
              <strong>كاڵاكەان:</strong>
              <span v-for="(item, index) in data.filters.projects" :key="item.id">
                {{ item.name }}<span v-if="index < data.filters.projects.length - 1">، </span>
              </span>
            </p>
          </div>
        </div>

        <!-- table sections -->
        <div class="flex flex-col justify-between projects-center w-full mt-3">
          <div class="w-full overflow-x-auto overflow-hidden">
            <table class="w-full table-auto">
              <thead class="font-droidkufi">
                <tr class="bg-gray-800 text-right dark:bg-meta-4">
                  <th class="border border-[#eee] dark:border-strokedark py-3 px-4 text-xs text-white">
                    #</th>
                  <th v-if="options.name"
                    class="border border-[#eee] dark:border-strokedark py-3 px-4 text-xs text-white">
                    ناوی پڕۆژە</th>
                 
                  <th v-if="options.stage"
                    class="border border-[#eee] dark:border-strokedark py-3 px-4 text-xs text-white">
                    حاڵەکاتی پڕۆژە</th>
                 
                  <th v-if="options.start_time"
                    class="border border-[#eee] dark:border-strokedark py-3 px-4 text-xs text-white">
                   بەرواری دەستپێکردن</th>
                 
                  <th v-if="options.end_time"
                    class="border border-[#eee] dark:border-strokedark py-3 px-4 text-xs text-white">
                   بەرواری کۆتایی</th>
                 
                  <th v-if="options.user_id" class="border border-[#eee] dark:border-strokedark py-3 px-4 text-sm sm:text-xs text-white">
                    دروستکراوە لە </th>
                </tr>
              </thead>
              <tbody class="font-droidkufi">
<tr
  v-for="(item, i) in data.data"
  :key="i"
  :class="getDurationColor(item?.start_time, item?.end_time)"
>                  <td
                    class="border border-[#eee] font-NRT py-0 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                    {{ i + 1 }}
                  </td>
                  <td v-if="options.name"
                    class="border border-[#eee] py-0 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                    <div class="flex projects-center">
                      {{ item?.project?.name }}
                    </div>
                  </td>
                
                  <td v-if="options.stage"
                    class="border border-[#eee] py-0 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                    <div class="flex projects-center">
                      {{ item?.stage }}
                    </div>
                  </td>
                  <td v-if="options.start_time"
                    class="border border-[#eee] py-0 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                    <div class="flex projects-center">
                      {{ item?.start_time }}
                    </div>
                  </td>
                  <td v-if="options.end_time"
                    class="border border-[#eee] py-0 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                    <div class="flex projects-center">
                      {{ item?.end_time }}
                    </div>
                  </td>
                  <td
                  v-if="options.name"
                    class="border border-[#eee] py-0 font-NRT px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                    {{ new Date(item.created_at).toLocaleString() }}
                    <span class="mx-1">by</span>
                    {{ item.user.name }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="py-5 font-droidkufi">
      <div class="w-full bg-white mx-auto sm:px-6 lg:px-8">

        <div class="p-4 bg-white space-y-4">
          <!-- Top Buttons -->
          <div class="flex justify-end projects-center">
            <div>
              <el-button plain type="success" @click="downloadExcel">📥 داگرتنی ئێکسل</el-button>
              <el-button plain type="primary" @click="handlePrintRepport">🖨️ پرینتکردن</el-button>
            </div>
          </div>

          <div>
            <p class="font-bold font-droidkufi text-base pb-1 border-b text-primary">فلتەرەکان</p>
          </div>

          <!-- Filter Dropdowns -->
          <div class="grid grid-cols-2 gap-4">
            <!-- Category -->
            <div>
              <h1 class="font-bold text-sm mb-2">پڕۆژەکان</h1>
              <el-select filterable multiple v-model="filters.projects_id" placeholder="پڕۆژەکان" clearable>
                <el-option v-for="category in props.filter.projects" :key="category.id" :label="category.name"
                  :value="category.id" />
              </el-select>
            </div>
               <!-- date filter -->
                        <div>
                            <h1 class="font-bold text-sm mb-2">بەرواری هەڵبژاردن</h1>
                            <el-date-picker style="width: 100%;" v-model="filters.datefilter" type="daterange" range-separator="تا وەکو"
                                start-placeholder="لە ڕۆژ" end-placeholder="بۆ ڕۆژ" format="YYYY-MM-DD"
                                value-format="YYYY-MM-DD" />
                        </div>
           
          </div>

          <!-- Checkbox Options -->

          <div>
            <p class="font-bold font-droidkufi text-base border-b pb-1">هەڵبژاردنی ستوونەکان</p>
          </div>
          <div class="grid grid-cols-3 gap-4">
            <el-checkbox v-model="options.name">ناوی پڕۆژە</el-checkbox>
            <el-checkbox v-model="options.stage">حاڵەکاتی پڕۆژە</el-checkbox>
            <el-checkbox v-model="options.start_time">بەرواری دەستپێکردن</el-checkbox>
            <el-checkbox v-model="options.end_time">بەرواری کۆتای</el-checkbox>
            <el-checkbox v-model="options.user_id">دروستکراوە لە</el-checkbox>


          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>
  
<style>
@media print {
  * {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .print-hidden {
    display: none !important;
  }
}

@media print {

  /* Show footer */
  .print-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    border-top: 1px solid #1f2937;
    /* gray-800 */
    font-family: 'Droid Kufi', sans-serif;
    text-align: center;
    padding: 0.5rem 0;
    /* Or transparent, but white is usually better */
    display: block !important;
    z-index: 1000;
  }

  /* Hide the original div in screen */
  .print-footer.hidden {
    display: block !important;
    /* override hidden on print */
  }
}
</style>
