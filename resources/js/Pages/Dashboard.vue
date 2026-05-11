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

 <!-- ══════════════════════════════════════
       SHOW CUSTOMER DIALOG — Enhanced UI
  ══════════════════════════════════════ -->
 <el-dialog
    class="font-droidkufi customer-show-dialog"
    dir="rtl"
    v-model="dialogFormVisibleShow"
    width="72%"
    :close-on-click-modal="true"
    :show-close="false"
    @close="resetFormdata"
  >
    <template #header>
      <div class="dialog-header">
        <div class="dialog-header-left">
          <span class="customer-dot" :style="{ background: dataform.color ?? '#22c55e' }"></span>
          <span class="dialog-title">وردەکاری کڕیار</span>
        </div>
        <button class="dialog-close-btn" @click="dialogFormVisibleShow = false">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </template>
 
    <div class="show-dialog-body">
 
      <!-- Row 1: Name & Phone -->
      <div class="info-grid">
        <div class="info-card">
          <p class="info-label">ناوی کڕیار</p>
          <div class="info-value-row">
            <span class="color-dot" :style="{ background: dataform.color ?? '#22c55e' }"></span>
            <span class="info-value">{{ dataform.name }}</span>
          </div>
        </div>
 
        <div class="info-card">
          <p class="info-label">پەیوەندی و تۆڕە کۆمەڵایەتییەکان</p>
          <div class="info-value-row" style="justify-content: space-between; align-items: center;">
            <span class="info-value ltr">{{ dataform.phone }}</span>
            <div class="contact-button-group">
              <a :href="'tel:' + dataform.phone" class="action-btn call-btn" title="پەیوەندی">
                <i class="fa-solid fa-phone"></i>
              </a>
              <button class="action-btn wa-btn-new" @click="openWhatsApp" title="واتساپ">
                <i class="fa-brands fa-whatsapp"></i>
              </button>
              <a :href="'https://t.me/' + dataform.phone" target="_blank" class="action-btn tg-btn" title="تێلیگرام">
                <i class="fa-brands fa-telegram"></i>
              </a>
              <a :href="'viber://add?number=' + dataform.phone" class="action-btn vb-btn" title="ڤایبەر">
                <i class="fa-brands fa-viber"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
 
      <!-- Row 2: Type & Location -->
      <div class="info-grid">
        <div class="info-card">
          <p class="info-label">جۆری موڵک</p>
          <div class="badge-row">
            <span v-for="t in dataform.type_projects" :key="t.id" class="badge badge-purple">{{ t.name }}</span>
          </div>
        </div>
        <div class="info-card">
          <p class="info-label">ناونیشان</p>
          <div class="badge-row">
            <span v-for="l in dataform.locations" :key="l.id" class="badge badge-green">{{ l.name }}</span>
          </div>
        </div>
      </div>
 
      <!-- Row 3: Prices -->
      <div class="info-grid">
        <div class="info-card price-card">
          <p class="info-label">نرخی سەرەتا</p>
          <p class="price-value">{{ commaNumber(dataform.price_one * 1) }} $</p>
        </div>
        <div class="info-card price-card">
          <p class="info-label">نرخی کۆتایی</p>
          <p class="price-value">{{ commaNumber(dataform.price_two * 1) }} $</p>
        </div>
      </div>
 
      <!-- Note -->
      <div v-if="dataform.note" class="note-card">
        <p class="info-label note-label">تێبینی</p>
        <p class="note-text">{{ dataform.note }}</p>
      </div>
 
      <!-- Mulks Section -->
      <div v-if="dataform.mulks?.length">
        <div class="section-header">
          <p class="section-title">موڵکی هەڵبژێردراو</p>
          <span class="count-badge">{{ dataform.mulks.length }} موڵک</span>
        </div>
 
        <div class="mulk-list">
          <div
            v-for="(mulk, mulkIndex) in dataform.mulks"
            :key="mulk.id"
            class="mulk-card"
          >
            <!-- Mulk Card Header with blur-reveal on name -->
            <div class="mulk-card-header">
              <div class="mulk-header-left">
                <!-- Blurred name — click to reveal -->
                <span
                  class="mulk-name reveal-text"
                  :class="{ revealed: revealedNames.has(mulk.id) }"
                  @click="toggleReveal(revealedNames, mulk.id)"
                  :title="revealedNames.has(mulk.id) ? '' : 'کلیک بکە بۆ دیتن'"
                >{{ mulk.name }}</span>
 
                <span
                  v-if="!revealedNames.has(mulk.id)"
                  class="reveal-hint"
                  @click="toggleReveal(revealedNames, mulk.id)"
                >
                  <i class="fa-solid fa-eye"></i> کلیک بکە
                </span>
 
                <span class="badge badge-purple" style="font-size: 11px;">
                  <i class="fa-solid fa-tag mr-1"></i>
                  {{ mulk.type_project?.name ?? '—' }}
                </span>
              </div>
              <div class="mulk-header-right">
                <span class="mulk-price">{{ commaNumber(mulk.price) }} $</span>
                <button class="remove-btn" @click="removeMulk(mulk)">
                  <i class="fa-solid fa-trash-can"></i>
                  سڕینەوە
                </button>
              </div>
            </div>
 
            <!-- Mulk Stats Grid -->
            <div class="mulk-stats">
              <div class="stat-cell">
                <p class="stat-label"><i class="fa-solid fa-ruler-combined"></i> ڕووبەر</p>
                <p class="stat-value">{{ mulk.Rwbar }}</p>
              </div>
              <div class="stat-cell stat-divider">
                <p class="stat-label"><i class="fa-solid fa-building"></i> جۆری موڵک</p>
                <p class="stat-value">{{ mulk.type_project?.name ?? '—' }}</p>
              </div>
              <div class="stat-cell stat-divider">
                <p class="stat-label"><i class="fa-solid fa-location-dot"></i> شوێن</p>
                <p class="stat-value">{{ mulk.location?.name ?? '—' }}</p>
              </div>
              <div class="stat-cell stat-divider">
                <p class="stat-label"><i class="fa-solid fa-address-book"></i> پەیوەندی</p>
                <div class="contact-actions">
                  <!-- Blurred phone — click to reveal -->
                  <div class="phone-reveal-row">
                    <span
                      class="stat-value ltr reveal-text"
                      :class="{ revealed: revealedPhones.has(mulk.id) }"
                      @click="toggleReveal(revealedPhones, mulk.id)"
                      :title="revealedPhones.has(mulk.id) ? '' : 'کلیک بکە بۆ دیتنی ژمارە'"
                    >{{ mulk.phone }}</span>
                    <button
                      v-if="!revealedPhones.has(mulk.id)"
                      class="reveal-phone-btn"
                      @click="toggleReveal(revealedPhones, mulk.id)"
                    >
                      <i class="fa-solid fa-eye"></i>
                    </button>
                  </div>
 
                  <!-- Contact icons — only show when phone is revealed -->
                  <transition name="fade-slide">
                    <div v-if="revealedPhones.has(mulk.id)" class="flex gap-2 mt-1">
                      <a :href="'tel:' + mulk.phone" class="contact-icon call" title="Call">
                        <i class="fa-solid fa-phone"></i>
                      </a>
                      <button class="contact-icon whatsapp" @click="openWhatsAppmulks(mulk.phone)" title="WhatsApp">
                        <i class="fa-brands fa-whatsapp"></i>
                      </button>
                      <a :href="'https://t.me/' + mulk.phone" target="_blank" class="contact-icon telegram" title="Telegram">
                        <i class="fa-brands fa-telegram"></i>
                      </a>
                      <a :href="'viber://add?number=' + mulk.phone" class="contact-icon viber" title="Viber">
                        <i class="fa-brands fa-viber"></i>
                      </a>
                    </div>
                  </transition>
                </div>
              </div>
            </div>
 
            <!-- Mulk Badges -->
            <div
              v-if="mulk.emara || mulk.qat || mulk.zhmarai_shwqa || mulk.facebook_link || mulk.link_location"
              class="mulk-badges"
            >
              <span v-if="mulk.emara" class="badge badge-neutral"><i class="fa-solid fa-hotel mr-1"></i> عیمارە: {{ mulk.emara }}</span>
              <span v-if="mulk.qat" class="badge badge-neutral"><i class="fa-solid fa-layer-group mr-1"></i> قات: {{ mulk.qat }}</span>
              <span v-if="mulk.zhmarai_shwqa" class="badge badge-neutral"><i class="fa-solid fa-door-open mr-1"></i> شووقە: {{ mulk.zhmarai_shwqa }}</span>
              <a v-if="mulk.facebook_link" :href="mulk.facebook_link" target="_blank" class="badge badge-blue">
                <i class="fa-brands fa-facebook mr-1"></i> فەیسبووک
              </a>
              <a v-if="mulk.link_location" :href="mulk.link_location" target="_blank" class="badge badge-teal">
                <i class="fa-solid fa-map-location-dot mr-1"></i> بینینی شوێن
              </a>
            </div>
 
            <!-- Mulk Note + Images -->
            <div class="mulk-bottom">
              <div v-if="mulk.note" class="mulk-note">
                <p class="info-label" style="margin-bottom: 4px;">تێبینی</p>
                <p class="note-text" style="margin: 0;">{{ mulk.note }}</p>
              </div>
              <div class="mulk-images">
                <p class="info-label" style="margin-bottom: 8px;">وێنەکان</p>
                <div v-if="mulk.images?.length" class="images-row">
                  <div
                    v-for="(img, imgIndex) in mulk.images"
                    :key="img.id"
                    class="img-thumb-wrap group"
                  >
                    <el-image
                      :src="`/storage/${img.path}`"
                      :preview-src-list="mulk.images.map(i => `/storage/${i.path}`)"
                      fit="cover"
                      class="img-thumb"
                    />
                    <button class="print-btn" @click="printImage(mulkIndex, imgIndex)">
                      <i class="fa-solid fa-print"></i>
                    </button>
                  </div>
                </div>
                <p v-else class="empty-text">وێنە نییە</p>
              </div>
            </div>
 
          </div>
        </div>
      </div>
 
      <!-- Empty state -->
      <div v-else class="empty-mulks">
        <i class="fa-regular fa-folder-open" style="font-size: 28px; color: #d1d5db; display: block; margin-bottom: 8px;"></i>
        موڵک نییە
      </div>
 
    </div>
 
    <template #footer>
      <div class="dialog-footer">
        <button class="footer-close-btn" @click="dialogFormVisibleShow = false">داخستن</button>
      </div>
    </template>
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



