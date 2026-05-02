<template>
  <div className="rounded-sm border border-stroke bg-white px-5 pt-6 m-5 pb-2.5 
    shadow-default dark:border-strokedark dark:bg-boxdark-2 sm:px-7.5 xl:pb-1">
    <div class="md:w-1/2 px-4 font-droidkufi">
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
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                #</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                ناوی پڕۆژە</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                جۆری موڵک</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                ناونیشان</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                نرخ</th>

              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
              </th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
              </th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                حاڵەکاتی پڕۆژە</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-black">
                دروستکراوە لە</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-black">
                دەستکاری</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, i) in data.data" :key="i"
              :class="(item.is_archived === 1 || item.is_archived === '1' || item.is_archived === true) ? 'bg-orange-100' : ''">
              <td
                className="border bg-gray-100 border-[#eee] py-2 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                {{ i + 1 + (data.current_page - 1) * data.per_page }}
              </td>
              <td
                className="border font-droidkufi border-[#eee] py-1 px-4 dark:border-strokedark sm:text-xs text-primary font-bold text-base">
                {{ item.name }}
              </td>
              <td
                className="border font-droidkufi border-[#eee] py-1 px-4 dark:border-strokedark sm:text-xs text-black font-bold text-base">
                {{item.type_projects.map(tp => tp.name).join(', ') || '—'}}
              </td>
              <td
                className="border font-droidkufi border-[#eee] py-1 px-4 dark:border-strokedark sm:text-xs text-black font-bold text-base">
                {{item.locations.map(tp => tp.name).join(', ') || '—'}}
              </td>
              <td
                className="border font-droidkufi border-[#eee] py-1 px-4 dark:border-strokedark sm:text-xs text-danger font-bold text-base">
                {{ item.price_one }} $ - {{ item.price_two }} $
              </td>
              <td
                className="border font-droidkufi border-[#eee] py-1 mx-auto  dark:border-strokedark text-sm sm:text-xs text-black">
                <el-button plain @click="show(item.id)">

                  <i class="fa-solid fa-eye"></i>
                </el-button>

              </td>
              <td
                className="border font-droidkufi border-[#eee] py-1 mx-auto  dark:border-strokedark text-sm sm:text-xs text-black">
                <el-button plain @click="archive(item)">

                  <i class="fa-solid fa-archive"></i>
                </el-button>

              </td>

              <td class="border border-[#eee] py-1 flex px-4 text-sm text-black">
                <div v-for="(stage, index) in item.mulkstages" :key="stage.id" class="flex items-center">
                  <el-button class="no-click" plain :type="stageType(stage.stage)">
                    {{ stage.stage }}
                  </el-button>

                  <span v-if="index !== item.mulkstages.length - 1" class="mx-1">
                    &#8630;
                  </span>
                </div>

                <span v-if="!item.mulkstages.length" class="text-gray-400">
                  هیچ ئاستێک نییە
                </span>
              </td>

              <td className="border border-[#eee] py-1 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                {{ new Date(item.created_at).toLocaleString() }}
                <span className="mx-1">by</span>
                {{ item?.user?.name }}
              </td>
              <td className="border border-[#eee] py-1 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
               <el-button v-if="can('delete projects')" type="danger" plain :icon="Delete" @click="destroy(item.id)"
                  circle class="mr-2" />
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

      <div class="mt-3 p-4 space-x-3 flex items-center text-sm font-droidkufi">
        <div class="w-4 h-4 mx-2 bg-orange-100 border border-gray-300 mr-2"></div>
        <span>پڕۆژەی ئەرشیڤکراو</span>
        <div class="w-4 h-4 mx-2 bg-white border border-gray-300 mr-2"></div>
        <span>پڕۆژەی ئەرشیڤنەکراو</span>
      </div>
    </div>
    <el-dialog class="font-droidkufi" dir="rtl" v-model="dialogFormVisibleShow" title="وردەکاری کڕیار" width="70%"
      :close-on-click-modal="true" @close="resetFormdata">
      <div class="space-y-6">
        <!-- Row 1: Customer Info -->
        <div class="md:flex gap-6">
          <div class="w-full md:w-1/2">
            <p class="text-sm font-bold text-gray-600">ناوی کڕیار</p>
            <p class="text-base text-gray-900">{{ dataform.name }}</p>
          </div>
          <div class="w-full md:w-1/2">
            <p class="text-sm font-bold text-gray-600">
              ژمارەی تەلەفون
              <el-button type="success" plain circle class="mr-2" @click="openWhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
              </el-button>
            </p>
            <p class="text-base text-gray-900">{{ dataform.phone }}</p>
          </div>
        </div>

        <!-- Row 2: Project Info -->
        <div class="md:flex gap-6">
          <div class="w-full md:w-1/2">
            <p class="text-sm font-bold text-gray-600">جۆری موڵک</p>
            <p class="text-base text-gray-900">
              {{dataform.type_projects?.map(t => t.name).join('، ') || '—'}}
            </p>
          </div>
          <div class="w-full md:w-1/2">
            <p class="text-sm font-bold text-gray-600">ناونیشان</p>
            <p class="text-base text-gray-900">
              {{dataform.locations?.map(l => l.name).join('، ') || '—'}}
            </p>
          </div>
        </div>

        <!-- Row 3: Prices -->
        <div class="md:flex items-center justify-between">
          <div class="md:w-1/2">
            <p class="text-sm font-bold text-gray-600">نرخی سەرەتا</p>
            <p class="text-base text-danger whitespace-pre-line">{{ commaNumber(dataform.price_one * 1) }}</p>
          </div>
          <div class="md:w-1/2">
            <p class="text-sm font-bold text-gray-600">نرخی کۆتایی</p>
            <p class="text-base text-danger whitespace-pre-line">{{ commaNumber(dataform.price_two * 1) }}</p>
          </div>
        </div>

        <!-- Mulks Header -->
        <div v-if="dataform.mulks && dataform.mulks.length" class="mb-2">
          <p class="text-base font-bold text-gray-700">
            موڵکی هەڵبژێردراو:
            <span class="text-gray-900">
              {{dataform.mulks.map((m, i) => i + 1).join('، ')}}
            </span>
          </p>
        </div>

        <!-- Mulks List -->
        <div v-if="dataform.mulks && dataform.mulks.length" class="space-y-4">
          <div v-for="(mulk, mulkIndex) in dataform.mulks" :key="mulk.id"
            class="border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="md:flex gap-4 mb-2">
              <div class="md:w-1/3">
                <p class="text-sm font-bold text-gray-600">ناوی موڵک</p>
                <p class="text-base text-gray-900">{{ mulk.name }}</p>
              </div>
              <div class="md:w-1/3">
                <p class="text-sm font-bold text-gray-600">نرخ</p>
                <p class="text-base text-danger">{{ commaNumber(mulk.price) }}</p>
              </div>
              <div class="md:w-1/3">
                <p class="text-sm font-bold text-gray-600">ڕوووبەر</p>
                <p class="text-base text-gray-900">{{ mulk.Rwbar }}</p>
              </div>
            </div>

            <div class="md:flex gap-4 mb-2">
              <div class="md:w-1/2">
                <p class="text-sm font-bold text-gray-600">جۆری موڵک</p>
                <p class="text-base text-gray-900">{{ mulk.type_project?.name ?? '—' }}
                </p>
              </div>
              <div class="md:w-1/2">
                <p class="text-sm font-bold text-gray-600">شوێن</p>
                <p class="text-base text-gray-900">{{ mulk.location?.name ?? '—' }}
                </p>
              </div>
            </div>

            <!-- Mulks Images -->
            <div class="mt-2">
              <p class="text-sm font-bold text-gray-600 mb-2">وێنەکان</p>
              <div v-if="mulk.images && mulk.images.length" class="flex gap-3 flex-wrap">
                <div v-for="(img, imgIndex) in mulk.images" :key="img.id" class="relative"
                  style="width: 120px; height: 120px">
                  <el-image :src="`/storage/${img.path}`" :preview-src-list="mulk.images.map(i => `/storage/${i.path}`)"
                    fit="cover" show-progress style="width: 100%; height: 100%; border-radius: 0.5rem" />
                  <el-button size="mini" type="danger" class="absolute top-1 right-1"
                    @click="printImage(mulkIndex, imgIndex)">
                    <i class="fa-solid fa-print"></i>
                  </el-button>
                </div>
              </div>
              <p v-else class="text-gray-400 text-sm">وێنە نییە</p>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-400 text-sm">موڵک نییە</p>

        <!-- Notes -->
        <div class="flex items-center justify-between mt-4">
          <div>
            <p class="text-sm font-bold text-gray-600">تێبینی</p>
            <p class="text-base text-gray-900">{{ dataform.note }}</p>
          </div>
        </div>
      </div>
    </el-dialog>

  </div>


