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
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs min-w-[10px]">#</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs min-w-[100px]">ناوی لایەنی یەکەم
              </th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs min-w-[100px]">ناوی موڵک</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs min-w-[100px]">جۆری موڵک</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-xs min-w-[100px]">نرخی فرۆشتن</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-sm sm:text-xs">دروستکراوە لە</th>
              <th className="border borderbla dark:border-strokedark py-2 px-4 text-sm sm:text-xs">کردار</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, i) in data.data" :key="i"
              :class="(item.is_archived === 1 || item.is_archived === '1' || item.is_archived === true) ? 'bg-orange-100' : ''">
              <td
                className="border space-x-2 bg-gray-100 borderbla py-2 px-4 dark:border-strokedark text-sm sm:text-xs">
                {{ i + 1 + (data.current_page - 1) * data.per_page }}
              </td>
              <td
                className="border font-droidkufi borderbla py-1 px-4 dark:border-strokedark sm:text-xs text-primary text-base">
                {{ item?.name_one }}
              </td>
              <td className="border borderbla py-1 px-4 dark:border-strokedark text-sm sm:text-xs">
                {{ item?.mulk_one }}
              </td>
              <td className="border borderbla py-1 px-4 dark:border-strokedark text-sm sm:text-xs">
                {{ item?.mulk_type }}
              </td>
              <td
                className="border font-droidkufi borderbla py-1 px-4 dark:border-strokedark sm:text-xs text-danger text-base">
                {{ commaNumber(item?.nrxi_froshtn * 1) }}
              </td>
              <td className="border borderbla py-1 px-4 dark:border-strokedark text-sm sm:text-xs">
                {{ new Date(item.created_at).toLocaleString() }}
                <span className="mx-1">by</span>
                {{ item?.user?.name }}
              </td>
              <td className="border borderbla py-1 px-4 dark:border-strokedark text-sm sm:text-xs">
                <el-button v-if="can('delete projects')" type="danger" plain :icon="Delete" @click="destroy(item.id)"
                  circle class="mr-2" />
                <el-button v-if="can('edit projects')" type="primary" plain :icon="Edit" @click="edit(item.id)" circle
                  class="mr-2" />
                <el-button v-if="can('edit projects')" type="warning" plain @click="printInvoices(item.id)" circle
                  :icon="Printer" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="mt-4 flex justify-start items-end">
        <el-pagination background layout="prev, pager, next" :total="data.total" :current-page="data.current_page"
          :page-size="data.per_page" @current-change="handlePageChange" />
        <el-select v-model="perPage" placeholder="perpage" style="width: 100px" @change="fetchPage()">
          <el-option v-for="size in [10, 25, 50, 100]" :key="size" :label="size" :value="size" />
        </el-select>
      </div>
    </div>

    <!-- Print Dialog -->
    <el-dialog class="font-droidkufi" dir="rtl" title="پرنتکردنی دەرخستە" width="60%" v-model="props.dialogvisablity"
      @close="resetform" :close-on-click-modal="true">
      <div class="space-y-6 ">
        <div class="w-full">
          <div class="justify-end flex w-full items-end mb-4">
            <el-button type="warning" plain @click="printInvoicesdetail" circle :icon="Printer" />
          </div>

          <div ref="printArea" class="flex flex-col items-center gap-6">
            <!-- A4 Invoice Page -->
            <div class="invoice-container invoice-details-page" :style="{ backgroundImage: `url(${invoiceBgUrl})` }">
              <div class="text-center items-center mt-[118px]">
                <h1 class="text-xl font-bold text-[#9b7d59]">
                  گرێبەستی کڕین و فرۆشتن
                </h1>
              </div>

              <div class="invoice-details font-droidkufi w-full mt-[6px]">
                <div>
                <div class="flex justify-between items-center mb-2">
    <div class="flex items-center gap-1 bg-[#9b7d59]/10 border border-[#9b7d59]/40 rounded px-3 py-1">
      <span class="text-[#9b7d59] font-bold text-xs">No.</span>
      <span class="text-primary font-extrabold text-sm">#{{ datainvoices?.invoice?.id }}</span>
    </div>

    <div class="flex items-center gap-1 bg-[#9b7d59]/10 border border-[#9b7d59]/40 rounded px-3 py-1">
      <span class="text-[#9b7d59] font-bold text-xs">ژمارەی مۆڵەت/</span>
      <span class="text-primary font-bold text-sm">{{ datainvoices?.invoice?.molat_no }}</span>
    </div>

    <div class="flex items-center gap-1 bg-[#9b7d59]/10 border border-[#9b7d59]/40 rounded px-3 py-1">
      <span class="text-[#9b7d59] font-bold text-xs">بەروار/</span>
      <span class="font-bold text-sm">{{ formatDate(datainvoices?.invoice?.date_invoice) }}</span>
    </div>
  </div>
                  <div class="flex justify-between items-center">
                    <div>
                      <p>زانیاری لایەنی یەکەم/ {{ datainvoices?.invoice.name_one }}</p>
                    </div>
                    <div class="flex items-center gap-2 justify-between">
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

                <div class="mt-[6px] w-full text-center bg-[#9b7d59]/80 py-1 items-center justify-center header-center">
                  <h1 class="text-medium text-white">هەردوو لایەن ڕێکەوتن لەسەر ئەم خاڵانەی خوارەوە</h1>
                </div>

                <div class="px-5 text-[14px] text-justify leading-relaxed">
                  <ol class="list-decimal text-[12.5px]">
                    <li>لایەنی یەکەم دان بەوەدا دەنێت کە ئەو موڵکەی هەیەتی فرۆشتی بە
                      لایەنی دووەم کە بریتیە لە: جۆری موڵک <span class="text-primary">({{
                        datainvoices?.invoice?.mulk_type }})</span>

                      <template v-if="isShuqah">
                        عیمارە <span class="text-primary">({{ datainvoices?.invoice?.emara }})</span>
                        قات <span class="text-primary">({{ datainvoices?.invoice?.qat }})</span>
                        ژمارەی شووقە <span class="text-primary">({{ datainvoices?.invoice?.zhmarai_shwqa }})</span>
                      </template>

                      ژمارەی و زنجیرەی <span class="text-primary">({{ datainvoices?.invoice?.mulk_no }})</span>
                      گەڕەک <span class="text-primary">({{ datainvoices?.invoice?.mulk_garak }})</span>
                      پێوانەکەی <span class="text-primary">({{ datainvoices?.invoice?.mulk_metter }}) </span>
                      <span class="font-bold">(م <sup>٢</sup>)</span>.
                    </li>
                    <li>نرخی فرۆشتن <span class="text-primary">({{ commaNumber(datainvoices?.invoice?.nrxi_froshtn * 1)
                    }}$)</span>
                      <span class="font-bold">(تەنها {{ numberToKurdishText(datainvoices?.invoice?.nrxi_froshtn * 1) }} دۆلار)</span>
                      لایەنی یەکەم دان بەوەدا دەنێت کە پێشەکی
                      <span class="text-primary">({{ commaNumber(datainvoices?.invoice?.nrxi_peshaki * 1) }}$)</span>
                      <span class="font-bold">(تەنها {{ numberToKurdishText(datainvoices?.invoice?.nrxi_peshaki * 1) }} دۆلار)</span>
                      وەرگرت و
                      <span class="text-primary">({{ commaNumber(datainvoices?.invoice?.nrxi_mawa * 1) }}$)</span>
                      <span class="font-bold">(تەنها {{ numberToKurdishText(datainvoices?.invoice?.nrxi_mawa * 1) }} دۆلار)</span>
                      ماوە بۆ دوای تۆمارکردنی موڵک لە بەڕێوەبەرایەتی تۆمارکردنی موڵک.
                    </li>
                    <li>ئەگەر لایەنی یەکەم پەشیمان بووەوە لە فرۆشتن یان تەواوکردنی کاروباری فرۆشتن دەبێت پێشەکی
                      بگەڕێنێتەوە بۆ لایەنی دووەم و
                      <span class="text-primary">({{ commaNumber(datainvoices?.invoice?.nrxi_pashimani_two * 1)
                      }}$)</span>
                      <span class="font-bold">(تەنها {{ numberToKurdishText(datainvoices?.invoice?.nrxi_pashimani_two * 1) }} دۆلار)</span>
                      پەشیمانی بدات بە لایەنی دووەم بێ ئاگادارکردنەوەی بە شێوەی فەرمی.
                    </li>
                    <li>لایەنی دووەم بڕیاری کڕینیدا بەو مەرجانەی لە بەڵێننامەکەدا هاتووە و دەبێت پاشماوەی بدات بە لایەنی
                      یەکەم بێ ئاگادارکردنەوەی بەشێوەی فەرمان ئەگەر پەشیمان بووەوە لە کڕینی موڵک یان لا دانی پاشماوەی
                      پارەکە دەبێت
                      <span class="text-primary">({{ commaNumber(datainvoices?.invoice?.nrxi_pashimani_one * 1)
                      }}$)</span>
                      <span class="font-bold">(تەنها {{ numberToKurdishText(datainvoices?.invoice?.nrxi_pashimani_one * 1) }} دۆلار)</span>
                      پەشیمانی بدات و لایەنی یەکەم بۆی هەیە ئەو موڵکە بکات بە ناوی هەرکەس کە خۆی دەیەوێت.
                    </li>
                    <li>لایەنی <span class="text-primary">(کڕیار)</span> بەرپرسیارە لەو پارەیەی کە دەچێتە کاروباری
                      فرۆشتن لە بەڕێوەبەرایەتی تۆمارکردنی موڵک.</li>
                    <li>لایەنی <span class="text-primary">(فرۆشیار)</span> بەرپرسیارە لە پارەی گوێزانەوە و ڕاست کردنەوە
                      و جیاکردنەوە و بەیەک کردن و باجی خانوبەرە دەرامەت <span class="text-primary">(الداخل)</span> و
                      شارەوانی ئەگەر هەبوو.</li>
                    <li>کرێی نێوەند <span class="text-primary">(الدلال)</span> بەپێی ماددەی <span
                        class="text-primary">(٥٨)</span> ساڵی
                      <span class="text-primary">(١٩٨٧)</span> بەم جۆرەیە:
                      <div class="flex items-center justify-between">
                        <div>آ-<span class="text-primary">٪٢</span> لە یەکەم <span class="text-primary">(١٠.٠٠٠)</span>
                          دینارە.</div>
                        <div>ب-<span class="text-primary">٪١</span> لە <span class="text-primary">(١٠.٠٠٠)</span> دینار
                          زیاتر بێت.</div>
                      </div>
                      ج-هەردوو لایەن وەک یەک کرێی نێوەند دەدەن مەگەر لەسەر شتی تر ڕێکەون.
                      <p>لەکاتی هەڵوەشاندنەوەی ئەم بەڵێن نامەیەدا هەردوو لایەن کرێی نێوەند دەدەن بە <span class="text-primary">نیوەیی</span>
                        هەرچەند بێت.</p>
                    </li>
                    <li>من کە لایەنی یەکەمم دەبێت ئەو موڵکە چۆڵ بکەم لە ماوەی
                      <span class="text-primary">({{ datainvoices?.invoice?.zhmarai_rozhi_cholkrdn }}) {{
                        numberToKurdishText(datainvoices?.invoice?.zhmarai_rozhi_cholkrdn) }} ڕۆژدا</span>
                      لەبەرواری <span class="text-primary">({{ datainvoices?.invoice?.from_datecholkrdn }})</span>
                      تا <span class="text-primary">({{ datainvoices?.invoice?.to_datecholkrdn }})</span>
                      و بە چۆڵی بەدەستی لایەنی دووەم بگات بێ دواکەوتن و بەپێچەوانەوە دەبێت مانگی
                      <span class="text-primary">
  ({{ commaNumber(datainvoices?.invoice?.krey_mangana * 1) }})
