<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive, watch } from "vue";
import { ElButton, ElMessage } from 'element-plus';
import { router } from "@inertiajs/vue3";
import {
    Delete,
    DeleteFilled,
    Edit,
    Plus,
  } from '@element-plus/icons-vue';
  import InvoiceProjectsTable from './InvoiceProjectsTable.vue';
  const props = defineProps({
    data: Array, // Ensure type matches Laravel's response
    errors: Object,
    filters: Object,
  });
  
  const selectedItems = ref([]);
  
  const today = new Date();
  const day = today.getDate();
  const month = today.getMonth() + 1; // months start from 0
  const year = today.getFullYear();
// Add new row
function addRow() {
  selectedItems.value.push({ id: Date.now(), name: "", measure: "", price: "", quantity: "", unit_price: "" });
}

// Remove row
function removeRow(index) {
  selectedItems.value.splice(index, 1);
}


const form = reactive({
    id: null,
    to: null,
    matter: null,
    content: 'ئێمە وەک چاپخانەی ئینفینیتی هەڵدەستین بە دروستکردنی بارزی داگیرساو بۆ عقاراتەکەتان بە باشترین کوالێتی بەم دیزاین و قیاسانە، تەواوی کارەکان گرەنتی (دوو ساڵی) لەگەڵدایە.',
  projects_id: null,
  datenow: new Date().toISOString().split("T")[0], // e.g. "2025-09-22"
});

const ErrorForms=ref({})



function submit(event) {
    event.preventDefault();

    const method = form.id ? 'put' : 'post'; // Determine HTTP method
    const url = form.id ? `/invoiceProjects/${form.id}` : '/invoiceProjects'; // Determine URL

    router[method](url, {
        invoices: form,
        selectedItems: selectedItems.value,
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
  router.get('/invoiceProjects/' + id, {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      const invoice = page.props.flash?.data;
      if (!invoice) return;

      // Fill form
      form.id = invoice.id;
      form.to = invoice.to;
      form.matter = invoice.matter;
      form.projects_id = invoice.projects_id;
      form.content = invoice.content;
      form.datenow = invoice.datenow;

      // Fill selectedItems with existing invoice items
      selectedItems.value = invoice.invoice_items.map(item => ({
        id: item.id,
        name: item.name,
        measure: item.measure,
        quantity: item.quantity,
        price: item.price,
        unit_price: item.unit_price,
      }));

    }
  });
}

const dialogvisablity = ref(false);

const formInvoices = reactive({
  id: null,
  to: "",
  matter: "",
  content:
    "ئێمە وەک چاپخانەی ئینفینیتی هەڵدەستین بە دروستکردنی بارزی داگیرساو بۆ عقاراتەکەتان بە باشترین کوالێتی بەم دیزاین و قیاسانە، تەواوی کارەکان گرەنتی (دوو ساڵی) لەگەڵدایە.",
  projects_id: null,
  total_price: 0,
  datenow: new Date().toISOString().split("T")[0], // e.g. "2025-09-22"
});
const selectedItemInvoices = ref([]);
const projectImages = ref([]);

function printInvoices(id) {
  router.get('/invoiceProjects/' + id, {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      const invoice = page.props.flash?.data;
      if (!invoice) return;

      // Fill form
      formInvoices.id = invoice.id;
      formInvoices.to = invoice.to;
      formInvoices.matter = invoice.matter;
      formInvoices.content = invoice.content;
      formInvoices.projects_id = invoice.projects_id;
      formInvoices.total_price = invoice.total_price;
      formInvoices.datenow = invoice.datenow;

      // Fill selectedItems with existing invoice items
      selectedItemInvoices.value = invoice.invoice_items.map(item => ({
        id: item.id,
        name: item.name,
        measure: item.measure,
        price: item.price,
        unit_price: item.unit_price,
        quantity: item.quantity,
      }));
            projectImages.value = invoice.projects?.images?.map(img => `/storage/${img.path}`) || [];

  dialogvisablity.value = true;

    }
  });
}

function resetForm() {
    form.id = null;
    form.to = null;
    form.matter = null;
    form.content= 'ئێمە وەک چاپخانەی ئینفینیتی هەڵدەستین بە دروستکردنی بارزی داگیرساو بۆ عقاراتەکەتان بە باشترین کوالێتی بەم دیزاین و قیاسانە، تەواوی کارەکان گرەنتی (دوو ساڵی) لەگەڵدایە.',
form.projects_id = null;
      ErrorForms.value = {};
  selectedItems.value = [];
    
    dialogvisablity.value = false;

}


// Watch changes for each row
watch(
  selectedItems,
  (newVal) => {
    newVal.forEach((item) => {
      item.price = (Number(item.unit_price) || 0) * (Number(item.quantity) || 0)
    })
  },
  { deep: true }
)

</script>

<template>
    <AppLayout title="Projects Index">
        <template #header>
            <div dir="rtl" class="flex w-full items-center justify-between">
                <div class="flex items-center justify-between w-full">
                    <h4 dir="rtl"
                        class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black dark:text-black pl-1 sm:pl-2">
                        دەرخستە
                    </h4>
                
                </div>
              
            </div>
        </template>

        <div class="bg-white rounded shadow-md px-8 py-3 m-4">