const revealedNames  = reactive(new Set())
const revealedPhones = reactive(new Set())
 
function toggleReveal(set, id) {
  if (set.has(id)) set.delete(id)
  else set.add(id)
}

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
 
// ─── WhatsApp ─────────────────────────────────────────
function openWhatsApp() {
  let phone = dataform.phone.replace(/\D/g, '')
  if (phone.startsWith('0')) phone = phone.slice(1)
  if (!phone.startsWith('964')) phone = '964' + phone
  window.open(`https://wa.me/${phone}`, '_blank')
}
 
function openWhatsAppmulks(phone) {
  if (!phone) return
  phone = phone.replace(/\D/g, '')
  if (phone.startsWith('0')) phone = phone.slice(1)
  if (!phone.startsWith('964')) phone = '964' + phone
  window.open(`https://wa.me/${phone}`, '_blank')
}

 
// ─── Print image ─────────────────────────────────────
function printImage(mulkIndex, imgIndex) {
  const mulk = dataform.mulks[mulkIndex]
  if (!mulk?.images?.[imgIndex]) return
  const imgSrc = `/storage/${mulk.images[imgIndex].path}`
  const templateSrc = '/assets/images/A4templateimg.jpg'
  const printWindow = window.open('', '_blank', 'width=900,height=1200')
  printWindow.document.write(`
    <html><head><title>Print Image</title>
    <style>
      @page{size:A4;margin:0}
      body{margin:0;padding:0;width:210mm;height:297mm;
        background-image:url('${templateSrc}');background-size:cover;background-position:center;
        display:flex;justify-content:center;align-items:center}
      img{max-width:180mm;max-height:250mm;object-fit:contain}
    </style></head>
    <body><img src="${imgSrc}" /></body></html>`)
  printWindow.document.close()
  printWindow.focus()
  printWindow.onload = () => { printWindow.print(); printWindow.close() }
}
 

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
    confirmButtonText: 'بەڵێ',
    cancelButtonText: 'نەخێر',
    type: 'warning',
    customClass: 'custom-confirm-box',
  }).then(() => {
    router.delete(route('customers.mulks.detach', [mulk.pivot.customer_id, mulk.id]), {
      preserveScroll: true,
      onSuccess: () => {
        ElMessage.success('سڕایەوە بە سەرکەوتوویی')
        router.visit('/dashboard', { preserveScroll: true })
      },
    })
  })
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