</template>




<script setup>
import { defineProps, reactive, ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import {
  Delete,
  Edit,
} from '@element-plus/icons-vue';
import { dayjs } from 'element-plus';
import commaNumber from 'comma-number';

const props = defineProps({
  data: Object,
  destroy: Function,
  edit: Function,
  searchQuery: String,
  search: Function,
  openForm: Function,
  filters: Object, // 👈 add filters from backend

});


const dialogFormVisibleShow = ref(false);
// Reactive object to store project data
const dataform = reactive({})

// Show project details
function show(id) {
  router.get(`customers/${id}`, {}, {
    preserveState: true,
    preserveScroll: true,

    onSuccess: (result) => {
      // ✅ get mulk from flash
      const mulk = result.props.flash?.data;

      if (!mulk) {
        console.error("Mulk data not found!");
        return;
      }

      // ✅ reset old data first (important)
      Object.keys(dataform).forEach(k => delete dataform[k]);

      // ✅ assign main fields
      Object.assign(dataform, mulk);

      dataform.type_projects = mulk.type_projects || [];
      dataform.locations = mulk.locations || [];
      dataform.mulks = mulk.mulks || [];
      // ✅ images (safe)
      dataform.desgin_img = mulk.images?.map(img => img.path) || [];

      // ✅ relations (safe access)
      dataform.type_project_name = mulk.type_project?.name || '';
      dataform.location_name = mulk.location?.name || '';

      // ✅ fallback values
      dataform.name = mulk.name ?? '';
      dataform.phone = mulk.phone ?? '';
      dataform.note = mulk.note ?? '';
      dataform.price = mulk.price ?? '';
      dataform.Rwbar = mulk.Rwbar ?? '';

      // ✅ open dialog
      dialogFormVisibleShow.value = true;
    },

    onError: (errors) => {
      console.error("Failed to load mulk:", errors);
    }
  });
}


const archive = (item) => {
  ElMessageBox.confirm(
    'ئایا دڵنیایت لە ئەرشیڤکردنی ئەم پڕۆسەیە؟', // Message
    'ئەرشیڤکردن', // Title
    {
      confirmButtonText: 'بەڵێ',
      cancelButtonText: 'نەخێر',
      type: 'info',
      customClass: 'custom-confirm-box', // Optional custom class
    }
  )
    .then(() => {
      // User confirmed
      router.post('/customers/archive', {
        customer_id: item?.id
      }, {
        onSuccess: () => {
          ElMessage({
            message: 'بەسەرکەو تووی ئەرشیڤ کرا👌',
            type: 'success',
            duration: 4000,
            customClass: 'custom-confirm-box', // Optional custom class

          });
          router.visit('/customers', { preserveScroll: true });
        },
        onError: (errors) => {
          const errorMessages = Object.values(errors).flat().join('<br>');
          ElMessage({
            message: errorMessages,
            type: 'error',
            dangerouslyUseHTMLString: true,
            duration: 5000,
            customClass: 'custom-confirm-box', // Optional custom class

          });
        }
      });
    })
    .catch(() => {
      // User canceled
      ElMessage({
        message: 'پاشگەزبوونەوە',
        type: 'info',
        duration: 3000,
        customClass: 'custom-confirm-box', // Optional custom class

      });
    });
}


const printImage = (mulkIndex, imgIndex) => {
  const mulk = dataform.mulks[mulkIndex];
  if (!mulk || !mulk.images || !mulk.images[imgIndex]) return;

  const imgSrc = `/storage/${mulk.images[imgIndex].path}`;
  const templateSrc = '/assets/images/A4templateimg.jpg';

  const printWindow = window.open('', '_blank', 'width=900,height=1200');
  printWindow.document.write(`
    <html>
      <head>
        <title>Print Image</title>
        <style>
          @page { size: A4; margin: 0; }
          body {
            margin: 0;
            padding: 0;
            width: 210mm;
            height: 297mm;
            background-image: url('${templateSrc}');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
          }
          img {
            max-width: 180mm;
            max-height: 250mm;
            object-fit: contain;
          }
        </style>
      </head>
      <body>
        <img src="${imgSrc}" />
      </body>
    </html>
  `);
  printWindow.document.close();
  printWindow.focus();

  printWindow.onload = () => {
    printWindow.print();
    printWindow.close();
  };
};


// Reset project data (for closing dialog or clearing)
const resetFormdata = () => {
  Object.keys(dataform).forEach(key => delete dataform[key])
  dialogFormVisibleShow.value = false
}

const perPage = ref(10); // default value


// hydrate datefilter from backend (so refresh keeps range)
const datefilter = ref(props.filters?.datefilter);

const fetchPage = debounce((page = props.data.current_page) => {
  const df = Array.isArray(datefilter.value) ? datefilter.value : [];

  router.get('/customers', {
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

const stageType = (stageName) => {
  const colors = {
    request: 'danger', // blue
    prepare: 'warning', // green
    show: 'primary',   // orange
    handlinh: 'success',      // red
    show: 'contract',   // orange
  };
  return colors[stageName] || 'info'; // fallback
};



function openWhatsApp() {
  let phone = dataform.phone.replace(/\D/g, ''); // remove non-digits

  // Remove leading 0
  if (phone.startsWith('0')) {
    phone = phone.slice(1);
  }

  // Add +964 if not already present
  if (!phone.startsWith('964')) {
    phone = '964' + phone;
  }

  const url = `https://wa.me/${phone}`;
  window.open(url, '_blank');
}


</script>

<style>
.custom-input .el-input__inner {
  border-radius: 0 !important;
  padding: 0 !important;
}

.print-container {
  display: none;
  /* hide normally */
}

.no-click {
  cursor: default !important;
}
</style>