</span>

<span>
  ({{ numberToKurdishText(datainvoices?.invoice?.krey_mangana * 1) }}
  {{ datainvoices?.invoice?.krey_mangana_currency === 'USD' ? 'دۆلار' : 'دینار' }})
</span>
                      بدەم لە جیاتی کرێی هەر مانگێک تا ڕۆژی چۆڵکردن نابێت ئەم شتانە لەگەڵ خۆم ببەم:
                      <p>سکەی پەنجەرە ({{ datainvoices?.invoice?.skay_panjara }}) پانکەی ئاسمانی ({{
                        datainvoices?.invoice?.pankay_asman }}) سنگی چێشتنگە ({{ datainvoices?.invoice?.sngi_cheshtnga
                        }}) دووکەڵ کێش ({{ datainvoices?.invoice?.dukalkesh }}) ناوکوڵێنەر ({{
                          datainvoices?.invoice?.dastshor }}) دەست شۆر ({{ datainvoices?.invoice?.dastshor }}) تانکی ئاو
                        ({{ datainvoices?.invoice?.tankiaw }}) و هەرشتێکی خانوو بێت.</p>
                    </li>
                    <li>دەبێت لایەنی (فرۆشیار) و (کڕیار) کاروباری تەواوکردنی موڵک تەواو بکات لە ماوەی ({{ datainvoices?.invoice?.mawai_katy_cholkrdn }}) {{ numberToKurdishText(Number(datainvoices?.invoice?.mawai_katy_cholkrdn)) }} ڕۆژدا.</li>
