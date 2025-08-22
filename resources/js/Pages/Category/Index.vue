<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CategoryTable from "./CategoryTable.vue";
import { ref, reactive } from "vue";
import { ElButton, ElMessage, ElMessageBox } from 'element-plus';
import { router, usePage } from '@inertiajs/vue3';
import debounce from "lodash/debounce"; // For efficient input handling

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
    const url = form.id ? `/categories/${form.id}` : '/categories'; // Determine URL

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
            router.delete('categories/' + id, {
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
    router.get('categories/' + id, {}, {
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
    router.get('/categories', { q: searchQuery.value }, {
        preserveState: true, preserveScroll: true,
    });
}, 300); // Delay of 300ms



</script>

<template>
    <AppLayout title="categories Index">
        <template #header>
            <div dir="rtl" class="flex w-full items-center justify-between">
                <div class="flex items-center justify-between w-full">
                    <h4 dir="rtl"
                        class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black dark:text-black pl-1 sm:pl-2">
                        پۆلێنەکان
                    </h4>
                    <div class="flex items-center border border-primary rounded-sm py-2 sm:pl-32 h-10">
                        <i class="fa fa-search text-primary mx-2"></i>
                        <input v-model="searchQuery" @input="handleSearch" type="text"
                            placeholder="گــــــەڕان ئــــەنــجـــــام بـــدە..."
                            class="outline-none border-none focus:outline-none focus:ring-0 focus:border-transparent sm:text-sm text-xs font-droidkufi rounded-md w-full text-primary bg-transparent placeholder:text-primary" />
                    </div>
                </div>
                <el-button v-if="can('write category')" style="height: 40px;" @click="dialogFormVisible = true"
                    type="primary" class="font-droidkufi mr-2 object-cover">
                    <i class="fa fa-plus ml-2"></i>
                    تۆمارکردن
                </el-button>
            </div>
        </template>

        <CategoryTable :data="data" :destroy="destroy" :edit="edit" />

    </AppLayout>

    <el-dialog class="font-droidkufi" dir="rtl" v-model="dialogFormVisible" title="فۆڕمی زیاکردنی پۆلێنەکان" width="700"
        align-center="true" :close-on-click-modal="false">
        <el-form :model="form" @keydown.enter="submit">
            <el-form-item label-position="left" label="ناوی پۆلێن" :label-width="formLabelWidth">
                <el-input clearable v-model="form.name" />
            </el-form-item>
        </el-form>

        <template #footer class="w-full">
            <div class="dialog-footer">
                <el-button @click="submit" type="success" plain style="float: left;">
                    زەخیرەکردنی زانییاری پۆلێن
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