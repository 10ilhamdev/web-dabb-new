function pageManager() {
    return {
        addModal: { open: false },
        editModal: { open: false, id: null, title: '', description: '', order: 0 },
        deleteModal: { open: false, id: null, name: '' },
        visibilityModal: { open: false, id: null, name: '' },

        openAddModal() { this.addModal = { open: true }; },
        openEditModal(id, title, description, order) {
            this.editModal = { open: true, id, title, description, order };
        },
        openDeleteModal(id, name) {
            this.deleteModal = { open: true, id, name };
        },
        openVisibilityModal(id, name) {
            this.visibilityModal = { open: true, id, name };
        }
    }
}