// ─── Open dialog ──────────────────────────────────────
function show(id) {
  router.get(`customers/${id}`, {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (result) => {
      const mulk = result.props.flash?.data
      if (!mulk) return
      Object.keys(dataform).forEach(k => delete dataform[k])
      Object.assign(dataform, mulk)
      dataform.type_projects = mulk.type_projects || []
      dataform.locations = mulk.locations || []
      dataform.color = mulk.color ?? '#22c55e'
      dialogFormVisibleShow.value = true
    },
  })
}

// ─── Reset ────────────────────────────────────────────
function resetFormdata() {
  Object.keys(dataform).forEach(key => delete dataform[key])
  dialogFormVisibleShow.value = false
   revealedNames.clear()
   revealedPhones.clear()
}

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

/* ─── Dialog Shell ───────────────────────────────────── */
:deep(.customer-show-dialog .el-dialog__header) {
  padding: 0;
  margin-bottom: 0;
}
:deep(.customer-show-dialog .el-dialog__body) {
  padding: 0;
}
:deep(.customer-show-dialog .el-dialog__footer) {
  padding: 0;
}
 
/* ─── Header ─────────────────────────────────────────── */
.dialog-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.875rem 1.25rem;
  border-bottom: 0.5px solid #e5e7eb;
  background: #f9fafb;
  border-radius: 12px 12px 0 0;
}
.dialog-header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}
.customer-dot {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 50%;
}
.dialog-title {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}
.dialog-close-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: #d91313;
  font-size: 16px;
  width: 30px;
  height: 30px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s;
}
.dialog-close-btn:hover {
  background: #f3f4f6;
  color: #111827;
}


