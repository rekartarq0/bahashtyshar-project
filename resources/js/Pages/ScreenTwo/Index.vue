

<template>
  <AppLayout title="شاشەی دووەم">
    <template #header>
      <div class="flex items-center justify-between">
<h4
        dir="rtl"
        class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black pl-1 sm:pl-2"
      >
        شاشەی دووەم
      </h4>
      <el-button v-if="can('write projects')" style="height: 40px;" @click="dialogFormVisible = true"
                 type="primary" class="font-droidkufi mr-2 object-cover">
                 <i class="fa fa-plus ml-2"></i>
             </el-button>
      </div>

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
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-2 gap-8 px-4">
  <div
    v-for="stage in stages.filter(s => can(`read ${s}`))"
    :key="stage"
    class="flex flex-col items-center"
  >
    <!-- Stage Header -->
    <div
      :class="[
        'w-full text-center py-2 rounded-sm text-white font-bold text-lg mb-6',
        getStageColor(stage)
      ]"
    >
      {{ 
        stage === 'Planning' ? 'دانان' :
        stage === 'Creative' ? 'دروستکردن' :
        stage === 'Design' ? 'دیزاین' :
        stage === 'Measurment' ? 'قیاسکردن' :
        stage === 'Pricing' ? 'نرخ پێدان' :
        stage === 'Reklam' ? 'ڕیکلام' :
        stage === 'Sale' ? 'وەرگرتن' : stage
      }}
    </div>

    <!-- Projects -->
    <div
      v-if="projectsByStage[stage]?.length"
      class="grid grid-cols-3 xl:grid-cols-5 gap-3 space-x-8"
    >
      <div
    v-for="item in projectsByStage[stage].filter(i => !i.is_pricing_complete)"
        :key="item.id"
        class="relative group"
      >
        <!-- Project Card -->
        <div
          v-if="!item.is_pricing_complete"
          @click="show(item.project_id)"
           :class="[
      'relative flex items-center justify-center rounded-md text-white text-[10px] md:text-[12px] font-semibold text-center leading-tight shadow-md cursor-pointer transition-transform duration-200 hover:scale-105 group',
      item.backed_from_pricing ? 'bg-black' : getDurationColor(item.start_time, item.end_time),
      index === 0 
        ? 'w-36 h-24 scale-105 shadow-xl ring-2 ring-yellow-400 animate-pulse' 
        : 'w-36 h-20'
    ]"
        >
          {{ item.project.name }}
          
          <!-- Note Pin -->
           <div class="flex items-center justify-between">

             <div v-if="item.note" class="absolute top-1 left-1">
             <i class="fa-solid fa-pen text-primary"></i>
             </div>
               <div v-if="item.stage ==='Design' && item.designer_name !==null" 
                 class="absolute top-2 text-black p-[2px] shadow ring-1 ring-primary bg-white text-[10px] left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center font-bold">

                 {{ item.designer_name }}
               </div>
           </div>
          <!-- Status Icon -->
          <div class="absolute top-1 right-1">
            <Check v-if="getProjectStatus(item) === 'success'" class="w-4 h-4 text-green-300" />
            <Clock v-else-if="getProjectStatus(item) === 'inProcess'" class="w-4 h-4 text-yellow-300" />
            <Minus v-else class="w-4 h-4 text-gray-300" />
          </div>
        

          <!-- Hover Actions -->
          <div
            class="absolute inset-0 flex justify-between items-center px-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
          >
            <!-- Completed & Archived -->
            <Check class="w-5 h-5 text-green-500" />
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
  <p class="my-1">
    <strong>حاڵەت:</strong>
    <span v-if="getProjectStatus(item) === 'success'" class="text-green-600">بەسەرکەوتوویی تەواو بوو ✅</span>
    <span v-else-if="getProjectStatus(item) === 'inProcess'" class="text-yellow-600">هێشتا بەردەوامە ⏳</span>
    <span v-else class="text-gray-500">دەستپێ نەکراوە</span>
  </p>
  <hr v-if="item.note !==null">
  <div v-if="item.note !==null" class="flex mt-2 font-droidkufi">
     <p class="font-bold text-primary">تێبینی:</p>
              <p>{{ item.note }}</p>
  </div>
  <hr v-if="item.stage ==='Design' && item.designer_name !==null">
  <div v-if="item.stage ==='Design' && item.designer_name !==null" class="flex mt-2 font-droidkufi">
     <p class="font-bold text-danger">دیزاینەر:</p>
              <p>{{ item.designer_name }}</p>
  </div>
</div>
</div>

