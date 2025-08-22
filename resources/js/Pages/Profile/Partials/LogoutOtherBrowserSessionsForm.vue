<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmLogout = () => {
    confirmingLogout.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;

    form.reset();
};
</script>

<template>
    <div class="font-droidkufi">
        <ActionSection>
            <template #title>
                کۆکردنەوەی کاتی گەڕان
            </template>

            <template #description>
                بەڕێوەبردنی کۆدانەوەی ئەو کاتانەی لەگەڵ ئەکاونتەکەت چالاکن لەسەر ئەو بڕاوسەرە و ئامێرە ترەکان.
            </template>

            <template #content>
                <div class="max-w-xl text-sm text-gray-600">
                    ئەگەر پێویست بوو، دەتوانی لە هەموو بڕاوسەرە و ئامێرەکانی تر کۆدەبەیت. هەندێک لە کاتی ڕابردووت
                    خوارەوە دیارین، بەڵام ئەم لیستە گەورەترین ناکات. ئەگەر وای کردووە هەژمارت لەناوچووە، باشە وشەی
                    نهێنیت نوێ بکەیت.
                </div>

                <!-- کاتەکانی بڕاوسەرەکانی تر -->
                <div v-if="sessions.length > 0" class="mt-5 space-y-6">
                    <div v-for="(session, i) in sessions" :key="i" class="flex items-center">
                        <div>
                            <!-- Desktop Icon -->
                            <svg v-if="session.agent.is_desktop" class="size-8 text-gray-500"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="..." />
                            </svg>

                            <!-- Mobile Icon -->
                            <svg v-else class="size-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="..." />
                            </svg>
                        </div>

                        <div class="ms-3">
                            <div class="text-sm text-gray-600">
                                {{ session.agent.platform ? session.agent.platform : 'نەناسراو' }} - {{
                                session.agent.browser ? session.agent.browser : 'نەناسراو' }}
                            </div>

                            <div class="text-xs text-gray-500">
                                {{ session.ip_address }},
                                <span v-if="session.is_current_device" class="text-green-500 font-semibold">ئەم
                                    ئامێرە</span>
                                <span v-else>دوایین چالاکی {{ session.last_active }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center mt-5">
                    <PrimaryButton @click="confirmLogout">
                        کۆکردنەوەی کاتی گەڕانی بڕاوسەرە ترەکان
                    </PrimaryButton>

                    <ActionMessage :on="form.recentlySuccessful" class="ms-3">
                        تەواو بوو.
                    </ActionMessage>
                </div>

                <!-- دڵنیاکردنەوەی کۆدانەوەی ئامێرە ترەکان -->
                <DialogModal :show="confirmingLogout" @close="closeModal">
                    <template #title>
                        کۆکردنەوەی کاتی گەڕانی بڕاوسەرە ترەکان
                    </template>

                    <template #content>
                        تکایە وشەی نهێنیت بنووسە بۆ دڵنیا بوونەوە لە کۆدانەوەی هەموو بڕاوسەرەکانت لەسەر ئامێرە ترەکان.

                        <div class="mt-4">
                            <TextInput ref="passwordInput" v-model="form.password" type="password"
                                class="mt-1 block w-3/4" placeholder="وشەی نهێنی" autocomplete="current-password"
                                @keyup.enter="logoutOtherBrowserSessions" />

                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>
                    </template>

                    <template #footer>
                        <SecondaryButton @click="closeModal">
                            هەڵوەشاندنەوە
                        </SecondaryButton>

                        <PrimaryButton class="ms-3" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing" @click="logoutOtherBrowserSessions">
                            کۆکردنەوەی بڕاوسەرە ترەکان
                        </PrimaryButton>
                    </template>
                </DialogModal>
            </template>
        </ActionSection>
    </div>
</template>
