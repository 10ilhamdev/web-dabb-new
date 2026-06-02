function featureManager() {
    return {
        editModal: { open: false, id: null, name: '', type: 'link', path: '', order: 0, pageType: 'none', newParentId: '', isLoginRequired: false },
        addModal: { open: false, type: 'link', pageType: 'none', isLoginRequired: false },
        deleteModal: { open: false, id: null, name: '' },

        openEditModal(id, name, type, path, order, pageType = 'none', newParentId = '', isLoginRequired = false) {
            this.editModal = { open: true, id, name, type, path, order, pageType, newParentId, isLoginRequired: !!isLoginRequired };
        },
        openAddModal() {
            this.addModal = { open: true, type: 'link', pageType: 'none', isLoginRequired: false };
        },
        openDeleteModal(id, name) {
            this.deleteModal = { open: true, id, name };
        }
    }
}
