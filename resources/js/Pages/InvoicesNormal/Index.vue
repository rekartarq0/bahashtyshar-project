<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive, computed } from "vue";
import { ElButton, ElMessage } from 'element-plus';
import { router } from "@inertiajs/vue3";

import InvoicesNormal from './InvoicesNormalTable.vue';
import { watch } from 'vue';

const props = defineProps({
  data: Array, // Ensure type matches Laravel's response
  errors: Object,
  filters: Object,
  typeProjects: Array, // ✅ add this

});

const today = new Date().toISOString().split('T')[0];


const form = reactive({
  id: null,
  molat_no: '31554',
  name_one: null,
  penas_one: null,
  phone_one: null,
  name_two: null,
  penas_two: null,
  phone_two: null,
  mulk_one: null,
  mulk_type: null,
  mulk_no: null,
  mulk_garak: null,
  mulk_metter: null,
  nrxi_froshtn: null,
  nrxi_peshaki: null,
  nrxi_mawa: null,
  peshaki_layaniyakam: null,
  nrxi_pashimani_one: null,
  nrxi_pashimani_two: null,
  datecholkrdn_year: null,
  from_datecholkrdn: null,
  to_datecholkrdn: null,
  krey_mangana: null,
  skay_panjara: null,
  pankay_asman: null,
  sngi_cheshtnga: null,
  dukalkesh: null,
  naw_kwlenar: null,
  dastshor: null,
  tankiaw: null,
  garak_one: null,
  garak_two: null,
  job_one: null,
  job_two: null,
  shahid_one: null,
  shahid_two: null,
  date_invoice: today,
  // ✅ NEW
  emara: null,
  qat: null,
  zhmarai_shwqa: null,

  phone_one_shahid: null,
  phone_two_shahid: null,
  zhmarai_rozhi_cholkrdn: null,
  mawai_katy_cholkrdn: null,

  krey_mangana_currency: 'IQD',
});

const mamalaTypes = [
  { label: 'خانوو', value: 'خانوو' },
  { label: 'زەوی', value: 'زەوی' },
  { label: 'دوکان/مەخزەن', value: 'دوکان/مەخزەن' },
];


const ErrorForms = ref({})



function submit(event) {
  event.preventDefault();

  const method = form.id ? 'put' : 'post'; // Determine HTTP method
  const url = form.id ? `/invoicenormal/${form.id}` : '/invoicenormal'; // Determine URL

  router[method](url, form, {
    onError: (errors) => {
      console.log(errors);

      ErrorForms.value = errors;
    },
    onSuccess: (success) => {
      ElMessage({
        center: false,
        message: success.props.flash?.message || 'Operation completed successfully.',
        type: 'success',
        customClass: 'custom-confirm-box', // Add a custom class
        duration: 5000,
      });
      resetForm();
    },
  });
}

