<template>
  <AppLayout title="داشبۆرد">
    <template #header>
      <div class="flex items-center justify-between">
        <h4 dir="rtl" class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black pl-1 sm:pl-2">
          داشبۆرد
        </h4>
        <el-button v-if="can('write projects')" style="height: 40px;" @click="dialogFormVisible = true" type="primary"
          class="font-droidkufi mr-2 object-cover">
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
              <el-date-picker v-model="searchQueryitem.datefilter" type="daterange" range-separator="تا"
                start-placeholder="لە ڕۆژ" end-placeholder="بۆ ڕۆژ" format="YYYY-MM-DD" value-format="YYYY-MM-DD" />
            </el-form-item>
          </div>

          <!-- Stages Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8 px-4">
            <div v-for="stage in stages.filter(s => can(`read ${s}`))" :key="stage" class="flex flex-col items-center">
              <!-- Stage Header -->
              <div :class="[
                'w-full text-center py-2 rounded-sm text-white font-bold text-lg mb-6',
                getStageColor(stage)
              ]">
                {{
                  stage === 'request' ? 'داواکاری' :
                    stage === 'prepare' ? 'ئامادەکردن' :
                      stage === 'show' ? 'نیشاندان' :
                        stage === 'handling' ? 'مامەڵەکردن' :
                          stage === 'contract' ? 'گرێبەست'
                            : stage
                }}
              </div>

              <!-- Projects -->
              <div v-if="mulksByStage[stage]?.length" class="grid grid-cols-2 xl:grid-cols-2 gap-3 space-x-8">
                <div v-for="item in mulksByStage[stage].filter(i => !i.is_pricing_complete)" :key="item.id"
                  class="relative group">
                  <!-- Project Card — uses customer color when not backed, else black -->
                  <div v-if="!item.is_pricing_complete" @click="show(item.customer_id)" :style="
                    !item.backed_from_pricing
                      ? { background: item.customer?.color ?? '#22c55e' }
                      : {}
                  " :class="[
                    'relative flex items-center justify-center rounded-md text-white text-[10px] md:text-[12px] font-semibold text-center leading-tight shadow-md cursor-pointer transition-transform duration-200 hover:scale-105 group',
                    item.backed_from_pricing ? 'bg-[#64748b]' : '',
                    index === 0
                      ? 'w-36 h-24 scale-105 shadow-xl ring-2 ring-yellow-400 animate-pulse'
                      : 'w-36 h-20'
                  ]">
                    {{ item.customer.name }}

                    <!-- Note Pin -->
                    <div class="flex items-center justify-between">
                      <div v-if="item.note" class="absolute top-1 left-1">
                        <i class="fa-solid fa-pen text-white opacity-80"></i>
                      </div>
                      <div v-if="item.stage === 'Design' && item.designer_name !== null"
                        class="absolute top-2 text-black p-[2px] shadow ring-1 ring-white bg-white text-[10px] left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center font-bold">
                        {{ item.designer_name }}
                      </div>
                    </div>

                    <!-- Status Icon -->
                    <div class="absolute top-1 right-1">
                      <Check v-if="getProjectStatus(item) === 'success'" class="w-4 h-4 text-white drop-shadow" />
                      <Clock v-else-if="getProjectStatus(item) === 'inProcess'" class="w-4 h-4 text-white drop-shadow" />
                      <Minus v-else class="w-4 h-4 text-white opacity-60" />
                    </div>

                    <!-- Hover Actions -->
                    <div
                      class="absolute inset-0 flex justify-between items-center px-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                      <Edit v-if="can('edit projects')" @click.stop="edit(item.customer_id)"
                        class="w-5 h-5 text-white bg-black/40 rounded-full p-1 cursor-pointer hover:bg-black" />

                      <div v-if="item.stage !== 'request' && can('change stage')"
                        class="absolute bottom-1 left-1/2 -translate-x-1/2">
                        <ArrowRightBold @click.stop="BackStages(item)"
                          class="w-6 h-6 text-white bg-black/40 rounded-full p-1 cursor-pointer hover:bg-black" />
                      </div>

                      <ArrowLeftBold v-if="
                        stageOrder.includes(item.stage) &&
                        !isLastStage(item.stage)
                      " @click.stop="addNextStage(item)"
                        class="w-5 h-5 text-white bg-black/40 rounded-full p-1 cursor-pointer hover:bg-black" />

                      <Finished v-else-if="!item.end_time" @click.stop="quitStage(item)"
                        class="w-5 h-5 text-white bg-red-500 rounded-full p-1 cursor-pointer hover:bg-red-600" />

                      <TakeawayBox
                        v-else-if="item.end_time && item.stage !== 'Pricing' && item.stage !== 'Sale' && item.stage !== 'Promotion'"
                        @click.stop="archiveProject(item)"
                        class="w-5 h-5 text-white bg-blue-500 rounded-full p-1 cursor-pointer hover:bg-blue-600" />

                      <CircleCheck
                        v-else-if="item.stage == 'Pricing' || item.stage == 'Sale' || item.stage == 'Promotion'"
                        @click.stop="CompleteStatusProjectStage(item)"
                        class="w-5 h-5 bg-black/40 hover:scale-105 rounded-full p-1 cursor-pointer" />

                      <Check v-else class="w-5 h-5 text-green-500" />
                    </div>
                  </div>

                  <!-- Tooltip -->
                  <div
                    class="absolute z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-[3000ms] delay-0 -top-32 left-1/2 -translate-x-1/2 min-w-60 bg-white text-black text-xs rounded-lg shadow-lg p-2 pointer-events-none">
                    <p class="font-bold">{{ item.customer.name }}</p>
                    <!-- Color dot in tooltip -->
                    <p class="flex items-center gap-1">
                      <span class="inline-block w-3 h-3 rounded-full border border-gray-300"
                        :style="{ background: item.customer?.color ?? '#22c55e' }"></span>
                      ڕەنگی کڕیار
                    </p>
                    <p>دەستپێك: {{ formatDate(item.start_time) }}</p>
                    <p v-if="item.end_time">کۆتایی: {{ formatDate(item.end_time) }}</p>
                    <p>ماوە: {{ getTimeBetween(item.start_time, item.end_time) }}</p>
                    <p class="my-1">
                      <strong>حاڵەت:</strong>
                      <span v-if="getProjectStatus(item) === 'success'" class="text-green-600"> بەسەرکەوتوویی تەواو بوو ✅</span>
                      <span v-else-if="getProjectStatus(item) === 'inProcess'" class="text-yellow-600"> هێشتا بەردەوامە ⏳</span>
                      <span v-else class="text-gray-500"> دەستپێ نەکراوە</span>
                    </p>
                    <hr v-if="item.note !== null">
                    <div v-if="item.note !== null" class="flex mt-2 font-droidkufi">
                      <p class="font-bold text-primary">تێبینی:</p>
                      <p>{{ item.note }}</p>
                    </div>
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
          <div v-if="can('read projects')"
            class="mt-6 p-4 border-t border-gray-200 text-sm text-gray-600 font-droidkufi">
            <p><strong>تێبینی:</strong> ڕەنگی کارتەکان دیاریکراوە لە کاتی زیاد کردنی کڕیار</p>
            <ul class="list-disc ml-5 mt-2">
              <li><span class="text-black font-bold">ڕەش</span> → گەڕاوەتەوە لە نرخ پێدان بۆ دیزاین</li>
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

  <!-- ══════════════════════════════════════
       CREATE / EDIT CUSTOMER DIALOG
  ══════════════════════════════════════ -->
  <el-dialog class="font-droidkufi" dir="rtl" v-model="dialogFormVisible" title="فۆڕمی زیاکردنی کڕیار" width="70%"
    :close-on-click-modal="true" @close="resetForm">
    <el-form :model="form" @keydown.enter="submit" label-position="left" label-width="120px">

      <div class="md:flex text-[8px] md:text-sm">
        <el-form-item :error="ErrorForms.name" style="width: 100%;" label="ناوی کڕیار">
          <el-input v-model="form.name" clearable />
        </el-form-item>
        <el-form-item :error="ErrorForms.phone" style="width: 100%;" label="ژمارەی تەلەفون">
          <el-input v-model="form.phone" type="text" dir="ltr" />
        </el-form-item>
      </div>

      <div>
        <el-form-item :error="ErrorForms.type_project_id" style="width: 100%;" label="جۆری موڵکەکەی">
          <el-select filterable multiple v-model="form.type_project_id" placeholder="هەڵبژاردنی جۆری موڵکەکەی" clearable>
            <el-option v-for="type in data.typeProjects" :key="type.id" :label="type.name" :value="type.id" />
          </el-select>
        </el-form-item>

        <el-form-item :error="ErrorForms.location_id" style="width: 100%;" label="ناونیشانی موڵکەکەی">
          <el-select filterable multiple v-model="form.location_id" placeholder="هەڵبژاردنی ناونیشانی موڵک" clearable>
            <el-option v-for="type in data.locations" :key="type.id" :label="type.name" :value="type.id" />
          </el-select>
        </el-form-item>
      </div>

      <div class="md:flex">
        <el-form-item :error="ErrorForms.price_one" style="width: 100%;" label="نرخی سەرەتا">
          <el-input v-model="form.price_one" type="text" clearable />
        </el-form-item>
        <el-form-item :error="ErrorForms.price_two" style="width: 100%;" label="نرخی کۆتایی">
          <el-input v-model="form.price_two" type="text" clearable />
        </el-form-item>
      </div>

      <el-form-item :error="ErrorForms.note" label="تێبینی" class="text-[8px] md:text-sm">
        <el-input v-model="form.note" type="textarea" clearable />
      </el-form-item>

      <!-- ══ COLOR PICKER ══ -->
      <el-form-item label="ڕەنگی کڕیار" class="text-[8px] md:text-sm">
        <div class="flex flex-col gap-3 w-full">

          <!-- Preset color swatches -->
          <div class="flex flex-wrap gap-2">
            <button
              v-for="preset in colorPresets"
              :key="preset.value"
              type="button"
              @click="form.color = preset.value"
              :title="preset.label"
              :style="{ background: preset.value }"
              :class="[
                'w-8 h-8 rounded-full border-2 transition-all duration-150 cursor-pointer shadow-sm hover:scale-110',
                form.color === preset.value
                  ? 'border-gray-800 scale-125 ring-2 ring-offset-1 ring-gray-500'
                  : 'border-white'
              ]"
            ></button>
          </div>

          <!-- Selected color preview + hex input -->
          <div class="flex items-center gap-3">
            <span
              class="inline-block w-10 h-10 rounded-lg border border-gray-300 shadow-inner"
              :style="{ background: form.color }"
            ></span>
            <span class="text-sm text-gray-600">ڕەنگی دیاریکراو:</span>
            <code class="text-sm font-mono bg-gray-100 px-2 py-1 rounded">{{ form.color }}</code>
          </div>

        </div>
      </el-form-item>
      <!-- ══ END COLOR PICKER ══ -->

    </el-form>
    <template #footer>
      <div class="w-full justify-end items-end flex">
        <el-button @click="submit" type="success" plain dir="rtl">
          پاشکەوتکردن
        </el-button>
      </div>
    </template>
  </el-dialog>

  <!-- SHOW CUSTOMER DIALOG (unchanged structure, kept intact) -->
  <el-dialog class="font-droidkufi" dir="rtl" v-model="dialogFormVisibleShow" title="وردەکاری کڕیار" width="70%"
    :close-on-click-modal="true" @close="resetFormdata">
    <div class="space-y-6">
      <div class="md:flex gap-6">
        <div class="w-full md:w-1/2">
          <p class="text-sm font-bold text-gray-600">ناوی کڕیار</p>
          <p class="text-base text-gray-900 flex items-center gap-2">
            <span class="inline-block w-4 h-4 rounded-full border border-gray-300 shadow-sm"
              :style="{ background: dataform.color ?? '#22c55e' }"></span>
            {{ dataform.name }}
          </p>
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

      <div class="md:flex gap-6">
        <div class="w-full md:w-1/2">
          <p class="text-sm font-bold text-gray-600">جۆری موڵک</p>
          <p class="text-base text-gray-900">{{ dataform.type_projects?.map(t => t.name).join('، ') }}</p>
        </div>
        <div class="w-full md:w-1/2">
          <p class="text-sm font-bold text-gray-600">ناونیشان</p>
          <p class="text-base text-gray-900">{{ dataform.locations?.map(l => l.name).join('، ') }}</p>
        </div>
      </div>

      <div class="md:flex items-center justify-between">
        <div class="md:w-1/2">
          <p class="text-sm font-bold text-gray-600">نرخی سەرەتا</p>
          <p class="text-base text-danger whitespace-pre-line">{{ commaNumber(dataform.price_one * 1) }} $</p>
        </div>
        <div class="md:w-1/2">
          <p class="text-sm font-bold text-gray-600">نرخی کۆتایی</p>
          <p class="text-base text-danger whitespace-pre-line">{{ commaNumber(dataform.price_two * 1) }} $</p>
        </div>
      </div>

      <div class="flex items-center justify-between mt-4">
        <div>
          <p class="text-sm font-bold text-gray-600">تێبینی</p>
          <p class="text-base text-gray-900">{{ dataform.note }}</p>
        </div>
      </div>

      <div v-if="dataform.mulks && dataform.mulks.length" class="mb-2">
        <p class="text-base font-bold text-gray-700">موڵکی هەڵبژێردراو</p>
      </div>

      <div v-if="dataform.mulks?.length" class="space-y-8">
        <div v-for="(mulk, mulkIndex) in dataform.mulks" :key="mulk.id"
          class="bg-white rounded-2xl border shadow-sm hover:shadow-xl transition overflow-hidden">
          <div class="flex items-center gap-2 p-3">
            <div class="bg-white px-4 py-2 rounded-xl shadow text-danger font-bold text-lg">
              💰 {{ commaNumber(mulk.price) }}
            </div>
            <el-button type="danger" circle plain @click="removeMulk(mulk)">
              <i class="fa-solid fa-xmark"></i>
            </el-button>
          </div>

          <div class="bg-gradient-to-r from-gray-100 to-gray-50 p-5 flex justify-between items-center">
            <div>
              <h3 class="text-xl font-extrabold text-gray-800 flex items-center gap-2">🏠 {{ mulk.name }}</h3>
              <p class="text-sm text-gray-500 mt-1">📍 {{ mulk.location?.name ?? '—' }}</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-xl shadow text-danger font-bold text-lg">
              💰 {{ commaNumber(mulk.price) }}
            </div>
          </div>

          <div class="p-5 space-y-5">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
              <div class="fact-box"><span>📐</span><p>{{ mulk.Rwbar }}</p><small>ڕوووبەر</small></div>
              <div class="fact-box"><span>🏷</span><p>{{ mulk.type_project?.name ?? '—' }}</p><small>جۆری موڵک</small></div>
              <div class="fact-box"><span>📍</span><p>{{ mulk.location?.name ?? '—' }}</p><small>شوێن</small></div>
              <div class="fact-box">
                <span>📞</span>
                <p>{{ mulk.phone }}</p>
                <small class="flex justify-center gap-2">
                  تەلەفون
                  <el-button type="success" plain circle size="small" @click="openWhatsAppmulks(mulk.phone)">
                    <i class="fa-brands fa-whatsapp"></i>
                  </el-button>
                </small>
              </div>
            </div>

            <div v-if="mulk.emara || mulk.qat || mulk.zhmarai_shwqa" class="flex flex-wrap gap-3">
              <span v-if="mulk.emara" class="badge">🏢 عیمارە: {{ mulk.emara }}</span>
              <span v-if="mulk.qat" class="badge">🏗 قات: {{ mulk.qat }}</span>
              <span v-if="mulk.zhmarai_shwqa" class="badge">🚪 شووقە: {{ mulk.zhmarai_shwqa }}</span>
            </div>

            <div v-if="mulk.facebook_link" class="flex flex-wrap gap-3">
              <a :href="mulk.facebook_link" target="_blank" rel="noopener noreferrer"
                class="badge flex items-center gap-1 bg-blue-100 text-blue-700 hover:bg-blue-200 transition cursor-pointer">
                📘 لینکی فەیسبووک
              </a>
            </div>

            <div class="badge md:col-span-2">📝 تێبینی: {{ mulk.note }}</div>
            <div class="badge">
              📍 شوێنی نەخشە:
              <a :href="mulk.link_location" target="_blank" class="text-blue-600 underline">بینینی شوێن</a>
            </div>

            <div>
              <p class="text-sm font-bold text-gray-600 mb-3">وێنەکان</p>
              <div v-if="mulk.images?.length" class="flex gap-3 flex-wrap">
                <div v-for="(img, imgIndex) in mulk.images" :key="img.id" class="relative group"
                  style="width: 120px; height: 120px">
                  <el-image :src="`/storage/${img.path}`" :preview-src-list="mulk.images.map(i => `/storage/${i.path}`)"
                    fit="cover" show-progress style="width: 100%; height: 100%; border-radius: 0.5rem;">
                  </el-image>
                  <el-button size="mini" type="danger"
                    class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center"
                    @click="printImage(mulkIndex, imgIndex)">
                    <i class="fa-solid fa-print"></i>
                  </el-button>
                </div>
              </div>
              <p v-else class="text-gray-400 text-sm">وێنە نییە</p>
            </div>
          </div>
        </div>
      </div>
      <p v-else class="text-center text-gray-400 text-sm">موڵک نییە</p>
    </div>
  </el-dialog>

  <!-- Back Stage Dialog -->
  <el-dialog class="font-droidkufi" dir="rtl" v-model="dialogFormVisibleBackToDesign" title="فۆڕمی گەڕانەوە" width="70%"
    :close-on-click-modal="true" @close="resetBackToDesign">
    <el-form :model="form" label-position="left" label-width="120px">
      <el-form-item :error="ErrorsShowtoBack" label="تێبینی" required>
        <el-input v-model="formbacktodesghing.note" type="textarea" placeholder="تکایە نوتەکە بنووسە" rows="4" clearable />
      </el-form-item>
    </el-form>
    <template #footer>
      <el-button @click="submitBackToDesign" type="success" plain>گەرانەوە</el-button>
    </template>
  </el-dialog>

  <!-- Mulk Select Dialog -->
  <el-dialog class="font-droidkufi" dir="rtl" width="70%" v-model="dialogDesignerVisible" title="هەڵبژاردنی مووڵکەکان"
    :close-on-click-modal="false">
    <el-form label-position="top" class="space-y-4">
      <div class="p-4">
        <h3 class="text-sm font-bold text-gray-700 mb-3">فلتەرەکان</h3>
        <el-form-item>
          <el-checkbox v-model="filterByPrice">فلتەر بەپێی نرخ</el-checkbox>
        </el-form-item>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <el-form-item label="فلتەر بەپێی جۆری موڵک">
            <el-select v-model="selectedTypeProjects" multiple clearable filterable placeholder="جۆری موڵک هەڵبژێرە" class="w-full">
              <el-option v-for="type in props.data.typeProjects" :key="type.id" :label="type.name" :value="type.id" />
            </el-select>
          </el-form-item>
          <el-form-item label="فلتەر بەپێی ناونیشان">
            <el-select v-model="selectedLocations" multiple clearable filterable placeholder="ناونیشان هەڵبژێرە" class="w-full">
              <el-option v-for="location in props.data.locations" :key="location.id" :label="location.name" :value="location.id" />
            </el-select>
          </el-form-item>
        </div>
      </div>
      <div class="p-4 border-t-2">
        <div class="flex justify-between items-center mb-2">
          <h3 class="text-sm font-bold text-gray-700">مووڵکەکان</h3>
          <span class="text-xs text-gray-500">{{ filteredMulks.length }} مووڵک دۆزرایەوە</span>
        </div>
        <el-form-item>
          <el-select v-model="form.mulks" multiple clearable filterable placeholder="مووڵکەکان هەڵبژێرە" class="w-full">
            <el-option v-for="mulk in filteredMulks" :key="mulk.id" :value="mulk.id" :label="mulk.name">
              <div class="flex flex-col sm:flex-row sm:justify-between gap-2">
                <div class="flex flex-wrap gap-1 text-xs sm:text-sm text-gray-600">
                  <span v-if="mulk.price">💰 {{ mulk.price }}$</span>
                  <span v-if="mulk.Rwbar">📐 {{ mulk.Rwbar }}</span>
                  <span>🏷 {{ mulk.type_project?.name ?? '—' }}</span>
                  <span v-if="mulk.location?.name">{{ '📍 ' + mulk.location.name }}</span>
                </div>
                <div class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ mulk.name }}</div>
              </div>
            </el-option>
          </el-select>
        </el-form-item>
      </div>
    </el-form>
    <template #footer>
      <div class="flex justify-end gap-2">
        <el-button @click="dialogDesignerVisible = false">داخستن</el-button>
        <el-button type="primary" @click="submitStage">پاشکەوتکردن</el-button>
      </div>
    </template>
  </el-dialog>

  <!-- Prev Stage Dialog -->
  <el-dialog class="font-droidkufi" dir="rtl" width="50%" v-model="dialogPrevStageVisible" title="گەڕانەوەی حاڵەکات"
    :close-on-click-modal="false">
    <el-form label-position="top">
      <el-form-item :error="ErrorsShowtoBack?.stage" label="لیستی ستەیجەکانی پێشتر">
        <el-select v-model="formbackstage.stage" placeholder="هەڵبژێرە" clearable>
          <el-option v-for="designer in prevStages" :key="designer.value" :label="designer.label" :value="designer.value" />
        </el-select>
      </el-form-item>
      <el-form-item :error="ErrorsShowtoBack?.note" label="تێبینی" required>
        <el-input v-model="formbackstage.note" type="textarea" placeholder="تکایە نوتەکە بنووسە" rows="4" clearable />
      </el-form-item>
    </el-form>
    <template #footer>
      <el-button type="primary" @click="BackToStageSubmit">پاشکەوتکردن</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref, onMounted, reactive, onUnmounted, watch, computed } from "vue";