/* ─── Blur / Reveal ──────────────────────────────────────────────── */
.reveal-text {
  filter: blur(5px);
  cursor: pointer;
  user-select: none;
  transition: filter 0.35s ease;
  border-radius: 4px;
  padding: 1px 4px;
  position: relative;
}
.reveal-text:hover {
  filter: blur(3px);
  background: rgba(99, 153, 34, 0.06);
}
.reveal-text.revealed {
  filter: none;
  cursor: default;
  background: transparent;
}
 
/* Hint badge that appears next to blurred name */
.reveal-hint {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 10px;
  padding: 2px 8px;
  border-radius: 20px;
  background: #f0fdf4;
  border: 0.5px solid #86efac;
  color: #16a34a;
  cursor: pointer;
  transition: background 0.15s;
  white-space: nowrap;
}
.reveal-hint:hover {
  background: #dcfce7;
}
 
/* Phone reveal row */
.phone-reveal-row {
  display: flex;
  align-items: center;
  gap: 6px;
}
.reveal-phone-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  border-radius: 6px;
  border: 0.5px solid #d1d5db;
  background: #f9fafb;
  color: #6b7280;
  font-size: 11px;
  cursor: pointer;
  transition: all 0.15s;
  flex-shrink: 0;
}
.reveal-phone-btn:hover {
  background: #f0fdf4;
  border-color: #86efac;
  color: #16a34a;
}
 
/* Fade-slide transition for contact icons appearing */
.fade-slide-enter-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(-4px);
}
.fade-slide-enter-to {
  opacity: 1;
  transform: translateY(0);
}
 
/* ─── Dialog Shell ───────────────────────────────────────────────── */
:deep(.customer-show-dialog .el-dialog__header) { padding: 0; margin-bottom: 0; }
:deep(.customer-show-dialog .el-dialog__body)   { padding: 0; }
:deep(.customer-show-dialog .el-dialog__footer) { padding: 0; }
 
