<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ProjectsTable from "./ProjectStagesDetail.vue";
import { ref } from "vue";
import {  ElMessage, ElMessageBox } from 'element-plus';
import { router } from '@inertiajs/vue3';
import debounce from "lodash/debounce"; // For efficient input handling

const props = defineProps({
    data: Array, // Ensure type matches Laravel's response
    errors: Object,
});


const dialogFormVisible = ref(false);



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

</script>

<template>
    <AppLayout title="Projects Index">
        <template #header>
            <div dir="rtl" class="flex w-full items-center justify-between">
                <div class="flex items-center justify-between w-full">
                    <h4 dir="rtl"
                        class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black dark:text-black pl-1 sm:pl-2">
                        دیاریکردنی حاڵەکاتی پڕۆژەی <span class="font-bold text-primary">{{data.project.name}}</span>
                    </h4>
                   
                </div>
               
            </div>
        </template>

        <ProjectsTable :data="data" :destroy="destroy" :edit="edit" />

    </AppLayout>

   

</template>
<style scoped>
.custom-input .el-input__inner {
    border-radius: 0 !important;
    padding: 0 !important;
}
</style>