<li>
  بەڕەزامەندی هەردوو لایەن لەسەر ئەم بڕگانەی لە بەڵێننامەکەدا هاتووە 
  لە بەرواری 
 
  <span class="text-primary">
    {{ (datainvoices?.invoice?.date_invoice) }}
  </span>
  ئیمزاکرا.
</li>                  </ol>
                </div>

                <div class="flex mt-8 items-center justify-between">
                  <div class="text-lg font-bold mt-8 text-[#9b7d59]">خاوەنی کۆمپانیا</div>
                  <div class="text-xs">
                    <p class="text-center mb-1 font-bold">ئیمزا</p>
                    <p>لایەنی یەکەم (فرۆشیار) :</p>
                    <p>ناو: {{ datainvoices?.invoice?.name_one }}</p>
                    <p>گەڕەک: {{ datainvoices?.invoice?.garak_one }}</p>
                    <p>کاری: {{ datainvoices?.invoice?.job_one }}</p>
                    <p>شاهد: {{ datainvoices?.invoice?.shahid_one }}</p>
                    <p>ژ.ت شاهید: {{ datainvoices?.invoice?.phone_one_shahid }}</p>
                    <p class="text-center mt-1 font-bold">ئیمزا</p>
                  </div>
                  <div class="text-xs">
                    <p class="text-center font-bold mb-1">ئیمزا</p>
                    <p>لایەنی دووەم (کڕیار) :</p>
                    <p>ناو: {{ datainvoices?.invoice?.name_two }}</p>
                    <p>گەڕەک: {{ datainvoices?.invoice?.garak_two }}</p>
                    <p>کاری: {{ datainvoices?.invoice?.job_two }}</p>
                    <p>شاهد: {{ datainvoices?.invoice?.shahid_two }}</p>
                    <p>ژ.ت شاهید: {{ datainvoices?.invoice?.phone_two_shahid }}</p>
                    <p class="text-center font-bold mt-1">ئیمزا</p>
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
import { defineProps, ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import { Delete, Edit, Printer } from '@element-plus/icons-vue';
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
  filters: Object,
  datainvoices: Object,
});

