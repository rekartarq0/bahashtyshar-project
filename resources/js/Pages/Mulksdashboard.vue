<template>
  <AppLayout title="داشبۆردی موڵکەکان">
    <template #header>
      <div class="flex font-droidkufi items-center justify-between w-full">
        <h4 dir="rtl" class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black pl-1 sm:pl-2">
          داشبۆرد
        </h4>
        <div class="flex items-center gap-2 mr-2">
          <span class="header-stat-pill emerald">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
              <polyline points="9 22 9 12 15 12 15 22" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            </svg>
            {{ filteredMulks.length }} موڵک
          </span>
          <span class="header-stat-pill blue">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ totalCustomers }} کڕیار
          </span>
        </div>
      </div>
    </template>

    <div class="dashboard-root font-droidkufi" dir="rtl">

      <!-- Loading Overlay -->
      <transition name="fade-overlay">
        <div v-if="loading" class="loading-overlay">
          <div class="loading-card">
            <div class="spinner-ring"></div>
            <span>چاوەڕێ بکە…</span>
          </div>
        </div>
      </transition>

      <div class="dashboard-container">

        <!-- KPI Strip -->
        <div class="kpi-strip">
          <div class="kpi-card" v-for="kpi in kpiCards" :key="kpi.label">
            <div class="kpi-icon" :class="kpi.color">
              <span v-html="kpi.icon"></span>
            </div>
            <div class="kpi-body">
              <p class="kpi-label">{{ kpi.label }}</p>
              <p class="kpi-value">{{ kpi.value }}</p>
            </div>
          </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
          <div class="filter-row">
            <div class="search-wrap">
              <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input style="font-family: NRT;" v-model="searchText" type="text" placeholder="گەڕان بە ناوی موڵک یان ژمارە…" class="search-input"/>
            </div>

            <div class="filter-group">
              <label class="filter-label">بەروار</label>
              <el-date-picker
                v-model="searchQuery.datefilter"
                type="daterange" range-separator="—"
                start-placeholder="لە" end-placeholder="بۆ"
                format="YYYY-MM-DD" value-format="YYYY-MM-DD" size="small"
              />
            </div>

            <div class="filter-group">
              <label class="filter-label">جۆری موڵک</label>
              <el-select v-model="filterType" multiple clearable filterable placeholder="هەموو جۆرەکان" size="small" style="min-width:150px">
                <el-option v-for="t in data.typeProjects" :key="t.id" :label="t.name" :value="t.id"/>
              </el-select>
            </div>

            <div class="filter-group">
              <label class="filter-label">ناونیشان</label>
              <el-select v-model="filterLocation" multiple clearable filterable placeholder="هەموو شوێنەکان" size="small" style="min-width:150px">
                <el-option v-for="l in data.locations" :key="l.id" :label="l.name" :value="l.id"/>
              </el-select>
            </div>

            <div class="filter-group">
              <label class="filter-label">ڕیزکردن</label>
              <el-select v-model="sortBy" size="small" style="min-width:150px">
                <el-option label="زیاترین کڕیار" value="customers_desc"/>
                <el-option label="کەمترین کڕیار" value="customers_asc"/>
                <el-option label="زیاترین نرخ" value="price_desc"/>
                <el-option label="کەمترین نرخ" value="price_asc"/>
                <el-option label="زیاترین ڕووبەر" value="area_desc"/>
                <el-option label="نوێترین" value="newest"/>
                <el-option label="کۆنترین" value="oldest"/>
              </el-select>
            </div>

            <div class="view-toggle mr-auto">
              <button @click="viewMode='grid'" :class="['toggle-btn', viewMode==='grid' && 'active']" title="گرید">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
              </button>
              <button @click="viewMode='list'" :class="['toggle-btn', viewMode==='list' && 'active']" title="لیست">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
              </button>
            </div>
          </div>

          <transition name="slide-down">
            <div v-if="hasActiveFilters" class="active-filters">
              <span class="text-xs text-slate-400 font-semibold">فلتەرەکان:</span>
              <button v-if="searchText" @click="searchText=''" class="filter-chip">🔍 {{ searchText }} ×</button>
              <button v-if="filterType.length" @click="filterType=[]" class="filter-chip">جۆر ({{ filterType.length }}) ×</button>
              <button v-if="filterLocation.length" @click="filterLocation=[]" class="filter-chip">شوێن ({{ filterLocation.length }}) ×</button>
              <button @click="clearAllFilters" class="filter-chip danger">پاک کردنەوەی هەموو ×</button>
            </div>
          </transition>
        </div>

        <!-- Stage Legend -->
        <div class="stage-legend">
          <span v-for="s in STAGE_ORDER" :key="s" class="stage-pill" :style="{ background: STAGE_COLORS[s] }">
            <span>{{ STAGE_EMOJI[s] }}</span>
            {{ stageLabel(s) }}
            <span class="stage-pill-count">{{ stageTotals[s] ?? 0 }}</span>
          </span>
        </div>

        <!-- Empty State -->
        <div v-if="!filteredMulks.length" class="empty-state">
          <div class="empty-icon">🏚</div>
          <p class="empty-title">هیچ موڵکێک نەدۆزرایەوە</p>
          <p class="empty-sub">فلتەرەکانت بگۆڕە یان موڵکی نوێ زیاد بکە</p>
        </div>

        <!-- Grid View -->
        <div v-else-if="viewMode === 'grid'" class="mulk-grid">
          <div
            v-for="(mulk, idx) in filteredMulks"
            :key="mulk.id"
            class="mulk-card"
            :style="{ animationDelay: `${idx * 40}ms` }"
            @click="openMulkDetail(mulk)"
          >
            <div class="card-accent" :style="{ background: customerCountAccent(mulk.customer_count) }"></div>
            <div class="card-body">
              <!-- Header -->
              <div class="card-header-row">
                <div class="card-name-wrap">
                  <span class="card-house-icon">🏠</span>
                  <p class="card-name">{{ mulk.name }}</p>
                </div>
                <div class="cust-badge-wrap">
                  <span class="cust-badge" :style="{ background: customerCountAccent(mulk.customer_count) }">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ mulk.customer_count }}
                  </span>
                  <span class="cust-badge-label">کڕیار</span>
                </div>
              </div>

              <!-- Price + Area -->
              <div class="card-meta-row">
                <span v-if="mulk.price" class="meta-chip gold">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  {{ commaNumber(mulk.price) }}$
                </span>
                <span v-if="mulk.Rwbar" class="meta-chip blue">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                  {{ mulk.Rwbar }} م²
                </span>
                <span v-if="mulk.number_mulk" class="meta-chip gray"># {{ mulk.number_mulk }}</span>
              </div>

              <!-- Location + Type -->
              <div class="card-info-row">
                <span v-if="mulk.location?.name" class="info-item">
                  <span class="loc-dot"></span>{{ mulk.location.name }}
                </span>
                <span v-if="mulk.type_project?.name" class="info-sep">·</span>
                <span v-if="mulk.type_project?.name" class="info-item muted">{{ mulk.type_project.name }}</span>
              </div>

              <!-- Building Details -->
              <div v-if="mulk.emara || mulk.qat || mulk.zhmarai_shwqa" class="building-chips">
                <span v-if="mulk.emara" class="b-chip">🏢 {{ mulk.emara }}</span>
                <span v-if="mulk.qat" class="b-chip">🏗 قات {{ mulk.qat }}</span>
                <span v-if="mulk.zhmarai_shwqa" class="b-chip">🚪 {{ mulk.zhmarai_shwqa }}</span>
              </div>

              <!-- Phone -->
              <div v-if="mulk.phone" class="card-phone-row">
                <a :href="`tel:${mulk.phone}`" @click.stop class="phone-action tel" :title="'پەیوەندی: ' + mulk.phone">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </a>
                <a :href="`https://wa.me/${cleanPhone(mulk.phone)}`" target="_blank" @click.stop class="phone-action whatsapp" title="واتساپ">
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
                <a :href="`viber://chat?number=${cleanPhone(mulk.phone)}`" @click.stop class="phone-action viber" title="ڤایبەر">
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M11.398.002C8.717-.025 3.254.532 1.09 5.77c-1.118 2.713-1.073 6.235-.573 9.098.5 2.862 1.5 5.498 4.003 6.998.5.3 1 .5 1.5.6v2.5c0 .2.1.4.3.5.1 0 .2.034.3.034.15 0 .3-.05.4-.15l2.7-2.734c.4.034.8.067 1.2.067 2.48 0 4.663-.484 6.363-1.567 3.603-2.3 3.903-6.7 3.87-9.5-.07-4.3-1.87-7.4-4.37-8.8C15.282.516 13.415.023 11.4.003zm.11 1.8c1.78-.017 3.47.43 4.89 1.2 2.1 1.2 3.5 3.8 3.57 7.7.03 2.6-.27 6.4-3.2 8.2-1.6 1-3.6 1.4-5.9 1.4-.4 0-.8-.03-1.2-.07-.2-.017-.4.067-.5.2l-2.1 2.1v-1.9c0-.3-.2-.5-.4-.6-2-.6-3.3-2.9-3.7-5.3-.47-2.67-.5-5.83.45-8.17C5.073 2.45 9.248 1.822 11.508 1.802zm-.41 2.7c-.1 0-.2.05-.3.1-.7.4-1.5.9-1.8 1.7-.3.7-.1 1.5.2 2.2.6 1.4 1.5 2.7 2.5 3.8 1 1 2.3 2 3.7 2.6.7.3 1.5.5 2.2.2.8-.3 1.3-1.1 1.7-1.8.2-.4.1-.9-.2-1.2L17 10.4c-.3-.3-.8-.3-1.1-.1l-.9.7c-.2.2-.5.2-.7.1-.6-.3-1.6-1-2.3-1.7-.7-.7-1.3-1.7-1.6-2.3-.1-.2-.1-.5.1-.7l.7-.9c.2-.3.2-.8-.1-1.1L9.5 2.9c-.1-.2-.3-.4-.4-.4z"/></svg>
                </a>
                <span class="phone-num">{{ mulk.phone }}</span>
              </div>

              <!-- Customer Stage Bar -->
              <div v-if="mulk.customers?.length" class="card-footer">
                <div class="stage-bar">
                  <div
                    v-for="(cnt, st) in customerStageBreakdown(mulk.customers)"
                    :key="st"
                    :style="{ flex: cnt, background: STAGE_COLORS[st] }"
                    :title="stageLabel(st) + ': ' + cnt"
                  ></div>
                </div>
                <div class="stage-tags">
                  <span v-for="(cnt, st) in customerStageBreakdown(mulk.customers)" :key="st"
                    class="s-tag" :style="{ background: STAGE_COLORS[st] }">
                    {{ STAGE_EMOJI[st] }} {{ stageLabel(st) }} {{ cnt }}
                  </span>
                </div>
                <div class="dot-row">
                  <el-tooltip v-for="c in mulk.customers.slice(0,10)" :key="c.id"
                    :content="c.name + ' — ' + stageLabel(c.current_stage)" placement="top">
                    <span class="cust-dot" :style="{ background: c.color ?? '#22c55e' }"></span>
                  </el-tooltip>
                  <span v-if="mulk.customers.length > 10" class="dot-more">+{{ mulk.customers.length - 10 }}</span>
                </div>
              </div>
              <div v-else class="no-customers">کڕیار نییە</div>
            </div>

            <div class="card-hover-overlay">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              وردەکاری ببینە
            </div>
          </div>
        </div>

        <!-- List View -->
        <div v-else class="list-wrap">
          <table class="mulk-table">
            <thead>
              <tr>
                <th>ناوی موڵک</th>
                <th>شوێن</th>
                <th>جۆر</th>
                <th>نرخ</th>
                <th>ڕووبەر</th>
                <th>ژمارەی موڵک</th>
                <th class="text-center">کڕیار</th>
                <th>مەرحەلەکان</th>
                <th>پەیوەندی</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="mulk in filteredMulks" :key="mulk.id" class="table-row" @click="openMulkDetail(mulk)">
                <td>
                  <div class="flex items-center gap-2">
                    <div class="table-bar" :style="{ background: customerCountAccent(mulk.customer_count) }"></div>
                    <span class="font-bold text-slate-800">{{ mulk.name }}</span>
                  </div>
                </td>
                <td class="text-slate-500 text-xs">{{ mulk.location?.name ?? '—' }}</td>
                <td class="text-slate-500 text-xs">{{ mulk.type_project?.name ?? '—' }}</td>
                <td>
                  <span v-if="mulk.price" class="price-tag">{{ commaNumber(mulk.price) }}$</span>
                  <span v-else class="text-slate-300">—</span>
                </td>
                <td class="text-slate-500 text-xs">{{ mulk.Rwbar ? mulk.Rwbar + ' م²' : '—' }}</td>
                <td class="text-slate-500 text-xs">{{ mulk.number_mulk ?? '—' }}</td>
                <td class="text-center">
                  <span class="table-cust-badge" :style="{ background: customerCountAccent(mulk.customer_count) }">
                    {{ mulk.customer_count }}
                  </span>
                </td>
                <td>
                  <div class="flex flex-wrap gap-1">
                    <span v-for="(cnt, st) in customerStageBreakdown(mulk.customers ?? [])" :key="st"
                      class="s-tag" :style="{ background: STAGE_COLORS[st] }">
                      {{ STAGE_EMOJI[st] }} {{ stageLabel(st) }} {{ cnt }}
                    </span>
                  </div>
                </td>
                <td @click.stop>
                  <div class="flex gap-1.5" v-if="mulk.phone">
                    <a :href="`tel:${mulk.phone}`" class="phone-action tel sm">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </a>
                    <a :href="`https://wa.me/${cleanPhone(mulk.phone)}`" target="_blank" class="phone-action whatsapp sm">
                      <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    <a :href="`viber://chat?number=${cleanPhone(mulk.phone)}`" class="phone-action viber sm">
                      <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M11.398.002C8.717-.025 3.254.532 1.09 5.77c-1.118 2.713-1.073 6.235-.573 9.098.5 2.862 1.5 5.498 4.003 6.998.5.3 1 .5 1.5.6v2.5c0 .2.1.4.3.5.1 0 .2.034.3.034.15 0 .3-.05.4-.15l2.7-2.734c.4.034.8.067 1.2.067 2.48 0 4.663-.484 6.363-1.567 3.603-2.3 3.903-6.7 3.87-9.5-.07-4.3-1.87-7.4-4.37-8.8C15.282.516 13.415.023 11.4.003zm.11 1.8c1.78-.017 3.47.43 4.89 1.2 2.1 1.2 3.5 3.8 3.57 7.7.03 2.6-.27 6.4-3.2 8.2-1.6 1-3.6 1.4-5.9 1.4-.4 0-.8-.03-1.2-.07-.2-.017-.4.067-.5.2l-2.1 2.1v-1.9c0-.3-.2-.5-.4-.6-2-.6-3.3-2.9-3.7-5.3-.47-2.67-.5-5.83.45-8.17C5.073 2.45 9.248 1.822 11.508 1.802zm-.41 2.7c-.1 0-.2.05-.3.1-.7.4-1.5.9-1.8 1.7-.3.7-.1 1.5.2 2.2.6 1.4 1.5 2.7 2.5 3.8 1 1 2.3 2 3.7 2.6.7.3 1.5.5 2.2.2.8-.3 1.3-1.1 1.7-1.8.2-.4.1-.9-.2-1.2L17 10.4c-.3-.3-.8-.3-1.1-.1l-.9.7c-.2.2-.5.2-.7.1-.6-.3-1.6-1-2.3-1.7-.7-.7-1.3-1.7-1.6-2.3-.1-.2-.1-.5.1-.7l.7-.9c.2-.3.2-.8-.1-1.1L9.5 2.9c-.1-.2-.3-.4-.4-.4z"/></svg>
                    </a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div><!-- /container -->
    </div><!-- /dashboard-root -->
  </AppLayout>

  <!-- ══════════════════════════════════════════
       MULK DETAIL DIALOG
  ══════════════════════════════════════════ -->
  <el-dialog
    class="font-droidkufi mulk-dialog"
    dir="rtl"
    v-model="detailVisible"
    width="90%"
    :close-on-click-modal="true"
    destroy-on-close
    top="2vh"
    :show-close="true"
  >
    <template #header>
      <div class="dialog-header-custom">
        <div class="dialog-title-row">
          <span class="dialog-house">🏠</span>
          <div>
            <h2 class="dialog-mulk-name">{{ selectedMulk?.name }}</h2>
            <p class="dialog-mulk-sub">
              <span v-if="selectedMulk?.location?.name">📍 {{ selectedMulk.location.name }}</span>
              <span v-if="selectedMulk?.type_project?.name" class="mx-2 text-slate-300">·</span>
              <span v-if="selectedMulk?.type_project?.name">{{ selectedMulk.type_project.name }}</span>
              <span v-if="selectedMulk?.number_mulk" class="mx-2 text-slate-300">·</span>
              <span v-if="selectedMulk?.number_mulk"># {{ selectedMulk.number_mulk }}</span>
            </p>
          </div>
        </div>
        <div class="dialog-tabs">
          <button v-for="tab in dialogTabs" :key="tab.key"
            @click="activeTab = tab.key"
            :class="['dialog-tab', activeTab === tab.key && 'active']">
            {{ tab.icon }} {{ tab.label }}
            <span v-if="tab.key === 'customers' && selectedMulk?.customer_count" class="tab-badge">{{ selectedMulk.customer_count }}</span>
          </button>
        </div>
      </div>
    </template>

    <div v-if="selectedMulk" class="dialog-body">

      <!-- TAB: وردەکاری -->
      <div v-show="activeTab === 'info'" class="tab-panel space-y-5">

        <div class="info-kpi-row">
          <div class="info-kpi">
            <p class="info-kpi-label">نرخ</p>
            <p class="info-kpi-val gold">{{ selectedMulk.price ? commaNumber(selectedMulk.price) + '$' : '—' }}</p>
          </div>
          <div class="info-kpi">
            <p class="info-kpi-label">نرخ بە م²</p>
            <p class="info-kpi-val blue">{{ pricePerSqm }}</p>
          </div>
          <div class="info-kpi">
            <p class="info-kpi-label">ڕووبەر</p>
            <p class="info-kpi-val">{{ selectedMulk.Rwbar ? selectedMulk.Rwbar + ' م²' : '—' }}</p>
          </div>
          <div class="info-kpi">
            <p class="info-kpi-label">جۆری موڵک</p>
            <p class="info-kpi-val">{{ selectedMulk.type_project?.name ?? '—' }}</p>
          </div>
          <div class="info-kpi">
            <p class="info-kpi-label">شوێن</p>
            <p class="info-kpi-val">{{ selectedMulk.location?.name ?? '—' }}</p>
          </div>
          <div class="info-kpi">
            <p class="info-kpi-label">ژمارەی موڵک</p>
            <p class="info-kpi-val">{{ selectedMulk.number_mulk ?? '—' }}</p>
          </div>
        </div>

        <div v-if="selectedMulk.emara || selectedMulk.qat || selectedMulk.zhmarai_shwqa" class="detail-section">
          <p class="section-title">🏗 وردەکاری بینا</p>
          <div class="flex flex-wrap gap-2 mt-2">
            <span v-if="selectedMulk.emara" class="detail-chip">🏢 عیمارە: <strong>{{ selectedMulk.emara }}</strong></span>
            <span v-if="selectedMulk.qat" class="detail-chip">🏗 قات: <strong>{{ selectedMulk.qat }}</strong></span>
            <span v-if="selectedMulk.zhmarai_shwqa" class="detail-chip">🚪 شووقە: <strong>{{ selectedMulk.zhmarai_shwqa }}</strong></span>
          </div>
        </div>

        <div class="detail-section">
          <p class="section-title">📞 پەیوەندی و لینکەکان</p>
          <div class="contact-row mt-3">
            <div v-if="selectedMulk.phone" class="contact-block">
              <span class="contact-label">ژمارەی تەلەفۆن</span>
              <div class="contact-actions">
                <span class="contact-num">{{ selectedMulk.phone }}</span>
                <a :href="`tel:${selectedMulk.phone}`" class="contact-btn tel">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                  پەیوەندی
                </a>
                <a :href="`https://wa.me/${cleanPhone(selectedMulk.phone)}`" target="_blank" class="contact-btn whatsapp">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                  واتساپ
                </a>
                <a :href="`viber://chat?number=${cleanPhone(selectedMulk.phone)}`" class="contact-btn viber">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11.398.002C8.717-.025 3.254.532 1.09 5.77c-1.118 2.713-1.073 6.235-.573 9.098.5 2.862 1.5 5.498 4.003 6.998.5.3 1 .5 1.5.6v2.5c0 .2.1.4.3.5.1 0 .2.034.3.034.15 0 .3-.05.4-.15l2.7-2.734c.4.034.8.067 1.2.067 2.48 0 4.663-.484 6.363-1.567 3.603-2.3 3.903-6.7 3.87-9.5-.07-4.3-1.87-7.4-4.37-8.8C15.282.516 13.415.023 11.4.003zm.11 1.8c1.78-.017 3.47.43 4.89 1.2 2.1 1.2 3.5 3.8 3.57 7.7.03 2.6-.27 6.4-3.2 8.2-1.6 1-3.6 1.4-5.9 1.4-.4 0-.8-.03-1.2-.07-.2-.017-.4.067-.5.2l-2.1 2.1v-1.9c0-.3-.2-.5-.4-.6-2-.6-3.3-2.9-3.7-5.3-.47-2.67-.5-5.83.45-8.17C5.073 2.45 9.248 1.822 11.508 1.802zm-.41 2.7c-.1 0-.2.05-.3.1-.7.4-1.5.9-1.8 1.7-.3.7-.1 1.5.2 2.2.6 1.4 1.5 2.7 2.5 3.8 1 1 2.3 2 3.7 2.6.7.3 1.5.5 2.2.2.8-.3 1.3-1.1 1.7-1.8.2-.4.1-.9-.2-1.2L17 10.4c-.3-.3-.8-.3-1.1-.1l-.9.7c-.2.2-.5.2-.7.1-.6-.3-1.6-1-2.3-1.7-.7-.7-1.3-1.7-1.6-2.3-.1-.2-.1-.5.1-.7l.7-.9c.2-.3.2-.8-.1-1.1L9.5 2.9c-.1-.2-.3-.4-.4-.4z"/></svg>
                  ڤایبەر
                </a>
              </div>
            </div>
            <div v-if="selectedMulk.link_location" class="contact-block">
              <span class="contact-label">لوکەیشنی نەخشە</span>
              <a :href="selectedMulk.link_location" target="_blank" class="contact-btn map">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                نەخشەی گووگڵ
              </a>
            </div>
            <div v-if="selectedMulk.facebook_link" class="contact-block">
              <span class="contact-label">فەیسبووک</span>
              <a :href="selectedMulk.facebook_link" target="_blank" class="contact-btn facebook">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                فەیسبووک
              </a>
            </div>
          </div>
        </div>

        <div v-if="selectedMulk.note" class="detail-section">
          <p class="section-title">📝 تێبینی</p>
          <div class="note-box mt-2">{{ selectedMulk.note }}</div>
        </div>

        <div v-if="selectedMulk.images?.length" class="detail-section">
          <p class="section-title">🖼 وێنەکان <span class="text-slate-400 font-normal">({{ selectedMulk.images.length }})</span></p>
          <div class="images-grid mt-3">
            <el-image
              v-for="img in selectedMulk.images" :key="img.id"
              :src="`/storage/${img.path}`"
              :preview-src-list="selectedMulk.images.map(i => `/storage/${i.path}`)"
              fit="cover"
              class="gallery-img"
            />
          </div>
        </div>

        <div class="detail-section">
          <p class="section-title">📅 تاریخ</p>
          <div class="flex flex-wrap gap-4 mt-2 text-xs text-slate-500">
            <span>دروستکراوە: {{ formatDate(selectedMulk.created_at) }}</span>
            <span v-if="selectedMulk.updated_at !== selectedMulk.created_at">نوێکراوەتەوە: {{ formatDate(selectedMulk.updated_at) }}</span>
          </div>
        </div>
      </div>

      <!-- TAB: کڕیارەکان -->
      <div v-show="activeTab === 'customers'" class="tab-panel space-y-4">

        <div v-if="selectedMulk.customers?.length" class="stage-summary-row">
          <div
            v-for="(cnt, st) in customerStageBreakdown(selectedMulk.customers)"
            :key="st"
            class="stage-summary-box"
            :style="{ background: STAGE_COLORS[st] }"
            @click="detailStageFilter = detailStageFilter === st ? null : st"
            :class="{ 'ring-2 ring-offset-2 ring-white scale-105': detailStageFilter === st }"
          >
            <span class="text-2xl">{{ STAGE_EMOJI[st] }}</span>
            <span class="text-2xl font-extrabold">{{ cnt }}</span>
            <span class="text-xs opacity-90 font-semibold">{{ stageLabel(st) }}</span>
          </div>
        </div>

        <div v-if="selectedMulk.customers?.length" class="customer-progress-bar">
          <div v-for="(cnt, st) in customerStageBreakdown(selectedMulk.customers)" :key="st"
            :style="{ flex: cnt, background: STAGE_COLORS[st] }"
            :title="stageLabel(st) + ': ' + cnt + ' کڕیار'">
          </div>
        </div>

        <div v-if="selectedMulk.customers?.length" class="cust-filter-row">
          <button @click="detailStageFilter = null" :class="['cust-filter-btn', detailStageFilter === null && 'active-all']">
            هەموو ({{ selectedMulk.customer_count }})
          </button>
          <button
            v-for="(cnt, st) in customerStageBreakdown(selectedMulk.customers)" :key="st"
            @click="detailStageFilter = detailStageFilter === st ? null : st"
            :class="['cust-filter-btn stage-btn', detailStageFilter === st && 'active-stage']"
            :style="detailStageFilter === st ? { background: STAGE_COLORS[st], borderColor: STAGE_COLORS[st], color: '#fff' } : {}"
          >
            {{ STAGE_EMOJI[st] }} {{ stageLabel(st) }} ({{ cnt }})
          </button>
        </div>

        <div v-if="filteredDetailCustomers?.length" class="cust-grid">
          <div v-for="c in filteredDetailCustomers" :key="c.id" class="cust-card">
            <div class="cust-color-band" :style="{ background: c.color ?? '#22c55e' }"></div>
            <div class="cust-card-body">

              <!-- Name -->
              <div class="cust-name-row">
                <span class="cust-color-dot" :style="{ background: c.color ?? '#22c55e' }"></span>
                <p class="cust-name">{{ c.name }}</p>
                <span v-if="c.backed_from_pricing" class="backed-badge">↩ گەڕاوەتەوە</span>
              </div>

              <!-- Stage -->
              <div class="cust-stage-badge" :style="{ background: STAGE_COLORS[c.current_stage] }">
                <span>{{ STAGE_EMOJI[c.current_stage] }}</span>
                {{ stageLabel(c.current_stage) }}
              </div>

              <!-- ✅ Phone — works because controller now selects phone -->
              <div v-if="c.phone" class="cust-contact-row">
                <span class="cust-phone-num">{{ c.phone }}</span>
                <div class="flex gap-1">
                  <a :href="`tel:${c.phone}`" @click.stop class="phone-action tel sm" title="پەیوەندی">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                  </a>
                  <a :href="`https://wa.me/${cleanPhone(c.phone)}`" target="_blank" @click.stop class="phone-action whatsapp sm" title="واتساپ">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                  </a>
                  <a :href="`viber://chat?number=${cleanPhone(c.phone)}`" @click.stop class="phone-action viber sm" title="ڤایبەر">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M11.398.002C8.717-.025 3.254.532 1.09 5.77c-1.118 2.713-1.073 6.235-.573 9.098.5 2.862 1.5 5.498 4.003 6.998.5.3 1 .5 1.5.6v2.5c0 .2.1.4.3.5.1 0 .2.034.3.034.15 0 .3-.05.4-.15l2.7-2.734c.4.034.8.067 1.2.067 2.48 0 4.663-.484 6.363-1.567 3.603-2.3 3.903-6.7 3.87-9.5-.07-4.3-1.87-7.4-4.37-8.8C15.282.516 13.415.023 11.4.003zm.11 1.8c1.78-.017 3.47.43 4.89 1.2 2.1 1.2 3.5 3.8 3.57 7.7.03 2.6-.27 6.4-3.2 8.2-1.6 1-3.6 1.4-5.9 1.4-.4 0-.8-.03-1.2-.07-.2-.017-.4.067-.5.2l-2.1 2.1v-1.9c0-.3-.2-.5-.4-.6-2-.6-3.3-2.9-3.7-5.3-.47-2.67-.5-5.83.45-8.17C5.073 2.45 9.248 1.822 11.508 1.802zm-.41 2.7c-.1 0-.2.05-.3.1-.7.4-1.5.9-1.8 1.7-.3.7-.1 1.5.2 2.2.6 1.4 1.5 2.7 2.5 3.8 1 1 2.3 2 3.7 2.6.7.3 1.5.5 2.2.2.8-.3 1.3-1.1 1.7-1.8.2-.4.1-.9-.2-1.2L17 10.4c-.3-.3-.8-.3-1.1-.1l-.9.7c-.2.2-.5.2-.7.1-.6-.3-1.6-1-2.3-1.7-.7-.7-1.3-1.7-1.6-2.3-.1-.2-.1-.5.1-.7l.7-.9c.2-.3.2-.8-.1-1.1L9.5 2.9c-.1-.2-.3-.4-.4-.4z"/></svg>
                  </a>
                </div>
              </div>

              <!-- Price -->
              <div v-if="c.price_one || c.price_two" class="cust-price-row">
                <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span v-if="c.price_one">لە {{ commaNumber(c.price_one) }}$</span>
                <span v-if="c.price_one && c.price_two" class="text-slate-300 mx-1">—</span>
                <span v-if="c.price_two">بۆ {{ commaNumber(c.price_two) }}$</span>
              </div>

              <!-- Timing -->
              <div v-if="c.stage_start_time" class="cust-timing">
                <div class="timing-row"><span class="timing-dot start"></span><span>{{ formatDate(c.stage_start_time) }}</span></div>
                <div v-if="c.stage_end_time" class="timing-row"><span class="timing-dot end"></span><span>{{ formatDate(c.stage_end_time) }}</span></div>
                <div class="timing-row duration"><span>⏱</span><span class="font-semibold text-blue-600">{{ getTimeBetween(c.stage_start_time, c.stage_end_time) }}</span></div>
              </div>

              <!-- Stage note -->
              <div v-if="c.stage_note" class="cust-note">📝 {{ c.stage_note }}</div>

              <!-- ✅ Customer note — works because controller now selects note -->
              <div v-if="c.note" class="cust-note slate">💬 {{ c.note }}</div>
            </div>
          </div>
        </div>

        <div v-else class="empty-customers">
          <span class="text-3xl">👤</span>
          <p>{{ detailStageFilter ? 'کڕیار بۆ ئەم مەرحەلەیە نییە' : 'کڕیار نییە' }}</p>
        </div>
      </div>

    </div><!-- /dialog-body -->
  </el-dialog>