function edit(id) {
  router.get(`/invoicenormal/${id}`, {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      const invoice = page.props.flash?.data;
      if (!invoice) return;

      // Main
      form.id = invoice.id ?? null;
      form.molat_no = invoice.molat_no ?? null;
      form.date_invoice = invoice.date_invoice ?? null;

      // First Person
      form.name_one = invoice.name_one ?? null;
      form.penas_one = invoice.penas_one ?? null;
      form.phone_one = invoice.phone_one ?? null;
      form.job_one = invoice.job_one ?? null;
      form.garak_one = invoice.garak_one ?? null;
      form.shahid_one = invoice.shahid_one ?? null;

      // Second Person
      form.name_two = invoice.name_two ?? null;
      form.penas_two = invoice.penas_two ?? null;
      form.phone_two = invoice.phone_two ?? null;
      form.job_two = invoice.job_two ?? null;
      form.garak_two = invoice.garak_two ?? null;
      form.shahid_two = invoice.shahid_two ?? null;

      // Property Info
      form.mulk_one = invoice.mulk_one ?? null;
      form.mulk_type = invoice.mulk_type ?? null;
      form.mulk_no = invoice.mulk_no ?? null;
      form.mulk_garak = invoice.mulk_garak ?? null;
      form.mulk_metter = invoice.mulk_metter ?? null;

      // Prices
      form.nrxi_froshtn = invoice.nrxi_froshtn ?? null;
      form.nrxi_peshaki = invoice.nrxi_peshaki ?? null;
      form.nrxi_mawa = invoice.nrxi_mawa ?? null;
      form.peshaki_layaniyakam = invoice.peshaki_layaniyakam ?? null;
      form.nrxi_pashimani_one = invoice.nrxi_pashimani_one ?? null;
      form.nrxi_pashimani_two = invoice.nrxi_pashimani_two ?? null;

      // Dates
      form.datecholkrdn_year = invoice.datecholkrdn_year ?? null;
      form.from_datecholkrdn = invoice.from_datecholkrdn ?? null;
      form.to_datecholkrdn = invoice.to_datecholkrdn ?? null;

      // Rent / Monthly
      form.krey_mangana = invoice.krey_mangana ?? null;
      form.krey_mangana_currency = invoice.krey_mangana_currency ?? 'IQD';

      // Property Details (options)
      form.skay_panjara = invoice.skay_panjara ?? null;
      form.pankay_asman = invoice.pankay_asman ?? null;
      form.sngi_cheshtnga = invoice.sngi_cheshtnga ?? null;
      form.dukalkesh = invoice.dukalkesh ?? null;
      form.naw_kwlenar = invoice.naw_kwlenar ?? null;
      form.dastshor = invoice.dastshor ?? null;
      form.tankiaw = invoice.tankiaw ?? null;

      form.emara = invoice.emara ?? null;
      form.qat = invoice.qat ?? null;
      form.zhmarai_shwqa = invoice.zhmarai_shwqa ?? null;

      form.phone_one_shahid = invoice.phone_one_shahid ?? null;
      form.phone_two_shahid = invoice.phone_two_shahid ?? null;
      form.zhmarai_rozhi_cholkrdn = invoice.zhmarai_rozhi_cholkrdn ?? null;
      form.mawai_katy_cholkrdn = invoice.mawai_katy_cholkrdn ?? null;
    }
  });
}


const dialogvisablity = ref(false);
function printInvoices(id) {
  router.get(`/invoicenormal/${id}`, {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      const invoice = page.props.flash?.data;
      if (!invoice) return;

      // Auto fill all form fields
      Object.keys(form).forEach((key) => {
        form[key] = invoice[key] ?? null;
      });

      // Open dialog
      dialogvisablity.value = true;
    }
  });
}
function resetForm() {
  Object.keys(form).forEach((key) => {
    form[key] = null;
  });


  ErrorForms.value = {};
  dialogvisablity.value = false;
}

watch(
  () => [form.nrxi_froshtn, form.nrxi_peshaki],
  ([froshtn, peshaki]) => {
    const total = parseFloat(froshtn) || 0;
    const paid = parseFloat(peshaki) || 0;

    form.nrxi_mawa = total - paid;
  }
);

const isShuqah = computed(() => {
  return form.mulk_type === 'شووقە';
});

watch(
  () => form.mulk_type,
  () => {
    if (!isShuqah.value) {
      form.emara = null;
      form.qat = null;
      form.zhmarai_shwqa = null;
    }
  }
);

watch(
  () => form.zhmarai_rozhi_cholkrdn,
  (days) => {
    const numDays = parseInt(days) || 0;

    if (numDays > 0) {
      const today = new Date();

      // set from_date = today
      const fromDate = new Date(today);
      form.from_datecholkrdn = fromDate.toISOString().split('T')[0];

      // set to_date = today + days
      const toDate = new Date(today);
      toDate.setDate(toDate.getDate() + numDays);

      form.to_datecholkrdn = toDate.toISOString().split('T')[0];
    } else {
      form.from_datecholkrdn = null;
      form.to_datecholkrdn = null;
    }
  }
);

watch(
  () => form.from_datecholkrdn,
  (newFromDate) => {
    const numDays = parseInt(form.zhmarai_rozhi_cholkrdn) || 0;

    if (newFromDate && numDays > 0) {
      const fromDate = new Date(newFromDate);

      const toDate = new Date(fromDate);
      toDate.setDate(toDate.getDate() + numDays);

      form.to_datecholkrdn = toDate.toISOString().split('T')[0];
    }
  }
);

