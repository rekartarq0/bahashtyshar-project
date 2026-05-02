<template>
    <div className="rounded-sm border border-stroke bg-white px-5 pt-6 m-5 pb-2.5 
    shadow-default dark:border-strokedark dark:bg-boxdark-2 sm:px-7.5 xl:pb-1">
      <div class="md:w-1/2 font-droidkufi px-4 flex justify-between">
            <el-form-item style="width: 100%;" label-position="left" label-width="120px">
              <el-date-picker 
                v-model="datefilter" 
                type="daterange"
                range-separator="تا" 
                start-placeholder="لە ڕۆژ" 
                end-placeholder="بۆ ڕۆژ"
                format="YYYY-MM-DD" 
                value-format="YYYY-MM-DD" />
            </el-form-item>
  
          </div>

        <div className="flex flex-col justify-between items-center w-full mb-4">
            <div className="w-full overflow-x-auto">

                <table className="w-full table-auto">
                    <thead className="font-droidkufi">
                        <tr className="bg-gray-100 text-right dark:bg-meta-4">
                            <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs text-black min-w-[10px]">
                                #</th>
                           
                            <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs text-black min-w-[100px]">
                              بۆ بەڕێز</th>
                            <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs text-black min-w-[100px]">
                             بابەت</th>
                          
                            <th
                                className="border borderbla dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-black">
                                دروستکراوە لە</th>
                            <th
                                className="border borderbla dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-blacky">
                                کردار </th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, i) in data.data" :key="i"
  :class="(item.is_archived === 1 || item.is_archived === '1' || item.is_archived === true) ? 'bg-orange-100' : ''"
                        >
                       
                            <td
                            className="border space-x-2 bg-gray-100 borderbla py-2 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                                                                {{ i + 1 + (data.current_page - 1) * data.per_page }}

                            </td>
                            <td
                                className="border font-droidkufi borderbla py-1 px-4 dark:border-strokedark sm:text-xs text-primary font-bold text-base">
                                {{ item?.to }}
                            </td>
                            <td
                                className="border font-droidkufi borderbla py-1 px-4 dark:border-strokedark sm:text-xs text-primary font-bold text-base">
                                {{ item?.matter }}
                            </td>
                           
                            <td
                                className="border borderbla py-1 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                                {{ new Date(item.created_at).toLocaleString() }}
                                <span className="mx-1">by</span>
                                {{ item?.user?.name }}
                            </td>

                            <td
                                className="border borderbla py-1 px-4  dark:border-strokedark text-sm sm:text-xs text-black">
                                <el-button v-if="can('delete projects')" type="danger" plain :icon="Delete"
                                    @click="destroy(item.id)" circle class="mr-2" />
                                <el-button v-if="can('edit projects')" type="primary" plain :icon="Edit"
                                    @click="edit(item.id)" circle class="mr-2" />
                                <el-button v-if="can('edit projects')" type="warning" plain 
                                    @click="printInvoices(item.id)" circle :icon="Printer" >
                                </el-button>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination Controls -->
            <div class="mt-4 flex justify-start items-end">
                <el-pagination background layout="prev, pager, next" :total="data.total"
                    :current-page="data.current_page" :page-size="data.per_page" @current-change="handlePageChange" />
                <el-select v-model="perPage" placeholder="perpage" style="width: 100px" @change="fetchPage()">
                    <el-option v-for="size in [10, 25, 50, 100]" :key="size" :label="size" :value="size" />
                </el-select>
            </div>

        </div>
        <div >
</div>

  <el-dialog
  class="font-droidkufi"
  dir="rtl"
  title="پرنتکردنی دەرخستە"
  width="60%"
  v-model="props.dialogvisablity"
  @close="resetform"
  :close-on-click-modal="true"
>
  <div class="space-y-6">
    <div class="w-full">
      <!-- Print button -->
      <div class="justify-end flex w-full items-end mb-4">
        <el-button
          type="warning"
          plain
          @click="printInvoicesdetail"
          circle
          :icon="Printer"
        />
      </div>
      <div class="mb-2">
        <p class="font-droidkufi text-base text-start text-primary">
          هەڵبژاردنی وێنەی بەردەست بۆ دەرخستە
        </p>
      </div>
    <!-- Image toggle checkboxes -->
    <div v-if="projectImages.length" class="mb-4 flex flex-wrap gap-4">
  <div
    v-for="(img, index) in projectImages"
    :key="'chk-' + index"
    class="relative w-24 h-24 border rounded-lg overflow-hidden cursor-pointer group"
    @click="img.checked = !img.checked"
  >
    <!-- Image preview -->
    <img
      :src="img.url"
      :alt="'Invoice Image ' + (index+1)"
      class="w-full h-full object-cover transition duration-200"
      :class="{ 'opacity-40': !img.checked }"
    />
    <!-- Checkbox overlay -->
    <div
      class="absolute top-2 right-2 p-1 rounded shadow"
    >
      <input type="checkbox" v-model="img.checked" />
    </div>

    <!-- Label at bottom -->
    <div
      class="absolute bottom-0 left-0 right-0 text-center text-xs bg-black bg-opacity-30 text-white py-1"
    >
      وێنە {{ index + 1 }}
    </div>

  </div>