import {
  ArrowLeftBold, ArrowRightBold, Back, Check, CircleCheck, CircleCheckFilled,
  Clock, Download, Edit, Finished, Minus, Refresh, RefreshLeft, RefreshRight,
  Right, TakeawayBox, ZoomIn, ZoomOut,
} from '@element-plus/icons-vue';
import { router } from '@inertiajs/vue3';
import { ElIcon } from 'element-plus';
import commaNumber from 'comma-number';

// ─── Props ───────────────────────────────────────────
const props = defineProps({ data: Object });
const ErrorForms = ref({});
const dataform = reactive({});

// ─── Preset colors (the palette) ─────────────────────
const colorPresets = [
  { label: 'سەوز',        value: '#22c55e' },
  { label: 'سەوزی تاریک', value: '#16a34a' },
  { label: 'شین',         value: '#3b82f6' },
  { label: 'شینی تاریک',  value: '#1d4ed8' },
  { label: 'سور',         value: '#ef4444' },
  { label: 'سوری تاریک',  value: '#b91c1c' },
  { label: 'نارنجی',      value: '#f97316' },
  { label: 'زەرد',        value: '#eab308' },
  { label: 'بنەفشەیی',    value: '#a855f7' },
  { label: 'پەمەیی',      value: '#ec4899' },
  { label: 'فیرووزەیی',   value: '#06b6d4' },
  { label: 'خۆرئاوابوو',  value: '#64748b' },
  { label: 'قاوەیی',      value: '#92400e' },
  { label: 'ڕەش',         value: '#111827' },
];

