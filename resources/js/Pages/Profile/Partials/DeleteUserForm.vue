<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>

    <div class="font-droidkufi">
        <ActionSection>
            <template #title>
                سڕینەوەی ئەکاونت
            </template>

            <template #description>
                سڕینەوەی هەموو ئەکاونتەکەت بەشێوەیەکی هەموو شتێک.
            </template>

            <template #content>
                <div class="max-w-xl text-sm text-gray-600">
                    کاتێک ئەکاونتەکەت سڕایەوە، هەموو سەرچاوەکان و داتاکانی لەگەڵەوە بەشێوەیەکی هەموو شتێک سڕدرێنەوە.
                    تکایە پێش سڕینەوە، هەر داتاو زانیارییەک کە دەتەوێت پاشەکەوت بکەیت داگرتن بکە.
                </div>

                <div class="mt-5">
                    <DangerButton @click="confirmUserDeletion">
                        سڕینەوەی ئەکاونت
                    </DangerButton>
                </div>

                <!-- دڵنیابوونەوەی سڕینەوەی ئەکاونت -->
                <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                    <template #title>
                        سڕینەوەی ئەکاونت
                    </template>

                    <template #content>
                        دڵنیایت لە سڕینەوەی ئەکاونتەکەت؟ کاتێک ئەکاونتەکەت سڕایەوە، هەموو سەرچاوەکان و داتاکانی لەگەڵەوە
                        بەشێوەیەکی هەموو شتێک سڕدرێنەوە. تکایە وشەی نهێنیت بنووسە بۆ دڵنیابوونەوە لە سڕینەوەی هەموو
                        ئەکاونتەکەت.

                        <div class="mt-4">
                            <TextInput ref="passwordInput" v-model="form.password" type="password"
                                class="mt-1 block w-3/4" placeholder="وشەی نهێنی" autocomplete="current-password"
                                @keyup.enter="deleteUser" />

                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>
                    </template>

                    <template #footer>
                        <SecondaryButton @click="closeModal">
                            هەڵوەشاندنەوە
                        </SecondaryButton>

                        <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing" @click="deleteUser">
                            سڕینەوەی ئەکاونت
                        </DangerButton>
                    </template>
                </DialogModal>
            </template>
        </ActionSection>

    </div>

</template>