</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import commaNumber from 'comma-number';

// ── Props ──────────────────────────────────────────────────────────────────────
const props = defineProps({ data: Object });

// ── Stage constants ────────────────────────────────────────────────────────────
const STAGE_ORDER = ['request', 'prepare', 'show', 'handling', 'contract'];

const STAGE_LABELS = {
  request:  'داواکاری',
  prepare:  'ئامادەکردن',
  show:     'نیشاندان',
  handling: 'مامەڵەکردن',
  contract: 'گرێبەست',
};

const STAGE_EMOJI = {
  request:  '📋',
  prepare:  '⚙️',
  show:     '👁',
  handling: '🤝',
  contract: '📝',
};

const STAGE_COLORS = {
  request:  '#7c3aed',
  prepare:  '#0284c7',
  show:     '#d97706',
  handling: '#be123c',
  contract: '#059669',
  unknown:  '#6b7280',
};

function stageLabel(s) { return STAGE_LABELS[s] ?? s; }

// ── Reactive State ─────────────────────────────────────────────────────────────
const searchQuery    = ref({ datefilter: props.data.datefilter ?? null });
const filterType     = ref([]);
const filterLocation = ref([]);
const searchText     = ref('');
const sortBy         = ref('customers_desc');
const viewMode       = ref('grid');
const loading        = ref(false);
let   refreshTimer;