// ─── WhatsApp helpers ─────────────────────────────────
function openWhatsApp() {
  let phone = dataform.phone.replace(/\D/g, '');
  if (phone.startsWith('0')) phone = phone.slice(1);
  if (!phone.startsWith('964')) phone = '964' + phone;
  window.open(`https://wa.me/${phone}`, '_blank');
}
const openWhatsAppmulks = (phone) => {
  if (!phone) return;
  phone = phone.replace(/\D/g, '');
  if (phone.startsWith('0')) phone = phone.slice(1);
  if (!phone.startsWith('964')) phone = '964' + phone;
  window.open(`https://wa.me/${phone}`, '_blank');
};

// ─── Print image ──────────────────────────────────────
const printImage = (mulkIndex, imgIndex) => {
  const mulk = dataform.mulks[mulkIndex];
  if (!mulk?.images?.[imgIndex]) return;
  const imgSrc = `/storage/${mulk.images[imgIndex].path}`;
  const templateSrc = '/assets/images/A4templateimg.jpg';
  const printWindow = window.open('', '_blank', 'width=900,height=1200');
  printWindow.document.write(`
    <html><head><title>Print Image</title>
    <style>
      @page{size:A4;margin:0}
      body{margin:0;padding:0;width:210mm;height:297mm;
        background-image:url('${templateSrc}');background-size:cover;background-position:center;
        display:flex;justify-content:center;align-items:center}
      img{max-width:180mm;max-height:250mm;object-fit:contain}
    </style></head>
    <body><img src="${imgSrc}" /></body></html>`);
  printWindow.document.close();
  printWindow.focus();
  printWindow.onload = () => { printWindow.print(); printWindow.close(); };
};