</script>

<template>
  <AppLayout title="Projects Index">
    <template #header>
      <div dir="rtl" class="flex w-full items-center justify-between">
        <div class="flex items-center justify-between w-full">
          <h4 dir="rtl" class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black dark:text-black pl-1 sm:pl-2">
            دەرخستەی ڕاپۆرتی ئاسایی
          </h4>

        </div>

      </div>
    </template>

    <div class="bg-white rounded shadow-md px-8 py-3 m-4">
      <div class="font-droidkufi w-full mx-auto" dir="rtl">
        <h2 class="text-lg text-danger font-extrabold mb-4">فۆڕمی دروستکردنی دەرخستە</h2>

        <el-form :model="form">

        <div class="flex">
  <div class="w-1/3">
    <el-form-item :error="ErrorForms['molat_no']" style="width: 75%;" label-position="left" label="ژمارەی مۆڵەت"
      label-width="120px">
      <el-input type="text" clearable v-model="form.molat_no" />
    </el-form-item>
  </div>

  <div class="w-1/3">
    <el-form-item :error="ErrorForms['date_invoice']" style="width: 75%;" label-position="left" label="بەروار"
      label-width="120px">
      <el-input type="date" clearable v-model="form.date_invoice" />
    </el-form-item>
  </div>
</div>


          <p class="font-droidkufi text-sm text-primary ">زانیاری لایەنی یەکەم</p>
          <div class="flex gap-1 items-center justify-center">
            <!-- name one -->
            <el-form-item :error="ErrorForms['name_one']" style="width: 75%;" label-position="left" label="ناو"
              label-width="75px">
              <el-input type="text" clearable v-model="form.name_one" />
            </el-form-item>

            <!-- penas one -->
            <el-form-item :error="ErrorForms['penas_one']" style="width: 75%;" label-position="left" label="ژ.پێناس "
              label-width="75px">
              <el-input type="text" clearable v-model="form.penas_one" />
            </el-form-item>
            <!-- phone one -->
            <el-form-item :error="ErrorForms['phone_one']" style="width: 75%;" label-position="left" label="ژ.تەلەفۆن "
              label-width="75px">
              <el-input type="text" clearable v-model="form.phone_one" />
            </el-form-item>

            <!-- garak one -->
            <el-form-item :error="ErrorForms['garak_one']" style="width: 75%;" label-position="left" label="گەڕەک "
              label-width="75px">
              <el-input type="text" clearable v-model="form.garak_one" />
            </el-form-item>

            <!-- job_one -->
            <el-form-item :error="ErrorForms['code_one']" style="width: 75%;" label-position="left" label="پیشە "
              label-width="75px">
              <el-input type="text" clearable v-model="form.job_one" />
            </el-form-item>
            <!-- shahid_one -->
            <el-form-item :error="ErrorForms['shahid_one']" style="width: 75%;" label-position="left" label="شاهید "
              label-width="75px">
              <el-input type="text" clearable v-model="form.shahid_one" />
            </el-form-item>
            <!-- phone_one_shahid -->
            <el-form-item :error="ErrorForms['phone_one_shahid']" style="width: 75%;" label-position="left" label="ژ.ت شاهید "
              label-width="75px">
              <el-input type="text" clearable v-model="form.phone_one_shahid" />
            </el-form-item>

          </div>

          <p class="font-droidkufi text-sm text-primary ">زانیاری لایەنی دووەم</p>
          <div class="flex gap-4 items-center justify-center">
            <!-- name two -->
            <el-form-item :error="ErrorForms['name_two']" style="width: 75%;" label-position="left" label="ناو"
              label-width="75px">
              <el-input type="text" clearable v-model="form.name_two" />
            </el-form-item>

            <!-- penas two -->
            <el-form-item :error="ErrorForms['penas_two']" style="width: 75%;" label-position="left" label="ژ.پێناس "
              label-width="75px">
              <el-input type="text" clearable v-model="form.penas_two" />
            </el-form-item>
            <!-- phone two -->
            <el-form-item :error="ErrorForms['phone_two']" style="width: 75%;" label-position="left" label="ژ.تەلەفۆن "
              label-width="75px">
              <el-input type="text" clearable v-model="form.phone_two" />
            </el-form-item>

            <!-- garak two -->
            <el-form-item :error="ErrorForms['garak_two']" style="width: 75%;" label-position="left" label="گەڕەک "
              label-width="75px">
              <el-input type="text" clearable v-model="form.garak_two" />
            </el-form-item>

            <!-- job_two -->
            <el-form-item :error="ErrorForms['code_two']" style="width: 75%;" label-position="left" label="پیشە "
              label-width="75px">
              <el-input type="text" clearable v-model="form.job_two" />
            </el-form-item>
            <!-- shahid_two -->
            <el-form-item :error="ErrorForms['shahid_two']" style="width: 75%;" label-position="left" label="شاهید "
              label-width="75px">
              <el-input type="text" clearable v-model="form.shahid_two" />
            </el-form-item>
            <!-- phone_two_shahid -->
            <el-form-item :error="ErrorForms['phone_two_shahid']" style="width: 75%;" label-position="left" label="ژ.ت شاهید "
              label-width="75px">
              <el-input type="text" clearable v-model="form.phone_two_shahid" />
              </el-form-item>
          </div>

          <p class="font-droidkufi text-sm text-primary ">ناوەڕۆکی مامەڵە</p>
         <div class="grid grid-cols-4 gap-x-4 gap-y-2 w-full">

  <el-form-item :error="ErrorForms['mulk_one']" class="w-full">
    <el-input placeholder="ناوی موڵک" type="text" clearable v-model="form.mulk_one" />
  </el-form-item>

  <el-form-item :error="ErrorForms['mulk_type']" class="w-full">
    <el-select v-model="form.mulk_type" placeholder="جۆری موڵک" filterable clearable style="width:100%">
      <el-option v-for="item in typeProjects" :key="item.name" :label="item.name" :value="item.name" />
    </el-select>
  </el-form-item>

  
  <!-- شووقە extra fields — only appear when isShuqah is true -->
  <template v-if="isShuqah">
    <el-form-item :error="ErrorForms['emara']" class="w-full">
      <el-input placeholder="عیمارە" v-model="form.emara" clearable />
    </el-form-item>

    <el-form-item :error="ErrorForms['qat']" class="w-full">
      <el-input placeholder="قات" v-model="form.qat" clearable />
    </el-form-item>

    <el-form-item :error="ErrorForms['zhmarai_shwqa']" class="w-full">
      <el-input placeholder="ژمارەی شووقە" v-model="form.zhmarai_shwqa" clearable />
    </el-form-item>
  </template>

  <el-form-item :error="ErrorForms['mulk_no']" class="w-full">
    <el-input type="text" placeholder="زنجیرەی موڵک" v-model="form.mulk_no" />
  </el-form-item>

  <el-form-item :error="ErrorForms['mulk_garak']" class="w-full">
    <el-input type="text" placeholder="گەڕەک" clearable v-model="form.mulk_garak" />
  </el-form-item>

  <el-form-item :error="ErrorForms['mulk_metter']" class="w-full">
    <el-input placeholder="پێوانەی موڵک" type="text" clearable v-model="form.mulk_metter" />
  </el-form-item>

  <el-form-item :error="ErrorForms['nrxi_froshtn']" class="w-full">
    <el-input placeholder="نرخی فرۆشتن" type="text" clearable v-model="form.nrxi_froshtn" />
  </el-form-item>

  <el-form-item :error="ErrorForms['nrxi_peshaki']" class="w-full">
    <el-input type="text" placeholder="پارەی پێشەکی موڵک" clearable v-model="form.nrxi_peshaki" />
  </el-form-item>


