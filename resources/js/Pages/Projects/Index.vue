<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ProjectsTable from "./ProjectsTable.vue";
import { ref, reactive, computed, watch } from "vue";
import { ElButton, ElMessage, ElMessageBox } from 'element-plus';
import debounce from "lodash/debounce"; // For efficient input handling
import { router } from "@inertiajs/vue3";

import {
    Back,
    Download,
    Refresh,
    RefreshLeft,
    RefreshRight,
    Right,
    UploadFilled,
    ZoomIn,
    ZoomOut,
} from '@element-plus/icons-vue'

const props = defineProps({
    data: Array, // Ensure type matches Laravel's response
    errors: Object,
    filters: Object,
});

const uploadRef = ref()

const dialogFormVisible = ref(false);
const ErrorForms = ref({})

const form = reactive({
    id: null,
    name: null,
    phone: '+9647',
    location_id: null,

    price: null,
    Rwbar: null,
    note: null,
    number_mulk: null,
    link_location: null,
    type_project_id: null,

    emara: null,
    qat: null,
    zhmarai_shwqa: null,
    facebook_link: null,

    desgin_img: [],
    existing_images: [],
});

function resetForm() {
    form.id = null;
    form.name = null;
    form.phone = '+9647';
    form.location_id = null;
    form.price = null;
    form.Rwbar = null;
    form.note = null;
    form.number_mulk = null;
    form.link_location = null;
    form.type_project_id = null;
    form.facebook_link = null;

    form.desgin_img = [];
    ErrorForms.value = {};
    form.desgin_img = [];
    form.existing_images = [];
    form.emara = null;
    form.qat = null;
    form.zhmarai_shwqa = null;
    uploadRef.value?.clearFiles()

}


const isShuqah = computed(() => {
    const type = props.filters?.typeProjects?.find(
        (t: any) => t.id === form.type_project_id
    );
    return type?.name === 'شووقە';
});

watch(
    () => form.type_project_id,
    () => {
        if (!isShuqah.value) {
            form.emara = null;
            form.qat = null;
            form.zhmarai_shwqa = null;
        }
    }
);

const searchQuery = ref("");
function submit(event) {
    event.preventDefault();

    const isUpdate = !!form.id;
    const url = isUpdate
        ? `/projects/${form.id}/update/dashboard`
        : '/projects/store/dashboard';

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
            ErrorForms.value = errors;

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
}
// Fetch user data for editing
function edit(id) {
    router.get('projects/' + id, {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (result) => {
            const project = result.props.flash.data;

            // Map all fields from the DB to the form
            form.id = project.id;
            form.name = project.name;
            form.phone = project.phone;

            // ✅ IMPORTANT
            form.type_project_id = project.type_project?.id ?? null;
            form.location_id = project.location?.id ?? null;

            // Additional fields
            form.price = project.price || null;
            form.Rwbar = project.Rwbar || null;
            form.note = project.note || null;
            form.number_mulk = project.number_mulk || null;
            form.link_location = project.link_location || null;
            form.facebook_link = project.facebook_link || null;

            form.emara = project.emara || null;
            form.qat = project.qat || null;
            form.zhmarai_shwqa = project.zhmarai_shwqa || null;

            // Assign images from DB
            form.existing_images = project.images || []; // [{id, path}, ...]

            // Clear any new images before edit
            form.desgin_img = [];

            dialogFormVisible.value = true;
        }
    });
}


const perPage = ref(props.filters.perPage || 10);
const datefilter = ref(props.filters.datefilter || null);