// ─── Back-to-design state ─────────────────────────────
const dialogFormVisibleBackToDesign = ref(false);
const formbacktodesghing = reactive({ note: '' });
const ErrorsShowtoBack = ref('');
const currentProject = ref(null);

const resetBackToDesign = () => {
  dialogFormVisibleBackToDesign.value = false;
  formbacktodesghing.note = '';
  ErrorsShowtoBack.value = '';
};

// ─── Prev-stage back state ────────────────────────────
const currentItem = ref(null);
const dialogPrevStageVisible = ref(false);
const formbackstage = reactive({ note: '', stage: null });
const prevStages = ref([]);
const stages = props.data.stages;

const stageLabels = {
  request: 'داواکاری',
  prepare: 'ئامادەکردن',
  show: 'نیشاندان',
  handling: 'مامەڵەکردن',
  contract: 'گرێبەست',
};

function BackStages(item) {
  dialogPrevStageVisible.value = true;
  currentItem.value = item;
  const nowIndex = stages.indexOf(item.stage);
  prevStages.value = stages.slice(0, nowIndex).map(s => ({ label: stageLabels[s] ?? s, value: s }));
  formbackstage.note = '';
  formbackstage.stage = null;
}

function BackToStageSubmit() {
  router.post('/projectStages/back/prev', {
    customer_id: currentItem.value.customer.id,
    stage: formbackstage.stage,
    note: formbackstage.note,
  }, {
    onSuccess: () => {
      dialogPrevStageVisible.value = false;
      ElMessage({ message: 'ئاستی نوێ زیاد کرا 👌', type: 'success', duration: 4000, customClass: 'custom-confirm-box' });
      router.visit('/dashboard', { preserveScroll: true });
    },
    onError: (errors) => { ErrorsShowtoBack.value = errors; },
  });
}