</div>

    <!-- No projects -->
    <div v-else class="text-gray-400 text-xs text-center italic mt-6">
      هیچ پرۆژەیەک نییە
    </div>
    <!-- Divider for Pricing -->
    <hr v-if="stage === 'Pricing' && projectsByStage[stage]?.length" class="mt-2 w-full border-gray-300 shadow rounded" />
    <hr v-if="stage === 'Sale' && projectsByStage[stage]?.length" class="mt-2 w-full border-gray-300 shadow rounded" />

    <!-- Completed Pricing Projects (below hr) -->
    <div
      v-if="stage === 'Pricing' ||stage === 'Sale'  && projectsByStage[stage]?.length"
      class="grid grid-cols-3 xl:grid-cols-5 gap-3 space-x-8 mt-2"
    >
      <div
    v-for="item in projectsByStage[stage]?.filter(i => i.is_pricing_complete)"
        :key="item.id"
        class="relative group"
      >
        <!-- Project Card (only if pricing is complete) -->
        <div
          v-if="item.is_pricing_complete"
          @click="show(item.project_id)"
          :class="[
            'relative w-36 h-20 flex items-center bg-gray-400 justify-center rounded-md text-white text-[10px] md:text-[12px] font-semibold text-center leading-tight shadow-md cursor-pointer transition-transform duration-200 hover:scale-105 group',
          ]"
        >
          {{ item.project.name }}

          <!-- Note Pin -->
          <div class="flex items-center justify-between">
            <div v-if="item.note" class="absolute top-1 left-1">
              <i class="fa-solid fa-pen text-primary"></i>
            </div>
            <div
              v-if="item.stage === 'Design' && item.designer_name !== null"
              class="absolute top-2 text-black p-[2px] shadow ring-1 ring-primary bg-white text-[10px] left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center font-bold"
            >
              {{ item.designer_name }}
            </div>
          </div>

          <!-- Status Icon -->
          <div class="absolute top-1 right-1">
            
            <CircleCheckFilled class="w-4 h-4" />
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
          <p class="my-1">
            <strong>حاڵەت:</strong>
            <span v-if="getProjectStatus(item) === 'success'" class="text-green-600">بەسەرکەوتوویی تەواو بوو ✅</span>
            <span v-else-if="getProjectStatus(item) === 'inProcess'" class="text-yellow-600">هێشتا بەردەوامە ⏳</span>
            <span v-else class="text-gray-500">دەستپێ نەکراوە</span>
          </p>
          <hr v-if="item.note !== null" />
          <div v-if="item.note !== null" class="flex mt-2 font-droidkufi">
            <p class="font-bold text-primary">تێبینی:</p>
            <p>{{ item.note }}</p>
          </div>
          <hr v-if="item.stage === 'Design' && item.designer_name !== null" />
          <div v-if="item.stage === 'Design' && item.designer_name !== null" class="flex mt-2 font-droidkufi">
            <p class="font-bold text-danger">دیزاینەر:</p>
            <p>{{ item.designer_name }}</p>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

        </div>
      </div>
    </div>
  </AppLayout>

    <el-dialog class="font-droidkufi" dir="rtl" v-model="dialogFormVisible" title="فۆڕمی زیاکردنی پڕۆژە" width="70%"
             :close-on-click-modal="true"  @close="resetForm">
    <el-form :model="form" @keydown.enter="submit" label-position="left" label-width="120px">
      <div class="md:flex text-[8px] md:text-sm ">
          <el-form-item :error="ErrorForms.name" style="width: 100%;" label="ناوی پڕۆژە">
            <el-input v-model="form.name" clearable />
        </el-form-item>

        <el-form-item :error="ErrorForms.customer_name" style="width: 100%;" label="ناوی کڕیار">
          <el-input v-model="form.customer_name" clearable />
        </el-form-item>
      </div>

      

   <div class="md:flex ">
     <el-form-item :error="ErrorForms.phone" style="width: 100%;" label="ژمارەی تەلەفون">
       <el-input v-model="form.phone" type="text" dir="ltr" />
     </el-form-item>
     <el-form-item :error="ErrorForms.type_project_id" style="width: 100%;" label="جۆری پڕۆژە">
       <el-select v-model="form.type_project_id" placeholder="هەڵبژاردنی جۆری پڕۆژە" clearable>
      <el-option 
          v-for="type in data.typeProjects" 
          :key="type.id" 
          :label="type.name" 
          :value="type.id" />
  </el-select>
      
     </el-form-item>



   </div>     

   <div class="md:flex">
         <el-form-item :error="ErrorForms.address" style="width: 100%;" label="ناونیشان">
            <el-input v-model="form.address" type="text" clearable />
        </el-form-item>

        <el-form-item :error="ErrorForms.location" style="width: 100%;" label="لۆکەیشن">
            <el-input v-model="form.location" type="text" clearable />
        </el-form-item>
   </div>
