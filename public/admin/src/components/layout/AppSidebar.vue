<template>
  <aside
    :class="[
      'fixed mt-16 flex flex-col lg:mt-0 top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999 border-r border-gray-200',
      {
        'lg:w-[290px]': isExpanded || isMobileOpen || isHovered,
        'lg:w-[90px]': !isExpanded && !isHovered,
        'translate-x-0 w-[290px]': isMobileOpen,
        '-translate-x-full': !isMobileOpen,
        'lg:translate-x-0': true,
      },
    ]"
    @mouseenter="!isExpanded && (isHovered = true)"
    @mouseleave="isHovered = false"
  >
    <div
      :class="[
        'py-2 flex',
        !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
      ]"
    >
    </div>
    <div
      class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar"
    >
      <nav class="mb-6" v-if="Array.isArray(menuGroups) && menuGroups.length > 0">
        <div class="flex flex-col gap-4">
          <div v-for="(menuGroup, groupIndex) in menuGroups" :key="groupIndex">
            <h2
              :class="[
                'mb-4 text-xs uppercase flex leading-[20px] text-gray-400',
                !isExpanded && !isHovered
                  ? 'lg:justify-center'
                  : 'justify-start',
              ]"
            >
              <template v-if="isExpanded || isHovered || isMobileOpen">
                {{ menuGroup.title }}
              </template>
              <HorizontalDots v-else />
            </h2>
            <ul class="flex flex-col gap-4">
              <li v-for="(item, index) in menuGroup.items" :key="item.name">
                <button
                  v-if="item.subItems"
                  @click="toggleSubmenu(groupIndex, index)"
                  :class="[
                    'menu-item group w-full',
                    {
                      'menu-item-active': isSubmenuOpen(groupIndex, index),
                      'menu-item-inactive': !isSubmenuOpen(groupIndex, index),
                    },
                    !isExpanded && !isHovered
                      ? 'lg:justify-center'
                      : 'lg:justify-start',
                  ]"
                >
                  <span
                    :class="[
                      isSubmenuOpen(groupIndex, index)
                        ? 'menu-item-icon-active'
                        : 'menu-item-icon-inactive',
                    ]"
                  >
                    <component :is="item.icon" />
                  </span>
                  <span
                    v-if="isExpanded || isHovered || isMobileOpen"
                    class="menu-item-text"
                    >{{ item.name }}</span
                  >
                  <ChevronDownIcon
                    v-if="isExpanded || isHovered || isMobileOpen"
                    :class="[
                      'ml-auto w-5 h-5 transition-transform duration-200',
                      {
                        'rotate-180 text-brand-500': isSubmenuOpen(
                          groupIndex,
                          index
                        ),
                      },
                    ]"
                  />
                </button>
                <router-link
                  v-else-if="item.path"
                  :to="item.path"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive(item.path),
                      'menu-item-inactive': !isActive(item.path),
                    },
                  ]"
                >
                  <span
                    :class="[
                      isActive(item.path)
                        ? 'menu-item-icon-active'
                        : 'menu-item-icon-inactive',
                    ]"
                  >
                    <component :is="item.icon" />
                  </span>
                  <span
                    v-if="isExpanded || isHovered || isMobileOpen"
                    class="menu-item-text"
                    >{{ item.name }}</span
                  >
                </router-link>
                <transition
                  @enter="startTransition"
                  @after-enter="endTransition"
                  @before-leave="startTransition"
                  @after-leave="endTransition"
                >
                  <div
                    v-show="
                      isSubmenuOpen(groupIndex, index) &&
                      (isExpanded || isHovered || isMobileOpen)
                    "
                  >
                    <ul class="mt-2 space-y-1 ml-9">
                      <li v-for="subItem in item.subItems" :key="subItem.name">
                        <router-link
                          :to="subItem.path"
                          :class="[
                            'menu-dropdown-item',
                            {
                              'menu-dropdown-item-active': isActive(
                                subItem.path
                              ),
                              'menu-dropdown-item-inactive': !isActive(
                                subItem.path
                              ),
                            },
                          ]"
                        >
                          {{ subItem.name }}
                          <span class="flex items-center gap-1 ml-auto">
                            <span
                              v-if="subItem.new"
                              :class="[
                                'menu-dropdown-badge',
                                {
                                  'menu-dropdown-badge-active': isActive(
                                    subItem.path
                                  ),
                                  'menu-dropdown-badge-inactive': !isActive(
                                    subItem.path
                                  ),
                                },
                              ]"
                            >
                              new
                            </span>
                            <span
                              v-if="subItem.pro"
                              :class="[
                                'menu-dropdown-badge',
                                {
                                  'menu-dropdown-badge-active': isActive(
                                    subItem.path
                                  ),
                                  'menu-dropdown-badge-inactive': !isActive(
                                    subItem.path
                                  ),
                                },
                              ]"
                            >
                              pro
                            </span>
                          </span>
                        </router-link>
                      </li>
                    </ul>
                  </div>
                </transition>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <div v-else-if="loading" class="flex items-center justify-center p-8">
        <p class="text-sm text-gray-500">Loading menu...</p>
      </div>
      <div v-else class="flex items-center justify-center p-8">
        <p class="text-sm text-gray-500">No menu available</p>
      </div>
      <SidebarWidget v-if="isExpanded || isHovered || isMobileOpen" />
    </div>
  </aside>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useRoute } from "vue-router";

