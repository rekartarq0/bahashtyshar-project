<template>
  <div className="rounded-sm border border-stroke bg-white px-5 pt-6 m-5 pb-2.5 
    shadow-default dark:border-strokedark dark:bg-boxdark-2 sm:px-7.5 xl:pb-1">
    <div className="flex flex-col justify-between items-center w-full mb-4">

{{ }}      <div className="w-full overflow-x-auto">

        <table className="w-full table-auto">
          <thead className="font-droidkufi">
            <tr className="bg-gray-100 text-right dark:bg-meta-4">
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                #</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                ناوی مووڵک</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                جۆری موڵک</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                ژ.تەلەفۆن</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
              </th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                ناونیشان</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                نرخ</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                ڕووبەر</th>

              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
              </th>

              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-black">
                دروستکراوە لە</th>
              <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-blacky">
                دەسکاری </th>

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
                className="border font-droidkufi border-[#eee] py-1 px-4 dark:border-strokedark sm:text-xs text-gray-700 font-bold text-base">
                {{ item.type_project.name }}

              </td>
              <td
                className="border font-droidkufi border-[#eee] py-1 px-4 dark:border-strokedark sm:text-xs text-gray-700 font-bold text-base">
                {{ item.phone }}
              </td>
              <td
                className="border font-droidkufi border-[#eee] py-1 mx-auto  dark:border-strokedark text-sm sm:text-xs text-black">
                <el-button plain @click="archive(item)">

                  <i class="fa-solid fa-archive"></i>
                </el-button>

              </td>
              <td
                className="border font-droidkufi border-[#eee] py-1 px-4 dark:border-strokedark sm:text-xs text-gray-700 font-bold text-base">
                {{ item?.location?.name }}
              </td>
              <td
                className="border  font-droidkufi border-[#eee] py-1 px-4 dark:border-strokedark sm:text-xs text-gray-700 font-bold text-base">
                {{ item.price }} <span class="text-sm text-red-500">$</span>
              </td>
              <td
                className="border font-droidkufi border-[#eee] py-1 px-4 dark:border-strokedark sm:text-xs text-gray-700 font-bold text-base">
                {{ item.Rwbar }}
              </td>
              <td
                className="border font-droidkufi border-[#eee] py-1 mx-auto  dark:border-strokedark text-sm sm:text-xs text-black">
                <el-button plain @click="show(item.id)">
                  <i class="fa-solid fa-eye"></i>
                </el-button>

              </td>


              <td className="border border-[#eee] py-1 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                {{ new Date(item.created_at).toLocaleString() }}
                <span className="mx-1">by</span>
                {{ item?.user?.name }}
              </td>

              <td className="border border-[#eee] py-1 px-4  dark:border-strokedark text-sm sm:text-xs text-black">
                <el-button v-if="can('delete projects')" type="danger" plain :icon="Delete" @click="destroy(item.id)"
                  circle class="mr-2" />
                <el-button v-if="can('edit projects')" type="primary" plain :icon="Edit" @click="edit(item.id)" circle
                  class="mr-2" />
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
        <span>مووڵکی ئەرشیڤکراو</span>
        <div class="w-4 h-4 mx-2 bg-white border border-gray-300 mr-2"></div>
        <span>مووڵکی ئەرشیڤنەکراو</span>
      </div>
    </div>
    <el-dialog class="font-droidkufi" dir="rtl" v-model="dialogFormVisibleShow" title="وردەکاری مووڵک" width="80%"
      :close-on-click-modal="true" @close="resetFormdata">
      <div class="space-y-6">
        <!-- Row 1 -->
        <div class="md:flex gap-6">
          <div class="w-full md:w-1/2">
            <p class="text-sm font-bold text-gray-600">ناوی مووڵک</p>
            <p class="text-base text-gray-900">{{ dataform.name }}</p>
          </div>

          <div class="w-full md:w-1/2">
            <p class="text-sm font-bold text-gray-600">ناونیشان</p>
            <p class="text-base text-gray-900">{{ dataform?.location?.name }}</p>
          </div>

        </div>



        <!-- Row 2 -->
        <div class="md:flex gap-6">
          <div class="w-1/2">
            <p class="text-sm font-bold text-gray-600">
              ژمارەی تەلەفون
              <el-button type="success" plain circle class="mr-2" @click="openWhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
              </el-button>
            </p>
            <p class="text-base text-gray-900">{{ dataform.phone }}</p>
          </div>
          <div class="w-1/2">
            <p class="text-sm font-bold text-gray-600">
              جۆری موڵک

            </p>
            <p class="text-base text-gray-900">{{ dataform?.type_project?.name }}</p>
          </div>
        </div>

        <!-- شووقە details -->
        <div v-if="isShuqahShow" class="md:flex gap-6">
          <div class="w-full md:w-1/3">
            <p class="text-sm font-bold text-gray-600">عيماره</p>
            <p class="text-base text-gray-900">
              {{ dataform.emara || '-' }}
            </p>
          </div>

          <div class="w-full md:w-1/3">
            <p class="text-sm font-bold text-gray-600">قات</p>
            <p class="text-base text-gray-900">
              {{ dataform.qat || '-' }}
            </p>
          </div>

          <div class="w-full md:w-1/3">
            <p class="text-sm font-bold text-gray-600">ژ. شوقە</p>
            <p class="text-base text-gray-900">
              {{ dataform.zhmarai_shwqa || '-' }}
            </p>
          </div>
        </div>

        <!-- Row 3 -->
        <div class="md:flex gap-6">
          <div class="w-full md:w-1/2">
            <p class="text-sm font-bold text-gray-600">نرخ</p>
            <p class="text-base text-gray-900">{{ dataform.price }} $</p>
          </div>
          <div class="w-full md:w-1/2">
            <p class="text-sm font-bold text-gray-600">لینکی ناونیشان</p>
            <a :href="dataform.link_location" target="_blank"
              class="inline-flex items-center mt-2 px-3 py-1.5 text-sm font-medium text-white bg-primary rounded-lg shadow transition">
              بینینی لەسەر ماپ
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M14 3h7m0 0v7m0-7L10 14m0 0H3m7 0v7" />
              </svg>
            </a>
          </div>

        </div>

        <!-- Content -->
        <div class="flex items-center justify-between w-full">
          <div class="w-1/2">
            <p class="text-sm font-bold text-gray-600">ڕووبەر</p>
            <p class="text-base text-gray-900 whitespace-pre-line">{{ dataform.Rwbar }}</p>
          </div>
          <div class="w-1/2">

          <div class="flex items-center gap-3 mt-2">
            <!-- Facebook Icon -->
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white shadow">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                <path
                  d="M22.675 0h-21.35C.597 0 0 .597 0 1.326v21.348C0 23.403.597 24 1.326 24h11.495v-9.294H9.691V11.01h3.13V8.309c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.796.715-1.796 1.763v2.313h3.587l-.467 3.696h-3.12V24h6.116C23.403 24 24 23.403 24 22.674V1.326C24 .597 23.403 0 22.675 0z" />
              </svg>
            </div>

            <!-- Link -->
            <div>
              <p class="text-sm font-semibold text-gray-600">لینکی فەیسبووک</p>
              <a :href="dataform.facebook_link" target="_blank" rel="noopener noreferrer"
                class="text-blue-600 font-medium hover:underline hover:text-blue-700 transition">
                بینینی پۆستی فەیسبووک
              </a>
            </div>
          </div>
          </div>

        </div>

        <!-- Measurement -->
        <div>
          <p class="text-sm font-bold text-gray-600">ژمارەی موڵکەکە</p>
          <p class="text-base text-gray-900">{{ dataform.number_mulk }}</p>
        </div>

        <!-- Images -->
        <div>
          <p class="text-sm font-bold text-gray-600 mb-2">وێنەکان</p>
          <div v-if="dataform.desgin_img && dataform.desgin_img.length" class="flex gap-3 flex-wrap">
            <el-image v-for="(img, index) in dataform.desgin_img" :key="index" style="width: 120px; height: 120px"
              :src="`/storage/${img}`" :preview-src-list="dataform.desgin_img.map(i => `/storage/${i}`)" fit="cover">
              <template #viewer-toolbar="{ actions, prev, next, reset, activeIndex, setActiveItem }">
                <el-icon @click="prev">
                  <Back />
                </el-icon>
                <el-icon @click="next">
                  <Right />
                </el-icon>
                <el-icon @click="actions('zoomOut')">
                  <ZoomOut />
                </el-icon>
                <el-icon @click="actions('zoomIn')">
                  <ZoomIn />
                </el-icon>
                <el-icon @click="actions('clockwise')">
                  <RefreshRight />
                </el-icon>
                <el-icon @click="actions('anticlockwise')">
                  <RefreshLeft />
                </el-icon>
                <el-icon @click="reset">
                  <Refresh />
                </el-icon>
                <!-- ✅ ئەمە دەتوانی زیاد بکەی -->
                <el-icon @click="downloadImage(activeIndex)">
                  <Download />
                </el-icon>
              </template>

            </el-image>

          </div>
          <p v-else class="text-gray-400 text-sm">وێنە نییە</p>
        </div>

        <!-- Xamlandn -->
        <div>
          <p class="text-sm font-bold text-gray-600">تێبینی</p>
          <p class="text-base text-gray-900">{{ dataform.note }}</p>
        </div>
      </div>

    </el-dialog>
  </div>


