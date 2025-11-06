<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <nav
            class="border-b border-gray-100 bg-white"
        >
            <!-- Primary Navigation Menu -->
            <div class="mx-auto px-4 sm:px-10 lg:px-28">
                <div class="flex h-16 justify-between">
                    <!-- Logo -->
                    <div class="flex gap-2 items-center">
                        <Link :href="route('dashboard')">
                            <ApplicationLogo
                                class="block h-9 w-auto fill-current text-gray-800"
                            />
                        </Link>
                        <div class="sm:-my-px sm:ms-10">
                            <NavLink
                                :href="route('home')"
                                :active="route().current('home')"
                            >
                                Events
                            </NavLink>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <NavLink :href="route('login')" :active="route().current('login')">
                          Login
                        </NavLink>
                        <NavLink :href="route('register')" :active="route().current('register')">
                          Register
                        </NavLink>
                    </div>
                </div>
            </div>
        </nav>
        <div
            class="mt-6 flex justify-center"
        >
            <slot />
        </div>
    </div>
</template>
