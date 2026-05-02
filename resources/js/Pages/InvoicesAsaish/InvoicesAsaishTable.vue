<template>
  <div className="rounded-sm border border-stroke bg-white px-5 pt-6 m-5 pb-2.5 
    shadow-default dark:border-strokedark dark:bg-boxdark-2 sm:px-7.5 xl:pb-1">
    <div class="md:w-1/2 font-droidkufi px-4 flex justify-between">
      <el-form-item style="width: 100%;" label-position="left" label-width="120px">
        <el-date-picker v-model="datefilter" type="daterange" range-separator="تا" start-placeholder="لە ڕۆژ"
          end-placeholder="بۆ ڕۆژ" format="YYYY-MM-DD" value-format="YYYY-MM-DD" />
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
                ژ.وەسڵ</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs text-black min-w-[100px]">
                بەروار</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs text-black min-w-[100px]">
                بۆ بەڕێز</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs text-black min-w-[100px]">
                بابەت</th>

              <th className="border borderbla dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-black">
                دروستکراوە لە</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-blacky">
                کردار </th>

            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, i) in data.data" :key="i"
              :class="(item.is_archived === 1 || item.is_archived === '1' || item.is_archived === true) ? 'bg-orange-100' : ''">

              <td
                className="border space-x-2 bg-gray-100 borderbla py-2 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                {{ i + 1 + (data.current_page - 1) * data.per_page }}

              </td>
              <td
                className="border font-droidkufi borderbla py-1 px-4 dark:border-strokedark sm:text-xs text-primary font-bold text-base">
                {{ item?.invoice_no }}
              </td>
              <td
                className="border font-droidkufi borderbla py-1 px-4 dark:border-strokedark sm:text-xs text-primary font-bold text-base">
                {{ item?.invoice_year }}
              </td>

              <td
                className="border font-droidkufi borderbla py-1 px-4 dark:border-strokedark sm:text-xs text-primary font-bold text-base">
                {{ item?.to }}
              </td>
              <td
                className="border font-droidkufi borderbla py-1 px-4 dark:border-strokedark sm:text-xs text-primary font-bold text-base">
                {{ item?.matter }}
              </td>

              <td className="border borderbla py-1 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                {{ new Date(item.created_at).toLocaleString() }}
                <span className="mx-1">by</span>
                {{ item?.user?.name }}
              </td>

              <td className="border borderbla py-1 px-4  dark:border-strokedark text-sm sm:text-xs text-black">
                <el-button v-if="can('delete projects')" type="danger" plain :icon="Delete" @click="destroy(item.id)"
                  circle class="mr-2" />
                <el-button v-if="can('edit projects')" type="primary" plain :icon="Edit" @click="edit(item.id)" circle
                  class="mr-2" />
                <el-button v-if="can('edit projects')" type="warning" plain @click="printInvoices(item.id)" circle
                  :icon="Printer">
                </el-button>
              </td>

            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination Controls -->
      <div class="mt-4 flex justify-start items-end">
        <el-pagination background layout="prev, pager, next" :total="data.total" :current-page="data.current_page"
          :page-size="data.per_page" @current-change="handlePageChange" />
        <el-select v-model="perPage" placeholder="perpage" style="width: 100px" @change="fetchPage()">
          <el-option v-for="size in [10, 25, 50, 100]" :key="size" :label="size" :value="size" />
        </el-select>
      </div>

    </div>
    <div>
    </div>

    <el-dialog class="font-droidkufi" dir="rtl" title="پرنتکردنی دەرخستە" width="60%" v-model="props.dialogvisablity"
      @close="resetform" :close-on-click-modal="true">
      <div class="space-y-6">
        <div class="w-full">
          <!-- Print button -->
          <div class="justify-end flex w-full items-end mb-4">
            <el-button type="warning" plain @click="printInvoicesdetail" circle :icon="Printer" />
          </div>

          <!-- Pages to print -->
          <div ref="printArea" class="flex flex-col items-center gap-6">

            <!-- First A4 page: Invoice Details -->
            <div class="invoice-container invoice-details-page">
              <div class="invoice-details font-droidkufi w-full">
                <div class="mt-14 header-top text-left text-black">
                  <div class="header-item">
                    NO. {{ datainvoices.invoice.invoice_no }}
                  </div>

                  <div class="header-item">
                    Date: {{ new Date(datainvoices?.invoice.created_at).toLocaleDateString() }}
                  </div>
                </div>

             <!-- Invoice Header -->
<div class="mt-10 w-full text-center header-center">
  <div>
    <h1 class="text-xl font-bold text-black my-2">
      بۆ بەڕێز / {{ datainvoices?.invoice.to }}
    </h1>
  </div>

  <div>
    <h1 class="text-xl font-bold text-black my-2">
      بابەت / {{ datainvoices?.invoice.matter }}
    </h1>
  </div>