</div>


      <!-- Pages to print -->
      <div ref="printArea" class="flex flex-col items-center gap-6">

        <!-- First A4 page: Invoice Details -->
<div class="invoice-container invoice-details-page">
  <div class="invoice-details font-droidkufi w-full">
            <div class="mt-36 flex flex-col items-end justify-end header-top text-black">
              <div></div>
              <div>Date /{{ formatDateBaghdad(datainvoices.invoice.datenow) }}</div>
              <div>NO    /0{{ datainvoices.invoice.id }}</div>
            </div>

            <!-- Invoice Header -->
            <div class="mt-10 w-full flex flex-col justify-center items-center text-center header-center">
              <h1 class="text-xl font-bold text-black my-4">
                بۆ بەڕێز / {{ datainvoices.invoice.to }}
              </h1>
              <h1 class="text-xl font-bold text-black">
                بابەت / {{ datainvoices.invoice.matter }}
              </h1>
            </div>

            <!-- Content paragraph -->
            <p class="mt-10 text-lg text-justify leading-relaxed">
              {{ datainvoices.invoice.content }}
            </p>

            <!-- Items Table -->
            <div class="mt-10">
              <table class="items w-full border-collapse border-black">
                <thead>
                  <tr class="bg-blue-200">
                    <th class="border border-black px-3 py-2 text-black text-right">ژ</th>
                    <th class="border border-black px-3 py-2 text-black text-right">بابەت</th>
                    <th class="border border-black px-3 py-2 text-black text-right">قیاس</th>
                    <th class="border border-black px-3 py-2 text-black text-right">نرخ</th>
                    <th class="border border-black px-3 py-2 text-black text-right">دانە</th>
                    <th class="border border-black px-3 py-2 text-black text-right">کۆی نرخ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in datainvoices.selectedItems" :key="index">
                    <td class="border border-black px-3 py-1 text-black">{{ index+1 }}</td>
                    <td class="border border-black px-3 py-1 text-black">{{ item.name }}</td>
                    <td class="border border-black px-3 py-1 text-black">{{ item.measure }}</td>
                    <td class="border border-black px-3 py-1 text-black">
                      <span class="ml-1 text-danger">$</span>{{ commaNumber(item.unit_price*1) }}
                    </td>
                    <td class="border border-black px-3 py-1 text-black">{{ commaNumber(item.quantity*1) }}</td>
                    <td class="border border-black px-3 py-1 text-black">
                      <span class="ml-1 text-danger">$</span>{{ commaNumber(item.price) }}
                    </td>
                  </tr>
                  <tr class="font-bold">
                    <td colspan="4" class="noborder"></td>
                    <td class="border border-black px-3 py-2">کۆی گشتی</td>
                    <td class="border border-black px-3 py-2 text-black">
                      <span class="ml-1 text-danger">$</span>{{ commaNumber(datainvoices.invoice.total_price*1) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>
        <!-- Next A4 pages: Images -->
       <div
        v-for="(img, index) in projectImages"
        :key="'img-' + index"
        v-show="img.checked"
        class="invoice-container invoice-images-page flex justify-center items-center"
      >
        <img :src="img.url" alt="Invoice Image" class="invoice-image" />
      </div>
      </div>
    </div>
  </div>
</el-dialog>

    </div>
</template>




<script setup>
import { defineProps, ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import {
    Delete,
    Edit,
    Printer,
} from '@element-plus/icons-vue';
import { dayjs } from 'element-plus';
import commaNumber from 'comma-number';

const props = defineProps({
  data: Object,
  destroy: Function,
  dialogvisablity: Boolean,
  edit: Function,
  searchQuery: String,
  search: Function,
  printInvoices: Function,
  resetform: Function,
  filters: Object, // 👈 add filters from backend
  datainvoices: Object,
});

// reactive list with checked state
const projectImages = ref([]);

// keep it synced when datainvoices changes
watch(
  () => props.datainvoices?.projectImages,
  (newImages) => {
    if (newImages) {
      projectImages.value = newImages.map((img) => ({
        url: img,
        checked: true, // ✅ default checked
      }));
    }
  },
  { immediate: true }
);


function formatDateBaghdad(dateString) {
  const date = new Date(dateString);
  const utc = date.getTime() + date.getTimezoneOffset() * 60000;
  const baghdadOffset = 3 * 60 * 60 * 1000; // +03:00
  const baghdadDate = new Date(utc + baghdadOffset);
  
  const day = baghdadDate.getDate();
  const month = baghdadDate.getMonth() + 1;
  const year = baghdadDate.getFullYear();
  
  return `${day}/${month}/${year}`;
}


const perPage = ref(25); // default value


// hydrate datefilter from backend (so refresh keeps range)
const datefilter = ref(props.filters?.datefilter);

const fetchPage = debounce((page = props.data.current_page) => {
  const df = Array.isArray(datefilter.value) ? datefilter.value : [];

  router.get('/invoiceProjects', {
    q: props.searchQuery,
    page,
    perPage: perPage.value,
    datefilter: df, // 👈 send datefilter
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    onFinish: () => { loading.value = false; },
  });
}, 300);

const handlePageChange = (page) => {
  fetchPage(page); // Fetch new page with datefilter included
};

// 🔎 Watch search query
watch(
  () => props.searchQuery,
  () => {
    fetchPage(props.data.current_page);
  }
);

// 📅 Watch datefilter changes
watch(
  () => datefilter.value,
  () => {
    fetchPage(1); // reset to page 1 when datefilter changes
  },
  { deep: true }
);
const printArea = ref(null);

function printInvoicesdetail() {

  const printContents = printArea.value.outerHTML;
  const newWin = window.open("", "", "width=900,height=650");

  newWin.document.write(`
    <html>
      <head>
        <title>Invoice</title>
        <style>
          @page {
            size: A4;
            margin: 0;
          }

          @font-face {
            font-family: "DroidKufi";
            src: url('${window.location.origin}/assets/font/DroidKufi-Regular.ttf') format("truetype");
            font-weight: normal;
            font-style: normal;
          }

          body {
            margin: 0;
            padding: 0;
            direction: rtl;
            font-family: "DroidKufi", sans-serif;
          }

          /* Base A4 page */
          .invoice-container {
            width: 210mm;
            height: 297mm;
            background-size: cover;
            background-position: center;
            position: relative;
            padding: 20mm;
            box-sizing: border-box;
            page-break-after: always;
          }

          /* Backgrounds */
          .invoice-details-page {
            background-image: url('https://bahashtyshar.com/assets/images/A4template.jpg');

          }
          .invoice-images-page {
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('https://bahashtyshar.com/assets/images/A4templateimg.jpg');

            background-size: cover;
            background-position: center;
          }

          .noborder {
            border: none !important;
          }

          /* Invoice text */
          .invoice-details {
            position: relative;
            z-index: 10;
            width: 100%;
          }

          .mt-24 { margin-top: 6rem; }
          .mt-36 { margin-top: 9rem; }
          .mt-10 { margin-top: 2.5rem; }
            .header-center {
            margin-top: 10mm;
            text-align: center;
          }
         .header-top {
            margin-top: 36mm;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            font-size: 14px;
            font-weight: normal;
          }
          .invoice-details h1 {
            font-size: 1.25rem;
            font-weight: bold;
            margin: 0.75rem 0;
            color: black;
          }

          p {
            font-size: 1.125rem;
            line-height: 1.75rem;
            text-align: justify;
          }

          /* Table */
          table {
            width: 100%;
            border-collapse: collapse;
          }
          th, td {
            border: 1px solid #000;
            padding: 0px 2px;
            text-align: center;
          }
          thead tr {
            background-color: #DBEAFE;
          }

          /* Images */
          .invoice-images-page img {
            max-width: 180mm;
            max-height: 250mm;
            object-fit: contain;
            display: block;
            margin: 0 auto;
          }

          span {
            color: #dc3545;
            margin-right: 0.25rem;
          }
        </style>
      </head>
      <body>
        ${printContents}
      </body>
    </html>
  `);

  newWin.document.close();
  newWin.focus();

  newWin.onload = () => {
    newWin.print();
    newWin.close();
  };
}

</script>

<style>

.no-click {
    cursor: default !important;
}

.el-checkbox__label {
  display: none;
}
.invoice-container {
  width: 210mm;   /* A4 exact size */
  height: 297mm;  /* A4 exact size */
  background-size: cover;
  background-position: center;
  font-family: 'DroidKufi';
  position: relative;
  padding: 20mm;
  box-sizing: border-box;
  page-break-after: always; /* each prints on new page */
}

/* First page (Invoice details) */
.invoice-details-page {
  background-image: url('/assets/images/A4template.jpg');
}

.invoice-images-page {
  display: flex;
  justify-content: center;
  align-items: center;
  background-image: url('/assets/images/A4templateimg.jpg');
  background-size: cover;
  background-position: center;
}

.invoice-details {
  position: relative;
  z-index: 10;
}
.invoice-images-page img.invoice-image {
  max-width: 180mm;
  max-height: 250mm;
  object-fit: contain;
}


</style>