<div class="font-droidkufi w-full mx-auto" dir="rtl">
    <h2 class="text-lg text-primary font-bold mb-4">فۆڕمی دروستکردنی دەرخستە</h2>

    <el-form :model="form">

        <div class="flex gap-4 items-center justify-center">
            <el-form-item   :error="ErrorForms['invoices.to']"
 style="width: 100%;" label-position="left" label="بۆ بەڕێز / " label-width="100px">
                <el-input type="text" clearable v-model="form.to" />
            </el-form-item>
            <el-form-item   :error="ErrorForms['invoices.matter']"
 style="width: 100%;" label-position="left" label="بابەت / " label-width="100px">
                <el-input type="text" clearable v-model="form.matter" />
            </el-form-item>
    
            <el-form-item
  :error="ErrorForms['invoices.datenow']"
  style="width: 100%;"
  label-position="left"
  label="بەروار / "
  label-width="100px"
>
  <el-date-picker
    v-model="form.datenow"
    type="date"
    clearable
    style="width: 100%;"
  />
</el-form-item>

          </div>
    <div class="flex gap-4 items-center justify-center">
            <el-form-item style="width: 100%;"   :error="ErrorForms['invoices.content']"
       label-position="left" label="ناوەڕۆک" label-width="100px">
         <el-input autosize type="textarea" clearable v-model="form.content" />
      </el-form-item>

     <el-form-item
  style="width: 100%;"
  :error="ErrorForms['invoices.projects_id']"
  label-position="left"
  label="ناوی پڕۆژە"
  label-width="100px"
>
  <el-select
    v-model="form.projects_id"
    filterable
    clearable
    placeholder="هەڵبژاردنی پڕۆژە"
    style="width: 100%"
  >
    <el-option
      v-for="item in filters?.projects"
      :key="item.id"
      :label="item.name"
      :value="item.id"
    />
  </el-select>
</el-form-item>


    </div>

       

        <div>
  <div class="flex flex-col justify-between items-center w-full mb-4 px-4">
    <div class="w-full overflow-x-auto">
      <table class="w-full table-auto">
        <thead class="font-droidkufi">
          <tr class="bg-gray-100 text-right dark:bg-meta-4">
                <!-- + button in the header -->
                <th class="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black min-w-[10px]">
                    #
                </th>
                <th class="border border-[#eee] dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-black min-w-[10px]">
         <button type="button" @click="addRow" class="bg-primary text-white px-2.5 py-1.5 font-bold items-center justify-center
          text-center rounded hover:bg-blue-600">
             <el-icon>
                 <Plus />
      </el-icon>
            </button>
       </th>
            <th class="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black min-w-[200px]">
بابەت            </th>
            <th class="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black min-w-[200px]">
              قیاس
            </th>
            <th class="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black min-w-[200px]">
              نرخ
            </th>
            <th class="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black min-w-[200px]">
              دانە
            </th>
            <th class="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black min-w-[200px]">
              کۆی گشتی
            </th>
         
        
          </tr>
        </thead>
        <tbody>
          <tr class="items-center justify-center" v-for="(item, index) in selectedItems"      :key="item.id" 
>
              <td class="border border-[#eee] py-0 px-4 text-sm sm:text-xs text-black">{{ index + 1 }}</td>
              <td class="border border-[#eee] py-0 px-4">
                <button @click="removeRow(index)" 
 class="bg-danger text-white px-2 py-1 rounded hover:bg-red-600">
                  <el-icon><DeleteFilled /></el-icon>
                </button>
              </td>
            <td class="border border-[#eee] py-0 px-4">
                
<el-form-item
  :error="ErrorForms[`selectedItems.${index}.name`]"
>
  <el-input type="text" v-model="item.name"  style="width: 100%;"/>
</el-form-item>            </td>
            <td class="border border-[#eee] py-0 px-4">
                <el-form-item
                  :error="ErrorForms[`selectedItems.${index}.measure`]"

                >

                    <el-input type="text" v-model="item.measure" style="width: 100%;"  />
                </el-form-item>
            </td>
            <td class="border border-[#eee] py-0 px-4">
                <el-form-item :error="ErrorForms[`selectedItems.${index}.unit_price`]">
                    <el-input type="number" v-model="item.unit_price" style="width: 100%;"  />
                </el-form-item>
            </td>
           
            <td class="border border-[#eee] py-0 px-4">
                <el-form-item :error="ErrorForms[`selectedItems.${index}.quantity`]">
                    <el-input type="number" v-model="item.quantity" style="width: 100%;"  />
                </el-form-item>
            </td>
           
            <td class="border border-[#eee] py-0 px-4">
                <el-form-item :error="ErrorForms[`selectedItems.${index}.price`]">
                    <el-input type="number" v-model="item.price" style="width: 100%;"  />
                </el-form-item>
            </td>
           
          </tr>

          <!-- Empty state -->
          <tr v-if="selectedItems.length === 0">
            <td colspan="7" class="border border-[#eee] py-4 text-center text-gray-500">
              هیچ بابەتێک نییە. بۆ زیادکردن، کرتە لە + بکە.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
        </div>

       
    </el-form>

    <div class="mt-6 flex justify-end">
        <el-button @click="submit" type="success" plain>
            زەخیرەکردنی زانییاری دەرخستە
        </el-button>
    </div>
</div>

</div>
<InvoiceProjectsTable :data="data" :datainvoices="{
  invoice: formInvoices,
  selectedItems: selectedItemInvoices,
  projectImages:projectImages
}" :dialogvisablity="dialogvisablity" :printInvoices="printInvoices" :resetform="resetForm" :destroy="destroy" :edit="edit" :openForm="openForm"/>

    </AppLayout>
</template>
<style scoped>
.custom-input .el-input__inner {
    border-radius: 0 !important;
    padding: 0 !important;
}
</style>