// ✅ Build absolute background image URL that works both locally and on server
const invoiceBgUrl = computed(() => {
  return `${window.location.origin}/assets/images/A4templateimg.jpg`;
});

const projectImages = ref([]);

watch(
  () => props.datainvoices?.projectImages,
  (newImages) => {
    if (newImages) {
      projectImages.value = newImages.map((img) => ({
        url: img,
        checked: true,
      }));
    }
  },
  { immediate: true }
);
function formatDate(date) {
  if (!date) return '';
  const [year, month, day] = date.split('T')[0].split('-');
  return `${day}/${month}/${year}`;
}


const isShuqah = computed(() => {
  return props.datainvoices?.invoice?.mulk_type === 'شووقە';
});


const perPage = ref(25);
const datefilter = ref(props.filters?.datefilter);

const fetchPage = debounce((page = props.data.current_page) => {
  const df = Array.isArray(datefilter.value) ? datefilter.value : [];
  router.get('/invoiceProjects', {
    q: props.searchQuery,
    page,
    perPage: perPage.value,
    datefilter: df,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}, 300);

const handlePageChange = (page) => fetchPage(page);

watch(() => props.searchQuery, () => fetchPage(props.data.current_page));
watch(() => datefilter.value, () => fetchPage(1), { deep: true });

const printArea = ref(null);

function printInvoicesdetail() {
  const printContents = printArea.value.outerHTML;
  const newWin = window.open("", "", "width=900,height=650");

  // ✅ Absolute URL so the new print window can find the image on the server
  const absoluteBgUrl = `${window.location.origin}/assets/images/A4templateimg.jpg`;

  let styles = "";
  document.querySelectorAll("style, link[rel='stylesheet']").forEach(s => {
    styles += s.outerHTML;
  });

  newWin.document.write(`
    <html>
      <head>
        <title>Invoice</title>
        ${styles}
        <style>
          @page { size: A4; margin: 0; }
          body { margin: 0; direction: rtl; }
          .invoice-container {
            width: 210mm;
            min-height: 297mm;
            box-sizing: border-box;
            page-break-after: always;
            font-family: 'DroidKufi';
            position: relative;
            padding-top: 20mm;
            padding-right: 10mm;
            padding-left: 10mm;
          }
          .invoice-details-page {
            background-image: url('${absoluteBgUrl}') !important;
            background-size: cover !important;
            background-position: center !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
          }
        </style>
      </head>
      <body>${printContents}</body>
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

function numberToKurdishText(number) {
  if (number === null || number === undefined || number === '') return '';
  const num = Math.floor(Number(number));
  if (isNaN(num)) return '';
  if (num === 0) return 'سفر';

  const ones = ['', 'یەک', 'دوو', 'سێ', 'چوار', 'پێنج', 'شەش', 'حەوت', 'هەشت', 'نۆ',
    'دە', 'یازدە', 'دوازدە', 'سێزدە', 'چواردە', 'پازدە', 'شازدە', 'حەڤدە', 'هەژدە', 'نۆزدە'];
  const tens = ['', '', 'بیست', 'سی', 'چل', 'پەنجا', 'شەست', 'حەفتا', 'هەشتا', 'نەوەد'];
  const hundreds = ['', 'سەد', 'دوو سەد', 'سێ سەد', 'چوار سەد', 'پێنج سەد',
    'شەش سەد', 'حەوت سەد', 'هەشت سەد', 'نۆ سەد'];

  function convertBelow1000(n) {
    if (n === 0) return '';
    if (n < 20) return ones[n];
    if (n < 100) {
      const ten = tens[Math.floor(n / 10)];
      const one = ones[n % 10];
      return one ? ten + ' و ' + one : ten;
    }
    const h = hundreds[Math.floor(n / 100)];
    const rest = convertBelow1000(n % 100);
    return rest ? h + ' و ' + rest : h;
  }

  const parts = [];
  if (num >= 1_000_000_000) parts.push(convertBelow1000(Math.floor(num / 1_000_000_000)) + ' ملیار');
  const afterBillion = num % 1_000_000_000;
  if (afterBillion >= 1_000_000) parts.push(convertBelow1000(Math.floor(afterBillion / 1_000_000)) + ' ملیۆن');
  const afterMillion = num % 1_000_000;
  if (afterMillion >= 1_000) parts.push(convertBelow1000(Math.floor(afterMillion / 1_000)) + ' هەزار');
  const last = num % 1_000;
  if (last > 0) parts.push(convertBelow1000(last));

  return parts.join(' و ');
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
  height: 297mm;
  background-size: cover;
  background-position: center;
  font-family: 'DroidKufi';
  position: relative;
  padding-top: 20mm;
  padding-right: 10mm;
  padding-left: 10mm;
  box-sizing: border-box;
  page-break-after: always;
  /* ✅ removed background-image from here — now set via :style binding */
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

-webkit-print-color-adjust: exact;
print-color-adjust: exact;
</style>