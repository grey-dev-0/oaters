<modal id="profile-modal" ref="profileModal" color="primary" size="lg">
    <template #header>Profile - @{{openContact.name}}</template>
    <avatar v-if="openContact.id" :name="openContact.name" :image="openContact.image"></avatar>
</modal>