// ── Dialog State ──────────────────────────────────────────────────────────────
const detailVisible     = ref(false);
const selectedMulk      = ref(null);
const detailStageFilter = ref(null);
const activeTab         = ref('info');

const dialogTabs = [
  { key: 'info',      label: 'وردەکاری',   icon: '🏠' },
  { key: 'customers', label: 'کڕیارەکان',  icon: '👥' },
];

function openMulkDetail(mulk) {
  selectedMulk.value      = mulk;
  detailStageFilter.value = null;
  activeTab.value         = 'info';
  detailVisible.value     = true;
}

function clearAllFilters() {
  searchText.value = '';
  filterType.value = [];
  filterLocation.value = [];
  searchQuery.value.datefilter = null;
}

// ── Computed ──────────────────────────────────────────────────────────────────
const filteredMulks = computed(() => {
  let list = (props.data.mulks ?? []).filter(m => !m.is_archived);

  if (searchText.value.trim()) {
    const q = searchText.value.trim().toLowerCase();
    list = list.filter(m =>
      m.name?.toLowerCase().includes(q) ||
      m.number_mulk?.toLowerCase?.().includes(q) ||
      m.phone?.includes(q)
    );
  }
  if (filterType.value.length)     list = list.filter(m => filterType.value.includes(m.type_project?.id));
  if (filterLocation.value.length) list = list.filter(m => filterLocation.value.includes(m.location?.id));

  return [...list].sort((a, b) => {
    switch (sortBy.value) {
      case 'customers_desc': return (b.customer_count ?? 0) - (a.customer_count ?? 0);
      case 'customers_asc':  return (a.customer_count ?? 0) - (b.customer_count ?? 0);
      case 'price_desc':     return (b.price ?? 0) - (a.price ?? 0);
      case 'price_asc':      return (a.price ?? 0) - (b.price ?? 0);
      case 'area_desc':      return (b.Rwbar ?? 0) - (a.Rwbar ?? 0);
      case 'newest':         return new Date(b.created_at) - new Date(a.created_at);
      case 'oldest':         return new Date(a.created_at) - new Date(b.created_at);
      default:               return 0;
    }
  });
});

