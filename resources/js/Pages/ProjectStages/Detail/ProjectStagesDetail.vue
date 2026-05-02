
<script setup>
import { ref, reactive } from "vue"
import {  router } from "@inertiajs/vue3"
import { ElMessage, ElMessageBox } from "element-plus"
import { onMounted } from "vue"

const projectID = ref(null)

onMounted(() => {
  // Take the last part of the URL path
  projectID.value = window.location.pathname.split("/").pop()
  console.log("Project ID:", projectID.value)
})
// ✅ Props
const props = defineProps({
  data: Object,
  openForm: Function,
});

// ✅ Reactive states
const dialogFormVisible = ref(false)

function openAddStage(stageName) {
  if (stageName !== getNextStageToInsert()) {
    ElMessage({
      message: "ناتوانرێت ئەم حاڵەکە زیاد بکرێت تاکو پێشوو زیاد نەکرا.",
      type: "warning",
    })
    return
  }

  form.stage = stageName
  form.project_id = projectID.value
  form.start_time = new Date().toLocaleString()
  form.end_time = null
  dialogFormVisible.value = true
}

const formLabelWidth = "190px";

const form = reactive({
  id: null,
  project_id: projectID,
  stage: null,
  start_time: new Date().toLocaleString(),
  end_time: null,
});
function resetForm() {
  form.id = null
  form.project_id = projectID,
    form.stage = null,
    form.start_time = new Date().toLocaleString(),
    form.end_time = null

}

// ✅ Constants
const allStages = [
  'Sale',
    'Measurment',
    'Design',
    'Pricing',
    'Creative',
    'Planning',
    'Reklam',
]

// ✅ Helpers
function getStageTitle(stage) {
  const titles = {
    Sale: "فرۆشتن",
    Design: "دیزاین",
    Creative: "دروستکردن",
    Planning: "دانان",
    Reklam: "ڕیکلام",
    Measurment: "قیاسکردن",
    Pricing: "نرخ پێدان",
  }
  return titles[stage] || stage
}

function getStageColor(stage) {
  const stageColors = {
    Planning: "bg-gradient-to-r from-fuchsia-800 to-fuchsia-600 to-fuchsia-500",
    Creative: "bg-gradient-to-r from-fuchsia-800 to-fuchsia-600 to-fuchsia-500",
    Design: "bg-gradient-to-r from-fuchsia-800 to-fuchsia-600 to-fuchsia-500",
    Sale: "bg-gradient-to-r from-fuchsia-800 to-fuchsia-600 to-fuchsia-500",
    Reklam: "bg-gradient-to-r from-fuchsia-800 to-fuchsia-600 to-fuchsia-500",
    Measurment: "bg-gradient-to-r from-fuchsia-800 to-fuchsia-600 to-fuchsia-500",
    Pricing: "bg-gradient-to-r from-fuchsia-800 to-fuchsia-600 to-fuchsia-500",
  }
  return stageColors[stage] || "bg-gray-500"
}

function getStageData(stageName) {
  return props.data.projectStages.find((s) => s.stage === stageName) || null
}
function formatDate(dateStr) {
  if (!dateStr) return ""
  return new Date(dateStr).toLocaleString("ckb-IQ", {
    timeZone: "Asia/Baghdad",
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  })
}

