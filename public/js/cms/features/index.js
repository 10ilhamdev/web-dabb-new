function featureManager() {
    return {
        editModal: { open: false, id: null, name: '', type: 'link', path: '', order: 0, pageType: 'none', newParentId: '' },
        addModal: { open: false, type: 'link', pageType: 'none' },
        deleteModal: { open: false, id: null, name: '' },

        openEditModal(id, name, type, path, order, pageType = 'none', newParentId = '') {
            this.editModal = { open: true, id, name, type, path, order, pageType, newParentId };
        },
        openAddModal() {
            this.addModal = { open: true, type: 'link', pageType: 'none' };
        },
        openDeleteModal(id, name) {
            this.deleteModal = { open: true, id, name };
        }
    }
}
