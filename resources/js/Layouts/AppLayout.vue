<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

// Handle hover states for dropdowns
const showDocsDropdown = ref(false);

</script>


<template>
    <div dir="rtl">

        <Head :title="title" />
        <Banner />
        <div class="h-screen flex flex-col bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-10 ">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationMark class="block h-9 w-auto" />
                                </Link>
                            </div>

                            <div class="hidden space-x-4 sm:-my-px sm:ms-5 sm:flex">
                                <!-- Dashboard fastfood application Link -->
                                <NavLink v-if="can('read dashboard')" :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                    :class="{ 'border-b-2 border-black': route().current('dashboard') }">
                                    <h4 class="font-bold font-droidkufi text-black dark:text-black pl-1 sm:pl-2">
                                        داشبۆرد
                                    </h4>
                                </NavLink>
                                <NavLink v-if="can('read dashboard')" :href="route('mulks.dashboard')"
                                    :active="route().current('mulks.dashboard')"
                                    :class="{ 'border-b-2 border-black': route().current('mulks.dashboard') }">
                                    <h4 class="font-bold font-droidkufi text-black dark:text-black pl-1 sm:pl-2">
داشبۆرد ٢                                    </h4>
                                </NavLink>




                                <!-- main -->
                                <nav v-if="can('read users') || can('read roles')" class="text-black z-50 inline-flex items-center
        px-1 pt-0.5 border-b-2 border-transparent hover:border-transparent text-sm font-medium leading-5
        focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out" :class="{
            // Active state for the parent nav (if the current route is 'users.index')
            'border-b-2 border-black text-gray-900': route().current('users.index') || route().current('roles.index'),
            '': !route().current('users.index')
        }">
                                    <ul class="flex space" @mouseleave="showDocsDropdown = false">
                                        <!-- Main User Item -->
                                        <li class="relative group" @mouseenter="showDocsDropdown = true" :class="{
                                            // Hover state for the parent li element
                                            'border-none': true
                                        }">
                                            <NavLink :href="route('users.index')" :active="false"
                                                class="hover:border-transparent">
                                                <h4
                                                    class="font-bold font-droidkufi text-black dark:text-black pl-1 sm:pl-2">
                                                    گشتی
                                                </h4>
                                            </NavLink>
                                            <!-- Dropdown Menu -->
                                            <ul v-if="showDocsDropdown" class="absolute shadow left-0 hidden mt-0 border-t-2 border-success 
                                                 space-y-2 bg-white text-black p-2 group-hover:block">
                                                <li>
                                                    <NavLink v-if="can('read projects')" :href="route('typemulk.index')"
                                                        :active="route().current('typemulk.index')"
                                                        :class="{ 'border-b-0 border-b-transparent ': route().current('typemulk.index') }">
                                                        <p
                                                            class="font-droidkufi border-b-transparent text-primary hover:text-success pl-1 sm:pl-2">
                                                            جۆری موڵک
                                                        </p>
                                                    </NavLink>
                                                </li>


                                                <li>
                                                    <NavLink v-if="can('read projects')"
                                                        :href="route('locations.index')"
                                                        :active="route().current('typeprojects.index')"
                                                        :class="{ 'border-b-0 border-b-transparent ': route().current('typeprojects.index') }">
                                                        <p
                                                            class="font-droidkufi border-b-transparent text-primary hover:text-success pl-1 sm:pl-2">
                                                            ناونیشانەکان </p>
                                                    </NavLink>
                                                </li>

                                                <li>
                                                    <NavLink v-if="can('read users')" :href="route('users.index')"
                                                        :active="route().current('users.index')"
                                                        :class="{ 'border-b-0 border-b-transparent ': route().current('users.index') }">
                                                        <p
                                                            class="font-droidkufi border-b-transparent text-black hover:text-success dark:text-black pl-1 sm:pl-2">
                                                            بەکارهێنەرەکان
                                                        </p>
                                                    </NavLink>
                                                </li>
                                                <li>
                                                    <NavLink v-if="can('read roles')" :href="route('roles.index')"
                                                        :active="route().current('roles.index')"
                                                        :class="{ 'border-b-0 border-b-transparent ': route().current('roles.index') }">
                                                        <p
                                                            class="font-droidkufi border-b-transparent text-black hover:text-success dark:text-black pl-1 sm:pl-2">
                                                            دەسەڵات
                                                        </p>
                                                    </NavLink>
                                                </li>
                                                <li>
                                                    <NavLink :href="route('settings.index')"
                                                        :active="route().current('settings.index')"
                                                        :class="{ 'border-b-0 border-b-transparent ': route().current('settings.index') }">
                                                        <p
                                                            class="font-droidkufi border-b-transparent text-black hover:text-success dark:text-black pl-1 sm:pl-2">
                                                            ڕێکخستنەکان
                                                        </p>
                                                    </NavLink>
                                                </li>


                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                                <!-- repports -->
                                <!-- <NavLink v-if="can('read repport')" :href="route('repport')" :active="route().current('repport')"
                                :class="{ 'border-b-2 border-black': route().current('repport') }">
                                <h4 class="font-bold font-droidkufi text-black dark:text-black pl-1 sm:pl-2">
                                    ڕاپۆرتەکان
                                </h4>
                            </NavLink> -->
                                <!--mullks  -->
                                <NavLink v-if="can('read repport')" :href="route('projects.index')"
                                    :active="route().current('projects.index')"
                                    :class="{ 'border-b-2 border-black': route().current('projects.index') }">
                                    <h4 class="font-bold font-droidkufi text-black dark:text-black pl-1 sm:pl-2">
                                        مووڵکەکان
                                    </h4>
                                </NavLink>
                                <!-- customers -->
                                <NavLink v-if="can('read repport')" :href="route('customers.index')"
                                    :active="route().current('customers.index')"
                                    :class="{ 'border-b-2 border-black': route().current('customers.index') }">
                                    <h4 class="font-bold font-droidkufi text-black dark:text-black pl-1 sm:pl-2">
                                        کڕیارەکان
                                    </h4>
                                </NavLink>
                                <!-- reppport assaish -->
                                <NavLink v-if="can('read repport')" :href="route('invoicesAsaish.index')"
                                :active="route().current('invoicesAsaish.index')"
                                :class="{ 'border-b-2 border-black': route().current('invoicesAsaish.index') }">
                                <h4 class="font-bold font-droidkufi text-black dark:text-black pl-1 sm:pl-2">
                                    ڕاپۆرتی ئاسایش
                                </h4>
                            </NavLink>
                            <!-- reppport normal -->
                                <NavLink v-if="can('read repport')" :href="route('invoicenormal.index')"
                                    :active="route().current('invoicenormal.index')"
                                    :class="{ 'border-b-2 border-black': route().current('invoicenormal.index') }">
                                    <h4 class="font-bold font-droidkufi text-black dark:text-black pl-1 sm:pl-2">
                                        ڕاپۆرتی ئاسایی
                                    </h4>
                                </NavLink>
                            </div>
                        </div>


                        <!-- Your Dropdown Menu -->


                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="ms-3 relative">
                                <!-- Teams Dropdown -->
                                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.current_team.name }}

                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                Manage Team
                                            </div>

                                            <!-- Team Settings -->
                                            <DropdownLink
                                                :href="route('teams.show', $page.props.auth.user.current_team)">
                                                Team Settings
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.jetstream.canCreateTeams"
                                                :href="route('teams.create')">
                                                Create New Team
                                            </DropdownLink>

                                            <!-- Team Switcher -->
                                            <template v-if="$page.props.auth.user.all_teams.length > 1">
                                                <div class="border-t border-gray-200" />

                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Teams
                                                </div>

                                                <template v-for="team in $page.props.auth.user.all_teams"
                                                    :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <DropdownLink as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props.auth.user.current_team_id"
                                                                    class="me-2 size-5 text-green-400"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>

                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos"
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="size-8 rounded-full object-cover"
                                                :src="$page.props.auth.user.profile_photo_url"
                                                :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block font-droidkufi px-4 py-2 text-xs text-gray-400">
                                            بەڕێوەبردنی ئەکاونت
                                        </div>

                                        <DropdownLink class="font-droidkufi" :href="route('profile.show')">
                                            پرۆفایل
                                        </DropdownLink>

                                        <DropdownLink v-if="$page.props.jetstream.hasApiFeatures"
                                            :href="route('api-tokens.index')">
                                            API Tokens
                                        </DropdownLink>

                                        <div class="border-t border-gray-200" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink
                                                class="font-droidkufi text-danger flex items-center justify-center "
                                                as="button">
                                                <p class="text-danger"><span><i
                                                            class="fa-solid fa-arrow-right-to-bracket"></i></span> چوونە
                                                    دەرەوە</p>
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                @click="showingNavigationDropdown = !showingNavigationDropdown">
                                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path
                                        :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                    class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1 font-droidkufi">
                        <ResponsiveNavLink v-if="can('read dashboard')" :href="route('dashboard')"
                            :active="route().current('dashboard')">
                            داشبۆرد
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="can('read dashboard')" :href="route('mulks.dashboard')"
                            :active="route().current('mulks.dashboard')">
                            داشبۆرد ٢
                        </ResponsiveNavLink>

                        <ResponsiveNavLink v-if="can('read users')" :href="route('users.index')"
                            :active="route().current('users.index')">
                            بەکارهێنەرەکان
                        </ResponsiveNavLink>

                        <ResponsiveNavLink v-if="can('read roles')" :href="route('roles.index')"
                            :active="route().current('roles.index')">
                            دەسەڵات
                        </ResponsiveNavLink>

                        <ResponsiveNavLink v-if="can('read projects')" :href="route('typemulk.index')"
                            :active="route().current('typemulk.index')">
                            جۆری موڵک
                        </ResponsiveNavLink>
                        <NavLink v-if="can('read projects')" :href="route('locations.index')"
                            :active="route().current('locations.index')"
                            :class="{ 'border-b-0 border-b-transparent ': route().current('locations.index') }">

                            ناونیشان
                        </NavLink>



                        <ResponsiveNavLink v-if="can('read screenTwo')" :href="route('projects.index')"
                            :active="route().current('projects.index')">
                            مووڵکەکان
                        </ResponsiveNavLink>

                        <ResponsiveNavLink v-if="can('read darxsta')" :href="route('customers.index')"
                            :active="route().current('customers.index')">
                            کڕیارەکان
                        </ResponsiveNavLink>

                        <ResponsiveNavLink v-if="can('read roles')" :href="route('settings.index')"
                            :active="route().current('settings.index')">
                            ڕێکخستنەکان
                        </ResponsiveNavLink>
                        <!-- Authentication -->
                        <form method="POST" @submit.prevent="logout">
                            <ResponsiveNavLink class="text-danger" as="button">
                                <h1 class="text-danger font-bold">
                                    چوونەدەرەوە
                                </h1>
                            </ResponsiveNavLink>
                        </form>
                    </div>
                </div>
            </nav>
            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