// ─── Remove mulk ─────────────────────────────────────
function removeMulk(mulk) {
  ElMessageBox.confirm('دڵنیایت لە سڕینەوەی ئەم موڵکە؟', 'سڕینەوە', {
    confirmButtonText: 'بەڵێ', cancelButtonText: 'نەخێر', type: 'warning', customClass: 'custom-confirm-box',
  }).then(() => {
    router.delete(route('customers.mulks.detach', [mulk.pivot.customer_id, mulk.id]), {
      preserveScroll: true,
      onSuccess: () => {
        ElMessage.success('سڕایەوە بە سەرکەوتوویی');
        router.visit('/dashboard', { preserveScroll: true });
      },
    });
  });
}

// ─── Complete pricing ─────────────────────────────────
const CompleteStatusProjectStage = (item) => {
  ElMessageBox.confirm('ئایا دڵنیایت لە تەواو کردنی ئەم پڕۆسەیە؟', 'تەواو کردن', {
    confirmButtonText: 'بەڵێ', cancelButtonText: 'نەخێر', type: 'info', customClass: 'custom-confirm-box',
  }).then(() => {
    router.post('/projectStages/completePricing', { project_id: item.project.id, stage: item.stage }, {
      onSuccess: () => {
        ElMessage({ message: 'ئاستی کۆتایی زانیاری زیادکرا 👌', type: 'success', duration: 4000, customClass: 'custom-confirm-box' });
        router.visit('/dashboard', { preserveScroll: true });
      },
      onError: (errors) => {
        ElMessage({ message: Object.values(errors).flat().join('<br>'), type: 'error', dangerouslyUseHTMLString: true, duration: 5000, customClass: 'custom-confirm-box' });
      },
    });
  }).catch(() => {
    ElMessage({ message: 'پاشگەزبوونەوە', type: 'info', duration: 3000, customClass: 'custom-confirm-box' });
  });
};