</div>


                <!-- Content paragraph -->
                <p class="mt-10 text-lg text-justify leading-relaxed">
                  {{ datainvoices?.invoice.content }}
                </p>


                <div class="flex items-center justify-between">
                  <div class="w-1/2 border-2 rounded-lg ml-2">
                    <div class="m-2">
                      <p class="font-bold text-black font-droidkufi">
                        لایەنی یەکەم (فرۆشیار یان بەکرێدە)
                      </p>
                      <p>
                        <span>ناو:</span>
                        {{ datainvoices?.invoice.name_one }}
                      </p>
                      <p>
                        <span>نەتەوە:</span>
                        {{ datainvoices?.invoice.national_one }}
                      </p>
                      <p>
                        <span>ژ.تەلەفون</span>
                        {{ datainvoices?.invoice.phone_one }}
                      </p>
                      <p>
                        <span>ژ.کۆد</span>
                        {{ datainvoices?.invoice.code_one }}
                      </p>
                    </div>
                  </div>
                  <div class="w-1/2 border-2 rounded-lg ml-2">
                    <div class="m-2">

                      <p class="font-bold text-black font-droidkufi">
                        لایەنی دووەم (کڕیار یان بکرێگیر)
                      </p>
                      <p>
                        <span>ناو:</span>
                        {{ datainvoices?.invoice.name_two }}
                      </p>
                      <p>
                        <span>نەتەوە:</span>
                        {{ datainvoices?.invoice.national_two }}
                      </p>
                      <p>
                        <span>ژ.تەلەفون</span>
                        {{ datainvoices?.invoice.phone_two }}
                      </p>
                      <p>
                        <span>ژ.کۆد</span>
                        {{ datainvoices?.invoice.code_two }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="mt-2">
                  <p class="font-bold text-black font-droidkufi">
                    جۆری مامەڵە:
                    <span> {{ datainvoices?.invoice.mamala_type }}</span>
                  </p>
                </div>

                <!-- create table invoice -->
                <div class="mt-2">
                  <table class="w-full border-collapse border border-black text-right font-droidkufi">
                    <thead>
                      <tr class="bg-gray-200">
                        <th class="border border-black px-2 py-1">جۆری مووڵک</th>
                        <th class="border border-black px-2 py-1">ژ.مووڵک</th>
                        <th class="border border-black px-2 py-1">ژ.خانوو</th>
                        <th class="border border-black px-2 py-1">ژ.گەڕەک</th>
                        <th class="border border-black px-2 py-1">ژ.کۆڵان</th>
                        <th class="border border-black px-2 py-1">تێبینی</th>
                      </tr>
                    </thead>

                    <tbody>
                      <!-- Static Row -->
                      <tr>
                        <td class="border border-black px-2 py-2">{{ datainvoices?.invoice.mamala_type }}</td>
                        <td class="border border-black px-2 py-2 text-center">{{ datainvoices?.invoice.zhmarai_mulk }}
                        </td>
                        <td class="border border-black px-2 py-2 text-center">{{ datainvoices?.invoice.zhmarai_xanw }}
                        </td>
                        <td class="border border-black px-2 py-2 text-center">{{ datainvoices?.invoice.zhmarai_garak }}
                        </td>
                        <td class="border border-black px-2 py-2 text-center">{{ datainvoices?.invoice.zhmarai_kolan }}
                        </td>
                        <td class="border border-black px-2 py-2 text-center">{{ datainvoices?.invoice.note }}</td>
                      </tr>
                    </tbody>
                  </table>

                </div>




              </div>
            </div>
            <!-- Next A4 pages: Images -->
            <div v-for="(img, index) in projectImages" :key="'img-' + index" v-show="img.checked"
              class="invoice-container invoice-images-page flex justify-center items-center">
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

  // Copy all styles from current page
  let styles = "";
  document.querySelectorAll("style, link[rel='stylesheet']").forEach(style => {
    styles += style.outerHTML;
  });

  newWin.document.write(`
  <html>
    <head>
      <title>Invoice</title>

      ${styles}

      <style>
        @page {
          size: A4;
          margin: 0;
        }

        body {
          margin: 0;
          direction: rtl;
        }

        .invoice-container {
          width: 210mm;
          min-height: 297mm;
          box-sizing: border-box;
          page-break-after: always;
        }

        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      </style>

    </head>

    <body>
      ${printContents}
    </body>
  </html>
  `);

  newWin.document.close();

  newWin.onload = () => {
    setTimeout(() => {
      newWin.focus();
      newWin.print();
      newWin.close();
    }, 500);
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
  width: 210mm;
  /* A4 exact size */
  height: 297mm;
  /* A4 exact size */
  background-size: cover;
  background-position: center;
  font-family: 'DroidKufi';
  position: relative;
  padding: 20mm;
  box-sizing: border-box;
  page-break-after: always;
  /* each prints on new page */
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