<div class="md:flex w-full flex-col gap-4">
  <p class="w-[190px] font-droidkufi text-[8px] md:text-sm text-gray-500">
    زیادکردنی وێنە
  </p>

  <!-- Images -->
  <el-form-item :error="ErrorForms.images" label="وێنەکان" required>
    <div v-if="form.existing_images && form.existing_images.length" class="flex text-[8px] md:text-sm gap-3 flex-wrap">
      <div
        v-for="(img, index) in form.existing_images"
        :key="img.id"
        class="relative"
        style="width: 120px; height: 120px"
      >
        <el-image
          :src="`/storage/${img.path}`"
          :preview-src-list="form.existing_images.map(i => `/storage/${i.path}`)"
          fit="cover"
          style="width: 100%; height: 100%; border-radius: 0.5rem;"
        >
          <template #viewer-toolbar="{ actions, prev, next, reset, activeIndex }">
            <el-icon @click="prev"><Back /></el-icon>
            <el-icon @click="next"><Right /></el-icon>
            <el-icon @click="actions('zoomOut')"><ZoomOut /></el-icon>
            <el-icon @click="actions('zoomIn')"><ZoomIn /></el-icon>
            <el-icon @click="actions('clockwise')"><RefreshRight /></el-icon>
            <el-icon @click="actions('anticlockwise')"><RefreshLeft /></el-icon>
            <el-icon @click="reset"><Refresh /></el-icon>
            <el-icon @click="downloadImage(activeIndex)"><Download /></el-icon>
          </template>
        </el-image>

        <!-- Delete Button -->
        <el-button
          size="mini"
          type="danger"
          class="absolute top-0 right-0"
          @click="form.existing_images = form.existing_images.filter(i => i.id !== img.id)"
        >
          X
        </el-button>
      </div>
    </div>
    <p v-else class="text-gray-400 text-[8px] md:text-sm">وێنە نییە</p>

    <!-- Upload button -->
    <el-upload
      class="upload-demo w-full"
      drag
      multiple
      :on-change="handleImageUpload"
      :auto-upload="false"
      accept="image/*"
    >
      <el-icon class="el-icon--upload"><UploadFilled /></el-icon>
      <div class="el-upload__text text-[8px] md:text-sm">
        Drop files here or <em>click to upload</em>
      </div>
    </el-upload>
  </el-form-item>
</div>



        <el-form-item :error="ErrorForms.content" label="ناوەڕۆک" class="text-[8px] md:text-sm">
            <el-input v-model="form.content" type="textarea" clearable />
        </el-form-item>

        <el-form-item :error="ErrorForms.measurment" label="قیاس">
            <el-input v-model="form.measurment" clearable />
        </el-form-item>


     <div class="flex items-center justify-between">
<div class="w-1/2">
     <el-form-item :error="ErrorForms.xamlandn" label="خەمڵاندن">
            <el-input v-model="form.xamlandn" type="text" clearable />
        </el-form-item>
</div>
<div class="w-1/2">
     <el-form-item :error="ErrorForms.telegram_link" label="لینکی تەلەگرام">
            <el-input v-model="form.telegram_link" type="text" clearable />
        </el-form-item>
     </div>
     </div>
     
    </el-form>
      <template #footer>
                       <div class="w-full justify-end items-end flex">
                         <el-button @click="submit" type="success" plain dir="rtl" >
پاشکەوتکردن</el-button>
                       </div>
  </template>
     
  </el-dialog>

    <el-dialog
  class="font-droidkufi"
  dir="rtl"
  v-model="dialogFormVisibleShow"
  title="وردەکاری پڕۆژە"
  width="70%"
  :close-on-click-modal="true"
   @close="resetFormdata"