// ─── Submit back to design ────────────────────────────
function submitBackToDesign() {
  if (!formbacktodesghing.note.trim()) { ErrorsShowtoBack.value = 'نوتەکە پێویستە'; return; }
  router.post('/projectStages/back', { project_id: currentProject.value.project.id, note: formbacktodesghing.note }, {
    onSuccess: () => {
      ElMessage({ message: 'گەڕایەوە 👌', type: 'success', duration: 4000, customClass: 'custom-confirm-box' });
      resetBackToDesign();
      router.visit('/dashboard', { preserveScroll: true });
    },
    onError: (errors) => { ErrorsShowtoBack.value = Object.values(errors).flat().join('<br>'); },
  });
}

// ─── Computed mulks by stage ──────────────────────────
const mulksByStage = computed(() => {
  const data = props.data?.mulksByStage ?? {};
  return Object.fromEntries(Object.entries(data).map(([stage, items]) => [stage, items ?? []]));
});

// ─── Dialog state ─────────────────────────────────────
const dialogFormVisible = ref(false);
const dialogFormVisibleShow = ref(false);

// ─── Show customer ─────────────────────────────────────
function show(id) {
  router.get(`customers/${id}`, {}, {
    preserveState: true, preserveScroll: true,
    onSuccess: (result) => {
      const mulk = result.props.flash?.data;
      if (!mulk) return;
      Object.keys(dataform).forEach(k => delete dataform[k]);
      Object.assign(dataform, mulk);
      dataform.type_projects = mulk.type_projects || [];
      dataform.locations = mulk.locations || [];
      dataform.desgin_img = mulk.images?.map(img => img.path) || [];
      dataform.type_project_name = mulk.type_project?.name || '';
      dataform.location_name = mulk.location?.name || '';
      dataform.name = mulk.name ?? '';
      dataform.phone = mulk.phone ?? '';
      dataform.note = mulk.note ?? '';
      dataform.price = mulk.price ?? '';
      dataform.Rwbar = mulk.Rwbar ?? '';
      dataform.color = mulk.color ?? '#22c55e';
      dialogFormVisibleShow.value = true;
    },
    onError: (errors) => console.error('Failed to load:', errors),
  });
}

const resetFormdata = () => {
  Object.keys(dataform).forEach(key => delete dataform[key]);
  dialogFormVisibleShow.value = false;
};

// ─── Mulk select dialog ────────────────────────────────
const dialogDesignerVisible = ref(false);
const dialogPromotionCreativeVisible = ref(false);
const selectedStage = ref(null);

// ─── Form ──────────────────────────────────────────────
const form = reactive({
  id: null,
  name: null,
  phone: '+9647',
  type_project_id: [],
  location_id: [],
  price_one: null,
  price_two: null,
  note: null,
  stages: 'request',
  mulks: [],
  color: '#22c55e',   // ✅ default green
});

function resetForm() {
  form.id = null;
  form.name = null;
  form.phone = '+9647';
  form.type_project_id = [];
  form.location_id = [];
  form.price_one = null;
  form.price_two = null;
  form.note = null;
  form.mulks = [];
  form.stages = 'request';
  form.color = '#22c55e';  // ✅ reset to default green
}

// ─── Stage header colors ───────────────────────────────
function getStageColor(stage) {
  switch (stage) {
    case 'handling': return 'bg-gradient-to-r from-red-800 to-red-500';
    case 'contract': return 'bg-gradient-to-r from-red-800 to-red-500';
    default:         return 'bg-gradient-to-r from-fuchsia-800 to-fuchsia-500';
  }
}

