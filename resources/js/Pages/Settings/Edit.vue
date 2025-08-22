<script lang="ts" setup>
import { reactive, ref } from "vue";
import { ElMessage } from "element-plus";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    Settings: Object, // Passed from the controller
});

const form = useForm({
    name: props.Settings.name || '',
    address: props.Settings.address || '',
    phone1: props.Settings.phone1 || '',
    phone2: props.Settings.phone2 || '',
    title1: props.Settings.title1 || '',
    title2: props.Settings.title2 || '',
    image: null, // for uploaded image
    });

function submit() {
    form.post("/settings/update", {
        forceFormData: true, // required for file upload
        onSuccess: () => {
            ElMessage({
                message: "بەسەرکەوتوویی نوێکرایەوە",
                type: "success",
                duration: 3000,
            });
        },
        onError: (errors) => {
            ElMessage({
                message: Object.values(errors).join("<br>"),
                type: "error",
                dangerouslyUseHTMLString: true,
                duration: 3000,
            });
        },
    });
}

const imagePreview = ref(
    props.Settings.image ? `/storage/${props.Settings.image}` : null
);

function handleImageUpload(uploadFile: any) {
    form.image = uploadFile.raw;
    imagePreview.value = URL.createObjectURL(uploadFile.raw);
}

</script>

<template>
    <AppLayout title="Settings">
        <template #header>
            <h2 dir="rtl" class="font-Sarchia_Qaisy_bold text-xl text-gray-800 leading-tight">
                ڕێکخستنەکان
            </h2>
        </template>
        <div class=" items-center font-droidkufi justify-center text-center my-auto bg-white">
            <div>
                <el-form @submit.prevent="submit" label-position="left" class=" grid grid-cols-2 gap-5">
                    <!-- Name and address -->
                    <el-form-item label="ناو" label-width="120px">
                        <el-input v-model="form.name" type="text" />
                    </el-form-item>
                    <el-form-item label="ناونیشان" label-width="120px">
                        <el-input v-model="form.address" type="text" />
                    </el-form-item>
                    <!-- phone 1,2 -->
                    <el-form-item label="ژمارە تەلەفۆن ١" label-width="120px">
                        <el-input v-model="form.phone1" type="text" />
                    </el-form-item>
                    <el-form-item label="ژمارە تەلەفۆن ٢" label-width="120px">
                        <el-input v-model="form.phone2" type="text" />
                    </el-form-item>
                    <!-- title 1,2 -->
                    <el-form-item label="سەردێر ١" label-width="120px">
                        <el-input v-model="form.title1" type="text" />
                    </el-form-item>
                    <el-form-item label="سەردێر ٢" label-width="120px">
                        <el-input v-model="form.title2" type="text" />
                    </el-form-item>

                    <el-form-item label="وێنە" label-width="120px" class="col-span-2">
                        <el-upload class="upload-demo w-full" drag action="" :auto-upload="false"
                            :on-change="handleImageUpload" accept="image/*" :show-file-list="false">
                            <template v-if="imagePreview">
                                <img :src="imagePreview" class="w-32 h-32 object-cover rounded border mx-auto" />
                            </template>
                            <template v-else>
                                <el-icon class="el-icon--upload">
                                    <UploadFilled />
                                </el-icon>
                                <div class="el-upload__text">Drop file here or <em>click to upload</em></div>
                            </template>

                            <template #tip>
                                <div class="el-upload__tip">
                                    jpg/png files under 2MB
                                </div>
                            </template>
                        </el-upload>
                    </el-form-item>




                </el-form>
            </div>
            <el-button type="success" plain @click="submit">زەخیرەکردن</el-button>

        </div>


    </AppLayout>
</template>
<style scoped>
.upload-demo {
    border: 2px dashed #d9d9d9;
    border-radius: 6px;
    background-color: #f5f7fa;
    padding: 1rem;
}
</style>