><div class="space-y-6">
  <!-- Row 1 -->
  <div class="md:flex gap-6">
    <div class="w-full md:w-1/2">
      <p class="text-sm font-bold text-gray-600">ناوی پڕۆژە</p>
      <p class="text-base text-gray-900">{{ dataform.name }}</p>
    </div>
    <div class="w-full md:w-1/2">
      <p class="text-sm font-bold text-gray-600">ناوی کڕیار</p>
      <p class="text-base text-gray-900">{{ dataform.customer_name }}</p>
    </div>
  </div>

  <!-- Row 2 -->
  <div class="md:flex gap-6">
    <div class="w-full md:w-1/2">
    <p class="text-sm font-bold text-gray-600">
      ژمارەی تەلەفون
      <el-button 
        type="success" 
        plain 
        circle 
        class="mr-2"
        @click="openWhatsApp"
      >
        <i class="fa-brands fa-whatsapp"></i>
      </el-button>
    </p>
    <p class="text-base text-gray-900">{{ dataform.phone }}</p>
  </div>
    <div class="w-full md:w-1/2">
      <p class="text-sm font-bold text-gray-600">جۆری پڕۆژە</p>
      <p class="text-base text-gray-900">
        {{ data.typeProjects.find(t => t.id === Number(dataform.type_project_id))?.name || "N/A" }}
      </p>
    </div>
  </div>

  <!-- Row 3 -->
  <div class="md:flex gap-6">
    <div class="w-full md:w-1/2">
      <p class="text-sm font-bold text-gray-600">ناونیشان</p>
      <p class="text-base text-gray-900">{{ dataform.address }}</p>
    </div>
    <div class="w-full md:w-1/2">
  <p class="text-sm font-bold text-gray-600">لۆکەیشن</p>
  <a
    :href="dataform.location"
    target="_blank"
    class="inline-flex items-center mt-2 px-3 py-1.5 text-sm font-medium text-white bg-primary rounded-lg shadow transition"
  >
    بینینی لەسەر ماپ
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m0 0H3m7 0v7" />
    </svg>
  </a>
</div>

  </div>

  <!-- Content -->
  <div>
    <p class="text-sm font-bold text-gray-600">ناوەڕۆک</p>
    <p class="text-base text-gray-900 whitespace-pre-line">{{ dataform.content }}</p>
  </div>

  <!-- Measurement -->
  <div>
    <p class="text-sm font-bold text-gray-600">قیاس</p>
    <p class="text-base text-gray-900">{{ dataform.measurment }}</p>
  </div>
<div>
  <p class="text-sm font-bold text-gray-600 mb-2">وێنەکان</p>
  <div
    v-if="dataform.desgin_img && dataform.desgin_img.length"
    class="flex gap-3 flex-wrap demo-image__custom-toolbar"
  >
    <div
      v-for="(img, index) in dataform.desgin_img"
      :key="index"
      class="relative group"
      style="width: 120px; height: 120px"
    >
      <el-image
        :src="`/storage/${img}`"
        :preview-src-list="dataform.desgin_img.map(i => `/storage/${i}`)"
        fit="cover"
        show-progress
        style="width: 100%; height: 100%; border-radius: 0.5rem;"
      >
        <template
          #toolbar="{ actions, prev, next, reset, activeIndex, setActiveItem }"
        >
          <el-icon @click="prev"><Back /></el-icon>
          <el-icon @click="next"><Right /></el-icon>
          <el-icon @click="setActiveItem(srcList.length - 1)">
            <DArrowRight />
          </el-icon>
          <el-icon @click="actions('zoomOut')"><ZoomOut /></el-icon>
          <el-icon
            @click="actions('zoomIn', { enableTransition: false, zoomRate: 2 })"
          >
            <ZoomIn />
          </el-icon>
          <el-icon
            @click="actions('clockwise', { rotateDeg: 180, enableTransition: false })"
          >
            <RefreshRight />
          </el-icon>
          <el-icon @click="actions('anticlockwise')"><RefreshLeft /></el-icon>
          <el-icon @click="reset"><Refresh /></el-icon>
          <el-icon @click="downloadImage(activeIndex)"><Download /></el-icon>
        </template>
      </el-image>

      <!-- Hover Button with Printer Icon -->
      <el-button
        size="mini"
        type="danger"
        class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center"
        @click="printImage(index)"
      >
      <i class="fa-solid fa-print"></i>
      </el-button>
    </div>
  </div>

  <p v-else class="text-gray-400 text-sm">وێنە نییە</p>
</div>

  <!-- Xamlandn -->
 <div class="flex items-center justify-between">
   <div>
    <p class="text-sm font-bold text-gray-600">خەمڵاندن</p>
    <p class="text-base text-gray-900">{{ dataform.xamlandn }}</p>
  </div>

   <div class="w-1/2">
  <p class="text-sm font-bold text-gray-600">لینکی تەلەگرام</p>
  <a 
    :href="dataform.telegram_link" 
    target="_blank" 
    rel="noopener noreferrer"
    class=" text-primary hover:text-blue-800"
  >
    <i class="fab fa-telegram-plane mr-2"></i>
کردنەوەی لینکی تەلەگرام  </a>
</div>

  </div>
</div>

</el-dialog>

<!-- Back to Design Modal -->
<el-dialog
  class="font-droidkufi"
  dir="rtl"
  v-model="dialogFormVisibleBackToDesign"
  title="فۆڕمی گەڕانەوە"
  width="70%"
  :close-on-click-modal="true"
  @close="resetBackToDesign"