// Search Handling with Debounce
const handleSearch = debounce(() => {
    router.get('/projects', { q: searchQuery.value, page: 1, perPage: props.filters.perPage || 10, datefilter: datefilter.value }, {
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
function handleImageUpload(file, fileList) {
    form.desgin_img = fileList.map(f => f.raw); // keep raw files
}


const fetchProjects = debounce((page = 1) => {
    router.get(
        '/projects',
        {
            q: searchQuery.value,
            page,
            perPage: perPage.value,
            datefilter: datefilter.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
}, 300);

watch(searchQuery, () => {
    fetchProjects(1);
});

watch(perPage, () => {
    fetchProjects(1);
});

</script>

<template>
    <AppLayout title="Projects Index">
        <template #header>
            <div dir="rtl" class="flex w-full items-center justify-between">
                <div class="flex items-center justify-between w-full">
                    <h4 dir="rtl"
                        class="font-bold font-Sarchia_Qaisy_bold sm:text-xl text-black dark:text-black pl-1 sm:pl-2">
                        مووڵکەکان
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

        <ProjectsTable :search="searchQuery" :data="data" :destroy="destroy" :edit="edit" :openForm="openForm"
            :perPage="perPage" @page-change="fetchProjects" />

    </AppLayout>

    <el-dialog class="font-droidkufi" dir="rtl" v-model="dialogFormVisible" title="فۆڕمی زیاکردنی مووڵک" width="70%"
        :close-on-click-modal="true" @close="resetForm">
        <el-form :model="form" @keydown.enter="submit" label-position="left" label-width="120px">

            <div class="md:flex text-[8px] md:text-sm ">
                <el-form-item :error="ErrorForms.name" style="width: 100%;" label="ناو">
                    <el-input v-model="form.name" clearable />
                </el-form-item>

                <el-form-item :error="ErrorForms.phone" style="width: 100%;" label="ژمارە تەلەفۆن">
                    <el-input v-model="form.phone" clearable />
                </el-form-item>
            </div>



            <div class="flex flex-col md:flex-row w-full gap-3">

                <!-- Location -->
                <div class="w-full md:w-1/2">
                    <el-form-item class="w-full" :error="ErrorForms.location_id" label="ناونیشان">
                        <el-select filterable v-if="filters.locations?.length" v-model="form.location_id"
                            placeholder="هەڵبژاردنی ناونیشان" clearable class="w-full">
                            <el-option v-for="type in filters.locations" :key="type.id" :label="type.name"
                                :value="type.id" />
                        </el-select>
                    </el-form-item>
                </div>

                <!-- Type Project -->
                <div class="w-full md:w-1/2">
                    <el-form-item class="w-full" :error="ErrorForms.type_project_id" label="جۆری مووڵک">
                        <el-select filterable v-if="filters.typeProjects?.length" v-model="form.type_project_id"
                            placeholder="هەڵبژاردنی جۆری مووڵک" clearable class="w-full">
                            <el-option v-for="type in filters.typeProjects" :key="type.id" :label="type.name"
                                :value="type.id" />
                        </el-select>
                    </el-form-item>
                </div>

            </div>


            <!-- شووقە fields -->
            <div v-if="isShuqah" class="md:flex text-[8px] md:text-sm">
                <el-form-item :error="ErrorForms.emara" style="width: 100%;" label="عيماره">
                    <el-input v-model="form.emara" clearable />
                </el-form-item>

                <el-form-item :error="ErrorForms.qat" style="width: 100%;" label="قات">
                    <el-input v-model="form.qat" clearable />
                </el-form-item>

                <el-form-item :error="ErrorForms.zhmarai_shwqa" style="width: 100%;" label="ژ. شوقە">
                    <el-input v-model="form.zhmarai_shwqa" clearable />
                </el-form-item>
            </div>


            <div class="md:flex">
                <el-form-item :error="ErrorForms.price" style="width: 100%;" label="نرخ">
                    <el-input v-model="form.price" type="number" clearable />
                </el-form-item>

                <el-form-item :error="ErrorForms.Rwbar" style="width: 100%;" label="ڕووبەر">
                    <el-input v-model="form.Rwbar" type="number" clearable />
                </el-form-item>
            </div>
            <div class="md:flex w-full flex-col gap-4">
                <p class="w-[190px] font-droidkufi text-[8px] md:text-sm text-gray-500">
                    زیادکردنی وێنە
                </p>

                <!-- Images -->
                <el-form-item :error="ErrorForms.images" label="وێنەکان" required>
                    <div v-if="form.existing_images && form.existing_images.length"
                        class="flex text-[8px] md:text-sm gap-3 flex-wrap">
                        <div v-for="(img, index) in form.existing_images" :key="img.id" class="relative"
                            style="width: 120px; height: 120px">
                            <el-image :src="`/storage/${img.path}`"
                                :preview-src-list="form.existing_images.map(i => `/storage/${i.path}`)" fit="cover"
                                style="width: 100%; height: 100%; border-radius: 0.5rem;">
                                <template #viewer-toolbar="{ actions, prev, next, reset, activeIndex }">
                                    <el-icon @click="prev">
                                        <Back />
                                    </el-icon>
                                    <el-icon @click="next">
                                        <Right />
                                    </el-icon>
                                    <el-icon @click="actions('zoomOut')">
                                        <ZoomOut />
                                    </el-icon>
                                    <el-icon @click="actions('zoomIn')">
                                        <ZoomIn />
                                    </el-icon>
                                    <el-icon @click="actions('clockwise')">
                                        <RefreshRight />
                                    </el-icon>
                                    <el-icon @click="actions('anticlockwise')">
                                        <RefreshLeft />
                                    </el-icon>
                                    <el-icon @click="reset">
                                        <Refresh />
                                    </el-icon>
                                    <el-icon @click="downloadImage(activeIndex)">
                                        <Download />
                                    </el-icon>
                                </template>
                            </el-image>

                            <!-- Delete Button -->
                            <el-button size="mini" type="danger" class="absolute top-0 right-0"
                                @click="form.existing_images = form.existing_images.filter(i => i.id !== img.id)">
                                X
                            </el-button>
                        </div>
                    </div>
                    <p v-else class="text-gray-400 text-[8px] md:text-sm">وێنە نییە</p>

                    <!-- Upload button -->
                    <el-upload ref="uploadRef" class="upload-demo w-full" drag multiple :on-change="handleImageUpload"
                        :auto-upload="false" accept="image/*">
                        <el-icon class="el-icon--upload">
                            <UploadFilled />
                        </el-icon>
                        <div class="el-upload__text text-[8px] md:text-sm">
                            Drop files here or <em>click to upload</em>
                        </div>
                    </el-upload>

                </el-form-item>
            </div>
            <div class="flex items-center justify-between">
                <div class="w-1/2">
                    <el-form-item style="width: 100%;" :error="ErrorForms.facebook_link" label="لینکی فەیسبووک">
                        <el-input v-model="form.facebook_link" clearable />
                    </el-form-item>
                </div>

                <div class="w-1/2">
                    <el-form-item :error="ErrorForms.note" label="تێبینی" class="text-[8px] md:text-sm">
                        <el-input v-model="form.note" type="textarea" clearable />
                    </el-form-item>

                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="w-1/2">
                    <el-form-item style="width: 100%;" :error="ErrorForms.number_mulk" label="ژمارەی موڵکەکە">
                        <el-input v-model="form.number_mulk" clearable />
                    </el-form-item>

                </div>
                <!-- tttttt -->
                <div class="w-1/2">
                    <el-form-item style="width: 100%;" :error="ErrorForms.link_location" label="لینکی ناونیشان">
                        <el-input v-model="form.link_location" clearable />
                    </el-form-item>

                </div>
            </div>


        </el-form>
        <template #footer>
            <div class="w-full justify-end items-end flex">
                <el-button @click="submit" type="success" plain dir="rtl">
                    پاشکەوتکردن</el-button>
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