const filteredDetailCustomers = computed(() => {
  const custs = selectedMulk.value?.customers ?? [];
  if (!detailStageFilter.value) return custs;
  return custs.filter(c => c.current_stage === detailStageFilter.value);
});

const totalCustomers = computed(() =>
  filteredMulks.value.reduce((s, m) => s + (parseInt(m.customer_count) || 0), 0)
);

const avgPriceFormatted = computed(() => {
  const p = filteredMulks.value.filter(m => m.price);
  if (!p.length) return '—';
  return commaNumber(Math.round(p.reduce((s, m) => s + Number(m.price), 0) / p.length)) + '$';
});

const totalAreaFormatted = computed(() => {
  const a = filteredMulks.value.filter(m => m.Rwbar).reduce((s, m) => s + Number(m.Rwbar), 0);
  return a ? commaNumber(a) + ' م²' : '—';
});

const uniqueLocations = computed(() =>
  new Set(filteredMulks.value.map(m => m.location?.id).filter(Boolean)).size
);

const hasActiveFilters = computed(() =>
  searchText.value || filterType.value.length || filterLocation.value.length || searchQuery.value.datefilter
);

const stageTotals = computed(() => {
  const t = {};
  for (const m of filteredMulks.value) {
    for (const c of m.customers ?? []) {
      const s = c.current_stage ?? 'unknown';
      t[s] = (t[s] ?? 0) + 1;
    }
  }
  return t;
});

