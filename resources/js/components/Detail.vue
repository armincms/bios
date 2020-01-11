<template>
  <loading-view :loading="initialLoading">
    <custom-detail-header
      class="mb-3"
      :resource="resource"
      :resource-id="resourceId"
      :resource-name="resourceName"
    />

    <div v-if="shouldShowCards">
      <cards
        v-if="smallCards.length > 0"
        :cards="smallCards"
        class="mb-3"
        :resource="resource"
        :resource-id="resourceId"
        :resource-name="resourceName"
        :only-on-detail="true"
      />

      <cards
        v-if="largeCards.length > 0"
        :cards="largeCards"
        size="large"
        :resource="resource"
        :resource-id="resourceId"
        :resource-name="resourceName"
        :only-on-detail="true"
      />
    </div>

    <!-- Resource Detail -->
    <div
      v-for="panel in availablePanels"
      :dusk="resourceName + '-detail-component'"
      class="mb-8"
      :key="panel.id"
    >
      <component
        :is="panel.component"
        :resource-name="resourceName"
        :resource-id="resourceId"
        :resource="resource"
        :panel="panel"
      >
        <div v-if="panel.showToolbar" class="flex items-center mb-3">
          <heading :level="1" class="flex-no-shrink">{{ panel.name }}</heading>

          <div class="ml-3 w-full flex items-center">
            <custom-detail-toolbar
              :resource="resource"
              :resource-name="resourceName"
              :resource-id="resourceId"
            />

            <!-- Actions -->
            <action-selector
              v-if="resource"
              :resource-name="resourceName"
              :actions="actions"
              :pivot-actions="{ actions: [] }"
              :selected-resources="selectedResources"
              :query-string="{
                currentSearch,
                encodedFilters,
                currentTrashed,
                viaResource,
                viaResourceId,
                viaRelationship,
              }"
              @actionExecuted="actionExecuted"
              class="ml-3"
            /> 
            </portal>

            <router-link
              v-if="resource.authorizedToUpdate"
              data-testid="edit-bios-resource"
              dusk="edit-bios-resource-button"
              :to="{ name: 'bios.edit', params: { resourceName: resourceName } }"
              class="btn btn-default btn-icon bg-primary"
              :title="__('Edit')"
            >
              <icon
                type="edit"
                class="text-white"
                style="margin-top: -2px; margin-left: 3px"
              />
            </router-link>
          </div>
        </div>
      </component>
    </div>
  </loading-view>
</template>

<script>
import { 
  Errors, 
  Minimum, 
} from 'laravel-nova'

export default { 
  mixins: [],

  data: () => ({
    initialLoading: true,
    loading: true,

    resource: null,
    resourceName: null,
    panels: [],
    actions: [],
    actionValidationErrors: new Errors(),
    deleteModalOpen: false,
    restoreModalOpen: false,
    forceDeleteModalOpen: false,
  }),

  watch: {
    resourceId: function(newResourceId, oldResourceId) {
      if (newResourceId != oldResourceId) {
        this.initializeComponent()
      }
    },
  },

  /**
   * Bind the keydown even listener when the component is created
   */
  created() {
    this.resourceName = this.$route.params.resourceName;

    if (Nova.missingResource(this.resourceName)){ 
      return this.$router.push({ name: '404' })
    }

    document.addEventListener('keydown', this.handleKeydown)
  },

  /**
   * Unbind the keydown even listener when the component is destroyed
   */
  destroyed() {
    document.removeEventListener('keydown', this.handleKeydown)
  },

  /**
   * Mount the component.
   */
  mounted() {
    this.initializeComponent()
  },

  methods: { 

    /**
     * Handle the keydown event
     */
    handleKeydown(e) {
      if (
        this.resource.authorizedToUpdate &&
        !e.ctrlKey &&
        !e.altKey &&
        !e.metaKey &&
        !e.shiftKey &&
        e.keyCode == 69 &&
        e.target.tagName != 'INPUT' &&
        e.target.tagName != 'TEXTAREA'
      ) {
        this.$router.push({
          name: 'bios.edit',
          params: { resourceName: this.resourceName },
        })
      }
    },

    /**
     * Initialize the compnent's data.
     */
    async initializeComponent() {
      await this.getResource() 

      this.initialLoading = false
    },

    /**
     * Get the resource information.
     */
    getResource() {
      this.resource = null

      return Minimum(
        Nova.request().get(
          '/nova-vendor/bios/' + this.resourceName 
        )
      )
        .then(({ data: { panels, resource } }) => {
          this.panels = panels
          this.resource = resource
          this.loading = false
        })
        .catch(error => {
          if (error.response.status >= 500) {
            Nova.$emit('error', error.response.data.message)
            return
          }

          if (error.response.status === 404 && this.initialLoading) {
            this.$router.push({ name: '404' })
            return
          }

          if (error.response.status === 403) {
            this.$router.push({ name: '403' })
            return
          }

          Nova.error(this.__('This resource no longer exists'))

          this.$router.push({ name: '404' })
        })
    }, 
 
    /**
     * Create a new panel for the given field.
     */
    createPanelForField(field) {
      return _.tap(
        _.find(this.panels, panel => panel.name == field.panel),
        panel => {
          panel.fields = [field]
        }
      )
    },
  },

  computed: {
    /**
     * Get the available field panels.
     */
    availablePanels() {
      if (this.resource) {
        var panels = {}

        var fields = _.toArray(JSON.parse(JSON.stringify(this.resource.fields)))

        fields.forEach(field => {
          if (panels[field.panel]) {
            return panels[field.panel].fields.push(field)
          }

          panels[field.panel] = this.createPanelForField(field)
        })

        return _.toArray(panels)
      }
    },
  },
}
</script>