/* ─── Header ─────────────────────────────────────────────────────── */
.dialog-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.875rem 1.25rem;
  border-bottom: 0.5px solid #e5e7eb;
  background: #f9fafb;
  border-radius: 12px 12px 0 0;
}
.dialog-header-left { display: flex; align-items: center; gap: 10px; }
.customer-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; }
.dialog-title { font-size: 16px; font-weight: 600; color: #111827; }
.dialog-close-btn {
  background: none; border: none; cursor: pointer; color: #d91313;
  font-size: 16px; width: 30px; height: 30px; border-radius: 6px;
  display: flex; align-items: center; justify-content: center; transition: background 0.15s;
}
.dialog-close-btn:hover { background: #f3f4f6; color: #111827; }
 
/* ─── Body ───────────────────────────────────────────────────────── */
.show-dialog-body {
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.875rem;
  max-height: 78vh;
  overflow-y: auto;
}
.show-dialog-body::-webkit-scrollbar { width: 5px; }
.show-dialog-body::-webkit-scrollbar-track { background: transparent; }
.show-dialog-body::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
 
/* ─── Info Cards ─────────────────────────────────────────────────── */
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
.info-card { background: #f9fafb; border-radius: 10px; padding: 0.75rem 1rem; border: 0.5px solid #e5e7eb; }
.info-label { font-size: 11px; font-weight: 600; color: #9ca3af; margin: 0 0 6px; letter-spacing: 0.04em; text-transform: uppercase; }
.info-value-row { display: flex; align-items: center; gap: 8px; }
.color-dot {
  display: inline-block; width: 13px; height: 13px; border-radius: 50%; flex-shrink: 0;
  border: 1.5px solid rgba(255,255,255,0.5); box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
}
.info-value { font-size: 15px; font-weight: 500; color: #111827; }
.ltr { direction: ltr; }
 
/* ─── Badges ─────────────────────────────────────────────────────── */
.badge-row { display: flex; gap: 6px; flex-wrap: wrap; }
.badge { font-size: 12px; padding: 3px 10px; border-radius: 20px; font-weight: 500; }
.badge-purple { background: #ede9fe; color: #5b21b6; border: 0.5px solid #c4b5fd; }
.badge-green  { background: #ecfdf5; color: #065f46; border: 0.5px solid #6ee7b7; }
.badge-blue   { background: #eff6ff; color: #1d4ed8; border: 0.5px solid #93c5fd; text-decoration: none; }
.badge-teal   { background: #f0fdfa; color: #0f766e; border: 0.5px solid #5eead4; text-decoration: none; }
.badge-neutral{ background: #f9fafb; color: #4b5563; border: 0.5px solid #e5e7eb; }
 
/* ─── Price Cards ────────────────────────────────────────────────── */
.price-card { border-left: 3px solid #fca5a5; }
.price-value { font-size: 20px; font-weight: 600; color: #b91c1c; margin: 0; direction: ltr; }
 
/* ─── Note ───────────────────────────────────────────────────────── */
.note-card { background: #fffbeb; border: 0.5px solid #fde68a; border-radius: 10px; padding: 0.75rem 1rem; }
.note-label { color: #92400e; }
.note-text { font-size: 14px; color: #78350f; margin: 0; line-height: 1.65; }
 
/* ─── Section Header ─────────────────────────────────────────────── */
.section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; }
.section-title { font-size: 14px; font-weight: 600; color: #111827; margin: 0; }
.count-badge { font-size: 12px; background: #f3f4f6; border: 0.5px solid #e5e7eb; padding: 3px 10px; border-radius: 20px; color: #3eb131; }
 
/* ─── Mulk Cards ─────────────────────────────────────────────────── */
.mulk-list { display: flex; flex-direction: column; gap: 0.875rem; }
.mulk-card { border: 0.5px solid #e5e7eb; border-radius: 12px; overflow: hidden; background: #fff; transition: box-shadow 0.2s; }
.mulk-card:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
 
.mulk-card-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;
}
.mulk-header-left { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.mulk-name { font-size: 14px; font-weight: 600; color: #111827; }
.mulk-header-right { display: flex; align-items: center; gap: 10px; }
.mulk-price { font-size: 15px; font-weight: 600; color: #b91c1c; }
.remove-btn {
  display: inline-flex; align-items: center; gap: 4px; background: none;
  border: 0.5px solid #fca5a5; border-radius: 8px; color: #dc2626;
  padding: 3px 10px; font-size: 12px; cursor: pointer; font-family: inherit; transition: background 0.15s;
}
.remove-btn:hover { background: #fef2f2; }
 
/* ─── Stats Grid ─────────────────────────────────────────────────── */
.mulk-stats { display: grid; grid-template-columns: repeat(4, 1fr); padding: 0.875rem 1rem; border-bottom: 0.5px solid #e5e7eb; }
.stat-cell { text-align: center; }
.stat-divider { border-right: 0.5px solid #e5e7eb; }
.stat-label { font-size: 11px; color: #9ca3af; margin: 0 0 3px; font-weight: 500; }
.stat-label i { margin-left: 4px; color: #6b7280; font-size: 12px; }
.stat-value { font-size: 13px; font-weight: 500; color: #111827; margin: 0; }
 
/* ─── Contact Actions (inside stat cell) ────────────────────────── */
.contact-actions { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.contact-icon {
  display: flex; align-items: center; justify-content: center;
  width: 28px; height: 28px; border-radius: 6px; color: white;
  font-size: 14px; transition: transform 0.2s; cursor: pointer;
  border: none; text-decoration: none;
}
.contact-icon:hover { transform: scale(1.15); }
.contact-icon.call      { background-color: #3b82f6; }
.contact-icon.whatsapp  { background-color: #25D366; }
.contact-icon.telegram  { background-color: #0088cc; }
.contact-icon.viber     { background-color: #7360f2; }
 
/* ─── Customer contact buttons ───────────────────────────────────── */
.contact-button-group { display: flex; gap: 8px; align-items: center; }
.action-btn {
  display: flex; justify-content: center; align-items: center;
  width: 32px; height: 32px; border-radius: 8px; color: white;
  text-decoration: none; transition: all 0.2s ease; border: none; cursor: pointer; font-size: 16px;
}
.action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
.call-btn   { background-color: #3b82f6; }
.wa-btn-new { background-color: #25D366; }
.tg-btn     { background-color: #0088cc; }
.vb-btn     { background-color: #7360f2; }
 
/* ─── Mulk Badges Row ────────────────────────────────────────────── */
.mulk-badges { display: flex; gap: 6px; flex-wrap: wrap; padding: 0.625rem 1rem; border-bottom: 0.5px solid #e5e7eb; }
 
/* ─── Mulk Bottom ────────────────────────────────────────────────── */
.mulk-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 0.875rem 1rem; }
.images-row { display: flex; gap: 8px; flex-wrap: wrap; }
.img-thumb-wrap { position: relative; width: 72px; height: 72px; border-radius: 8px; overflow: hidden; border: 0.5px solid #e5e7eb; }
.img-thumb { width: 100%; height: 100%; object-fit: cover; }
.print-btn {
  position: absolute; top: 4px; right: 4px; background: rgba(0,0,0,0.55); border: none;
  border-radius: 6px; color: #fff; width: 24px; height: 24px; display: flex;
  align-items: center; justify-content: center; cursor: pointer; font-size: 11px;
  opacity: 0; transition: opacity 0.15s;
}
.img-thumb-wrap:hover .print-btn { opacity: 1; }
 
/* ─── Empty States ───────────────────────────────────────────────── */
.empty-text { font-size: 13px; color: #9ca3af; margin: 0; }
.empty-mulks { text-align: center; padding: 2rem 0; font-size: 13px; color: #9ca3af; }
 
/* ─── Footer ─────────────────────────────────────────────────────── */
.dialog-footer {
  display: flex; justify-content: flex-end;
  padding: 0.75rem 1.25rem; border-top: 0.5px solid #e5e7eb;
  background: #f9fafb; border-radius: 0 0 12px 12px;
}
.footer-close-btn {
  padding: 7px 20px; border-radius: 8px; border: 0.5px solid #d1d5db;
  background: #fff; color: #d91313; font-size: 13px; cursor: pointer;
  font-family: inherit; transition: background 0.15s;
}
.footer-close-btn:hover { background: #f3f4f6; }
 
/* ─── Responsive ─────────────────────────────────────────────────── */
@media (max-width: 640px) {
  .info-grid, .mulk-stats, .mulk-bottom { grid-template-columns: 1fr; }
  .stat-divider { border-right: none; border-top: 0.5px solid #e5e7eb; }
}
 
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
 
/* ─── Body ───────────────────────────────────────────── */
.show-dialog-body {
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.875rem;
  max-height: 78vh;
  overflow-y: auto;
}
 
/* ─── Info Cards ─────────────────────────────────────── */
.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}
.info-card {
  background: #f9fafb;
  border-radius: 10px;
  padding: 0.75rem 1rem;
  border: 0.5px solid #e5e7eb;
}
.info-label {
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
  margin: 0 0 6px;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}
.info-value-row {
  display: flex;
  align-items: center;
  gap: 8px;
}
.color-dot {
  display: inline-block;
  width: 13px;
  height: 13px;
  border-radius: 50%;
  flex-shrink: 0;
  border: 1.5px solid rgba(255,255,255,0.5);
  box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
}
.info-value {
  font-size: 15px;
  font-weight: 500;
  color: #111827;
}
.ltr {
  direction: ltr;
}
 
/* ─── WhatsApp Button ────────────────────────────────── */
.wa-btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: #f0fdf4;
  border: 0.5px solid #86efac;
  color: #16a34a;
  border-radius: 8px;
  padding: 4px 12px;
  font-size: 12px;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.15s;
}
.wa-btn:hover {
  background: #dcfce7;
}
.wa-icon-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: #16a34a;
  font-size: 14px;
  padding: 0 2px;
}
 
/* ─── Badges ─────────────────────────────────────────── */
.badge-row {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}
.badge {
  font-size: 12px;
  padding: 3px 10px;
  border-radius: 20px;
  font-weight: 500;
}
.badge-purple {
  background: #ede9fe;
  color: #5b21b6;
  border: 0.5px solid #c4b5fd;
}
.badge-green {
  background: #ecfdf5;
  color: #065f46;
  border: 0.5px solid #6ee7b7;
}
.badge-blue {
  background: #eff6ff;
  color: #1d4ed8;
  border: 0.5px solid #93c5fd;
  text-decoration: none;
}
.badge-teal {
  background: #f0fdfa;
  color: #0f766e;
  border: 0.5px solid #5eead4;
  text-decoration: none;
}
.badge-neutral {
  background: #f9fafb;
  color: #4b5563;
  border: 0.5px solid #e5e7eb;
}
 
/* ─── Price Cards ────────────────────────────────────── */
.price-card {
  border-left: 3px solid #fca5a5;
}
.price-value {
  font-size: 20px;
  font-weight: 600;
  color: #b91c1c;
  margin: 0;
  direction: ltr;
}
 
/* ─── Note ───────────────────────────────────────────── */
.note-card {
  background: #fffbeb;
  border: 0.5px solid #fde68a;
  border-radius: 10px;
  padding: 0.75rem 1rem;
}
.note-label {
  color: #92400e;
}
.note-text {
  font-size: 14px;
  color: #78350f;
  margin: 0;
  line-height: 1.65;
}
 
/* ─── Section Header ─────────────────────────────────── */
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}
.section-title {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  margin: 0;
}
.count-badge {
  font-size: 12px;
  background: #f3f4f6;
  border: 0.5px solid #e5e7eb;
  padding: 3px 10px;
  border-radius: 20px;
  color: #3eb131;
}
 
/* ─── Mulk Cards ─────────────────────────────────────── */
.mulk-list {
  display: flex;
  flex-direction: column;
  gap: 0.875rem;
}
.mulk-card {
  border: 0.5px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
  transition: box-shadow 0.2s;
}
.mulk-card:hover {
  box-shadow: 0 2px 12px rgba(0,0,0,0.07);
}
.mulk-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 1rem;
  background: #f9fafb;
  border-bottom: 0.5px solid #e5e7eb;
}
.mulk-header-left {
  display: flex;
  align-items: center;
  gap: 8px;
}
.mulk-name {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
}
.mulk-header-right {
  display: flex;
  align-items: center;
  gap: 10px;
}
.mulk-price {
  font-size: 15px;
  font-weight: 600;
  color: #b91c1c;
}
.remove-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: none;
  border: 0.5px solid #fca5a5;
  border-radius: 8px;
  color: #dc2626;
  padding: 3px 10px;
  font-size: 12px;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.15s;
}
.remove-btn:hover {
  background: #fef2f2;
}
 
/* ─── Stats Grid ─────────────────────────────────────── */
.mulk-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  padding: 0.875rem 1rem;
  border-bottom: 0.5px solid #e5e7eb;
}
.stat-cell {
  text-align: center;
}
.stat-divider {
  border-right: 0.5px solid #e5e7eb;
}
.stat-label {
  font-size: 11px;
  color: #9ca3af;
  margin: 0 0 3px;
  font-weight: 500;
}
.stat-value {
  font-size: 13px;
  font-weight: 500;
  color: #111827;
  margin: 0;
}
.stat-phone {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
}
 
/* ─── Mulk Badges Row ────────────────────────────────── */
.mulk-badges {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  padding: 0.625rem 1rem;
  border-bottom: 0.5px solid #e5e7eb;
}
 
/* ─── Mulk Bottom ────────────────────────────────────── */
.mulk-bottom {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  padding: 0.875rem 1rem;
}
.mulk-note {
  /* inherits */
}
.mulk-images {
  /* inherits */
}
.images-row {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.img-thumb-wrap {
  position: relative;
  width: 72px;
  height: 72px;
  border-radius: 8px;
  overflow: hidden;
  border: 0.5px solid #e5e7eb;
}
.img-thumb {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.print-btn {
  position: absolute;
  top: 4px;
  right: 4px;
  background: rgba(0,0,0,0.55);
  border: none;
  border-radius: 6px;
  color: #fff;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 11px;
  opacity: 0;
  transition: opacity 0.15s;
}
.img-thumb-wrap:hover .print-btn {
  opacity: 1;
}
 
/* ─── Empty States ───────────────────────────────────── */
.empty-text {
  font-size: 13px;
  color: #9ca3af;
  margin: 0;
}
.empty-mulks {
  text-align: center;
  padding: 2rem 0;
  font-size: 13px;
  color: #9ca3af;
}
 
/* ─── Footer ─────────────────────────────────────────── */
.dialog-footer {
  display: flex;
  justify-content: flex-end;
  padding: 0.75rem 1.25rem;
  border-top: 0.5px solid #e5e7eb;
  background: #f9fafb;
  border-radius: 0 0 12px 12px;
}
.footer-close-btn {
  padding: 7px 20px;
  border-radius: 8px;
  border: 0.5px solid #d1d5db;
  background: #fff;
  color: #d91313;
  font-size: 13px;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.15s;
}
.footer-close-btn:hover {
  background: #f3f4f6;
}
 
/* ─── Scrollbar ──────────────────────────────────────── */
.show-dialog-body::-webkit-scrollbar {
  width: 5px;
}
.show-dialog-body::-webkit-scrollbar-track {
  background: transparent;
}
.show-dialog-body::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 10px;
}
 
/* ─── Responsive ─────────────────────────────────────── */
@media (max-width: 640px) {
  .info-grid,
  .mulk-stats,
  .mulk-bottom {
    grid-template-columns: 1fr;
  }
  .stat-divider {
    border-right: none;
    border-top: 0.5px solid #e5e7eb;
  }
}



/* Styling for the new communication buttons */
.contact-actions {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.contact-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 6px;
  color: white;
  font-size: 14px;
  transition: transform 0.2s;
  cursor: pointer;
  border: none;
  text-decoration: none;
}

.contact-icon:hover {
  transform: scale(1.15);
}

.contact-icon.call { background-color: #3b82f6; } /* Blue */
.contact-icon.whatsapp { background-color: #25D366; } /* Green */
.contact-icon.telegram { background-color: #0088cc; } /* Sky Blue */
.contact-icon.viber { background-color: #7360f2; } /* Purple */

.stat-label i {
  margin-left: 4px;
  color: #6b7280;
  font-size: 12px;
}

.ltr {
  direction: ltr;
}


/* Container for the row of buttons */
.contact-button-group {
  display: flex;
  gap: 8px;
  align-items: center;
}

/* Base style for small circle action buttons */
.action-btn {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  color: white;
  text-decoration: none;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
  font-size: 16px;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Brand Colors */
.call-btn { background-color: #3b82f6; } /* Phone Blue */
.wa-btn-new { background-color: #25D366; } /* WhatsApp Green */
.tg-btn { background-color: #0088cc; }   /* Telegram Blue */
.vb-btn { background-color: #7360f2; }   /* Viber Purple */

/* Ensure LTR for phone numbers */
.ltr {
  direction: ltr;
  font-family: sans-serif;
}

</style>