function getTimeBetween(start, end = null) {
  if (!start) return "0 seconds"

  const startDate = new Date(start)
  const endDate = end ? new Date(end) : new Date()
  let diffMs = endDate - startDate

  const days = Math.floor(diffMs / (1000 * 60 * 60 * 24))
  diffMs -= days * (1000 * 60 * 60 * 24)

  const hours = Math.floor(diffMs / (1000 * 60 * 60))
  diffMs -= hours * (1000 * 60 * 60)

  const minutes = Math.floor(diffMs / (1000 * 60))
  diffMs -= minutes * (1000 * 60)

  const seconds = Math.floor(diffMs / 1000)

  // Build a readable string
  const parts = []
  if (days) parts.push(`${days} ڕۆژ`)
  if (hours) parts.push(`${hours} سعات`)
  if (minutes) parts.push(`${minutes} دەقە`)
  if (seconds) parts.push(`${seconds} چرکە`)

  return parts.join(" و ")
}
function getTotalDays(start, end = null) {
  if (!start) return 0
  const startDate = new Date(start)
  const endDate = end ? new Date(end) : new Date()
  const diffMs = endDate - startDate
  return diffMs / (1000 * 60 * 60 * 24) // total days as float
}
function getDurationColor(start, end) {
  if (!start) return "bg-gray-100 dark:bg-gray-800"

  const diffDays = getTotalDays(start, end)

  if (diffDays <= 2) return "bg-green-100 dark:bg-green-900"
  else if (diffDays <= 4) return "bg-orange-100 dark:bg-orange-900"
  else return "bg-red-100 dark:bg-red-900"
}
// sbmit
function submit(event) {
  event.preventDefault()

  const method = form.id ? "put" : "post"
  const url = form.id ? `/projectStages/${form.id}` : "/projectStages"

  router[method](url, form, {
    onError: (errors) => {
      const errorMessages = Object.values(errors).flat().join("<br>")
      ElMessage({
        message: errorMessages,
        type: "error",
        dangerouslyUseHTMLString: true,
        duration: 5000,
      })
    },
    onSuccess: (success) => {
      ElMessage({
        message: success.props.flash?.message || "Operation completed successfully.",
        type: "success",
        duration: 5000,
      })
      resetForm()
      dialogFormVisible.value = false
    },
  })
}
// destroy
function destroy(id) {
  ElMessageBox.confirm("ئایە دڵنییایت لەسڕینەوەی ئەم داتاییە", "سڕینەوە", {
    confirmButtonText: "بەڵێ",
    cancelButtonText: "نەخێر",
    type: "info",
  })
    .then(() => {
      router.delete("/projectStages/" + id, {
        onError: (errors) => {
          const errorMessages = Object.values(errors).flat().join("<br>")
          ElMessage({ type: "info", message: errorMessages })
        },
        onSuccess: (success) => {
          ElMessage({
            type: "success",
            message: success.props.flash?.message,
          })
        },
      })
    })
    .catch(() => {
      ElMessage({ type: "info", message: "پاشگەزبوونەوە" })
    })
}
  // edit
  function edit(id) {
    router.get("/projectStages/" + id+"/edit", {}, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: (result) => {
        const stage = result.props.flash.data
        
        form.id = stage.id
        form.project_id = stage.project_id
        form.stage = stage.stage
        form.start_time = stage.start_time
        form.end_time = stage.end_time
        dialogFormVisible.value = true
      },
    })
  }

function getNextStageToInsert() {
  // Check which stages exist
  const existingStages = props.data.projectStages.map(s => s.stage)

  // Return the first stage that does not exist yet
  return allStages.find(stage => !existingStages.includes(stage)) || null
}


</script>


<template>
  <div
    class="rounded-sm border font-droidkufi border-stroke bg-white px-5 pt-6 m-5 pb-5 shadow-default 
           dark:border-strokedark dark:bg-boxdark-2 sm:px-7.5"
  >
    <!-- Header -->
    <h2 class="text-lg font-bold mb-4 text-center font-droidkufi">
      حاڵەکاتی پڕۆژەی <span class="font-bold text-2xl text-danger">" {{ data.project.name }} "</span>
    </h2>
    

    <!-- Stages Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div
        v-for="stageName in allStages"
        :key="stageName"
        :class="[
          'flex flex-col items-center p-4 rounded-lg shadow transition-shadow',
          getDurationColor(getStageData(stageName)?.start_time, getStageData(stageName)?.end_time)
        ]"
      >
      {{  }}
        <!-- Stage Title -->
        <div
          :class="[
            'w-full text-center py-2 rounded-sm text-white font-bold text-lg mb-6',
            getStageColor(stageName)
          ]"
        >
          <h4>{{ getStageTitle(stageName) }}</h4>
        </div>

        <!-- Times -->
        <div class="mt-4 w-full text-center">
          <template v-if="getStageData(stageName)">
            <p class="text-sm text-gray-600 dark:text-gray-300">
              ⏱ دەستپێکردن: {{ formatDate(getStageData(stageName).start_time) }}
            </p>
            <p v-if="getStageData(stageName).end_time" class="text-sm text-gray-600 dark:text-gray-300">
  ✅ کۆتایی: {{ formatDate(getStageData(stageName).end_time) }}