</div>

          <div class="flex gap-4 items-center justify-center">
            <el-form-item :error="ErrorForms['peshaki_layaniyakam']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="پارەی پێشەکی لایەنی یەکەم" clearable
                v-model="form.peshaki_layaniyakam" />
            </el-form-item>
            <el-form-item :error="ErrorForms['nrxi_pashimani_one']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="پارەی پەشیمانی لایەنی یەکەم" clearable
                v-model="form.nrxi_pashimani_one" />
            </el-form-item>
            <el-form-item :error="ErrorForms['nrxi_pashimani_two']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="پارەی پەشیمانی لایەنی دووەم" clearable
                v-model="form.nrxi_pashimani_two" />
            </el-form-item>
            <el-form-item :error="ErrorForms['mawai_katy_cholkrdn']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="ماوەی کاتی چۆڵکردن" clearable
                v-model="form.mawai_katy_cholkrdn" />
            </el-form-item>
            <el-form-item :error="ErrorForms['zhmarai_rozhi_cholkrdn']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="ژ.ڕۆژی چۆڵکردن" clearable
                v-model="form.zhmarai_rozhi_cholkrdn" />
            </el-form-item>

            

            <el-form-item :error="ErrorForms['from_datecholkrdn']" style="width: 75%;" label-position="left">
              <el-input type="date" placeholder="لە بەرواری" clearable v-model="form.from_datecholkrdn" />
            </el-form-item>
            <el-form-item :error="ErrorForms['to_datecholkrdn']" style="width: 75%;" label-position="left">
              <el-input type="date" placeholder="بۆ بەرواری" clearable v-model="form.to_datecholkrdn" />
            </el-form-item>
          </div>
         <div class="flex gap-4 items-start justify-start border-t-2 border-primary">

  <!-- Amount -->
  <el-form-item
    label="کرێی مانگانە"
    label-width="100px"
    :error="ErrorForms['krey_mangana']"
    style="width: 30%;"
    label-position="left"
  >
    <el-input
      type="number"
      placeholder="بڕی کرێ"
      clearable
      v-model="form.krey_mangana"
    />
  </el-form-item>

  <!-- Currency dropdown -->
  <el-form-item
    :error="ErrorForms['krey_mangana_currency']"
      label="دراوی کرێی مانگانە"
    label-width="150px"
    style="width: 30%;"
    label-position="left"
  >
    <el-select
      v-model="form.krey_mangana_currency"
      placeholder="دراو"
      style="width: 100%;"
    >
      <el-option label="IQD" value="IQD" />
      <el-option label="USD" value="USD" />
    </el-select>
  </el-form-item>