>
  <el-form :model="form" label-position="left" label-width="120px">
    <el-form-item :error="ErrorsShowtoBack" label="تێبینی" required>
      <el-input
        v-model="formbacktodesghing.note"
        type="textarea"
        placeholder="تکایە نوتەکە بنووسە"
        rows="4"
        clearable
      />
    </el-form-item>
  </el-form>

  <template #footer>
    <div class="dialog-footer">
      <el-button @click="submitBackToDesign" type="success" plain>
       گەرانەوە
      </el-button>
    </div>
  </template>
</el-dialog>
<!-- Modal name Desghiner Add -->
<el-dialog
  class="font-droidkufi"
  dir="rtl"
  width="50%"
  v-model="dialogDesignerVisible"
  title="هەڵبژاردنی ناوی دیزاینەر"
  :close-on-click-modal="false"
>
  <el-form label-position="top">
    <el-form-item label="ناوی دیزاینەر">
      <el-select
        v-model="form.desghiner_name"
        placeholder="هەڵبژێرە"
        clearable
      >
        <el-option
          v-for="designer in form.desghiner_list"
          :key="designer.value"
          :label="designer.label"
          :value="designer.value"
        />
      </el-select>
    </el-form-item>
    
  </el-form>

  <template #footer>
    <el-button type="primary" @click="submitStage">پاشکەوتکردن</el-button>
  </template>
</el-dialog>
<!-- Modal prev stagesto back
`````````````````````````````````````````````
`````````````````````````````````````````````
````````````````````````````````````````````` 
`````````````````````````````````````````````
`````````````````````````````````````````````
`````````````````````````````````````````````
-->
<el-dialog
  class="font-droidkufi"
  dir="rtl"
  width="50%"
  v-model="dialogPrevStageVisible"
  title="گەڕانەوەی حاڵەکات"
  :close-on-click-modal="false"
>
  <el-form label-position="top">
    <el-form-item :error="ErrorsShowtoBack?.stage" label="لیستی ستەیجەکانی پێشتر">
      <el-select
        v-model="formbackstage.stage"
        placeholder="هەڵبژێرە"
        clearable
      >

        <el-option
          v-for="designer in prevStages"
          :key="designer.value"
          :label="designer.label"
          :value="designer.value"
        />
      </el-select>
    </el-form-item>
       <el-form-item :error="ErrorsShowtoBack?.note" label="تێبینی" required>
      <el-input
        v-model="formbackstage.note"
        type="textarea"
        placeholder="تکایە نوتەکە بنووسە"
        rows="4"
        clearable
      />
    </el-form-item>
  </el-form>

  <template #footer>
    <el-button type="primary" @click="BackToStageSubmit">پاشکەوتکردن</el-button>
  </template>
</el-dialog>

</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref, onMounted, reactive, onUnmounted, watch } from "vue";

import {
  Back,
  Check,
  CircleCheckFilled,
  Clock,
  Download,
  Minus,
  Refresh,
  RefreshLeft,
  RefreshRight,
  Right,
  UploadFilled,
  ZoomIn,
  ZoomOut,
} from '@element-plus/icons-vue'

import dayjs from 'dayjs';
import { router } from '@inertiajs/vue3';
import { ElIcon } from 'element-plus'

const props = defineProps({
  data: Object, // contains { stages: [...], projectsByStage: {...} }
  
});
const ErrorForms=ref({})

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
const dataform = reactive({})