</p>
<p v-if="getStageData(stageName).end_time" class="text-sm text-success  mt-1">
  ⏱ ئەم پڕۆسەیە تێپەڕبوو بە : {{ getTimeBetween(getStageData(stageName).start_time, getStageData(stageName).end_time) }} بەردەوامبوو
</p>

            <p v-else class="text-sm text-danger">
              ⏳ لە پرۆسێسدایە ({{ getTimeBetween(getStageData(stageName).start_time) }} یە بەردەوامە)
            </p>
          </template>

          <!-- Stage not entered yet -->
          <template v-else>
            <p class="text-sm text-gray-500 italic">🚧 لە ئێستادا ئەو حاڵەکاتە بەردەست نییە</p>
          </template>
        </div>

 <div class="mt-3 flex gap-2 w-full justify-center">
  <template v-if="getStageData(stageName)">
    <!-- Stage exists → show edit/delete -->
 <el-button
  size="small"
  plain
  type="primary"
  @click="edit(getStageData(stageName)?.id)"
>
  دەسکاریکردن
</el-button>
    <el-button  @click="destroy(getStageData(stageName)?.id)" size="small" plain type="danger">
      سڕینەوە
    </el-button>
  </template>

  <template v-else-if="stageName === getNextStageToInsert()">
    <!-- Only allow insert for the next stage -->
    <el-button size="small" plain type="success" @click="openAddStage(stageName)">
      زیادکردن
    </el-button>
  </template>
</div>
      </div>
    </div>

    <!-- Duration color note -->
    <div class="mt-6 p-4 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300 font-droidkufi">
       <p><strong>تێبینی:</strong> ڕەنگەکان بنەمای پاشەکەوتکردنی کاتی پڕۆژەی هەیە</p>
       <ul class="list-disc ml-5 mt-2">
         <li><span class="text-green-600">سەوز</span> → ≤ 2 ڕۆژ</li>
         <li><span class="text-orange-600">نارنجی</span> → > 2 ڕۆژ و ≤ 4 ڕۆژ</li>
         <li><span class="text-red-600">سور</span> → > 4 ڕۆژ</li>
       </ul>
    </div>
  </div>


     <el-dialog class="font-droidkufi" dir="rtl" v-model="dialogFormVisible" title="فۆڕمی زیاکردنی حاڵەکاتی پڕۆژەکان" width="700"
        align-center="true" :close-on-click-modal="false">
        <el-form :model="form" @keydown.enter="submit">
            <el-form-item label-position="left" label="ناوی پڕۆژە" :label-width="formLabelWidth">
                <el-input disabled clearable v-model="data.project.name" />
            </el-form-item>
            <el-form-item label-position="left" label="حاڵەکاتی پڕۆژە" :label-width="formLabelWidth">
                <el-input disabled clearable v-model="form.stage" />
            </el-form-item>
            <el-form-item label-position="left" label="بەرواری دەستپێکردنی پڕۆژە" :label-width="formLabelWidth">
<el-date-picker
  style="width: 100%;"
  type="datetime"
  clearable
  v-model="form.start_time"

/>
            </el-form-item>
            <el-form-item label-position="left" label="بەرواری کۆتای هاتنی پڕۆژە" :label-width="formLabelWidth">
                <el-date-picker
                
                style="width: 100%;" type="datetime" clearable v-model="form.end_time" />  
                  <small class="text-danger mt-1 block">
        ئەم فیلدە پێویست نییە تۆمار بکرێت تا کاتێ پڕۆژە کۆتایی بگەیەت
      </small>
            </el-form-item>
            
        </el-form>

        <template #footer class="w-full">
            <div class="dialog-footer">
                <el-button @click="submit" type="primary" plain style="float: left;">
                    زەخیرەکردنی زانییاری حاڵەکاتی پڕۆژەکان
                </el-button>
            </div>
        </template>
    </el-dialog>

</template>