</div>
          <div class="flex gap-4 items-center justify-center">
            <el-form-item :error="ErrorForms['skay_panjara']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="سکەی پەنجەرە" clearable v-model="form.skay_panjara" />
            </el-form-item>
            <el-form-item :error="ErrorForms['pankay_asman']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="پانکەی ئاسمان" clearable v-model="form.pankay_asman" />
            </el-form-item>
            <el-form-item :error="ErrorForms['sngi_cheshtnga']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="سنگی چێشتنگە" clearable v-model="form.sngi_cheshtnga" />
            </el-form-item>
            <el-form-item :error="ErrorForms['dukalkesh']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="دووکەڵ کێش" clearable v-model="form.dukalkesh" />
            </el-form-item>
            <el-form-item :error="ErrorForms['naw_kwlenar']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="ناوکوڵێنەر" clearable v-model="form.naw_kwlenar" />
            </el-form-item>
            <el-form-item :error="ErrorForms['dastshor']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="دەست شۆر" clearable v-model="form.dastshor" />
            </el-form-item>
            <el-form-item :error="ErrorForms['tankiaw']" style="width: 75%;" label-position="left">
              <el-input type="text" placeholder="تانکی ئاو" clearable v-model="form.tankiaw" />
            </el-form-item>
          </div>





        </el-form>

        <div class="mt-6 flex justify-end">
          <el-button @click="submit" type="success" plain>
            زەخیرەکردنی زانییاری دەرخستە
          </el-button>
        </div>
      </div>

    </div>
    <InvoicesNormal :data="data" :datainvoices="{
      invoice: form,
    }" :dialogvisablity="dialogvisablity" :printInvoices="printInvoices" :resetform="resetForm" :destroy="destroy"
      :edit="edit" :openForm="openForm" />

  </AppLayout>
</template>
<style scoped>
.custom-input .el-input__inner {
  border-radius: 0 !important;
  padding: 0 !important;
}
</style>