// ─── Edit ──────────────────────────────────────────────
function edit(id) {
  router.get(`customers/${id}`, {}, {
    preserveState: true, preserveScroll: true,
    onSuccess: (result) => {
      const customer = result.props.flash?.data;
      if (!customer) return;
      Object.keys(form).forEach(k => delete form[k]);
      Object.assign(form, customer);
      form.type_project_id = customer.type_projects?.map(tp => tp.id) || [];
      form.location_id = customer.locations?.map(loc => loc.id) || [];
      form.name = customer.name ?? '';
      form.phone = customer.phone ?? '+9647';
      form.note = customer.note ?? '';
      form.price_one = customer.price_one ?? '';
      form.price_two = customer.price_two ?? '';
      form.mulks = customer.mulks?.map(mulk => mulk.id) || [];
      form.color = customer.color ?? '#22c55e';  // ✅ load saved color
      dialogFormVisible.value = true;
    },
  });
}

// ─── Helpers ───────────────────────────────────────────
function getTimeBetween(start, end = null) {
  if (!start) return '0 چرکە';
  const diffMs = (end ? new Date(end) : new Date()) - new Date(start);
  const days = Math.floor(diffMs / 86400000);
  const hours = Math.floor((diffMs / 3600000) % 24);
  const minutes = Math.floor((diffMs / 60000) % 60);
  return `${days} ڕۆژ - ${hours} س - ${minutes} د`;
}

