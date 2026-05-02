<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CustomersTable from "./CustomersTable.vue";
import { ref, reactive } from "vue";
import { ElButton, ElMessage, ElMessageBox } from 'element-plus';
import debounce from "lodash/debounce"; // For efficient input handling
import { router } from "@inertiajs/vue3";

const props = defineProps({
    data: Array, // Ensure type matches Laravel's response
    errors: Object,
    filters: Object,
});


const dialogFormVisible = ref(false);
const formLabelWidth = '190px';

const form = reactive({
    id: null,
    name: null,
    type_project_id: null,
    customer_name: null,
    phone: null,
    address: null,
    location: null,
    content: null,
    measurment: null,
    desgin_img: [],         // new uploaded images (File objects)
    existing_images: [],    // images already saved in DB
    xamlandn: null,
});

function resetForm() {
    form.id = null;
    form.name = null;
    form.type_project_id = null;
    form.customer_name = null;
    form.phone = null;
    form.address = null;
    form.location = null;
    form.content = null;
    form.measurment = null;
    form.desgin_img = null;
    form.xamlandn = null;
}

const searchQuery = ref("");
function submit(event) {
    event.preventDefault();

    const isUpdate = !!form.id;
    const url = isUpdate 
        ? `/customers/${form.id}/update/dashboard` 
        : '/customers/store/dashboard';

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
            const errorMessages = Object.values(errors).flat().join('<br>');
            ElMessage({
                message: errorMessages,
                type: 'error',
                dangerouslyUseHTMLString: true,
                duration: 5000,
            });
        },
        onSuccess: (page) => {
            ElMessage({
                message: page.props.flash?.message ||
                         (isUpdate ? 'Project updated successfully.' : 'Project created successfully.'),
                type: 'success',
                duration: 5000,
            });
            resetForm();
            dialogFormVisible.value = false;
        },
    });
}

function destroy(id) {
    ElMessageBox.confirm(
        'ئایە دڵنییایت لەسڕینەوەی ئەم داتاییە',
        'سڕینەوە',
        {
            confirmButtonText: 'بەڵێ',
            cancelButtonText: 'نەخێر',
            type: 'info',
            customClass: 'custom-confirm-box', // Add a custom class
        }
    )
        .then(() => {
            router.delete('customers/' + id, {
                onError: (errors) => {
                    const errorMessages = Object.values(errors)
                        .flat()
                        .join('<br>');

                    ElMessage({
                        customClass: 'custom-confirm-box', // Add a custom class
                        type: 'info',
                        message: errorMessages,
                    })
                },
                onSuccess: (success) => {
                    ElMessage({
                        type: 'success',
                        message: success.props.flash?.message,
                        customClass: 'custom-confirm-box', // Add a custom class

                    })
                }
            })

        })
        .catch(() => {
            ElMessage({
                customClass: 'custom-confirm-box', // Add a custom class
                type: 'info',
                message: 'پاشگەزبوونەوە',
            })
        })
}// Fetch user data for editing

function edit(id) {
    router.get('customers/' + id, {}, {
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

            // Assign DB images
            form.existing_images = project.images; // [{id, path}, ...]

            dialogFormVisible.value = true;
        }
    });
}



// Search Handling with Debounce
const handleSearch = debounce(() => {
    router.get('/customers', { q: searchQuery.value }, {
        preserveState: true, preserveScroll: true,
    });
}, 300); // Delay of 300ms


function openForm(id) {
            router.get("customerstages/" + id, {
                onError: (errors) => {
                    const errorMessages = Object.values(errors)
                        .flat()
                        .join("<br>");

                    ElMessage({
                        customClass: "custom-confirm-box", // Add a custom class
                        type: "info",
                        message: errorMessages,
                    });
                },
                onSuccess: (success) => {
                    ElMessage({
                        type: "success",
                        message: success.props.flash?.message,
                        customClass: "custom-confirm-box", // Add a custom class
                    });
                },
            });
      
}
function handleImageUpload(file, fileList) {
    form.desgin_img = fileList.map(f => f.raw); // keep raw files
}

</script>

