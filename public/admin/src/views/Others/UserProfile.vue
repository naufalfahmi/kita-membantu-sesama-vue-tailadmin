<template>
  <div>
    <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Profile</h3>
    <div class="">
      <ProfileCard />
      <PersonalInfoCard />
    </div>
  </div>
</template>

<script setup lang="ts">
import ProfileCard from '@/components/profile/ProfileCard.vue'
import PersonalInfoCard from '@/components/profile/PersonalInfoCard.vue'
import AddressCard from '@/components/profile/AddressCard.vue'
</script>
