<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive, watch } from "vue";
import { ElButton, ElMessage } from 'element-plus';
import { router } from "@inertiajs/vue3";

import InvoicesAsaishTable from './InvoicesAsaishTable.vue';
import { content } from 'html2canvas/dist/types/css/property-descriptors/content';
const props = defineProps({
  data: Array, // Ensure type matches Laravel's response
  errors: Object,
  filters: Object,
});


const form = reactive({
  id: null,

  invoice_year: null,
  invoice_no: null,
  created_at: null,

  to: null,
  matter: null,
  content: '',
  name_one: null,
  name_two: null,

  national_one: null,
  national_two: null,

  phone_one: null,
  phone_two: null,

  code_one: null,
  code_two: null,

  mamala_type: null,

  zhmarai_mulk: null,
  zhmarai_xanw: null,
  zhmarai_garak: null,
  zhmarai_kolan: null,

  note: null,
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
  const url = form.id ? `/invoiceAsaish/${form.id}` : '/invoiceAsaish'; // Determine URL

  router[method](url, {
    invoices: form,
  }, {
    onError: (errors) => {
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
  router.get(`/invoiceAsaish/${id}`, {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      const invoice = page.props.flash?.data;
      if (!invoice) return;

      // Basic
      form.id = invoice.id ?? null;
      form.to = invoice.to ?? null;
      form.invoice_year = invoice.invoice_year ?? null;
      form.invoice_no = invoice.invoice_no ?? null;
      form.matter = invoice.matter ?? null;
      form.created_at = invoice.created_at ?? null;
      form.content = invoice.content ?? form.content;

      // Persons
      form.name_one = invoice.name_one ?? null;
      form.name_two = invoice.name_two ?? null;

      form.national_one = invoice.national_one ?? null;
      form.national_two = invoice.national_two ?? null;

      form.phone_one = invoice.phone_one ?? null;
      form.phone_two = invoice.phone_two ?? null;

      form.code_one = invoice.code_one ?? null;
      form.code_two = invoice.code_two ?? null;

      // Mamala
      form.mamala_type = invoice.mamala_type ?? null;

      // Location numbers
      form.zhmarai_mulk = invoice.zhmarai_mulk ?? null;
      form.zhmarai_xanw = invoice.zhmarai_xanw ?? null;
      form.zhmarai_garak = invoice.zhmarai_garak ?? null;
      form.zhmarai_kolan = invoice.zhmarai_kolan ?? null;

      // Note
      form.note = invoice.note ?? null;

    }
  });
}


const dialogvisablity = ref(false);

function printInvoices(id) {
  router.get(`/invoiceAsaish/${id}`, {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      const invoice = page.props.flash?.data;
      if (!invoice) return;

      // Basic
      form.id = invoice.id ?? null;
      form.to = invoice.to ?? null;
      form.matter = invoice.matter ?? null;
      form.content = invoice.content ?? form.content;

      form.invoice_year = invoice.invoice_year ?? null;
      form.invoice_no = invoice.invoice_no ?? null;
      form.created_at = invoice.created_at ?? null;

      // Persons
      form.name_one = invoice.name_one ?? null;
      form.name_two = invoice.name_two ?? null;

      form.national_one = invoice.national_one ?? null;
      form.national_two = invoice.national_two ?? null;

      form.phone_one = invoice.phone_one ?? null;
      form.phone_two = invoice.phone_two ?? null;

      form.code_one = invoice.code_one ?? null;
      form.code_two = invoice.code_two ?? null;

      // Mamala
      form.mamala_type = invoice.mamala_type ?? null;

      // Location numbers
      form.zhmarai_mulk = invoice.zhmarai_mulk ?? null;
      form.zhmarai_xanw = invoice.zhmarai_xanw ?? null;
      form.zhmarai_garak = invoice.zhmarai_garak ?? null;
      form.zhmarai_kolan = invoice.zhmarai_kolan ?? null;

      // Note
      form.note = invoice.note ?? null;

      // Open dialog
      dialogvisablity.value = true;
    }
  });
}

function resetForm() {
  form.id = null;

  form.invoice_year = null;
  form.invoice_no = null;
  form.created_at = null;

  form.to = null;
  form.matter = null;
  form.content = '';
  form.name_one = null;
  form.name_two = null;

  form.national_one = null;
  form.national_two = null;

  form.phone_one = null;
  
  form.phone_two = null;

  form.code_one = null;
  form.code_two = null;

  form.mamala_type = null;

  form.zhmarai_mulk = null;
  form.zhmarai_xanw = null;
  form.zhmarai_garak = null;
  form.zhmarai_kolan = null;

  form.note = null;

  // Clear errors and close dialog
  ErrorForms.value = {};
  dialogvisablity.value = false;
}



</script>

<template>
  <AppLayout title="Projects Index">
    <template #header>
      <div dir="rtl" class="flex w-full items-center justify-between">
        <div class="flex items-center justify-between w-full">
          <h4 dir="rtl" class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black dark:text-black pl-1 sm:pl-2">
            دەرخستەی ڕاپۆرتی ئاسایش
          </h4>

        </div>

      </div>
    </template>

    <div class="bg-white rounded shadow-md px-8 py-3 m-4">
      <div class="font-droidkufi w-full mx-auto" dir="rtl">
        <h2 class="text-lg text-primary font-bold mb-4">فۆڕمی دروستکردنی دەرخستە</h2>

        <el-form :model="form">

          <div class="flex gap-4 items-center justify-center">
            <el-form-item :error="ErrorForms['invoices.to']" style="width: 100%;" label-position="left"
              label="بۆ بەڕێز / " label-width="100px">
              <el-input type="text" clearable v-model="form.to" />
            </el-form-item>
            <el-form-item :error="ErrorForms['invoices.matter']" style="width: 100%;" label-position="left"
              label="بابەت / " label-width="100px">
              <el-input type="text" clearable v-model="form.matter" />
            </el-form-item>

            <el-form-item style="width: 100%;" :error="ErrorForms['invoices.content']" label-position="left"
              label="ناوەڕۆک" label-width="100px">
              <el-input autosize type="textarea" clearable v-model="form.content" />
            </el-form-item>


          </div>

          <p class="font-droidkufi text-sm text-primary underline">زانیاری لایەنی یەکەم</p>
          <div class="flex gap-4 items-center justify-center">
            <!-- name one -->
            <el-form-item :error="ErrorForms['invoices.name_one']" style="width: 100%;" label-position="left"
              label="ناو" label-width="100px">
              <el-input type="text" clearable v-model="form.name_one" />
            </el-form-item>

            <!-- phone one -->
            <el-form-item :error="ErrorForms['invoices.phone_one']" style="width: 100%;" label-position="left"
              label="ژ.تەلەفۆن " label-width="100px">
              <el-input type="text" clearable v-model="form.phone_one" />
            </el-form-item>

            <!-- national one -->
            <el-form-item :error="ErrorForms['invoices.national_one']" style="width: 100%;" label-position="left"
              label="نەتەوە " label-width="100px">
              <el-input type="text" clearable v-model="form.national_one" />
            </el-form-item>

            <!-- code one -->
            <el-form-item :error="ErrorForms['invoices.code_one']" style="width: 100%;" label-position="left"
              label="ژ.کۆد " label-width="100px">
              <el-input type="text" clearable v-model="form.code_one" />
            </el-form-item>

          </div>

          <p class="font-droidkufi text-sm text-primary underline">زانیاری لایەنی دووەم</p>
          <div class="flex gap-4 items-center justify-center">
            <!-- name two -->
            <el-form-item :error="ErrorForms['invoices.name_two']" style="width: 100%;" label-position="left"
              label="ناو" label-width="100px">
              <el-input type="text" clearable v-model="form.name_two" />
            </el-form-item>

            <!-- phone two -->
            <el-form-item :error="ErrorForms['invoices.phone_two']" style="width: 100%;" label-position="left"
              label="ژ.تەلەفۆن " label-width="100px">
              <el-input type="text" clearable v-model="form.phone_two" />
            </el-form-item>

            <!-- national two -->
            <el-form-item :error="ErrorForms['invoices.national_two']" style="width: 100%;" label-position="left"
              label="نەتەوە " label-width="100px">
              <el-input type="text" clearable v-model="form.national_two" />
            </el-form-item>

            <!-- code two -->
            <el-form-item :error="ErrorForms['invoices.code_two']" style="width: 100%;" label-position="left"
              label="ژ.کۆد " label-width="100px">
              <el-input type="text" clearable v-model="form.code_two" />
            </el-form-item>

          </div>

          <p class="font-droidkufi text-sm text-primary underline">جۆری مامەڵە</p>

          <el-form-item :error="ErrorForms['invoices.mamala_type']" label="جۆری مامەڵە" label-width="100px"
            label-position="left" style="width: 100%;">
            <el-select v-model="form.mamala_type" placeholder="جۆرێک هەڵبژێرە" clearable>
              <el-option v-for="type in mamalaTypes" :key="type.value" :label="type.label" :value="type.value" />
            </el-select>
          </el-form-item>
          <hr class="my-2 border-[var(--el-color-danger)]">
          <div class="flex gap-4 items-center justify-center">


            <!-- zhmarai mulk -->
            <el-form-item :error="ErrorForms['invoices.zhmarai_mulk']" style="width: 100%;" label-position="left"
              label="ژ،موڵک" label-width="70px">
              <el-input type="text" clearable v-model="form.zhmarai_mulk" />
            </el-form-item>

            <!--zhmarai_xanwzhmarai_xanw -->
            <el-form-item :error="ErrorForms['invoices.zhmarai_xanw']" style="width: 100%;" label-position="left"
              label="ژ.خانوو" label-width="70px">
              <el-input type="text" clearable v-model="form.zhmarai_xanw" />
            </el-form-item>

            <!--zhmarai_garak -->
            <el-form-item :error="ErrorForms['invoices.zhmarai_garak']" style="width: 100%;" label-position="left"
              label="ژ.گەڕەک" label-width="70px">
              <el-input type="text" clearable v-model="form.zhmarai_garak" />
            </el-form-item>

            <!-- zhmarai kolan -->
            <el-form-item :error="ErrorForms['invoices.zhmarai_kolan']" style="width: 100%;" label-position="left"
              label="ژ.کۆڵان" label-width="70px">
              <el-input type="text" clearable v-model="form.zhmarai_kolan" />
            </el-form-item>

            <!-- note -->
            <el-form-item :error="ErrorForms['invoices.note']" style="width: 100%;" label-position="left" label="تێبینی"
              label-width="70px">
              <el-input type="text" clearable v-model="form.note" />
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
    <InvoicesAsaishTable :data="data" :datainvoices="{
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