const kpiCards = computed(() => [
  { label: 'موڵکی ئامادە',  value: filteredMulks.value.length,  color: 'emerald', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>' },
  { label: 'کۆی کڕیار',    value: totalCustomers.value,        color: 'blue',    icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' },
  { label: 'ناوەندی نرخ',  value: avgPriceFormatted.value,     color: 'gold',    icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' },
  { label: 'کۆی ڕووبەر',   value: totalAreaFormatted.value,    color: 'purple',  icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>' },
  { label: 'شوێنەکان',     value: uniqueLocations.value,       color: 'rose',    icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' },
]);

const pricePerSqm = computed(() => {
  if (!selectedMulk.value?.price || !selectedMulk.value?.Rwbar) return '—';
  return commaNumber(Math.round(selectedMulk.value.price / selectedMulk.value.Rwbar)) + '$';
});

// ── Helpers ───────────────────────────────────────────────────────────────────
function customerStageBreakdown(customers) {
  if (!customers?.length) return {};
  const b = {};
  for (const c of customers) {
    const s = c.current_stage ?? 'unknown';
    b[s] = (b[s] ?? 0) + 1;
  }
  const sorted = {};
  for (const s of STAGE_ORDER) { if (b[s]) sorted[s] = b[s]; }
  for (const s of Object.keys(b)) { if (!sorted[s]) sorted[s] = b[s]; }
  return sorted;
}

function customerCountAccent(count) {
  if (!count)     return '#d1d5db';
  if (count >= 5) return '#059669';
  if (count >= 3) return '#0284c7';
  return '#d97706';
}

function cleanPhone(phone) {
  return (phone ?? '').replace(/\D/g, '');
}

function formatDate(d) {
  if (!d) return 'N/A';
  return new Date(d).toLocaleString('ckb-IQ', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
  });
}

function getTimeBetween(start, end = null) {
  if (!start) return '—';
  const ms   = (end ? new Date(end) : new Date()) - new Date(start);
  if (ms < 0) return '—';
  const days  = Math.floor(ms / 86_400_000);
  const hours = Math.floor((ms / 3_600_000) % 24);
  const mins  = Math.floor((ms / 60_000) % 60);
  return `${days} ڕۆژ ${hours} س ${mins} د`;
}

// ── Data Fetching ─────────────────────────────────────────────────────────────
function fetchPage() {
  loading.value = true;
  router.get('/mulks-dashboard', { ...searchQuery.value }, {
    preserveScroll: true, preserveState: false, replace: true,
    onFinish: () => { loading.value = false; },
    onError:  () => { loading.value = false; },
  });
  refreshTimer = setTimeout(fetchPage, 600_000);
}

watch(searchQuery, () => { clearTimeout(refreshTimer); fetchPage(); }, { deep: true });
onMounted(()   => { refreshTimer = setTimeout(fetchPage, 600_000); });
onUnmounted(() => { clearTimeout(refreshTimer); });
</script>

<style scoped>
/* ════════════════════════════════════════
   ROOT
════════════════════════════════════════ */
.dashboard-root {
  background: #f8f7f4;
  min-height: 100vh;
  padding: 1.25rem 0 3rem;
}
.dashboard-container {
  max-width: 100%;
  margin: 0 auto;
  padding: 0 1rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}
@media (min-width: 640px)  { .dashboard-container { padding: 0 1.5rem; } }
@media (min-width: 1024px) { .dashboard-container { padding: 0 2rem; } }

/* ════════════════════════════════════════
   HEADER PILLS
════════════════════════════════════════ */
.header-stat-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.375rem 0.75rem;
  border-radius: 9999px;
  border: 1px solid;
}
.header-stat-pill.emerald { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
.header-stat-pill.blue    { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }

/* ════════════════════════════════════════
   LOADING
════════════════════════════════════════ */
.loading-overlay {
  position: fixed; inset: 0;
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  z-index: 9999;
}
.loading-card {
  background: #fff;
  border-radius: 1rem;
  padding: 2rem;
  display: flex; flex-direction: column; align-items: center; gap: 1rem;
  box-shadow: 0 20px 60px rgba(0,0,0,0.12);
  font-size: 0.875rem; color: #64748b; font-weight: 600;
}
.spinner-ring {
  width: 3rem; height: 3rem;
  border: 3px solid #d1fae5;
  border-top-color: #059669;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.fade-overlay-enter-active, .fade-overlay-leave-active { transition: opacity 0.2s; }
.fade-overlay-enter-from, .fade-overlay-leave-to { opacity: 0; }

/* ════════════════════════════════════════
   KPI STRIP
════════════════════════════════════════ */
.kpi-strip {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
}
@media (min-width: 640px)  { .kpi-strip { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1024px) { .kpi-strip { grid-template-columns: repeat(5, 1fr); } }

.kpi-card {
  background: #fff;
  border-radius: 1rem;
  border: 1px solid #f1f5f9;
  padding: 0.875rem 1rem;
  display: flex; align-items: center; gap: 0.75rem;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  transition: transform 0.15s, box-shadow 0.15s;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }

.kpi-icon {
  width: 2.25rem; height: 2.25rem;
  border-radius: 0.625rem;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.kpi-icon.emerald { background: #ecfdf5; color: #059669; }
.kpi-icon.blue    { background: #eff6ff; color: #0284c7; }
.kpi-icon.gold    { background: #fffbeb; color: #d97706; }
.kpi-icon.purple  { background: #f5f3ff; color: #7c3aed; }
.kpi-icon.rose    { background: #fff1f2; color: #be123c; }

.kpi-label { font-size: 0.65rem; color: #94a3b8; font-weight: 600; margin-bottom: 0.125rem; }
.kpi-value { font-size: 1.125rem; font-weight: 800; color: #1e293b; font-family: 'NRT', sans-serif; }

/* ════════════════════════════════════════
   FILTER BAR
════════════════════════════════════════ */
.filter-bar {
  background: #fff;
  border-radius: 1rem;
  border: 1px solid #f1f5f9;
  padding: 1rem 1.25rem;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.filter-row {
  display: flex; flex-wrap: wrap;
  align-items: flex-end; gap: 0.75rem;
}
.filter-group { display: flex; flex-direction: column; gap: 0.25rem; }
.filter-label { font-size: 0.65rem; color: #94a3b8; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; }

.search-wrap { position: relative; flex: 1; min-width: 180px; }
.search-icon { position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: #94a3b8; }
.search-input {
  width: 100%;
  border: 1.5px solid #e2e8f0;
  border-radius: 0.625rem;
  padding: 0.45rem 2.25rem 0.45rem 0.75rem;
  font-size: 0.8125rem;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
  background: #f8fafc; color: #1e293b;
}
.search-input:focus { border-color: #059669; box-shadow: 0 0 0 3px rgba(5,150,105,0.1); background: #fff; }

.view-toggle { display: flex; gap: 0.25rem; background: #f1f5f9; border-radius: 0.5rem; padding: 0.25rem; }
.toggle-btn {
  padding: 0.375rem 0.625rem; border-radius: 0.375rem;
  transition: all 0.15s; color: #64748b;
  border: none; background: transparent; cursor: pointer;
}
.toggle-btn.active { background: #fff; color: #059669; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }

.active-filters {
  display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem;
  padding-top: 0.75rem; margin-top: 0.75rem; border-top: 1px solid #f1f5f9;
}
.filter-chip {
  display: inline-flex; align-items: center; gap: 0.25rem;
  background: #f1f5f9; color: #475569;
  border: none; cursor: pointer;
  font-size: 0.7rem; font-weight: 600; font-family: 'NRT', sans-serif;
  border-radius: 9999px; padding: 0.25rem 0.625rem;
  transition: all 0.15s;
}
.filter-chip:hover { background: #fee2e2; color: #dc2626; }
.filter-chip.danger { background: #fee2e2; color: #dc2626; }

.slide-down-enter-active, .slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-8px); }

/* ════════════════════════════════════════
   STAGE LEGEND
════════════════════════════════════════ */
.stage-legend { display: flex; flex-wrap: wrap; gap: 0.5rem; }
.stage-pill {
  display: inline-flex; align-items: center; gap: 0.375rem;
  padding: 0.35rem 0.875rem; border-radius: 9999px;
  color: #fff; font-size: 0.72rem; font-weight: 700;
  box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}
.stage-pill-count {
  background: rgba(255,255,255,0.25);
  border-radius: 9999px; padding: 0.05rem 0.5rem; font-size: 0.65rem;
}

/* ════════════════════════════════════════
   EMPTY STATE
════════════════════════════════════════ */
.empty-state {
  background: #fff; border: 2px dashed #e2e8f0;
  border-radius: 1.25rem; padding: 4rem 2rem; text-align: center;
}
.empty-icon { font-size: 3.5rem; margin-bottom: 0.75rem; }
.empty-title { font-size: 1rem; font-weight: 700; color: #475569; }
.empty-sub { font-size: 0.8rem; color: #94a3b8; margin-top: 0.25rem; }

/* ════════════════════════════════════════
   MULK GRID
════════════════════════════════════════ */
.mulk-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1rem;
}
@media (min-width: 640px)  { .mulk-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .mulk-grid { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1280px) { .mulk-grid { grid-template-columns: repeat(4, 1fr); } }

.mulk-card {
  background: #fff; border-radius: 1.125rem;
  border: 1px solid #f1f5f9;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  cursor: pointer; overflow: hidden;
  display: flex; flex-direction: column;
  transition: transform 0.18s ease, box-shadow 0.18s ease;
  animation: cardIn 0.35s ease both;
  position: relative;
}
@keyframes cardIn {
  from { opacity: 0; transform: translateY(14px); }
  to   { opacity: 1; transform: translateY(0); }
}
.mulk-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,0.1); }

.card-accent { height: 5px; width: 100%; flex-shrink: 0; }
.card-body { padding: 0.875rem 1rem 0.75rem; display: flex; flex-direction: column; gap: 0.5rem; flex: 1; }

.card-header-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; }
.card-name-wrap { display: flex; align-items: flex-start; gap: 0.375rem; flex: 1; min-width: 0; }
.card-house-icon { font-size: 0.9rem; flex-shrink: 0; margin-top: 1px; }
.card-name { font-size: 0.8125rem; font-weight: 800; color: #1e293b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

.cust-badge-wrap { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
.cust-badge {
  display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem;
  border-radius: 0.5rem; padding: 0.25rem 0.5rem;
  color: #fff; font-size: 0.7rem; font-weight: 800;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.cust-badge-label { font-size: 0.6rem; color: #94a3b8; margin-top: 2px; }

.card-meta-row { display: flex; flex-wrap: wrap; gap: 0.375rem; }
.meta-chip {
  display: inline-flex; align-items: center; gap: 0.25rem;
  font-size: 0.7rem; font-weight: 700;
  padding: 0.2rem 0.5rem; border-radius: 0.375rem; border: 1px solid;
}
.meta-chip.gold   { background: #fffbeb; color: #b45309; border-color: #fde68a; }
.meta-chip.blue   { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
.meta-chip.gray   { background: #f8fafc; color: #475569; border-color: #e2e8f0; }

.card-info-row { display: flex; align-items: center; flex-wrap: wrap; gap: 0.375rem; font-size: 0.7rem; color: #64748b; }
.loc-dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #059669; margin-left: 2px; flex-shrink: 0; }
.info-item { display: flex; align-items: center; gap: 0.25rem; }
.info-sep { color: #cbd5e1; }
.info-item.muted { color: #94a3b8; }

.building-chips { display: flex; flex-wrap: wrap; gap: 0.25rem; }
.b-chip { font-size: 0.65rem; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 0.15rem 0.5rem; }

.card-phone-row { display: flex; align-items: center; gap: 0.5rem; padding-top: 0.375rem; }
.phone-num { font-size: 0.65rem; color: #94a3b8; font-family: 'NRT', sans-serif; }

.phone-action {
  display: inline-flex; align-items: center; justify-content: center;
  width: 1.625rem; height: 1.625rem; border-radius: 0.5rem;
  font-size: 0.7rem; font-weight: 600;
  transition: all 0.15s; border: none; cursor: pointer; text-decoration: none;
}
.phone-action.tel           { background: #eff6ff; color: #1d4ed8; }
.phone-action.tel:hover     { background: #dbeafe; }
.phone-action.whatsapp      { background: #f0fdf4; color: #15803d; }
.phone-action.whatsapp:hover{ background: #dcfce7; }
.phone-action.viber         { background: #f5f3ff; color: #6d28d9; }
.phone-action.viber:hover   { background: #ede9fe; }
.phone-action.sm { width: 1.375rem; height: 1.375rem; border-radius: 0.375rem; }

.card-footer { padding-top: 0.5rem; border-top: 1px solid #f8fafc; margin-top: auto; }
.stage-bar { display: flex; height: 4px; border-radius: 9999px; overflow: hidden; gap: 1px; margin-bottom: 0.375rem; }
.stage-tags { display: flex; flex-wrap: wrap; gap: 0.25rem; margin-bottom: 0.375rem; }
.s-tag { display: inline-flex; align-items: center; gap: 0.2rem; font-size: 0.6rem; font-weight: 700; color: #fff; padding: 0.15rem 0.5rem; border-radius: 9999px; }
.dot-row { display: flex; flex-wrap: wrap; gap: 0.25rem; align-items: center; }
.cust-dot { display: inline-block; width: 1rem; height: 1rem; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.15); cursor: pointer; transition: transform 0.1s; }
.cust-dot:hover { transform: scale(1.3); }
.dot-more { display: inline-flex; align-items: center; justify-content: center; width: 1rem; height: 1rem; background: #e2e8f0; color: #475569; font-size: 0.55rem; font-weight: 800; border-radius: 50%; }
.no-customers { font-size: 0.7rem; color: #cbd5e1; font-style: italic; padding-top: 0.5rem; margin-top: auto; border-top: 1px solid #f8fafc; }

.card-hover-overlay {
  position: absolute; bottom: 0; left: 0; right: 0;
  background: linear-gradient(to top, rgba(5,150,105,0.95), rgba(5,150,105,0));
  color: #fff; font-size: 0.7rem; font-weight: 700;
  padding: 1.25rem 1rem 0.5rem;
  display: flex; align-items: flex-end; justify-content: center; gap: 0.375rem;
  opacity: 0; transition: opacity 0.2s; pointer-events: none;
}
.mulk-card:hover .card-hover-overlay { opacity: 1; }

/* ════════════════════════════════════════
   LIST VIEW
════════════════════════════════════════ */
.list-wrap { background: #fff; border-radius: 1rem; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
.mulk-table { width: 100%; font-size: 0.8rem; border-collapse: collapse; }
.mulk-table thead tr { background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
.mulk-table th { padding: 0.75rem 1rem; text-align: right; font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; }
.table-row { border-bottom: 1px solid #f8fafc; cursor: pointer; transition: background 0.12s; }
.table-row:hover { background: #f0fdf4; }
.mulk-table td { padding: 0.75rem 1rem; vertical-align: middle; }
.table-bar { width: 3px; height: 1.75rem; border-radius: 9999px; flex-shrink: 0; }
.price-tag { font-size: 0.75rem; font-weight: 800; color: #b45309; }
.table-cust-badge { display: inline-flex; align-items: center; justify-content: center; width: 1.625rem; height: 1.625rem; border-radius: 50%; font-size: 0.7rem; font-weight: 800; color: #fff; }

/* ════════════════════════════════════════
   DIALOG
════════════════════════════════════════ */
:deep(.mulk-dialog) { border-radius: 1.25rem; overflow: hidden; }
:deep(.mulk-dialog .el-dialog__header) { padding: 0; }
:deep(.mulk-dialog .el-dialog__body) { padding: 0; max-height: 80vh; overflow-y: auto; }

.dialog-header-custom {
  background: linear-gradient(135deg, #ec8d44 0%, #e07a2c 100%);
  padding: 1.25rem 1.5rem 0;
}
.dialog-title-row { display: flex; align-items: center; gap: 0.875rem; margin-bottom: 1rem; }
.dialog-house { font-size: 2rem; }
.dialog-mulk-name { font-size: 1.25rem; font-weight: 800; color: #fff; margin: 0; }
.dialog-mulk-sub { font-size: 0.75rem; color: rgba(255,255,255,0.85); margin-top: 0.25rem; display: flex; flex-wrap: wrap; gap: 0.25rem; align-items: center; }

.dialog-tabs { display: flex; gap: 0.25rem; }
.dialog-tab {
  position: relative; padding: 0.625rem 1.125rem;
  font-size: 0.8rem; font-weight: 700;
  color: rgba(255,255,255,0.6); background: transparent;
  border: none; cursor: pointer; border-radius: 0.5rem 0.5rem 0 0;
  transition: all 0.15s;
  display: flex; align-items: center; gap: 0.375rem;
}
.dialog-tab:hover { color: rgba(255,255,255,0.85); }
.dialog-tab.active { color: #fff; background: rgba(255,255,255,0.12); }
.dialog-tab.active::after {
  content: ''; position: absolute; bottom: 0; left: 0; right: 0;
  height: 2px; background: #fff; border-radius: 1px 1px 0 0;
}
.tab-badge {
  background: rgba(255,255,255,0.25); color: #fff;
  font-size: 0.6rem; font-weight: 800;
  padding: 0.05rem 0.4rem; border-radius: 9999px;
}

.dialog-body { padding: 1.5rem; background: #f8f7f4; }
.tab-panel { animation: tabIn 0.2s ease; }
@keyframes tabIn { from { opacity: 0; transform: translateX(8px); } to { opacity: 1; transform: translateX(0); } }

/* Info KPI */
.info-kpi-row {
  display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;
}
@media (min-width: 640px)  { .info-kpi-row { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1024px) { .info-kpi-row { grid-template-columns: repeat(6, 1fr); } }

.info-kpi { background: #fff; border-radius: 0.875rem; border: 1px solid #f1f5f9; padding: 0.875rem; }
.info-kpi-label { font-size: 0.65rem; color: #94a3b8; font-weight: 700; margin-bottom: 0.375rem; }
.info-kpi-val { font-size: 1rem; font-weight: 800; color: #1e293b; font-family: 'NRT', sans-serif; }
.info-kpi-val.gold { color: #b45309; }
.info-kpi-val.blue { color: #1d4ed8; }

/* Detail Section */
.detail-section { background: #fff; border-radius: 0.875rem; border: 1px solid #f1f5f9; padding: 1rem 1.25rem; }
.section-title { font-size: 0.8rem; font-weight: 800; color: #334155; display: flex; align-items: center; gap: 0.375rem; }
.detail-chip { display: inline-flex; align-items: center; gap: 0.375rem; background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; font-size: 0.75rem; border-radius: 0.5rem; padding: 0.35rem 0.75rem; }
.detail-chip strong { color: #1e293b; }

/* Contact */
.contact-row { display: flex; flex-wrap: wrap; gap: 1rem; }
.contact-block { display: flex; flex-direction: column; gap: 0.5rem; }
.contact-label { font-size: 0.65rem; color: #94a3b8; font-weight: 700; }
.contact-actions { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.contact-num { font-family: 'NRT', sans-serif; font-weight: 700; color: #1e293b; font-size: 0.875rem; }
.contact-btn {
  display: inline-flex; align-items: center; gap: 0.375rem;
  font-size: 0.75rem; font-weight: 700;
  padding: 0.4rem 0.875rem; border-radius: 0.5rem;
  text-decoration: none; transition: all 0.15s; border: 1px solid transparent;
}
.contact-btn.tel           { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
.contact-btn.tel:hover     { background: #dbeafe; }
.contact-btn.whatsapp      { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.contact-btn.whatsapp:hover{ background: #dcfce7; }
.contact-btn.viber         { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
.contact-btn.viber:hover   { background: #ede9fe; }
.contact-btn.map           { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
.contact-btn.map:hover     { background: #ffedd5; }
.contact-btn.facebook      { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
.contact-btn.facebook:hover{ background: #dbeafe; }

.note-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.625rem; padding: 0.875rem; font-size: 0.8125rem; color: #78350f; line-height: 1.6; }
.images-grid { display: flex; flex-wrap: wrap; gap: 0.625rem; }
.gallery-img { width: 110px; height: 110px; border-radius: 0.75rem; border: 1px solid #f1f5f9; object-fit: cover; }

/* Customer Tab */
.stage-summary-row { display: flex; flex-wrap: wrap; gap: 0.75rem; }
.stage-summary-box {
  flex: 1; min-width: 100px;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  border-radius: 0.875rem; padding: 1rem 0.75rem; color: #fff;
  cursor: pointer; transition: transform 0.15s, box-shadow 0.15s;
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}
.stage-summary-box:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }

.customer-progress-bar { display: flex; height: 8px; border-radius: 9999px; overflow: hidden; gap: 2px; background: #f1f5f9; }

.cust-filter-row { display: flex; flex-wrap: wrap; gap: 0.5rem; }
.cust-filter-btn {
  padding: 0.375rem 0.875rem; font-size: 0.75rem; font-weight: 700;
  border: 1.5px solid #e2e8f0; border-radius: 9999px;
  background: #fff; color: #475569; cursor: pointer; transition: all 0.15s;
}
.cust-filter-btn:hover { border-color: #94a3b8; }
.cust-filter-btn.active-all { background: #1e293b; color: #fff; border-color: #1e293b; }

.cust-grid {
  display: grid; grid-template-columns: repeat(1, 1fr); gap: 0.875rem;
}
@media (min-width: 640px)  { .cust-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .cust-grid { grid-template-columns: repeat(3, 1fr); } }

.cust-card {
  background: #fff; border-radius: 1rem; border: 1px solid #f1f5f9;
  overflow: hidden; transition: box-shadow 0.15s; box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.cust-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
.cust-color-band { height: 6px; width: 100%; }
.cust-card-body { padding: 0.875rem; display: flex; flex-direction: column; gap: 0.5rem; }

.cust-name-row { display: flex; align-items: center; gap: 0.5rem; }
.cust-color-dot { width: 0.875rem; height: 0.875rem; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.15); flex-shrink: 0; }
.cust-name { font-weight: 800; color: #1e293b; font-size: 0.875rem; flex: 1; }
.backed-badge { font-size: 0.6rem; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 9999px; padding: 0.1rem 0.5rem; font-weight: 700; flex-shrink: 0; }

.cust-stage-badge {
  display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem;
  padding: 0.5rem; border-radius: 0.625rem; color: #fff; font-size: 0.8rem; font-weight: 800;
}

/* ✅ Customer contact row — phone number + action buttons */
.cust-contact-row {
  display: flex; align-items: center; justify-content: space-between;
  background: #f8fafc; border-radius: 0.5rem; padding: 0.375rem 0.625rem;
  gap: 0.5rem;
}
.cust-phone-num {
  font-size: 0.72rem; color: #475569;
  font-family: 'NRT', sans-serif; font-weight: 600;
  flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}

.cust-price-row { display: flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; font-weight: 600; color: #b45309; background: #fffbeb; border-radius: 0.5rem; padding: 0.375rem 0.625rem; }

.cust-timing { background: #f8fafc; border-radius: 0.5rem; padding: 0.5rem 0.625rem; display: flex; flex-direction: column; gap: 0.25rem; }
.timing-row { display: flex; align-items: center; gap: 0.5rem; font-size: 0.7rem; color: #64748b; }
.timing-row.duration { color: #1d4ed8; font-weight: 700; }
.timing-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.timing-dot.start { background: #059669; }
.timing-dot.end   { background: #dc2626; }

.cust-note { font-size: 0.75rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.5rem; padding: 0.5rem 0.625rem; color: #78350f; line-height: 1.5; }
.cust-note.slate { background: #f8fafc; border-color: #e2e8f0; color: #475569; }

.empty-customers { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; padding: 3rem; color: #94a3b8; font-size: 0.875rem; }
</style>