function formatDate(dateStr) {
  if (!dateStr) return 'N/A';
  return new Date(dateStr).toLocaleString('ckb-IQ', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function getProjectStatus(item) {
  if (!item.start_time) return 'pending';
  return item.end_time ? 'success' : 'inProcess';
}

// ─── Date filter & auto-refresh ───────────────────────
const searchQueryitem = ref({ datefilter: props.data.datefilter });
const loading = ref(false);
let timeoutId;

const fetchPage = () => {
  loading.value = true;
  router.get('/dashboard', { ...searchQueryitem.value }, {
    preserveScroll: true, preserveState: false, replace: true,
    onFinish: () => { loading.value = false; },
    onError: () => { loading.value = false; },
  });
  timeoutId = setTimeout(fetchPage, 600000);
};

watch(searchQueryitem, () => { clearTimeout(timeoutId); fetchPage(); }, { deep: true });
onMounted(() => { timeoutId = setTimeout(fetchPage, 600000); });
onUnmounted(() => { clearTimeout(timeoutId); });

// ─── Submit form ───────────────────────────────────────
function submit(event) {
  event.preventDefault();
  const isUpdate = !!form.id;
  const url = isUpdate ? `/customers/${form.id}/update/dashboard` : '/customers/store/dashboard';
  const formData = new FormData();

  for (const key in form) {
    if (form[key] === null || form[key] === undefined) continue;
    if (key === 'desgin_img') {
      form[key].forEach((file, i) => formData.append(`desgin_img[${i}]`, file));
    } else if (key === 'existing_images') {
      formData.append('existing_images', JSON.stringify(form[key].map(i => i.id)));
    } else if (Array.isArray(form[key])) {
      form[key].forEach((val, i) => formData.append(`${key}[${i}]`, val));
    } else {
      formData.append(key, form[key]);
    }
  }

  router.post(url, formData, {
    forceFormData: true,
    onError: (errors) => { ErrorForms.value = errors; },
    onSuccess: (page) => {
      ElMessage({
        message: page.props.flash?.message || (isUpdate ? 'نوێ کرایەوە' : 'زیادکرا'),
        type: 'success', duration: 5000, customClass: 'custom-confirm-box',
      });
      resetForm();
      dialogFormVisible.value = false;
      router.visit('/dashboard');
    },
  });
}

// ─── Mulk filters ─────────────────────────────────────
const filterByPrice = ref(false);
const selectedTypeProjects = ref([]);
const selectedLocations = ref([]);

const filteredMulks = computed(() => {
  if (!currentItem.value?.customer) return [];
  const customer = currentItem.value.customer;
  const min = customer.price_one ? Number(customer.price_one) : null;
  const max = customer.price_two ? Number(customer.price_two) : null;
  const selectedMulkIds = (form.mulks || []).map(Number);
  const selectedTypes = selectedTypeProjects.value.map(Number);
  const selectedLocs = selectedLocations.value.map(Number);
  const mulks = Array.isArray(props.data?.mulks) ? props.data.mulks : [];

  return mulks.filter((mulk) => {
    if (selectedMulkIds.includes(Number(mulk.id))) return true;
    const price = Number(mulk.price);
    const priceMatch = !filterByPrice.value || ((!min || price >= min) && (!max || price <= max));
    const typeMatch = selectedTypes.length === 0 || selectedTypes.includes(Number(mulk.type_project_id));
    const locationMatch = selectedLocs.length === 0 || selectedLocs.includes(Number(mulk.location_id));
    return priceMatch && typeMatch && locationMatch;
  });
});

// ─── Stage order ───────────────────────────────────────
const stageOrder = ['request', 'prepare', 'show', 'handling', 'contract'];

function isLastStage(stage) {
  return stageOrder.indexOf(stage) === stageOrder.length - 1;
}


function addNextStage(item) {
  const currentIndex = stageOrder.indexOf(item.stage);
  if (currentIndex === -1 || currentIndex === stageOrder.length - 1) {
    ElMessage({ message: 'هیچ ئاستی داهاتوو نییە 🚫', type: 'warning', duration: 4000, customClass: 'custom-confirm-box' });
    return;
  }
 
  const nextStage = stageOrder[currentIndex + 1];
  selectedStage.value = nextStage;
  currentItem.value = item;
 
  if (['prepare', 'show', 'handling'].includes(nextStage)) {
    form.mulks = item.customer.mulks?.map(m => m.id) || [];
 
    // ✅ Pre-fill filters from the customer's saved preferences
    selectedTypeProjects.value = item.customer.type_projects?.map(t => t.id) || [];
    selectedLocations.value    = item.customer.locations?.map(l => l.id)     || [];
 
    ElMessageBox.confirm(`ئایا دڵنیایت لە گۆڕینی قۆناخ بۆ ${nextStage}؟`, 'گۆڕینی قۆناخ', {
      confirmButtonText: 'بەڵێ', cancelButtonText: 'نەخێر', type: 'info',
      customClass: 'custom-confirm-box', distinguishCancelAndClose: true,
    }).then(() => {
      dialogDesignerVisible.value = true;
    }).catch(action => {
      if (action === 'cancel') ElMessage({ message: 'پاشگەزبوونەوە', type: 'info', duration: 3000, customClass: 'custom-confirm-box' });
    });
    return;
  }
 
  ElMessageBox.confirm(`ئایا دڵنیایت لە چێک کردنەوەی کارەکە بۆ قۆناخی ${nextStage}`, 'گۆڕینی قۆناخ', {
    confirmButtonText: 'بەڵێ', cancelButtonText: 'نەخێر', type: 'info',
    customClass: 'custom-confirm-box', distinguishCancelAndClose: true,
  }).then(() => {
    submitStage();
  }).catch((action) => {
    if (action === 'cancel') ElMessage({ message: 'پاشگەزبوونەوە', type: 'info', duration: 3000, customClass: 'custom-confirm-box' });
  });
}

function submitStage() {
  dialogDesignerVisible.value = false;
  if (selectedStage.value === 'prepare' && (!form.mulks || form.mulks.length === 0)) {
    ElMessage({ message: 'تکایە موڵکەکان هەڵبژێرە! ⚠️', type: 'error', duration: 4000, customClass: 'custom-confirm-box' });
    return;
  }
  router.post('/projectStages', {
    customer_id: currentItem.value.customer.id,
    stage: selectedStage.value,
    start_time: new Date().toISOString(),
    note: form.note || null,
    mulks: form.mulks || [],
  }, {
    onSuccess: () => {
      ElMessage({ message: 'ئاستی نوێ زیاد کرا 👌', type: 'success', duration: 4000, customClass: 'custom-confirm-box' });
      router.visit('/dashboard', { preserveScroll: true });
    },
    onError: (errors) => {
      ElMessage({ message: Object.values(errors).flat().join('<br>'), type: 'error', dangerouslyUseHTMLString: true, duration: 5000, customClass: 'custom-confirm-box' });
    },
  });
}

function quitStage(item) {
  ElMessageBox.confirm('ئایە دڵنییایت لە تەواو کردنی ئەم پڕۆسەیە؟', 'تەواو کردن', {
    confirmButtonText: 'بەڵێ', cancelButtonText: 'نەخێر', type: 'info', customClass: 'custom-confirm-box',
  }).then(() => {
    router.post('/projectStages/quit', { customer_id: item.customer.id, stage: item.stage }, {
      onSuccess: () => {
        ElMessage({ message: 'ئاستی کۆتایی زانیاری زیادکرا 👌', type: 'success', duration: 4000, customClass: 'custom-confirm-box' });
        router.visit('/dashboard', { preserveScroll: true });
      },
      onError: (errors) => {
        ElMessage({ message: Object.values(errors).flat().join('<br>'), type: 'error', dangerouslyUseHTMLString: true, duration: 5000, customClass: 'custom-confirm-box' });
      },
    });
  }).catch(() => {
    ElMessage({ message: 'پاشگەزبوونەوە', type: 'info', duration: 3000, customClass: 'custom-confirm-box' });
  });
}

function archiveProject(item) {
  ElMessageBox.confirm('ئایا دڵنیایت بۆ ئەرشیفکردنی ئەم کڕیاریە + ئەم موڵکە؟', 'ئەرشیفکردن', {
    confirmButtonText: 'بەڵێ', cancelButtonText: 'نەخێر', type: 'warning', customClass: 'custom-confirm-box',
  }).then(() => {
    router.post('/projects/archivedwithmulks', { customer_id: item.customer.id }, {
      onSuccess: () => {
        ElMessage({ message: 'کڕیارکە بە سەرکەوتوویی ئەرشیف کرا 👌', type: 'success', duration: 4000, customClass: 'custom-confirm-box' });
        router.visit('/dashboard', { preserveScroll: true });
      },
      onError: (errors) => {
        ElMessage({ message: Object.values(errors).flat().join('<br>'), type: 'error', dangerouslyUseHTMLString: true, duration: 5000, customClass: 'custom-confirm-box' });
      },
    });
  }).catch(() => {
    ElMessage({ message: 'پاشگەزبوونەوە', type: 'info', duration: 3000, customClass: 'custom-confirm-box' });
  });
}
</script>

<style scoped>
@media print {
  body * { visibility: hidden; }
  .print-container, .print-container * { visibility: visible; }
  .print-container {
    position: absolute; left: 0; top: 0; display: block;
    width: 210mm; height: 297mm; margin: 0 auto;
    background: url('/assets/images/A4template.jpg') no-repeat center center;
    background-size: cover;
  }
}
</style>