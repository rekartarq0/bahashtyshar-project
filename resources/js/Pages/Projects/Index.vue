<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ProjectsTable from "./ProjectsTable.vue";
import { ref, reactive } from "vue";
import { ElButton, ElMessage, ElMessageBox } from 'element-plus';
import debounce from "lodash/debounce"; // For efficient input handling
import { router } from "@inertiajs/vue3";

const props = defineProps({
    data: Array, // Ensure type matches Laravel's response
    errors: Object,
});


const dialogFormVisible = ref(false);
const formLabelWidth = '190px';
const form = reactive({
    id: null,
    name: null,
});

const searchQuery = ref("");

function resetForm() {
    form.id = null;
    form.name = null;
}
function submit(event) {
    event.preventDefault();

    const method = form.id ? 'put' : 'post'; // Determine HTTP method
    const url = form.id ? `/projects/${form.id}` : '/projects'; // Determine URL

    router[method](url, form, {
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join('<br>');

            ElMessage({
                center: false,
                message: errorMessages,
                type: 'error',
                dangerouslyUseHTMLString: true,
                // customClass: 'custom-confirm-box', // Add a custom class
                duration: 5000,
            });
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
            router.delete('projects/' + id, {
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
    router.get('projects/' + id, {}, {
        preserveState: true, preserveScroll: true, onSuccess: (result) => {
            const user = result.props.flash.data;
            form.id = user.id;
            form.name = user.name;
            dialogFormVisible.value = true; // Open dialog
        }
    })
}


// Search Handling with Debounce
const handleSearch = debounce(() => {
    router.get('/projects', { q: searchQuery.value }, {
        preserveState: true, preserveScroll: true,
    });
}, 300); // Delay of 300ms


function openForm(id) {
            router.get("projectStages/" + id, {
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


</script>

<template>
    <AppLayout title="Projects Index">
        <template #header>
            <div dir="rtl" class="flex w-full items-center justify-between">
                <div class="flex items-center justify-between w-full">
                    <h4 dir="rtl"
                        class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black dark:text-black pl-1 sm:pl-2">
                        پڕۆژەکان
                    </h4>
                    <div class="flex items-center border border-primary rounded-sm py-2 sm:pl-32 h-10">
                        <i class="fa fa-search text-primary mx-2"></i>
                        <input v-model="searchQuery" @input="handleSearch" type="text"
                            placeholder="گــــــەڕان ئــــەنــجـــــام بـــدە..."
                            class="outline-none border-none focus:outline-none focus:ring-0 focus:border-transparent sm:text-sm text-xs font-droidkufi rounded-md w-full text-primary bg-transparent placeholder:text-primary" />
                    </div> 
                </div>
                <el-button v-if="can('write projects')" style="height: 40px;" @click="dialogFormVisible = true"
                    type="primary" class="font-droidkufi mr-2 object-cover">
                    <i class="fa fa-plus ml-2"></i>
                    تۆمارکردن
                </el-button>
            </div>
        </template>

        <ProjectsTable :data="data" :destroy="destroy" :edit="edit" :openForm="openForm"/>

    </AppLayout>

    <el-dialog class="font-droidkufi" dir="rtl" v-model="dialogFormVisible" title="فۆڕمی زیاکردنی پڕۆژەکان" width="700"
        align-center="true" :close-on-click-modal="false">
        <el-form :model="form" @keydown.enter="submit">
            <el-form-item label-position="left" label="ناوی پڕۆژە" :label-width="formLabelWidth">
                <el-input clearable v-model="form.name" />
            </el-form-item>
        </el-form>

        <template #footer class="w-full">
            <div class="dialog-footer">
                <el-button @click="submit" type="success" plain style="float: left;">
                    زەخیرەکردنی زانییاری پڕۆژەکان
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