<template>
    <AppLayout title="customers Index">
        <template #header>
            <div dir="rtl" class="flex w-full items-center justify-between">
                <div class="flex items-center justify-between w-full">
                    <h4 dir="rtl"
                        class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black dark:text-black pl-1 sm:pl-2">
                        کڕیارەکان
                    </h4>
                    <div class="flex items-center border border-primary rounded-sm py-2 sm:pl-32 h-10">
                        <i class="fa fa-search text-primary mx-2"></i>
                        <input v-model="searchQuery" @input="handleSearch" type="text"
                            placeholder="گــــــەڕان ئــــەنــجـــــام بـــدە..."
                            class="outline-none border-none focus:outline-none focus:ring-0 focus:border-transparent sm:text-sm text-xs font-droidkufi rounded-md w-full text-primary bg-transparent placeholder:text-primary" />
                    </div> 
                </div>
                <el-button v-if="can('write customers')" style="height: 40px;" @click="dialogFormVisible = true"
                    type="primary" class="font-droidkufi mr-2 object-cover">
                    <i class="fa fa-plus ml-2"></i>
                    تۆمارکردن
                </el-button>
            </div>
        </template>

<CustomersTable 
  :data="data"
  :filters="filters"
  :destroy="destroy"
  :edit="edit"
  :openForm="openForm"
/>

    </AppLayout>
    <!-- customers Table insert -->
   <el-dialog class="font-droidkufi" dir="rtl" v-model="dialogFormVisible" title="فۆڕمی زیاکردنی پڕۆژە" width="1000"
             :close-on-click-modal="true"  @close="resetForm">
    <el-form :model="form" @keydown.enter="submit" label-position="left" label-width="120px">
      <div class="flex">
          <el-form-item style="width: 100%;" label="ناوی پڕۆژە">
            <el-input v-model="form.name" clearable />
        </el-form-item>

        <el-form-item style="width: 100%;" label="ناوی کڕیار">
          <el-input v-model="form.customer_name" clearable />
        </el-form-item>
      </div>

      

   <div class="flex ">
     <el-form-item style="width: 100%;" label="ژمارەی تەلەفون">
       <el-input v-model="form.phone" clearable />
     </el-form-item>
     <el-form-item style="width: 100%;" label="جۆری پڕۆژە">
         <el-select v-model="form.type_project_id" placeholder="هەڵبژاردنی جۆری پڕۆژە" clearable>
             <el-option v-for="type in filters.typeProjects" :key="type.id" :label="type.name" :value="type.id" />
         </el-select>
     </el-form-item>

   </div>     

   <div class="flex">
         <el-form-item style="width: 100%;" label="ناونیشان">
            <el-input v-model="form.address" type="text" clearable />
        </el-form-item>

        <el-form-item style="width: 100%;" label="لۆکەیشن">
            <el-input v-model="form.location" type="text" clearable />
        </el-form-item>
   </div>
<div class="flex w-full flex-col gap-4">
  <p class="w-[190px] font-droidkufi text-sm text-gray-500">
    زیادکردنی وێنە
  </p>

  <!-- Existing images -->
  <div class="flex gap-3 flex-wrap">
    <div
      v-for="img in form.existing_images"
      :key="img.id"
      class="relative"
    >
      <img
        :src="`/storage/${img.path}`"
        class="w-20 h-20 object-cover rounded-lg shadow"
      />
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

  <!-- Upload button always at bottom -->
  <el-upload
    class="upload-demo w-full"
    drag
    multiple
    :on-change="handleImageUpload"
    :auto-upload="false"
    accept="image/*"
  >
    <el-icon class="el-icon--upload"><UploadFilled /></el-icon>
    <div class="el-upload__text">
      Drop files here or <em>click to upload</em>
    </div>
  </el-upload>
</div>


        <el-form-item label="ناوەڕۆک">
            <el-input v-model="form.content" type="textarea" clearable />
        </el-form-item>

        <el-form-item label="قیاس">
            <el-input v-model="form.measurment" clearable />
        </el-form-item>


        <el-form-item label="خەمڵاندن">
            <el-input v-model="form.xamlandn" type="text" clearable />
        </el-form-item>
     
    </el-form>
     <template #footer class="w-full">
            <div class="dialog-footer">
                <el-button @click="submit" type="success" plain style="float: left;">
                    زەخیرەکردنی زانییاری کڕیارەکان
                </el-button>
            </div>
        </template>
  </el-dialog>
   

</template>
<style scoped>
.custom-input .el-input__inner {
    border-radius: 0 !important;
    padding: 0 !important;
}
</style>