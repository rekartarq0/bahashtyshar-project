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
                ناوی لایەنی یەکەم</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs text-black min-w-[100px]">
                ناوی موڵک</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs text-black min-w-[100px]">
                جۆری موڵک</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs text-black min-w-[100px]">
                نرخی فرۆشتن</th>

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
                {{ item?.name_one }}
              </td>
              <td className="border borderbla py-1 px-4 dark:border-strokedark text-sm sm:text-xs text-black">

                {{ item?.mulk_one }}
              </td>

              <td className="border borderbla py-1 px-4 dark:border-strokedark text-sm sm:text-xs text-black">

                {{ item?.mulk_type }}
              </td>
              <td
                className="border font-droidkufi borderbla py-1 px-4 dark:border-strokedark sm:text-xs text-danger font-bold text-base">
                {{ commaNumber(item?.nrxi_froshtn * 1) }}
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
              <div class="text-center items-center">

                <h1 class="text-2xl underline font-extrabold text-[#9b7d59] tracking-widest
  drop-shadow-[2px_2px_0px_#fff]
  drop-shadow-[4px_4px_4px_rgba(0,0,0,0.25)]">
                  بەڵێن نامەی کڕین و فرۆشتن
                </h1>

              </div>
              <div class="invoice-details font-droidkufi w-full mt-4">
                <div class="text-black">
                  <div class="justify-start items-start my-1">
                    <h1 class="font-bold ">ژمارەی مۆڵەت/ {{ datainvoices?.invoice.molat_no }}</h1>
                  </div>
                  <div class="flex justify-between items-center my-1">
                    <div>
                      <p>زانیاری لایەنی یەکەم/ {{ datainvoices?.invoice.name_one }}</p>

                    </div>

                    <div class="flex items-center gap-4 justify-between my-1">
                      <p>ژ.پێناس/ {{ datainvoices?.invoice.penas_one }}</p>
                      <p>ژ.مۆبایل/ {{ datainvoices?.invoice.phone_one }}</p>

                    </div>
                  </div>
                  <div class="flex justify-between items-center">
                    <div>
                      <p>زانیاری لایەنی دووەم/ {{ datainvoices?.invoice.name_two }}</p>

                    </div>

                    <div class="flex items-center gap-4 justify-between">
                      <p>ژ.پێناس/ {{ datainvoices?.invoice.penas_two }}</p>
                      <p>ژ.مۆبایل/ {{ datainvoices?.invoice.phone_two }}</p>

                    </div>
                  </div>


                </div>

                <!-- Invoice Header -->
                <div class="mt-6 w-full text-center bg-[#9b7d59]/80 py-2 items-center justify-center header-center">
                  <h1 class="font-bold text-medium text-white">هەردوو لایەن ڕێکەوتن لەسەر ئەم خاڵانەی خوارەوە</h1>
                </div>

                <!-- Content paragraph -->
                <div class="mt-1 px-2 text-lg text-justify leading-relaxed">
                  <ol class="list-decimal text-sm space-y-2">
                    <li>لایەنی یەکەم دان بەوەدا دەنێت کە ئەو <span class="text-primary font-bold">({{
                      datainvoices?.invoice?.mulk_one }})</span> هەیەتی فرۆشتی بە
                      لایەنی دووەم کە بریتیە لە: جۆری موڵک <span class="text-primary font-bold">({{
                        datainvoices?.invoice?.mulk_type }})</span>
                      ژمارەی و زنجیرەی <span class="font-bold text-primary">({{ datainvoices?.invoice?.mulk_no
                        }})</span> گەڕەک <span class="font-bold text-primary">({{
                          datainvoices?.invoice?.mulk_garak }})</span> پێوانەکەی <span class="font-bold text-primary">({{
                          datainvoices?.invoice?.mulk_metter }})</span>.</li>
                    <li>نرخی فرۆشتن <span class="font-bold text-primary">({{
                      commaNumber(datainvoices?.invoice?.nrxi_froshtn * 1) }})</span> لایەنی یەکەم دان بەوەدا
                      دەنێت کە پێشەکی <span class="font-bold text-primary">({{
                        commaNumber(datainvoices?.invoice?.nrxi_peshaki * 1) }})</span> وەرگرت و <span
                        class="font-bold text-primary">({{
                          commaNumber(datainvoices?.invoice?.nrxi_mawa * 1) }})</span> ماوە بۆ دوای تۆمارکردنی
                      موڵک لە بەڕێوەبەرایەتی تۆمارکردنی موڵک.</li>
                    <li>ئەگەر لایەنی یەکەم پەشیمان بووەوە لە فرۆشتن یان تەواوکردنی کاروباری فرۆشتن دەبێت پێشەکی
                      بگەڕێنێتەوە بۆ لایەنی دووەم و <span class="text-primary font-bold">({{
                        commaNumber(datainvoices?.invoice?.nrxi_pashimani_two * 1) }})</span>
                      پەشیمانی بدات بە لایەنی دووەم بێ ئاگادارکردنەوەی بە شێوەی فەرمی.
                    </li>
                    <li>لایەنی دووەم بڕیاری کڕنی دا بەو مەرجانەی لە بەڵێننامەکەدا هاتووە و دەبێت پاشماوەی بدات بە لایەنی
                      یەکەم بێ ئاگادارکردنەوەی بەشێوەی فەرمان ئەگەر پەشیمان بووەوە لە کڕینی موڵک یان لا دانی پاشماوەی
                      پارەکە دەبێت <span class="text-primary font-bold">({{
                        commaNumber(datainvoices?.invoice?.nrxi_pashimani_one * 1) }})</span> پەشیمانی بدات و
                      لایەنی یەکەم بۆی هەیە ئەو موڵکە بکات بە ناوی هەرکەس کە خۆی
                      دەیەوێت.</li>
                    <li>لایەنی <span class="font-bold text-primary"> (فرۆشیار) </span> بەرپرسیارە لەو پارەیەی کە دەچێتە
                      کاروباری فرۆشتن لە بەڕێوەبەرایەتی تۆمارکردنی
                      موڵک.</li>
                    <li>
                      لایەنی <span class="font-bold text-primary">(فرۆشیار)</span> بەرپرسیارە لە پارەی گوێزانەوە و ڕاست
                      کردنەوە و جیاکردنەوە و بەیەک کردن و باجی
                      خانوبەرە دەرامەت <span class="font-bold text-primary">(الداخل)</span> و شارەوانی ئەگەر هەبوو.
                    </li>
                    <li>
                      کرێی نێوەند <span class="font-bold text-primary">(الدلال)</span> بەپێی ماددەی <span
                        class="font-bold text-primary">(٥٨)</span> ساڵی <span
                        class="font-bold text-primary">(١٩٨٧)</span> بەم جۆرەیە:
                      <div class="flex items-center justify-between">
                        <div>
                          آ-<span class="font-bold text-primary">٪٢</span> لە یەکەم <span
                            class="font-bold text-primary">(١٠.٠٠٠)</span> دینارە.
                        </div>
                        <div>ب-<span class="font-bold text-primary">٪١</span> لە <span
                            class="font-bold text-primary">(١٠.٠٠٠)</span> دینار زیاتر بێت.</div>
                      </div>
                      ج-هەردوو لایەن وەک یەک کرێی نێوەند دەدەن مەگەر لەسەر شتی تر ڕێکەون.

                      <p>
                        لەکاتی هەڵوەشاندنەوەی ئەم بەڵێن نامەیەدا هەردوو هەردوو لایەن کرێی نێوەند دەدەن بە نیوەیی
                        هەرچەند بێت.
                      </p>
                    </li>
                    <li>
                      من کە لایەنی یەکەمم دەبێت ئەو موڵکە چۆڵ بکەم لە ماوەی
                      <span class="font-bold text-primary">({{ datainvoices?.invoice?.datecholkrdn_year?.split('-')[0]
                        }})</span> لەبەرواری <span class="font-bold text-primary">({{
                          datainvoices?.invoice?.from_datecholkrdn }})</span> تا <span class="font-bold text-primary">({{
                          datainvoices?.invoice?.to_datecholkrdn }})</span> و بە
                      چۆڵی بەدەستی
                      لایەنی دووەم بگات بێ دواکەوتن و بەپێچەوانەوە دەبێت مانگی <span class="font-bold text-primary">({{
                        commaNumber(datainvoices?.invoice?.krey_mangana * 1) }})</span> دینار بدەم لە جیاتی کرێی هەر
                      مانگێک تا
                      ڕۆژی چۆڵکردن نابێت ئەم شتانە لەگەڵ خۆم ببەم:
                      <p>سکەی پەنجەرە ({{ datainvoices?.invoice?.skay_panjara }}) پانکەی ئاسمانی ({{
                        datainvoices?.invoice?.pankay_asman }}) سنگی چێشتنگە ({{ datainvoices?.invoice?.sngi_cheshtnga
                        }}) دووکەڵ کێش ({{ datainvoices?.invoice?.dukalkesh }}) ناوکوڵێنەر ({{
                          datainvoices?.invoice?.dastshor }} ) دەست شۆر ({{ datainvoices?.invoice?.dastshor }}) تانکی
                        ئاو ({{ datainvoices?.invoice?.tankiaw }}) و هەرشتێکی خانوو بێت.</p>
                    </li>
                    <li>دەبێت لایەنی (فرۆشیار) کاروباری تەواوکردنی موڵک تەواو بکات لە ماوەی (٢) ڕۆژدا.</li>
                    <li>بەڕەزامەندی هەردوو لایەن لەسەر ئەم بڕگانەی لە بەڵێننامەکەدا هاتووە ئیمزاکرد لە </li>

                  </ol>
                </div>
                <div class="flex mt-8 items-center justify-between">
                  <div class="font-bold text-xl underline mt-8 text-[#9b7d59]">خاوەنی کۆمپانیا</div>
                  <div>
                    <p class="font-bold text-black">لایەنی یەکەم (فرۆشیار) :</p>
                    <p>ناو: {{ datainvoices?.invoice?.name_one }}</p>
                    <p>گەڕەک: {{ datainvoices?.invoice?.garak_one }}</p>
                    <p>کاری: {{ datainvoices?.invoice?.job_one }}</p>
                    <p>شاهد: {{ datainvoices?.invoice?.shahid_one }}</p>
                  </div>
                  <div>
                    <p class="font-bold text-black">لایەنی دووەم (کڕیار) :</p>
                    <p>ناو: {{ datainvoices?.invoice?.name_two }}</p>
                    <p>گەڕەک: {{ datainvoices?.invoice?.garak_two }}</p>
                    <p>کاری: {{ datainvoices?.invoice?.job_two }}</p>
                    <p>شاهد: {{ datainvoices?.invoice?.shahid_two }}</p>
                  </div>
                </div>

              </div>
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
  padding-top: 20mm;
  padding-right: 10mm;
  padding-left: 10mm;
  box-sizing: border-box;
  page-break-after: always;
  /* each prints on new page */
}

/* First page (Invoice details) */
.invoice-details-page {
  background-image: url('/assets/images/img_normal_invoice.png');
}

.invoice-images-page {
  display: flex;
  justify-content: center;
  align-items: center;
  background-image: url('/assets/images/img_normal_invoice.png');
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