</template>




<script setup>
import { computed, defineProps, reactive, ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import {
  Delete,
  Edit,
} from '@element-plus/icons-vue';
import { dayjs } from 'element-plus';

const props = defineProps({
  data: Object,
  destroy: Function,
  edit: Function,
  search: String,
  openForm: Function,
  filters: Object, // 👈 add filters from backend
    perPage: Number,


});
const emit = defineEmits(['page-change']);



const dialogFormVisibleShow = ref(false);
// Reactive object to store project data
const dataform = reactive({})



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
      router.post('/projects/archivedwithoutmulk', {
        id: item?.id
      }, {
        onSuccess: () => {
          ElMessage({
            message: 'بەسەرکەو تووی ئەرشیڤ کرا👌',
            type: 'success',
            duration: 4000,
            customClass: 'custom-confirm-box', // Optional custom class

          });
          router.visit('/projects', { preserveScroll: true });
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


// Show project details
function show(id) {
  router.get(`projects/${id}`, {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (result) => {
      // Safely get project from props (supports both Inertia props or flash)
      const project = result.props.project || result.props.flash?.data
      if (!project) {
        console.error("Project data not found!")
        return
      }

      // Copy all project properties into dataform
      Object.assign(dataform, project)

      // Normalize type_project_id to integer
      dataform.type_project_id = Number(project.type_project_id)

      // Normalize images array (avoid undefined)
      dataform.desgin_img = project.images?.map(img => img.path) || []

      // Ensure other null fields are safely displayed
      dataform.name = project.name || ""
      dataform.customer_name = project.customer_name || ""
      dataform.phone = project.phone || ""
      dataform.address = project.address || ""
      dataform.location = project.location || ""
      dataform.content = project.content || ""
      dataform.measurment = project.measurment || ""
      dataform.xamlandn = project.xamlandn || ""
      console.log(project);

      dataform.emara = project.emara || ""
      dataform.qat = project.qat || ""
      dataform.zhmarai_shwqa = project.zhmarai_shwqa || ""

      // Open dialog
      dialogFormVisibleShow.value = true
    },
    onError: (errors) => {
      console.error("Failed to load project:", errors)
    }
  })
}
const isShuqahShow = computed(() => {
  return dataform?.type_project?.name === 'شووقە'
})



// Reset project data (for closing dialog or clearing)
const resetFormdata = () => {
  Object.keys(dataform).forEach(key => delete dataform[key])
  dialogFormVisibleShow.value = false
}

const perPage = ref(10); // default value

const fetchPage = debounce((page = props.data.current_page) => {
  router.get(
    '/projects',
    {
      q: props.search || null,
      page,
      perPage: perPage.value,
      datefilter: props.filters?.datefilter || null,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    }
  );
}, 300);


// 🔎 Watch search query
watch(
  () => props.searchQuery,
  () => {
    fetchPage(props.data.current_page);
  }
);

const handlePageChange = (page) => {
  fetchPage(page); 
};

const stageType = (stageName) => {
  const colors = {
    Planning: 'danger', // blue
    Creative: 'warning', // green
    Design: 'primary',   // orange
    Sale: 'success',      // red
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