// Download image function
const downloadImage = (index) => {
  const imgPath = `/storage/${dataform.desgin_img[index]}`
  const suffix = imgPath.slice(imgPath.lastIndexOf('.'))
  const filename = Date.now() + suffix

  fetch(imgPath)
    .then(res => res.blob())
    .then(blob => {
      const blobUrl = URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = blobUrl
      link.download = filename
      document.body.appendChild(link)
      link.click()
      URL.revokeObjectURL(blobUrl)
      link.remove()
    })
}
const printImage = (index) => {
  const imgSrc = `/storage/${dataform.desgin_img[index]}`; // The el-image source
  const templateSrc = '/assets/images/A4templateimg.jpg';     // Your A4 background template

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


const dialogFormVisibleBackToDesign = ref(false)

// form reactive object
const formbacktodesghing = reactive({
  note: '', // this is the required note
})

const ErrorsShowtoBack = ref('')

// reset modal
const resetBackToDesign = () => {
  dialogFormVisibleBackToDesign.value = false
  formbacktodesghing.note = ''
  ErrorsShowtoBack.value = ''
}
const currentProject = ref(null)
const dialogPrevStageVisible = ref(false);
const formbackstage = reactive({
  note: '', // this is the required note
  stage: null,
});
const prevStages = ref([]) // will hold stages for select
const stages = props.data.stages;

// mapping values → labels
const stageLabels = {
  Sale: 'وەرگرتن',
  Measurment: 'قیاسکردن',
  Design: 'دیزاین',
  Pricing: 'نرخ پێدان',
  Creative: 'دروستکردن',
  Planning: 'دانان',
  Reklam: 'ڕیکلام',
}
function BackStages(item) {
  dialogPrevStageVisible.value = true
  currentItem.value = item

  const nowIndex = stages.indexOf(item.stage)

  prevStages.value = stages
    .slice(0, nowIndex)
    .map(stage => ({
      label: stageLabels[stage] ?? stage, // use translated label
      value: stage, // keep original value
    }))

  // reset form
  formbackstage.note = ''
  formbackstage.stage = null
}


function BackToStageSubmit() {

  router.post('/projectStages/back/prev', {
    project_id: currentItem.value.project.id,
    stage: formbackstage.stage,
    note: formbackstage.note,
   
  }, {
    onSuccess: () => {
  dialogPrevStageVisible.value = false;

      ElMessage({
        message: 'ئاستی نوێ زیاد کرا 👌',
        type: 'success',
        duration: 4000,
        customClass: 'custom-confirm-box',
      });
      router.visit('/dashboard', { preserveScroll: true });
    },
    onError: (errors) => {
      ErrorsShowtoBack.value = errors;
    }
  });
}

const CompleteStatusProjectStage = (item) => { 

  ElMessageBox.confirm(
    'ئایا دڵنیایت لە تەواو کردنی ئەم پڕۆسەیە؟', // Message
    'تەواو کردن', // Title
    {
      confirmButtonText: 'بەڵێ',
      cancelButtonText: 'نەخێر',
      type: 'info',
      customClass: 'custom-confirm-box', // Optional custom class
    }
  )
    .then(() => {
      // User confirmed
      router.post('/projectStages/completePricing', {
        project_id: item.project.id,
        stage:item.stage
      }, {
        onSuccess: () => {
          ElMessage({
            message: 'ئاستی کۆتایی زانیاری زیادکرا 👌',
            type: 'success',
            duration: 4000,
                  customClass: 'custom-confirm-box', // Optional custom class

          });
          router.visit('/dashboard', { preserveScroll: true });
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

// submit modal
function submitBackToDesign() {
  if (!formbacktodesghing.note.trim()) {
    ErrorsShowtoBack.value = 'نوتەکە پێویستە'
    return
  }

router.post('/projectStages/back', {
  project_id: currentProject.value.project.id,
  note: formbacktodesghing.note,
}, {
  onSuccess: () => {
    ElMessage({
      message: 'گەڕایەوە 👌',
      type: 'success',
      duration: 4000,
            customClass: 'custom-confirm-box',

    })
    resetBackToDesign()
    router.visit('/dashboard', { preserveScroll: true })
  },
  onError: (errors) => {
    ErrorsShowtoBack.value = Object.values(errors).flat().join('<br>')
  }
})
}

const projectsByStage = props.data.projectsByStage;

const dialogFormVisible = ref(false);

const dialogFormVisibleShow = ref(false);
// Reactive object to store project data

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
      dataform.telegram_link = project.telegram_link || ""

      // Open dialog
      dialogFormVisibleShow.value = true
    },
    onError: (errors) => {
      console.error("Failed to load project:", errors)
    }
  })
}

// Reset project data (for closing dialog or clearing)
const resetFormdata = () => {
  Object.keys(dataform).forEach(key => delete dataform[key])
  dialogFormVisibleShow.value = false
}


const dialogDesignerVisible = ref(false); // dialog toggle
const selectedStage = ref(null);          // keep next stage
const currentItem = ref(null);            // keep project data

const form = reactive({
  id: null,
  name: null,
  type_project_id: null,
  customer_name: null,
  phone: '+9647',
  address: null,
  location: null,
  content: null,
  measurment: null,
  desgin_img: [],
  existing_images: [],
  xamlandn: null,
  desghiner_name: null,
  telegram_link: null,
  desghiner_list: [
    { label: 'جوتیار', value: 'جوتیار' },
    { label: 'هودا', value: 'هودا' },
    { label: 'محمد', value: 'محمد' },
  ]
});

function resetForm() {
    form.id = null;
    form.name = null;
    form.type_project_id = null;
    form.customer_name = null;
    form.phone = '+9647';
    form.address = null;
    form.location = null;
    form.content = null;
    form.measurment = null;
  form.desgin_img = null;
    form.images = null;
  form.xamlandn = null;
  form.telegram_link = null;
  ErrorForms.value = {};
  form.desgin_img = [];
  form.existing_images = [];
  form.desghiner_name = null;
}
// Stage header colors
function getStageColor(stage) {
  switch (stage) {
    case "Planning":
      return "bg-gradient-to-r from-fuchsia-800 to-fuchsia-500";
    case "Creative":
      return "bg-gradient-to-r from-fuchsia-800 to-fuchsia-500";
    case "Design":
      return "bg-gradient-to-r from-fuchsia-800 to-fuchsia-500";
    case "Measurment":
      return "bg-gradient-to-r from-red-800 to-red-500";
    case "Pricing":
      return "bg-gradient-to-r from-red-800 to-red-500";
    case "Reklam":
      return "bg-gradient-to-r from-fuchsia-800 to-fuchsia-500";
    case "Sale":
      return "bg-gradient-to-r from-fuchsia-800 to-fuchsia-500";
    default:
      return "bg-gradient-to-r from-fuchsia-800 to-fuchsia-500";
  }
}
function edit(id) {
    router.get('projects/' + id, {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (result) => {
            const project = result.props.flash.data;
            form.id = project.id;
            form.name = project.name;
            form.customer_name = project.customer_name;
            form.type_project_id = project.type_project_id;
            form.phone = project.phone;
            form.address = project.address;
            form.location = project.location;
            form.content = project.content;
            form.measurment = project.measurment;
          form.xamlandn = project.xamlandn;
            form.telegram_link = project.telegram_link;

            // Assign DB images
            form.existing_images = project.images; // [{id, path}, ...]

            dialogFormVisible.value = true;
        }
    });
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
  datefilter: props.data.datefilter
});

const loading = ref(false);
let timeoutId;

const fetchPage = () => {
  loading.value = true;

  router.get(
    "/screenTwo",
    {
      // send filters too
      ...searchQueryitem.value
    },
    {
      preserveScroll: true,
      preserveState: false,
      replace: true,
      onFinish: () => {
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      },
    }
  );

  // schedule next run after 10 minutes
  timeoutId = setTimeout(fetchPage, 600000);
};

// 🔹 Watch for changes in searchQueryitem
watch(
  searchQueryitem,
  () => {
    // cancel previous timeout if user changes filters
    clearTimeout(timeoutId);
    fetchPage();
  },
  { deep: true }
);

onMounted(() => {
  // first run only after 10 minutes
  timeoutId = setTimeout(fetchPage, 600000);
});

onUnmounted(() => {
  clearTimeout(timeoutId);
});


function submit(event) {
    event.preventDefault();

    const isUpdate = !!form.id;
    const url = isUpdate 
        ? `/projects/${form.id}/update/dashboard` 
        : '/projects/store/dashboard';

    const formData = new FormData();

    // Append normal fields
    for (const key in form) {
        if (form[key] === null || form[key] === undefined) continue;

        if (key === 'desgin_img') {
            // append new image files
            form[key].forEach((file, i) => {
                formData.append(`desgin_img[${i}]`, file);
            });
        } else if (key === 'existing_images') {
            // send IDs of existing images (those not deleted)
            formData.append('existing_images', JSON.stringify(form[key].map(i => i.id)));
        } else if (Array.isArray(form[key])) {
            form[key].forEach((val, i) => {
                formData.append(`${key}[${i}]`, val);
            });
        } else {
            formData.append(key, form[key]);
        }
    }

    router.post(url, formData, {
        forceFormData: true,
      onError: (errors) => {
                      ErrorForms.value = errors;
        },
        onSuccess: (page) => {
            ElMessage({
                message: page.props.flash?.message ||
                         (isUpdate ? 'Project updated successfully.' : 'Project created successfully.'),
                type: 'success',
              duration: 5000,
                      customClass: 'custom-confirm-box',

            });
            resetForm();
            dialogFormVisible.value = false;
            router.visit('/dashboard');
        },
    });
}


function addNextStage(item) {
  const stageOrder = [
    'Sale',
    'Measurment',
    'Design',
    'Pricing',
    'Creative',
    'Planning',
    'Reklam',
  ];

  const currentIndex = stageOrder.indexOf(item.stage);

  if (currentIndex === -1 || currentIndex === stageOrder.length - 1) {
    ElMessage({
      message: 'هیچ ئاستی داهاتوو نییە بۆ ئەم پڕۆژەیە 🚫',
      type: 'warning',
      duration: 4000,
      customClass: 'custom-confirm-box',
    });
    return;
  }

  const nextStage = stageOrder[currentIndex + 1];
  selectedStage.value = nextStage;
  currentItem.value = item;

  // special case for Design → show dialog
 if (nextStage === "Design") {
  ElMessageBox.confirm(
    `ئایا دڵنیایت لە گۆڕینی قۆناخ بۆ Design؟`,
    'گۆڕینی قۆناخ',
    {
      confirmButtonText: 'بەڵێ',
      cancelButtonText: 'نەخێر',
      type: 'info',
      customClass: 'custom-confirm-box',
    }
  ).then(() => {
    dialogDesignerVisible.value = true;
  }).catch(() => {
    ElMessage({
      message: 'پاشگەزبوونەوە',
      type: 'info',
      duration: 3000,
      customClass: 'custom-confirm-box',
    });
  });

  return;
}

  // otherwise normal confirm
  ElMessageBox.confirm(
    `ئایا دڵنیایت لە چێک کردنەوەی کارەکە بۆ قۆناخی  ${nextStage}`,
    'گۆڕینی قۆناخ',
    {
      confirmButtonText: 'بەڵێ',
      cancelButtonText: 'نەخێر',
      type: 'info',
      customClass: 'custom-confirm-box',
    }
  )
    .then(() => {
      submitStage();
    })
    .catch(() => {
      ElMessage({
        message: 'پاشگەزبوونەوە',
        type: 'info',
        duration: 3000,
        customClass: 'custom-confirm-box',
      });
    });
}

function submitStage() {
  dialogDesignerVisible.value = false;

  router.post('/projectStages', {
    project_id: currentItem.value.project.id,
    stage: selectedStage.value,
    start_time: new Date().toISOString(),
    designer_name: form.desghiner_name ?? null, // 👈 include if selected
  }, {
    onSuccess: () => {
      ElMessage({
        message: 'ئاستی نوێ زیاد کرا 👌',
        type: 'success',
        duration: 4000,
        customClass: 'custom-confirm-box',
      });
      router.visit('/dashboard', { preserveScroll: true });
    },
    onError: (errors) => {
      const errorMessages = Object.values(errors).flat().join('<br>');
      ElMessage({
        message: errorMessages,
        type: 'error',
        dangerouslyUseHTMLString: true,
        duration: 5000,
        customClass: 'custom-confirm-box',
      });
    }
  });
}



function isLastStage(stage) {
  const stageOrder = ['Sale', 'Measurement', 'Design', 'Pricing', 'Creative', 'Planning', 'Reklam'];
  return stage === stageOrder[6]; // Sale is the first in the order (last stage in lifecycle)
}

function quitStage(item) {
  ElMessageBox.confirm(
    'ئایە دڵنییایت لە تەواو کردنی ئەم پڕۆسەیە؟', // Message
    'تەواو کردن', // Title
    {
      confirmButtonText: 'بەڵێ',
      cancelButtonText: 'نەخێر',
      type: 'info',
      customClass: 'custom-confirm-box', // Optional custom class
    }
  )
    .then(() => {
      // User confirmed
      router.post('/projectStages/quit', {
        project_id: item.project.id,
        stage: item.stage,
      }, {
        onSuccess: () => {
          ElMessage({
            message: 'ئاستی کۆتایی زانیاری زیادکرا 👌',
            type: 'success',
            duration: 4000,
                  customClass: 'custom-confirm-box', // Optional custom class

          });
          router.visit('/dashboard', { preserveScroll: true });
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

//archived
function archiveProject(item) {
  ElMessageBox.confirm(
    'ئایا دڵنیایت بۆ ئەرشیفکردنی ئەم پڕۆژەیە؟',
    'ئەرشیفکردن',
    {
      confirmButtonText: 'بەڵێ',
      cancelButtonText: 'نەخێر',
      type: 'warning',
      customClass: 'custom-confirm-box',
    }
  )
    .then(() => {
      router.post('/projects/archive', {
        project_id: item.project.id,
      }, {
        onSuccess: () => {
          ElMessage({
            message: 'پڕۆژەکە بە سەرکەوتوویی ئەرشیف کرا 👌',
            type: 'success',
            duration: 4000,
                  customClass: 'custom-confirm-box', // Optional custom class

          });
          router.visit('/screenTwo', { preserveScroll: true });
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
      ElMessage({
        message: 'پاشگەزبوونەوە',
        type: 'info',
        duration: 3000,
              customClass: 'custom-confirm-box', // Optional custom class

      });
    });
}

function handleImageUpload(file, fileList) {
    form.desgin_img = fileList.map(f => f.raw); // keep raw files
}



</script>


<style scoped>
.custom-input .el-input__inner {
    border-radius: 0 !important;
    padding: 0 !important;
}

.print-container {
  display: none; /* hide normally */
}

@media print {
  body * {
    visibility: hidden;
  }
  .print-container, .print-container * {
    visibility: visible;
  }
  .print-container {
    position: absolute;
    left: 0;
    top: 0;
    display: block;
    width: 210mm;
    height: 297mm;
    margin: 0 auto;
    background: url('/assets/images/A4template.jpg') no-repeat center center;
    background-size: cover;
  }
}

</style>