import {
  ChevronDownIcon,
  HorizontalDots,
} from "../../icons";
import SidebarWidget from "./SidebarWidget.vue";
import { useSidebar } from "@/composables/useSidebar";
import { useMenu } from "@/composables/useMenu";

const route = useRoute();

const { isExpanded, isMobileOpen, isHovered, openSubmenu } = useSidebar();
const { menuGroups: menuGroupsRef, loading } = useMenu();

// Track explicitly closed menus
const closedMenus = ref({});

// Ensure menuGroups is always an array
const menuGroups = computed(() => {
  return Array.isArray(menuGroupsRef.value) ? menuGroupsRef.value : [];
});

const isActive = (path) => {
  // Exact match
  if (route.path === path) {
    return true;
  }
  
  // Check if current route starts with menu path (for add/edit pages)
  // Example: /administrasi/kantor-cabang/new or /administrasi/kantor-cabang/:id/edit
  // should match menu path /administrasi/kantor-cabang
  if (path && route.path.startsWith(path + '/')) {
    return true;
  }
  
  return false;
};

const toggleSubmenu = (groupIndex, itemIndex) => {
  const key = `${groupIndex}-${itemIndex}`;
  if (openSubmenu.value === key) {
    // User is closing the menu
    openSubmenu.value = null;
    closedMenus.value[key] = true;
  } else {
    // User is opening the menu
    openSubmenu.value = key;
    delete closedMenus.value[key];
  }
};

const isAnySubmenuRouteActive = computed(() => {
  if (menuGroups.value.length === 0) {
    return false;
  }
  return menuGroups.value.some((group) =>
    Array.isArray(group.items) &&
    group.items.some(
      (item) =>
        Array.isArray(item.subItems) && item.subItems.some((subItem) => isActive(subItem.path))
    )
  );
});

const isSubmenuOpen = (groupIndex, itemIndex) => {
  const key = `${groupIndex}-${itemIndex}`;
  if (menuGroups.value.length === 0 || !menuGroups.value[groupIndex]) {
    return false;
  }
  
  // If menu was explicitly closed by user, keep it closed
  if (closedMenus.value[key]) {
    return false;
  }
  
  const item = menuGroups.value[groupIndex].items?.[itemIndex];
  
  // Check if explicitly opened
  if (openSubmenu.value === key) {
    return true;
  }
  
  // Auto-open if route is active and menu hasn't been explicitly closed
  if (
    !closedMenus.value[key] &&
    Array.isArray(item?.subItems) &&
    item.subItems.some((subItem) => isActive(subItem.path))
  ) {
    return true;
  }
  
  return false;
};

// Keep submenu state in sync when route or menu data changes
const ensureActiveSubmenu = () => {
  let activeKey = null
  for (let g = 0; g < menuGroups.value.length; g++) {
    const group = menuGroups.value[g]
    if (!group || !Array.isArray(group.items)) continue
    for (let i = 0; i < group.items.length; i++) {
      const item = group.items[i]
      if (!item || !Array.isArray(item.subItems)) continue
      if (item.subItems.some((s) => isActive(s.path))) {
        activeKey = `${g}-${i}`
        break
      }
    }
    if (activeKey) break
  }

  if (activeKey) {
    if (!closedMenus.value[activeKey]) {
      openSubmenu.value = activeKey
    }
  } else {
    // If current route is not under a submenu, collapse open submenu
    openSubmenu.value = null
  }
}

watch(() => route.path, ensureActiveSubmenu)
watch(() => menuGroups.value, ensureActiveSubmenu)

const startTransition = (el) => {
  el.style.height = "auto";
  const height = el.scrollHeight;
  el.style.height = "0px";
  el.offsetHeight; // force reflow
  el.style.height = height + "px";
};

const endTransition = (el) => {
  el.style.height = "";
};
</script>
